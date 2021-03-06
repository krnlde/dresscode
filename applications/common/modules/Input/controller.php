<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;

class Input extends \Dresscode\Controller
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
	 * @property
	 * @var string
	 */
	protected $caption;

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
	protected $maxlength = 255;

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
			, 'pattern'		=> '^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2,10})$'
			, 'required'	=> true
			)
		, 'url'				=> array
			( 'type'		=> 'url'
			, 'value'		=> 'http://'
			, 'pattern'		=> '^https?:\/\/[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2,10})$'
			)
		, 'zip'				=> array
			( 'type'		=> 'zip'
			, 'minlength'	=> 5
			, 'maxlength'	=> 5
			, 'pattern'		=> '^[0-9]{5}$' // @todo needs to be checked!
			)
		, 'password'		=> array
			( 'type'		=> 'password'
			, 'required'	=> true
			)
		);

	/**
	 * @var boolean
	 */
	protected $dataSent = false;

	/**
	 * @var \Dresscode\Exception\Input
	 */
	protected $exception;

	public function setup()
	{
		if (!$this->id && strlen($this->label) > 0)
		{
			$this->id = $this->generateId();
		}

		$Input	= \Dresscode\Input::getInstance();
		if (!($form = $this->closest('Form')))
		{
			$this->error(new \Dresscode\Exception(get_class($this).' must be in a Form.'));
			return;
		}
		$method	= $form->getProperty('method'); // @todo is this the best solution?
		$params	= $Input->$method;
		if (array_key_exists($this->name, $params))
		{
			$this->setProperty('value', $params[$this->name]);
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

		if ($this->type === 'file')
		{
			$this->closest('Form')->setProperty('multipart', true);
		}

		if ($this->type === 'file')
		{
			$this->class .= ($this->class ? ' ' : '').'fancy';
			if (!$this->caption)
			{
				$this->caption = \Dresscode\Translator::translate('Input.ChooseFile')->nodeValue;
			}
			$this->Application->javascript(new FileAsset('applications/common/assets/js/jquery.fancyupload.js'));
		}
	}

	public function get(array $params = array())
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
		catch (\Dresscode\Exception\Input $e)
		{
			if ($node = \Dresscode\Translator::translate(get_class($e)))
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
			throw new \Dresscode\Exception\Input\Required($this->name);
		}
		if ($this->maxlength && strlen($this->value) > $this->maxlength)
		{
			throw new \Dresscode\Exception\Input\WrongFormat\TooLong($this->name);
		}
		if ($this->minlength && strlen($this->value) < $this->minlength)
		{
			throw new \Dresscode\Exception\Input\WrongFormat\TooShort($this->name);
		}
		if ($this->pattern && !preg_match('/'.$this->pattern.'/', $this->value))
		{
			throw new \Dresscode\Exception\Input\WrongFormat($this->name);
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