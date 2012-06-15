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

		if (!$this->id) // Set an ID with maxLength 32, if none is set.
		{
			$candidate	= urlencode(trim(substr(trim($this->node->nodeValue), 0, 32)));
			$tmp		= $candidate;
			$i			= 2;
			while (in_array($tmp, self::$used_ids))
			{
				$tmp = $candidate.'_'.$i++;
			}
			self::$used_ids[] = $this->id = $tmp;
		}
	}
}