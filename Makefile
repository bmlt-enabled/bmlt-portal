COMMIT := $(shell git rev-parse HEAD)
BASENAME := $(shell basename $(PWD))
BUILD_DIR := $(or $(BUILD_DIR),build)
ZIP_FILENAME := $(or $(ZIP_FILENAME),$(BASENAME).zip)
VENDOR_AUTOLOAD := vendor/autoload.php
PHPCS := vendor/squizlabs/php_codesniffer/bin/phpcs
PHPCS_ARGS := --warning-severity=6 --standard=PSR2 --ignore=vendor --extensions=php .

ifeq ($(PROD)x, x)
	COMPOSER_ARGS := --prefer-dist --no-progress
else
	COMPOSER_ARGS := --no-dev --prefer-dist --no-progress --classmap-authoritative
endif

help:  ## Print the help documentation
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

$(VENDOR_AUTOLOAD):
	composer install $(COMPOSER_ARGS)

.PHONY: composer
composer: $(VENDOR_AUTOLOAD)  ## Install composer dependencies

.PHONY: lint
lint: composer  ## Lint PHP (PSR2)
	$(PHPCS) --report=full $(PHPCS_ARGS)

.PHONY: fmt
fmt: composer  ## Auto-fix PHP formatting
	vendor/squizlabs/php_codesniffer/bin/phpcbf $(PHPCS_ARGS)

.PHONY: build
build:  ## Build the distributable zip into build/ (PROD=1 for a no-dev vendor)
	composer install $(COMPOSER_ARGS)
	rm -rf $(BUILD_DIR)
	mkdir -p $(BUILD_DIR)/$(BASENAME)
	git archive --format=tar $(COMMIT) | tar -x -C $(BUILD_DIR)/$(BASENAME)
	echo "$(COMMIT)" > $(BUILD_DIR)/$(BASENAME)/build.txt
	cp -R vendor $(BUILD_DIR)/$(BASENAME)/
	cd $(BUILD_DIR) && zip -r -9 $(ZIP_FILENAME) $(BASENAME)

.PHONY: clean
clean:  ## Remove build artifacts
	rm -rf $(BUILD_DIR)

.PHONY: docker-build
docker-build:  ## Build the Docker image
	docker build -f docker/Dockerfile . -t $(BASENAME):latest

.PHONY: up
up:  ## Start the app with docker compose (http://localhost:8888)
	docker compose up --build

.PHONY: down
down:  ## Stop the docker compose stack
	docker compose down
