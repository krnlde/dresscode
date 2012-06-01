<?php
namespace Dresscode\Controller;

class Email extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var string
	 * @pattern /^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$/
	 */
	protected $from;

	/**
	 * @property
	 * @var string
	 * @pattern /^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$/
	 */
	protected $to;

	/**
	 * @property
	 * @var string
	 */
	protected $subject = '';

	/**
	 * @property
	 * @var boolean
	 */
	protected $fake = false;

	/**
	 * @property
	 * @var boolean
	 */
	protected $debug = false;

	public function setup()
	{
		if ($this->debug)
		{
			$this->fake = true;
		}
		if (!$this->subject)
		{
			$this->subject = $this->closest('Root')->getProperty('canonical');
		}
		if (!$this->from)
		{
			$this->setProperty('from', 'info@'.$this->Application->getName());
		}
		$inputs = array();
		foreach ($this->find('\Dresscode\Controller\Input') as $input)
		{
			$inputs[$input->getProperty('name')] = $input; // @todo what about radios and checkboxes?
		}

		$self = $this;
		$this->findOne('Form')->on('success', function($event) use ($self, $inputs) {
			$event->target->setProperty('jumpTo', $event->target->getProperty('jumpTo').'?name='.$event->data['name']); // @todo is this a good solution?
			$message = '';
			foreach ($event->data as $key => $value)
			{
				$message .= (isset($inputs[$key]) && $inputs[$key]->getProperty('label') ? $inputs[$key]->getProperty('label') : $key).': '. $value.PHP_EOL;
			}
			$header = 'From: '.$self->getProperty('from')."\r\n"
				// .'Reply-To: webmaster@example.com'."\r\n"
				. 'MIME-Version: 1.0'."\r\n"
				.'Content-Type: text/plain; charset=UTF-8'."\r\n"
				.'X-Mailer: Dresscode/'.\Dresscode\Version::VERSION.' PHP/'.phpversion();

			// @todo if HTML mail do: $message = htmlentities($message);
			if (!$self->getProperty('fake'))
			{
				if (!mail($self->getProperty('to'), $self->getProperty('subject'), $message, $header))
				{
					$self->error(new \Exception('Mail could not be delivered.'));
				}
			}
			if ($self->getProperty('debug'))
			{
				print_r(array
				( 'To'		=> $self->getProperty('to')
				, 'Subject'	=> $self->getProperty('subject')
				, 'Message'	=> $message
				, 'Header'	=> $header
				));
				die();
			}
		});
	}
}