<?php

namespace JFStudio;

class Img
{
	const ROOT = "../";
	# public vars
	public $valid_formats = ['image/jpeg','image/png'];
	public $max_width = 52;
	public $max_height = 52;
	public $filename = 52;
	public $output = 'src';
	public $quality = ['png'=>9,'jpeg'=>100];
	public $image_properties = [];

	public function __construct($filename = false,$output = false)
	{
		if($this->filename) {
			$this->loaded = true;
			$this->filename = $filename;
			$this->image_properties = getimagesize($this->filename);
			$this->image_properties['type'] = explode('/',$this->image_properties['mime'])[1];
		}

		$this->output = $output ? $output : $this->filename;
	}

	public function __destruct() { }

	public function __clone() { }

	public function setQuality($quality = false)
	{
		if($quality)
		{
			$this->quality[$this->image_properties['type']] = $quality;
		}
	}
	public function resize($max_width = false,$max_height = false)
	{
		if(in_array($this->image_properties['mime'],$this->valid_formats))
		{
			$this->max_width = $max_width ? $max_width : $this->max_width;
			$this->max_height = $max_height ? $max_height : $this->max_height;

			switch ($this->image_properties['type']) {
				case 'jpeg':
					$image = imagecreatefromjpeg($this->filename);
					break;
				case 'png':
					$image = imagecreatefrompng($this->filename);
					break;
				default:
					break;
			}

			list($width,$height) = $this->image_properties;

			$x_ratio = $this->max_width / $width;
			$y_ratio = $this->max_height / $height;

			if( ($width <= $this->max_width) && ($height <= $this->max_height) ){//Si width
				$width = $width;
				$height = $height;
			} elseif (($x_ratio * $height) < $this->max_height) {
				$height = ceil($x_ratio * $height);
				$width = $this->max_width;
			} else {
				$width = ceil($y_ratio * $width);
				$height = $this->max_height;
			}

			if($new_image = imagecreatetruecolor($width,$height))
			{
				if(imagealphablending($new_image,false))
				{
					if(imagesavealpha($new_image,true))
					{
						if(imagecopyresampled($new_image,$image,0,0,0,0,$width,$height,imagesx($image),imagesy($image)))
						{
							$image = $new_image;

							if(imagealphablending($image,false))
							{
								if(imagesavealpha($image,true))
								{
									switch ($this->image_properties['type']) {
										case 'jpeg':
											imagejpeg($image, $this->output, $this->quality[$this->image_properties['type']]);
											break;
										case 'png':
											imagepng($image, $this->output, $this->quality[$this->image_properties['type']]);
											break;
										default:
											break;
									}
									return true;
								}
							}
						}
					}
				}
			}
		}

		return false;
	}

	public static function getFileExtension(string $url = null) : string
	{

		$file_extension = strtolower(substr(strrchr(basename($url),"."),1));

		if($file_extension === 'gif')
		{
			return 'image/gif';
		} else if($file_extension === 'png') {
			return 'image/png';
		} else if($file_extension === 'jpeg' || $file_extension === 'jpg') {
			return 'image/jpeg';
		} else if($file_extension === 'svg') {
			return 'image/svg+xml';
		}
	}

	public static function displayImage(string $url = null)
	{
		if(isset($url) === true)
		{
			if (file_exists($url) === true) 
			{
				header('Content-type: ' . self::getFileExtension($url));
				readfile($url);
			}
		}
	}
}