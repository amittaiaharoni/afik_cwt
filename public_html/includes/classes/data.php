<?
	class data{
		protected static $factories;
		
		function __construct() {
					
		}
		
		public function __get($name){
			$class_name = $name."_factory";
			if (class_exists($class_name)){
				// $factory = null;
									
				// if (!empty(self::$factories[$name]))
					// $factory = self::$factories[$name];
				// else
					$factory = $class_name::get_factory();
					
				return $factory;
			}
			else if (class_exists($name)){
				$factory = $name::get_factory();
					
				return $factory;
			}
			else{
				error_log("data: ".$name." Factory class not found");
				return null;
			}
		}
	}
?>