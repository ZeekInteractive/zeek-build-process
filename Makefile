.DEFAULT_GOAL := help
.PHONY: $(filter-out vendor node_modules,$(MAKECMDGOALS))

bin = ./vendor/bin
template = ./build/templates
tools = friendsofphp/php-cs-fixer pestphp/pest php-parallel-lint/php-console-highlighter php-parallel-lint/php-parallel-lint phpmd/phpmd phpstan/phpstan

help: ## This help message
	@printf "\033[33mUsage:\033[0m\n  make [target]\n\n\033[33mTargets:\033[0m\n"
	@grep -E '^[-a-zA-Z0-9_\.\/]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-15s\033[0m %s\n", $$1, $$2}'

setup: versions ## Set up build system (Run this the first time)
	@composer require --dev -n $(tools)
	@rsync --ignore-existing $(template)/.env.ci .env.ci
	@rsync --ignore-existing $(template)/.node-version .node-version
	@mkdir -p .github/workflows && rsync --ignore-existing $(template)/build.yml .github/workflows/build.yml
	@mkdir -p .git/hooks && cp $(template)/pre-commit .git/hooks/pre-commit && chmod +x .git/hooks/pre-commit

# Version Management
versions: ## Set PHP version
	@valet use php@8.1

## Build Processes
vendor: composer.json composer.lock ## Install PHP dependencies
	@composer install --quiet -n
	@echo "PHP dependencies installed."

node_modules: package.json package-lock.json ## Install Node modules
	@npm install --silent
	@echo "Npm dependencies installed."

clean: ## Removes all build dependencies (vendor, node_modules)
	@rm -rf vendor/ node_modules/
	@echo "Dependencies removed."

# Build Tooling
cs-fixer: ## Code styling fixer
	@$(bin)/php-cs-fixer fix --config=build/php-cs-fixer/.php-cs-fixer.dist.php --quiet

lint: ## PHP Syntax Checking
	@$(bin)/parallel-lint -j 10 app config routes --no-progress --colors --blame

phpmd: ## PHP Mess Detection
	@$(bin)/phpmd app ansi build/phpmd/phpmd.xml

phpstan: ## PHP Static Analyzer
	@$(bin)/phpstan analyse --error-format=table -c build/phpstan/phpstan.neon.dist

phpstan-baseline: ## PHP Static Analyzer. Generate Baseline.
	@$(bin)/phpstan analyse --error-format=table -c build/phpstan/phpstan.neon.dist --generate-baseline=build/phpstan/phpstan-baseline.neon

# Testing. Requires installing Pest
#pest: ## PHP Tests
#	$(bin)/pest --colors=always -c build/pest/phpunit.xml

# Aliases
precommit: cs-fixer lint phpstan ## Run style fixing and linting commands
scan: cs-fixer lint phpmd phpstan ## Run all scans including mess detection and static analysis
build: versions clean vendor node_modules precommit ## Recompile all assets from scratch
