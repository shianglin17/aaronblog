# Repository Guidelines

## Project Structure & Module Organization

Back-end domain logic lives in `app/` (Controllers → Services → Repositories) with HTTP routes declared in
`routes/web.php` and `routes/api.php`. Vue 3 assets sit under `resources/js` and Blade scaffolding in `resources/views`.
Shared configuration is in `config/`, database factories and seeders are in `database/`, and public-facing assets ship
from `public/`. Reference `docs/architecture/README.md` for higher-level diagrams and `ops/` plus `scripts/` for
deployment utilities.

## Build, Test, and Development Commands

Install dependencies once with `composer install` and `npm install`. Run `npm run dev` for a Vite-powered front-end with
hot reloads; pair it with `php artisan serve` or use `npm run start` to launch both servers concurrently. Build
production bundles via `npm run build`, and rely on `composer run dev` when you need the full Laravel queue/log stack
locally. Execute the application test suite with `composer test` (aliases `php artisan test`) and inspect additional
artisan helpers using `php artisan list`.

## Coding Style & Naming Conventions

The project enforces `.editorconfig` defaults: UTF-8, LF endings, and 4-space indentation (YAML stays at 2). PHP code
should remain PSR-12 compliant and be auto-formatted with `./vendor/bin/pint`. Vue and TypeScript modules in
`resources/js` use the Composition API, PascalCase component files, and kebab-case view folders; prefer single quotes
and explicit type annotations for shared stores in `resources/js/stores`.

## Testing Guidelines

Feature and API coverage belongs in `tests/Feature`, domain units in `tests/Unit`, and cross-cutting helpers in
`tests/Support`. Name new tests after the subject plus expectation (e.g.
`ArticleApiTest::test_get_articles_returns_success`). Use SQLite in-memory fixtures via `RefreshDatabase` and mirror
existing factories for seeding. Document noteworthy scenarios in `docs/testing-guide.md` whenever you add endpoints or
acceptance flows.

## Commit & Pull Request Guidelines

Follow the existing Conventional Commit style—`type(scope): summary`—and keep summaries under 72 characters; bilingual
summaries are fine when concise. Every PR should describe the intent, list key changes, and link to tracking issues or
ADRs; include screenshots when UI or API schema changes. Confirm linting (`./vendor/bin/pint`) and tests pass before
requesting review, and mention any skipped checks so reviewers can verify manually.

## 一定要符合專案使用版本的最佳實踐，如果沒有就是以最新版本的文件、最佳實踐為主
