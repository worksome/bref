<?php /** @noinspection ALL */
declare(strict_types=1);

/**
 * This file runs tests on Docker images.
 */

// All layers
$allLayers = [
    'bref/php-80',
    'bref/php-81',
];
foreach ($allLayers as $layer) {
    // Working directory
    $workdir = trim(`docker run --rm --entrypoint pwd $layer`);
    assertEquals('/var/task', $workdir);
    echo '.';

    // PHP runs correctly
    $phpVersion = trim(`docker run --rm --entrypoint php $layer -v`);
    assertMatchesRegex('/PHP (7|8)\.\d+\.\d+/', $phpVersion);
    echo '.';

    // Composer is installed correctly
    $composerVersion = trim(`docker run --rm --entrypoint composer $layer -V`);
    assertMatchesRegex('/Composer version 2\.\d+\.\d+.*/', $composerVersion);
    echo '.';

    // Git is installed correctly
    $gitVersion = trim(`docker run --rm --entrypoint git $layer --version`);
    assertMatchesRegex('/git version \d+\.\d+\.\d+/', $gitVersion);
    echo '.';

    // Unzip is installed correctly
    $unzipVersion = trim(`docker run --rm --entrypoint unzip $layer`);
    assertMatchesRegex('/UnZip \d+\.\d+ .*/', $unzipVersion);
    echo '.';

    // Test extensions load correctly
    // Skip this for PHP 8.0 and 8.1 until all extensions are supported
    if (strpos($layer, 'php-8') === false) {
        exec("docker run --rm -v \${PWD}/helpers:/var/task/ --entrypoint /var/task/extensions-test.sh $layer", $output, $exitCode);
        if ($exitCode !== 0) {
            throw new Exception(implode(PHP_EOL, $output), $exitCode);
        }
        echo '.';
    }
}

echo "\nTests passed\n";

function assertEquals($expected, $actual)
{
    if ($expected !== $actual) {
        throw new Exception("$actual is not equal to expected $expected");
    }
}

function assertMatchesRegex(string $expected, string $actual)
{
    if (preg_match($expected, $actual) === false) {
        throw new Exception("$actual does not match regex $expected");
    }
}
