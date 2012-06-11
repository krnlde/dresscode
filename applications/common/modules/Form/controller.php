<?php
namespace Dresscode\Controller;

use \Assetic\Asset\FileAsset;
use \Assetic\Asset\StringAsset;

class Form extends \Dresscode\Controller
{
	/**
	 * Defines the destination URL which should be opened after success.
	 *
	 * @property
	 * @var string
	 */
	protected $jumpTo = '.';

	/**
	 * Defines the URL which handles the form data.
	 *
	 * @property
	 * @var string
	 */
	protected $action = '.';


	/**
	 * HTTP method.
	 *
	 * @property
	 * @var string
	 */
	protected $method = 'post';

	/**
	 * @property
	 * @var boolean
	 */
	protected $multipart = false;

	/**
	 * Defines whether the form should be transmitted via ajax.
	 *
	 * @property
	 * @var boolean
	 */
	protected $ajax = false;

	/**
	 * Javascript which will be called clientside on submit.
	 * @property
	 * @hidden
	 * @var string
	 */
	protected $onsubmit;

	/**
	 * Defines the context object.
	 *
	 * @var \Dresscode\Controller
	 */
	public $context;

	/**
	 * @var array
	 */
	protected $methods = array
	( 'get'
	, 'post'
	);

	/**
	 * @var \Dresscode\Input
	 */
	protected $Input;

	/**
	 * @var array of \Dresscode\Controller\Input
	 */
	protected $inputs;

	/**
	 * Initialize script (Javascript)
	 *
	 * @var string
	 */
	protected static $initialize;

	/**
	 * @var \Dresscode\Pool
	 */
	protected static $Pool;

	public function setup()
	{

		if (strlen($this->jumpTo) > 0)
		{
			if ($this->jumpTo[0] === '/')
			{
				$this->jumpTo = \Dresscode\Application::basePath().$this->jumpTo;
			}
			elseif($this->jumpTo[0] === '.')
			{
				$this->jumpTo = substr_replace($this->jumpTo, \Dresscode\Application::basePath().$this->Application->Request->path, 0, 1);
			}
		}

		if (strlen($this->action) > 0)
		{
			if ($this->action[0] === '/')
			{
				$this->action = \Dresscode\Application::basePath().$this->action;
			}
			elseif($this->action[0] === '.')
			{
				$this->action = substr_replace($this->action, \Dresscode\Application::basePath().$this->Application->Request->path, 0, 1);
			}
		}

		// $this->Application->javascript(new FileAsset('applications/common/assets/js/jquery-validation/jquery.validate.js'));
		// if (is_null(self::$Pool))
		// {
		// 	self::$Pool = new \Dresscode\Pool('js');
		// 	self::$Pool->add(new \DirectoryIterator('applications/common/assets/js/jquery-validation/localization/'));
		// }
		// if ($localization = self::$Pool->find('messages_'.\Dresscode\Translator::getLanguage()))
		// {
		// 	$this->Application->javascript(new FileAsset($localization));
		// }
		// $this->Application->javascript(new StringAsset('/* This comes from '.__FILE__.' */
		// 	$("form").validate({
		// 		debug: true,
		// 		errorElement: "span",
		// 		errorClass: "error",
		// 		validClass: "success",
		// 		errorPlacement: function(error, element) {
		// 			error.addClass("help-inline");
		// 			var proxy = element.closest(".fileProxy");
		// 			if (proxy.length) { // if is fileproxy (fancy file upload)
		// 				proxy.append(error);
		// 			} else {
		// 				error.insertAfter(element);
		// 			}
		// 		},
		// 		unhighlight: function(element, errorClass, validClass) {
		// 			if (element.type === "radio") {
		// 				this.findByName(element.name).closest(".control-group").removeClass(errorClass).addClass(validClass);
		// 			} else {
		// 				$(element).closest(".control-group").removeClass(errorClass).addClass(validClass);
		// 			}
		// 		},
		// 		highlight: function(element, errorClass, validClass) {
		// 			if (element.type === "radio") {
		// 				this.findByName(element.name).closest(".control-group").removeClass(validClass).addClass(errorClass);
		// 			} else {
		// 				$(element).closest(".control-group").removeClass(validClass).addClass(errorClass);
		// 			}
		// 		}
		// 	});')
		// );

		if ($this->ajax)
		{
			$this->Application->javascript(new FileAsset('applications/common/assets/js/spin.min.js'));
			$this->Application->javascript(new FileAsset('applications/common/assets/js/jquery.spin.js'));

			if (!$this->onsubmit)
			{
				$this->onsubmit = '
					event.preventDefault();
					var $inputs = $this.find(":input");
					var $data = $this.serializeObject(); // attention! custom method
					$inputs.prop("disabled", true);
					$spinner = $context.spin("medium"); // make the size dynamic
					$.ajax("'.$this->action.'", {
						cache: false,
						context: $context,
						type: "'.strtoupper($this->method).'",
						data: $data,
						headers: {
							x_xpath: "'.$this->context->getXpath().'/*[position() = last()]" // returns the newest element immediately
						}
					})
					.done(function(data) {
						// @todo Maybe some custom code here from $this->done?
						$context.append($(data));
						$this[0].reset();
					}).error(function(jqXHR, textStatus, errorThrown) {
						// @todo Maybe some custom code here from $this->error?
						console.log(jqXHR);
						console.log(textStatus);
						console.log(errorThrown);
					}).complete(function (jqXHR, textStatus) {
						// @todo Maybe some custom code here from $this->complete?
						$inputs.prop("disabled", false);
						$spinner.spin(false);
					});
				';
			}
		}

		$this->Input	= \Dresscode\Input::getInstance();
		$this->inputs	= $this->find('Input');
		if (!in_array($this->method, $this->methods))
		{
			$this->method = $this->methods[count($this->methods) - 1];
		}

		// @todo implement routine to check if a submit button exists.
	}

	public function get(array $params = array())
	{
		if ($this->onsubmit)
		{
			if (!$this->id)
			{
				$this->id = $this->generateId();
			}

			if (is_null(self::$initialize))
			{
				self::$initialize = new StringAsset
				(
					'
					jQuery.fn.serializeObject = function()
					{
						var arrayData, objectData;
						arrayData	= this.serializeArray();
						objectData	= {};
						$.each(arrayData, function() {
							var value;
							if (this.value != null)
							{
								value = this.value;
							}
							else
							{
								value = "";
							}
							if (objectData[this.name] != null)
							{
								if (!objectData[this.name].push)
								{
									objectData[this.name] = [objectData[this.name]];
								}
								objectData[this.name].push(value);
							}
							else
							{
								objectData[this.name] = value;
							}
						});
						return objectData;
					};

					var $basepath	= "'.\Dresscode\Application::basePath().'";
					var $name		= "'.$this->getName().'";
					'.($this->context ? 'var $context	= $("#'.$this->context->getProperty('id').'");' : '').'
					'
				);
			}
			$self			= $this;
			$Application	= $this->Application;
			$onsubmit		= $this->onsubmit;
			$Application->javascript(self::$initialize);
			$this->closest('Root')->on('ready', function ($event) use ($self, $Application, $onsubmit) { // @todo "use ($self)"" is obsolote in PHP > 5.4
				$Application->javascript
				(	new StringAsset
					(
						'
						$("#'.$self->getProperty('id').'").submit(function (event) {
							var $this	= $(this);
							var $id		= "'.$self->getProperty('id').'";
							var $xpath	= "'.$self->getXPath().'";

							'.$onsubmit.'
						});
						'
					)
				);
			});
		}

		if ($this->method === 'get' && count($this->Input->get) > 0)
		{
			try
			{
				$this->process();
			}
			catch (\Dresscode\Exception\Input $e)
			{} // This is important! It prevents the Exception from bubbling up.
		}
	}

	protected function post(array $params = array())
	{
		if ($this->method === 'post' && count($this->Input->post) > 0)
		{
			$this->process();
		}
	}

	/**
	 * Processes the input values and fires the "success" event on success.
	 *
	 * If execute $event->preventDefault(); on the success event, the jumpTo
	 * header redirect will be ignored.
	 *
	 * If one input type validation check fails an "error" event will be triggered.
	 *
	 * @return void
	 * @triggers success
	 * @triggers error
	 */
	protected function process()
	{
		$values = array();
		try
		{
			foreach ($this->inputs as $input)
			{
				if ($input->isDataSent())
				{
					if($input->isValid())
					{
						$values[$input->getProperty('name')] = $input->getProperty('value'); // @todo array values like &equipment[]=23
					}
					else
					{
						throw $input->exception;
					}
				}
			}
		}
		catch (\Dresscode\Exception\Input $e)
		{
			if (!$this->trigger('error', $e)->isDefaultPrevented())
			{
				throw $e;
			}
		}
		/*
			The success event will only be triggered when the size of the
			inputs inside this form and the count of the received values are matching.
			Otherwise it's assumed that this is not the correct form.
		*/
		if (count($values) === $this->inputs->length)
		{
			if (!$this->trigger('success', /*relatedTarget*/ null, /*data*/ $values)->isDefaultPrevented())
			{
				if ($this->jumpTo)
				{
					$this->Application->Response->Header->location($this->jumpTo, $this->Application->statusCode = 303); // 303 See Other
				}
			}
		}
	}
}