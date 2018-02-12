<?
	class category_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "categories";
		const instance_class_name = "category";
		const cont_class_name = "category_controller";

		protected static $factory = null;
		protected static $instances;

		protected $constant_filters = array(array("col_name" => "is_active", "condition" => "=", "value" => "1", "relation" => "AND"));

		protected function __construct() {
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}
/////////////////////////////////////////////////////////////////////////
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){

		}

		protected function new_object_created(&$new_object){
			$new_gallery = $this->data->gallery->add_new("name_trans_id", $new_object->name); // create a new gallery for this cat
			$new_object->gallery_id = $new_gallery->id;
			$new_object->save();
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

		public function get_parent_cat_name($cat, $get_full_path = false){
			$ret = "";

			if (!empty($cat->parent_cat_id)){
				$parent_cat = $this->data->category->get_by_id($cat->parent_cat_id);
				if (!emptY($parent_cat))
					$ret = $parent_cat->name;

				if ($get_full_path){
					$long_path = $this->get_parent_cat_name($parent_cat, $get_full_path);
					if (!empty($long_path))
						$ret = $long_path . " -> " . $ret;
				}
			}

			return $ret;
		}

		public function get_by_area_id($area_id){
			if (!empty($area_id)){
				return $this->get_by_column("area_id", (int)$area_id);
			}
			return null;
		}

		public function get_top_menu(){
			return $this->get_by_column("show_on_top_menu", 1);
		}

		public function get_side_menu(){
			return $this->get_by_column("show_on_side_menu", 1);
		}

		public function get_sale_products($include_subcats = true){
			$res = array();
			$this->data->product->search_products("", $this->id);
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class category extends base_data_object{

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

		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}

		public function get_subcats(){
			$res = array();

			$sql = "SELECT * FROM `".$this->db_table_name."` WHERE `parent_cat_id` = '".clear_string($this->id)."'";

			$result = db_con::get_con()->fetchData($sql);

			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$obj = $this->data->category->get_by_id($row['id']);
					if (!emptY($obj)){
						$res[] = $obj;
					}
				}
			}

			return $res;
		}

		public function get_gallery(){
			$gallery = $this->data->gallery->get_by_id($this->gallery_id);
			if (!emptY($gallery)){
				return $gallery->get_images();
			}
			else{
				$new_gallery = $this->data->gallery->add_new("name_trans_id", $this->name); // create a new gallery for this cat
				$this->gallery_id = $new_gallery->id;
				$this->save();
				return $new_gallery->get_images();
			}

			return null;
		}

		public function get_products($num = 0, $skip = 0){

			$products = array();

			$con = db_con::get_con();
			$cat_sql = "SELECT pc.*
						FROM `prods2cat` as pc inner join `products` as p on pc.prod_id = p.id
						WHERE pc.`cat_id` = '" . $this->id . "' ";
			$prod_constant_filter = $this->data->product->get_constant_filter_string();

			if (!emptY($prod_constant_filter))
				$cat_sql .= " AND " . $prod_constant_filter;

			$cat_sql .= " ORDER BY `display_order` ASC";

			if (!empty($num)){
				$cat_sql .= " LIMIT " . (int)$num;
			}
			if (!empty($skip)){
				$cat_sql .= " OFFSET " . $skip;
			}

            //error_log($cat_sql);
			$result = $con->fetchData($cat_sql);

			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$prod = $this->data->product->get_by_id($row['prod_id']);

					if (!empty($prod)){
						$products[] = $prod;
					}
					else{
						//error_log("prod not found");
						//error_log(print_r($row,true));
					}
				}
			}
			else{
				$subcat_sql = "
						SELECT
							p.prod_id
						FROM
							(SELECT * FROM `categories` WHERE `parent_cat_id` = '" . $this->id . "') AS c
							INNER JOIN
							`prods2cat` AS p ON c.id = p.cat_id
						";
                $subcat_sql .= " ORDER BY `display_order` ASC";

				if (!empty($num)){
					$subcat_sql .= " LIMIT " . (int)$num;
				}
				if (!empty($skip)){
					$subcat_sql .= " OFFSET " . $skip;
				}

                //error_log($subcat_sql);
				$result = $con->fetchData($subcat_sql);

				if (!empty($result['rows'])){
					foreach ($result['rows'] as $row){
						$prod = $this->data->product->get_by_id($row['prod_id']);

						if (!empty($prod))
							$products[] = $prod;
					}
				}
			}

			return $products;
		}

		public function get_pages_count($prods_on_page){
			$products = array();

			$con = db_con::get_con();
			//$cat_sql = "SELECT count(`id`) as 'count' FROM `prods2cat` WHERE `cat_id` = '" . $this->id ."'" ;
			$cat_sql = "SELECT count(pc.`id`) as 'count' FROM `prods2cat` as pc
						INNER JOIN `products` as p
						on pc.`prod_id` = p.`id`
						WHERE pc.`cat_id` = '" . $this->id ."'
						AND p.`hide_it` = 0";
			$result = $con->fetchData($cat_sql);

			if (!empty($result['rows'])){
				$count = $result['rows'][0]['count'];
				if (!empty($count)){

					return (int)ceil($count / $prods_on_page);
				}
				else{
					$subcat_sql = "
							SELECT
								count(p.prod_id ) as 'count'
							FROM
								(SELECT * FROM `categories` WHERE `parent_cat_id` = '" . $this->id . "') AS c
								INNER JOIN
								`prods2cat` AS p ON c.id = p.cat_id
							";

					$result = $con->fetchData($subcat_sql);

					if (!empty($result['rows'])){
						$count = $result['rows'][0]['count'];
						if (!empty($count)){
							return (int)ceil($count / $prods_on_page);
						}
					}
				}
			}

			return 1;
		}

		public function get_options(){
			$options = $this->data->option->get_by_cat_id($this->id);
			return $options;
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class category_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "categories";
			$this->object_class_name = "category";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class category_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "categories";
			$this->object_class_name = "category";
			$this->view_class_name = "category_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',			'path' => "name"),
				array( 'db' => 'gallery_id', 	'dt' => 'gallery_id',	'path' => "gallery_id"),
				array( 'db' => 'parent_cat_id', 'dt' => 'parent_cat_id','path' => "parent_cat_id",
						'formatter' =>
							function( $value, $object ) {
								global $data;
								$cat = $data->category->get_by_id($value);
								if (!empty($cat))
									return $cat->name;
								return $value;
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
