$("a[href^='http://'],a[href^='https://']")
.click(function(event) {
	window.open(this.href);
	event.preventDefault();
});