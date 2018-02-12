<?	
	class json{
		public static function array_int_closure($value, $key){
			return self::encode($value);
		}
		public static function array_closure($value, $key){
			return self::encode((string)$key) . ':' . self::encode($value);
		}
		
		 public static function encode( $data ) {
			if( is_array($data) && is_int(key($data)) ) {
				return '[' . self::implode($data, ',', false) . ']';
			}
			elseif( is_array($data) ) {
				return '{' . self::implode($data, ',', true) . '}';
			}
			elseif( is_object($data) ) {
				if (method_exists($data, "to_json"))
					return $data->to_json();
				return self::encode(get_object_vars($data));
				
				// return $data instanceof ISerializable 
					// ? $data->asSerializable()
					// : self::encode(get_object_vars($data));
			}
			else {
				return json_encode($data);
			}
		}
		public static function implode(array $arr, $delimiter=',', $use_key=false ) {
			// $callback   = $callback ?: function($value,$key) { return $value; };
			$result     = '';
			foreach( $arr AS $key => $value ) {
				$result .= (empty($result) ? '' : $delimiter);
				if ($use_key)
					$result .= self::encode((string)$key) . ':' . self::encode($value);
				else
					$result .= self::encode($value);
			}
			return $result;
		}
	}
?>