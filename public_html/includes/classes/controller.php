<?
	class controller extends controller_abstract{

		public function __construct() {
			parent::__construct();
		}

		public function extend_request_processing(){

		}

		public function admin_insert_custom_data($new_object){

		}

		public function admin_edit_custom_data(){

		}
	}

	abstract class controller_abstract{
		protected $defaults;
		protected $data;
		protected $page_path;
		protected $url_page_name;
		protected $view_class_name;
		protected $dt_data_source_added_filter;
		public $object_class_name;

		protected $edited_object = null;

		public function __construct(){
			global $data;
			$this->data = $data;

			if (!empty($this->url_page_name))
				$this->page_path = "index.php?page=" . $this->url_page_name;
			else
				$this->page_path = "index.php";
		}

		abstract function extend_request_processing();

		public function get_view($path){
			if (!empty($this->view_class_name))
				return new $this->view_class_name($path);
			return new view($path);
		}

		public function process_request(){

			if (!empty($_REQUEST['action'])){
				switch ($_REQUEST['action']){
					case "get_dt_data":
						echo $this->get_dt_data();
						exit();
					break;
					case "save_dt_data":
						echo $this->save_dt_data();
						exit();
					break;
					case "delete_dt_data":
						echo $this->delete_dt_data();
						exit();
					break;
					case "admin_insert_data":
						$this->admin_insert_data();
					break;
					case "admin_edit_data":
						$this->admin_edit_data();
					break;
					case "admin_delete_image":
						$this->admin_delete_image();
						exit();
					break;
					case "admin_delete_file":
						$this->admin_delete_file();
						exit();
					break;
				}
			}

			$this->extend_request_processing();
		}

		// allows to insert added data, custom for each object
		abstract function admin_insert_custom_data($new_object);

		public function admin_insert_data(){

			$table_info = $this->data->{$this->object_class_name}->get_db_table_info();

			$new_object = null;
			$insert_data = array();
			foreach ($table_info as $col_name => $col_type){
				$col_new_name = $col_name;
				if (substr($col_name, -9) === "_trans_id"){ // check if its a translatable field
					$col_new_name = str_replace("_trans_id", "", $col_name);
				}

				if (!empty($_REQUEST[$col_new_name])){
					$value = $_REQUEST[$col_new_name];
					$insert_data[$col_name] = $value;
				}

				if (!empty($_FILES) && !empty($_FILES[$col_new_name])){
					if(is_image($col_new_name))
						$file_name = upload_with_thumb($col_new_name, 300);
					else
						$file_name = upload_file($col_new_name);
					if (!empty($file_name)){
						$insert_data[$col_name] = $file_name;
					}
				}
			}

			if (!empty($insert_data)){
				$new_object = $this->data->{$this->object_class_name}->add_new($insert_data);
			}

			$this->admin_insert_custom_data($new_object);
		}

		// allows to edit added data, custom for each object
		abstract function admin_edit_custom_data();

		public function admin_edit_data(){

			if (!empty($_REQUEST['update_id'])){
				$obj = $this->data->{$this->object_class_name}->get_by_id((int)$_REQUEST['update_id']);

				if (!empty($obj)){
					if (!empty($_POST))
						foreach ($_POST as $key => $value){
							if (get_magic_quotes_gpc()){
								if (is_array($value)){
									foreach ($value as $k => $v){
										$value[$k] = stripslashes($v);
									}
								}
								else{
									$value = stripslashes($value);
								}
							}
							if (isset($obj->$key))
								$obj->$key = $value;
						}

					if (!empty($_FILES))
						foreach ($_FILES as $key => $file){
							if (isset($obj->$key)){
								$is_image = is_image($key);

								$old_file = "";
								if (!empty($obj->$key)){
									if (is_object($obj->$key))
										$old_file = $obj->$key->to_string();
									else
										$old_file = $obj->$key;
								}

								if ($is_image)
									$file_name = upload_with_thumb($key, 300);
								else
									$file_name = upload_file($key);

								if (!empty($file_name)){
									$obj->$key = $file_name;

									if (!emptY($old_file)){
										if ($is_image)
											remove_image_with_thumb($old_file);
										else
											unlink(site_config::get_value('upload_files_folder').$old_file);
									}
								}
							}
						}

					$obj->save();
				}
			}

			$this->admin_edit_custom_data();
		}

		public function admin_delete_image(){
			if (!empty($_REQUEST['update_id'])){
				$obj = $this->data->{$this->object_class_name}->get_by_id((int)$_REQUEST['update_id']);

				if (!empty($obj)){
					if (!emptY($_REQUEST['image']) && !empty($obj->{$_REQUEST['image']})){
						if (remove_image_with_thumb($obj->{$_REQUEST['image']})){
							$obj->{$_REQUEST['image']} = "";
							$obj->save();
						}
					}
				}
			}
		}

		public function admin_delete_file(){
			global $defaults;

			if (!empty($_REQUEST['update_id'])){
				$obj = $this->data->{$this->object_class_name}->get_by_id((int)$_REQUEST['update_id']);

				if (!empty($obj)){
					if (!emptY($_REQUEST['file']) && !empty($obj->{$_REQUEST['file']})){
						if (unlink(site_config::get_value('upload_files_folder').$obj->{$_REQUEST['file']})){
							$obj->{$_REQUEST['file']} = "";
							$obj->save();
						}
					}
				}
			}
		}
		// return ajax data do datatable, depends on dt_columns definition in the child class
		public function get_dt_data(){
			if (empty($this->dt_columns)){
				error_log("controller - dt_columns not implemented in class : " . get_called_class());
				return "";
			}

			$data = datatable_source::get_data( $this->object_class_name, $this->dt_columns, $this->dt_data_source_added_filter );

			// return json_encode($data);
			return json::encode($data);
		}

		// return ajax data do datatable, depends on dt_columns definition in the child class
		public function save_dt_data(){
			if (empty($this->dt_columns)){
				error_log("controller - dt_columns not implemented in class : " . get_called_class());
				return "";
			}

			$res = datatable_source::save_data( $this->object_class_name, $this->dt_columns );

			// return json_encode($res);
			return json::encode($res);
		}

		public function delete_dt_data(){
			
			if (!empty($_POST['delete_id'])){
				$obj = $this->data->{$this->object_class_name}->get_by_id((int)$_POST['delete_id']);
// error_log(print_r($obj,1));
				if (!emptY($obj))
					return $obj->delete();
			}
			return true;
		}
	}
?>
