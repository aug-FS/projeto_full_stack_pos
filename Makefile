COMPOSE=docker compose
APP=escola_app_php

PROJECT_NAME=projeto_full_stack_pos
f ?= $(PROJECT_NAME)

rebuild:
	$(COMPOSE) build --no-cache
	$(COMPOSE) up -d

restart:
	$(COMPOSE) restart

p:
	$(COMPOSE) exec $(APP) bash

perm:
	sudo chmod -R 777 ../$(f)/

admin-reset:
	$(COMPOSE) exec $(APP) php artisan migrate --force
	$(COMPOSE) exec $(APP) php artisan tinker --execute="\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0'); \App\Models\User::truncate(); \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1'); \App\Models\User::create(['name' => 'Admin Escola', 'email' => 'admin@escola.com', 'email_verified_at' => now(), 'password' => bcrypt('admin1234')]);"

deploy-dev:
	./deploy-dev.sh
