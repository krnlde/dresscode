<?php
namespace Dresscode\Controller;

use \Assetic\Asset\StringAsset;

class Link extends \Dresscode\Controller
{

  /**
   * @property
   * @var string
   */
  protected $url;

  /**
   * Activating this feature will require client side JavaScript!
   *
   * @property
   * @var boolean
   */
  protected $encrypt = false;

    /**
    * @property
     * @var string
     */
    protected $secret = 'secret';

	/**
   * @property
	 * @var string
	 */
	protected $cipher;

	/**
	 * @property
	 * @var string
	 */
	protected $to;

	/**
	 * @property
	 * @var string
	 */
	protected $description;

	/**
	 * @property
	 * @var boolean
	 */
	protected $escape = true;

	public function post(array $params = array()) {
		$self = $this;
		$this->closest('Root')->on('ready', function () use ($self, $params) {
			if ($self->getXPath() === $params['xpath']) {
				die(print_r($params, true)); // @TODO
			}
		});
	}

	public function get(array $params = array()) {
		if ($this->to) {
			if (substr($this->to, 0, 1) === '/') {
				$appendix = $this->Application->basePath();
				$this->to = ($appendix != '/' ? $appendix : '').$this->to;
			}
			if ($this->escape && !preg_match('/$[a-zA-Z+-]+:/', $this->to)) {
				$this->url = implode
					( '/'
					, array_map
						( function($element) {
								return urlencode($element);
							}
						, explode
							( '/'
							, $this->to
							)
						)
					);
				$this->url = str_replace('%40', '@', $this->url); // recover mail declaration (from urlencode)
				$this->url = str_replace('%23', '#', $this->url); // recover site anchor declaration (from urlencode)
				$this->url = preg_replace('/^([a-z]+)\%3A\/\//', '$1://', $this->url); // recover scheme declaration (from urlencode)
				if (preg_match('/^[a-zA-Z0-9._%+-]+\@[a-zA-Z0-9.-]+\.(?:[a-zA-Z]{2,10})$/', $this->url)) {
					$this->url = 'mailto:'.$this->url;
        }
      } else {
        $this->url = $this->to;
      }
      $this->url = str_replace(' ', '%20', $this->url); // Replace whitespace

      if ($this->encrypt) {
        $this->Application->externalJavascript('/applications/common/modules/Link/assets/js/decrypt-links.js');
        $this->to = $this->encrypt($this->to);
        $this->url = $this->encrypt($this->url);
      }
      if ($this->node->childNodes->length === 0) {
        $this->node->appendChild($this->dom->createTextNode($this->to));
      }
		}
		if (!strlen($this->node->nodeValue)) {
			$this->node->appendChild($this->dom->createTextNode($this->url));
		}
    $this->setProperty('to', $this->to);
    $this->setProperty('url', $this->url);
	}

  private function encrypt($string) {
    $encrypted = '';
    $shift = strlen($string);

    if (!$this->cipher) {
      $this->cipher = $this->generateCipher();
    }

    // Based on www.jottings.com/obfuscator.htm

    for ($i = 0; $i < strlen($string); $i++) {
      $char = substr($string, $i, 1);
      if (strpos($this->cipher, $char) === false) {
        $encrypted .= $char;
      }
      else {
        $chr = (strpos($this->cipher, $char) + $shift) % strlen($this->cipher);
        $encrypted .= substr($this->cipher, $chr, 1);
      }
    }
    return $encrypted;
  }

  private function generateCipher() {
    $charPositions = array(
        array(48, 57)
      , array(65, 90)
      , array(97, 122)
      // , array(128, 165)
      // , array(181, 183)
    );
    $chars = array();
    for ($i = 0; $i < count($charPositions); $i++) {
      for($j = $charPositions[$i][0]; $j <= $charPositions[$i][1]; $j++) {
        $chars[] = chr($j);
      }
    }
    shuffle($chars);
   return join($chars);
  }
}