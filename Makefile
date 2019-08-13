install:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	php composer.phar install

update:
	php composer.phar update

build:
	$(MAKE) precommit
	$(MAKE) integration
	$(MAKE) coverage

precommit:
	$(MAKE) unit

unit:
	./vendor/bin/phpunit --no-coverage --testsuite=AbterPHP\\Unit --stop-on-error --stop-on-failure

integration:
	./vendor/bin/phpunit --no-coverage --testsuite=AbterPHP\\Integration

coverage:
	./vendor/bin/phpunit --testsuite=unit

flush:
	./apex abterphp:flushcache

.PHONY: build install update precommit unit integration coverage flush
