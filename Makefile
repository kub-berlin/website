.PHONY: server
server: static
	php -S localhost:8000 route.php

.PHONY: static
static: static/kub.css admin/tinymce

static/kub.css: scss/kub.scss scss/_*.scss scss/components/_*.scss node_modules
	npx sass --no-source-map --no-error-css $< $@

admin/tinymce: node_modules
	rm -rf admin/tinymce
	cp -r node_modules/tinymce admin/

node_modules:
	npm install mfbs tinymce

.PHONY: clean
clean:
	rm -rf static/kub.css admin/tinymce node_modules
