<?
	class sale_per_product_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY		
		const db_table_name = "sale_per_product";
		const instance_class_name = "sale_per_product";		
		const cont_class_name = "sale_per_product_controller";
			
		protected static $factory = null;
		protected static $instances;		
		
		protected function __construct() {			
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}			
/////////////////////////////////////////////////////////////////////////		
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){
			$categories_array = array();

			$con = db_con::get_con();
			$cat_sql = "SELECT * FROM `cats2sale_per_product` WHERE `sale_per_product_id` = '" . $instance->id . "'";
			$result = $this->con->fetchData($cat_sql);

			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$categories_array[] = $row['cat_id'];
				}
			}
			$instance->categories = $categories_array;
		}
		
		protected function new_object_created(&$new_object){
		
		}
		
		public function dt_custom_search($columns){
			$ret = array();
			$from = " `".self::db_table_name."` as main_table ";
			$where = " 1 " ;

			if (!empty($_GET['custom_search'])){
				foreach ($_GET['custom_search'] as $field){
					switch ($field['name']){
						case "cat_id":
							if (!emptY($field['value'])){
								$from .= " LEFT JOIN `cats2sale_per_product` as p2c ON main_table.`id` = p2c.`sale_per_product_id` ";
								$where .= " AND p2c.`cat_id` = '" .clear_string($field['value']) . "' ";
							}
						break;
					}
				}
			}

			$sql = "SELECT main_table.`id` FROM $from WHERE $where ";

			$result = $this->con->fetchData($sql);
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$ret[] = $row['id'];
				}
			}
			if (empty($ret))
				return array("-1");
			return $ret;
		}
/////////////////////////////////////////////////////////////////////////	
		

	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
	
	class sale_per_product extends base_data_object{
		
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
	
		
	class sale_per_product_view extends view_abstract{
				
		public function __construct($file_path) {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "sale_per_product";
			$this->object_class_name = "sale_per_product";
			
			parent::__construct($file_path);				
		}
		
		public function post_render_html(&$html){
			
		}
		protected function is_cat_selected($sale_per_product_id){
             if (!empty($this->edited_object) && !empty($this->edited_object->categories) && in_array($sale_per_product_id,$this->edited_object->categories)){
                 return "selected";
             }
             return "";
        }
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class sale_per_product_controller extends controller_abstract{
				
		public function __construct() {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "sale_per_product";
			$this->object_class_name = "sale_per_product";
			$this->view_class_name = "sale_per_product_view";
			
			parent::__construct();	
			
			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 		'dt' => 'id',		'path' => "id"),
				array( 'db' => 'is_active', 		'dt' => 'is_active',		'path' => "is_active",
				
						'formatter' => 
							function( $value, $object ) {
								global $data;
								
								switch ($object->type){
									case "0":
										return $value . "%";	
									break;
									case "1":
										return $value . " &#8362;";	
									break;
								}								
								return 	$value;
							}
						)
				// array(
					// 'db'        => 'start_date',
					// 'dt'        => 4,
					// 'formatter' => function( $d, $row ) {
						// return date( 'jS M y', strtotime($d));
					// }
				// )
			);
		}
		
		protected function admin_sale_per_product_to_cats($sale_per_product_id){
			$con = db_con::get_con();

			// LINK TO CATEGORY
			$delete_sql = "DELETE FROM `cats2sale_per_product` WHERE `sale_per_product_id` = '" . $sale_per_product_id . "'";
			$con->query($delete_sql, true); // remove old rows

			if (!empty($_POST['cat_ids'])){ // add new rows
				foreach ($_POST['cat_ids'] as $cat_id){
					$insert_sql = "INSERT INTO `cats2sale_per_product`(`cat_id`, `sale_per_product_id`) VALUES ('".(int)$cat_id."','".$sale_per_product_id."')";
					// error_log($insert_sql);
					$con->query($insert_sql, true);
				}
			}
		}
		
		public function extend_request_processing(){
			
		}
		
		public function admin_insert_custom_data($new_object){
			$this->admin_sale_per_product_to_cats($new_object->id);
		}

		public function admin_edit_custom_data(){
			$edited_id = (int)$_REQUEST['update_id'];

			$this->admin_sale_per_product_to_cats($edited_id);
		}
	}
?>