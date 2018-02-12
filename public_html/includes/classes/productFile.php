<?
	class productFile_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY		
		const db_table_name = "product_files";
		const instance_class_name = "productFile";		
		const cont_class_name = "productFile_controller";
			
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
			
		}
/////////////////////////////////////////////////////////////////////////	

		public function get_by_prod_id($prod_id){
			if (!empty($prod_id)){
				return $this->get_by_column("prod_id", (int)$prod_id);
			}
			return null;
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
	
	class productFile extends base_data_object{
		
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
	
		
	class productFile_view extends view_abstract{
				
		public function __construct($file_path) {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "product_files";
			if (!empty($_GET['prod_id'])){
				$this->url_page_name .= "&prod_id=" . (int)$_GET['prod_id'];
			}
			
			$this->object_class_name = "productFile";
			
			parent::__construct($file_path);				
		}
		
		public function post_render_html(&$html){
			
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class productFile_controller extends controller_abstract{
				
		public function __construct() {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "product_files";
			$this->object_class_name = "productFile";
			$this->view_class_name = "productFile_view";
			
			parent::__construct();	
			
			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 		'dt' => 'id',	'path' => "id"),
				array( 'db' => 'name', 		'dt' => 'name',	'path' => "name"),
				array( 'db' => 'prod_id', 	'dt' => 'prod_id',	'path' => "prod_id")
				// array(
					// 'db'        => 'start_date',
					// 'dt'        => 4,
					// 'formatter' => function( $d, $row ) {
						// return date( 'jS M y', strtotime($d));
					// }
				// )
			);
			
			if (!empty($_GET['prod_id'])){
				$where = array();
				$where = array("columns" => array(array("col_name" => "prod_id", "condition" => "=", "value" => (int)$_GET['prod_id'])), "relation" => "AND");
				
				$this->dt_data_source_added_filter = $where;
			}
		}
		
		public function extend_request_processing(){
			
		}
		
		public function admin_insert_custom_data($new_object){
			
		}
		
		public function admin_edit_custom_data(){
			
		}
	}
?>