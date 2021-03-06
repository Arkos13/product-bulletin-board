docker-up:
	docker-compose up -d
docker-down:
	docker-compose down
docker-build:
	docker-compose up --build -d
test:
	docker-compose exec php-cli vendor/bin/phpunit
assets-install:
	docker-compose exec node yarn install
assets-rebuild:
	docker-compose exec node npm rebuild node-sass --force
assets-dev:
	docker-compose exec node yarn run dev
assets-watch:
	docker-compose exec node yarn run watch
php_container:
    docker exec -ti app_php-fpm_1 bash
queue:
	docker-compose exec php-cli php artisan queue:work
horizon:
	docker-compose exec php-cli php artisan horizon
horizon-pause:
	docker-compose exec php-cli php artisan horizon:pause
horizon-continue:
	docker-compose exec php-cli php artisan horizon:continue
horizon-terminate:
	docker-compose exec php-cli php artisan horizon:terminate
