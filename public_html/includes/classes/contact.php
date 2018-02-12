<?

class contact{
	public static function send_contact() {
		global $data;
		if (isset($_POST['send_contact'])){

			$msg="
			<html>
			<head>
				<META HTTP-EQUIV='CONTENT-TYPE' CONTENT='TEXT/HTML; CHARSET=UTF-8'>
				<title>הודעה מצור קשר אתר ".site_config::get_value("site_name")."</title>
				<style type='text/css'>
				.content-block{
					background:#333;
					color: white;
					padding: 15px;
				}
				.content-block h2{
					color: #99cc67;
					text-decoration:underline;
					padding:0px;
					margin:0px;
					margin-bottom:15px;
				}
				.content-block a:link,.content-block a:visited{
					color: #99cc67;
				}
				.content-block a:hover, .content-block a:active{
					color: white;
				}
				</style>
			</head>
				<body dir='rtl' align='center'>
				<table border='0' cellspacing='0' cellpadding='0' class='content-block'>
					<thead>
						<th colspan='2'>
							</h2>ההודעה הבאה התקבלה מטופס צור קשר מאתר ".site_config::get_value("site_name").":<h2>
						</th>
					</thead>
					<tbody>
						<tr>
							<td>שם:</td>
							<td>".$_POST['name']."</td>
						</tr>
						<tr>
							<td>דואל:</td>
							<td>".$_POST['email']."</td>
						</tr>
						<tr>
							<td>תוכן הפנייה:</td>
							<td>".$_POST['text']."</td>
						</tr>
					</tbody>
				</table>
				</body>
			</html>";

			$to = site_config::get_value("site_contact_email");
			$subject = 'טופס צור קשר מאתר '.site_config::get_value("site_name");
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
			$headers .= "From: ".site_config::get_value("site_name")." <".site_config::get_value("site_email_from")."> \r\n";
			$mail_sent = mail($to,$subject,$msg,$headers);
			$_SESSION['sent'] = 1;
		}

		if (isset($_POST['send_quick'])){
			if(!empty($_POST['email'])){
				$sql = "INSERT INTO `newsletter`
								(`email`)
							VALUES
								('".$_POST['email']."')
							";
					// error_log($sql);
				$answ = db_con::get_con()->query($sql, true);
				$msg="
				<html>
				<head>
					<META HTTP-EQUIV='CONTENT-TYPE' CONTENT='TEXT/HTML; CHARSET=UTF-8'>
					<title>הודעה מצור קשר אתר ".site_config::get_value("site_name")."</title>
					<style type='text/css'>
					.content-block{
						background:#333;
						color: white;
						padding: 15px;
					}
					.content-block h2{
						color: #99cc67;
						text-decoration:underline;
						padding:0px;
						margin:0px;
						margin-bottom:15px;
					}
					.content-block a:link,.content-block a:visited{
						color: #99cc67;
					}
					.content-block a:hover, .content-block a:active{
						color: white;
					}
					</style>
				</head>
					<body dir='rtl' align='center'>
					<table border='0' cellspacing='0' cellpadding='0' class='content-block'>
						<thead>
							<th colspan='2'>
								</h2>ההודעה הבאה התקבלה מטופס צור קשר מאתר ".site_config::get_value("site_name").":<h2>
							</th>
						</thead>
						<tbody>
							<tr>
								<td>תודה שנרשמת לניוזלטר שלנו</td>
							</tr>
							<tr>
								<td>דואל:</td>
								<td>".$_POST['email']."</td>
							</tr>
						</tbody>
					</table>
					</body>
				</html>";

				$to = $_POST['email'].','.site_config::get_value("site_contact_email");
				$subject = 'טופס צור קשר מאתר '.site_config::get_value("site_name");
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
				$headers .= "From: ".site_config::get_value("site_name")." <".site_config::get_value("site_email_from")."> \r\n";
				$mail_sent = mail($to,$subject,$msg,$headers);
				$_SESSION['sent'] = 2;
			}
		}

		if (isset($_POST['send_not_in_stock'])){
			$msg="
			<html>
			<head>
				<META HTTP-EQUIV='CONTENT-TYPE' CONTENT='TEXT/HTML; CHARSET=UTF-8'>
				<title>משתמש מתעניין במוצר שלא במלאי</title>
				<style type='text/css'>
				.content-block{
					background:#333;
					color: white;
					padding: 15px;
				}
				.content-block h2{
					color: #99cc67;
					text-decoration:underline;
					padding:0px;
					margin:0px;
					margin-bottom:15px;
				}
				.content-block a:link,.content-block a:visited{
					color: #99cc67;
				}
				.content-block a:hover, .content-block a:active{
					color: white;
				}
				</style>
			</head>
				<body dir='rtl' align='center'>
				<table border='0' cellspacing='0' cellpadding='0' class='content-block'>
					<thead>
						<th colspan='2'>
							<h2>משתמש מתעניין במוצר שלא במלאי</h2>
						</th>
					</thead>
					<tbody>
						<tr>
							<td>שם:</td>
							<td>".$_POST['name']."</td>
						</tr>
						<tr>
							<td>טלפון:</td>
							<td>".$_POST['phone']."</td>
						</tr>
						<tr>
							<td>אימייל:</td>
							<td>".$_POST['email']."</td>
						</tr>
						<tr>
							<td>מוצר:</td>
							<td>".$_POST['product_name']."</td>
						</tr>
						<tr>
							<td>לינק למוצר:</td>
							<td><a href='http://www.tosee.co.il/2016/index.php?page=admin_products&id=".(int)$_POST['product_id']."'>".$_POST['product_name']."</a></td>
						</tr>
					</tbody>
				</table>
				</body>
			</html>";
			$to = site_config::get_value("site_contact_email");
			$subject = 'משתמש מתעניין במוצר שלא במלאי';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
			$headers .= "From: ".site_config::get_value("site_name")." <".site_config::get_value("site_email_from")."> \r\n";
			$mail_sent = mail($to,$subject,$msg,$headers);
			$_SESSION['sent'] = 3;
		}

		if (isset($_POST['birthday_mail'])){
            error_log('birthday mail sent');

			/*$msg="
			<html>
			<head>
				<META HTTP-EQUIV='CONTENT-TYPE' CONTENT='TEXT/HTML; CHARSET=UTF-8'>
				<title>יום הולדת שמח מ".site_config::get_value("site_name")."</title>
				<style type='text/css'>
				.content-block{
					background:#333;
					color: white;
					padding: 15px;
				}
				.content-block h2{
					color: #99cc67;
					text-decoration:underline;
					padding:0px;
					margin:0px;
					margin-bottom:15px;
				}
				.content-block a:link,.content-block a:visited{
					color: #99cc67;
				}
				.content-block a:hover, .content-block a:active{
					color: white;
				}
				</style>
			</head>
				<body dir='rtl' align='center'>
				<table border='0' cellspacing='0' cellpadding='0' class='content-block'>
					<thead>
						<th colspan='2'>
							</h2>לפי הנתונים שלנו יהיה לך יום הולדת בחודש הקרוב".site_config::get_value("site_name").":<h2>
						</th>
					</thead>
					<tbody>
						<tr>
							<td>שם:</td>
							<td>".$_POST['name']."</td>
						</tr>
						<tr>
							<td>טלפון:</td>
							<td>".$_POST['phone']."</td>
						</tr>
						<tr>
							<td>דגם:</td>
							<td>".$_POST['model']."</td>
						</tr>
					</tbody>
				</table>
				</body>
			</html>";

			$to = '';//select all users with birthdays this month
			$subject = 'טופס צור קשר מאתר '.site_config::get_value("site_name");
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
			$headers .= "From: ".site_config::get_value("site_name")." <".site_config::get_value("site_email_from")."> \r\n";
			$mail_sent = mail($to,$subject,$msg,$headers);*/
		}
		if(isset($_POST['send_mail_cart'])){
			$mail = $data->mail->get_by_id(4);
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers .= 'From: '.site_config::get_value("site_name").' <'.site_config::get_value("site_email_from").'>' . "\r\n";
			
			$q = 'SELECT * FROM `carts`';
			$result = db_con::get_con()->query($q);
			if(!empty($result)){
				while($row = $result->fetch_assoc()){
					if(!empty($row['uid'])){
						
						$user = $data->user->get_by_id($row['uid']);
						
						$replacement_arr =	array(
							'first_name' => $user->first_name
						);
						$html = $mail->text;
						foreach($replacement_arr AS $key => $value)
						{
							$html = str_replace('[['.$key.']]', $value, $html);
						}

						$msg = $html;
						unset($html);
						// mail($row['email'],'מזל טוב',$msg,$headers);
						mail($user->email,'נא השלם קנייתך',$msg,$headers);
					}
				}
			}
			error_log("Bom");
			unset($mail);					
								
			/* if(!empty($_POST['id'])){
				$mail = $data->mail->get_by_id($_POST['id']);
				if(!empty($mail) && !empty($mail->text)){
					switch($_POST['id']){
						case '1':
						// Birthday
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
							$headers .= 'From: '.site_config::get_value("site_name").' <'.site_config::get_value("site_email_from").'>' . "\r\n";
							$q = 'SELECT * FROM `users` where `birthday`';
							$result = db_con::get_con()->query($q);
							if(!empty($result)){
								while($row = $result->fetch_assoc()){
									if(date('m-d') == substr($row['birthday'],5,5) or (date('y')%4 <> 0 and substr($row['birthday'],5,5)=='02-29' and date('m-d')=='02-28')){
										$replacement_arr =
											array(
												'first_name' => $row['first_name'],
												'birthday' =>  date('d/m/Y',strtotime($row['birthday'])),
												'coupon_code' => site_config::get_value('site_phone')
											);
											$html = $mail->text;
											foreach($replacement_arr AS $key => $value)
											{
												$html = str_replace('[['.$key.']]', $value, $html);
											}

											$msg = $html;
											unset($html);
											unset($mail);
											mail($row['email'],'מזל טוב',$msg,$headers);
									}
								}
							}
							
						break;
						case '3':
							
						break;
					}
					
					  
					  
					  
				}
				
				
				$mail_tos = '';
				$q = 'SELECT * FROM `carts`';
				$result = db_con::get_con()->query($q);
				if(!empty($result)){
					while($row = $result->fetch_assoc()){
						if(!empty($row['uid'])){
							$mail_tos .= $data->user->get_by_id($row['uid'])->email.',';
							// self::$cart_data = unserialize(base64_decode($row['cart']));
							// self::calculate_cart_price();
							//error_log("get_cart db" . print_r(self::$cart_data,1));
						}
					}
					$mail_tos = rtrim($mail_tos,',');
					// error_log($mail_tos);
					// mail($mail_tos,'test','complete your order => '.site_config::get_value('site_url').'index.php?page=login');
				}
			} */
		}
	}
}

?>
