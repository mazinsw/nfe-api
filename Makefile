# Makefile for Docker Nginx PHP Composer MySQL

export CURRENT_UID

CURRENT_UID= $(shell id -u):$(shell id -g)

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  install      Install PHP dependencies with composer"
	@echo "  update       Update PHP dependencies with composer"
	@echo "  autoload     Update PHP autoload files"
	@echo "  test         Run PHPUnit Tests"
	@echo "  cmd          Open terminal with php"

install:
	@docker run --rm \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		grandchef/composer:2.8.2 install --ignore-platform-reqs --no-scripts --no-interaction

update:
	@docker run --rm \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		grandchef/composer:2.8.2 update --no-scripts --no-interaction

autoload:
	@docker run --rm \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		grandchef/composer:2.8.2 dump-autoload --no-scripts --no-interaction

test:
	@docker run --rm -it \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-w /app \
		grandchef/php:8.2.2-fpm-dev php ./vendor/bin/phpunit --configuration . --no-coverage --colors=always

cmd:
	@docker run --rm -it \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		-w /app \
		grandchef/php:8.2.2-fpm-dev /bin/bash
