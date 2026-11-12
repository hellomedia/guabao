<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        // House style: `$dir . '/' . $name` and `$x === null`, as the code was already written.
        'concat_space' => ['spacing' => 'one'],
        'yoda_style' => false,
        // No trailing dot forced at the end of docblock summaries.
        'phpdoc_summary' => false,
        // Promoted constructors keep their `) {}`.
        'single_line_empty_body' => true,
    ])
    ->setFinder($finder)
;
