static/kub-rtl.css: static/kub-ltr.css
	cp $< $@
	sed -i 's/left/RIGHT/g;s/right/left/g;s/RIGHT/right/g' $@

static/kub-ltr.css: scss/kub.scss scss/_*.scss scss/components/_*.scss node_modules
	npx sass --no-error-css $< $@

node_modules:
	npm install mfbs sass-planifolia
