<?php

namespace ZeekBuildProcess\Traits;

trait ComposerPackages
{
    private function setupComposerPackages(): void
    {
        $this->exec(sprintf('composer require --dev -n %s',
            implode(' ', self::COMPOSER_PACKAGES)
        ));
    }

    private function removeComposerPackages(): void
    {
        $this->exec(sprintf('composer remove --dev -n %s',
            implode(' ', self::COMPOSER_PACKAGES)
        ));
    }
}
