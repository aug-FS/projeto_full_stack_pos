COMPOSE=docker compose
APP=escola_app_php

rebuild:
	$(COMPOSE) build --no-cache
	$(COMPOSE) up -d

restart:
	$(COMPOSE) restart

p:
	$(COMPOSE) exec $(APP) bash

perm:
	sudo chown -R $(USER):$(USER) .
	chmod -R 775 .
	chmod -R 775 storage bootstrap/cache
	docker compose exec escola_app_php chmod 1777 /tmp
	docker compose exec escola_app_php chown -R www-data:www-data storage bootstrap/cache
	docker compose exec escola_app_php chmod -R 775 storage bootstrap/cache

admin-reset:
	$(COMPOSE) exec $(APP) php artisan migrate --force
	$(COMPOSE) exec $(APP) php artisan tinker --execute="\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0'); \App\Models\User::truncate(); \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1'); \App\Models\User::create(['name' => 'Admin Escola', 'email' => 'admin@escola.com', 'email_verified_at' => now(), 'password' => bcrypt('admin1234')]);"

deploy-dev:
	./deploy-dev.sh
