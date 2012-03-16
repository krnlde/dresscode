<?php
namespace Mocovi\Controller;

class Input extends \Mocovi\Controller
{
	/**
	 * Type of the input.
	 *
	 * @property
	 * @var string
	 */
	protected $type = 'text';

	/**
	 * Name of the input.
	 *
	 * @property
	 * @var string
	 */
	protected $name;

	/**
	 * Applies a preset defined in the {@see $presets} array.
	 *
	 * @property
	 * @var string
	 */
	protected $preset;

	/**
	 * Input value.
	 *
	 * @property
	 * @var string
	 */
	protected $value = '';

	/**
	 * Optional label for the input.
	 *
	 * Won't be displayed when empty.
	 *
	 * @property
	 * @var string
	 */
	protected $label;

	/**
	 * Placeholder text.
	 *
	 * @property
	 * @var string
	 */
	protected $placeholder;

	/**
	 * Title text used for error messages.
	 *
	 * @property
	 * @var string
	 */
	protected $title;

	/**
	 * Minimum length of the value (inclusive).
	 *
	 * @property
	 * @var integer
	 */
	protected $minlength;

	/**
	 * Maximum length of the value (inclusive).
	 *
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
	 * Regular expression the {@see $value} must match.
	 *
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
			, 'pattern'		=> '^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$'
			, 'required'	=> true
			)
		, 'url'				=> array
			( 'type'		=> 'url'
			, 'value'		=> 'http://'
			, 'pattern'		=> '^https?:\/\/[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$' // needs to be checked!
			)
		, 'zip'				=> array
			( 'type'		=> 'zip'
			, 'minlength'	=> 5
			, 'maxlength'	=> 5
			, 'pattern'		=> '^[0-9]{5}$' // needs to be checked!
			)
		);

	/**
	 * @var boolean
	 */
	protected $dataSent = false;

	/**
	 * @var \Mocovi\Exception\Input
	 */
	protected $exception;

	protected function before(array $params = array())
	{
		if (!$this->id && strlen($this->label) > 0)
		{
			$this->id = $this->generateId();
		}
		if (array_key_exists($this->name, $params))
		{
			$this->setProperty('value', $params[$this->name]); //@todo sets every input even if the form is POST or GET and you provide the opposite.
			$this->dataSent = true;
		}
		if ($this->preset && array_key_exists($this->preset, $this->presets))
		{
			foreach ($this->presets[$this->preset] as $property => $value)
			{
				if (!$this->sourceNode->hasAttribute($property))
				{
					$this->setProperty($property, $value);
				}
			}
		}
	}

	protected function get(array $params = array())
	{
		if ($this->isDataSent())
		{
			$this->process();
		}
	}

	protected function post(array $params = array())
	{
		if ($this->isDataSent())
		{
			$this->process();
		}
	}

	/**
	 * Processes the input validation of all child inputs.
	 *
	 * @return void
	 */
	protected function process()
	{
		if (!$this->isValid())
		{
			$this->setProperty('highlight', true);
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
			if ($node = \Mocovi\Translator::translate(get_class($e)))
			{
				$this->setProperty('title', $node->nodeValue); // @todo maybe improve this?
			}
			else
			{
				$this->setProperty('title', get_class($e));
			}
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
			throw new \Mocovi\Exception\Input\WrongFormat\TooLong($this->name);
		}
		if ($this->minlength && strlen($this->value) < $this->minlength)
		{
			throw new \Mocovi\Exception\Input\WrongFormat\TooShort($this->name);
		}
		if ($this->pattern && !preg_match('/'.$this->pattern.'/', $this->value))
		{
			throw new \Mocovi\Exception\Input\WrongFormat($this->name);
		}
		return true;
	}

	/**
	 * This magic method transforms all properties to a public readonly.
	 *
	 * @return mixed
	 * @param string $var
	 */
	public function __get($var)
	{
		if (property_exists($this, $var))
		{
			return $this->$var;
		}
		return null;
	}
}