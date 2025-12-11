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

var textarea = document.querySelector('textarea');
if (textarea) {
	tinymce.init({
		license_key: 'gpl',
		target: textarea,
		menubar: false,
		contextmenu: false,
		plugins: 'code image link table lists',
		toolbar: 'undo redo blocks bold italic link | image table numlist bullist outdent indent readmore module | code',
		content_css: '/static/kub.css',
		formats: {
			info: {block: 'aside', classes: 'infobox', wrapper: true},
			warning: {block: 'div', classes: 'alert', wrapper: true},
		},
		block_formats: 'Paragraph=p; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Info Box=info; Warning Box=warning',
		image_dimensions: false,
		convert_urls: false,
		entity_encoding: 'raw',
		directionality: textarea.dir,
		setup: (editor) => {
			editor.ui.registry.addButton('readmore', {
				icon: 'horizontal-rule',
				tooltip: 'read more',
				onAction: function() {
					return editor.execCommand('mceInsertContent', false, '<hr class="system-read-more" />');
				},
			});
			editor.ui.registry.addButton('module', {
				icon: 'embed-page',
				tooltip: 'module',
				onAction: function() {
					return editor.execCommand('mceInsertContent', false, '<div class="system-module"></div>');
				},
			});
			editor.on('change', function() {
				var form = editor.getElement().closest('form');
				if (form && !unsavedForms.includes(form)) {
					unsavedForms.push(form);
				}
			});
		},
	});
}

// show/hide the twingle id field based on layout selection
const layout = document.getElementById("layout");
const twid = document.getElementById("twid");
const twidInput = document.getElementById("twid_input");
function showOrHideTwingle() {
  const value = layout.value;
  if (value === "spenden") {
    // show element and make required
    twid.style.display = "";
    twidInput.setAttribute("required", "");
  } else {
    // hide element and remove requirement
    twid.style.display = "none";
    twidInput.removeAttribute("required");
    twidInput.value = "";
  }
}
// run on change
layout.addEventListener("change", showOrHideTwingle);
// run on initial load
document.addEventListener("DOMContentLoaded", showOrHideTwingle);