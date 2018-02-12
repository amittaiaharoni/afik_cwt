<?
	class view extends view_abstract{

		public function __construct($file_path, &$view_data = null) {
			parent::__construct($file_path, &$view_data);
			//parent::__construct($file_path, $view_data);
		}

		public function post_render_html(&$html){

		}
	}

	abstract class view_abstract{
		protected $data;
		protected $file_path;
		protected $view_data;
		protected $page_path;
		protected $translation_key;
		protected $url_page_name;
		public $object_class_name;
		protected $dependencies = array();
		protected $dt_columns;

		protected $edited_object = null;

		public function __construct($file_path, &$view_data = null){
			global $data;
			global $is_admin;

			$this->file_path 		= $file_path;
			$this->view_data 		= $view_data;
			$this->translation_key = md5($this->file_path);
			$this->data = $data;
			$this->is_admin = $is_admin;

			if (!empty($this->url_page_name))
				$this->page_path = "index.php?page=" . $this->url_page_name;
			else
				$this->page_path = "index.php";

			// if (!empty($object_class_name))
				// $this->object_class_name = $object_class_name;

			if (!empty($_GET['id']) && !empty($this->object_class_name))
				$this->edited_object = $this->data->{$this->object_class_name}->get_by_id($_GET['id']);
		}

		public function register_include($include_name, &$view){
			$this->dependencies[$include_name]["view"] = $view;
		}

		public function render_file($path){
			global $site_path;
			global $is_mobile;
			global $is_main;

			if (!empty($path) && file_exists($path)){
				ob_start();

				include $path;

				$rendered_html = ob_get_clean();

				return $rendered_html;
			}
			return "";
		}

		public abstract function post_render_html(&$html);

		public function get_html(){
			if (!empty($this->file_path) && file_exists($this->file_path)){

				$raw_html = $this->render_file($this->file_path);

				$placeholders = array();

				if (!empty($raw_html)){
					preg_match_all("#\{__(?<placeholder>.*?)__\}#", $raw_html, $placeholders);

					if (!empty($placeholders)){
						foreach ($placeholders["placeholder"] as $placeholder){
							if (!empty($this->dependencies[$placeholder])){
								$include_html = "";

								if (is_object($this->dependencies[$placeholder]["view"])) // its a view
									$include_html = $this->dependencies[$placeholder]["view"]->get_html();
								else
									$include_html = $this->dependencies[$placeholder]["view"];

								$raw_html = preg_replace("#\{__".$placeholder."__\}#", $include_html, $raw_html);
							}
							else{
								// error_log(" include not registered for file : " . $this->file_path . ", include name : " . $placeholder);

								$raw_html = preg_replace("#\{__".$placeholder."__\}#", "", $raw_html);
							}
						}
					}

					$this->post_render_html($raw_html);// can be implemented in the child class to make last minute changes

					return $raw_html;
				}
			}
			return "";
		}

		private function get_edited_data($key){
			if (!empty($this->edited_object) && !empty($this->edited_object->$key)){
				return $this->edited_object->$key;
			}
			return "";
		}
		private function is_selected($key,$val){
			if (!empty($this->edited_object) && !empty($this->edited_object->{$key}) && $this->edited_object->{$key}==$val){
				return "selected";
			}
			return "";
		}
		private function is_checked($key,$val,$default = false){
			if (!empty($this->edited_object)){
				if (isset($this->edited_object->{$key})){
					if ($this->edited_object->{$key}==$val){
						return "checked";
					}
				}
			}
			else if ($default)
				return "checked";
			return "";
		}
		private function is_cat_selected($cat_id){
			if (!empty($this->edited_object) && !empty($this->edited_object->categories) && in_array($cat_id,$this->edited_object->categories)){
				return "selected";
			}
			return "";
		}
	}
?>
