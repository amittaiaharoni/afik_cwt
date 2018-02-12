<?
class db_con{
	
	private static $class_instance = null; 
	private $con = null; 
	private $wcon = null; 
	
	private $db_host = ''; 
	private $db_name = ''; 
	private $db_reader_user = ''; 
	private $db_reader_pass = ''; 
	private $db_writer_user = ''; 
	private $db_writer_pass = ''; 
	
	protected function __construct(){
	
	}
	
	public static function open_con($db_host, $db_name, $db_reader_user, $db_reader_pass, $db_writer_user, $db_writer_pass) {
		if (self::$class_instance === null){
			self::$class_instance = new db_con();
			self::$class_instance->db_host 			= $db_host;
			self::$class_instance->db_name 			= $db_name;
			self::$class_instance->db_reader_user 	= $db_reader_user;
			self::$class_instance->db_reader_pass 	= $db_reader_pass;
			self::$class_instance->db_writer_user 	= $db_writer_user;
			self::$class_instance->db_writer_pass 	= $db_writer_pass;
		   
			self::$class_instance->connect();
		}
	}
	
	public static function get_con(){	
		if (empty(self::$class_instance))
			self::open_con(
								site_config::get_default_value("db_host"), 
								site_config::get_default_value("db_name"), 
								site_config::get_default_value("db_reader_user"), 
								site_config::get_default_value("db_reader_pass"), 
								site_config::get_default_value("db_writer_user"), 
								site_config::get_default_value("db_writer_pass")
							);
							
		return self::$class_instance;
	}
	
	public function real_escape_string($string){
		return $this->con->real_escape_string($string);
	}
	
	private function connect(){
		$this->con = mysqli_connect($this->db_host, $this->db_reader_user, $this->db_reader_pass, $this->db_name); 
		$this->wcon = mysqli_connect($this->db_host, $this->db_writer_user, $this->db_writer_pass, $this->db_name); 
			
		$this->con->set_charset("utf8");
		$this->wcon->set_charset("utf8");
	}
	
	public function query($sql, $is_writer = false){
		if (!empty($sql)){
			if ($is_writer){
				$res = $this->wcon->query($sql);
				$this->insert_id = $this->wcon->insert_id;
				return $res;
			}
			else
				return $this->con->query($sql);
		}
		return null;
	}
	
	public function get_table($table_name){
		if (!empty($table_name)){
			$sql = "SELECT * from `" . clear_string($table_name) . "` ORDER BY `id` ASC";
			return $this->fetchData($sql);
		}
		return null;
	}
	
	public function fetchData($sql){
		$res = array();
		if (!empty($sql)){
			if($result = $this->con->query($sql)){
				while($row = $result->fetch_assoc()){
					$res['rows'][] = $row;
				}
				
				$finfo = $result->fetch_fields();

				foreach ($finfo as $val) {
					$field = array();
					$field['name'] = $val->orgname;
					$field['table'] = $val->orgtable;
					$field['type'] = $val->type;
					$res['fields_info'][] = $field;
				}
				
				
				// FIELD TYPES
				
				// tinyint_    1
				// boolean_    1
				// smallint_   2
				// int_        3
				// float_      4
				// double_     5
				// real_       5
				// timestamp_  7
				// bigint_     8
				// serial      8
				// mediumint_  9
				// date_       10
				// time_       11
				// datetime_   12
				// year_       13
				// bit_       	16
				// decimal_    246
				// text_       252
				// tinytext_   252
				// mediumtext_ 252
				// longtext_   252
				// tinyblob_   252
				// mediumblob_ 252
				// blob_       252
				// longblob_   252
				// varchar_    253
				// varbinary_  253
				// char_       254
				// binary_     254
				
			}
		}
		return $res;
	}
}
?>