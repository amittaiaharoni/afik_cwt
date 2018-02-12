<?
	abstract class base_factory{
		protected $class_name;
		protected $cont_class_name;
		protected $db_table_name;
		protected $db_table_info;
		protected $con;
		protected $all_objects_loaded = false;

		public $controller = null;

		// protected static $instances;

		protected function __construct($class_name, $db_table_name, $cont_class_name = null) {
			global $data;
			$this->data = $data;
			$this->class_name 		= $class_name;
			$this->cont_class_name 	= $cont_class_name;
			$this->db_table_name 	= $db_table_name;
			$this->con 				= db_con::get_con();

			// get table info
			$q_table_info = "DESCRIBE `{$db_table_name}`";
			$result = $this->con->fetchData($q_table_info);

			if (!empty($result['rows'])){
				$this->db_table_info = array();

				foreach ($result['rows'] as $row){
					$this->db_table_info[$row['Field']] = $row['Type'];
				}
			}
			/////////////////

			if (!empty($this->cont_class_name)){
				$this->controller = new $this->cont_class_name();
			}
		}

		public function get_db_table_name(){
			return $this->db_table_name;
		}

		public function get_db_table_info(){
			return $this->db_table_info;
		}

		public static function get_factory(){
			if (static::$factory == null){
				static::$factory = new static();
			}
			return static::$factory;
		}

		protected abstract function extend_instance(&$instance); // used to extend the object, for example: add options to product, see in product.php

		public abstract function dt_custom_search($columns); // used to implement custom search in the admin page, return array of matcing ID's

		protected abstract function new_object_created(&$new_object); // used to perform additional actions after new object was created

		public function add_new($data){ // expect data as key value pairs, where key is the name of the db value

			if (!empty($data)){
				if (!emptY($this->db_table_info)){
					$insert_sql = "INSERT INTO `{$this->db_table_name}` ";
					$fields = "(";
					$values = "(";
					foreach ($this->db_table_info as $field => $type){

						if (!empty($data[$field])){
							$trans_key = "";
							if (substr($field, -9) === "_trans_id"){ // check if its a translatable field
								$trans_key = $this->data->translator->get_max_id() + 1;
								$translator = $this->data->translator->add_new(
									array(
										"key" => $trans_key,
										"value" => $data[$field],
										"lang" => language::$current_lang
									));
							}

							$value = "";
							if (!empty($trans_key))
								$value = $trans_key;
							else
								$value = clear_string($data[$field]);

							$fields .= "`".$field."`,";
							$values .= "'".$value."',";
						}
					}
					if ($fields != "("){
						$fields = rtrim($fields, ',') . ")";
						$values = rtrim($values, ',') . ")";

						$insert_sql .= $fields . " VALUES " . $values;

						if ($this->con->query($insert_sql, true)){
							$new_id = $this->con->insert_id;
							$new_object = $this->get_by_id($new_id);
							$this->new_object_created($new_object);
							return $new_object;
						}
					}
				}
			}
			return null;
		}

		protected function create_instance($data, $fields_info = null){

			if (!empty($data)){
				$class_name = $this->class_name;
				$instance = $class_name::get_instance($data, $fields_info, $this->db_table_name);
				$this->extend_instance($instance);
				return $instance;
			}
			return null;
		}

		public function get_constant_filter_string(){
			global $is_admin;

			$ret = "";

			if ($is_admin) return $ret;

			if (!empty($this->constant_filters)){
				foreach ($this->constant_filters as $filter){
					if (!empty($ret))
						$ret .= " ".$filter['relation']." ";
					$ret .= " `".$filter['col_name']."` ".$filter['condition']." '".$filter['value']."' ";
				}
			}
			if (!empty($ret))
				$ret = " (".$ret.") ";
			return $ret;
		}

		public function search($filter, $order, $limit, &$found_rows = null){
			// $start = microtime(true);

			$res = array();
			$use_text_filter = false;
			$use_numeric_filter = false;
			$orderby_trans_used = false;
			$order_by = "";

			$trans_fields = " dt_table.* ";
			$trans_from = "";
			$trans_where = "";

			$num_fields = " * ";
			$num_from = " `".$this->db_table_name . "` ";
			$num_where = "";

			$order_fields = "";
			$order_fields_count = 0;
			$order_from = "";
			$order_from_for_num_filter = "";
			$order_where = "";

			if (!empty($filter) && !empty($filter["groups"])){
				foreach ($filter["groups"] as $filter_group){
					$trans_where_group = "";
					$num_where_group = "";

					foreach($filter_group["columns"] as $col){
						$col_new_name = "";
						if (substr($col["col_name"], -9) === "_trans_id"){ // check if its a translatable field
							$use_text_filter = true;
							$col_new_name = str_replace("_trans_id", "", $col["col_name"]);

							$trans_from	.= " LEFT JOIN (
											SELECT
												*
											FROM
												`" . translator::db_table_name ."` as tt_$col_new_name
											WHERE
												(tt_$col_new_name.lang = 1 OR tt_$col_new_name.lang is null)
												AND
												tt_$col_new_name.`value` " . $col["condition"] . " '" . clear_string($col["value"]) . "'
											) as t_$col_new_name
										on dt_table.".$col["col_name"]." = t_$col_new_name.key ";

							$trans_fields .= ", t_$col_new_name.value as '$col_new_name' ";
							$num_fields .= ", null ";

							if (!empty($col["relation"]) && !empty($trans_where_group))
								$trans_where_group .= " " . $col["relation"] . " ";
							$trans_where_group .= " t_$col_new_name.id is not null ";

							$joined_translations[] = "t_$col_new_name";
						}
						else{
							$use_numeric_filter = true;

							if (!empty($col["relation"]) && !empty($num_where_group))
								$num_where_group .= " " . $col["relation"] . " ";

							if ($col["value"][0] == "(" && trim($col["value"][strlen($col["value"])-1]) == ")") // values like " IN (1,2,3,4) " dont need quote
								$num_where_group .= " dt_table.`" . $col["col_name"] . "` " . $col["condition"] . " " . clear_string($col["value"]) . " ";
							else
								$num_where_group .= " dt_table.`" . $col["col_name"] . "` " . $col["condition"] . " '" . clear_string($col["value"]) . "' ";
						}
					}

					if (!empty($trans_where_group)){
						$trans_where_group = " ( " . $trans_where_group . " ) ";

						if (!empty($filter_group["relation"]) && !empty($trans_where))
							$trans_where .= " " . $filter_group["relation"] . " ";

						$trans_where .= $trans_where_group;
					}
					if (!empty($num_where_group)){
						$num_where_group = " ( " . $num_where_group . " ) ";

						if (!empty($filter_group["relation"]) && !empty($num_where))
							$num_where .= " " . $filter_group["relation"] . " ";

						$num_where .= $num_where_group;
					}

				}
			}

			$constant_filter = $this->get_constant_filter_string();
			if (!empty($constant_filter)){
				if (!emptY($num_where))
					$num_where .= " AND ";
				if (!emptY($trans_where))
					$trans_where .= " AND ";
				$num_where .= " $constant_filter ";
				$trans_where .= " $constant_filter ";
			}

			// if (!empty($this->contant_filters)){
				// foreach ($this->contant_filters as $filters_field_name => $filters_field_value){
					// $num_where .= " AND `$filters_field_name` = " . $filters_field_value;
					// $trans_where .= " AND `$filters_field_name` = " . $filters_field_value;
				// }
			// }

			if (!empty($order)){
				foreach ($order as $col){
					if (!empty($order_by))
						$order_by .= ", ";

					if (substr($col["col_name"], -9) === "_trans_id"){ // check if its a translatable field

						$col_new_name = str_replace("_trans_id", "", $col["col_name"]);
						$order_by .= $col_new_name . " " . $col["dir"];

						if (empty($joined_translations) || !in_array("t_$col_new_name", $joined_translations)){
							$orderby_trans_used = true;

							/*
							$order_from	.= " LEFT JOIN (
											SELECT
												*
											FROM
												`" . translator::db_table_name ."` as tt_$col_new_name
											WHERE
												(tt_$col_new_name.lang = 1 OR tt_$col_new_name.lang is null)
											) as t_$col_new_name ";
							*/

							$order_from	.= " LEFT JOIN `" . translator::db_table_name ."` as t_$col_new_name ";

							$order_from_for_num_filter = $order_from;

							$order_from .= 					"on dt_table.".$col["col_name"]." = t_$col_new_name.key ";
							$order_from_for_num_filter .= 	"on num_table.".$col["col_name"]." = t_$col_new_name.key ";	 // diferent table alias

							$order_where = " (t_$col_new_name.lang = 1 OR t_$col_new_name.lang is null) ";

							$order_fields .= ", t_$col_new_name.value as '$col_new_name' ";
							$order_fields_count++;

							$joined_translations[] = "t_$col_new_name";
						}
					}
					else
						$order_by .= $col["col_name"] . " " . $col["dir"];
				}
			}

			if ($use_text_filter && $use_numeric_filter){
				// error_log("use_text_filter and use_numeric_filter");
				for ($i = 0; $i < $order_fields_count; $i++){
					$num_fields .= ", null";
				}

				$sql = "
					SELECT
						".(!empty($limit)?"SQL_CALC_FOUND_ROWS":"")."
						*
					FROM
					(
						(
							SELECT
								$trans_fields
								$order_fields
							FROM
								`".$this->db_table_name . "` as dt_table
								$trans_from
								$order_from
							".(!empty($trans_where)||!empty($order_where)?" WHERE ":"")."
							".(!empty($trans_where)?$trans_where:"")."
							".(!empty($order_where)?" AND ".$order_where:"")."
						)
						UNION ALL
						(
							SELECT $num_fields FROM `".$this->db_table_name . "` as dt_table
							".(!empty($num_where)?" WHERE ".$num_where:"")."
						)
					) as filtered_table
					".(!empty($order_by)?" ORDER BY ".$order_by:"")."
					".(!empty($limit)?" LIMIT ".$limit:"");
			}
			else if ($use_text_filter){
				// error_log("use_text_filter");
				$sql = "
					SELECT
						".(!empty($limit)?"SQL_CALC_FOUND_ROWS":"")."
						$trans_fields
						$order_fields
					FROM
						`".$this->db_table_name . "` as dt_table
						$trans_from
						$order_from
					".(!empty($trans_where)||!empty($order_where)?" WHERE ":"")."
					".(!empty($trans_where)?$trans_where:"")."
					".(!empty($order_where)?" AND ".$order_where:"")."
					".(!empty($order_by)?" ORDER BY ".$order_by:"")."
					".(!empty($limit)?" LIMIT ".$limit:"");
			}
			else if ($use_numeric_filter){
				if ($orderby_trans_used){
					// error_log("use_numeric_filter - orderby_trans_used");
					$sql = "
						SELECT
							".(!empty($limit)?"SQL_CALC_FOUND_ROWS":"")."
							*
							$order_fields
						FROM
							(
								SELECT
									$num_fields
								FROM
									`".$this->db_table_name . "` as dt_table
								".(!empty($num_where)?" WHERE ".$num_where:"")."
							) as num_table
							$order_from_for_num_filter
						".(!empty($order_where)?" WHERE ".$order_where:"")."
						".(!empty($order_by)?" ORDER BY ".$order_by:"")."
						".(!empty($limit)?" LIMIT ".$limit:"");
				}
				else{
					// error_log("use_numeric_filter - NO orderby_trans_used");
					$sql = "
						SELECT
							".(!empty($limit)?"SQL_CALC_FOUND_ROWS":"")."
							$num_fields
						FROM
							`".$this->db_table_name . "` as dt_table
						".(!empty($num_where)?" WHERE ".$num_where:"")."
						".(!empty($order_by)?" ORDER BY ".$order_by:"")."
						".(!empty($limit)?" LIMIT ".$limit:"");
				}
			}
			else{
				if ($orderby_trans_used){
					// error_log("default - orderby_trans_used");
					$sql = "
						SELECT
							".(!empty($limit)?"SQL_CALC_FOUND_ROWS":"")."
							dt_table.*
							$order_fields
						FROM
							`".$this->db_table_name . "` as dt_table
							$order_from
						".(!empty($order_where)?" WHERE ".$order_where:"")." ";

						$constant_filter = $this->get_constant_filter_string();
						if (!empty($constant_filter)){
							if(empty($order_where))
								$sql .= " WHERE ";
							else
								$sql .= " AND ";
							$sql .= " $constant_filter ";
						}

						$sql .= (!empty($order_by)?" ORDER BY ".$order_by:"");
						$sql .= (!empty($limit)?" LIMIT ".$limit:"");
				}
				else{
					// error_log("default - NO orderby_trans_used");
					$sql = "
					SELECT
						".(!empty($limit)?"SQL_CALC_FOUND_ROWS":"")."
						*
					FROM
						`".$this->db_table_name . "` ";

					$constant_filter = $this->get_constant_filter_string();
					if (!empty($constant_filter)){
						$sql .= " WHERE $constant_filter ";
					}

					$sql .= (!empty($order_by)?" ORDER BY ".$order_by:"");
					$sql .= (!empty($limit)?" LIMIT ".$limit:"");
				}
			}

			// $time_elapsed_us = microtime(true) - $start;
			// error_log("search sql ready: " . $time_elapsed_us);

			// error_log($sql);
			$result = $this->con->fetchData($sql);

			// $time_elapsed_us = microtime(true) - $start;
			// error_log("search sql executed: " . $time_elapsed_us);

			if (!empty($result['rows'])){
				$resFilterLength = $this->con->fetchData("SELECT FOUND_ROWS() as 'found_rows'");
				$found_rows = $resFilterLength['rows'][0]['found_rows'];

				foreach ($result['rows'] as $row){
					if (!empty(static::$instances['by_id'][$row['id']])){
						$res[] = static::$instances['by_id'][$row['id']];
					}
					else{
						$instance = $this->create_instance($row, $result['fields_info']);

						if (!empty($instance)){
							static::$instances['by_id'][$row['id']] = $instance;
						}
						$res[] = $instance;
					}
				}
				// $time_elapsed_us = microtime(true) - $start;
				// error_log("search objects ready: " . $time_elapsed_us);
			}
			return $res;

			/*
			SAMPLE FULL SEARCH SQL

			SELECT
				SQL_CALC_FOUND_ROWS
				*
			FROM
			(
				(
					SELECT
						dt_table.* ,
						t_name.value as 'name'
					FROM
						`products` as dt_table
						LEFT JOIN
						(
							SELECT
								*
							FROM
								`translations` as tt_name
							WHERE
								(tt_name.lang = 1 OR tt_name.lang is null)
								AND
								tt_name.`value` LIKE '%in%'
						) as t_name
						on dt_table.name_trans_id = t_name.key
					WHERE
						(  t_name.id is not null  )
						AND
						`hide_it` = 0
				)
				UNION ALL
				(
					SELECT *, null FROM `products`
					WHERE
						(
							`id` = '3359'  OR
							`id` = '3359'  OR
							`barcode` = '3359'  OR
							`price` = '3359'  OR
							`id` = '3359'  OR
							`id` = '3359'
						)
						AND
						`hide_it` = 0
				)
			) as filtered_table
			ORDER BY
				id DESC
			LIMIT 0, 20

			*/
		}

		public function get_by_column($column_name, $value, $is_unique_selector = false){
			$res = array();
			if (isset($value)){
				// check if already loaded and return if exist.
				if (!empty(static::$instances['by_'.$column_name][$value]))
					return static::$instances['by_'.$column_name][$value];

				$sql = "SELECT * FROM `".$this->db_table_name."` WHERE `".$column_name."` = '".clear_string($value)."'";

				$constant_filter = $this->get_constant_filter_string();
				if (!empty($constant_filter)){
					$sql .= " AND $constant_filter ";
				}
				// if (!empty($this->contant_filters)){
					// foreach ($this->contant_filters as $filters_field_name => $filters_field_value){
						// $sql .= " AND `$filters_field_name` = " . $filters_field_value;
					// }
				// }

				if (empty($sql)) return $res;
				$result = $this->con->fetchData($sql);

				if (!empty($result['rows'])){
					foreach ($result['rows'] as $row){
						// check if already loaded and return if exist
						if (!emptY(static::$instances['by_id'][$row['id']])){
							$res[] = static::$instances['by_id'][$row['id']];
						}
						else{
							// create a new instance
							$instance = $this->create_instance($row, $result['fields_info']);

							if (!empty($instance)){
								static::$instances['by_id'][$row['id']] = $instance;
								if ($is_unique_selector){
									if ($column_name != "id")
										static::$instances['by_'.$column_name][$value] = &static::$instances['by_id'][$row['id']]; // store instance by reference
									return $instance; // is_unique_selector = true : there is only one instanse
								}else
									static::$instances['by_'.$column_name][$value][] = &static::$instances['by_id'][$row['id']]; // store instance by reference
							}
							$res[] = $instance;
						}
					}
				}
			}
			return $res;
		}

		public function get_by_id($id){
			if (isset($id)){
				return $this->get_by_column("id", (int)$id, true);
			}
			return null;
		}

		public function get_all(){
			$res = array();
			if ($this->all_objects_loaded && !empty(static::$instances['by_id']))
				return static::$instances['by_id'];

			$res = static::search("","","");

			if (!empty($res))
				$this->all_objects_loaded = true;

			return $res;
		}

		public function get_total_count(){
			$sql = "SELECT COUNT(`id`) as 'count' FROM   `{$this->db_table_name}` ";

			$constant_filter = $this->get_constant_filter_string();
			if (!empty($constant_filter)){
				$sql .= " AND $constant_filter ";
			}
			// if (!empty($this->contant_filters)){
				// foreach ($this->contant_filters as $filters_field_name => $filters_field_value){
					// $sql .= " AND `$filters_field_name` = " . $filters_field_value;
				// }
			// }

			if ($res = db_con::get_con()->fetchData($sql))
				return $res['rows'][0]['count'];
			return 0;
		}

		public function get_max_id(){
			$sql = "SELECT MAX(`id`) as 'max_id' FROM   `{$this->db_table_name}` ";

			$constant_filter = $this->get_constant_filter_string();
			if (!empty($constant_filter)){
				$sql .= " AND $constant_filter ";
			}
			// if (!empty($this->contant_filters)){
				// foreach ($this->contant_filters as $filters_field_name => $filters_field_value){
					// $sql .= " AND `$filters_field_name` = " . $filters_field_value;
				// }
			// }

			if ($res = db_con::get_con()->fetchData($sql))
				if (!empty($res['rows'][0]['max_id']))
					return $res['rows'][0]['max_id'];

			return 1;
		}
	}

	abstract class base_data_object{
		public $fields_info;
		protected $data;
		protected $data_fields;
		protected $child_class_name;
		protected $translation_key;
		protected $db_table_name;

		protected function __construct($data_fields, $fields_info, $db_table_name) {
			global $data;
			$this->data = $data;

			$this->fields_info = $fields_info;
			$this->db_table_name = $db_table_name;

			if (!empty($data_fields)){
				$this->child_class_name = get_class($this);
				// $this->translation_key = $this->child_class_name . "-" . $data['id'];
				foreach ($data_fields as $key => $value){
					// if its a translatable field look for a translation in a db
					// if (substr($key, -9) === "_trans_id"){
						// $key = str_replace("_trans_id", "", $key);
						// $this->data_fields[$key] = translator::get_translator($value);

					// }else
						$this->data_fields[$key] = $value;
				}
			}
		}

		protected abstract function extend_deletion();

		public static function get_instance($data_fields, $fields_info, $db_table_name){
			return new static($data_fields, $fields_info, $db_table_name);
		}

		public function __isset($key){
			// if (isset($this->data_fields[$key]))
			if (array_key_exists ($key, $this->data_fields) || array_key_exists ($key."_trans_id", $this->data_fields))
				return true;
			return false;
		}

		public function __get($key){
			if (isset($this->data_fields[$key."_trans_id"])){
				$this->data_fields[$key] = translator::get_translator($this->data_fields[$key."_trans_id"]);
				return $this->data_fields[$key];
				unset($this->data_fields[$key."_trans_id"]);
			}
			else if (isset($this->data_fields[$key]))
				return $this->data_fields[$key];
			return null;
		}

		public function __set($key, $value){
			if (!empty($this->data_fields[$key]) &&
				is_object($this->data_fields[$key]) &&
				$this->data_fields[$key] instanceof translator_object){ // translator

				$this->data_fields[$key]->value = $value;
			}
			// its a translated field, but the translatend wasnt loaded
			else if (isset($this->data_fields[$key."_trans_id"])){
				$trans_key = $this->data_fields[$key."_trans_id"];

				if (empty($trans_key)){ // there is no trans key - maybe this row was manualy added to db - add a translator
					$trans_key = $this->data->translator->get_max_id() + 1;
					$translator = $this->data->translator->add_new(
						array(
							"key" => $trans_key,
							"value" => "",
							"lang" => language::$current_lang
						));
					$this->data_fields[$key."_trans_id"] = $trans_key;
					$this->save();
				}

				$this->data_fields[$key] = translator::get_translator($this->data_fields[$key."_trans_id"]);
				if (!empty($this->data_fields[$key]))
					$this->data_fields[$key]->value = $value;
				unset($this->data_fields[$key."_trans_id"]);
			}
			else{
				$this->data_fields[$key] = $value;
			}
		}

		public function save(){

			if (!empty($this->data_fields)){
				$table_name = $this->db_table_name;
				$where = " `id` = '" . (int)$this->data_fields['id'] . "'";
				$fields = "";

				foreach ($this->data_fields as $data_field_name => $data_value){
					if ($data_field_name == 'id') continue;

					if (is_object($data_value)){ // its a translator... or something
						if ($data_value instanceof base_data_object){
							$data_value->save();
						}
					}
					else if (is_array($data_value)){// its an array of translators... or something
						foreach ($data_value as $subdata){
							if ($subdata instanceof base_data_object){
								$subdata->save();
							}
						}
					}
					else {
						foreach ($this->fields_info as $field_info){ // check if there is a field with this name in the db

							if (substr($field_info['name'], -9) === "_trans_id" &&
								str_replace("_trans_id", "", $field_info['name']) == $data_field_name &&
								(empty($data_value) || !is_object($data_value))){ // translator was not created on insert, create it now

								$trans_key = $this->data->translator->get_max_id() + 1;
								$translator = $this->data->translator->add_new(
									array(
										"key" => $trans_key,
										"value" => $data_value,
										"lang" => language::$current_lang
									));

								if (!empty($fields))
									$fields .= " , ";

								$fields .= " `".$field_info['name']."` = '".clear_string($trans_key)."' ";
							}
							else if ($field_info['name'] == $data_field_name){
								if (!empty($fields))
									$fields .= " , ";

								$fields .= " `".$data_field_name."` = '".clear_string($data_value)."' ";

								break;
							}
						}
					}
				}

				if (!empty($fields)){
					$sql = "UPDATE {$table_name} SET {$fields} WHERE {$where}";
					// error_log("save sql : " . $sql);
					db_con::get_con()->query($sql, true);
				}
			}
		}

		public function delete(){
			global $defaults;

			if (!empty($this->data_fields)){
				foreach ($this->data_fields as $data_field_name => $data_value){
					if (is_object($data_value)){ // its a translator
						if ($data_value instanceof translator_object){
							$data_value->delete(); // don't keep old translations
						}
					}
					else if (is_array($data_value)){// its an array of translators
						foreach ($data_value as $subdata){
							if ($subdata instanceof translator_object){
								$subdata->delete(); // don't keep old translations
							}
						}
					}
					else if (!empty($data_value) && file_exists(site_config::get_value('upload_files_folder').$data_value)){ // its a file - remove it
						unlink(site_config::get_value('upload_files_folder').$data_value);
					}
					else if (!empty($data_value) && file_exists(site_config::get_value('upload_images_folder').$data_value)){ // its an image - remove it
						unlink(site_config::get_value('upload_images_folder').$data_value);
					}
					else if (!empty($data_value) && file_exists(site_config::get_value('upload_thumbs_folder').$data_value)){ // its a thumb - remove it
						unlink(site_config::get_value('upload_thumbs_folder').$data_value);
					}
				}

				$sql = "DELETE FROM {$this->db_table_name} WHERE `id` = '{$this->data_fields['id']}'";
				// error_log($sql);
				$this->extend_deletion();
				unset($this);
				return db_con::get_con()->query($sql, true);
			}
			return false;
		}
	}

	abstract class base_enum {
		private static $constCacheArray = NULL;

		private static function getConstants() {
			if (self::$constCacheArray == NULL) {
				self::$constCacheArray = array();
			}
			$calledClass = get_called_class();
			if (!array_key_exists($calledClass, self::$constCacheArray)) {
				$reflect = new ReflectionClass($calledClass);
				self::$constCacheArray[$calledClass] = $reflect->getConstants();
			}
			return self::$constCacheArray[$calledClass];
		}

		public static function is_valid_name($name, $strict = false) {
			$constants = self::getConstants();

			if ($strict) {
				return array_key_exists($name, $constants);
			}

			$keys = array_map('strtolower', array_keys($constants));
			return in_array(strtolower($name), $keys);
		}

		public static function is_valid_value($value) {
			$values = array_values(self::getConstants());
			return in_array($value, $values, $strict = true);
		}

		public static function get_key_for_value($value) {
			$values = array_values(self::getConstants());
			$key = array_search($value, $values);
			if ($key)
				return $key;
			return "";
		}

		public static function get_dispaly_text($value){
			return self::get_key_for_value($value);
		}
	}
?>
