test:
	./vendor/bin/phpunit

coverage:
	./vendor/bin/phpunit --coverage-html ./coverage

code-style:
	vendor/bin/php-cs-fixer fix src --rules=@PSR12

