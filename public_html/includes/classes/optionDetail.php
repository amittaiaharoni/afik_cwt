<?
	class optionDetail_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "option_details";
		const instance_class_name = "optionDetail";
		const cont_class_name = "optionDetail_controller";

		protected static $factory = null;
		protected static $instances;

		protected function __construct() {
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}
/////////////////////////////////////////////////////////////////////////
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){
			$data = new data();
			$instance->option = $data->option->get_by_id($instance->option_id);
			$instance->price = $instance->option->price + $instance->added_price;
			$instance->stock = $instance->stock;
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


	class optionDetail extends base_data_object{

		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);
		}

		protected function extend_deletion(){

		}

		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}

		public function connect_to_all_relevant_prods(){
			$sql = "
				INSERT INTO
					`option_details2product`( `option_det_id`, `prod_id`, `price`)
				SELECT
					".$this->id.", pc.prod_id, ".$this->added_price." + o.`price` as 'price'
				FROM
					(
						SELECT * FROM `options2cat` WHERE option_id = '".$this->option_id."'
					) AS oc
					INNER JOIN
					`options` as o on o.id = oc.option_id
					INNER JOIN
					`prods2cat` AS pc ON oc.cat_id = pc.cat_id
					LEFT JOIN
					(
						SELECT
							*
						FROM
							`option_details2product`
						WHERE
							option_det_id = '".$this->id."'
					) AS op ON op.prod_id = pc.prod_id
				WHERE
					op.id IS NULL
				GROUP BY
					pc.prod_id
			";

			db_con::get_con()->query($sql, true);
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class optionDetail_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "option_details";
			if (!empty($_GET['opt_id'])){
				$this->url_page_name .= "&opt_id=" . (int)$_GET['opt_id'];
			}

			$this->object_class_name = "optionDetail";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class optionDetail_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "option_details";
			$this->object_class_name = "optionDetail";
			$this->view_class_name = "optionDetail_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',			'path' => "name"),
				array( 'db' => 'image', 		'dt' => 'image',		'path' => "image")
			);

			if (!empty($_GET['opt_id'])){
				$where = array();
				$where = array("columns" => array(
												array(
													"col_name" => "option_id",
													"condition" => "=",
													"value" => (int)$_GET['opt_id'])),
													"relation" => "AND"
								);

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
