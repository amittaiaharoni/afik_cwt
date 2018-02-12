<?
	class deliveryType_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "delivery_types";
		const instance_class_name = "deliveryType";
		const cont_class_name = "deliveryType_controller";

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

	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class deliveryType extends base_data_object{

		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);
		}

		protected function extend_deletion(){

		}

		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}

		public function get_delivery_places(){
			return $this->data->deliveryPlace->get_by_column("area_id", $this->id);

			return null;
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class deliveryType_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "delivery_types";
			$this->object_class_name = "deliveryType";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class deliveryType_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "delivery_types";
			$this->object_class_name = "deliveryType";
			$this->view_class_name = "deliveryType_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',			'path' => "name"),
				array( 'db' => 'price', 		'dt' => 'price',		'path' => "price")
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
