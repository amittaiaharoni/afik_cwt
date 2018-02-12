<?
	class product_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "products";
		const instance_class_name = "product";
		const cont_class_name = "product_controller";

		protected static $factory = null;
		protected static $instances;

		// protected $contant_filters = array("hide_it" => "0");
		protected $constant_filters = array(array("col_name" => "hide_it", "condition" => "=", "value" => "0", "relation" => "AND"));

		protected function __construct() {
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}

		protected function extend_instance(&$instance){
			$instance->product_options = array("test","test2");

			// get linked cats
			$categories_array = array();

			$con = db_con::get_con();
			$cat_sql = "SELECT * FROM `prods2cat` WHERE `prod_id` = '" . $instance->id . "'";
			$result = $this->con->fetchData($cat_sql);

			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$categories_array[] = $row['cat_id'];
				}
			}
			$instance->categories = $categories_array;

			// get linked prods
			$linked_prods_array = array();

			$con = db_con::get_con();
			$linkeds_prods_sql = "SELECT * FROM `linked_prods` WHERE `prod_id` = '" . $instance->id . "'";
			$result = $this->con->fetchData($linkeds_prods_sql);

			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
                    for ($i = 1; $i <= 3; $i++) {
                        if(!empty($row['link_prod'.$i.'_id']))
                            $linked_prods_array[] = $row['link_prod'.$i.'_id'];
                    }
				}
			}
			$instance->linked_prods = $linked_prods_array;

			$attachment_files = $this->data->productFile->get_by_prod_id($instance->id);
			if (!empty($attachment_files)){
				$instance->attachment_files = $attachment_files;
			}
		}

		protected function new_object_created(&$new_object){
			$new_gallery = $this->data->gallery->add_new("name_trans_id", $new_object->name); // create a new gallery for this cat
			$new_object->gallery_id = $new_gallery->id;
			$new_object->save();
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
								$from .= " LEFT JOIN `prods2cat` as p2c ON main_table.`id` = p2c.`prod_id` ";
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

		public function get_by_cat($cat_id){
			if (!empty($cat_id)){
				return $this->get_by_column("cat_id", (int)$cat_id);
			}
			return null;
		}

		public function search_products($text = "", $cat_id = 0, $is_sale = 0, $in_stock = 0, $min_price = 0, $max_price = 0, $manufacturer_id = 0, $option_details = array(), $num = 0, $skip = 0, $return_count_only = 0){
			$res = array();

			$from = " `products` as p ";
			$where = " `hide_it` = 0 ";

			if (!empty($text)){
				$text = clear_string($text);

				$from .= "
					LEFT JOIN
						(SELECT * FROM `translations` as t1 WHERE t1.`lang` = ".(int)language::$current_lang." AND t1.`value` LIKE '%$text%') as t_name on `name_trans_id` = t_name.`key`
					LEFT JOIN
						(SELECT * FROM `translations` as t2 WHERE t2.`lang` = ".(int)language::$current_lang." AND t2.`value` LIKE '%$text%') as t_desc on `desc_trans_id` = t_desc.`key`
					LEFT JOIN
						(SELECT * FROM `translations` as t3 WHERE t3.`lang` = ".(int)language::$current_lang." AND t3.`value` LIKE '%$text%') as t_text on `text_trans_id` = t_text.`key` ";

				$where .= "
					AND (
						(t_name.`value` is not NULL) OR
						(t_desc.`value` is not NULL) OR
						(t_text.`value`  is not NULL)
					)
				";
			}

			// if (!empty($cat_id)){
				// $from .= " LEFT JOIN `prods2cat` as pc on p.id = pc.prod_id	";
				// $where .= " AND pc.cat_id = '".(int)$cat_id."' ";
			// }

			if (!empty($cat_id)){
				$from .= " INNER JOIN
							(
								SELECT
									pc.*
								FROM
									`categories` as c INNER JOIN
									`prods2cat` as pc ON c.`id` = pc.`cat_id`
								WHERE
									c.`id` = '".(int)$cat_id."' AND
									c.`is_active` = 1
							) as c2p ON c2p.`prod_id` = p.`id` ";
			}
			else{
				$from .= " INNER JOIN
							(
								SELECT
									pc.*
								FROM
									`categories` as c INNER JOIN
									`prods2cat` as pc ON c.`id` = pc.`cat_id`
								WHERE
									c.`is_active` = 1
							) as c2p ON c2p.`prod_id` = p.`id` ";
			}

			if (!empty($option_details)){
				$details_whare = "(0 ";
				foreach ($option_details as $option_det_id){
					$from .= "
						LEFT JOIN
							(SELECT * FROM `option_details2product` as od$option_det_id WHERE od$option_det_id.`option_det_id` = ".(int)$option_det_id.") as d2p$option_det_id on d2p$option_det_id.prod_id = p.id ";

					$details_whare .= " OR (d2p$option_det_id.`id` is not NULL) ";
				}
				$details_whare .= ")";
				$where .= " AND " . $details_whare;
			}

			if (!empty($is_sale)){
				$where .= " AND p.is_sale = '1' ";
			}

			if (!empty($in_stock)){
				$where .= " AND p.in_stock = '1' ";
			}

			if (!empty($min_price)){
				$where .= " AND p.price > '".(int)$min_price."' ";
			}

			if (!empty($max_price)){
				$where .= " AND p.price < '".(int)$max_price."' ";
			}

			if (!empty($manufacturer_id)){
				$where .= " AND p.manufacturer_id = '".(int)$manufacturer_id."' ";
			}

			$sql = "
				SELECT
					p.id
				FROM " .
					$from . "
				WHERE " .
					$where . "
				GROUP BY p.id
				ORDER BY p.display_order ";

			if (!$return_count_only){
				if (!empty($num))
					$sql .= " LIMIT " . (int)$num . " ";
				if (!empty($skip))
					$sql .= " OFFSET " . (int)$skip . " ";
			}

			// error_log($sql);
			$result = db_con::get_con()->fetchData($sql);

			if (!empty($result['rows'])){
				if ($return_count_only)
					return count($result['rows']);

				foreach ($result['rows'] as $row){
					$prod = $this->data->product->get_by_id($row['id']);
					if (!empty($prod))
						$res[] = $prod;
				}
			}

			if ($return_count_only)
				return 0;

			return $res;
		}

		public function get_not_hided_products_count(){
			$products = $this->data->product->get_by_column("hide_it",0);
			$count = 0;
			foreach( $products as $product ) {
				$count++;
			}
			return $count;
		}

		public function get_most_visited($max = 3, $random = true){
			$ret = array();

			$con = db_con::get_con();
			$sql = "SELECT `id` FROM `products` ORDER BY `views_count` DESC LIMIT " . ($max + 10);
			$result = $this->con->fetchData($sql);

			if (!empty($result['rows'])){
				$count = 0;

				if ($random)
					shuffle($result['rows']);

				foreach ($result['rows'] as $row){
					$prod = $this->get_by_id($row['id']);
					$ret[] = $prod;

					$count++;
					if ($count == $max)
						break;
				}
			}
			return $ret;
		}

		public function get_most_sold($max = 3, $random = true){
			$ret = array();

			$con = db_con::get_con();
			$sql = "SELECT `id` FROM `products` ORDER BY `sold_count` DESC LIMIT " . ($max + 10);
			$result = $this->con->fetchData($sql);

			if (!empty($result['rows'])){
				$count = 0;

				if ($random)
					shuffle($result['rows']);

				foreach ($result['rows'] as $row){
					$prod = $this->get_by_id($row['id']);
					$ret[] = $prod;

					$count++;
					if ($count == $max)
						break;
				}
			}
			return $ret;
		}

		public function get_chosen($max = 3, $random = true){
			$ret = array();

			$prods = $this->get_by_column("show_on_chosen","1");

			if (!empty($prods)){
				$count = 0;

				if ($random)
					shuffle($prods);

				foreach ($prods as $prod){
					$ret[] = $prod;

					$count++;
					if ($count == $max)
						break;
				}
			}
			return $ret;
		}
		public function get_sale_by_prod_id($prod_id){
			$res = array();
			/* $cat_sql = "SELECT s.* FROM `products` as p
						join `prods2cat` as pc
						on p.`id` = pc.`prod_id`
						join `sale` as s
						on pc.`cat_id` = s.`sale_to_cat`
						WHERE s.`is_active` = 1 and p.`id` = ".$prod_id; */
			$cat_sql = "SELECT s.* FROM `products` as p
						join `prods2cat` as pc
						on p.`id` = pc.`prod_id`
						join `cats2sale` as s
						on pc.`cat_id` = s.`cat_id`
						WHERE s.`is_active` = 1 and p.`id` = ".$prod_id;
			$result = db_con::get_con()->fetchData($cat_sql);
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$ret = $this->data->sale->get_by_id($row['id']);
					if( !empty( $ret ) ) {
						$res[] = $ret;
					}
				}
			}

			return $res;
		}
	}

	class product extends base_data_object{

		protected $product_options;

		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);
		}

		protected function extend_deletion(){
			// delete gallery
			$gallery = $this->data->gallery->get_by_id($this->gallery_id);
			if (!empty($gallery)){
				$images = $gallery->get_images();
				foreach ($images as $image){
					$image->delete();
				}
				$gallery->delete();
			}
		}

		public function add_option($option){
			$this->product_options[] = $option;
		}

		public static function sort_by_example(product $a, product $b){
			return $a->something > $b->something ? 1 : -1;
		}

		public static function sort_by_order_and_barcode(product $a, product $b){
			$bar_compare = strcmp($a->barcode, $b->barcode);
			$order_compare = 0;
			if ($a->display_order != $b->display_order)
				$order_compare = $a->display_order > $b->display_order ? 1 : -1;

			if ($order_compare != 0)
				return $order_compare;

			return $bar_compare;
		}
		public function get_option_details($ods_array){
			$res = array();
			$ands = '';
			foreach($ods_array as $od){
				$ands .= ' and `option_det_id` = '.$od;
			}
			$sql = 'select `id` from `option_details2product` where `prod_id` = '.$this->id.' '.$ands;
			$result = db_con::get_con()->fetchData($sql);
			error_log($sql);
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$opt = $this->data->details2product->get_by_id($row['id']);

					if (!empty($opt))
						$res[] = $opt;
				}
			}

			return $res;
		}
		public function get_options(){
			$res = array();
			$sql = "SELECT
						od.option_id
					FROM
						option_details2product as dp LEFT JOIN
						option_details as od on dp.option_det_id  = od.id
					WHERE
						dp.prod_id = ".$this->id ."
					group by od.option_id";

			$result = db_con::get_con()->fetchData($sql);

			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$opt = $this->data->option->get_by_id($row['option_id']);

					if (!empty($opt))
						$res[] = $opt;
				}
			}

			return $res;

			// $cats = $this->categories;
			// foreach ($cats as $cat_id){
				// $cat_options = $this->data->option->get_by_cat_id($cat_id);
				// foreach ($cat_options as $option){
					// $details = $option->get_details();

					// foreach ($details as $detail){
						// $detail_data = $this->data->details2product->get_details_for_product($this->id, $detail->id);

					// }
				// }
			// }
		}

		public function get_gallery(){
			if (!empty($this->gallery_id)){
				$gallery = $this->data->gallery->get_by_id($this->gallery_id);
				if (!empty($gallery)){
					return $gallery->get_images();
				}
			}
			else{
				$new_gallery = $this->data->gallery->add_new("name_trans_id", $this->name); // create a new gallery for this cat
				$this->gallery_id = $new_gallery->id;
				$this->save();
				return $new_gallery->get_images();
			}
			return array();
		}

		public function get_parent_cats_array(){
			$res = array();

			$cat_sql = "SELECT * FROM `prods2cat` WHERE `prod_id` = '" . $this->id . "' ";

			$result = db_con::get_con()->fetchData($cat_sql);

			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$cat = $this->data->category->get_by_id($row['cat_id']);

					if (!empty($cat)){
						// $res[] = $cat->id;

						while (!empty($cat)){
							if (!empty($cat->parent_cat_id)){
								$cat = $this->data->category->get_by_id($cat->parent_cat_id);

								if (!empty($cat))
									$res[] = $cat->id;
							}
							else{
								break;
							}
						}
					}
				}
			}

			return $res;
		}

	}

	class product_view extends view_abstract{

		public function __construct($file_path) {
			$this->url_page_name = "admin_products";
			$this->object_class_name = "product";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}

        protected function is_linked_prod_selected($prod_id){
             if (!empty($this->edited_object) && !empty($this->edited_object->linked_prods) && in_array($prod_id,$this->edited_object->linked_prods)){
                 return "selected";
             }
             return "";
        }

		// public function is_cat_selected($cat_id){
			// if (!empty($this->edited_object) && !empty($this->edited_object->categories) && in_array($cat_id,$this->edited_object->categories)){
				// return "selected";
			// }
			// return "";
		// }
	}

	class product_controller extends controller_abstract{

		public function __construct() {
			$this->url_page_name = "admin_products";
			$this->object_class_name = "product";
			$this->view_class_name = "product_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',		'path' => "id",			"data_type" => "int"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',		'path' => "name",		"data_type" => "string"),
				array( 'db' => 'barcode', 		'dt' => 'barcode',	'path' => "barcode",	"data_type" => "string"),
				array( 'db' => 'price', 		'dt' => 'price',	'path' => "price",		"data_type" => "int"),
				array( 'db' => 'gallery_id', 	'dt' => 'gallery_id',	'path' => "gallery_id",		"data_type" => "int")
				// array(
					// 'db'        => 'start_date',
					// 'dt'        => 4,
					// 'formatter' => function( $d, $row ) {
						// return date( 'jS M y', strtotime($d));
					// }
				// )
			);
		}

		protected function admin_link_to_cats($prod_id){
			$con = db_con::get_con();

			// LINK TO CATEGORY
			$delete_sql = "DELETE FROM `prods2cat` WHERE `prod_id` = '" . $prod_id . "'";
			$con->query($delete_sql, true); // remove old rows

			if (!empty($_POST['cat_ids'])){ // add new rows
				foreach ($_POST['cat_ids'] as $cat_id){
					$insert_sql = "INSERT INTO `prods2cat`(`cat_id`, `prod_id`) VALUES ('".(int)$cat_id."','".$prod_id."')";
					$con->query($insert_sql, true);
				}
			}
		}

		protected function admin_link_to_prods($prod_id){
			$con = db_con::get_con();

            $delete_sql = "DELETE FROM `linked_prods` WHERE `prod_id` = '" . $prod_id . "'";
            $con->query($delete_sql, true); // remove old rows

            // LINK TO PRODUCTS
			if (!empty($_POST['prods_ids'])){ // add new rows

                $i = 1;
                $keys = "`prod_id`";
                $values = clear_string($prod_id);
				foreach ($_POST['prods_ids'] as $pro_id){
                    if($i <= 3){
                        $keys .= ",`link_prod".$i."_id`";
                        $values .= "','".clear_string($pro_id);
                        $i++;
                    }
				}
                if(( !empty($keys) && strpos($keys,",") !== -1 )  && ( !empty($values)&& strpos($values,",") !== -1 )){
                    $insert_sql = "INSERT INTO `linked_prods` ( ".$keys." ) VALUES ('".$values."')";
                    //error_log($insert_sql);
                    $con->query($insert_sql, true);
                }
			}
		}

		public function extend_request_processing(){

		}

		public function admin_insert_custom_data($new_object){
			$this->admin_link_to_cats($new_object->id);
            $this->admin_link_to_prods($new_object->id);

			// $this->admin_link_to_options($new_object->id);
		}

		public function admin_edit_custom_data(){
			$edited_id = (int)$_REQUEST['update_id'];

			$this->admin_link_to_cats($edited_id);
            $this->admin_link_to_prods($edited_id);

			// $this->admin_link_to_options($edited_id);
		}
	}

	///////////////////////////////////////////////////////////////////////////////////////

	class product_manage_controller extends controller_abstract{

		public function __construct() {
			$this->url_page_name = "admin_productManage";
			$this->object_class_name = "product";
			$this->view_class_name = "product_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function - actual data type (_trans_id = string)
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',		'path' => "id",			"data_type" => "int"),
				array( 'db' => 'image', 		'dt' => 'image',	'path' => "image",		"data_type" => "string"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',		'path' => "name",		"data_type" => "string"),
				array( 'db' => 'barcode', 		'dt' => 'barcode',	'path' => "barcode",	"data_type" => "int"),
				array( 'db' => 'price', 		'dt' => 'price',	'path' => "price",		"data_type" => "int"),
				array( 'db' => 'price2', 		'dt' => 'price2',	'path' => "price2",		"data_type" => "int"),
				array( 'db' => 'display_order', 'dt' => 'display_order', 'path' => "display_order",		"data_type" => "int"),
				array( 'db' => 'hide_it', 		'dt' => 'hide_it',	'path' => "hide_it",	"data_type" => "int"),
				array( 'db' => 'in_stock', 		'dt' => 'in_stock',	'path' => "in_stock",	"data_type" => "int"),
				array( 'db' => 'is_sale', 		'dt' => 'is_sale',	'path' => "is_sale",	"data_type" => "int"),
				array( 'db' => 'gallery_id', 	'dt' => 'gallery_id','path' => "gallery_id")
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
