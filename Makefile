# Makefile for Docker Nginx PHP Composer MySQL

export CURRENT_UID

CURRENT_UID= $(shell id -u):$(shell id -g)

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  update       Update PHP dependencies with composer"
	@echo "  install      Install PHP dependencies with composer"
	@echo "  autoload     Update PHP autoload files"
	@echo "  cmd          Open terminal with php"

update:
	@docker run --rm \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		grandchef/composer:2.7.4 update --no-scripts --no-interaction

install:
	@docker run --rm \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		grandchef/composer:2.7.4 install --ignore-platform-reqs --no-scripts --no-interaction

cmd:
	@docker run --rm -it \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		-w /app \
		grandchef/php:7.4.27-fpm-dev /bin/bash

autoload:
	@docker run --rm \
		-u $(CURRENT_UID) \
		-v $(shell pwd):/app \
		-v /etc/passwd:/etc/passwd:ro \
		-v /etc/group:/etc/group:ro \
		grandchef/composer:2.7.4 dump-autoload --no-scripts --no-interaction
