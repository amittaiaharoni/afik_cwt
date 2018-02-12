<?
	class site_config{
		
		protected static $defaults	= array();
		protected static $config 	= array();
		
		public static function add_default_value($key, $value, $type = "text", $allow_edit = "true"){			
			if ($value === true || $value === false)
				$type = "bool";
			self::$defaults[$key] = array( "key" => $key, "value" => $value, "type" => $type, "allow_edit" => $allow_edit);
		}
		
		public static function get_all(){
			$ret = array();
			
			$sql = "SELECT * FROM `site_config` ORDER BY `id` ASC";
			
			if ($res = db_con::get_con()->fetchData($sql)){				
				if (!empty($res['rows'])){
					foreach ($res['rows'] as $row){
						$ret[$row['key']] = $row;
					}
					
					return $ret;
				}
			}
			
			return array();
		}
		
		public static function get_config_row($key){
			$sql = "SELECT * FROM `site_config` WHERE `key` = '" . clear_string($key) . "'";
			
			if ($res = db_con::get_con()->fetchData($sql)){				
				if (!empty($res['rows'][0])){
					return $res['rows'][0];
				}
			}
			
			return "";
		}
		
		public static function get_default_value($key){
			if (!empty(self::$defaults[$key])){
				return self::$defaults[$key]['value'];
			}
			return "";
		}
		
		public static function get_value($key){
			if (empty(self::$config))
				self::$config = self::get_all();
			
			if (!empty(self::$config) && !empty(self::$config[$key])){
				return self::$config[$key]['value'];
			}
			else if (!empty(self::$defaults[$key])){ // there is no value in db, check if it's in the defaults			
				if (self::$defaults[$key]['allow_edit']){ // if its a default that is open for edit, add it to the table
					$insert_sql =  "INSERT INTO `site_config`(`key`, `value`, `type`, `name`) 
									VALUES 
									(
										'".clear_string($key)."',
										'".clear_string(self::$defaults[$key]['value'])."',
										'".clear_string(self::$defaults[$key]['type'])."',
										'".clear_string($key)."'
									)";
					
					db_con::get_con()->query($insert_sql, true);
				}
				
				return self::$defaults[$key]['value'];
			}
			
			return "";
		}
		
		public static function update_values(){
			if (!empty($_POST) || !empty($_FILES)){
				$sql = "SELECT GROUP_CONCAT(`key`,',') as 'keys' FROM `site_config` WHERE 1";
			
				if ($res = db_con::get_con()->fetchData($sql)){	
					if (!empty($res['rows'][0])){
						$keys = $res['rows'][0]['keys'];
						$keys = explode(",", $keys);
						
						if (!empty($_POST['action'])){
							
							if ($_POST['action'] == "admin_config_delete_image"){
								if (!empty($_POST['key']) && in_array($_POST['key'], $keys)){
									$row = self::get_config_row($_POST['key']);
									if (!empty($row)){ 
										remove_image_with_thumb($row['value']);
										
										$sql = "UPDATE `site_config` SET `value`='' WHERE `key` = '".clear_string($_POST['key'])."'";
										db_con::get_con()->query($sql, true);
									}
								}
							}
						}
						else{
							foreach ($keys as $key){
								if (isset($_POST[$key])){
									$row = self::get_config_row($key);
									
									if (!empty($row)){ 
										$value = "";
										if ($row['type'] == "text" || $row['type'] == "bool"){
											$value = clear_string($_POST[$key]);
											
											$sql = "UPDATE `site_config` SET `value`='".$value."' WHERE `key` = '".$key."'";
											db_con::get_con()->query($sql, true);
										}
									}							
								}
								else if (!empty($_FILES) && !empty($_FILES[$key])){
									$row = self::get_config_row($key);
									
									if (!empty($row)){ 
										if ($row['type'] == "file"){
											$value = "";
											
											$is_image = is_image($key);
											if ($is_image)								
												$value = upload_with_thumb($key, 150);
											else
												$value = upload_file($key);
												
											if (!empty($value)){
												$value = clear_string($value);
												
												$sql = "UPDATE `site_config` SET `value`='".$value."' WHERE `key` = '".$key."'";
												db_con::get_con()->query($sql, true);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
?>