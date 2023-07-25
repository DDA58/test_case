.DEFAULT_GOAL := help
.PHONY: help
help:
	@echo "Hello"
	@echo "Did you make command 'init' and 'database_init'?"

init: run_copy_env run_down_project run_build_up_d_project run_composeri

phpfix: run_phpfix
composeri: run_composeri
database_init: run_database_init
top: run_top_project

run_phpfix:
	docker-compose exec app ./vendor/bin/phpcbf

run_composeri:
	docker-compose exec app composer install

run_database_init:
	docker-compose exec app php ./src/main.php init:database

run_copy_env:
	cp .env.example .env

run_down_project:
	docker-compose down

run_build_up_d_project:
	docker-compose up --build -d

run_top_project:
	docker-compose top app