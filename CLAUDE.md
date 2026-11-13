# CLAUDE.md

Project rules are in AGENTS.md, which Claude Code loads on its own. This file only holds what is
specific to running Claude in this project's dev container (the `claude-code` compose service).
Preferences shared by all of Nicolas's projects are in `~/.claude/CLAUDE.md`, a link to
`/home/node/shared/CLAUDE.md` (host `../claude-shared`, see its README.md).

- PHP 8.5 with pdo_pgsql, gd and imagick is available: run `bin/console`, phpstan, php-cs-fixer and
  `php bin/phpunit` directly. There is no docker CLI and no sudo.
- Packagist is blocked by the container firewall: ask the user to run `composer require/update`
  (they can type `! composer ...` in the prompt), then review the recipe changes.
- Never experiment on the dev database (`guabao`): the user works with its data. Prefer
  `php bin/phpunit` (its own `guabao_test` database) to check pages; for anything else, use a
  throwaway copy (`CREATE DATABASE <name> TEMPLATE guabao`) and drop it afterwards.
- To look at a dev page without nginx: `SMOKE_HOST=<host> php docs/upgrade-kit/smoke.php /path`
  (see its header for logging in).
