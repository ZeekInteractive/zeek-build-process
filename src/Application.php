<?php

namespace ZeekBuildProcess;

use ZeekBuildProcess\Traits\ComposerPackages;
use ZeekBuildProcess\Traits\GitHook;
use ZeekBuildProcess\Traits\Github;
use ZeekBuildProcess\Traits\Help;
use ZeekBuildProcess\Traits\ShellUtils;

class Application
{
    use Help;
    use ShellUtils;
    use Github;
    use GitHook;
    use ComposerPackages;

    private const VERSION = '1.3.2';

    // Return codes
    private const RUNNING = -1;
    private const SUCCESS = 0;
    private const WITH_ERRORS = 1;
    private const FAILED = 254; // Error code 255 is reserved for PHP itself

    private string $templateDir;

    /**
     * Run the application
     * @return int Return code
     */
    public function run(): int
    {
        $status = $this->maybeHandleHelp();
        if ($status !== self::RUNNING) {
            return $status;
        }

        try {
            // Set up our paths
            $this->templateDir = dirname(__DIR__).'/build/templates';

            // do work
            $this->rsyncFileSafely('.node-version');
            $this->rsyncFileSafely('Makefile');
            $this->setupGithubWorkflows();
            $this->setupGitHook();
            $this->setupComposerPackages();
        } catch (\Exception $e) {
            return self::WITH_ERRORS;
        }

        return self::SUCCESS;
    }
}
