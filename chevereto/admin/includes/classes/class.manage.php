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
 * class.manage.php
 * This class is used to manage the files (edit and delete)
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @requires	- class.imageresize.php
 *				- chevereto functions.php
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class Manage {
	
	public $error;
	public $ok;
	public $dead;
	private $image_info;
	
	function __construct($array) {
		
		global $dB;
		$this->dB = $dB;
		
		$id = $array['id'];
		$action = $array['action'];
		
		$this->resize_width = intval($array['resize_width']);
		$this->rename_name = sanitize($array['rename_name'], false);

		if((!check_value($id) || !preg_match("/^\d+$/", $id))) {
			$this->error = "invalid image id";
		} else if(!check_value($action) || !preg_match("/^(rename|delete|resize)$/", $action)){
			$this->error = "invalid action";
		}
		
		if(!check_value($this->error)) {
			switch($action) {
				case 'rename':
					if(empty($this->rename_name)) $this->error = "invalid rename value";
				break;
				case 'resize':
					if(!preg_match("/^\d+$/", $array['resize_width'])) $this->error = "invalid resize width";
				break;
			}
		}
		
		if(!check_value($this->error)) {
			// Test connection
			if($this->dB->dead) {
				$this->dead = true;
				$this->error = $this->dB->error;
			} else {
				$this->id = $id;
				$this->action = $action;
				// get image info
				$this->image_info = $this->dB->image_info($id);
				if(!is_array($this->image_info)) { // Record?					
					$this->dead = true;
					$this->error = $this->dB->error;
				} else {
					$this->image_name = $this->image_info['image_name'];
					$this->image_type = $this->image_info['image_type'];
					$image_target = get_image_target($this->image_info);
					$this->image_target = $image_target['image_path'];
					$this->image_thumb_target = $image_target['image_thumb_path'];	
				}
			}
				
		} else {
			$this->dead = true;
		}
		
	}
	
	/**
	 * process
	 * Do the manage process according with the __constructor action
	 * 
	 * @access	public
	 * @return	array
	 */
	public function process() {
		
		switch($this->action) {
			case 'delete':
				$this->delete_image();
			break;
			case 'resize':
				$this->resize_image();
			break;
			case 'rename':
				$this->rename_image();
			break;
		}
		
		// Do the json return
		if(check_value($this->ok)) {
			$status = 200;
			$status_txt = $this->ok;
		} else {
			$status = 403;
			$status_txt = $this->error;
		}
		
		$return = array('status_code' => $status, 'status_txt' => $status_txt);
		
		if(check_value($this->extra_info)) {
			$return = array_merge($return, $this->extra_info);
		}
		
		return $return;
		
	}
	
	/**
	 * delete_image
	 * Detele the target image
	 * 
	 * @access	private
	 * @return	string
	 */
	private function delete_image()
	{		
		if($this->dB->delete_file($this->id)) {
			@unlink($this->image_target);
			@unlink($this->image_thumb_target);
			if(file_exists($this->image_target) || file_exists($this->image_thumb_target)) {
				$this->error = "can't delete target image";
			} else {
				$this->ok = 'image deleted';
			}
		} else {
			$this->error = $this->dB->error;
			return false;
		}
	}
	
	/**
	 * resize_image
	 * Resize the target image
	 * 
	 * @access	private
	 * @return	string
	 */
	private function resize_image()
	{
		//Check if the resize is needed
		if($this->resize_width == $this->image_info['image_width']) {
			$this->error = "target width is the same of the original image";
			return false;
		}
		// Call the resize class
		require_once(__CHV_PATH_CLASSES__.'class.imageresize.php');
		$this->ImageResize = new ImageResize($this->image_target, $this->image_target, $this->resize_width);
		if(check_value($this->ImageResize->error)) {
			$this->error = $this->ImageResize->error;
		} else {
			$imageinfo = get_info($this->image_target);
			if($this->dB->resize_image($this->id, $imageinfo['width'], $imageinfo['height'], $imageinfo['bytes'])) {
				$this->ok = $this->ImageResize->ok;
				$this->extra_info = array(
					'image_size'	=> format_bytes($imageinfo['bytes'], 0),
					'image_width'	=> $imageinfo['width'],
					'image_height'	=> $imageinfo['height'],
					'image_width_height' => $imageinfo['width'].'x'.$imageinfo['height']
				);
			} else {
				$this->error = 'resize failed (db)';
			}
		}
	}
	
	/**
	 * rename_image
	 * Rename the target image
	 * 
	 * @access	private
	 * @return	string
	 */
	private function rename_image()
	{
		
		$this->image_rename = str_replace($this->image_name, $this->rename_name, $this->image_target);
		$this->image_thumb_rename = str_replace($this->image_name, $this->rename_name, $this->image_thumb_target);
		
		if(file_exists($this->image_rename)) {
			$this->error = 'ERR_RN:E1'; // target filename already exists
			$this->extra_info = array(
				'image_filename' => $this->image_name.".".$this->image_type,
			);
			return false;
		}
		
		// Avoid Windows OS multibyte error on rename
		if(is_windows() && strlen($this->rename_name) !== strlen(urlencode($this->rename_name))) {
			$this->error = 'ERR_RN:E4'; // can't rename the image (multi-byte in Windows OS)
			return false;
		}
		
		rename($this->image_target, $this->image_rename);
		rename($this->image_thumb_target, $this->image_thumb_rename);
		
		if(file_exists($this->image_rename)) {
			if($this->dB->rename_file($this->id, $this->rename_name)) {				
				$this->ok = "image renamed";
				$this->extra_info = array(
					'image_url'			=> absolute_to_url($this->image_rename),
					'image_thumb_url'	=> absolute_to_url($this->image_thumb_rename),
					'image_name'		=> $this->rename_name,
					'image_type'		=> $this->image_type
				);
			} else {
				@rename($this->image_rename, $this->image_target);
				@rename($this->image_thumb_rename, $this->image_thumb_target);
				$this->error = 'ERR_RN:E3'; //can't rename the image (DB)"
			}
		} else {
			$this->error = 'ERR_RN:E2'; // can't rename the image (FS)
			$this->extra_info = array(
				'image_filename' => $this->image_name.".".$this->image_type,
			);
		}

	}
	
}