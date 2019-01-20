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
    docker exec -ti app_php-fpm-1 bash
