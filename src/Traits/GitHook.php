<?php

namespace ZeekBuildProcess\Traits;

trait GitHook
{
    private function setupGitHook(): void
    {
        $path = '.git/hooks';
        $file = 'pre-commit';

        $this->mkdir($path);

        $this->rsyncFileSafely($file, $path.'/'.$file);

        $this->exec(sprintf('chmod +x %s/%s',
            $path,
            $file
        ));
    }
}
