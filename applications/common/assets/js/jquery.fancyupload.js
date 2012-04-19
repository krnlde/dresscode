(function($) {
	var click = function (e) {
		$fancy.click();
	};
	var $fancy			= $('input[type="file"].fancy');
	var $proxyInput		= $('<input type="text" class="proxyInput" readonly="readonly"/>').click(click);
	var $proxyButton	= $('<button type="button" class="proxyButton btn">Choose File...</button>').click(click);
	var $proxy			= $('<div class="fileProxy input-append"/>').append($proxyInput).append($proxyButton);
	$fancy.after($proxy).change(function (e) {
		if ($fancy.val())
		{
			$proxyInput.val(/[^\\\/]+$/.exec($fancy.val()));
		}
	});
})(jQuery);