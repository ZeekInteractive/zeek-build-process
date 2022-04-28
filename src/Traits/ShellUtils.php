<?php

namespace ZeekBuildProcess\Traits;

use const PHP_EOL;

trait ShellUtils
{

    private function mkdir(string $path): void
    {
        $this->exec(sprintf('mkdir -p %s', $path));
    }

    private function rsyncFileSafely(string $file, ?string $destination = null): void
    {
        if (empty($destination)) {
            $destination = $file;
        }

        $this->exec(sprintf('rsync --ignore-existing %s/%s %s',
            $this->templateDir,
            $file,
            $destination
        ));
    }

    private function info(string $message): void
    {
        echo $message, PHP_EOL;
    }

    private function exec(string $command): void
    {
        exec($command);
    }
}
