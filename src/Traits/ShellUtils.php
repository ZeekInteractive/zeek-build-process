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

        $this->exec(sprintf('rsync -a %s %s/%s %s',
            $this->noClobber ? '--ignore-existing' : '',
            $this->templateDir,
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

    private function sedSearchReplaceInFile(string $file, string $original, string $replacement): void
    {
        $this->exec("sed -i '' 's/$original/$replacement/g' $file");
    }

    private function searchReplaceTextInDirectory(string $directory, string $original, string $replacement): void {
        $this->exec("grep -rl $original $directory | xargs sed -i '' 's/$original/$replacement/g'");
    }
}
