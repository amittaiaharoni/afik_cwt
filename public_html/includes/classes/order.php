<?
	class order_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "orders";
		const instance_class_name = "order";
		const cont_class_name = "order_controller";

		protected static $factory = null;
		protected static $instances;

		public static $order_details = array();
		public static $coupon;

		public static $order_enabled = true;

		protected function __construct() {
			parent::__construct(self::instance_class_name, self::db_table_name, self::cont_class_name);
		}
/////////////////////////////////////////////////////////////////////////
//	CUSTOM FUNCTIONS
		protected function extend_instance(&$instance){

		}

		protected function new_object_created(&$new_object){

		}

		public function dt_custom_search($columns){

		}
/////////////////////////////////////////////////////////////////////////

		public static function init_order(){
			global $data;
			global $defaults;

			$cart = cart::get_cart();

			if (!site_config::get_value("order_enabled"))
				self::$order_enabled = false;

			if (!empty($_SESSION['coupon_id'])){
				$coupon_id = $_SESSION['coupon_id'];

				$coupon = $data->coupon->get_by_id($coupon_id);

				if (!empty($coupon) &&
					!$coupon->used &&
					(empty($coupon->expiration_date) || $coupon->expiration_date > date('Y-m-d'))){

					self::$coupon = $coupon;
				}
			}

			if (!empty($_SESSION['order'])){
				$order_details = unserialize(base64_decode($_SESSION['order']));

				self::$order_details = $order_details;
			}
			else{
				self::$order_details["name"] = $data->user->get_user_detail("first_name") . " " . $data->user->get_user_detail("last_name");
				self::$order_details["city"] = $data->user->get_user_detail("city");
				self::$order_details["street"] = $data->user->get_user_detail("address");
				self::$order_details["email"] = $data->user->get_user_detail("email");
				self::$order_details["house_num"] = "";
				self::$order_details["appartment"] = "";
				self::$order_details["mobile"] = $data->user->get_user_detail("phone");
				self::$order_details["phone"] = $data->user->get_user_detail("phone");
				self::$order_details["send_date"] = "";
				self::$order_details["notes"] = "";

				self::$order_details["self_pickup"] = "0";
				self::$order_details["pickup_location"] = "";
				self::$order_details["pickup_date"] = "";
				self::$order_details["delivery_place"] = "";
				if(!empty($cart['shipping_price']))
					self::$order_details["shipping_price"] = $cart['shipping_price'];
				else
					self::$order_details["shipping_price"] = 0;
				self::$order_details["payment_type"] = ""; // patmentType class contain the enum. -1 = tranzila, 1 = call me, 2 = credit card
				self::$order_details["card_type"] = "";
				self::$order_details["card_number"] = "";
				self::$order_details["expire_year"] = "";
				self::$order_details["expire_month"] = "";
				self::$order_details["owner_tz"] = $data->user->get_user_detail("tz");
				self::$order_details["owner_name"] = "";
				self::$order_details["cvv"] = "";
				self::$order_details["num_of_payments"] = "";

				self::$order_details["rcph"] = "";
				self::$order_details["rcyl"] = "";
				self::$order_details["raxe"] = "";
				self::$order_details["lcph"] = "";
				self::$order_details["lcyl"] = "";
				self::$order_details["laxe"] = "";
				self::$order_details["pd"] = "";

				if (site_config::get_value("use_tranzila") && !site_config::get_value("use_paypal"))
					self::$order_details["payment_type"] = paymentType::tranzila;
				if (!site_config::get_value("use_tranzila") && site_config::get_value("use_paypal"))
					self::$order_details["payment_type"] = paymentType::paypal;
			}
		}

		public static function enabled(){
			return self::$order_enabled;
		}

		public static function add_coupon($coupon_code){
			global $data;
			// error_log('add coupon ');
			$coupon = $data->coupon->get_by_code($coupon_code);
			if (!empty($coupon) && !$coupon->used && (empty($coupon->expiration_date) || $coupon->expiration_date > date('Y-m-d')) && $coupon->multi == 0){

				self::$coupon = $coupon;
				$_SESSION['coupon_id'] = self::$coupon->id;
				if($coupon->multi == 0){
					$coupon->used = 1;
					$coupon->save();
				}
				cart::calculate_cart_price();
			}
			else if(!empty($coupon) && $coupon->multi == 1){
				self::$coupon = $coupon;
				$_SESSION['coupon_id'] = self::$coupon->id;
				cart::calculate_cart_price();
			}
			else{
				unset($_SESSION['coupon_id']);
				// cart::calculate_cart_price();
			}
		}

		public function get_coupon(){
			if (!empty(self::$coupon))
				return self::$coupon;
			return null;
		}
		
		public function drop_coupon(){
			if (!empty(self::$coupon)){
				error_log('Coupon '.print_r(self::$coupon,1));
				self::$coupon = null;
			}
		}

		public static function process_order_details(){
			global $data;

			if (!emptY($_POST)){
				foreach ($_POST as $key => $value){
					if (is_array($value)){

					}
					else{
						if (isset(self::$order_details[$key])){
							self::$order_details[$key] = $value;
						}
					}
					// error_log('Order: '.$key." => ".$value);
				}

				$shipping_price = 0;
				if (empty($_POST['self_pickup']) && !empty($_POST['delivery_place'])){
					$delivery_place = $data->deliveryPlace->get_by_id($_POST['delivery_place']);
					if (!empty($delivery_place)){
						//$shipping_price = $delivery_place->price;
					}
				}
				self::$order_details["shipping_price"] = $shipping_price;

				$_SESSION['order'] = base64_encode(serialize(self::$order_details));
			}
		}

		public static function execute_order(){
			global $data;
			global $defaults;

			$order_good = false;
			error_log('GUEST '.(!isset($_SESSION['guest']))?$data->user->loged_in_user->id:'0');
			if (!empty($data->user->loged_in_user) || isset($_SESSION['guest'])){
				if (!empty(self::$order_details)){
					$cart = cart::get_cart();

					if (!emptY($cart) && !empty($cart["prods"])){
						// $shipping_price = 0;
						// if (!isset($_POST['self_pickup']) && !empty($_POST['delivery_place'])){
							// $delivery_place = $data->deliveryPlace->get_by_id($_POST['delivery_place']);
							// if (!empty($delivery_place)){
								// $shipping_price = $delivery_place->price;
							// }
						// }

						$order_data = array();

						$shipping_price = self::get_order_detail("shipping_price");

						$order_data["user_id"] 			= (!isset($_SESSION['guest']))?$data->user->loged_in_user->id:'0';
						$order_data["prods_price"] 		= $cart["total_price"];
						$order_data["discount"] 		= $cart["discount"];
						$order_data["shipping_price"] 	= $shipping_price;
						$order_data["final_price"] 		= $cart["price_to_pay"];/* $cart["total_price_after_discount"] + $shipping_price; */
						$order_data["prepay_price"] 	= $cart["prepay_price"];
						$order_data["price_to_pay"] 	= $cart["price_to_pay"];// + $shipping_price;

						$payment_type = clear_string(self::get_order_detail('payment_type'));

						if (!empty($payment_type)){
							$order_data["payment_type"] = $payment_type;

							switch ($payment_type){
								case 1 : // call me

								break;
								case 2 : // credit card
									$order_data["card_type"] 		= clear_string(self::get_order_detail('card_type'));
									$order_data["card_number"] 		= clear_string(self::get_order_detail('card_number'));
									$order_data["expire_year"] 		= clear_string(self::get_order_detail('expire_year'));
									$order_data["expire_month"] 	= clear_string(self::get_order_detail('expire_month'));
									$order_data["card_owner_name"] 	= clear_string(self::get_order_detail('card_owner_name'));
									$order_data["card_owner_tz"] 	= clear_string(self::get_order_detail('card_owner_tz'));
									$order_data["cvv"] 				= clear_string(self::get_order_detail('cvv'));
									$order_data["num_of_payments"] 	= clear_string(self::get_order_detail('num_of_payments'));
								break;
							}
						}

						$order_data["notes"] = clear_string(self::get_order_detail('notes'));

						$self_pickup = (int)clear_string(self::get_order_detail('self_pickup'));

						if ($self_pickup){
							$order_data["self_pickup"] = 1;

							$order_data["pickup_location"] = clear_string(self::get_order_detail('pickup_location'));
							$order_data["pickup_date"] = clear_string(self::get_order_detail('pickup_date'));
						}
						else{

							$order_data["self_pickup"] = 0;

							$order_data["recipient_name"] = clear_string(self::get_order_detail('name'));

							$order_data["recipient_mobile"] = clear_string(self::get_order_detail('mobile'));

							$order_data["recipient_phone"] = clear_string(self::get_order_detail('phone'));

							$order_data["recipient_city"] = clear_string(self::get_order_detail('city'));
							$order_data["recipient_email"] = clear_string(self::get_order_detail('email'));

							$order_data["recipient_street"] = clear_string(self::get_order_detail('street'));

							$order_data["recipient_house"] = clear_string(self::get_order_detail('house_num'));

							$order_data["recipient_apartment"] = clear_string(self::get_order_detail('appartment'));

							$order_data["send_date"] = clear_string(self::get_order_detail('send_date'));
						}

						$order_data["serialized_order"] = clear_string($_SESSION['order']);
						$order_data["serialized_cart"] = clear_string($_SESSION['cart']);

						/*$order_sql = "INSERT INTO `orders`
							(`user_id`, `shipping_price`, `prods_price`, `discount`, `final_price`,
							`payment_type`, `card_type`, `expire_month`, `expire_year`, `card_number`,
							`card_owner_name`, `card_owner_tz`, `num_of_payments`, `cvv`,
							`notes`, `tranzila_response_code`, `tranzila_response_text`,
							`recipient_name`, `recipient_mobile`, `recipient_phone`, `recipient_city`,
							`recipient_street`, `recipient_house`, `recipient_apartment`, `send_date`, `self_pickup`,
							`pickup_location`, `pickup_date`, `new`, `zman`)
							VALUES
							(
							[value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],
							[value-9],[value-10],[value-11],[value-12],[value-13],[value-14],[value-15],[value-16],
							[value-17],[value-18],[value-19],[value-20],[value-21],[value-22],[value-23],
							[value-24],[value-25],[value-26],[value-27],[value-28],[value-29],[value-30],
							[value-31]
							)";*/

						$fields = " ( ";
						$values = " VALUES ( ";
						foreach ($order_data as $col_name => $value){
							$fields .= "`$col_name`, ";
							$values .= "'$value', ";
						}
						$fields = rtrim($fields, ', ') . ") ";
						$values = rtrim($values, ', ') . ") ";

						$order_sql = "INSERT INTO `orders` " . $fields . $values;
						// error_log($order_sql);
						$order_good = db_con::get_con()->query($order_sql, true);

						if ($order_good){
							$new_order_id = db_con::get_con()->insert_id;
							$_SESSION['last_order_id'] = $new_order_id;

					//////////////////////////////////////////////////////////////////////////////////////////////////////////
					////	ORDER INSERTED TO DB, SEND MAIL AND EMTY CART

							$cart_html = "";
							foreach ($cart["prods"] as $entry_id => $cart_entry){
								$cart_html .= '
									<div style="direction: rtl; border-bottom: 2px solid #00AEEF; padding: 2%; background: rgba(255,255,255,.5); margin-bottom:2%">
										<div style="display:inline-block;">
											<img style="max-width: 200px;" src="'.site_config::get_value("site_url").site_config::get_value("upload_images_folder").$cart_entry["prod_image"].'"/>
										</div>
										<div style="display:inline-block; padding-right:2%;">
											<h2>'.$cart_entry["prod_name"].'</h2>
											<h3>'.$cart_entry["prod_price"].'</h3><br />
											<b>כמות :</b> &nbsp;'.$cart_entry["quantity"].'
										</div>

										<div style="display:inline-block; vertical-align: top; padding-top: 1.5%; padding-right: 10%; ">
											<b>הערות :</b><br /> '.$cart_entry["comments"].'
										</div>';

										if (!emptY($cart_entry["prod_options"])){
											$cart_html .= '<div>';
											foreach ($cart_entry["prod_options"] as $det_data_id => $det_data){
												$cart_html .=
													'<div style="padding: 2%; border-bottom: 1px solid #CCCCCC;">
														'.$det_data["name"].'
														'.$det_data["price"].'
													</div>';
											}
											$cart_html .= '</div>';
										}

									$cart_html .=
										'<div style="text-align: roight; float:right; width: 50%; padding:2%"><h1>סה"כ:'.$cart_entry["total_price"].'</h1></div>
									</div>';
							}
							$cart_html .= '<div>
								<div style="text-align: roight; float:right; width: 50%; padding:2%"><h1>סה"כ:'.$cart["total_price"].'</h1></div>';
                            if(!empty($cart["shipping_added_price"]))
                            $cart_html .= '<div>
                                          <div style="text-align: roight; float:right; width: 50%; padding:2%"><h1>משלוח:'.$cart["shipping_added_price"].'</h1></div>';
							 if(!empty($cart["shipping_price"]))
                            $cart_html .= '<div>
                                          <div style="text-align: right; float:right; width: 50%; padding:2%"><h1>משלוח:'.$cart["shipping_price"].'</h1></div>';
                            if(!empty($cart["shipping_price"]))
                            $cart_html .= '<div>
								<div style="text-align: roight; float:right; width: 50%; padding:2%"><h1>סה"כ כולל משלוח":'.($cart["total_price"] += $cart["shipping_price"]).'</h1></div>';

								if (!empty($cart["discount"])){

									$cart_html .= '
										<div style="clear:both;"></div>
										<div style="text-align: roight; float:right; width: 50%; padding:2%">
											<h1>הנחת קופון: '.$cart["discount"].'</h1>
										</div>
										<div style="clear:both;"></div>
										<div style="text-align: roight; float:right; width: 50%; padding:2%">
											<h1>מחיר לאחר הנחה: '.$cart["price_to_pay"]/* $cart["total_price_after_discount"] */.'</h1>
										</div>';
								}
							$cart_html .= '
							</div>
							<div style="clear:both;"></div>';

							$message_to_admin = '
							<html>
								<head>
									<meta charset="utf-8"/>
									<title>
										התקבלה הזמנה חדשה
									</title>
								</head>
								<body>
									<div style="direction: rtl; text-align: right;">
										<div>
											שם המזמין : '.!isset($_SESSION['guest'])?($data->user->loged_in_user->first_name." ".$data->user->loged_in_user->last_name):'GUEST'.'
										</div>
										<div>
											<h1 style="text-align: right;">
												פרטי הזמנה
											</h1>

											'.$cart_html.'
										</div>
									</div>
								</body>
							</html>
							';

							$message_to_user = '
							<html>
								<head>
									<meta charset="utf-8"/>
									<title>
										הזמנתך התקבלה
									</title>
								</head>
								<body>
									<div>
										הזמנתך התקבלה
									</div>
									<div>
										<h1>
											פרטי הזמנה
										</h1>

										'.$cart_html.'
									</div>
								</body>
							</html>
							';
						if(!isset($_SESSION['guest'])){
							$to_user  = $data->user->loged_in_user->email;
							$subject_user = 'הזמנתך התקבלה - ' . site_config::get_value("site_name");
						}

							$to_admin  = site_config::get_value("site_contact_email");
							$subject_admin = 'התקבלה הזמנה חדשה';

							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
							$headers .= 'From: '.site_config::get_value("site_name").' <'.site_config::get_value("site_email_from").'>' . "\r\n";
							if(!isset($_SESSION['guest'])){
								mail($to_user, $subject_user, $message_to_user, $headers);
							}
							mail($to_admin, $subject_admin, $message_to_admin, $headers);
					////	MAIL SENT
					//////////////////////////////////////////////////////////////////////////////////////////////////////////

							// count sold products, for most sold
							foreach ($cart["prods"] as $entry_id => $cart_entry){
								$prod = $data->product->get_by_id($cart_entry['prod_id']);
								if (!empty($prod)){
									$prod->sold_count++;
									$prod->save();
								}
								$msg = '<html>
								<head>
									<meta charset="utf-8"/>
									<title>
										התקבלה הזמנה חדשה
									</title>
								</head>
								<body>
									<div style="direction: rtl; text-align: right;">';
								if(!empty($cart_entry['option_det_id'])){
									$det2prod = $data->details2product->get_by_id($cart_entry['option_det_id']);
									if(!empty($det2prod)){
										$color = $data->optionDetail->get_by_id($det2prod->option_det_id)->name;
										if($det2prod->stock <= 3){
											// Mail to admin
											//containing:
											//  1. product name
											//  2. the link to product
											//  3. text "כמות המוצר פחות מ 3"
											$subj = 'כמות המוצר פחות מ 3';
											$msg .= '<div>
														<span>שם מוצר:</span>
														<span>'.$prod->name.'</span>
													</div>
													<div>
														<span>צבע:</span>
														<span>'.$color.'</span>
													</div>
													<div>
														<span>כמות במלאי:</span>
														<span>'.$det2prod->stock.'</span>
													</div>
													<div>
														<span>לינק למוצר:</span>
														<span><a href="'.site_config::get_value('site_url').'index.php?page=product&prod_id='.$prod->id.'">העבר למוצר</a></span>
													</div>
													<div>
														<span>לינק לתוספות למוצר באדמין: </span>
														<span><a href="'.site_config::get_value('site_url').'index.php?page=admin_products_to_options&prod_id='.$prod->id.'">העבר למוצר</a></span>
													</div>
												</div>
												</body>
												</html>
													';
												$to = site_config::get_value('site_contact_email');
											mail($to,$subj,$msg,$headers);
										}
									}
								}
							}
							// error_log(print_r($cart,1));
							foreach ($cart["prods"] as $entry_id => $cart_entry){
								// error_log(print_r($cart_entry,1));
								$prod = $data->product->get_by_id($cart_entry['prod_id']);
								if (!empty($prod) && $prod->barcode == 'GIFT CARD'){
									// Create new coupon as gift card with amount $prod->price
									if($data->coupon->create_giftcard($prod->price)){
										$data->order->send_giftcard_mail($data->coupon->get_last_giftcard(),$cart_entry['gift_receiver_email']);
									}
								}
							}

							//cart::empty_cart(); // empty the cart

							/* unset($_SESSION['coupon_id']);
							unset($_SESSION['order']);
							unset($_SESSION['credit_amount']);
 */
							if (!emptY(self::$coupon)){
								if(self::$coupon->multi == 0)
									self::$coupon->used = 1;
								self::$coupon->save();
								self::$coupon = null;
							}

							self::$order_details = null;
							
					////	EMPTY CART
					//////////////////////////////////////////////////////////////////////////////////////////////////////////
						}
					}
				}
			}
			return $order_good;
		}

		public static function get_order_detail($key){
			if (!empty(self::$order_details[$key])){
				return self::$order_details[$key];
			}
			return "";
		}
		
		private function send_giftcard_mail($coupon_id, $to){
			global $data;
			// error_log('Coupon code: '.$to);
			mail($to,'GIFT CARD',$data->coupon->get_by_id($coupon_id)->number);
		}

	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class order extends base_data_object{

		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);
		}

		protected function extend_deletion(){

		}

		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class order_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "orders";
			if (!empty($_GET['user_id'])){
				$this->url_page_name .= "&user_id=" . (int)$_GET['user_id'];
			}

			$this->object_class_name = "order";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class order_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "orders";
			$this->object_class_name = "order";
			$this->view_class_name = "order_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
				array( 'db' => 'user_id', 		'dt' => 'name',			'path' => "user_id",
						'formatter' =>
							function( $value, $object ) {
								global $data;
								$user = $data->user->get_by_id($object->user_id);
								if (!empty($user))
									return ($user->first_name . " " . $user->last_name);
								else{
									if($value == 0){
										return 'הזמנה של אורח';
									}
								}
								return $value;
							}),
				array( 'db' => 'final_price', 	'dt' => 'final_price',	'path' => "final_price"),
				array( 'db' => 'created_date', 	'dt' => 'created_date','path' => "created_date",
						'formatter' =>
							function( $value, $object ) {
								return date( 'd-m-Y', strtotime($value));
							}
						),
				array( 'db' => 'user_id', 'dt' => 'phone', 'path' => "user_id",
						'formatter' =>
							function( $value, $object ) {
								// error_log(print_r($object,1));
								global $data;
								$user = $data->user->get_by_id($object->user_id);
								if (!empty($user))
									return $user->phone;
								else{
									$order = unserialize(base64_decode($object->serialized_order));
									if(!empty($order['phone'])){
										return $order['phone'];
									}
									else{
										return $order['mobile'];
									}
								}
								
								return $value;
							})
				// array(
					// 'db'        => 'start_date',
					// 'dt'        => 4,
					// 'formatter' => function( $d, $row ) {
						// return date( 'jS M y', strtotime($d));
					// }
				// )
			);

			if (!empty($_GET['user_id'])){
				$where = array();
				$where = array("columns" => array(array("col_name" => "user_id", "condition" => "=", "value" => (int)$_GET['user_id'])), "relation" => "AND");

				$this->dt_data_source_added_filter = $where;
			}
		}

		public function extend_request_processing(){
            if(!empty($_POST['action'])){
                /*switch () {
                case 'send_finish_orders_mails':
                        cart::send_mail_to_finish();
                break;

                default:

                    break;
                }*/
            }

		}

		public function admin_insert_custom_data($new_object){

		}

		public function admin_edit_custom_data(){

		}
	}
?>
