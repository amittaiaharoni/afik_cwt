<?
	class details2product_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "option_details2product";
		const instance_class_name = "details2product";
		const cont_class_name = "details2product_controller";

		protected static $factory = null;
		protected static $instances;

		protected function __construct() {
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}
/////////////////////////////////////////////////////////////////////////
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){
			$data = new data();
			$instance->detail = $data->optionDetail->get_by_id($instance->option_det_id);

			if ($instance->price == 0){
				$instance->price = $instance->detail->price;
				$instance->save();
			}
		}

		protected function new_object_created(&$new_object){

		}

		public function dt_custom_search($columns){

		}
/////////////////////////////////////////////////////////////////////////
		public function get_detail_data_for_product($prod_id, $option_det_id){
			$res = null;
			$sql = "SELECT * FROM `".$this->db_table_name."` WHERE `prod_id` = '".clear_string($prod_id)."' AND `option_det_id` = '".clear_string($option_det_id)."'";

			$result = $this->con->fetchData($sql);
			if (!empty($result['rows'])){
				$res = $this->get_by_id($result['rows'][0]['id']);
			}
			return $res;
		}

		public function get_details_for_product($prod_id, $option_id){
			$res = array();
			$sql = "SELECT
						dp.id
					FROM
						`option_details2product` as dp INNER JOIN `option_details` as od on dp.`option_det_id` = od.`id`
					WHERE
						dp.`prod_id` = '".(int)$prod_id."' AND od.`option_id` = '".(int)$option_id."'
					GROUP BY dp.id ORDER BY dp.`option_main`
					";
			// error_log($sql);
			$result = $this->con->fetchData($sql);
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$obj = $this->get_by_id($row['id']);

					if (!empty($obj))
						$res[] = $obj;
				}
			}
			return $res;
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class details2product extends base_data_object{

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


	class details2product_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "products_to_options";
			if (!empty($_GET['prod_id']))
				$this->url_page_name .= "&prod_id=".(int)$_GET['prod_id'];

			$this->object_class_name = "details2product";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class details2product_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "products_to_options";
			$this->object_class_name = "details2product";
			$this->view_class_name = "details2product_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(); // there is no dt
		}

		protected function link_details_to_details(){
			$detail = $this->data->details2product->get_by_id((int)$_POST['update_id']);
			if (!empty($detail)){
				$detail_id = $detail->option_det_id;
				if (!empty($_POST['option_det_id'])){
					$con = db_con::get_con();
					$prod_id = (int)$_GET['prod_id'];
					$detail_id_2 = (int)$_POST['option_det_id'];

					if (!emptY($_POST['linked_detail_id'])){
						$delete_q = "DELETE FROM `option_details2option_details` WHERE `detail_id_2` = '".(int)$detail_id."' AND `prod_id` = '".$prod_id."'";
						$con->query($delete_q, true); // remove old rows

						foreach ($_POST['linked_detail_id'] as $linked_detail_id){
							$linked_price = (int)$_POST['linked_price'][$linked_detail_id];

							$insert_q = "INSERT INTO `option_details2option_details`
											(`prod_id`, `detail_id_1`, `detail_id_2`, `price`) VALUES
											('".$prod_id."','".(int)$linked_detail_id."','".(int)$detail_id."','".$linked_price."')";
							$con->query($insert_q, true);
							// error_log($insert_q);
						}
					}
				}
			}
		}

		public function extend_request_processing(){

		}

		public function admin_insert_custom_data($new_object){
			$this->link_details_to_details();
			exit(); // ajax
		}

		public function admin_edit_custom_data(){
			$this->link_details_to_details();
			exit(); // ajax
		}
	}
?>
