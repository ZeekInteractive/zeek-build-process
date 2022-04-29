<?php

namespace ZeekBuildProcess\Traits;

use const PHP_EOL;

trait Help
{
    private function maybeHandleHelp(): int
    {
        if (in_array('exec', explode(',', ini_get('disable_functions')), true)) {
            echo "Function 'exec' is required, but it is disabled by the 'disable_functions' ini setting.", PHP_EOL;

            return self::FAILED;
        }

        if (in_array('-v', $_SERVER['argv'], true) || in_array('--version', $_SERVER['argv'], true)) {
            $this->showVersion();

            return self::SUCCESS;
        }

        if (in_array('-h', $_SERVER['argv'], true) || in_array('--help', $_SERVER['argv'], true) || 1 === $_SERVER['argc']) {
            $this->showUsage();

            return self::SUCCESS;
        }

        return -1;
    }

    /**
     * Outputs the options
     */
    private function showOptions(): void
    {
        echo <<<HELP
Options:
    -h, --help              Print this help
    -V, --version           Display the application version

HELP;
    }

    /**
     * Outputs the current version
     */
    private function showVersion(): void
    {
        echo 'Zeek Build Process '.self::VERSION.PHP_EOL;
    }

    /**
     * Shows usage
     */
    private function showUsage(): void
    {
        $this->showVersion();
        echo <<<USAGE
-------------------------------
Usage:
zbp install                 Install the build process into this project
zbp reinstall               Re-install build process (overwrites all build process files)
zbp uninstall               Remove all build related files and packages


USAGE;
        $this->showOptions();
    }
}
