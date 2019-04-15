build: vendor

rebuild: delete-vendor build

.PHONY: delete-vendor
delete-vendor:
	rm -rf vendor
	rm composer.lock

vendor:
	composer update --no-interaction --optimize-autoloader --prefer-dist

all: vendor lint code-style phpstan test

.PHONY: lint
lint:
	vendor/bin/parallel-lint -e php,phpt --exclude vendor .

.PHONY: code-style
code-style:
	vendor/bin/phpcs --standard=vendor/damejidlo/coding-standard/DameJidloCodingStandard/ruleset.xml --extensions=php,phpt -s src tests

.PHONY: phpstan
phpstan:
	vendor/bin/phpstan analyse -l max -c tests/phpstan.src.neon src
	IS_PHPSTAN=1 vendor/bin/phpstan analyse -l max -c tests/phpstan.tests.neon tests

.PHONY: autoload
autoload:
	composer dump-autoload

.PHONY: test
test: autoload
	vendor/bin/tester --info
	vendor/bin/tester tests
