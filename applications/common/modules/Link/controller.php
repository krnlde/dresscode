<?php
namespace Dresscode\Controller;

use \Assetic\Asset\StringAsset;

class Link extends \Dresscode\Controller
{

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @property
	 * @var string
	 */
	protected $to;

	/**
	 * @property
	 * @var string
	 */
	protected $description;

	public function post(array $params = array())
	{
		$self = $this;
		$this->closest('Root')->on('ready', function () use ($self, $params) {
			if ($self->getXPath() === $params['xpath'])
			{
				die(print_r($params, true)); // @TODO
			}
		});
	}

	public function get(array $params = array())
	{
		if ($this->to)
		{
			if (substr($this->to, 0, 1) === '/')
			{
				$this->to = dirname($_SERVER['SCRIPT_NAME']).$this->to;
			}
			if ($this->node->childNodes->length === 0)
			{
				$this->node->appendChild($this->dom->createTextNode($this->to));
			}
			if (substr($this->to, 0, 4) !== 'http')
			{
				$this->url = implode
					( '/'
					, array_map
						( function($element)
							{
								return urlencode($element);
							}
						, explode
							( '/'
							, $this->to
							)
						)
					);
				$this->url = str_replace('%40', '@', $this->url); // recover mail declaration (from urlencode)
				$this->url = str_replace('%23', '#', $this->url); // recover site anchor declaration (from urlencode)
				$this->url = preg_replace('/^([a-z]+)\%3A\/\//', '$1://', $this->url); // recover scheme declaration (from urlencode)
				if (preg_match('/^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2,10})$/', $this->url))
				{
					$this->url = 'mailto:'.$this->url;
				}
			}
			else
			{
				$this->url = $this->to;
			}
			$this->node->setAttribute('url', $this->url);
		}
		if (!strlen($this->node->nodeValue))
		{
			$this->node->appendChild($this->dom->createTextNode($this->url));
		}


		// if (!$this->id)
		// {
		// 	$this->id = $this->generateId();
		// }
		// $self			= $this;
		// $Application	= $this->Application;
		// $Root			= $this->closest('Root');
		// $canonical		= $Root->getProperty('canonical');
		// $Root->on('ready', function ($event) use ($self, $Application, $canonical) { // @todo "use ($self)"" is obsolote in PHP > 5.4
			// $Application->javascript
			// (	new StringAsset
			// 	(
			// 		'
			// 		$("#'.$self->getProperty('id').'").click(function (event) {
			// 			event.preventDefault();
			// 			var $this	= $(this);
			// 			var $id		= "'.$self->getProperty('id').'";
			// 			var $xpath	= "'.$self->getXPath().'";

			// 			$.ajax("'.$canonical.'", {
			// 				type: "post",
			// 				data: {
			// 					xpath:	$xpath,
			// 					id:		$id,
			// 					event: {
			// 						type:		event.type,
			// 						offsetX:	event.offsetX,
			// 						offsetY:	event.offsetY,
			// 						pageX:		event.pageX,
			// 						pageY:		event.PageY,
			// 						screenX:	event.screenX,
			// 						screenY:	event.screenY,
			// 						which:		event.which
			// 					}
			// 				}
			// 			}).done(function (msg) {
			// 				console.log(msg);
			// 			});
			// 		});
			// 		'
			// 	)
			// );
		// });

	}
}