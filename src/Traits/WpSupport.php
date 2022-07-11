<?php

namespace ZeekBuildProcess\Traits;

trait WpSupport
{
    private function wpSpecificReplacements(): void
    {
        if ( ! $this->isWp) {
            return;
        }

        $theme = readline('WordPress Theme name (folder name only): ' );

        if ( empty( $theme ) ) {
            return;
        }

        $this->sedSearchReplaceInFile('Makefile', '{{THEME_NAME}}', $theme);
        $this->searchReplaceTextInDirectory('build', '{{THEME_NAME}}', $theme);
    }
}
