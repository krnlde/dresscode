<?php
namespace Dresscode\Controller;

use \Assetic\Cache\ExpiringCache;
use \Assetic\Cache\FilesystemCache;

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

	/**
	 * Connection Timeout in milliseconds.
	 *
	 * @property
	 * @var integer
	 */
	protected $timeout = 3000; // milliseconds

	/**
	 * Cache Lifetime in seconds.
	 *
	 * @property
	 * @var integer
	 */
	protected $cacheLifetime = 120; //seconds

	protected $items = array();

	/**
	 * @var \Assetic\Cache\CacheInterface
	 */
	private $cache;

	public function setup()
	{
		$this->cache = new ExpiringCache(new FilesystemCache($this->Application->cachePath()), $this->cacheLifetime);
	}

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
			try
			{
				$doc = $this->fetchRss($this->url);
			}
			catch (\Exception $e)
			{
				return $this->error($e);
			}
			$this->items = $doc->channel->item;

			$count = 0;
			foreach ($this->items as $item)
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
					foreach ($clone->find('Variablecollection') as $var)
					{
						$collection = $item->{$var->getProperty('name')};
						$list = array();
						foreach($collection as $current)
						{
							if (!strlen((string) $current))
							{
								continue;
							}
							$list[] = (string) $current;
						}
						foreach ($list as $element)
						{
							$collectionClone = clone $var;
							$var->parent->addChild($collectionClone);
							$var->getNode()->parentNode->appendChild($collectionClone->getNode()); // @TODO improve these steps?
							foreach ($collectionClone->find('Variable') as $collectionCloneVar)
							{
								if ($collectionCloneVar->getProperty('name') == '$value')
								{
									$collectionCloneVar->setText($element);
								}
							}
						}
						$var->deleteNode();
					}
					foreach ($clone->find('Variable') as $var)
					{
						if (isset($item->{$var->getProperty('name')})) // @TODO is this necessary?
						{
							$collection = $item->{$var->getProperty('name')};
							$list = array();
							foreach($collection as $current)
							{
								if (!strlen((string) $current))
								{
									continue;
								}
								$list[] = (string) $current;
							}
							$var->setText(implode(', ', $list));
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
		$data = null;
		if ($this->cache->has(md5($url)))
		{
			$data = $this->cache->get(md5($url));
		}
		else
		{
			$handle = curl_init($url);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_HEADER, 0);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT_MS, $this->timeout);
			$data = curl_exec($handle);
			$errno = curl_errno($handle);
			$error = curl_error($handle);
			$status = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			curl_close($handle);
			if ($errno)
			{
				throw new \Exception('CURL Error: '.$error);
			}
			if ($status != '200')
			{
				throw new \Exception('CURL Error, HTTP Status: '.$status);
			}
			$this->cache->set(md5($url), $data);
		}
		return new \SimpleXmlElement($data, LIBXML_NOCDATA);
	}

}