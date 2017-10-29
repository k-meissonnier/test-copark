ifeq "$(shell whoami )" "root"
	CONTAINER_USERNAME = root
	CONTAINER_GROUPNAME = root
	HOMEDIR = /root
	CREATE_USER_COMMAND =
else
	CONTAINER_USERNAME = $(USER)
	CONTAINER_GROUPNAME = $(USER)
	HOMEDIR = /home/$(CONTAINER_USERNAME)
	GROUP_ID = $(shell id -g)
	USER_ID = $(shell id -u)
	CREATE_USER_COMMAND = \
       ( (if apk --version >/dev/null 2>&1; then \
		addgroup -g $(GROUP_ID) $(CONTAINER_GROUPNAME) && \
		adduser -D -u $(USER_ID) -G $(CONTAINER_GROUPNAME) $(CONTAINER_USERNAME); else \
		groupadd -f -g $(GROUP_ID) $(CONTAINER_GROUPNAME) && \
		useradd -u $(USER_ID) -g $(CONTAINER_GROUPNAME) $(CONTAINER_USERNAME); fi) && \
		mkdir -p $(HOMEDIR) ) > /dev/null 2>&1 &&
endif

AUTHORIZE_HOME_DIR_COMMAND = chown -R $(CONTAINER_USERNAME):$(CONTAINER_GROUPNAME) $(HOMEDIR) &&
EXECUTE_AS = sudo -E -u $(CONTAINER_USERNAME) HOME=$(HOMEDIR)

ADD_SSH_ACCESS_COMMAND = \
  mkdir -p $(HOMEDIR)/.ssh && \
  test -e /var/tmp/id && cp /var/tmp/id $(HOMEDIR)/.ssh/id_rsa ; \
  test -e /var/tmp/known_hosts && cp /var/tmp/known_hosts $(HOMEDIR)/.ssh/known_hosts ; \
  test -e $(HOMEDIR)/.ssh/id_rsa && chmod 600 $(HOMEDIR)/.ssh/id_rsa ;

USER_CMD=$(CREATE_USER_COMMAND) $(ADD_SSH_ACCESS_COMMAND) $(AUTHORIZE_HOME_DIR_COMMAND) $(EXECUTE_AS)

#====================
# Project VARS
#====================
step=/////////////////////////
project=test-copark
projectCompose=testcopark
console_path=bin/console
optional_containers=

composeFile = docker-compose-$(PROJECT_ENV).yml
compose = docker-compose -f $(composeFile) -p $(projectCompose)
PHP_DOCKER_CMD=$(compose) run --rm php

sf=$(USER_CMD) $(console_path)

cache_log_prefix=var

#====================
# Docker
#====================
build: remove
	@echo "$(step) Build $(project) $(step)"
	@$(compose) build $(COMMAND_ARGS)
	@docker run --rm \
	    -v `pwd`:/tmp \
	        debian:8 bash -c 'rm -rf /tmp/*.tar'

stop:
	@echo "$(step) Stopping $(project) $(step)"
	@$(compose) stop

remove: stop
	@echo "$(step) Remove $(project) $(step)"
	@$(compose) rm --force

start:
	@echo "$(step) Starting $(project) $(step)"
	@$(compose) up -d --remove-orphans web $(optional_containers)


#====================
# Symfony
#====================
DB_DROP_CMD=$(sf) doctrine:database:drop --if-exists --force || true
DB_CREATE_CMD=$(sf) doctrine:database:create --if-not-exists $(COMMAND_ARGS)
DB_MIG_CMD=$(sf) doctrine:migrations:migrate -n $(COMMAND_ARGS)

cache-clear:
	@echo "$(step) Clear cache $(SYMFONY_ENV) $(step)"
	@$(PHP_DOCKER_CMD) 'rm -rf $(cache_log_prefix)/cache/$(SYMFONY_ENV) \
	    && $(sf) cache:warmup'

cc: cache-clear

database-drop:
	@echo "$(step) Drop database $(SYMFONY_ENV) $(step)"
	@$(PHP_DOCKER_CMD) "$(DB_DROP_CMD)" || true

database-create:
	@echo "$(step) Create database $(SYMFONY_ENV) $(step)"
	@$(PHP_DOCKER_CMD) "$(DB_CREATE_CMD)"

fixtures:
	@echo "$(step) Installing fixtures $(SYMFONY_ENV) $(step)"
	@$(PHP_DOCKER_CMD) "$(sf) hautelook_alice:doctrine:fixtures:load -n $(COMMAND_ARGS)"

database:
	@echo "$(step) Database drop, create, migration $(SYMFONY_ENV) $(step)"
	@$(PHP_DOCKER_CMD) "$(DB_DROP_CMD) && $(DB_CREATE_CMD) && $(DB_MIG_CMD)"

migrations:
	@echo "$(step) Create database migration $(step)"
	@$(PHP_DOCKER_CMD) "$(sf) doctrine:migrations:diff $(COMMAND_ARGS)"

migrations-apply:
	@echo "$(step) Apply database migration $(step)"
	@$(PHP_DOCKER_CMD) "$(DB_MIG_CMD)"

assets:
	@echo "$(step) Installation assets $(SYMFONY_ENV) $(step)"
	@$(PHP_DOCKER_CMD) "$(sf) assets:install --symlink $(COMMAND_ARGS)"

#====================
# MySQL
#====================
mysql:
	@echo "$(step) MySQL $(project) $(step)"
	@$(compose) run --rm db /bin/bash -c "mysql -hdb -u\$$MYSQL_USER -p\$$MYSQL_PASSWORD \$$MYSQL_DATABASE"


#====================
# Composer
#====================
ifeq ($(SYMFONY_ENV), prod)
  composer_options=--no-dev -o
else
  composer_options=
endif

composer-install:
	@$(compose) run --rm composer bash -c '\
      composer install $(composer_options) --ignore-platform-reqs --no-interaction --prefer-dist $(COMMAND_ARGS)'

composer-update:
	@$(compose) run --rm composer bash -c '\
      composer update --ignore-platform-reqs --no-interaction --prefer-dist $(COMMAND_ARGS)'

composer-require:
	@$(compose) run --rm composer bash -c '\
      composer require --ignore-platform-reqs --no-interaction --prefer-dist $(COMMAND_ARGS)'

composer-dump:
	@$(compose) run --rm composer bash -c '\
      composer dump-autoload $(composer_options) $(COMMAND_ARGS)'

composer-bash:
	@$(compose) run --rm composer bash

composer-create:
	@$(compose) run --rm composer bash -c '\
      composer create-project symfony/framework-standard-edition project'

#====================
# Assets
#====================
BUILDER_DOCKER_CMD=$(compose) run --rm builder bash -c

NPM_CMD=$(USER_CMD) npm install --loglevel info $(COMMAND_ARGS)

YARN_CMD=$(USER_CMD) yarn install --cache-folder=${HOMEDIR}/.yarn/cache

GULP_CMD=$(USER_CMD) gulp --env=$(SYMFONY_ENV) --target=$(PROJECT_ENV) $(COMMAND_ARGS)

npm-install:
	@echo "$(step) Installation npm $(step)"
	@$(BUILDER_DOCKER_CMD) '$(YARN_CMD)'

gulp:
	@echo "$(step) Run gulp $(step)"
	@$(BUILDER_DOCKER_CMD) '$(GULP_CMD)'

gulp-watch:
	@echo "$(step) Run gulp watch $(step)"
	@$(BUILDER_DOCKER_CMD) '$(USER_CMD) gulp watch'

#====================
# Project
#====================
synchronize:
	@echo "$(step) Synchronize parking $(step)"
	@$(PHP_DOCKER_CMD) "$(sf) app:parking:synchronize $(COMMAND_ARGS)"

install: install-app install-db cc
install-app: remove build composer-install npm-install gulp assets
install-db: database-drop database-create migrations-apply synchronize