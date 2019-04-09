build: vendor

vendor:
	composer update --no-interaction --optimize-autoloader --prefer-dist


.PHONY: all lint code-style phpstan test
all: vendor lint code-style phpstan test

lint:
	vendor/bin/parallel-lint -e php,phpt --exclude vendor .

code-style:
	vendor/bin/phpcs --standard=vendor/damejidlo/coding-standard/DameJidloCodingStandard/ruleset.xml --extensions=php,phpt -s src tests

phpstan:
	vendor/bin/phpstan analyse -l max -c tests/phpstan.src.neon src
	IS_PHPSTAN=1 vendor/bin/phpstan analyse -l max -c tests/phpstan.tests.neon tests

test:
	vendor/bin/tester --info
	vendor/bin/tester tests
