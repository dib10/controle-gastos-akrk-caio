# Controle de Gastos - Desafio Full Stack

Aplicacao MVP de controle de gastos pessoais desenvolvida com **Laravel 12 + Vue 3**.

## Funcionalidades

- Autenticacao com Laravel Sanctum
- CRUD de categorias por usuario
- CRUD de despesas por usuario
- Dashboard com total gasto no mes, ultimas 5 despesas e resumo por categoria
- Validacoes de negocio:
  - valor maior que zero
  - data nao pode passar de amanha
  - categoria deve pertencer ao usuario autenticado
  - somente o dono pode editar e excluir registros

## Deploy publico

https://controle-gastos-akrk-caio-production-6c6f.up.railway.app

## Credenciais de demonstracao

- E-mail: `caiodib10@gmail.com`
- Senha: `caio`

Observacao: os campos de login podem aparecer pre-preenchidos para facilitar a avaliacao.

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- NPM

## Instalacao

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

## Configuracao do .env

Localmente, o projeto roda com **SQLite por padrao**, entao nao e necessario configurar MySQL, PostgreSQL ou XAMPP para avaliar a aplicacao.

No deploy publico, a aplicacao utiliza PostgreSQL.

## Migrations e seeders

```bash
php artisan migrate --seed
```

## Como rodar localmente

Terminal 1 (backend Laravel):

```bash
php artisan serve
```

Terminal 2 (frontend Vite):

```bash
npm run dev
```

Abra no navegador o endereco informado pelo Laravel, normalmente `http://127.0.0.1:8000`.

## Testes

```bash
php artisan test
```

## Repositorio

- GitHub: `https://github.com/dib10/controle-gastos-akrk-caio`
