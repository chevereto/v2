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
 * class.upload.php
 * This class is used to do image upload from URL or $_FILES
 * Also includes thumbnail generation and resize.
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @requires	- GD library <http://www.libgd.org/>
 *				- cURL <http://php.net/manual/en/book.curl.php>
 *				- class.imageconvert.php
 *				- class.imageresize.php
 *				- chevereto functions.php
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class Upload {
	public $is_remote;
	public $img_upload_path;
	
	public $resize_width;
	public $thumb_width;
	public $thumb_height;
	
	public $max_size;
	public $storage;
	
	public $error;
	public $image_info;
	
	private $source;
	private $info;
	private $mime;
	private $extension;
	private $working;

	// Grab the source
	function __construct($source) {
		global $dB;
		$this->source = $source;
		$this->dB = $dB;
	}

	/**
	 * process
	 * Does the thing
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function process()
	{
		
		if($this->valid_data()) {
			
			$this->extension = $this->get_true_extension($this->mime);		
			if($this->extension == 'bmp') {
				require_once('class.imageconvert.php');
				$this->ImageConvert = new ImageConvert($this->working, $this->extension, $this->img_upload_path.'temp_'.generateRandomString(256));
				unset($this->working); unset($this->extension);
				$this->working = $this->ImageConvert->out;
				$this->extension = 'png';
			}
			
			switch($this->storage) {
				case 'direct':
					$this->img_upload_path = __CHV_PATH_IMAGES__;
				break;
				case 'datefolder': case 'datefolders':
				default:
					// Sets the date folder YYYY/MM/DD
					$datefolder = $this->img_upload_path.date('Y/m/d/');
					$old_umask = umask(0);
					if(!file_exists($datefolder) && !@mkdir($datefolder, 0755, true)) {
						$this->error = "Unable to create upload folder";
						return false;
					}
					umask($old_umask);
					$this->img_upload_path = $datefolder;
				break;
			}
			
			$image_filename = $this->nameFile($this->img_upload_path, $this->extension, chevereto_config('file_naming'), $this->original_file_name);
			
			// Prepare and formats the temp image
			$formated_temp = $this->working.'.'.$this->extension;
			rename($this->working, $formated_temp);
			unset($this->working); $this->working = $formated_temp;
			
			// Call the resize class
			require_once('class.imageresize.php');
			
			// Thumb
			$thumb_filename = str_replace($this->extension, 'th.'.$this->extension, $image_filename);
			$this->ThumbResize = new ImageResize($this->working, $thumb_filename, $this->thumb_width, $this->thumb_height, true);
			// Fixed width but fluid height? Replace the line above with this:
			// $this->ThumbResize = new ImageResize($this->working, $thumb_filename, $this->thumb_width);
			
			if(check_value($this->ThumbResize->error)) {
				$this->error = $this->ThumbResize->error." (thumb)";
				return false;
			}
						
			// Resize?
			if(check_value($this->resize_width)) {
				$this->ImageResize = new ImageResize($this->working, $this->working, $this->resize_width);
				if(check_value($this->ImageResize->error)) {
					$this->error = $this->ImageResize->error;
					return false;
				}
			}
			
			if(!check_value($this->error)) {
				
				// Apply the watermark ?
				if(!is_animated_image($this->working) && conditional_config('watermark_enable') and chevereto_config('watermark_opacity')>0) {

					switch($this->extension) {
						case 'gif':
							$src = imagecreatefromgif($this->working);
						break;
						case 'png':
							$src = imagecreatefrompng($this->working);
						break;
						case 'jpg':
							$src = imagecreatefromjpeg($this->working);
						break;
					}
					$src_width = imagesx($src);
					$src_height = imagesy($src);
					
					$watermark_src = imagecreatefrompng(__CHV_WATERMARK_FILE__);
					$watermark_width = imagesx($watermark_src);
					$watermark_height = imagesy($watermark_src);

					// Calculate the position
					switch(chevereto_config('watermark_x_position')) {
						case 'left':
							$watermark_x = chevereto_config('watermark_margin');
						break;
						case 'center':
							$watermark_x = $src_width/2 - $watermark_width/2;
						break;
						case 'right':
							$watermark_x = $src_width - $watermark_width - chevereto_config('watermark_margin');
						break;
					}
					switch(chevereto_config('watermark_y_position')) {
						case 'top':
							$watermark_y = chevereto_config('watermark_margin');
						break;
						case 'center':
							$watermark_y = $src_height/2 - $watermark_height/2;
						break;
						case 'bottom':
							$watermark_y = $src_height - $watermark_height - chevereto_config('watermark_margin');
						break;
					}
					
					// Watermark has the same or greater size of the image ?
					// --> Center the watermark
					if($watermark_width == $src_width && $watermark_height == $src_height) {
						$watermark_x = $src_width/2 - $watermark_width/2;
						$watermark_y = $src_height/2 - $watermark_height/2;
					}
					
					// Watermark is too big ?
					// --> Fit the watermark on the image
					if($watermark_width > $src_width || $watermark_height > $src_height) {
						// Watermark is wider than the image
						if($watermark_width > $src_width) {
							$watermark_new_width  = $src_width;
							$watermark_new_height = $src_width * $watermark_height / $watermark_width;
							if($watermark_new_height > $src_height) {
								$watermark_new_width  = $src_height * $watermark_width / $watermark_height;
								$watermark_new_height = $src_height;
							}
						} else {
							$watermark_new_width  = $src_height * $watermark_width / $watermark_height;
							$watermark_new_height = $src_height;
						}
						$watermark_temp = $this->img_upload_path.'temp_watermark_'.generateRandomString(64).'.png';
						$WatermarkResize = new ImageResize(__CHV_WATERMARK_FILE__, $watermark_temp, $watermark_new_width);
						if(!check_value($WatermarkResize->error)) {
							$watermark_width = $watermark_new_width;
							$watermark_height = $watermark_new_height;
							$watermark_src = imagecreatefrompng($watermark_temp);
							$watermark_x = $src_width/2 - $watermark_width/2;
							$watermark_y = $src_height/2 - $watermark_height/2;
						}
					}
										
					// Apply and save the watermark
					imagecopymerge_alpha($src, $watermark_src, $watermark_x, $watermark_y, 0, 0, $watermark_width, $watermark_height, chevereto_config('watermark_opacity'), $this->extension);
					
					switch($this->extension) {
						case 'gif':
							imagegif($src, $this->working);
						break;
						case 'png':
							imagepng($src, $this->working);
						break;
						case 'jpg':
							imagejpeg($src, $this->working, 96);
						break;
					}
					imagedestroy($src);
					@unlink($watermark_temp);
				}
				
				// Move the temp to the final path...
				$uploaded = rename($this->working, $image_filename);
				
				// Change the CHMOD of the file (for some php enviroments)
				@chmod($image_filename, 0644);
				@chmod($thumb_filename, 0644);
				
				if($uploaded) {
					$info = get_info($image_filename);
					$file_path = absolute_to_relative($image_filename);
					$thumb_path = absolute_to_relative($thumb_filename);
					$image_url = absolute_to_url($image_filename);
					$name = str_replace('.'.$this->extension, '', str_replace($this->img_upload_path, '', $image_filename));	
					$this->image_info = array(
						'image_name'			=> $name,
						'image_filename'		=> $name.".".$this->extension,
						'image_type'			=> $this->extension,
						'image_path'			=> $file_path,
						'image_url'				=> $image_url,
						'image_width'			=> $info['width'],
						'image_height'			=> $info['height'],
						'image_attr'			=> 'width="'.$info['width'].'" height="'.$info['height'].'"',
						'image_bytes'			=> $info['bytes'],
						'image_size'			=> $info['size'],
						'image_thumb_url'		=> absolute_to_url($thumb_filename),
						'image_thumb_path'		=> $thumb_path,
						'image_thumb_width'		=> $this->thumb_width,
						'image_thumb_height'	=> $this->thumb_height
					);
					
					switch($this->storage) {
						case 'direct':
							$this->image_info['storage_id'] = 2;
						break;
						case 'datefolder': case 'datefolders':
							$this->image_info['storage_id'] = NULL;
						break;
					}
					
					// Shorthand the dB object
					$dB = $this->dB;
					
					if($dB->dead) {
						$this->error = $dB->error;
						return false;
					}

					if($dB->insert_file($this->image_info)) {
						$image_delete_hash = $dB->image_delete_hash;
						$this->image_info['image_id'] = $dB->last_insert_id();
						$this->image_info['image_id_public'] = encodeID($this->image_info['image_id']);
						$this->image_info['image_viewer'] = __CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/'.$this->image_info['image_id_public'];
						$this->image_info['image_shorturl'] = __CHV_BASE_URL__.$this->image_info['image_id_public'];
						$this->image_info['image_delete_hash'] =  $image_delete_hash;
						$this->image_info['image_delete_url'] = __CHV_BASE_URL__.'delete/image/'.$this->image_info['image_id_public'].'/'.$image_delete_hash;
						$this->image_info['image_delete_confirm_url'] = __CHV_BASE_URL__.'delete-confirm/image/'.$this->image_info['image_id_public'].'/'.$image_delete_hash;
						$this->image_info['image_date']	= date('Y-m-d H:i:s', time());
						
						return true;
					} else {
						unlink($image_filename);
						unlink($thumb_filename);
						$this->error = $dB->error;
						return false;
					}

				} else {
					unlink($this->working);
					$this->error = 'error uploading';
					return false;
				}
			} else {
				unlink($this->working);
				return false;				
			}
		} else { // Invalid data
			return false;
		}
	}

	
	/**
	 * valid_data
	 * Validate the whole data before do the thing.
	 *
	 * @param	string
	 * @return	mixed
	 */
	private function valid_data()
	{
		// Sourced?
		if(!check_value($this->source)) {
			$this->error = 'empty source';
		} else { // it's sourced.. But is valid?
			if($this->is_remote) {
				// URL Check...
				if(!is_image_url($this->source)) $this->error = 'invalid image url';
			} else {
				// $_FILES Check...
				if(count($this->source)<5 and !is_numeric($this->source['size'])) $this->error = 'invalid source';
			}
		}
		
		// Valid thumb size?
		if(!is_numeric($this->thumb_width) or !is_numeric($this->thumb_height)) {
			$this->error = 'invalid thumb size';
		}
		
		// Setted and valid resize?
		if($this->seted_value($this->resize_width) and !is_numeric($this->resize_width)) {
			$this->error = 'invalid resize size';
		}		
		
		// No errors... Process the thing
		if(!check_value($this->error) and $this->fetch_image()) {
			$this->info = get_info($this->working);
			if(!check_value($this->info)) {
				unlink($this->working);
				$this->error = 'invalid image';
				return false;
			} else {
				// The image is valid... Check for valid MIME
				if(!$this->valid_mime($this->info['mime'])) {
					unlink($this->working);
					$this->error = 'invalid mime';
					return false;
				} else {
					$this->mime = $this->info['mime'];
					// The MIME is valid... Check for valid size (bytes)
					if(filesize($this->working)>$this->max_size) {
						unlink($this->working);
						$this->error = 'too big';
						return false;
					} else {
						return true; // All valid!
					}
				}
			}
		} else {
			return false; // errors = false
		}
	}
	
	 
	/**
	 * fetch_image
	 * Fetch the current image
	 * 
	 * @param	string
	 * @return	string
	 */
	private function fetch_image()
	{
		$destination = $this->img_upload_path.'temp_'.generateRandomString(256);
		
		if($this->is_remote) {
			// Upload method: URL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, str_replace('https://', 'http://', $this->source));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			// We upload the image to the temp directory
			$out = fopen($destination, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $out);
			curl_exec($ch);
			fclose($out);
			// Checkout the errors and image size using the cURL data
			if(!curl_errno($ch)) {
				if(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD)>$this->max_size) {
					unlink($destination);
					$this->error = 'too big';
				} else {
					$this->original_file_name = basename($this->source);
					$result = true;
				}
			}
			curl_close($ch);
		} else {
			/**
			 * Upload method: local
			 *
			 * Notice that $_FILES['filename']['type'] returns mime according with the
			 * file extension even if is invalid. This is just a .ext filter and later
			 * the mime will be really checked.
			 */
			if($this->max_size>$this->source['size']) {
				if($this->valid_extension($this->source['name'])) {
					$this->original_file_name = $this->source['name'];
					$result = @rename($this->source['tmp_name'], $destination); // For API... RENAME
					// @rename gives openbasedir restriction error! Add to check list in functions.php
				} else {
					$this->error = 'invalid extension';
				}
			} else {
				$this->error = 'too big';
			}
		}
		
		if(!$result) {
			if(!check_value($this->error)) $this->error = 'error uploading';
			return false;
		} else {
			$this->working = $destination;
			return true;
		}
	}
	 
	 	
	/**
	 * valid_extension
	 * This checks for valid file extension
	 *
	 * @access	private
     * @param	string
     * @return	boolean
	 */	
	private function valid_extension($file)
	{
		return preg_match('/^.*\.(jpg|jpeg|png|gif|bmp)$/i', $file);
	}


	/**
	 * get_true_extension
	 * Returns the true extension according to the mime
	 *
	 * @param	string
	 * @return	string
	 */
	private function get_true_extension($mime)
	{
		if ($mime=='image/gif')	{ $ext = 'gif'; }
		if ($mime=='image/png')	{ $ext = 'png'; }
		if ($mime=='image/pjeg' or $mime=='image/jpeg')		{ $ext = 'jpg'; }
		if ($mime=='image/bmp'	or $mime=='image/x-ms-bmp')	{ $ext = 'bmp'; }
		return $ext;
	}
	
	
	/**
	 * valid_mime
	 * Returns TRUE if the mimetype is suported
	 *
	 * @param	string
	 * @return	boolean
	 */
	private function valid_mime($mime)
	{
		return preg_match("@image/(gif|pjpeg|jpeg|png|x-png|bmp|x-ms-bmp)$@", $mime);
	}
	
	/**
	 * nameFile
	 * Returns the valid file name to use
	 *
	 * @param	string
	 * @return	string
	 */
	private function nameFile($path='', $extension, $method, $original_name='') {
		$path = trim($path);
		$path = $path == '' ? './' : $path;
		$method = (!check_value($method) || !in_array($method, array('original', 'random', 'mixed')) ? 'original' : $method);

		$original_name = unaccent_string($original_name);
		
		$original_name = !check_value($original_name) ? generateRandomString(rand(5,10)) : preg_replace('/\.[^.\s]{3,4}$/', '', $original_name); // Original name as it
		
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ".", ">", "/", "?");
				   
    	$original_name = trim(str_replace($strip, "", strip_tags($original_name)));
		$original_name = preg_replace('/[\s\.]+/', '_', $original_name); // Original name with spaces and dots changed to "_"
		
		if(strlen($original_name)==0) {
			$method = 'random';
		}

		function get_name_by_method($method, $extension, $original_name) {
			//$max_lenght = 255 - 1 - strlen($extension) - 5; // 246
			$max_lenght = 200;
			$original_name = substr($original_name, 0, 200);
			$original_name_lenght = strlen($original_name);
			switch($method){
				default: case 'original':
					$name = $original_name;
				break;
				case 'random':
					$name = generateRandomString(rand(5, 10));
				break;
				case 'mixed':
					if($original_name_lenght>=$max_lenght) {
						$name = substr($original_name, 0, $max_lenght-5);
					} else {
						$name = $original_name;
					}
					$name .= generateRandomString(5);
				break;
			}
			return $name.'.'.strtolower($extension);
		}
		
		$filename = $path.get_name_by_method($method, $extension, $original_name);
		
		while(file_exists($filename)) {
			if($method=='original') $method = 'mixed';
			$filename = $path.get_name_by_method($method, $extension, $original_name);
		}

		return $filename;
	}

	 
	 /**
	 * seted_value
	 * Checks the seted value of the current $value 
	 *
	 * @param	string
	 * @return	boolean
	 */
	 private function seted_value($value)
	 {
		 if(isset($value) and !empty($value)) {
			 return true;
		 }
	 }
	 
}

?>