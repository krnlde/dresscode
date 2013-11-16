<?php
namespace Dresscode\Controller;

class Headline extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var integer
	 */
	protected $priority = 1;

	protected static $used_ids = array();

	public function get(array $params = array())
	{
		parent::get($params);

		if ($this->priority < 1)
		{
			$this->priority = 1;
		}
		elseif ($this->priority > 6)
		{
			$this->priority = 6;
		}
		if (!$this->id) {
			$this->id = urlencode(trim(substr(preg_replace('/[^a-zA-Z0-9 ]/', '', trim($this->node->nodeValue)), 0, 32)));
		}
		$tmp	= $this->id;
		$i		= 2;
		while (in_array($tmp, self::$used_ids))
		{
			$tmp = $this->id.'_'.$i++;
		}
		$this->setProperty('id', self::$used_ids[] = $tmp);
	}
}