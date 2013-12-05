<?php
namespace Dresscode\Controller;


class Googlemaps extends \Dresscode\Controller
{
	/**
	 * @property
	 * @var integer
	 */
	protected $zoom = 12;

	/**
	 * @property
	 * @var string
	 */
	protected $address;

	/**
	 * @property
	 * @var float
	 */
	protected $latitude;

	/**
	 * @property
	 * @var float
	 */
	protected $longitude;


	public function setup(array $params = array())
	{
		parent::setup();
		if (!$this->id)
		{
			$this->id = $this->generateId();
		}
	}

	/**
	 * Filters ({@see \Dresscode\Controller\Filter}) can be applied to modify the amount of the resulting images.
	 *
	 * @triggers loadFile
	 * @throws \Dresscode\Exception
	 */
	public function get(array $params = array())
	{
		if (strlen($this->address) == 0 && (strlen($this->latitude) == 0 || strlen($this->longitude) == 0)) {
			$this->error(new \Dresscode\Exception('Either @address or @latitude and @longitude are obligatory'));
			return;
		}

		$this->Application->externalJavascript('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');

		if (strlen($this->address)) {
			$this->Application->javascript(new \Assetic\Asset\StringAsset('
				jQuery(function ($) {
					(new google.maps.Geocoder).geocode( { "address": "'.$this->address.'"}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							var map = new google.maps.Map(document.getElementById("'.$this->id.'"), {
								zoom: '.$this->zoom.',
								center: results[0].geometry.location
							});
							var marker = new google.maps.Marker({
								map: map,
								position: results[0].geometry.location
							});
						} else {
							alert("Geocode was not successful for the following reason: " + status);
						}
					});
				});
			'));
		} else {
			$this->Application->javascript(new \Assetic\Asset\StringAsset('
				jQuery(function ($) {
					var map = new google.maps.Map(document.getElementById("'.$this->id.'"), {
						zoom: '.$this->zoom.',
						center: new google.maps.LatLng('.$this->latitude.', '.$this->longitude.')
					});
					var marker = new google.maps.Marker({
						map: map,
						position: new google.maps.LatLng('.$this->latitude.', '.$this->longitude.')
					});
				});
			'));
		}
	}
}