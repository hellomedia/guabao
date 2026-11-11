<?php

/**
 * Doctrine entity manager for phpstan/phpstan-doctrine (parameters.doctrine.objectManagerLoader in
 * phpstan.dist.neon): gives phpstan the real mapping, so getRepository(X::class) resolves to the custom
 * repository class and DQL / QueryBuilder calls get checked.
 * https://github.com/phpstan/phpstan-doctrine#configuration
 */

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__, 2) . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
