<?
	class user{
		
		private static $instance;
		private static $users_table_name = "users";
		public static $loged_in = false;
				
		public static function init(){
			global $is_admin;
			
			if (empty($_SESSION['login_error_count'])) 
				$_SESSION['login_error_count'] = 0;
			
			self::$loged_in = 0;
			if (!empty($_SESSION['user_id']))
				self::$loged_in = 1;
				
			if (isset($_POST['login'])){
				if (!empty($_POST['username']) && !empty($_POST['pass'])){
					$uname = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
					$pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
					
					self::login($uname, $pass);
					// error_log();
				}
			}
			
			if (!empty($_GET['action']) && $_GET['action'] == "logout"){
				self::logout();
			}
			
			if ($is_admin && !self::$loged_in && (empty($_GET['page']) || $_GET['page'] != "admin_login" )){
				header('Location: index.php?page=admin_login');
				exit();
			}
		}
		
		public static function logout(){
			unset($_SESSION['user_id']);
			unset($_SESSION['user_rank']);
			unset($_SESSION['user_name']);
			self::$loged_in = 0;
			
			$_SESSION['login_last_block_time'] = 0;
			$_SESSION['login_error_count'] = 0;
		}
		
		public static function login($uname, $pass){
			global $defaults;
			global $site_message;
			
			$con = db_con::get_con();
			
			if ($_SESSION['login_error_count'] >= $defaults["login_error_for_block"]){
				error_log("Login failed " . $defaults["login_error_for_block"] . " times for user IP : " . $_SERVER['REMOTE_ADDR']);
				
				$_SESSION['login_error_count'] = 0;
				$_SESSION['login_last_block_time'] = time();
				
				$site_message['login']['text'] = "לאחר " . $defaults["login_error_for_block"] . " ניסיונות כושלים כניסה למערכת נעולה למשך " . $defaults["login_error_block_time"]/60 . " דקות.";
				$site_message['login']['type'] = "error";
			}
			else if (!empty($_SESSION['login_last_block_time']) && time() <= ($_SESSION['login_last_block_time'] + $defaults["login_error_block_time"])){
				$site_message['login']['text'] = "לאחר מספר ניסיונות כושלים הכניסה למערכת נעולה למשך " . round(($_SESSION['login_last_block_time'] + $defaults["login_error_block_time"] - time())/60) . " דקות.";
				$site_message['login']['type'] = "error";
			}
			else{				
				$q = "select * from `".self::$users_table_name."` where `username` = '" . $uname . "'";
				// echo $q;
				if ($res = $con->query($q)){
					$row = $res->fetch_assoc();
					if (!empty($row['pass'])){
						if ($row['pass'] == $pass){
							$_SESSION['user_id'] = $row['id'];
							$_SESSION['user_rank'] = $row['rank'];
							$_SESSION['user_name'] = $row['name'];
							self::$loged_in = 1;
							
							$_SESSION['login_last_block_time'] = 0;
							$_SESSION['login_error_count'] = 0;							
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
					$site_message['login']['text'] = "שם משתמש שגוי";
					$site_message['login']['type'] = "error";
					$_SESSION['login_error_count']++;
				}
			}
		}
		
		public static function check_login( $required_rank = 1 ){
			if (self::$loged_in){
				if (!empty($_SESSION['user_rank']) && $_SESSION['user_rank'] >= $required_rank){
					return true;
				}
				else{
					header('Location: index.php?page=login');
					exit();
				}
			}
			else{
				header('Location: index.php?page=login');
				exit();
			}
			return false;
		}
	}
	
	class user_login_view extends view_abstract{
		
		public function __construct($file_path) {
			parent::__construct($file_path);
			
			$this->url_page_name = "admin_login";
			$this->object_class_name = "";
		}
		
		public function post_render_html(&$html){
			
		}
	}
?>