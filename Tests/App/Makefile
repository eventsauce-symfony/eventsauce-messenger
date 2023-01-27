.DEFAULT_GOAL=help

DOCKER_COMPOSE = docker-compose
USE_BUILDKIT = COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1

help:
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {\
		printf "\033[36m%-20s\033[0m %s\n", $$1, $$NF \
		}' $(MAKEFILE_LIST)

up: ## Create and start app
	@$(USE_BUILDKIT) $(DOCKER_COMPOSE) up -d --build

remove: ## Stop and remove app
	@$(DOCKER_COMPOSE) down --rmi local

start: ## Start app
	@$(DOCKER_COMPOSE) start

stop: ## Stop app
	@$(DOCKER_COMPOSE) stop

logs: ## Show logs
	@$(DOCKER_COMPOSE) logs -f

cli: ## Run php cli
	@$(DOCKER_COMPOSE) exec php sh

update: ## Run composer update
	@$(DOCKER_COMPOSE) exec php composer update --ignore-platform-reqs

cc: ## Clear cache
	@$(DOCKER_COMPOSE) exec php ./bin/console c:c

setup: ## Setup
	mkdir -p component
	rsync -av --progress ../../Attribute ../../DependencyInjection ../../Dispatcher ../../Middleware ../../Stamp ../../EventConsumer ./component
	composer dumpautoload
	@$(DOCKER_COMPOSE) exec php composer install
	@$(DOCKER_COMPOSE) exec php ./bin/console d:d:d --force
	@$(DOCKER_COMPOSE) exec php ./bin/console d:d:c
	@$(DOCKER_COMPOSE) exec php ./bin/console d:m:m --no-interaction

component-sync: ## Component sync
	mkdir -p component
	rsync -av --progress ../../Attribute ../../DependencyInjection ../../Dispatcher ../../Middleware ../../Stamp ../../EventConsumer ./component
	composer dumpautoload
	@$(DOCKER_COMPOSE) exec php ./bin/console c:c