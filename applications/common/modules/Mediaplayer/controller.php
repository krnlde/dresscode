<?php
namespace Mocovi\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Mediaplayer extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $source;

	public function setup()
	{
		if (!$this->id)
		{
			$this->id = $this->generateId();
		}
		if (isset($this->source[0]) && $this->source[0] === '/')
		{
			$this->source = \Mocovi\Application::basePath().$this->source;
		}
		if (file_exists(($this->source[0] === '/' ? $_SERVER['DOCUMENT_ROOT'] : '').$this->source))
		{
			$this->Application->javascript(new FileAsset(__DIR__.'/assets/mediaplayer-5.9/jwplayer.js'));
			$this->Application->javascript(new StringAsset(
				'
				jwplayer("'.$this->id.'").setup({
					modes: [
						{ type: "html5" },
						{ type: "flash", src: "'.$this->getFrontendPath().'/assets/mediaplayer-5.9/player.swf" }
					]
				});
				'
			));
		}
		else
		{
			$this->error(new \Mocovi\Exception('Media source "'.$this->source.'" not found'));
		}
	}
}