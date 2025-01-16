.PHONY: build test lint

build:
	composer install

test:
	composer test:unit

lint:
	composer lint
