<?
	class language extends base_factory{
		
		public static $current_lang;
		public static $default_lang_id;
		
		protected static $factory = null;
		protected static $instances;
		
		const instance_class_name = "language_object";
		const db_table_name = "languages";
		
		protected function __construct() {			
			parent::__construct(self::instance_class_name, self::db_table_name);
			
			if (!emptY($_SESSION['current_lang']))
				self::$current_lang = $_SESSION['current_lang'];
			else
				self::$current_lang = self::$default_lang_id;
		}
		
		protected function extend_instance(&$instance){
			if (!empty($instance->is_default)){
				self::$default_lang_id = $instance->id; // set default lang
				
				if (empty(self::$current_lang)){
					self::$current_lang = self::$default_lang_id;
				}
			}
		}
		
		protected function new_object_created(&$new_object){
		
		}
		
		public static function init(){
			$factory = self::get_factory();
			$langs = $factory->get_all();
			
			if (!empty($_GET['lang']) && !empty(self::$instances['by_id'][(int)$_GET['lang']])){
				self::$current_lang = (int)$_GET['lang'];
				$_SESSION['current_lang'] = self::$current_lang;
			}
			
			return $langs;
		}
		
		public static function set_lang($lang_id){
			self::$current_lang = $lang_id;
		}
		
		public function dt_custom_search($columns){
			
		}
	}
	
	class language_object extends base_data_object{
		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);				
		}
		
		protected function extend_deletion(){
		
		}
		
	}
?>