# 📚 Sistema de Gerenciamento de Biblioteca Escolar

Aplicação web fullstack para gestão de acervo e empréstimos de uma biblioteca escolar, com **autenticação**, **controle de acesso por função (RBAC)** e **CRUD** completo. Construída em PHP puro com PDO e MySQL.

## Funcionalidades

### Leitor (usuário comum)
- Visualizar o acervo com disponibilidade de cópias em tempo real
- Pegar livros emprestados (com validação de disponibilidade)
- Consultar e devolver os próprios empréstimos ativos

### Administrador
- Painel com métricas: total de títulos, cópias, leitores e empréstimos ativos, além do ranking de livros mais emprestados
- Gerenciar acervo: adicionar livros por formulário e excluir
- Gerenciar usuários: listar leitores e banir/remover contas

## Stack

| Camada | Tecnologia |
|--------|-----------|
| Back-end | PHP 8 (procedural, sem framework) |
| Banco | MySQL (InnoDB, chaves estrangeiras) |
| Acesso a dados | PDO com prepared statements |
| Front-end | HTML + CSS |

## Decisões de segurança

- **Senhas com hash** via `password_hash()` / `password_verify()` — nunca em texto puro.
- **Prepared statements** em 100% das consultas — proteção contra SQL injection.
- **Proteção CSRF** com token por sessão validado em todo `POST`.
- **Escape de saída** (`htmlspecialchars`) em todo dado renderizado — proteção contra XSS.
- **Controle de acesso** verificado no servidor a cada requisição, não só na UI.
- **Prevenção de IDOR**: devolução de empréstimo checa a posse (`user_id`) no banco.
- **Credenciais** do banco em `.env`, fora do versionamento.

## Modelo de dados

```
users (id, name, email, password, role[user|admin], created_at)
books (id, title, author, release_date, total_copies, created_at)
loans (id, user_id → users, book_id → books, borrowed_at, returned_at)
```
Disponibilidade de um livro = `total_copies` − empréstimos ativos (`returned_at IS NULL`).

## Como rodar localmente

Requer PHP 8+ e MySQL (ex.: XAMPP, WAMP ou Laragon).

1. Clone o repositório:
   ```bash
   git clone https://github.com/kaehrolim/library-management.git
   cd library-management
   ```
2. Crie o banco e importe o schema:
   ```sql
   CREATE DATABASE library_management;
   USE library_management;
   SOURCE sql/schema.sql;
   ```
3. Configure o acesso ao banco:
   ```bash
   cp .env.example .env
   # edite .env com seu usuário/senha do MySQL
   ```
4. Suba o servidor embutido do PHP a partir da raiz do projeto:
   ```bash
   php -S localhost:8000
   ```
5. Acesse `http://localhost:8000`.

## Acesso padrão (admin)

Criado automaticamente pelo `schema.sql`:

- **E-mail:** `admin@biblioteca.local`
- **Senha:** `Admin@123`

> Troque a senha do admin após o primeiro acesso.

## Estrutura de pastas

```
library-management/
├── config/       → conexão PDO
├── includes/     → auth/RBAC/CSRF, header e footer
├── admin/        → painel, gestão de livros e usuários
├── user/         → acervo, empréstimo e devolução
├── actions/      → handlers POST (borrow, return, add/delete)
├── sql/          → schema.sql
├── assets/       → style.css
├── index.php · login.php · register.php · logout.php
└── .env.example
```
