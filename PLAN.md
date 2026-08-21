# Client Project Tracker — Implementation Plan

## Context

Build a Client Project Tracker for a digital agency (`REQUIREMENTS.md`). Single Laravel app using **Inertia.js + React (TypeScript)** via the official **Laravel React starter kit**, on **MySQL**.

Rubric weights (`SUBMISSION.md`): Functionality 30%, Code Quality 25%, Architecture 20%, Documentation 10%, Error/Validation 10%, Communication 5%.

## Decisions

- **Stack**: Single Laravel app + Inertia.js + React (TypeScript) via official Laravel React starter kit.
- **Database**: MySQL.
- **Auth**: Included — starter kit auth; protect project routes with `auth` middleware (route-gating only, no multi-tenancy).
- **Bonus features**: Search (client/project name) + filter by status + filter by priority + sortable columns. All four.
- **Docker**: Yes — `docker-compose.yml` (PHP-FPM + MySQL + nginx).
- **Tests**: Yes — Pest feature tests for CRUD + validation.

## Project Model

`projects`: `id`, `client_name`, `project_name`, `description` (nullable), `status` (enum), `priority` (enum), `start_date` (date), `due_date` (date), `created_at`, `updated_at`.

- `ProjectStatus` enum: Planning, In Progress, On Hold, Completed
- `ProjectPriority` enum: Low, Medium, High
- Stored as `string` columns (DB-level indexes on status/priority for filters); model `$casts` to enums.

## Validation Rules

- `client_name`: required, string, max 255
- `project_name`: required, string, max 255
- `description`: nullable, string
- `status`: required, in `ProjectStatus`
- `priority`: required, in `ProjectPriority`
- `start_date`: required, date
- `due_date`: required, date, must be >= `start_date` (custom rule)
- Invalid → 422 with field-level error bag (Inertia surfaces on frontend).

## Phases

### Phase 0 — Scaffold
1. `laravel new koda --react` (Inertia + Vite + TS starter kit, includes auth).
2. Configure `.env` for MySQL (`koda_tracker`).
3. Verify `php artisan migrate` + `npm run dev` boot the welcome page.
4. Commit initial scaffold.

### Phase 1 — Model & Migration
1. `Project` model + migration + factory + seeder.
2. Migration columns above; indexes on `status`, `priority`.
3. `ProjectStatus` / `ProjectPriority` backed enums; model `$casts`.
4. `ProjectFactory` with faker data across all enum values.
5. Seeder reading `test_data.json`, mapping camelCase → snake_case, preserving IDs 1-12.
6. Verify: `php artisan migrate:fresh --seed` → 12 projects.

### Phase 2 — Backend CRUD + Validation
1. `ProjectController`: `index`, `show`, `store`, `update`, `destroy`.
2. `StoreProjectRequest` / `UpdateProjectRequest` form requests; shared custom rule for `due_date >= start_date`.
3. `index` query params: `?search=`, `?status=`, `?priority=`, `?sort=&direction=`; validate `sort` against allowlist (`start_date`, `due_date`, `priority`, `status`, `client_name`).
4. Routes in `routes/web.php` under `auth` middleware — Inertia responses (not JSON).
5. Flash messages on store/update/destroy.

### Phase 3 — Frontend (Inertia + React + TS)
1. `resources/js/types/project.ts` — TS type matching model.
2. `Projects/Index.tsx` — table with status/priority badges, dates, row actions; search box, status/priority filters, sortable headers; `router.reload` preserving scroll.
3. `Projects/Show.tsx` — single project detail.
4. `Projects/Create.tsx` + `Projects/Edit.tsx` — shared `ProjectForm`; inline `errors` binding; `type="date"` inputs; enum-driven selects.
5. Nav link in starter layout; Tailwind badge color mapping.

### Phase 4 — Tests
1. Pest feature tests: auth redirect, index (with search/filter/sort), show (404), store (success + all validation failures incl. date rule), update, destroy.
2. Unit tests for enums + date comparison rule.
3. `php artisan test` green.

### Phase 5 — Docker
1. `docker-compose.yml`: `app` (php-fpm), `nginx`, `db` (mysql:8).
2. `Dockerfile` (PHP 8.3 + php extensions + composer).
3. `nginx/default.conf` → php-fpm.
4. Note Sail as alternative; document `npm run dev` for vite on host.

### Phase 6 — Documentation
1. Root `README.md`: prerequisites, local + docker setup, env, seed, test command, feature list, AI-tool disclosure (per `README.md` "Allowed Tools").
2. Architecture notes: why single Laravel+Inertia app, enum-backed validation, query filtering approach.

## Risks / Edge Cases

- **Date comparison**: compare date-only; store as `date` not `datetime`.
- **Sort injection**: validate `sort` param against allowlist; default `due_date asc`.
- **Inertia error props**: verify form request returns 422 + flash errors the starter-kit version reads correctly.
- **Starter kit version drift**: adapt components to what ships (modals/forms/table APIs differ across versions).
- **Seeder keys**: `test_data.json` uses camelCase (`clientName`); map to snake_case columns.

## Validation / Acceptance

- `php artisan migrate:fresh --seed` → 12 projects matching `test_data.json`.
- Guest blocked from `/projects`; logged-in user full CRUD/search/filter/sort.
- All validation errors inline; due_date < start_date rejected.
- `php artisan test` green.
- `docker compose up -d` + migrate/seed → site reachable on `localhost:8080`.

## Out of Scope

- Multi-tenant per-user scoping (auth only gates routes; all logged-in users see all projects).
- Pagination (12 rows); note as optional follow-up.
- CI/CD / cloud deployment (beyond Docker).
- Real-time updates.
