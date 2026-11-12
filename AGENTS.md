# AGENTS.md

Guabao: a food and travel site (trips, meals, food, photos and 360° panoramas, stories), with an
admin called the controlroom. Symfony 8.1, PHP 8.5, PostgreSQL 17. UI in English (no prefix) and
French (`/fr`).

## Stack and layout

- Doctrine ORM + migrations, Twig + AssetMapper + Stimulus/Symfony UX (plain CSS, no build step),
  SecurityBundle, Form, Validator, Messenger (Doctrine transport), Lock, Mailer (Postmark),
  LiipImagine (thumbnails), Imagick (uploads converted to AVIF), Sentry.
- One codebase, two kernels picked by `HTTP_HOST` (`src/Kernel.php`): the public site, and the
  controlroom (EasyAdmin, `controlroom/`) whose config is loaded on top only for `HOST_CONTROLROOM`.
  On the CLI, reach the controlroom container with `HTTP_HOST=<HOST_CONTROLROOM> bin/console ...`
  (e.g. `debug:router`, `lint:container`). Each host has its own cache dir: `var/cache/{site,controlroom}/<env>`.
- The public site has no user accounts: the admin logs in on the controlroom host only.
- `packs/` holds self-contained modules (Chart, Media, Security, Status) with their own code and templates.
- Uploads go to `%uploads_path%` (`public/uploads`; `var/test/uploads` in test) through
  `Pack\Media\Helper\UploadHelper`. Vich is installed but no entity uses it.
- Environments: dev, prod and test only (`Kernel::getAllowedEnvs()`).

## Conventions

Follow https://symfony.com/doc/current/best_practices.html. The points that matter most:

- PHP attributes for framework metadata: `#[Route]`, `#[IsGranted]`, `#[Assert\...]`, `#[AsCommand]`,
  `#[AsEventListener]`, `#[AsMessageHandler]`... No new YAML or XML routes: the existing YAML only
  imports attribute routes per host and the EasyAdmin routes.
- Autowiring and autoconfiguration. Where a type-hint can't express it, `#[Autowire]` or `#[Target]` in
  the class. A YAML service definition (or a new `bind:`) is the last resort.
- Thin controllers extending `AbstractController`; logic goes in services.
- Use the framework for what it already does: Form, Validator, Serializer, Messenger, voters,
  `path()`/`url()` in Twig. Before hand-writing infrastructure (locks, queues, caches, HTTP clients),
  check for a Symfony component. `LockFactory` for mutual exclusion, never a hand-built flag.
- Request data: `#[MapRequestPayload]` / `#[MapQueryString]`, not `json_decode()`.
- Constructor property promotion; `readonly` for DTOs and value objects.
- New capability: `composer require <package>` and let the Flex recipe register it. Never hand-edit
  `config/bundles.php` or hand-write a bundle's base config.
- `maker-bundle`: pass every argument and `--no-interaction`; if a maker still prompts, write the code.
- Translations: grep all locales for a key before adding it (a duplicate key breaks the YAML file).

## Database

- Schema changes only through migrations (`make:migration`, `doctrine:migrations:migrate`), never
  `doctrine:schema:update` or hand-written SQL.
- Never edit a migration that has already run (check `doctrine:migrations:status`): add a follow-up one.
- Fixtures: `doctrine:fixtures:load` (doctrine-fixtures-bundle + Foundry factories). In dev they
  process ~400 MB of photos from `src/DataFixtures/images/`, which is left out of releases.

## Configuration and secrets

- `.env` is committed and holds defaults only. Real values go in `.env.local` / `.env.test.local`
  (git-ignored) or the secrets vault.
- The dev `MAILER_DSN` can be a real Postmark account: anything that may send emails outside the test
  environment (a local server, a console command, `/_check/messenger/email`) should run with
  `MAILER_DSN=null://null`.
- `bin/deploy.sh` (prod, or `TRACK=staging`) runs `composer install`, `importmap:install`,
  `asset-map:compile` and `cache:warmup`, and `symfony/requirements-checker` runs from composer's
  scripts: don't remove "unused" tooling without asking. Tests are not part of the deploy.

## Checks before committing

```bash
vendor/bin/php-cs-fixer fix                    # @Symfony + house style, see .php-cs-fixer.dist.php
vendor/bin/phpstan analyse                     # no new errors; don't add to phpstan-baseline.neon
bin/console lint:container                     # and again with HTTP_HOST=<HOST_CONTROLROOM>
bin/console lint:twig templates packs controlroom/templates   # bare lint:twig also lints vendor: 4 harmless errors
bin/console lint:translation                   # after translation changes
php bin/phpunit                                # after changes to controllers, templates, queries or forms
```

phpstan reads the controlroom container: after config changes, warm it first with
`HTTP_HOST=<HOST_CONTROLROOM> bin/console cache:warmup`.

## Tests

Deliberately small: static checks above, one smoke test, unit tests only for tricky services
(typically after a bug). Not run on deploy, no commit hook.

- `tests/Smoke/PagesTest.php` requests every page without route parameters, anonymously on the site
  and anonymously and as admin on the controlroom, and fails only on a 5xx. New pages are picked up
  from the router. Never assert on markup, wording, redirects or dates that age.
- The test database `guabao_test` (migrations + fixtures) is built by `tests/bootstrap.php` only when
  a migration or fixture file changes; each test is rolled back by dama/doctrine-test-bundle.
  `TEST_DB_REBUILD=1 php bin/phpunit` forces a rebuild. In test, picture fixtures upload a 64×32
  placeholder (`tests/fixtures/placeholder.jpg`) instead of the real photos.
- A new machine needs `.env.test.local` with `DATABASE_URL` (same as `.env.local`); the database
  user needs CREATEDB.

## Discover, don't guess

Framework APIs change between versions. Look things up in the project rather than relying on memory:
`bin/console about`, `debug:router`, `debug:container`, `debug:autowiring <name>`,
`config:dump-reference <bundle>`, the source and docblocks under `vendor/`, and
https://symfony.com/doc/current/. When something fails, read `var/log/<env>.log` and the profiler first.
