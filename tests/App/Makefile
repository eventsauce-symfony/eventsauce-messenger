.DEFAULT_GOAL=help

DOCKER_COMPOSE = docker-compose
USE_BUILDKIT = COMPOSE_DOCKER_CLI_BUILD=1 DOCKER_BUILDKIT=1

help:
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {\
		printf "\033[36m%-20s\033[0m %s\n", $$1, $$NF \
		}' $(MAKEFILE_LIST)

up: ## Create and start app
	@$(USE_BUILDKIT) $(DOCKER_COMPOSE) up -d --build

down: ## Stop and remove app
	@$(DOCKER_COMPOSE) down --rmi local

start: ## Start app
	@$(DOCKER_COMPOSE) start

stop: ## Stop app
	@$(DOCKER_COMPOSE) stop

ps: ## List containers
	@$(DOCKER_COMPOSE) ps

logs: ## Show logs
	@$(DOCKER_COMPOSE) logs -f

cli: ## Run php cli
	@$(DOCKER_COMPOSE) exec php sh

test: ## Run phpunit
	@$(DOCKER_COMPOSE) exec php ./bin/phpunit