<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('build')
    ->exclude('vendor')
    ->in(__DIR__)
    ->name('*.php');

if (file_exists(__DIR__.'/local.php_cs')) {
    require __DIR__.'/local.php_cs';
}

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(
        [
            'ordered_use',
            'short_array_syntax',
            'phpdoc_order',
        ]
    )
    ->finder($finder);
