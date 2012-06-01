<?php
/**
 *  Copyright (C) 2011 Kai Dorschner
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @copyright	Copyright 2011, Kai Dorschner
 * @license		http://www.gnu.org/licenses/gpl.html GPLv3
 * @package		Dresscode
 */
namespace Dresscode;

/**
 * Chainable image manipulation class.
 *
 * Example usage of the image class:
 * <code>
 * 		header('Content-Type: image/png');
 * 		echo Image::fromFile('largeTestFile.jpg')
 * 			->crop(false)
 * 			->backgroundColor('ffffff')
 * 			->align('middle')
 * 			->resize(400, 300)
 * 			->png()
 *			;
 * </code>
 *
 * @author		Kai Dorschner <the-kernel32@web.de>
 * @package		Dresscode
 */
class Image
{
	const HEXCOLOR				= '/^#?([0-9a-fA-F]{6})$/';
	const BACKGROUNDCOLOR		= 'ffffff';
	const ALIGN					= 'middle';
	const MAX_SIZE				= 2500;
	const MIN_SIZE				= 20;
	const FATAL_ERROR			= 1;

	protected	  $x				= 0			// offsetX used for alignment
				, $y				= 0			// offsetY used for alignment
				, $width						// width of the output image
				, $height						// height of the output image
				, $quality			= 100		// output image quality in percent
				, $backgroundColor				// background color for the output image
				, $crop				= false		// defines whether the source image should be cropped when resized
				, $align						// alignement defines the new position of the source when resized
				, $image						// output image resource
				;

	/**
	 * Constructor creates image resource.
	 *
	 * If the {@link $imagepath} cannot be found an error image resource will be created instead.
	 *
	 * @access public
	 * @param string $imagepath
	 */
	public function __construct($width, $height)
	{
		$this->backgroundColor	= self::BACKGROUNDCOLOR;
		$this->align			= self::ALIGN;
		set_error_handler(array($this, 'error'));
		set_exception_handler(array($this, 'exception'));
		$this->createEmptyImage($width, $height);
	}

	/**
	 * Used for chaining. Creates an image object from file.
	 *
	 * @access public
	 * @static
	 * @return Image
	 * @param string $imagepath
	 */
	public static function fromFile($imagepath)
	{
		list($width, $height, $type, $attr) = getimagesize($imagepath);
		$new = new self($width, $height);
		return $new->image($imagepath, $width, $height, 0, 0);
	}
	public static function create($width, $height)
	{
		return new self($width, $height);
	}

	/**
	 * Finally destroys open resources.
	 *
	 * @access public
	 */
	public function __destruct()
	{
		$this->destroyImage();
	}


	/**
	 * Generates a jpg output.
	 *
	 * @access public
	 * @return mixed JPG file
	 */
	public function jpg()
	{
		ob_start();
		imageJpeg($this->image, '', $this->quality);
		$image = ob_get_contents();
		ob_end_clean();
		return $image;
	}

	/**
	 * Generates a png output.
	 *
	 * @access public
	 * @return mixed PNG file
	 */
	public function png()
	{
		ob_start();
		imagePng($this->image, '', (int)ceil($this->quality / 100));
		$image = ob_get_contents();
		ob_end_clean();
		return $image;
	}


	/**
	 * Creates an empty image resource based on the dimensions {@link $width} and {@link $height}
	 *
	 * @access public
	 * @return Image $this
	 * @param int $width
	 * @param int $height
	 */
	public function createEmptyImage($width, $height)
	{
		$this->image = imageCreateTrueColor($this->width = $width, $this->height = $height);
		$this->drawBackgroundColor($this->backgroundColor());
		$this->align($this->align);
		return $this;
	}

	public function antialias($set = true)
	{
		imageantialias($this->image, $set);
		return $this;
	}

	public function image($imagepath, $width = null, $height = null, $x = null, $y = null)
	{
		$source = $this->imageResource($imagepath);
		$data = $this->readSource($imagepath);
		if (is_null($width) && is_null($height)) $width = $data['width'];
		if (is_null($width)) $width		= (int)floor($data['width'] * ($height / $data['height']));
		if (is_null($height)) $height	= (int)floor($data['height'] * ($width / $data['width']));
		if (is_int($width) && is_int($height))
		{
			if (self::MAX_SIZE >= $width && self::MAX_SIZE >= $height)
			{
				if ($width < self::MIN_SIZE) $width = self::MIN_SIZE;
				if ($height < self::MIN_SIZE) $height = self::MIN_SIZE;
				imagecopyresampled
					( $this->image	// This will be written
					, $source		// This will be copied
					, (is_null($x) ? $this->currentPositionX($width) : $x)		// Destination X
					, (is_null($y) ? $this->currentPositionY($height) : $y)		// Destination Y
					, 0				// Source X
					, 0				// Source Y
					, (int)$width
					, (int)$height
					, (int)$data['width']
					, (int)$data['height']
					);
			}
		}
		return $this;
	}

	public function currentPositionX($width)
	{
		switch($this->align)
		{
			case 'topleft':
			case 'left':
			case 'bottomleft':
				return $this->x;
			break;
			case 'topright':
			case 'right':
			case 'bottomright':
				return $this->x - $width;
			break;
			case 'top':
			case 'bottom':
			default: // middle
				return $this->x - $width / 2;
		}
	}

	public function currentPositionY($height)
	{
		switch($this->align)
		{
			case 'topleft':
			case 'top':
			case 'topright':
				return $this->y;
			break;
			case 'bottomleft':
			case 'bottom':
			case 'bottomright':
				return $this->y - $height;
			case 'left':
			case 'right':
			default: // middle
				return $this->y - $height / 2;
		}
	}
	/**
	 * Resizes the output image.
	 *
	 * At least one of both arguments is omitted, the other one is scaled when not set.
	 *
	 * @access public
	 * @return Image $this
	 * @param int $width Default: null
	 * @param int $height Default: null
	 */
	public function resize($width = null, $height = null)
	{
		if (!is_null($width) || !is_null($height))
		{
			if (is_null($width)) $width		= (int)floor($this->width * ($height / $this->height));	// keep aspect ratio
			if (is_null($height)) $height	= (int)floor($this->height * ($width / $this->width));	// keep aspect ratio
			if ($width <= self::MAX_SIZE && $height <= self::MAX_SIZE)
			{
				if ($width < self::MIN_SIZE) $width = self::MIN_SIZE;
				if ($height < self::MIN_SIZE) $height = self::MIN_SIZE;
				$copy		= $this->image;
				$copyWidth	= $this->width;
				$copyHeight	= $this->height;
				$this->createEmptyImage($width, $height);
				if ($this->width < $copyWidth)
					$widthFactor	= $resizeFactor = $this->width / $copyWidth;
				else
					$widthFactor	= 1;
				if ($this->height < $copyHeight)
					$heightFactor	= $resizeFactor = $this->height / $copyHeight;
				else
					$heightFactor	= 1;
				if (($widthFactor > $heightFactor) ^ !$this->crop)
					$resizeFactor	= $widthFactor;
				else
					$resizeFactor	= $heightFactor;
				$newWidth	= $copyWidth * $resizeFactor;
				$newHeight	= $copyHeight * $resizeFactor;
				imagecopyresampled
					( $this->image							// This will be written
					, $copy									// This will be copied
					, $this->currentPositionX($newWidth)	// Destination X
					, $this->currentPositionY($newHeight)	// Destination Y
					, 0										// Source X
					, 0										// Source Y
					, $newWidth								// Destination width
					, $newHeight							// Destination height
					, (int)$copyWidth						// Source width
					, (int)$copyHeight						// Source height
					);
				imagedestroy($copy);
			}
			else
				$this->error(self::FATAL_ERROR, 'Dimension exceeds limit.', __FILE__, __LINE__);
		}
		return $this;
	}

	/**
	 * Draws the background color.
	 *
	 * @access public
	 * @return Image $this
	 * @param string $hex Default: null
	 */
	public function drawBackgroundColor($hex = null)
	{
		if (!is_null($hex))
			$this->backgroundColor($hex);
		imageFilledRectangle
			( $this->image
			, 0
			, 0
			, $this->width
			, $this->height
			, $this->color($this->backgroundColor())
			);
		return $this;
	}

	public function text($text, $color = '000000', $offsetX = 0, $offsetY = 0)
	{
		imagestring
			( $this->image
			, 4			// font type
			, $offsetX
			, $offsetY
			, $text
			, $this->color($color)
			);
		return $this;
	}

	public function ttfText($fontFile, $text, $size = 9, $angle = 0, $color = '000000', $offsetX = 0, $offsetY = 0)
	{
		if (strlen($text) > 0)
		{
			imagettftext
				( $this->image
				, $size
				, $angle
				, $offsetX
				, $offsetY
				, $this->color($color)
				, $fontFile
				, $text
				);
		}
		return $this;
	}

	public function ttfTextBlock($fontFile, $text, $width = 100, $height = 30, $angle = 0, $color = '000000', $offsetX = 0, $offsetY = 0)
	{
		if (strlen($text) > 0)
		{
			$fontSize = 1;
			do
			{
				$x = imagettfbbox($fontSize, $angle, $fontFile, $text);
				$textWidth = abs($x[4] - $x[0]);
				$textHeight = abs($x[5] - $x[1]);
				$fontSize += .1;
			}
			while ($textWidth < $width && $textHeight < $height);
			$fontSize -= .1;
			imagettftext
				( $this->image
				, $fontSize
				, $angle
				, $offsetX
				, $offsetY
				, $this->color($color)
				, $fontFile
				, $text
				);
		}
		return $this;
	}

	/**
	 * Creates an error image resource.
	 *
	 * This method is also defined as error_handler for occuring PHP errors.
	 *
	 * @access public
	 * @return Image $this
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile Default: null
	 * @param int $errline Default: null
	 * @param array $errcontext Default: null
	 */
	public function error($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
	{
		debug // @debug
		( json_encode_pretty
			( array
				( 'error'	=> $errstr

				// DEBUG ONLY !!!
				, 'code'	=> $errno
				, 'file'	=> $errfile
				, 'line'	=> $errline
				, 'trace'	=> $errcontext
				)
			)
		);

		if (substr($errstr, 0, 4) == 'exif') // @todo USE WITH CAUTION!!! It just removes the "[function.exif-read-data]: Incorrect APP1 Exif Identifier Code" error!
			return $this;

		$this
			->destroyImage()
			->createEmptyImage
				( $this->width = 600
				, $this->height = 400
				)
			->drawBackgroundColor
				( 'ff0000'
				)
			->text
				( '#'.$errno.': '.$errstr
				, 'ffffff'
				, 20
				, 170
				)
			->text
				( 'in "'.$errfile.'" line '.$errline
				, 'ffffff'
				, 20
				, 190
				);
		return $this;
	}

	/**
	 * Creates an error image resource.
	 *
	 * This method is also defined as exception_handler for occuring PHP exceptions.
	 *
	 * @access public
	 * @return Image $this
	 * @param Exception $e
	 */
	public function exception(Exception $e)
	{
		$this->error
			( $e->getCode()
			, 'Exception: '.$e->getMessage()
			, $e->getFile()
			, $e->getLine()
			, $e->getTrace()
			);
		return $this;
	}

	/**
	 * Destroys the current image resource.
	 *
	 * @access public
	 * @return Image $this
	 */
	public function destroyImage()
	{
		if (is_resource($this->image))
			imageDestroy($this->image);
		return $this;
	}


	/* Getters and Setters */
	public function source()
	{
		return $this->source;
	}

	public function x()
	{
		return $this->x;
	}

	public function y()
	{
		return $this->y;
	}

	public function width()
	{
		return $this->width;
	}

	public function height()
	{
		return $this->height;
	}

	public function quality($quality = null)
	{
		if (is_null($quality))
			return $this->quality;
		$this->quality = (int)$quality;
		return $this;
	}

	public function backgroundColor($hex = null)
	{
		if (is_null($hex))
			return $this->backgroundColor;
		$this->color($hex);
		$this->backgroundColor = $hex;
		return $this;
	}

	public function crop($crop = null)
	{
		if (is_null($crop))
			return $this->crop;
		$this->crop = (boolean)$crop;
		return $this;
	}

	/**
	 * Converts a hex string to an array with three elements representing the colorcode (red, green, blue).
	 *
	 * @access protected
	 * @return array
	 * @param string $hex
	 */
	protected function hexToRGB($hex)
	{
		$array = array();
		if (preg_match(self::HEXCOLOR,$hex, $regs))
		{
			$hex			= $regs[1];
			$array['red']	= hexdec(substr($hex, 0, 2));
			$array['green']	= hexdec(substr($hex, 2, 2));
			$array['blue']	= hexdec(substr($hex, 4, 2));
		}
		return $array;
	}

	/**
	 * Creates an color used by the PHP image functions.
	 *
	 * @access protected
	 * @return int
	 * @param string $hex
	 */
	protected function color($hex)
	{
		if (preg_match(self::HEXCOLOR, $hex))
			$color = $this->hexToRGB($hex);
		else
			$this->error(self::FATAL_ERROR, 'Wrong background color format: '.$hex, __FILE__, __LINE__);
		return imagecolorallocate
			( $this->image
			, $color['red']
			, $color['green']
			, $color['blue']
			);
	}

	/**
	 * Sets the offset of the output image to the user defined alignment.
	 *
	 * @access protected
	 * @return void
	 */
	public function align($align)
	{
		switch ($align)
		{
			case 'topleft':
				$this->x = 0;
				$this->y = 0;
			break;
			case 'top':
				$this->x = $this->width / 2;
				$this->y = 0;
			break;
			case 'topright':
				$this->x = $this->width;
				$this->y = 0;
			break;
			case 'right':
				$this->x = $this->width;
				$this->y = $this->height / 2;
			break;
			case 'bottomright':
				$this->x = $this->width;
				$this->y = $this->height;
			break;
			case 'bottom':
				$this->x = $this->width / 2;
				$this->y = $this->height;
			break;
			case 'bottomleft':
				$this->x = 0;
				$this->y = $this->height;
			break;
			case 'left':
				$this->x = 0;
				$this->y = $this->height / 2;
			break;
			default: // middle
				$this->x = $this->width / 2;
				$this->y = $this->height / 2;
		}
		$this->align = $align;
		return $this;
	}

	/**
	 * Returns an image resource based on the source image type.
	 *
	 * @access protected
	 * @return resource
	 */
	protected function imageResource($imagePath)
	{
		if (is_readable($imagePath))
		{
			$data = $this->readSource($imagePath);
			switch($data['type'])
			{
				case IMAGETYPE_JPEG:
					return imagecreatefromjpeg($imagePath);
				break;
				case IMAGETYPE_GIF:
					return imagecreatefromgif ($imagePath);
				break;
				case IMAGETYPE_PNG:
					return imagecreatefrompng($imagePath);
				break;
			}
		}
		$this->error('Image not found');
		return $this->image;
	}

	/**
	 * Fetches information about the source image.
	 *
	 * @access protected
	 * @return void
	 */
	protected function readSource($imagePath)
	{
		$data = getimagesize($imagePath);
		return array
			( 'width'		=> $data[0]
			, 'height'		=> $data[1]
			, 'type'		=> $data[2]
			, 'exif'		=> ($data[2] === IMAGETYPE_JPEG ? $exif = @exif_read_data($imagePath, '', true, false) : null)
			, 'orientation'	=> (isset($exif['Orientation']) ? $exif['Orientation'] : (isset($exif['IFD0']['Orientation']) ? $exif['IFD0']['Orientation'] : 1))
			);
	}
}