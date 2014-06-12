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
 * class.db.php
 * This class is used to handle the PDO dB object
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @requires	PDO <http://php.net/manual/book.pdo.php>
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 * 
 */

class dB {
	
	public $error;
	public $ok;
	public $dead;
	public $image_delete_hash;
	
	// Constructor --> Connect
	function __construct()
	{
		try {
			
			$pdo_connect = 'mysql:host='.__CHV_DB_HOST__.';dbname='.__CHV_DB_NAME__;
			if(check_value(__CHV_DB_PORT__)) {
				$pdo_connect .= ';port='.__CHV_DB_PORT__;
			}
			$this->db = new PDO($pdo_connect, __CHV_DB_USER__, __CHV_DB_PASS__, array(PDO::ATTR_TIMEOUT => 30, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
			
			// Set the chv_tables
			@define('__CHV_TABLE_IMAGES__', 'chv_images');
			@define('__CHV_TABLE_STORAGES__', 'chv_storages');
			@define('__CHV_TABLE_OPTIONS__', 'chv_options');
			@define('__CHV_TABLE_INFO__', 'chv_info');
			
			$chv_tables = array(__CHV_TABLE_IMAGES__, __CHV_TABLE_STORAGES__, __CHV_TABLE_INFO__);
			
			// Get the db tables
			$chv_db_tables = array();
			$db_tables = $this->db->query("SHOW TABLES LIKE 'chv_%';");
			while ($db_table = $db_tables->fetch(PDO::FETCH_NUM)) {
				$chv_db_tables[] = $db_table[0];
			}
			$db_tables->closeCursor();
			
			// Sync PHP and DB Timezone
			$DT = new DateTime();
			$offset = $DT->getOffset();
			$offsetHours = round(abs($offset) / 3600);
			$offsetMinutes = round((abs($offset) - $offsetHours * 3600) / 60);
			$offset  = ($offset < 0 ? '-' : '+').(strlen($offsetHours) < 2 ? '0' : '').$offsetHours.':'.(strlen($offsetMinutes) < 2 ? '0' : '').$offsetMinutes;
			$this->db->exec("SET time_zone = '{$offset}'");
			
			/*** Define the insert row statements ***/
			@define('__INSERT_ROW_STORAGES_OLD__', "INSERT INTO ".__CHV_TABLE_STORAGES__." (storage_id, storage_type) VALUES (1, 'old');");
			@define('__INSERT_ROW_STORAGES_DIRECT__', "INSERT INTO ".__CHV_TABLE_STORAGES__." (storage_id, storage_type) VALUES (2, 'direct');");
			
			/*** Define the create table statements ***/
			// image_delete_hash @since 2.3
			@define('__CREATE_TABLE_IMAGES__', "DROP TABLE IF EXISTS ".__CHV_TABLE_IMAGES__.";
					CREATE TABLE ".__CHV_TABLE_IMAGES__." (
						image_id bigint(20) NOT NULL AUTO_INCREMENT,
						image_name varchar(200) NOT NULL,
						image_type varchar(200) NOT NULL,
						image_size int(11) NOT NULL,
						image_width int(11) NOT NULL,
						image_height int(11) NOT NULL,
						image_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						uploader_ip varchar(200) NOT NULL,
						storage_id int(11) DEFAULT NULL,
						image_delete_hash varchar(200) NOT NULL,
						PRIMARY KEY (image_id)
					) DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;"
			);
			@define('__CREATE_TABLE_STORAGES__', "DROP TABLE IF EXISTS ".__CHV_TABLE_STORAGES__.";
					CREATE TABLE ".__CHV_TABLE_STORAGES__." (
						storage_id int(11) NOT NULL AUTO_INCREMENT,
						storage_type varchar(200) NOT NULL,
						PRIMARY KEY (storage_id)
					) DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;"
					.__INSERT_ROW_STORAGES_OLD__
					.__INSERT_ROW_STORAGES_DIRECT__
			);
			@define('__CREATE_TABLE_OPTIONS__', "DROP TABLE IF EXISTS ".__CHV_TABLE_OPTIONS__.";
					CREATE TABLE ".__CHV_TABLE_OPTIONS__." (
						option_id int(11) NOT NULL AUTO_INCREMENT,
						option_key varchar(200) NOT NULL,
						option_value varchar(200) NOT NULL,
						PRIMARY KEY (option_id),
						UNIQUE KEY option_key (option_key)
					) DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
					INSERT INTO ".__CHV_TABLE_OPTIONS__." (option_id, option_key, option_value) VALUES (1, 'maintenance', '0');"
			);
			@define('__CREATE_TABLE_INFO__', "DROP TABLE IF EXISTS ".__CHV_TABLE_INFO__.";
					CREATE TABLE ".__CHV_TABLE_INFO__." (
						info_id int(11) NOT NULL AUTO_INCREMENT,
						info_key varchar(200) NOT NULL,
						info_value varchar(200) NOT NULL,
						PRIMARY KEY (info_id),
						UNIQUE KEY info_key (info_key)
					) DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
					INSERT INTO ".__CHV_TABLE_INFO__." (info_id, info_key, info_value) VALUES (1, 'version', '".__CHV_VERSION__."');"
			);
			
			// Create the database tables?
			if(!in_array(__CHV_TABLE_IMAGES__, $chv_db_tables)) {
				// Brand new installation
				$new_install = $this->db->prepare(__CREATE_TABLE_IMAGES__.__CREATE_TABLE_STORAGES__.__CREATE_TABLE_OPTIONS__.__CREATE_TABLE_INFO__);
				$new_install->execute();
				$new_install->closeCursor();
			} else {
				// Fix non existant tables
				$create_tables = '';
				if(!in_array(__CHV_TABLE_STORAGES__, $chv_db_tables))	$create_tables .= __CREATE_TABLE_STORAGES__;
				if(!in_array(__CHV_TABLE_OPTIONS__, $chv_db_tables))	$create_tables .= __CREATE_TABLE_OPTIONS__;
				if(!in_array(__CHV_TABLE_INFO__, $chv_db_tables))		$create_tables .= __CREATE_TABLE_INFO__;
				if(check_value($create_tables)) {
					$fix_non_existant = $this->db->prepare($create_tables);
					$fix_non_existant->execute();
					$fix_non_existant->closeCursor();
				}
			}
			
			// Update the dB structure to match the current version
			$images_table_colums = array();
			$image_table_structure = $this->db->query("SHOW COLUMNS FROM ".__CHV_TABLE_IMAGES__.";");
			while ($image_table_colum = $image_table_structure->fetch(PDO::FETCH_NUM)) {
				$images_table_colums[] = $image_table_colum[0];
			}
			if(!in_array('image_delete_hash', $images_table_colums)) {
				$fix_non_existant_columns = $this->db->prepare("ALTER TABLE ".__CHV_TABLE_IMAGES__." ADD `image_delete_hash` VARCHAR(200) NOT NULL;");
				$fix_non_existant_columns->execute();
				$fix_non_existant_columns->closeCursor();
			}		
			
			// Fix the dB defacto rows?
			$storage_row_insert = '';
			$storage_rows = $this->query_fetch("SELECT storage_type FROM ".__CHV_TABLE_STORAGES__." WHERE storage_id=? OR storage_id=?", array(1,2));
			if(!check_value($storage_rows[0]['storage_type'])) $storage_row_insert .= __INSERT_ROW_STORAGES_OLD__;
			if(!check_value($storage_rows[1]['storage_type'])) $storage_row_insert .= __INSERT_ROW_STORAGES_DIRECT__;
			if(check_value($storage_row_insert)) {
				$fix_storage_rows = $this->db->prepare($storage_row_insert);
				$fix_storage_rows->execute();
				$fix_storage_rows->closeCursor();
			}
			
		} catch (PDOException $e) {
			$this->dead = true;
			$this->error = "PDOException: ".$e->getMessage();
		}
		
	}
	
	
	/**
	 * disconnect
	 * Close the sql instance
	 * 
	 * @param	string
	 * @return	null
	 */
	/*public function disconnect()
	{
		$this->db = null;
	}*/
	
	
	/**
	 * query
	 * Performs sql query for statements that doesn't return data (INSERT/UPDATE/DELETE)
	 * 
	 * @param	string
	 * @return	bool
	 */
	public function query($sql, $array='')
	{
		if(!is_array($array)) $array = array($array);
		$query = $this->db->prepare($sql);
		if (!$query) {
			$this->error = $this->trow_error();
			$query->closeCursor();
			return false;
		} else {
			if($query->execute($array)) {
				$query->closeCursor();
				return true;
			} else {
				$this->error = $this->trow_error($query);
				return false;
			}
		}
	}
	
	
	/**
	 * query_fetch
	 * Performs a query + fetch in the result of an assosiative array
	 * 
	 * @param	string
	 * @return	array / null / FALSE on failure
	 */
	public function query_fetch($sql, $array='')
	{
		$params = (!is_array($array)) ? array($array) : $array;
		if(!check_value($array)) $params = NULL;
		$query = $this->db->prepare($sql);
		if (!$query) {
			$this->error = $this->trow_error();
			return false;
		} else {
			if($query->execute($params)) {
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				$query->closeCursor();
				return (check_value($result)) ? $result : NULL;
			} else {
				$this->error = $this->trow_error($query);
				return false;
			}
		}
	}
	
	
	/**
	 * query_fetch_single
	 * Performs a query + fetch in the result of an assosiative array for a single record
	 * 
	 * @param	string
	 * @return	array / null / FALSE on failure
	 */
	public function query_fetch_single($sql, $array='')
	{
		$result = $this->query_fetch($sql, $array);
		return (isset($result[0])) ? $result[0] : NULL;
	}
	
	
	/**
	 * last_insert_id
	 * Gets the last inserted id into the dB
	 * 
	 * @param	string
	 * @return	string
	 */
	public function last_insert_id() {
		return $this->db->lastInsertId();
	}
	
	
	/**
	 * trow_error
	 * Returns a dB status error
	 * 
	 * @param	string
	 * @return	string
	 */
	private function trow_error($state='')
	{
		if(!check_value($state)) {
			$error = $this->db->errorInfo();
		} else {
			$error = $state->errorInfo();
		}
		return '[SQL '.$error[0].'] ['.$this->db->getAttribute(PDO::ATTR_DRIVER_NAME).' '.$error[1].'] > '.$error[2];
	}
	

	/**
	 * insert_file
	 * Inserts a file into the dB
	 * 
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function insert_file($file_array)
	{
		$image_date  = (isset($file_array['image_date'])) ? $file_array['image_date'] : date('Y-m-d H:i:s', time());
		$uploader_ip = (isset($file_array['uploader_ip'])) ? $file_array['uploader_ip'] : $_SERVER['REMOTE_ADDR'];
		$storage_id  = (isset($file_array['storage_id'])) ? $file_array['storage_id'] : NULL; // Null storage --> default
		$this->image_delete_hash = $this->generate_delete_hash();

		$file_db_array = array(
			$file_array['image_name'],
			$file_array['image_type'],
			$file_array['image_bytes'],
			$file_array['image_width'],
			$file_array['image_height'],
			$image_date,
			$uploader_ip,
			$storage_id,
			$this->image_delete_hash
		);

		$query = $this->query('INSERT INTO chv_images (image_name, image_type, image_size, image_width, image_height, image_date, uploader_ip, storage_id, image_delete_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', $file_db_array);
		if($query) {
			return true;
		} else {
			$this->error = 'Error uploading (dB)';
			return $this->error;
		}
	}
	
	
	/**
	 * delete_file
	 * Detele a file from the dB
	 * 
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function delete_file($id)
	{	
		$query = $this->query('DELETE FROM chv_images WHERE image_id=?', array($id));
		return ($query) ? true : $this->error;
	}
	
	
	/**
	 * image_info
	 * get the image info from the dB
	 * 
	 * @access	public
	 * @param	string
	 * @return	array
	 */
	public function image_info($id)
	{
		$query = 'SELECT * FROM chv_images LEFT JOIN chv_storages ON chv_images.storage_id = chv_storages.storage_id WHERE ';
		
		// Legacy request (file.ext)
		if(preg_match("/(\w*\.)(jpg|png|gif)$/", $id)) {
			$target = explode(".", $id);
			$query .= 'chv_images.storage_id=1 AND chv_images.image_name=? AND chv_images.image_type=?';
			$query_array = array($target[0], $target[1]);
		} else {
			$query .=  'chv_images.image_id=?';
			$query_array = array($id);
		}
		
		$imageDB = $this->query_fetch_single($query, $query_array);
		
		if(is_array($imageDB)) {
			$id = $imageDB['image_id'];
			$id_public = encodeID($id);
			$image_target = get_image_target($imageDB);
			// if the image doesn't exits remove it from the dB
			if($this->must_delete_image_record($id, $image_target)) {
				$this->dead = true;
				$this->error = "file doesn't exists";
				return false;
			} else {
				// Recreate the thumb
				recreate_thumb($image_target);
				// Fix the dB values just in case...
				$imageDB['image_width'] = intval($imageDB['image_width']);
				$imageDB['image_height'] = intval($imageDB['image_height']);
				$imageDB['image_size'] = intval($imageDB['image_size']);
				// Populate the array
				$populate = array(
					'image_filename'	=> $imageDB['image_name'].'.'.$imageDB['image_type'],
					'image_id_public'	=> $id_public,
					'image_path'		=> $image_target['image_path'],
					'image_url'			=> absolute_to_url($image_target['image_path']),
					'image_attr'		=> 'width="'.$imageDB['image_width'].'" height="'.$imageDB['image_height'].'"',
					'image_bytes'		=> intval($imageDB['image_size']),
					'image_size'		=> format_bytes($imageDB['image_size']),
					'image_thumb_url'	=> absolute_to_url($image_target['image_thumb_path']),
					'image_thumb_path'	=> $image_target['image_thumb_path'],
					'image_thumb_width'	=> chevereto_config('thumb_width'),
					'image_thumb_height'=> chevereto_config('thumb_height'),
					'image_viewer'		=> __CHV_BASE_URL__.__CHV_VIRTUALFOLDER_IMAGE__.'/'.$id_public,
					'image_shorturl'	=> __CHV_BASE_URL__.$id_public,
					'image_delete_url'	=> __CHV_BASE_URL__.'delete/image/'.$id_public.'/'.$imageDB['image_delete_hash'],
					'image_delete_confirm_url' => __CHV_BASE_URL__.'delete-confirm/image/'.$id_public.'/'.$imageDB['image_delete_hash']
				);
				return array_merge($imageDB, $populate);
			}
		} else {
			$this->error = 'invalid id record ('.$id.')';
			return false;
		}
	}
	
	
	/**
	 * must_delete_image_record
	 * Delete the image from the dB and the thumb if the image doesn't exists in the filesystem
	 * 
	 * @access	public
	 * @param	mixed
	 * @return	bool
	 */
	public function must_delete_image_record($id, $image_array)
	{
		if(!file_exists($image_array["image_path"])) {
			@unlink($image_array["image_thumb_path"]);
			$this->delete_file($id);
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * rename_file
	 * Rename a file to the dB
	 * 
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function rename_file($id, $newname)
	{
		$query = $this->query('UPDATE chv_images SET image_name=?, image_date=image_date WHERE image_id=?', array($newname, $id));
		return ($query) ? true : $this->error;
	}
	
	
	/**
	 * resize_image
	 * Update image info upon resize to the dB
	 * 
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function resize_image($id, $width, $height, $size_bytes)
	{	
		$query = $this->query('UPDATE chv_images SET image_width=?, image_height=?, image_size=?, image_date=image_date WHERE image_id=?', array($width, $height, $size_bytes, $id));
		return ($query) ? true : $this->error;
	}
	
	
	private function get_key($table, $key) {
		switch($table) {
			case 'chv_options':
				$where = 'option_key';
				$value = 'option_value';
			break;
			case 'chv_info':
				$where = 'info_key';
				$value = 'info_value';
			break;
		}
		if(!check_value($where) && !check_value($value)) {
			return false;
		}
		$keyDB = $this->query_fetch_single('SELECT * FROM '.$table.' WHERE '.$where.'=? LIMIT 1', $key);
		if(is_array($keyDB)) {
			if(is_numeric($keyDB[$value])) {
				return ($keyDB[$value]==1) ? true : false;
			} else {
				return $keyDB[$value];
			}
		} else {
			$this->error = 'invalid key';
			return false;
		}
	}
	
	
	/**
	 * get_option
	 * Get the target option key from the dB
	 * 
	 * @param	string
	 * @return	mixed
	 */
	public function get_option($option_key)
	{
		return $this->get_key('chv_options', $option_key);
	}
	
	
	/**
	 * get_info
	 * Get the target info key from the dB
	 * 
	 * @param	string
	 * @return	mixed
	 */
	public function get_info($info_key)
	{
		return $this->get_key('info_key', $info_key);
	}
	
	/**
	 * generate_delete_hash
	 * Generates a random hash to be used as the delete hash
	 *
	 * @since	2.3
	 */
	private function generate_delete_hash() {
		 return preg_replace('/\.|\/|&|\?/', '', crypt(generateRandomString(16), generateRandomString(8)).crypt(generateRandomString(16), generateRandomString(8)));;
	}

}

?>