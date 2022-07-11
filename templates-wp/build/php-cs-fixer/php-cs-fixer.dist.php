<?php

$appDir = dirname(__DIR__, 2);

$finder = PhpCsFixer\Finder::create()
                           ->in($appDir.'/mu-plugins/app')
                           ->in($appDir.'/themes/{{THEME_NAME}}')
                           ->name('*.php')
                           ->notName('*.env.php')
                           ->ignoreDotFiles(true)
                           ->ignoreVCS(true)
                           ->exclude('vendor');

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@PSR12'                      => true,
        'indentation_type'            => true,
        'array_indentation'           => true,
        'braces'                      => true,
        'method_chaining_indentation' => true,
        'no_extra_blank_lines'        => true,
        'align_multiline_comment'     => true,
        'array_syntax'                => ['syntax' => 'short'],
    ]
)->setFinder($finder)
              ->setUsingCache(true)
              ->setCacheFile(__DIR__.'/.php-cs-fixer.cache');

