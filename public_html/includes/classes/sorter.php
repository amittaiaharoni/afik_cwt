<?
class sorter
{ 
    const SORT_DIRECTION_ASCENDING = 'asc';
    const SORT_DIRECTION_DESCENDING = 'desc';
 
    protected $_direction; 
    protected $_strategy;
 
    public function __construct(){
	
    }
	
	public static function sort_as_int($a, $b){
		if ($a == $b) {
			return 0;
		} 
		return $a > $b ? 1 : -1; 
	}
	
	public static function sort_as_string($a, $b){
		return strcmp($a, $b);
	}
	
	public static function sort_as_date($a, $b){
		if ($a == $b) {
            return 0;
        } 
        return $a > $b ? 1 : -1; 
	}
	
	public static function sort(&$objects, $sort_by = 'id', $dir = self::SORT_DIRECTION_ASCENDING){
		if (!empty($objects) && is_array($objects)){
			$first_obj = reset($objects);
			
			if (is_object($first_obj)){
				$class_name = get_class($first_obj);
				$fields_info = $first_obj->fields_info;
				$sort_by_field = false;
				$sort_function = "";
				$sort_function_class = "";
				foreach ($fields_info as $field){ // check if its a field
					if ($field['name'] == $sort_by || $field['name'] == $sort_by."_trans_id"){
						$is_translated_field = false;
						if ($field['name'] == $sort_by."_trans_id")
							$is_translated_field = true;
							
						$sort_function = "sort_as_string";
						$sort_function_class = "sorter";
						
						if (!$is_translated_field) // translated fields are strings
							switch ($field['type']){
								case "3": // int
									$sort_function = 'sort_as_int';
								break;
								case "253": // varchar
									$sort_function = 'sort_as_string';
								break;
								case "10": // date
									$sort_function = 'sort_as_date';
								break;
							}						
						$sort_by_field = true;
						break;
					}
				}
				
				// it will look for "sort_by_{sort_by}" function in object that is sorted
				if (empty($sort_function)){ // check if its a custom function
					if (in_array("sort_by_".$sort_by, get_class_methods($class_name))){
						$sort_function = "sort_by_".$sort_by;
						$sort_function_class = $class_name;
					}
				}
				
				if (!empty($sort_function)){
					$augmenter = ($dir == self::SORT_DIRECTION_DESCENDING) ? -1 : 1;	
					
					usort($objects, function($a, $b) use ($sort_function, $sort_function_class, $sort_by_field, $sort_by, $augmenter) {
						
						$res = 0;
						if ($sort_by_field)
							$res = $sort_function_class :: $sort_function($a->$sort_by, $b->$sort_by);
						else
							$res = $sort_function_class :: $sort_function($a, $b);
						return $res * $augmenter;
					});					
				}
				else{
					error_log("sorter : sorter function not found");
				}
			}
		}
	}
}
?>