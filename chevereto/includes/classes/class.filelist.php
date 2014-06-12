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
 * class.filelist.php
 * This class is used to retrieve a file list from dB
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @requires	class.db.php
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class FileList {
	
	public $error;
	public $ok;
	public $filelist;
	
	function __construct($type, $order_sort, $limit, $keyword)
	{
		global $dB;
		$this->dB = $dB;
		
		// Test connection
		if($this->dB->dead) {
			$this->error = $this->dB->error;
			return false;
		} else {
			$this->filelist = $this->getlist($type, $order_sort, $limit, $keyword);
		}
		
	}
	
	/**
	 * getlist
	 * get the filelist according to the params
	 * 
	 * @param	string
	 * @return	mixed
	 */
	private function getlist($type, $order_sort, $limit, $keyword)
	{
		// Defaults
		if(!preg_match('/jpg|png|gif|all/', $type)) {
			$type = 'all';
		}
		if(!check_value($order_sort))	$order_sort = 'date_desc';
		if(!check_value($limit))		$limit = 50;
		
		// Type cleaning		
		if($type=='all') {
			$type_qry = '';
		} else {
			$types = explode(',', $type);
			$types_qry = array();
			foreach ($types as $type_qry) {
				if(!in_array($type_qry, array('jpg','png','gif','all'))) {
					continue; // Continue (and warn)...
				}
				$types_qry[] = "'".$type_qry."'";
			}
			$sql_types = implode(',', $types_qry);
            $type_qry = "WHERE image_type IN ($sql_types)";
		}
		
		// Order sort
		$ordersort = explode("_", $order_sort);
		$order = strtolower($ordersort[0]);
		$sort = strtolower($ordersort[1]);
		
		// Order clean
		if(!in_array($order, array('date','size'))) $order = "date";
		$order = 'image_'.$order;
		
		// Sort clean
		if(!in_array($sort, array('asc','desc'))) $sort = "desc";
		
		// Limits clean
		$limits = explode(',', $limit);
		$limits_qry = array();
		if(count($limits)>1) {
			for($i = 0; $i <= 1; ++$i) { // Allow only two limits
				$limits_qry[] = intval($limits[$i]);
			}
			$sql_limits = implode(',', $limits_qry);
		} else {
			$sql_limits = intval($limit);
		}
		
		$base_qry = "SELECT * FROM chv_images LEFT JOIN chv_storages ON chv_images.storage_id = chv_storages.storage_id $type_qry";
		$prepare = array();
		
		if(check_value($keyword)) {
			$prepare[':keyword'] = "%$keyword%";
			$keyword_qry = (check_value($type_qry)) ? "AND" : "WHERE"." chv_images.image_name LIKE :keyword";
		}
							
		$results = $this->dB->query_fetch("SELECT * FROM chv_images LEFT JOIN chv_storages ON chv_images.storage_id = chv_storages.storage_id $type_qry $keyword_qry ORDER BY $order $sort LIMIT $sql_limits", $prepare);
		
		if(is_array($results)) {
			// Now we got the results: Fix the result array in something actually usable
			$output = array();
			foreach($results as $result) {
				foreach($result as $filevalues) {
					$file_array = $result;
					$image_target = get_image_target($file_array);
					
					// if the image doesn't exits remove it from the dB
					if($this->dB->must_delete_image_record($file_array['image_id'], $image_target)) {
						unset($output['"'.$file_array['image_id'].'"']);
						continue 2;
					}
					
					// Recreate the thumb
					recreate_thumb($image_target);
					
					$filename = $file_array['image_name'].'.'.$file_array['image_type'];
					$file_array['image_viewer'] = __CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/'.encodeID($file_array['image_id']);
					$file_array['image_size'] = format_bytes($result['image_size'], 0);
					$file_array['image_url'] = absolute_to_url($image_target['image_path']);
					$file_array['image_thumb_url'] = absolute_to_url($image_target['image_thumb_path']);
					$file_array['image_shorturl'] = __CHV_BASE_URL__.encodeID($file_array['image_id']);
					$file_array['timestamp'] = strtotime($file_array['image_date']);
					$file_array['image_date'] = date('Y-m-d', $file_array['timestamp']);
				}
				$output['"'.$file_array['image_id'].'"'] = $file_array; // Defined as "1" instead of 1 to don't rely on browser json sort (Chrome)
				
			}
			return $output;
			
		} else {
			return $this->dB->error;
		}

	}

}

?>