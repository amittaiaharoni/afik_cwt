<?
	class details2details_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY		
		const db_table_name = "option_details2option_details";
		const instance_class_name = "details2details";		
		const cont_class_name = "details2details_controller";
			
		protected static $factory = null;
		protected static $instances;		
		
		protected function __construct() {			
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}			
/////////////////////////////////////////////////////////////////////////		
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){
			$instance->detail1 = $this->data->optionDetail->get_by_id($instance->detail_id_1);
			$instance->detail2 = $this->data->optionDetail->get_by_id($instance->detail_id_2);
		}
		
		protected function new_object_created(&$new_object){
			
		}
		
		public function dt_custom_search($columns){
			
		}
/////////////////////////////////////////////////////////////////////////		
		public function get_linked_detail_data($detail_id_1, $detail_id_2, $prod_id){
			$res = null;
			$sql = "SELECT * FROM `".$this->db_table_name."` WHERE `detail_id_1` = '".(int)$detail_id_1."' AND `detail_id_2` = '".(int)$detail_id_2."' AND `prod_id` = '".(int)$prod_id."'";
				// error_log($sql);
			$result = $this->con->fetchData($sql);
			if (!empty($result['rows'])){			
				$res = $this->get_by_id($result['rows'][0]['id']);				
			}
			return $res;
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
	
	class details2details extends base_data_object{
		
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
	
		
	class details2details_view extends view_abstract{
				
		public function __construct($file_path) {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "products_to_options";
			$this->object_class_name = "details2details";
			
			parent::__construct($file_path);				
		}
		
		public function post_render_html(&$html){
			
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class details2details_controller extends controller_abstract{
				
		public function __construct() {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "products_to_options";
			$this->object_class_name = "details2details";
			$this->view_class_name = "details2details_view";
			
			parent::__construct();	
			
			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(); // there is no dt
		}
		
		public function extend_request_processing(){
			
		}
		
		public function admin_insert_custom_data($new_object){
			
		}
		
		public function admin_edit_custom_data(){
			
		}
	}
?>