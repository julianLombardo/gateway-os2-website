.PHONY: serve build test seed migrate setup-email clean

serve:
	ruby tools/server.rb

build:
	ruby tools/build.rb

test:
	php tests/run_all.php

seed:
	ruby tools/seed.rb

migrate:
	ruby tools/migrate.rb

setup-email:
	ruby tools/setup_email.rb

clean:
	rm -f data/cache/manifest.json
	rm -f data/cache/github.json
	@echo "Cache cleared."
