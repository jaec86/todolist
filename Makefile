.PHONY: up down stop test refresh

up:
	docker-compose up -d

down:
	docker-compose down

stop:
	docker-compose stop

test:
	docker-compose exec app vendor/bin/phpunit

refresh:
	docker-compose exec app php artisan migrate:refresh

seed:
	docker-compose exec app php artisan migrate:refresh --seed