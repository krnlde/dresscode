$(function() {
	var $slider = $('.slider');
	$slider.find('.slide:first-child').css({display: 'block'});
	var revolver = $slider.revolver(
		{ autoPlay:			true
		, rotationSpeed:	2000
		, transition:
			{ direction:	'up'
			, easing:		'swing'
			, speed:		1000
			, type:			'slide'
			}
		}
	).data('revolver');
});