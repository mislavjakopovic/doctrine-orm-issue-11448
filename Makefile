up:
	docker-compose up --build

upd:
	docker-compose up --build -d

stop:
	docker-compose stop

down:
	docker-compose down --volumes

v2.19.4:
	docker-compose exec php-testcase rm -rf "vendor"
	docker-compose exec php-testcase rm -rf "composer.json"
	docker-compose exec php-testcase rm -rf "composer.lock"
	docker-compose exec php-testcase cp ".composer/doctrine-orm-v2194.json" "composer.json"
	docker-compose exec php-testcase cp ".composer/doctrine-orm-v2194.lock" "composer.lock"
	docker-compose exec php-testcase composer install

v2.19.5:
	docker-compose exec php-testcase rm -rf "vendor"
	docker-compose exec php-testcase rm -rf "composer.json"
	docker-compose exec php-testcase rm -rf "composer.lock"
	docker-compose exec php-testcase cp ".composer/doctrine-orm-v2195.json" "composer.json"
	docker-compose exec php-testcase cp ".composer/doctrine-orm-v2195.lock" "composer.lock"
	docker-compose exec php-testcase composer install

case:
	docker-compose exec php-testcase bin/console doctrine:fixtures:load --no-interaction
	docker-compose exec php-testcase bin/console app:test:case

case-v2.19.4: v2.19.4 case
case-v2.19.5: v2.19.5 case
