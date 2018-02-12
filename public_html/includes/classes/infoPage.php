<?
	class infoPage_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "info_pages";
		const instance_class_name = "infoPage";
		const cont_class_name = "infoPage_controller";

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
			$new_gallery = $this->data->gallery->add_new("name_trans_id", "infoPage_".$new_object->id ."_".$new_object->name); // create a new gallery for this cat
			$new_object->gallery_id = $new_gallery->id;
			$new_object->save();
		}

		public function dt_custom_search($columns){
			$ret = array();
			$sql = "SELECT `id` FROM `".self::db_table_name."` WHERE 1 ";

			if (!empty($_GET['custom_search'])){
				foreach ($_GET['custom_search'] as $field){
					switch ($field['name']){
						case "cat_id":
							if (!emptY($field['value']))
								$sql .= " AND `cat_id` = '" .clear_string($field['value']) . "' ";
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
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class infoPage extends base_data_object{

		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);
		}

		protected function extend_deletion(){

		}

		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}

		public function get_gallery(){
			$gallery = $this->data->gallery->get_by_id($this->gallery_id);
			if (!emptY($gallery)){
				return $gallery->get_images();
			}else
                return '';
        }
		
		/* public function get_subcats(){
			$cat = $this->cat_id;
			$icat = $this->data->infoCat->get_by_id($cat);
			if(!empty($icat)){
				return $icat->get_subcats();
			}
			return null;
		} */
    }


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class infoPage_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "info_pages";
			$this->object_class_name = "infoPage";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class infoPage_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "info_pages";
			$this->object_class_name = "infoPage";
			$this->view_class_name = "infoPage_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',		'path' => "id"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',		'path' => "name"),
				array( 'db' => 'gallery_id', 	'dt' => 'gallery_id','path' => "gallery_id"),
				array( 'db' => 'cat_id', 		'dt' => 'cat_id',	'path' => "cat_id",
						'formatter' =>
							function( $value, $object ) {
								global $data;
								$cat = $data->infoCat->get_by_id($value);
								if (!empty($cat))
									return $cat->name;
								return $value;
							}
						)
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
