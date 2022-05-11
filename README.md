# Zeek Build Process

## What It Does
This package helps to set up a project with the following tools:

* Composer Packages
    * [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) (for automatic code styling fixes)
    * [PHP Linter](https://github.com/php-parallel-lint/PHP-Parallel-Lint) (for syntax checking)
    * [PHP Mess Detector](https://phpmd.org/) (detect code smells and possible errors within the analyzed source code)
    * [PHPStan](https://phpstan.org/) (static analyzer that examines code and looks for issues)
    * [Pest](https://pestphp.com/) (unit/feature testing framework)
* a `.node-version` file which sets the base `node` version for your project (useful for [fnm](https://github.com/Schniz/fnm))
* GitHub action workflow for automatic scanning on pushes/pull requests
* a `Makefile` to assist in running build and scanning commands in a consistent and simple manner
* installation to a `git` pre-commit hook that will automatically run the `cs-fixer`, `linter` and `phpstan`

## Requirements
Currently, this package only is meant for Laravel projects.

## Setup
Require this package as a `dev` dependency:

```bash
composer require --dev zeek/zeek-build-process
```

### Install
Install by running:

```bash
./vendor/bin/zbp install
```

This performs a safe, [no-clobber](https://unix.stackexchange.com/questions/572294/why-is-cps-option-not-to-overwrite-files-called-no-clobber) installation into an existing project. It does in an [idempotent](https://en.wikipedia.org/wiki/Idempotence) manner, so if you run the installation, make some changes to one of the configuration files and then run the installation again, it will not overwrite your changes.

### Reinstall
If you need to forcefully reinstall and start everything over from scratch you can run:

```bash
./vendor/bin/zbp reinstall
```

Be warned this will overwrite any changes you've made as well as any [baselines](https://phpstan.org/user-guide/baseline) you've created.

### Uninstall

If you'd like to completely remove all the tools, files and packages that this installs, you can run

```bash
./vendor/bin/zbp uninstall
```

### Git Hook Installation

If you just need to set up your own local `git` `pre-commit` hook, you can simply run:

```bash
./vendor/bin/zbp precommit
```

This will create a `.git/hooks/pre-commit` file that runs the `make precommit` command immediately before the actual git commit happens.

## Usage

A `Makefile` is installed that defines the commands (with configured flags/parameters) to utilize the tools in an easy and repeatable manner.

You can examine the [Makefile](https://github.com/ZeekInteractive/zeek-build-process/blob/main/templates/Makefile) source to really see what it's doing.

### Normal Usage

### Make Commands

Useful aliases:

Runs `cs-fixer`, `lint` and `phpstan`:
```bash
make precommit
```
---
Runs `cs-fixer`, `lint`, `phpstan` and `phpmd`:
```bash
make scan
```

---

Useful individual commands:
```bash
make cs-fixer
```
```bash
make lint
```
```bash
make phpmd
```
```bash
make phpstan
```
```bash
make pest
```

### Generating Baselines

If you've just installed and need to baseline your project so that the build system only looks at new code:
```bash
make baseline
```

If you want to run a specific tool baseline
```bash
make phpmd-baseline
```

```bash
make phpstan-baseline
```

## Git Hook
Upon installation, a `pre-commit` hook is created in `.git/hooks/pre-commit`. This hook runs immediately before you commit code.

It runs the `make precommit` alias, and will cause the commit to error out if any major problems are detected. 

You should run `make scan` on your own, as it includes the `phpmd` tool which attempts to give you guidance on good practices.

You can bypass the `pre-commit` hook by passing `--no-verify` to the commit command. This should be used sparingly and only when necessary.

## GitHub Action
A standardized GitHub action [`build.yml`](https://github.com/ZeekInteractive/zeek-build-process/blob/main/templates/build.yml) file is included which will do all the steps necessary to run the scan commands on every push to GitHub.

This build file will automatically review your code and pull requests and give feedback. You should attempt to fix whatever issues are reported, however this system is still in an experimental stage and it may report things that be irrelevant. If you have any questions please contact Aaron Holbrook.

## Customizing Configuration Files
It is completely possible to tweak the tool configurations for your individual project. A `build` [folder](https://github.com/ZeekInteractive/zeek-build-process/tree/main/build) is created upon the initial installation. 

Each tool has its own directory and configuration file. To tweak the individual file you will have to read the appropriate tool's documentation and see what works best.

