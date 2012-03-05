<?php
namespace Mocovi\Controller;

class Input extends \Mocovi\Controller
{
	/**
	 * @property
	 * @var string
	 */
	protected $type = 'text';

	/**
	 * @property
	 * @var string
	 */
	protected $name;

	/**
	 * @property
	 * @var string
	 */
	protected $preset;

	/**
	 * @property
	 * @var string
	 */
	protected $value = '';

	/**
	 * @property
	 * @var string
	 */
	protected $label;

	/**
	 * @property
	 * @var string
	 */
	protected $placeholder;

	/**
	 * @property
	 * @var integer
	 */
	protected $minlength;

	/**
	 * @property
	 * @var integer
	 */
	protected $maxlength;

	/**
	 * @property
	 * @var boolean
	 */
	protected $required = false;

	/**
	 * @property
	 * @var boolean
	 */
	protected $readonly = false;

	/**
	 * @property
	 * @var boolean
	 */
	protected $disabled = false;

	/**
	 * @property
	 * @var boolean
	 */
	protected $highlight = false;

	/**
	 * @property
	 * @var string
	 */
	protected $pattern;


	/**
	 * @var array
	 */
	protected $presets = array
		( 'email'			=> array
			( 'type'		=> 'email'
			, 'pattern'		=> '/^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]{2,}\.(?:[a-zA-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$/'
			, 'required'	=> true
			)
		, 'url'				=> array
			( 'type'		=> 'url'
			, 'value'		=> 'http://'
			, 'pattern'		=> '/^https?:\/\/[a-zA-Z0-9.-]{2,}\.(?:[a-zA-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$/' // needs to be checked!
			, 'required'	=> true
			)
		);

	protected $dataSent = false;
	protected $exception;

	protected function before(array $params = array())
	{
		if (array_key_exists($this->name, $params))
		{
			$this->value = $params[$this->name];
			$this->dataSent = true;
		}
	}

	protected function get(array $params = array())
	{
		if ($this->isDataSent())
		{
			$this->process($params);
		}
	}

	protected function post(array $params = array())
	{
		if ($this->isDataSent())
		{
			$this->process($params);
		}
	}


	protected function process(array $params)
	{
		if ($this->preset && array_key_exists($this->preset, $this->presets))
		{
			foreach ($this->presets[$this->preset] as $property => $value)
			{
				if (strlen($this->sourceNode->getAttribute($property)) === 0)
				{
					$this->setProperty($property, $value);
				}
			}
		}
		if (!$this->id)
		{
			$this->id = uniqid();
		}
		if (!$this->isValid())
		{
			$this->highlight = true;
		}
	}

	public function isDataSent()
	{
		return $this->dataSent;
	}

	public function isValid()
	{
		if (!$this->isDataSent())
		{
			return true;
		}
		if (!is_null($this->exception))
		{
			return false;
		}
		try
		{
			$this->validate();
		}
		catch (\Mocovi\Exception\Input $e)
		{
			$this->exception = $e;
			return false;
		}
		return true;
	}

	public function validate()
	{
		if ($this->required && strlen($this->value) === 0)
		{
			throw new \Mocovi\Exception\Input\Required($this->name);
		}
		if ($this->maxlength && strlen($this->value) > $this->maxlength)
		{
			throw new \Mocovi\Exception\Input\WrongFormat($this->name);
		}
		if ($this->minlength && strlen($this->value) < $this->minlength)
		{
			throw new \Mocovi\Exception\Input\WrongFormat($this->name);
		}
		if ($this->pattern && !preg_match($this->pattern, $this->value))
		{
			throw new \Mocovi\Exception\Input\WrongFormat($this->name);
		}
		return true;
	}

	public function __get($var)
	{
		if (property_exists($this, $var))
		{
			return $this->$var;
		}
		return null;
	}
}