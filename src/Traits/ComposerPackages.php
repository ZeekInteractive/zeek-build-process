<?php

namespace ZeekBuildProcess\Traits;

use Yosymfony\Toml\Toml;

trait ComposerPackages
{
    private function composerPackages(): array {
        $packages = Toml::parseFile($this->templateDir . '/zbp.toml');

        return $packages['packages'];
    }

    private function setupComposerPackages(): void
    {
        $this->exec(sprintf('composer require --dev -n --quiet %s',
            implode(' ', $this->composerPackages())
        ));
    }

    private function removeComposerPackages(): void
    {
        $this->exec(sprintf('composer remove --dev -n --quiet %s',
            implode(' ', $this->composerPackages())
        ));
    }
}
