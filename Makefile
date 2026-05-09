SAIL=./vendor/bin/sail

rebuild:
	$(SAIL) build --no-cache
	$(SAIL) up -d

restart:
	$(SAIL) restart

p:
	$(SAIL) shell

perm:
	sudo chown -R $(USER):$(USER) .
	chmod -R 775 .
	chmod -R 775 storage bootstrap/cache