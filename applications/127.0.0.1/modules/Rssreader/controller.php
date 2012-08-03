<?php
namespace Dresscode\Controller;

class Rssreader extends \Dresscode\Controller
{
	const MINIMUM = 1;
	const MAXIMUM = 100;
	/**
	 * @property
	 * @var string
	 * @pattern /^(https?|ftp):\/\/(?:[A-Z0-9-]+.)+[A-Z]{2,6}([\/?].+)?$/i
	 */
	protected $url;

	/**
	 * @property
	 * @var integer
	 */
	protected $maximum = 5;

	protected $items = array();

	public function get(array $params = array())
	{
		if ($this->maximum < self::MINIMUM)
		{
			$this->maximum = self::MINIMUM;
		}
		if ($this->maximum > self::MAXIMUM)
		{
			$this->maximum = self::MAXIMUM;
		}
		if ($this->url)
		{
			try {
				$doc = $this->fetchRss($this->url);
			}
			catch (\Exception $e) {
				return $this->error($e);
			}
			$this->items = &$doc->channel->item;

			$count = 0;
			foreach ($this->items as $item) // debug
			{
				if ($count++ >= $this->maximum)
				{
					break;
				}
				$clones = array();
				foreach ($this->children as $child)
				{
					$clone = clone $child;
					$this->parent->addChild($clone);
					foreach ($clone->find('Variable') as $var)
					{
						if (isset($item->{$var->getProperty('name')}))
						{
							$var->setText($item->{$var->getProperty('name')});
						}
					}
					$clone->launch('get', $params);
				}
			}
		}
		$this->deleteNode();
	}

	protected function fetchRss($url)
	{
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_HEADER, 0);
		// @TODO set timeout
		$data = curl_exec($handle);
		$error = curl_errno($handle);
		curl_close($handle);
		if ($error)
		{
			throw new \Exception('Connection Problems');
		}
		return new \SimpleXmlElement($data, LIBXML_NOCDATA);
	}

}