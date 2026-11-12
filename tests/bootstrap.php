<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

buildTestDatabaseIfOutdated(dirname(__DIR__));

/**
 * The test database (migrations + fixtures) is built once and reused: each test runs in a
 * transaction rolled back by dama/doctrine-test-bundle, so it never drifts.
 *
 * It is rebuilt when its inputs change: a migration or the fixtures code. Force a rebuild with
 * TEST_DB_REBUILD=1 php bin/phpunit.
 *
 * Picture fixtures upload a tiny placeholder into var/test/uploads in this environment (see
 * config/parameters.yaml), so building stays fast and never touches public/uploads.
 */
function buildTestDatabaseIfOutdated(string $projectDir): void
{
    $fixtureFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($projectDir . '/src/DataFixtures', FilesystemIterator::SKIP_DOTS));
    $inputs = [
        ...glob($projectDir . '/migrations/*.php'),
        ...array_filter(array_map('strval', iterator_to_array($fixtureFiles, false)), static fn (string $file): bool => str_ends_with($file, '.php')),
    ];
    sort($inputs);
    $checksum = hash('xxh128', implode('', array_map(
        static fn (string $file): string => $file . hash_file('xxh128', $file),
        $inputs,
    )));

    $checksumFile = $projectDir . '/var/test-database.checksum';
    if (!getenv('TEST_DB_REBUILD') && @file_get_contents($checksumFile) === $checksum) {
        return;
    }

    echo "Building the test database (migrations + fixtures)...\n";
    (new Filesystem())->remove($projectDir . '/var/test/uploads');
    $console = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($projectDir . '/bin/console') . ' --env=test --no-interaction';
    foreach ([
        'doctrine:database:drop --force --if-exists',
        'doctrine:database:create',
        'doctrine:migrations:migrate --quiet',
        'doctrine:fixtures:load --quiet',
    ] as $command) {
        passthru("$console $command > /dev/null", $exitCode);
        if ($exitCode !== 0) {
            @unlink($checksumFile);
            throw new RuntimeException("Building the test database failed on: bin/console $command --env=test");
        }
    }

    file_put_contents($checksumFile, $checksum);
}
