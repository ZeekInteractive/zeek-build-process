<?php

namespace ZeekBuildProcess\Traits;

use const PHP_EOL;

trait ShellUtils
{

    private function mkdir(string $path): void
    {
        $this->exec(sprintf('mkdir -p %s', $path));
    }

    private function rsyncFileSafely(string $file, ?string $destination = null, ?string $baseDir = null): void
    {
        if (empty($destination)) {
            $destination = $file;
        }

        if ( empty( $baseDir ) ) {
            $baseDir = $this->templateDir;
        }

        $this->exec(sprintf('rsync -a %s %s/%s %s',
            $this->noClobber ? '--ignore-existing' : '',
            $baseDir,
            $file,
            $destination
        ));
    }

    private function rm(string $path): void
    {
        $this->exec('rm -rf '.$path);
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
