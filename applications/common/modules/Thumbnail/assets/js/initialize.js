$('a[rel]').click(function (event) {
	event.preventDefault();
}).fancybox({
	openEffect	: 'fade',
	closeEffect	: 'none',
	prevEffect	: 'fade',
	nextEffect	: 'fade',
	helpers		: {
		title : {
			type : 'over'
		}
	}
});