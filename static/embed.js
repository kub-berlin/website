var iframe = document.querySelector('iframe');

window.addEventListener('message', event => {
	if (event.data && event.data.type) {
		if (event.data.type === 'size') {
			iframe.scrolling = 'no';
			iframe.style.height = event.data.value + 'px';
		}
	}
});
