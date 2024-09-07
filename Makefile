static/kub.css: scss/kub.scss scss/_*.scss scss/components/_*.scss node_modules
	npx sass --no-source-map --no-error-css $< $@

node_modules:
	npm install mfbs
