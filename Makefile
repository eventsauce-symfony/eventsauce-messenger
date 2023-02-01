.DEFAULT_GOAL=help

help:
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {\
		printf "\033[36m%-20s\033[0m %s\n", $$1, $$NF \
		}' $(MAKEFILE_LIST)

fix-cs: ## Fix cs
	PHP_CS_FIXER_IGNORE_ENV=1 tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --allow-risky=yes

phpunit: ## Run phpunit tests
	vendor/bin/phpunit --color

phpstan: ## Run phpstan
	vendor/bin/phpstan --memory-limit=1G

update: ## Run composer update
	@$(DOCKER_COMPOSE) composer update

test: ## Run phpunit and phpstan
	vendor/bin/phpunit --color
	vendor/bin/phpstan --memory-limit=1G