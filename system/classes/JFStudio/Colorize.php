<?php
/**
*
*/

namespace JFStudio;

class Colorize
{
	public $colors;
	public $image;
	public static $AMOUNT_OF_COLORS = 50;
	private static $instance;

	public static function getInstance()
 	{
	    if(!self::$instance instanceof self)
	      self::$instance = new self;

	    return self::$instance;
 	}


	public function compareColors($hex = null, $alpha = false) 
	{
		$image = imagecreatefromstring(file_get_contents($this->image));
		
		// $image = imagecreatefrompng($this->image);
		imagetruecolortopalette($image, false, 255);

		foreach($this->colors as $id => $rgb)
		{
		    $result = imagecolorclosest($image, $rgb[0], $rgb[1], $rgb[2]);
		    $result = imagecolorsforindex($image, $result);

		    $data[] = [
		    	"compare" => $this->colors[$id],
		    	"image" => array_values($result),
		    ];
		}

		imagedestroy($image);

		return $this->sanitizeResult($data);
	}

	public function sanitizeResult($results = null) 
	{
		if(isset($results) === true)
		{
			foreach ($results as $_key => $result) 
			{
				foreach ($result['compare'] as $key => $value) 
				{
					$data[$_key][] = abs($value - $result['image'][$key]);
				}
			}
		}

		foreach ($data as $key => $value) {
			$avg[] = [
				"hex_compare" => $this->rgbToHex($results[$key]['compare']),
				"hex_image" => $this->rgbToHex($results[$key]['image']),
				"avg" => array_sum($value) / sizeof($value)
			];
		}

		return $avg;
	}

	public function rgbToHex($rgb = null) 
	{
		return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]); 
	}

	public function hexToRgb($hex = null, $alpha = false,$array_values = false) 
	{
		if(isset($hex) === true)
		{
			$hex = str_replace('#', '', $hex);
			$length  = strlen($hex);
			$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
			$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
			$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));

			if (isset($alpha)) {
				$rgb['a'] = $alpha;
			}

			return $array_values === true ? array_values($rgb) : $rgb;
		}
	}
}