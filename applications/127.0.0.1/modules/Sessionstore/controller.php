<?php
namespace Mocovi\Controller;

class Sessionstore extends \Mocovi\Controller
{
	/**
	 * @property
	 * @hideIfEmpty
	 * @var string
	 */
	protected $bin;

	/**
	 * @var pointer Points to the $_SESSION array
	 */
	protected $data;

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
		$self = $this;
		$this->find('Form')->on('success', function ($event) use ($self) { // @todo you can use $this directly in PHP 5.4
			$event->preventDefault();
			$self->save($event->data);
		});
		$this->on('data', function ($event) { // @debug
			echo 'Sessionstore: ';
			print_r($event->data);
		});
	}

	/**
	 * @triggers data
	 */
	public function get(array $params = array())
	{
		if (count($this->data))
		{
			$this->trigger('data', null, $this->data);
		}
	}

	public function save(array $data)
	{
		foreach ($data as $key => $value)
		{
			$this->data[$key] = $value;
		}
	}

	public function remove($key)
	{
		if(isset ($this->data[$key]))
		{
			unset($this->data[$key]);
		}
	}

	public function clear()
	{
		$this->data = array();
	}

	public function getData($key = null)
	{
		if (!is_null($key))
		{
			if (isset ($this->data[$key]))
			{
				return $this->data[$key];
			}
			return null;
		}
		return $this->data;
	}
}