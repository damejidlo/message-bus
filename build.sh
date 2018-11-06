#!/usr/bin/env bash

./vendor/bin/phpcs --standard=vendor/damejidlo/coding-standard/DameJidloCodingStandard/ruleset.xml --extensions=php,phpt --ignore=tests/tmp/* --encoding=utf-8  src/ tests/
./vendor/bin/phpstan analyse -l 4 src tests
./vendor/bin/tester tests
