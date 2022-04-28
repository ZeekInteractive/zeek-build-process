<?php

namespace ZeekBuildProcess\Traits;

trait ComposerPackages
{
    private function setupComposerPackages(): void
    {
        $packages = [
            'friendsofphp/php-cs-fixer',
            'pestphp/pest',
            'php-parallel-lint/php-console-highlighter',
            'php-parallel-lint/php-parallel-lint',
            'phpmd/phpmd',
            'phpstan/phpstan',
        ];

        $this->exec(sprintf('composer require --dev -n %s',
            implode(' ', $packages)
        ));
    }
}
