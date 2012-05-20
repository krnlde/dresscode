(function($) {
	$.fn.fancyUpload = function ()
	{
		var $fancy 			= this
		,	$proxyInput		= $('<input type="text" class="proxyInput" readonly="readonly" name="fileInputProxy"/>')/*.bind('click.fancyUpload', clickHandler)*/
		,	$proxyButton	= $('<button type="button" class="proxyButton btn" name="fileButtonProxy">'+$fancy.attr('caption')+'</button>').bind('click.fancyUpload', clickHandler)
		,	$proxy			= $('<div class="fileProxy input-append"/>').append($proxyInput).append($proxyButton)
		;

		function clickHandler()
		{
			$fancy.click();
		}

		$fancy
		.after($proxy)
		.attr('tabindex', '-1')
		.css({
			marginRight: '-'+$fancy.outerWidth(true)+'.px',
			visibility: 'hidden'
		})
		.change(function (e) {
			if ($fancy.val())
			{
				$proxyInput.val(/[^\\\/]+$/.exec($fancy.val()));
			}
		});
			// @todo drag and drop
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
			// 	$fancy.val(file.name); // @todo
			// });

		if ($fancy.prop('required'))
		{
			$proxyInput.prop('required', true);
			$fancy.prop('required', false)
		}

		return this;
	};

	// Autoloader for .fancy classes
	$(function () {
		$('[type="file"].fancy').fancyUpload();
	});
})(jQuery);