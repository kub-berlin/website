document.addEventListener('submit', function(event) {
	if (event.target.dataset.js === 'confirm' && !confirm('Are you sure?')) {
		event.preventDefault();
	}
});

var unsavedForms = [];
document.addEventListener('change', function(event) {
	var form = event.target.closest('form');
	if (form && !unsavedForms.includes(form)) {
		unsavedForms.push(form);
	}
});
window.addEventListener('submit', function(event) {
	unsavedForms = unsavedForms.filter(f => f !== event.target);
});
window.addEventListener('beforeunload', function(event) {
	if (unsavedForms.length) {
		event.preventDefault();
		event.returnValue = '';
		return event.returnValue;
	}
});

var dir = document.querySelector('textarea').dir;
tinymce.init({
	selector: 'textarea',
	menubar: false,
	contextmenu: false,
	plugins: 'code image link table lists',
	toolbar: 'undo redo | formatselect | bold italic link | image table numlist bullist infobox | outdent indent | readmore | code',
	content_css: '/xi/static/kub-' + dir + '.css',
	formats: {
		info: {block: 'aside', classes: 'infobox', wrapper: true},
		warning: {block: 'div', classes: 'alert', wrapper: true},
	},
	block_formats: 'Paragraph=p; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Info Box=info; Warning Box=warning',
	convert_urls: false,
	entity_encoding: 'raw',
	directionality: dir,
	setup: (editor) => {
		editor.ui.registry.addButton('readmore', {
			icon: 'horizontal-rule',
			tooltip: 'read more',
			onAction: function () {
				return editor.execCommand('mceInsertContent', false, '<hr class="system-read-more" />');
			}
		});
		editor.on('input', function() {
			var form = editor.getElement().closest('form');
			if (form && !unsavedForms.includes(form)) {
				unsavedForms.push(form);
			}
		});
	}
});
