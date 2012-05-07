(function($) {
	var click = function (e) {
		$fancy.click();
	};
	var $fancy			= $('input[type="file"].fancy');
	var $proxyInput		= $('<input type="text" class="proxyInput" readonly="readonly" name="fileInputProxy"/>')/*.click(click)*/;
	var $proxyButton	= $('<button type="button" class="proxyButton btn" name="fileButtonProxy">'+$fancy.attr('caption')+'</button>').click(click);
	var $proxy			= $('<div class="fileProxy input-append"/>').append($proxyInput).append($proxyButton);

	console.log($fancy);

	$fancy.css({
		width:		'200px',
		padding:	'0',
		margin:		'0 -200px 0 0'
	});
	// $proxyInput.bind('dragenter dragover', function (e) {
	// 	e.stopPropagation();
	// 	e.preventDefault();
	// 	$(this).closest('.control-group').addClass('success');
	// }).bind('dragleave dragend', function (e) {
	// 	e.stopPropagation();
	// 	e.preventDefault();
	// 	$(this).closest('.control-group').removeClass('success');
	// }).bind('drop', function (e) {
	// 	e.stopPropagation();
	// 	e.preventDefault();
	// 	$(this).closest('.control-group').removeClass('success');
	// 	var file = e.originalEvent.dataTransfer.files[0];
	// 	console.log(file);
	// 	alert('todo');
	// 	$fancy.val(file.name); // @todo
	// });

	$fancy.after($proxy).change(function (e) {
		if ($fancy.val())
		{
			$proxyInput.val(/[^\\\/]+$/.exec($fancy.val()));
		}
	});
	$fancy.attr('tabindex', '-1');
	if ($fancy.prop('required'))
	{
		$proxyInput.prop('required', true);
		$fancy.prop('required', false)
	}
})(jQuery);