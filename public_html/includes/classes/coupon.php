<?
	class coupon_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "coupons";
		const instance_class_name = "coupon";
		const cont_class_name = "coupon_controller";

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

			if(!empty($this->is_giftcard) || $_POST['is_giftcard'] == 1){
                // error_log("is giftcard: " . $this->is_giftcard);
				$series = "GIFTCARD";
			}else {
				$series = random_string(5);
			}

			//check if multiuse coupon

			$coupon_num = random_string(7);
			$sql = "UPDATE `coupons` SET series='$series', number='$coupon_num', date_time=NOW() WHERE id='".$new_object->id."'";

			db_con::get_con()->query($sql, true);

			if (!empty($_POST['quantity'])){
				for ($i = 0; $i < ((int)$_POST['quantity'] - 1); $i++){
					$coupon_num = random_string(7);

					$sql = "
						INSERT INTO `coupons`
							(`name`, `series`, `number`, `amount`, `type`, `date_time`, `text`, `expiration_date`, `used`, `image`, `order_id`)
						SELECT
							`name`, `series`, '$coupon_num', `amount`, `type`, `date_time`, `text`, `expiration_date`, `used`, `image`, `order_id`
						FROM
							`coupons`
						WHERE
							`id` = '".$new_object->id."'";
					// error_log($sql);
					db_con::get_con()->query($sql, true);
				}
			}
		}
		
		public function create_giftcard($amount){
			if(isset($_SESSION['last_order_id'])){
				$series = "GIFTCARD";
				$coupon_num = random_string(7);
				$order_id = $_SESSION['last_order_id'];
				$sql = "INSERT INTO `coupons`
							(`name`, `series`, `number`, `amount`, `type`, `date_time`, `text`, `expiration_date`, `used`, `image`, `order_id`, `is_giftcard`)
						VALUES
							('גיפט קראד', '$series', '$coupon_num', $amount, 1, NOW() , NULL, NULL, 0, NULL, $order_id, 1)
						";
				// error_log($sql);
				$answ = db_con::get_con()->query($sql, true);
				return $answ;
			}
		}
		public function get_last_giftcard(){
			$giftcards = $this->get_giftcards();
			// error_log(max($giftcards));
			return max($giftcards);
		}

		public function dt_custom_search($columns){
				$ret = array();
				if (!empty($_GET['custom_search'])){
					foreach ($_GET['custom_search'] as $field){
						switch ($field['name']){
							case "giftcards":
                                // error_log("show only if giftcard");
								if (!emptY($field['value'])){
                                    // error_log("not empty");
									$ret = $this->get_giftcards();
								}
							break;
						}
					}
				}
				return $ret;
		}
/////////////////////////////////////////////////////////////////////////

		public function get_by_code($coupon_code){
			$coupons = $this->get_by_column("number", $coupon_code);
			if (!empty($coupons))
				return $coupons[0];
			return null;
		}

        public function count_by_code($coupon_code)
        {
            $series = $this->get_by_code($coupon_code);
            if(!empty($series)){
                $coupons = $this->get_by_column("series", $series);
                return count($coupons);
            }
        }

        public function get_giftcards()
        {
            $ret = array();
            //foreach ($this->get_by_column("is_giftcard","1") as $row) {
            foreach ($this->get_by_column("series","GIFTCARD") as $row) {
                $ret[] = $row->id;
            }
            return $ret;
        }
		
		public function add_credits($code){
            $giftcard_coupon = $this->get_by_code($code);
			if($giftcard_coupon->used == 0){
				$this->data->user->loged_in_user->credits += $giftcard_coupon->amount;
				$this->data->user->loged_in_user->save();
				$giftcard_coupon->used = 1;
				$giftcard_coupon->save();
				return true;
			}
            return false;
        }


        public function use_credits($amount){
            // error_log($this->data->user->loged_in_user->credits);
            return false;
        }

    }


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class coupon extends base_data_object{

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


	class coupon_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "coupons";
			$this->object_class_name = "coupon";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class coupon_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "coupons";
			$this->object_class_name = "coupon";
			$this->view_class_name = "coupon_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 		'dt' => 'id',		'path' => "id"),
				array( 'db' => 'name', 		'dt' => 'name',		'path' => "name"),
				array( 'db' => 'number', 	'dt' => 'number',	'path' => "number"),
				array( 'db' => 'amount', 	'dt' => 'amount',	'path' => "amount",
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

		public function extend_request_processing(){

		}

		public function admin_insert_custom_data($new_object){

		}

		public function admin_edit_custom_data(){

		}
	}
?>
