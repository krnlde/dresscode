<?php
namespace Mocovi\Controller;

\Mocovi\Module::requireController('Dataprovider');

class Sessionstore extends \Mocovi\Controller\Dataprovider
{
	/**
	 * @property
	 * @hideIfEmpty
	 * @var string
	 */
	protected $bin;

	public function setup()
	{
		if ($this->bin)
		{
			if (!isset($_SESSION[$this->bin]))
			{
				$_SESSION[$this->bin] = array();
			}
			$this->data = &$_SESSION[$this->bin];
		}
		else
		{
			$this->data = &$_SESSION;
		}
	}
}