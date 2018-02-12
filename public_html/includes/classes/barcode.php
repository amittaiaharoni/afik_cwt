<?
	class barcode_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY		
		const db_table_name = "product_barcods";
		const instance_class_name = "barcode";		
		const cont_class_name = "barcode_controller";
			
		protected static $factory = null;
		protected static $instances;		
		
		// protected $constant_filters = array(
										// array("col_name" => "price", "condition" => ">", "value" => "0", "relation" => "AND")										
									// );
									
		protected function __construct() {			
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}			
/////////////////////////////////////////////////////////////////////////		
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){
			// if ($instance->price == 0){
				// $prod = $this->data->product->get_by_id($instance->prod_id);
				// if (!emptY($prod)){
					// $instance->price = $prod->price;
				// }
			// }
		}
		
		protected function new_object_created(&$new_object){
			
		}
		
		public function dt_custom_search($columns){
			
		}
		
		public function get_by_prod_id($prod_id){
			if (!empty($prod_id)){
				return $this->get_by_column("prod_id", (int)$prod_id);
			}
			return null;
		}
/////////////////////////////////////////////////////////////////////////	
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
	
	class barcode extends base_data_object{
		
		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);				
		}
		
		protected function extend_deletion(){
			
		}
		
		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}
		
		public function get_by_dimension(){
			if(!empty($_POST) && isset($_POST['front'])){
				foreach($_POST as $key => $val){
					if(!empty($val) && $this->{$key} <= $val){
						return true;
					}
				}
			}
			return false;
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class barcode_view extends view_abstract{
				
		public function __construct($file_path) {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "barcodes";
			if (!empty($_GET['prod_id'])){
				$this->url_page_name .= "&prod_id=" . (int)$_GET['prod_id'];
			}
			$this->object_class_name = "barcode";
			
			parent::__construct($file_path);				
		}
		
		public function post_render_html(&$html){
			
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class barcode_controller extends controller_abstract{
				
		public function __construct() {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "barcodes";
			$this->object_class_name = "barcode";
			$this->view_class_name = "barcode_view";
			
			parent::__construct();	
			
			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
				array( 'db' => 'prod_id', 		'dt' => 'prod_id',	'path' => "prod_id"),
				array( 'db' => 'barcode', 		'dt' => 'barcode',		'path' => "barcode"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',			'path' => "name"),
				array( 'db' => 'front', 		'dt' => 'front',		'path' => "front"),
				array( 'db' => 'depth', 		'dt' => 'depth',		'path' => "depth"),
				array( 'db' => 'height', 		'dt' => 'height',		'path' => "height"),
				array( 'db' => 'elec_phase', 		'dt' => 'elec_phase',		'path' => "elec_phase"),
				array( 'db' => 'desc_trans_id', 		'dt' => 'desc',		'path' => "desc"),
				array( 'db' => 'file', 		'dt' => 'file',		'path' => "file")
						
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