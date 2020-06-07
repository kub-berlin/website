document.addEventListener('submit', function(event) {
	if (event.target.dataset.js === 'confirm' && !confirm('Are you sure?')) {
		event.preventDefault();
	}
});

tinymce.init({
	selector: 'textarea',
	menubar: false,
	contextmenu: false,
	plugins: 'code image link table lists',
	toolbar: 'undo redo | formatselect | bold italic link | image table numlist bullist infobox | outdent indent | code',
	content_css: '/templates/kub/css/kub-ltr.css',
	formats: {
		info: {block: 'div', classes: 'moduletable infobox', wrapper: true},
		warning: {block: 'div', classes: 'alert', wrapper: true},
	},
	block_formats: 'Paragraph=p; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Info Box=info; Warning Box=warning',
	image_dimensions: false,
	convert_urls: false,
	entity_encoding: 'raw',
	directionality: document.querySelector('textarea').dir,
});
