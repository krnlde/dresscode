<?php
namespace Dresscode\Controller;

abstract class Dataprovider extends \Dresscode\Controller implements \Iterator
{
	protected $data = array();

	public function __get($key)
	{
		return $this->data[$key];
	}

	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	public function all()
	{
		return $this->data;
	}

	public function add($value)
	{
		$this->data[] = $value;
	}

	/**
	 * USE WITH CAUTION!
	 *
	 */
	public function clear()
	{
		foreach ($this->data as &$data)
		{
			unset($data);
		}
	}

	public function current()
	{
		return current($this->data);
	}

	public function next()
	{
		return next($this->data);
	}

	public function key()
	{
		return key($this->data);
	}

	public function valid()
	{
		return key($this->data) !== null;
	}

	public function rewind()
	{
		return reset($this->data);
	}
}