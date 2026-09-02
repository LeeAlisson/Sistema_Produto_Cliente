<?php

/**
 * Smoke test — banco, regras de negócio, HTTP e compatibilidade Apache.
 *
 * Uso: php scripts/smoke-test.php [base_url]
 * Requer servidor rodando: php -S localhost:8080 -t public public/router.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/src/bootstrap.php';

use App\Config;
use App\Database;
use App\Models\Associacao;
use App\Models\Cliente;
use App\Models\Produto;

$baseUrl = rtrim($argv[1] ?? (Config::url() ?: 'http://localhost:8080'), '/');
$cookieJar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'produto_cliente_smoke_cookies.txt';

$passed = 0;
$failed = 0;
$skipped = 0;

function ok(string $label): void
{
    global $passed;
    $passed++;
    echo "  [OK] {$label}\n";
}

function fail(string $label, string $detail = ''): void
{
    global $failed;
    $failed++;
    echo "  [FAIL] {$label}" . ($detail ? " — {$detail}" : '') . "\n";
}

function skip(string $label, string $reason): void
{
    global $skipped;
    $skipped++;
    echo "  [SKIP] {$label} — {$reason}\n";
}

function curlRequest(string $url, string $cookieJar, array $options = []): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR => $cookieJar,
        CURLOPT_COOKIEFILE => $cookieJar,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 10,
    ]);

    if (!empty($options['post'])) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $options['post']);
    }

    if (!empty($options['headers'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
    }

    $raw = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        return ['code' => 0, 'body' => '', 'error' => $err];
    }

    $headerSize = strpos($raw, "\r\n\r\n");
    $body = $headerSize !== false ? substr($raw, $headerSize + 4) : $raw;

    return ['code' => $code, 'body' => $body, 'error' => $err];
}

if (!function_exists('curl_init')) {
    echo "curl extension não disponível.\n";
    exit(1);
}

if (file_exists($cookieJar)) {
    unlink($cookieJar);
}

echo "=== Smoke Test — Sistema Produto x Cliente ===\n";
echo "Base URL: {$baseUrl}\n\n";

echo "1. Banco de dados\n";
try {
    $pdo = Database::getConnection();
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach (['c00_cliente', 'p00_produto', 'r00_produto_cliente', 's00_usuario', 's00_audit_log'] as $table) {
        in_array($table, $tables, true) ? ok("Tabela {$table}") : fail("Tabela {$table}");
    }
    $admin = $pdo->query('SELECT s00_username FROM s00_usuario WHERE s00_username = \'admin\'')->fetchColumn();
    $admin === 'admin' ? ok('Usuário admin') : fail('Usuário admin');
} catch (Throwable $e) {
    fail('Conexão PDO', $e->getMessage());
}

echo "\n2. Regras de negócio\n";
Produto::calcularValorImposto(4500.0, 18.0) === 810.0
    ? ok('Valor do imposto (4500 × 18%)')
    : fail('Valor do imposto');

Cliente::isDataNascimentoValida('19900315') && !Cliente::isDataNascimentoValida('20900315')
    ? ok('Data de nascimento')
    : fail('Data de nascimento');

$produto = Produto::find('PROD001');
$produto ? ok('Produto PROD001') : fail('Produto PROD001');

$cliente = Cliente::find('CLI001');
$cliente ? ok('Cliente CLI001') : fail('Cliente CLI001');

$assocCount = count(Associacao::getAllAssociacoes());
$assocCount > 0 ? ok("Associações ({$assocCount})") : fail('Associações vazias');

$clientesComProduto = 0;
foreach ($pdo->query('SELECT DISTINCT r00_cliente_codigo FROM r00_produto_cliente') as $row) {
    if (count(Cliente::getProdutosAssociados($row['r00_cliente_codigo'])) > 0) {
        $clientesComProduto++;
    }
}
$clientesComProduto > 0
    ? ok('Cliente com produtos associados')
    : fail('Nenhum cliente com produtos');

$produtosComCliente = 0;
foreach ($pdo->query('SELECT DISTINCT r00_produto_codigo FROM r00_produto_cliente') as $row) {
    if (count(Produto::getClientesAssociados($row['r00_produto_codigo'])) > 0) {
        $produtosComCliente++;
    }
}
$produtosComCliente > 0
    ? ok('Produto com clientes associados')
    : fail('Nenhum produto com clientes');

echo "\n3. HTTP público\n";
$probe = curlRequest($baseUrl . '/login', $cookieJar);
if ($probe['code'] === 0) {
    fail('Servidor acessível', $probe['error'] ?: 'sem resposta — inicie: php -S localhost:8080 -t public public/router.php');
} elseif ($probe['code'] === 200 && str_contains($probe['body'], 'Bem-vindo')) {
    ok('GET /login');
} else {
    fail('GET /login', "HTTP {$probe['code']}");
}

$home = curlRequest($baseUrl . '/', $cookieJar);
if (in_array($home['code'], [200, 302], true)) {
    ok('GET / (guest)');
} else {
    fail('GET /', "HTTP {$home['code']}");
}

$apiKey = Config::get('API_KEY', '');
if ($apiKey) {
    $api = curlRequest($baseUrl . '/api/produtos', $cookieJar, [
        'headers' => ["X-API-Key: {$apiKey}"],
    ]);
    ($api['code'] === 200 && str_contains($api['body'], '"success":true'))
        ? ok('GET /api/produtos')
        : fail('GET /api/produtos', "HTTP {$api['code']}");
} else {
    skip('API Key', 'API_KEY vazio');
}

$api401 = curlRequest($baseUrl . '/api/produtos', $cookieJar);
$api401['code'] === 401 ? ok('API sem auth → 401') : fail('API sem auth', "HTTP {$api401['code']}");

$nf = curlRequest($baseUrl . '/pagina-invalida', $cookieJar);
$nf['code'] === 404 ? ok('GET 404') : fail('GET 404', "HTTP {$nf['code']}");

echo "\n4. Login e rotas autenticadas\n";
preg_match('/name="csrf_token" value="([^"]+)"/', $probe['body'], $m);
$csrf = $m[1] ?? '';

if ($csrf === '') {
    skip('Fluxo autenticado', 'CSRF não encontrado');
} else {
    $login = curlRequest($baseUrl . '/login', $cookieJar, [
        'post' => http_build_query([
            'csrf_token' => $csrf,
            'username' => 'admin',
            'password' => 'admin123',
        ]),
    ]);

    if (in_array($login['code'], [200, 302], true)) {
        ok('POST /login');
    } else {
        fail('POST /login', "HTTP {$login['code']}");
    }

    $routes = [
        '/produtos' => 'Produtos',
        '/clientes' => 'Clientes',
        '/associacao' => 'Associações',
        '/' => 'Dashboard',
        '/auditoria' => 'Auditoria',
    ];

    foreach ($routes as $path => $needle) {
        $res = curlRequest($baseUrl . $path, $cookieJar);
        if ($res['code'] === 200 && str_contains($res['body'], $needle)) {
            ok("GET {$path}");
        } else {
            fail("GET {$path}", "HTTP {$res['code']}");
        }
    }

    $show = curlRequest($baseUrl . '/produtos/PROD001', $cookieJar);
    if ($show['code'] === 200 && str_contains($show['body'], 'Valor do Imposto')) {
        ok('GET /produtos/PROD001 (detalhe + imposto)');
    } else {
        fail('GET /produtos/PROD001', "HTTP {$show['code']}");
    }
}

echo "\n5. Apache / XAMPP\n";
$xamppFound = false;
foreach (['C:\\xampp', 'D:\\xampp'] as $p) {
    if (is_dir($p)) {
        $xamppFound = true;
        break;
    }
}

if ($xamppFound) {
    ok('Diretório XAMPP encontrado');
    if (is_file('C:\\xampp\\apache\\bin\\httpd.exe')) {
        ok('httpd.exe presente');
    } else {
        skip('Apache XAMPP', 'httpd.exe não encontrado');
    }
} else {
    skip('XAMPP instalado', 'não detectado — MySQL/PHP standalone OK');
}

$htaccess = $root . '/public/.htaccess';
is_file($htaccess) && str_contains(file_get_contents($htaccess), 'RewriteRule')
    ? ok('.htaccess (rewrite → index.php)')
    : fail('.htaccess');

is_file($root . '/docker/apache-vhost.conf')
    ? ok('VirtualHost exemplo (docker/apache-vhost.conf)')
    : fail('VirtualHost exemplo');

echo "\n=== Resultado: {$passed} OK, {$failed} FAIL, {$skipped} SKIP ===\n";

if (file_exists($cookieJar)) {
    unlink($cookieJar);
}

exit($failed > 0 ? 1 : 0);
