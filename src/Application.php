<?php

namespace ZeekBuildProcess;

use ZeekBuildProcess\Exceptions\UnknownCommandException;
use ZeekBuildProcess\Traits\ComposerPackages;
use ZeekBuildProcess\Traits\GitHook;
use ZeekBuildProcess\Traits\Github;
use ZeekBuildProcess\Traits\Help;
use ZeekBuildProcess\Traits\ShellUtils;
use ZeekBuildProcess\Traits\WpSupport;

class Application
{
    use Help;
    use ShellUtils;
    use Github;
    use GitHook;
    use ComposerPackages;
    use WpSupport;

    /**
     * The current version of this package
     */
    private const VERSION = '0.1.0';

    /**
     * Whether to overwrite existing build process files
     * By default it will NOT overwrite existing files in cases where someone has altered them for the project
     *
     * @var bool
     */
    private bool $noClobber = true;
    private string $pwd;
    private bool $isWp = false;

    /**
     * Return codes
     */
    private const RUNNING = -1;
    private const SUCCESS = 0;
    private const WITH_ERRORS = 1;
    private const FAILED = 254;

    /**
     * The directory internal to this package that we will be using to grab our templates from
     *
     * @var string
     */
    private string $templateDir;

    /**
     * Run the application
     * @return int Return code
     */
    public function run(): int
    {
        $this->init();

        $status = $this->maybeHandleHelp();
        if ($status !== self::RUNNING) {
            return $status;
        }

        try {
            $command = $this->determineCommand();

            switch ($command) {
                case 'install':
                    $this->install();

                    break;

                case 'reinstall';
                    $this->noClobber = false;

                    $this->install();
                    break;

                case 'precommit':
                    $this->noClobber = false;

                    $this->setupGitHook();

                    break;
                case 'uninstall';
                    $this->uninstall();

                    break;
            }
        } catch (UnknownCommandException $e) {
            $this->info('Unknown command');

            return self::WITH_ERRORS;
        } catch (\Exception $e) {
            return self::WITH_ERRORS;
        }

        return self::SUCCESS;
    }

    private function init(): void
    {
        $this->pwd = $_SERVER['PWD'];
        $this->isWp = str_contains($this->pwd, '/wp-content');

        $templateDir = sprintf('templates%s',
            $this->isWp ? '-wp' : ''
        );

        $this->templateDir = sprintf('%s/%s',
            dirname(__DIR__),
            $templateDir
        );
    }

    /**
     * @return string
     * @throws UnknownCommandException
     */
    private function determineCommand(): string
    {
        $argv = $_SERVER['argv'];

        if (in_array('uninstall', $_SERVER['argv'], true)) {
            return 'uninstall';
        }

        if (in_array('reinstall', $_SERVER['argv'], true)) {
            return 'reinstall';
        }

        if (in_array('precommit', $_SERVER['argv'], true)) {
            return 'precommit';
        }

        if (in_array('install-wp', $_SERVER['argv'], true)) {
            return 'install-wp';
        }

        if (in_array('install', $_SERVER['argv'], true)) {
            return 'install';
        }

        throw new UnknownCommandException();
    }

    private function install(): void
    {
        $this->rsyncFileSafely('.node-version');
        $this->rsyncFileSafely('Makefile');

        $this->setupGithubWorkflows();
        $this->setupGitHook();

        $this->setupComposerPackages();

        $this->rsyncFileSafely('build/', 'build');

        $this->wpSpecificReplacements();
    }

    private function uninstall(): void
    {
        $this->rm('.node-version');
        $this->rm('Makefile');
        $this->rm('.github');
        $this->rm('.git/hooks/pre-commit');

        $this->removeComposerPackages();

        $this->rm('build');
    }
}
