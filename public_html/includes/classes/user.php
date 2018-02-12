<?
	class user_factory extends base_factory{
//////////////////////////////////////////////////////////////////////////
//	BASIC FUNCTIONALITY
		const db_table_name = "users";
		const instance_class_name = "user";
		const cont_class_name = "user_controller";

		protected static $factory = null;
		protected static $instances;

		public $loged_in = false;
		public $admin_loged_in = false;
		public $loged_in_user = false;

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

		public function register(){
			global $site_message;
			if (isset($_POST['rank'])){
				unset($_POST['rank']);
				error_log("user tried to set his rank");
				error_log(print_r($_SERVER,true));
			}
            if(empty($_POST['email']) && isset($_POST['username']))
                $_POST['email'] = $_POST['username'];

            $user = $this->get_by_column("email",clear_string($_POST['email']),true);
            if(empty($user)){

                    $user = $this->add_new($_POST);
					$user->credits = site_config::get_value('new_user_gift_credits');
					$user->save();
                    if(!empty($user)) {
                        $msg = "פרטי ההתחברות שלך הם: אימייל/שם משתמש - ".$user->email ;
                        $mail = $this->data->mail->get_by_id(2);
                        if(!empty($mail) && !empty($mail->text)){
                            $replacement_arr =
                                array(
                                        'email' => $user->email,
                                        'password' => $user->pass,
                                        'site_phone' => site_config::get_value('site_phone')
                                );
							  
							  $html = $mail->text;
                              foreach($replacement_arr AS $key => $value)
                              {
                                  $html = str_replace('[['.$key.']]', $value, $html);
                              }

                              $msg = $html;
                              unset($html);
							  unset($mail);
							  
                        }
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
						$headers .= 'From: '.site_config::get_value("site_name").' <'.site_config::get_value("site_email_from").'>' . "\r\n";

                        if(mail($user->email,"ברוכים הבאים לאתר TOSEE","ברוכים הבאים לאתר TOSEE www.tosee.co.il !
                            ".$msg,$headers)){
						$site_message['login']['text'] = "תודה על הרשמתך תוכל  עכשיו עפ הפרטים שנשלחו לך במייל";
						$site_message['login']['type'] = "success";
						}
						else{
						$site_message['login']['text'] = "מייל לא נשלח";
						$site_message['login']['type'] = "error";
						}
                        return $user;
                    }
                }else{
                    error_log("user already exists");
                    //echo '<script>$(function(){alert("חשבון קיים כבר")});</script>';
					$site_message['login']['text'] = "חשבון קיים כבר אנא התחבר";
					$site_message['login']['type'] = "error";

                    return false;
                }
				return null;
        }

           

		public function update_details(){
			if (isset($_POST['rank'])){
				unset($_POST['rank']);
				error_log("user tried to set his rank");
				error_log(print_r($_SERVER,true));
			}

			if (!empty($this->loged_in_user)){
				if (!empty($_POST))
					foreach ($_POST as $key => $value){
						if (isset($this->loged_in_user->$key))
							$this->loged_in_user->$key = $value;
					}

				$this->data->user->loged_in_user->save();
			}
		}


		public function get_user_detail($key){
			global $data;
			if (!empty($data->user->loged_in_user)){
				if (!empty($data->user->loged_in_user->{$key})){
					return $data->user->loged_in_user->{$key};
				}
			}
			return "";
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class user extends base_data_object{

		protected function __construct($data, $fields_info, $db_table_name) {
			parent::__construct($data, $fields_info, $db_table_name);
		}

		protected function extend_deletion(){

		}

        public function get_orders()
        {
			$ret = array();
            $orders =  $this->data->order->get_by_column("user_id",$this->data->user->loged_in_user->id);
			if(!empty($orders)){
				foreach($orders as $order){
					$response  = unserialize($order->tranzila_response);
					if(!empty($response)){
						if($response['Response'] == '000'){
							$ret[] = $order;
						}
					}
				}
			}
			return $ret;
        }

        public function get_credits()
        {
            if(!empty($this->credits))
                return $this->credits;
            else
                return '0';
        }

		public static function sort_by_example($a, $b){
			return $a->something > $b->something ? 1 : -1;
		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class user_view extends view_abstract{

		public function __construct($file_path) {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "users";

			$this->object_class_name = "user";

			parent::__construct($file_path);
		}

		public function post_render_html(&$html){

		}
	}


//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////


	class user_controller extends controller_abstract{

		public function __construct() {
			global $is_admin;

			$this->url_page_name = ($is_admin?"admin_":"") . "users";
			$this->object_class_name = "user";
			$this->view_class_name = "user_view";

			parent::__construct();

			// db name - dt name - object path as array - formatter function
			$this->dt_columns = array(
				array( 'db' => 'id', 			'dt' => 'id',			'path' => "id"),
				array( 'db' => 'first_name', 	'dt' => 'first_name',	'path' => "first_name"),
				array( 'db' => 'last_name', 	'dt' => 'last_name',	'path' => "last_name"),
				array( 'db' => 'phone', 		'dt' => 'phone',		'path' => "phone"),
				array( 'db' => 'address', 		'dt' => 'address',		'path' => "address"),
				array( 'db' => 'username', 		'dt' => 'username',		'path' => "username"),
				array( 'db' => 'pass', 			'dt' => 'pass',			'path' => "pass")
			);
		}

		public function extend_request_processing(){

		}

		public function admin_insert_custom_data($new_object){

		}

		public function admin_edit_custom_data(){

		}

		public function init(){
			global $is_admin;

			
			if (empty($_SESSION['login_error_count']))
				$_SESSION['login_error_count'] = 0;

			$this->data->user->loged_in = 0;
			// if (!empty($_SESSION['user_id'])){
				// $this->data->user->loged_in = 1;

				// $user = $this->data->user->get_by_id($_SESSION['user_id']);
				// $this->data->user->loged_in_user = $user;
			// }
			if (!empty($_SESSION['user_id'])){
				$user = $this->data->user->get_by_id($_SESSION['user_id']);

				if (!empty($user)){
					if ($is_admin || (!$is_admin && $user->rank < site_config::get_value("minimum_user_rank_for_admin"))){ // admin user, after loging in in the admin panel, will not be recognised in site
						$this->data->user->loged_in = 1;
						$this->data->user->loged_in_user = $user;
					}
				}
			}

			if (isset($_POST['login'])){
				if (!empty($_POST['username']) && !empty($_POST['pass'])){
					$uname = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
					$pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);

					$this->login($uname, $pass);
					// error_log();
				}
			}

			if (isset($_POST['register'])){
				$user = $this->data->user->register();
				if (!empty($user)){
					$this->data->user->loged_in_user = $user;
					$this->data->user->loged_in = 1;

					$_SESSION['user_id'] = $user->id;

					$_SESSION['login_last_block_time'] = 0;
					$_SESSION['login_error_count'] = 0;
					unset($_SESSION['guest']);
				}
			}

			if (isset($_POST['update_user_details'])){
				$this->data->user->update_details();
			}

			if (!empty($_GET['action']) && $_GET['action'] == "logout"){
				$this->logout();
			}

			// if ($is_admin && !$this->data->user->loged_in && (empty($_GET['page']) || $_GET['page'] != "admin_login" )){
				// header('Location: index.php?page=admin_login');
				// exit();
			// }
			if ($is_admin && (empty($_GET['page']) || $_GET['page'] != "admin_login" )){
				if (
						!$this->data->user->loged_in ||
						$this->data->user->loged_in_user->rank < site_config::get_value("minimum_user_rank_for_admin")
					)
				{
					header('Location: index.php?page=admin_login');
					exit();
				}
				else{
					$this->data->user->admin_loged_in = true;
				}
			}
			if(!empty($_POST) && !empty($_POST['action'])){
				switch($_POST['action']){
					case 'send_giftcard';
						$q = $this->data->product->get_by_id($_POST['amount']);
						cart::add_to_cart($q->id,'1','GIFT CARD');
						// error_log(print_r($q,1));
					break;
					case 'use_credits':
						if(!empty($_POST['credit_amount'])){
						$lu = $this->data->user->loged_in_user->id;
						$user = $this->data->user->get_by_id($lu);
						if(!empty($user)/*  && !empty(db_con::$con) */){
							if($user->credits < site_config::get_value('percent_of_deal_credits'))
								exit('כמות קרדיטים קטן מ-'.site_config::get_value('percent_of_deal_credits'));
							$q = "SELECT * FROM `carts` WHERE `uid` = '".$user->id."'";
							$con = db_con::get_con();
							$result = $con->query($q);
							if(!empty($result) && $result->num_rows == 1){
								// while($row = $result->fetch_assoc()){
								$row = $result->fetch_assoc();
									if(!empty($row['cart'])){
										cart::init_cart();
										$cart = cart::get_cart();
										if($cart['total_price_after_discount'] >= (int)$_POST['credit_amount']){
											$user->credits -= (int)$_POST['credit_amount'];
											$user->save();
											if(isset($_SESSION['credit_amount']))
												$_SESSION['credit_amount'] += (int)$_POST['credit_amount'];
											else
												$_SESSION['credit_amount'] = (int)$_POST['credit_amount'];
											// cart::calculate_cart_price();
											cart::init_cart();
											$cart = cart::get_cart();
											// unset($_SESSION['credit_amount']);
											$q2 = "SELECT * FROM `carts` WHERE `uid` = '".$user->id."'";
											$result2 = $con->query($q2);
											if(!empty($result2) && $result2->num_rows > 0){
												$q = "UPDATE `carts` SET `cart` = '".base64_encode(serialize($cart))."'".
													 " WHERE `uid` = '".$user->id."'";
												$result2 = $con->query($q,true);
											}else{
												$q = "INSERT INTO `carts` (`uid`,`cart`) VALUES ('".$user->id."','".base64_encode(serialize($cart))."')";
												$result2 = $con->query($q,true);
											}
											unset($q);
											unset($result2);
										}
										else{
											exit('אי אפשר לנצל יותר קרדיטים ממחיר של המוצרים בסל הקניות');
										}
									}
								// }
							}
						}
						$cart_data = cart::get_cart();
						// error_log(print_r($cart_data,1));
						/* if($cart_data['total_price_after_discount'] >= (int)$_POST['credit_amount']){
							error_log('In use_credits save');
							$user->credits -= (int)$_POST['credit_amount'];
							$user->save();
							$_SESSION['credit_amount'] = (int)$_POST['credit_amount'];
							// cart::change_discount((int)$_POST['credit_amount']);
						}
						else{
							$_SESSION['credit_amount'] = -(int)$_POST['credit_amount'];
							cart::calculate_cart_price();
							
							cart::init_cart();
							unset($_SESSION['credit_amount']);
						}
						error_log(print_r($cart_data,1)); */
						$_SESSION['cart'] = base64_encode(serialize($cart_data));
						exit('נוצל '.(int)$_POST['credit_amount'].' קרדיטים');
						}
						exit('');
					break;
				}
			}
		}

		public function logout(){
			setcookie('username',false, time()+60*60*24*365, '/' , 'tosee.co.il');
			setcookie('password',false, time()+60*60*24*365, '/' , 'tosee.co.il');
			unset($_POST['username']);
			unset($_POST['pass']);
			unset($_COOKIE['username']);
			unset($_COOKIE['password']);

			unset($_SESSION['user_id']);
			unset($_SESSION['guest']);
			unset($this->data->user->loged_in_user);
			$this->data->user->loged_in = 0;

			$_SESSION['login_last_block_time'] = 0;
			$_SESSION['login_error_count'] = 0;
			// unset($_SESSION);
			// cart::$cart_data = null;
			cart::empty_cart();

		}

		public function login($uname, $pass){
			global $defaults;
			global $site_message;

			$con = db_con::get_con();
			// if (isset($_COOKIE[['username']) && isset($_COOKIE['password'])) {

				// if (($_POST['username'] != $user) || ($_POST['password'] != md5($pass))) {
					// header('Location: login.html');
				// } else {
					// echo 'Welcome back ' . $_COOKIE['username'];
				// }

			// } else {
				// header('Location: login.html');
			// }

			if ($_SESSION['login_error_count'] >= site_config::get_value("login_error_for_block")){
				error_log("Login failed " . site_config::get_value("login_error_for_block") . " times for user IP : " . $_SERVER['REMOTE_ADDR']);

				$_SESSION['login_error_count'] = 0;
				$_SESSION['login_last_block_time'] = time();

				$site_message['login']['text'] = "לאחר " . site_config::get_value("login_error_for_block") . " ניסיונות כושלים כניסה למערכת נעולה למשך " . site_config::get_value("login_error_block_time")/60 . " דקות.";
				$site_message['login']['type'] = "error";
			}
			else if (!empty($_SESSION['login_last_block_time']) && time() <= ($_SESSION['login_last_block_time'] + site_config::get_value("login_error_block_time"))){
				$site_message['login']['text'] = "לאחר מספר ניסיונות כושלים הכניסה למערכת נעולה למשך " . round(($_SESSION['login_last_block_time'] + site_config::get_value("login_error_block_time") - time())/60) . " דקות.";
				$site_message['login']['type'] = "error";
			}
			else{
				$q = "select id, pass from `users` where `username` = '" . $uname . "'";
				 //echo $q;
				if ($res = $con->query($q)){
					$row = $res->fetch_assoc();
					if (!empty($row['pass'])){
						if ($row['pass'] == $pass){
							$user = $this->data->user->get_by_id($row['id']);
							$this->data->user->loged_in_user = $user;
							//echo($this->data->user->loged_in_user->first_name);
							$this->data->user->loged_in = 1;

							// /* Set cookie to last 1 year */
							setcookie('username', base64_encode($uname), time()+60*60*24*365, '/' , 'tosee.co.il');
							setcookie('password', base64_encode($pass), time()+60*60*24*365, '/' , 'tosee.co.il');

							$_SESSION['user_id'] = $user->id;

							$_SESSION['login_last_block_time'] = 0;
							$_SESSION['login_error_count'] = 0;
							unset($_SESSION['guest']);

						}
						else{
							$site_message['login']['text'] = "הסיסמה שגויה";
							$site_message['login']['type'] = "error";
							$_SESSION['login_error_count']++;
						}
					}
					else{
						$site_message['login']['text'] = "שם משתמש שגוי";
						$site_message['login']['type'] = "error";
						$_SESSION['login_error_count']++;
					}
				}
				else{
					$site_message['login']['text'] = "שם משתמש לא קיים במערכת";
					$site_message['login']['type'] = "error";
					$_SESSION['login_error_count']++;
				}
			}
		}

		public function check_login( $required_rank = 1, $redirect_path = "" ){
			if (empty($redirect_path))
				$redirect_path = "index.php?page=login";

			if ($this->data->user->loged_in_user){
				if (!empty($this->data->user->loged_in_user->rank) && $this->data->user->loged_in_user->rank >= $required_rank){
					return true;
				}
				else{
					header('Location: ' . $redirect_path);
					exit();
				}
			}
			else{
				header('Location: ' . $redirect_path);
				exit();
			}
			return false;
		}
	}
?>
