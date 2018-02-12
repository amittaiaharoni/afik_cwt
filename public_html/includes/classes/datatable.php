<?php

class datatable_source {
	static function walk_object($obj, $path_str, $new_val = null){
		$val = null;

		$path = preg_split('/->/', $path_str);
		$last_prop = null;
		$prev_node = null;
		$node = $obj;
		while (($prop = array_shift($path)) !== null) {
			if (!is_object($node) || !isset($node->$prop)) {
				$val = null;
				return $val;
			}
			$val = $node->$prop;
			// TODO: Insert any logic here for cleaning up $val
			$prev_node = $node;
			$last_prop = $prop;
			$node = $node->$prop;
		}
		
		if (isset($new_val) && isset($prev_node->$last_prop)){ // used for updating data			
			$prev_node->$last_prop = $new_val;
			return $new_val;
		}
		
		return $val;
	}
	
	static function data_output ( $columns, $data )
	{
		$out = array();
		
		for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
			$row = array();
			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];
				
				$col_data = self::walk_object($data[$i], $column['path']);
								
				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
					$row[ $column['dt'] ] = $column['formatter']( $col_data, $data[$i] );
				}
				else {
					$row[$column['dt']] = $col_data;
				}			
			}
			$out[] = $row;
			
			// break;
		}
		return $out;
	}
	
	static function limit ( $request)
	{
		$limit = '';
		if ( isset($request['start']) && $request['length'] != -1 ) {
			$limit = intval($request['start']).", ".intval($request['length']);
		}
		return $limit;
	}
	
	static function order ( $request, $columns )
	{
		$order = '';
		if ( isset($request['order']) && count($request['order']) ) {
			$orderBy = array();
			$dtColumns = self::pluck( $columns, 'dt' );
			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
				// Convert the column index into the column data property
				$columnIdx = intval($request['order'][$i]['column']);
				$requestColumn = $request['columns'][$columnIdx];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				if ( $requestColumn['orderable'] == 'true' ) {
					$dir = $request['order'][$i]['dir'] === 'asc' ?
						'ASC' :
						'DESC';
					$orderBy[] = array("col_name" => $column['db'], "dir" => $dir);
				}
			}
		}
		return $orderBy;
	}
	
	static function filter ( $request, $columns)
	{
		$search = array();
		$globalSearch = array();
		$columnSearch = array();
		$dtColumns = self::pluck( $columns, 'dt' );
		
		if ( isset($request['search']) && $request['search']['value'] != '' ) {
			$str = $request['search']['value'];
			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
				$requestColumn = $request['columns'][$i];
				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
				$column = $columns[ $columnIdx ];
				if ( $requestColumn['searchable'] == 'true' ) {
					
					if (!empty($column['data_type'])){
						
						if ($column['data_type'] == "string"){
							if (!is_numeric($str))
								$globalSearch[] = array("col_name" => $column['db'], "condition" => "LIKE", "value" => "%".$str."%", "relation" => "OR");
						}
						else if ($column['data_type'] == "int"){
							if (is_numeric($str))
								$globalSearch[] = array("col_name" => $column['db'], "condition" => "=", "value" => $str, "relation" => "OR");						
						}
					}
					else{
						$globalSearch[] = array("col_name" => $column['db'], "condition" => "LIKE", "value" => "%".$str."%", "relation" => "OR");				
					}
				}
			}
		}
		
		// Individual column filtering
		for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
			$requestColumn = $request['columns'][$i];
			$columnIdx = array_search( $requestColumn['data'], $dtColumns );
			$column = $columns[ $columnIdx ];
			$str = $requestColumn['search']['value'];
			
			if ( $requestColumn['searchable'] == 'true' && $str != '' ) {
				$columnSearch[] = array("col_name" => $column['db'], "condition" => "LIKE", "value" => "%".$str."%", "relation" => "OR");
			}
		}
		
		// custom filter
		if ( isset($request['custom_search'])) {
			// error_log(print_r($request['custom_search'],true));
		}
		
		if ( count( $globalSearch ) ) {
			$search["groups"][] = array("columns" => $globalSearch);
		}
		if ( count( $columnSearch ) ) {
			$search["groups"][] = array("columns" => $globalSearch, "relation" => "AND");
		}
		
		return $search;
	}	
	
	static function pluck ( $a, $prop )
	{
		$out = array();
		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
			$out[] = $a[$i][$prop];
		}
		return $out;
	}
		
	static function get_data ($object_class_name, $columns, $added_filter = null){
		global $data;
		
		if (empty($object_class_name) || empty($columns))
			return array();
			
		// Build the SQL query string from the request
		$limit = self::limit( $_GET, $columns );
		$order = self::order( $_GET, $columns );
		$where = self::filter( $_GET, $columns );
		
		if (!emptY($_GET['custom_search'])){
			$found_ids = $data->$object_class_name->dt_custom_search($columns);
			// $found_ids = array(1,22,15,44);
			
			if (!empty($found_ids))
				$where["groups"][] = array("columns" => array(array("col_name" => "id", "condition" => "IN", "value" => "(".implode(",",$found_ids).")")), "relation" => "AND");
		}
		
		if (!empty($added_filter)){
			$where["groups"][] = $added_filter;
		}
		
		$recordsFiltered = 0;
		
		$ret_objects = $data->$object_class_name->search($where, $order, $limit, $recordsFiltered);
				
		// Total data set length
		$recordsTotal = $data->$object_class_name->get_total_count();		
		
		return array(
			"draw"            => intval( $_GET['draw'] ),
			"recordsTotal"    => intval( $recordsTotal ),
			"recordsFiltered" => intval( $recordsFiltered ),
			"data"            => self::data_output( $columns, $ret_objects )
		);
	}
	
	// static function get_data_custom_search ($object_class_name, $columns){
		// global $data;
		
		// if (empty($object_class_name) || empty($columns))
			// return array();
			
		// $limit = self::limit( $_GET, $columns );
		// $order = self::order( $_GET, $columns );
		// $where = self::filter( $_GET, $columns );
		
		// if (!emptY($_GET['custom_search'])){
			// $found_ids = $data->$object_class_name->dt_custom_search($columns);
			// $found_ids = array(1,22,15,44);
			
			// $where["groups"][] = array("columns" => array(array("col_name" => "id", "condition" => "IN", "value" => "(".implode(",",$found_ids).")")), "relation" => "AND");
		// }
		
		// $recordsFiltered = 0;
		
		// $ret_objects = $data->$object_class_name->search($where, $order, $limit, $recordsFiltered);
				
		// $recordsTotal = $data->$object_class_name->get_total_count();		
		
		// return array(
			// "draw"            => intval( $_GET['draw'] ),
			// "recordsTotal"    => intval( $recordsTotal ),
			// "recordsFiltered" => intval( $recordsFiltered ),
			// "data"            => self::data_output( $columns, $ret_objects )
		// );
	// }
	
	static function save_data ($object_class_name, $columns){
		global $data;
		
		if (empty($object_class_name) || empty($columns) || emptY($_POST['edited_field']) || !isset($_POST['edited_data']))
			return array();
		
		$edited_field_parts = explode("__", $_POST['edited_field']);	
		$field_dt_name = $edited_field_parts[0];
		$row_id = $edited_field_parts[1];
		
		$edited_object = $data->$object_class_name->get_by_id((int)$row_id);
		
		if (!empty($edited_object)){
			foreach ($columns as $col){
				if ($col['dt'] == $field_dt_name){
					$edited_data = &$edited_object;
					
					$new_value = self::walk_object($edited_data, $col['path'], $_POST['edited_data']);
					$edited_object->save();
					return $new_value;
					
					break;
				}
			}
		}		
	}	
}
