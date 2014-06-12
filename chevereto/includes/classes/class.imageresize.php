<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (c) Rodolfo Berrios <inbox@rodolfoberrios.com>
  
  Licensed under the MIT license
  http://opensource.org/licenses/MIT

  --------------------------------------------------------------------- */
  
/**
 * class.imageresize.php
 * This class is used to resize image source to image destination width
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @requires	- GD library <http://www.libgd.org/>
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class ImageResize {
	
	public $error;
	public $ok;
	
	function __construct($source, $destination, $width, $height='', $thumb=false)
	{
		$this->resize($source, $destination, $width, $height, $thumb);
	}
	
	/**
	 * resize_image
	 * Resizes the image (thumbnail and resize feature)
	 * 
	 * @param	string
	 * @return	string
	 */
	private function resize($source, $destination, $width, $height='', $thumb=false)
	{
		
		if(!is_integer_val($width) || !is_integer_val($height)) {
			$this->error = "invalid resize value";
			return false;
		}
		
		if(!file_exists($source)) {
			$this->error = "image source doesn't exists";
			return false;
		}
	
		$extension = substr($source, -3);
		$info = get_info($source);
		
		switch($extension) {
			case 'gif':
				$src = imagecreatefromgif($source);
			break;
			case 'png':
				$src = imagecreatefrompng($source);
			break;
			case 'jpg':
				$src = imagecreatefromjpeg($source);
			break;
		}
		
		// If the SRC is valid we do the thing
		if($src) {

			$imageSX = imageSX($src);
			$imageSY = imageSY($src);
			
			// Target resize == actual image size
			if(!$thumb) {
				if($width == $imageSX) {
					//$this->error = 'resize not needed';
					return false;
				} elseif (!is_config_over_resize() && $width > $imageSX) {
					$this->error = 'over resize detected';
					return false;
				}
			}
						
			if($thumb) {
				$source_ratio = $imageSX/$imageSY;
				$destination_ratio = $width/$height;
				
				// Ratio
				if ($destination_ratio>$source_ratio) {
				   $ratio_height = round($width/$source_ratio);
				   $ratio_width = $width;
				} else {
				   $ratio_width = round($height*$source_ratio);
				   $ratio_height = $height;
				}
				
				$target = imagecreatetruecolor($ratio_width, $ratio_height);
				
				$x_center = $ratio_width/2;
				$y_center = $ratio_height/2;
				
			} else {
				$height = round($width * $info['height']/$info['width']);
				$target = imagecreatetruecolor($width, $height);
			}

			// Copies SRC to TARGET
			// Allocate SRC transparency
			if ($extension=='gif' or $extension=='png') {
				$this->allocate_transparency($src, $extension);
				$this->image_transparent($src, $target);
			}
				
			if($thumb) {
				imagecopyresampled($target, $src, 0, 0, 0, 0, $ratio_width, $ratio_height, $imageSX, $imageSY);
				$thumb = imagecreatetruecolor($width, $height);
				// Re-allocate the transparency
				if($extension=='gif') {
					$this->image_transparent($thumb, $target);
					$this->image_transparent($target, $thumb);
				}
				if($extension=='png') {
					$this->allocate_transparency($thumb, $extension);
					$this->allocate_transparency($target, $extension);
				} 
				imagecopyresampled($thumb, $target, 0, 0, ($x_center-($width/2)), ($y_center-($height/2)), $width, $height, $width, $height);
				imagedestroy($target);
				$process = $thumb;
			} else {
				if($extension=='gif') $this->image_transparent($target, $thumb);
				if($extension=='png') $this->allocate_transparency($target, $extension);
				imagecopyresampled($target, $src, 0, 0, 0, 0, $width, $height, $imageSX, $imageSY);
				$process = $target;
			}
			
			// Sharpen the image just for JPG
			// PHP 5 >= 5.1.0
			if(function_exists('imageconvolution') && $extension=='jpg') {
				if($thumb) {
					$matrix = array(array(-1, -1, -1), array(-1, 24, -1), array(-1, -1, -1));
				} else {
					$matrix = array(array(-1, -1, -1), array(-1, 32, -1), array(-1, -1, -1));
				}
				$divisor = array_sum(array_map('array_sum', $matrix));
				imageconvolution($process, $matrix, $divisor, 0);
			}
			
			// Creates the image
			switch($extension) {
				case 'gif':
					imagegif($process, $destination);
				break;
				case 'png':
					imagepng($process, $destination);
				break;
				case 'jpg':
					imagejpeg($process, $destination, 80);
				break;
			}
			
			// Remove the temp files
			imagedestroy($process); 
			imagedestroy($src);	
			
			$this->ok = "image resized";
			
		} else { // src?
			$this->error = "invalid source";
		}
	}
	 

	/**
	 * allocate_transparency
	 * Allocates transparency on the target
	 *
	 * @param	string
	 * @return	string
	 */
	private function allocate_transparency($target, $extension)
	{
		if($extension=='png') {
			imagealphablending($target, false);
			imagesavealpha($target, true);
		} else {
			imagetruecolortopalette($target, true, 255);
			imagesavealpha($target, false);
		}
	}
	
	
	/**
	 * image_transparent
	 * Fetch the transparecy for the current resize process
	 *
	 * @param	string
	 * @return	string
	 */
	private function image_transparent($source, $target)
	{
		$transparent_index = imagecolortransparent($source);
		if ($transparent_index >= 0) {
			$transparent_color = imagecolorsforindex($source, $transparent_index);
			$transparent_index = imagecolorallocatealpha($target, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue'], 127);
			imagefill($target, 0, 0, $transparent_index);
			imagecolortransparent($target, $transparent_index);
		} else {
			$color = imagecolorallocatealpha($target, 0, 0, 0, 127);
			imagefill($target, 0, 0, $color);
		}
	}

}

?>