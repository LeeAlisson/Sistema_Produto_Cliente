# Sistema Produto × Cliente

Aplicação PHP para cadastro de produtos e clientes, com associação N:N entre os dois. Foi feita para o teste técnico: MVC sem framework, MySQL e Docker.

**Acesso:** http://localhost:8080/login  
**Usuário:** `admin` / `admin123`

---

## Como subir

Docker sobe a API PHP (porta **8080**) e o MySQL 8 (porta **3307** no host, **3306** na rede interna). Fuso `America/Sao_Paulo`.

```bash
cd Sistema_Produto_Cliente
cp -n .env.example .env
docker compose up --build -d
```

O container instala o Composer, espera o banco, cria as tabelas e sobe o servidor. Não rode `setup.php` na máquina host.

Uso diário:

```bash
docker compose up -d    # ligar
docker compose down     # desligar (os dados do MySQL ficam no volume)
docker compose logs -f app
```

Se o login padrão falhar depois de um setup antigo:

```bash
docker compose exec app php scripts/fix-admin-password.php
```

---

## O que o enunciado pedia

| Pedido | Como ficou |
|--------|------------|
| CRUD de produtos e clientes | Telas de listagem, inclusão, visualização, alteração e exclusão |
| N:N (produto ↔ vários clientes e o inverso) | Tabela `r00_produto_cliente` + tela **Associações** |
| Na ficha do produto, listar clientes | `produtos/show` e `GET /api/produtos/{codigo}` |
| Campo virtual **Valor do Imposto** | `preço × (imposto ÷ 100)` — não é coluna |
| Na ficha do cliente, listar produtos | `clientes/show` e `GET /api/clientes/{codigo}` |
| DatePicker na data de nascimento | Flatpickr (`DD/MM/AAAA`); grava `AAAAMMDD` |
| Tipo de pessoa J / F / O | Select; regras de documento mudam com o tipo |
| CPF virtual, gravado em `c00_cnpj` | Campo do form `c00_documento`; persistido só com dígitos |
| PJ exige CNPJ, PF exige CPF, Outros opcional | Validador + dígito verificador da Receita |
| PKs e tabela de associação se necessário | PKs nas duas tabelas + `r00_produto_cliente` |

Colunas seguem o enunciado (`c00_*`, `p00_*`). Foram acrescentados índices de busca e a PK composta da associação.

---

## Regras que importam no uso

- **Imposto:** `p00_imposto` é percentual (ex.: 18.00). O valor em reais aparece na listagem, na ficha e na API.
- **Documento:** máscara na tela; banco só dígitos. CPF `111.111.111-11` e CNPJ sequencial inválido são recusados.
- **Data:** o DatePicker mostra `15/03/1985`; o banco guarda `19850315`. Data futura não passa.
- **Exclusão:** produto ou cliente com vínculo N:N não pode ser apagado. Remova a associação antes.
- **Associação duplicada:** a PK impede o mesmo par duas vezes; a operação roda em transação.

Dados de exemplo (já vêm no setup): 3 produtos, 3 clientes, 4 associações.

---

## Arquitetura

```
Request → public/index.php → Router → Controller → Service → Model → MySQL
```

Controllers só tratam HTTP. Regras ficam em `src/Services` e `src/Validators`. Models são PDO. Sem framework de propósito: o enunciado permitia um, mas o volume do teste cabe num MVC pequeno e fica fácil de revisar.

| Pasta | Papel |
|-------|--------|
| `src/Controllers` | Web e API |
| `src/Services` | Cadastro, associação, auditoria |
| `src/Validators` | Form + regras de PF/PJ/O |
| `src/Support/Documento.php` | CPF/CNPJ |
| `src/Models` | Queries |
| `routes/web.php` | Rotas |
| `database/schema.sql` | Schema MySQL |
| `compose.yaml` | App + banco |

---

## Telas

| Rota | Função |
|------|--------|
| `/` | Dashboard |
| `/produtos`, `/produtos/create`, `/produtos/{codigo}` | Listar, incluir, ver/editar |
| `/produtos/export` | CSV da listagem (respeita a busca) |
| `/clientes` … `/clientes/export` | Idem para clientes |
| `/associacao` | Vincular / desvincular |
| `/auditoria` | Log de operações (extra) |

Login, dashboard, busca, paginação, auditoria, CSV e API **não** estavam no enunciado.

---

## API

Autenticação: sessão do painel **ou** header `X-API-Key` (valor de `API_KEY` no `.env`).

```bash
curl -H "X-API-Key: dev-api-key-local" http://localhost:8080/api/produtos
curl -H "X-API-Key: dev-api-key-local" http://localhost:8080/api/produtos/PROD001
curl -H "X-API-Key: dev-api-key-local" http://localhost:8080/api/clientes/CLI001
```

| Método | Endpoint |
|--------|----------|
| GET | `/api/produtos`, `/api/produtos/{codigo}` — inclui `valor_imposto` e `clientes_associados` |
| POST | `/api/produtos`, `/api/produtos/{codigo}/edit`, `/api/produtos/{codigo}/delete` |
| GET | `/api/clientes`, `/api/clientes/{codigo}` — produtos associados já vêm com `valor_imposto` |
| POST | `/api/clientes`, `/api/clientes/{codigo}/edit`, `/api/clientes/{codigo}/delete` |
| GET | `/api/associacoes` |
| POST | `/api/associacoes`, `/api/associacoes/delete` |

Mutações da API usam POST (mesmo contrato do formulário), não PUT/DELETE.

---

## Testes

Rodar **dentro** do container (o PHP do host desta máquina é 7.4; o projeto pede 8.1+):

```bash
docker compose exec app vendor/bin/phpunit
docker compose exec app php scripts/smoke-test.php
docker compose exec app php scripts/verify.php
```

PHPUnit cobre imposto virtual, CPF/CNPJ, regras PF/PJ/O, data `AAAAMMDD`, associação N:N em transação e bloqueio de exclusão com vínculo. O smoke testa login, telas, API e export CSV.

---

## Ambiente (`.env`)

| Variável | Função | Padrão Docker |
|----------|--------|----------------|
| `APP_URL` | URL base | `http://localhost:8080` |
| `TZ` | Fuso | `America/Sao_Paulo` |
| `DB_HOST` | Host MySQL | `db` (o compose força isso no container) |
| `DB_PORT` | Porta interna | `3306` |
| `DB_DATABASE` | Banco | `produto_cliente` |
| `DB_PASSWORD` | Senha root | `secret` |
| `API_KEY` | Chave da API | `dev-api-key-local` no `.env` de exemplo |
| `PAGINATION_PER_PAGE` | Itens por página | `10` |

Não commitar `.env`. Copie de `.env.example`.

---

## Segurança (painel)

Sessão `HttpOnly` + `SameSite=Strict`, CSRF nos POSTs, headers/CSP, lockout no login, prepared statements, exclusão bloqueada com associação. Senha do admin é bcrypt.

---

## Licença

Projeto de demonstração técnica — Alisson Lee Martins.
