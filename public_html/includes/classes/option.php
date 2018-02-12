<?
	class option_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY		
		const db_table_name = "options";
		const instance_class_name = "option";		
		const cont_class_name = "option_controller";
			
		protected static $factory = null;
		protected static $instances;		
		
		protected function __construct() {			
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}			
/////////////////////////////////////////////////////////////////////////		
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){
			$categories_array = array();
			$options_array = array();
			
			// get linked cats
			$con = db_con::get_con();
			$cat_sql = "SELECT * FROM `options2cat` WHERE `option_id` = '" . $instance->id . "'";
			$result = $this->con->fetchData($cat_sql);			
			
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$categories_array[] = $row['cat_id'];
				}
			}
			
			$instance->categories = $categories_array;
			
			// get linked options
			$con = db_con::get_con();
			$opt_sql = "SELECT * FROM `options2options` WHERE `option_id_1` = '" . $instance->id . "'";
			$result = $this->con->fetchData($opt_sql);			
			
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$options_array[] = $row['option_id_2'];
				}
			}
			
			$instance->linked_options = $options_array;
		}
		
		protected function new_object_created(&$new_object){
			
		}
		
		public function dt_custom_search($columns){
			
		}
		
		public function get_by_cat_id($cat_id){
			$ret['by_id'] = array();
			$q = "SELECT o.id FROM `options` as o inner join `options2cat` as oc on o.id = oc.option_id WHERE oc.cat_id = '" . (int)$cat_id . "'";			
			$result = db_con::get_con()->fetchData($q);
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$option = $this->data->option->get_by_id($row['id']);
					if (!empty($option))
						$ret['by_id'][$option->id] = $option;
				}
			}
			return array_values($ret['by_id']);
		}		
		
/////////////////////////////////////////////////////////////////////////	
		
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
	
	class option extends base_data_object{
		
		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);				
		}
		
		protected function extend_deletion(){
			// delete details
			$details = $this->data->optionDetail->get_by_column("option_id",$this->id);
			if (!empty($details)){
				foreach ($details as $detail){
					$detail->delete();
				}
			}
		}
		
		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}
		
		public function get_details(){
			$details = $this->data->optionDetail->get_by_column("option_id",$this->id);
			
			return $details;
		}
		
		public function get_details_for_product($prod_id){
			return $this->data->details2product->get_details_for_product($prod_id, $this->id);			
		}
		
		public function get_linked_options(){
			$ret = array();
			
			$q = "SELECT o.id FROM `options` as o inner join `options2options` as oo on o.id = oo.option_id_1 WHERE oo.option_id_2 = '" . $this->id . "'";			
			$result = db_con::get_con()->fetchData($q);
			
			if (!empty($result['rows'])){
				foreach ($result['rows'] as $row){
					$option = $this->data->option->get_by_id($row['id']);
					if (!empty($option))
						$ret[] = $option;
				}
			}
			return $ret;
		}
		
		public function connect_to_all_relevant_prods(){			
			$sql = "
				INSERT INTO 
					`option_details2product`( `option_det_id`, `prod_id`, `price`)
				SELECT 
					det_ids.id,
					prod_ids.id,
					det_ids.price
				FROM
				(
					SELECT od.`id`, od.`added_price` + o.price as 'price' FROM `option_details` as od INNER JOIN `options` as o on o.`id` = od.`option_id`  WHERE od.`option_id` = '".$this->id."'
				) as det_ids,
				(
					SELECT 
						pc.prod_id as id
					FROM 
						`options2cat` as oc 
						inner join
						`prods2cat` as pc on oc.cat_id = pc.cat_id
						left join
						(
							select 
								opi.* 
							from 
								`option_details2product` as opi inner join 
								`option_details` as odi on opi.option_det_id = odi.id
							where
								odi.option_id = '".$this->id."'
							
						) as op on op.prod_id = pc.prod_id
					WHERE 
						oc.option_id = '".$this->id."'
						and
						op.id is null
					group by pc.prod_id
				) as prod_ids
				WHERE 
					prod_ids.id > 0
					AND 
					det_ids.id > 0
			";
			
			db_con::get_con()->query($sql, true);
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class option_view extends view_abstract{
				
		public function __construct($file_path) {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "options";
			$this->object_class_name = "option";
			
			parent::__construct($file_path);				
		}
		
		public function post_render_html(&$html){
			
		}
		
		public function is_cat_selected($cat_id){
			if (!empty($this->edited_object) && !empty($this->edited_object->categories) && in_array($cat_id,$this->edited_object->categories)){
				return "selected";	
			}		
			return "";
		}
		
		public function is_opt_selected($opt_id){
			if (!empty($this->edited_object) && !empty($this->edited_object->linked_options) && in_array($opt_id,$this->edited_object->linked_options)){
				return "selected";	
			}		
			return "";
		}
	}

	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////	
//////////////////////////////////////////////////////////////////////////////////////
	
		
	class option_controller extends controller_abstract{
				
		public function __construct() {
			global $is_admin;
			
			$this->url_page_name = ($is_admin?"admin_":"") . "options";
			$this->object_class_name = "option";
			$this->view_class_name = "option_view";
			
			parent::__construct();	
			
			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
				array( 'db' => 'option_name', 	'dt' => 'option_name',	'path' => "option_name"),
				array( 'db' => 'name_trans_id', 'dt' => 'name',			'path' => "name")
			);
		}
		
		protected function admin_link_to_cats($option_id){
			$con = db_con::get_con();
			
			// LINK TO CATEGORY
			$delete_sql = "DELETE FROM `options2cat` WHERE `option_id` = '" . $option_id . "'";
			$con->query($delete_sql, true); // remove old rows
			
			if (!empty($_POST['cat_ids'])){ // add new rows
				foreach ($_POST['cat_ids'] as $cat_id){
					$insert_sql = "INSERT INTO `options2cat`(`cat_id`, `option_id`) VALUES ('".(int)$cat_id."','".$option_id."')";
					$con->query($insert_sql, true);
				}
			}
		}
		
		protected function admin_link_to_options($option_id){
			$con = db_con::get_con();
						
			// LINK TO OPTIONS
			$delete_sql = "DELETE FROM `options2options` WHERE `option_id_1` = '" . $option_id . "'";
			$con->query($delete_sql, true); // remove old rows
			
			if (!empty($_POST['opt_ids'])){ // add new rows
				foreach ($_POST['opt_ids'] as $opt_id){
					$insert_sql = "INSERT INTO `options2options`(`option_id_2`, `option_id_1`) VALUES ('".(int)$opt_id."','".$option_id."')";
					$con->query($insert_sql, true);
				}
			}
		}
		
		public function extend_request_processing(){
			
		}
		
		public function admin_insert_custom_data($new_object){
			$this->admin_link_to_cats($new_object->id);
			$this->admin_link_to_options($new_object->id);
		}
		
		public function admin_edit_custom_data(){
			$edited_id = (int)$_REQUEST['update_id'];
			
			$this->admin_link_to_cats($edited_id);
			$this->admin_link_to_options($edited_id);
		}
	}
?>