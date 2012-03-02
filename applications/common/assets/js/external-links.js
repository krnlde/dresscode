$("a[href^='http://'],a[href^='https://']")
.click(function(event) {
	if (!event.isDefaultPrevented())
	{
		window.open(this.href);
		event.preventDefault();
	}
});