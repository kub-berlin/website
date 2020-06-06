document.addEventListener('submit', function(event) {
	if (event.target.dataset.js === 'confirm' && !confirm('Are you sure?')) {
		event.preventDefault();
	}
});
