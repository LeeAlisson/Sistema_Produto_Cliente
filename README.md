# Sistema Produto x Cliente

Aplicação web em PHP para gestão de produtos, clientes e associações N:N. MVC sem framework, com camada de serviços, validação, auditoria e API REST.

## Requisitos do teste técnico

| Requisito | Status |
|-----------|--------|
| Cadastro de produtos e clientes | ✓ |
| Associação N:N (produto ↔ vários clientes, cliente ↔ vários produtos) | ✓ |
| Listagem de produtos | ✓ |
| Manutenção de produtos (incluir / visualizar / alterar / excluir) | ✓ |
| Visualização do produto com clientes associados | ✓ |
| Campo virtual **Valor do Imposto** (`preço × imposto%`) | ✓ |
| Listagem de clientes | ✓ |
| Manutenção de clientes (incluir / visualizar / alterar / excluir) | ✓ |
| Visualização do cliente com produtos associados | ✓ |
| DatePicker em Data de Nascimento (Flatpickr) | ✓ |
| Tipo de pessoa J / F / O | ✓ |
| Campo virtual CPF (gravado em `c00_cnpj`) | ✓ |
| PJ exige CNPJ, PF exige CPF, Outros sem obrigatoriedade | ✓ |
| Tela de associação produto × cliente | ✓ |
| Tabelas `c00_cliente` e `p00_produto` conforme especificação | ✓ |
| Chaves primárias + tabela de associação `r00_produto_cliente` | ✓ |
| `c00_data_nascimento` em formato AAAAMMDD | ✓ |
| `p00_imposto` como percentual | ✓ |

Extras implementados (não exigidos): login, dashboard, busca/paginação, auditoria, API, testes PHPUnit, Docker.

## Stack

| Camada | Tecnologia |
|--------|------------|
| Runtime | PHP 8.1+ |
| Banco | MySQL 8 |
| Front | Bootstrap 5, Chart.js, Flatpickr |
| Testes | PHPUnit 10 |

## Como rodar

Há três formas de executar o projeto. Escolha **uma**:

| Opção | Quando usar |
|-------|-------------|
| **A — PHP + MySQL local** | Já tem PHP e MySQL instalados (recomendado para desenvolvimento) |
| **B — XAMPP** | Prefere Apache/MySQL pelo painel XAMPP |
| **C — Docker** | Quer subir app + MySQL sem instalar PHP/MySQL na máquina |

---

### A — PHP + MySQL local (servidor embutido)

#### Primeira vez

```powershell
cd sistema-produto-cliente

copy .env.example .env
# Edite .env e defina DB_PASSWORD com a senha do seu MySQL

composer install
php scripts/setup.php
php -S localhost:8080 -t public public/router.php
```

Acesse: **http://localhost:8080/login**  
Login: `admin` / `admin123`

> **Não** rode `copy .env.example .env` se o `.env` já existir — isso apaga a senha configurada.

#### Depois (uso diário)

```powershell
cd sistema-produto-cliente
php -S localhost:8080 -t public public/router.php
```

Só precisa de `setup.php` novamente se recriar o banco ou mudar o schema.

---

### B — XAMPP

O XAMPP fornece Apache + MySQL. O PHP do projeto roda no **servidor embutido** (mais simples para este projeto) ou via Apache apontando o DocumentRoot em `public/`.

#### Primeira vez

1. Instale [XAMPP](https://www.apachefriends.org/) e inicie **MySQL** no painel.
2. Copie o projeto em `C:\xampp\htdocs\sistema-produto-cliente` (ou mantenha onde está).
3. Configure o `.env`:

```powershell
copy .env.example .env
```

Edite `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=          # vazio no XAMPP padrão, ou sua senha se configurou
```

4. Setup e servidor:

```powershell
composer install
php scripts/setup.php
php -S localhost:8080 -t public public/router.php
```

**Alternativa Apache:** use `docker/apache-vhost.conf` como modelo. Copie para `C:\xampp\apache\conf\extra\`, ajuste o `DocumentRoot` para a pasta `public/` e inclua no `httpd.conf`. O `.htaccess` em `public/` já redireciona rotas ao `index.php`.

#### Depois

Inicie MySQL no XAMPP e rode:

```powershell
php -S localhost:8080 -t public public/router.php
```

---

### C — Docker (não é XAMPP)

Docker sobe **dois containers**: app PHP (porta 8080) e MySQL 8 (porta 3307). Não usa XAMPP.

#### Primeira vez

```powershell
cd sistema-produto-cliente

copy .env.example .env
```

Edite `.env` para Docker:

```env
DB_HOST=db
DB_PASSWORD=secret
DB_DATABASE=produto_cliente
```

```powershell
docker compose -f docker/docker-compose.yml up --build -d
php scripts/setup.php
```

> Rode `setup.php` **na máquina local** (não dentro do container). O script conecta em `localhost:3307` se `DB_HOST=localhost` no `.env` durante setup — **atenção**: o compose expõe MySQL em `3307`. Para o setup local, use temporariamente:

```env
DB_HOST=127.0.0.1
DB_PORT=3307
```

Depois do setup, volte para `DB_HOST=db` se a app roda só no container, ou mantenha `127.0.0.1:3307` se roda PHP local contra o MySQL do Docker.

Acesse: **http://localhost:8080/login**

#### Depois

```powershell
docker compose -f docker/docker-compose.yml up -d
```

Parar:

```powershell
docker compose -f docker/docker-compose.yml down
```

---

## Variáveis de ambiente

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `APP_ENV` | `local` ou `production` | `local` |
| `APP_DEBUG` | Erros detalhados (dev) | `true` |
| `APP_URL` | URL base | `http://localhost:8080` |
| `DB_HOST` | Host MySQL (`localhost` ou `db` no Docker) | `localhost` |
| `DB_PORT` | Porta MySQL (`3306` ou `3307` se Docker exposto) | `3306` |
| `DB_DATABASE` | Nome do banco | `produto_cliente` |
| `DB_USERNAME` | Usuário MySQL | `root` |
| `DB_PASSWORD` | Senha MySQL | — |
| `API_KEY` | Chave da API REST | — |
| `PAGINATION_PER_PAGE` | Registros por página | `10` |

## Testes

```powershell
composer test          # PHPUnit
composer smoke         # smoke test (servidor em :8080)
php scripts/verify.php # banco e tabelas
```

Se login `admin` / `admin123` falhar após setup antigo: `php scripts/fix-admin-password.php`

## API REST

Autenticação: sessão ativa ou header `X-API-Key` com valor de `API_KEY`.

```powershell
curl -H "X-API-Key: sua-chave" http://localhost:8080/api/produtos
```

| Método | Endpoint |
|--------|----------|
| GET | `/api/produtos`, `/api/produtos/{codigo}` |
| POST | `/api/produtos`, `/api/produtos/{codigo}/edit`, `/api/produtos/{codigo}/delete` |
| GET | `/api/clientes`, `/api/clientes/{codigo}` |
| POST | `/api/clientes`, `/api/clientes/{codigo}/edit`, `/api/clientes/{codigo}/delete` |
| GET | `/api/associacoes` |
| POST | `/api/associacoes`, `/api/associacoes/delete` |

## Arquitetura

```
Request → public/index.php → Router → Controller → Service → Model → MySQL
```

| Diretório | Função |
|-----------|--------|
| `src/Controllers` | HTTP, flash, views |
| `src/Services` | Regras de negócio |
| `src/Validators` | Validação de formulários |
| `src/Models` | PDO / queries |
| `routes/web.php` | Rotas web e API |

## Segurança

- Sessão `HttpOnly`, `SameSite=Strict`
- CSRF em POSTs do painel
- CSP e headers de proteção
- Rate limit no login
- Exclusão bloqueada com associações ativas
- Credenciais apenas em `.env`

## Estrutura

```
├── config/          Lê .env
├── database/        schema.sql
├── docker/          Dockerfile + Compose
├── public/          Document root
├── routes/          Rotas
├── scripts/         setup.php
├── src/
└── tests/
```

## Licença

Projeto de demonstração técnica — Alisson Lee Martins.
