# KuB Website

This is the custom PHP code that powers <https://kub-berlin.org/>.

# Features

-	**translations**: The website is translated into 6 languages, with german as
	a fallback if a translation does not exist. The language is determined via a
	path prefix. The rest of the path is the same for all languages.
-	**flexible editing**: [TinyMCE](https://www.tiny.cloud/docs/) is used as a
	WYSIWYG editor. It is configured to encourage semantic editing and supports
	custom elements like info or warning boxes. It also allows to enter raw HTML,
	so even things like iframes and forms can be created. JavaScript and CSS are
	prohibited by a Content Security Policy though. The editor uses the actual
	CSS to show a realistic preview.
-	**full page cache**: The completely rendered pages are stored in a way that
	apache can directly return the cached file on subsequent requests. So in
	those cases, PHP is not involved at all. The cache is cleared automatically
	on edits.
-	**modules**: Some parts of the website appear on every page but should still
	be editable by regular users. These are modelled as top-level pages and
	therefore have just the same features (translation and flexible editing).

# Installation

-	build CSS by running `make`
-	copy the files from this repo to the server
-	get TinyMCE (e.g. `npm i tinymce`) and copy it to the admin folder
-	add a `.htaccess` and `.htpasswd` to the admin folder for authentication
-	adapt `config.php`
-	create a root page via SQL
