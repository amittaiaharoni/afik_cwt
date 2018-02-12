<?
	abstract class paymentType extends base_enum {
		const paypal 		= -2;
		const tranzila 		= -1;
		const call_me 		= 1;
		const credit_card 	= 2;
		
		public static function get_display_text($value) {
			switch ($value){
				case "-2":
					return "שילם בפאיפל";
				break;
				case "-1":
					return "שילם בטרנזילה";
				break;
				case "1":
					return "ביקש ליצור איתו קשר";
				break;
				case "2":
					return "השעיר פרטי אשרי";
				break;
				default:
					return self::get_key_for_value($value);
				break;
			}
		}
	}
?>