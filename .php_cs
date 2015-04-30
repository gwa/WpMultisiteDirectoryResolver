<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('build')
    ->exclude('vendor')
    ->in(__DIR__)
    ->notName('*.phar')
    ->notName('LICENSE')
    ->notName('README.md')
    ->notName('composer.*')
    ->notName('phpunit.xml.dist')
    ->notName('.scrutinizer.yml')
    ->notName('.travis.yml')
    ->notName('phpcs.xml')
    ->notName('coveralls.yml')
    ->notName('.styleci.yml')
    ->notName('CONTRIBUTING')
    ->notName('.php_cs');

if (file_exists(__DIR__.'/local.php_cs')) {
    require __DIR__.'/local.php_cs';
}

return Symfony\CS\Config\Config::create()
    // use default SYMFONY_LEVEL and extra fixers:
    ->fixers(
        [
            'ordered_use',
            'short_array_syntax',
            'strict',
            'strict_param',
            '-no_empty_lines_after_phpdocs',
            'header_comment',
            'newline_after_open_tag',
            'phpdoc_order',
            'phpdoc_var_to_type',
            '-phpdoc_to_comment',
        ]
    )
    ->finder($finder);
