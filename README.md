# Controle de Gastos - Desafio Full Stack

Projeto MVP de controle de gastos pessoais usando **Laravel 12 + Vue 3**.

## Funcionalidades

- Autenticação com Laravel Sanctum (registro, login e logout)
- CRUD de categorias por usuário
- CRUD de despesas por usuário
- Regras de negócio das despesas:
  - valor maior que zero
  - data não pode passar de amanhã
  - categoria deve pertencer ao usuário autenticado
  - somente o dono pode editar/excluir
- Dashboard com:
  - total gasto no mês atual
  - últimas 5 despesas
  - resumo por categoria no mês atual

## Pre-requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- NPM
- Banco MySQL ou PostgreSQL (em testes, o projeto usa SQLite em memória)

## Instalação

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## Configuração do .env

Configure os campos de banco de dados no `.env`:

- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## Migrations e Seeders

```bash
php artisan migrate --seed
```

## Como rodar o projeto

Terminal 1 (backend Laravel):

```bash
php artisan serve
```

Terminal 2 (frontend Vite):

```bash
npm run dev
```

Abra no navegador o endereço que o Laravel mostrar (normalmente `http://127.0.0.1:8000`).

## Credenciais de seed

- E-mail: `caiodib10@gmail.com`
- Senha: `caio`

## Testes

```bash
php artisan test
```

## Entrega

- Repositório GitHub: `https://github.com/dib10/controle-gastos-akrk-caio`

