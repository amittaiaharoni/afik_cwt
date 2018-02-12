<?
	class translator extends base_factory{
		
		const instance_class_name = "translator_object";
		const db_table_name = "translations";		
		
		protected static $factory = null;
		protected static $instances;
		
		protected function __construct() {			
			parent::__construct(self::instance_class_name, self::db_table_name);
		}
		
		protected function extend_instance(&$instance){
			
		}
		
		protected function new_object_created(&$new_object){
		
		}
		
		public static function get_translator($key, $lang = null){			
			$factory = self::get_factory();
			
			$res = null;
			$is_default_loaded = false;
			if (empty($lang))
				$lang = language::$current_lang;
				
			if (!empty($key)){
				// check if already loaded and return if exist
				if (!empty(self::$instances['by_lang'][$lang][$key]))
					return self::$instances['by_lang'][$lang][$key];		
											
				$sql = "SELECT * FROM `".self::db_table_name."` WHERE `key` = '".$key."' AND `lang` = '".$lang."'";								
				$result = $factory->con->fetchData($sql);
				
				if (empty($result['rows'])){ // get the default lang					
					$sql = "SELECT * FROM `".self::db_table_name."` WHERE `key` = '".$key."' AND `lang` = '".language::$default_lang_id."'";
					$result = $factory->con->fetchData($sql);
					
					$is_default_loaded = true;										
				}
				
				if (!empty($result['rows'])){
					$row = $result['rows'][0];
					// check if already loaded and return if exist
					if (!emptY(self::$instances['by_id'][$row['id']])){
						return self::$instances['by_id'][$row['id']];
					}
					else{
						$instance = null;
						if ($is_default_loaded){ // create new object with the default text
							$instance = self::$factory->add_new(
								array(
									"key" => $key, 
									"value" => $row["value"], 
									"lang" => $lang
								));
						}
						else{// get instance							
							$instance = $factory->create_instance($row, $result['fields_info']);							
						}
						
						if (!empty($instance)){
							self::$instances['by_id'][$row['id']] = $instance;						
							self::$instances['by_lang'][$lang][$key] = &self::$instances['by_id'][$row['id']]; // store instance by reference
							return $instance; 							
						}
					}					
				}
				else{ // there is no translation at all, add emty string
					$instance = self::$factory->add_new(
						array(
							"key" => $key, 
							"value" => '', 
							"lang" => language::$default_lang_id
						));
					if (!empty($instance)){
						self::$instances['by_id'][$instance->id] = $instance;						
						self::$instances['by_lang'][$lang][$key] = &self::$instances['by_id'][$instance->id]; // store instance by reference
						return $instance; 							
					}
				}
			}
			return $res;
		}
		
		public function dt_custom_search($columns){
			
		}
	}
	
	class translator_object extends base_data_object{
		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);				
		}
		
		protected function extend_deletion(){
		
		}
		
		public function __toString() {
			if (!isset($this->data_fields['value']))
				return "";
			return $this->data_fields['value'];
		}
		
		public function to_json() {
			if (!isset($this->data_fields['value']))
				return '""';
			return json_encode($this->data_fields['value']);
		}
		
		public function to_string() {
			if (!isset($this->data_fields['value']))
				return "";
			return $this->data_fields['value'];
		}
	}
?>