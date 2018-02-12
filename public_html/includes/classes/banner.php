<?
	class banner_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY		
		const db_table_name = "banners";
		const instance_class_name = "banner";		
		const cont_class_name = "banner_controller";
			
		protected static $factory = null;
		protected static $instances;		
		
		protected function __construct() {			
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}			
/////////////////////////////////////////////////////////////////////////		
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){
			
		}
		
		protected function new_object_created(&$new_object){
			
		}
		
		public function dt_custom_search($columns){
			$ret = array();
			$sql = "SELECT `id` FROM `".self::db_table_name."` WHERE 1 ";
			
			if (!empty($_GET['custom_search'])){
				foreach ($_GET['custom_search'] as $field){
					switch ($field['name']){
						case "name":
							if (!emptY($field['value']))
								$sql .= " AND `name` = '" .clear_string($field['value']) . "' ";
						break;
						case "parent_cat_id":
							if (!emptY($field['value']))
								$sql .= " AND `parent_cat_id` = '" .clear_string($field['value']) . "' ";
						break;
					}
				}
			}
			
			$result = $this->con->fetchData($sql);			
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$ret[] = $row['id'];
				}
			}
			
			return $ret;
		}
/////////////////////////////////////////////////////////////////////////	

		
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
	
	class banner extends base_data_object{
		
		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);				
		}
		
		protected function extend_deletion(){
			
		}
		
		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class banner_view extends view_abstract{
				
		public function __construct($file_path) {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "banners";
			$this->object_class_name = "banner";
			
			parent::__construct($file_path);				
		}
		
		public function post_render_html(&$html){
			
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class banner_controller extends controller_abstract{
				
		public function __construct() {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "banners";
			$this->object_class_name = "banner";
			$this->view_class_name = "banner_view";
			
			parent::__construct();	
			
			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 		'dt' => 'id',		'path' => "id"),
				array( 'db' => 'name', 		'dt' => 'name',		'path' => "name"),
				array( 'db' => 'image_trans_id', 	'dt' => 'image',	'path' => "image")
			);
		}
		
		public function extend_request_processing(){
			
		}
		
		public function admin_insert_custom_data($new_object){
			
		}
		
		public function admin_edit_custom_data(){
			
		}
	}
?>