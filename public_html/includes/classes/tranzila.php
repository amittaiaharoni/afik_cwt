<?
	class tranzila{
		
		static function execute_delayed_transaction($order_id){
			global $data;
			
			$order = $data->order->get_by_id($_POST['order_id']);
			if (!empty($order)){
				/* EXAMPLE
				https://secure5.tranzila.com/cgi-bin/tranzila38.cgi?
					supplier=terminal_name&
					sum=1&currency=1&
					tranmode=F&
					authnr=0000000&
					index=123
				*/
				
				$url = site_config::get_value('tranzila_delayed_payment_url');
				$url .= "?";
				$url .= "supplier=".site_config::get_value("tranzila_terminal_name");
				$url .= "sum=".$order->price_to_pay;				
				$url .= "tranmode=F";				
				$url .= "authnr=".$order->tranzila_authnr;				
				$url .= "index=".$order->tranzila_index; 				
			}
		}
		
		static function process_report(){
			global $data;
			
			if (!empty($_POST['order_id'])){
				$order = $data->order->get_by_id($_POST['order_id']);
				if (!empty($order)){
					if (!empty($_POST['Response'])){
						if ($_POST['Response'] == "000"){
							$order->tranzila_sum_paid 	=	(float)$_POST['sum'];
							$order->tranzila_index 		=	(int)$_POST['index'];
							$order->tranzila_status 	=	get_tranzila_response_text($_POST['Response']);
							$order->tranzila_response 	=	serialize($_POST);
							
							if (!empty($_POST["authnr"]))
								$order->tranzila_authnr 	=	serialize($_POST['authnr']);
							//For cart delete from logic,php
							$cart = unserialize(base64_decode($order->serialized_cart));
							if(!isset($_SESSION['guest'])){
								$user = $data->user->get_by_id($order->user_id);
								$user->credits -= $_SESSION['credit_amount'];
								$user->credits += (float)$_POST['sum']/100*site_config::get_value('credits_percent_from_order');
								$user->save();
								if(!empty($user)){
									$data->user->loged_in_user = $user;	
								}
							}
							$order->save();
							
							error_log("tranzila report: good report, order id = " . $order->id);
						}
						else{
							$order->tranzila_sum_paid 	=	(float)$_POST['sum'];
							$order->tranzila_index 		=	(int)$_POST['index'];
							$order->tranzila_status 	=	get_tranzila_response_text($_POST['Response']);
							$order->tranzila_response 	=	serialize($_POST);
							
							//For cart delete from logic,php
							$cart = unserialize(base64_decode($order->serialized_cart));
							if(!isset($_SESSION['guest'])){
								$user = $data->user->get_by_id($order->user_id);
								if(!empty($user)){
									$data->user->loged_in_user = $user;
									/*
										// Do not add credits if not paid!
									$user->credits += (float)$_POST['sum']/100*site_config::get_value('credits_percent_from_order');
									$user->save(); */
									$coupon = $data->coupon->get_by_id($cart['user_coupon_id']);
									if(!empty($coupon)){
										$coupon->used = 0;
										$coupon->save();
									}
									$user->credits += $cart['user_credits'];
									$user->save();
								}
							}
							
							
							$data->order->drop_coupon();
							unset($_SESSION['coupon_id']);
							

							$order->save();
							unset($_SESSION['guest']);
							error_log("tranzila report: response is an error, error_code = " . (int)$_POST['Response'] . ", error msg : " . get_tranzila_response_text($_POST['Response']) . ", for order : " . (int)$_POST['order_id']);
						}
					}
					else{
						error_log("tranzila report: no response code found for order : " . (int)$_POST['order_id']);
					}
				}
				else{
					error_log("tranzila report: order not found for id : " . (int)$_POST['order_id']);
				}
			}
			else{
				error_log("tranzila report: empty order id");
			}
		}
	}
	
	/*	TRANZILA REPORT EXAMPLE
	[02-Feb-2015 15:43:21 Asia/Jerusalem] Array
	(
		[Response] => 000
		[o_tranmode] => 
		[adrs] => כגדחלכ, עיד דעע 78/8
		[fname] => שם
		[expmonth] => 10
		[myid] => 032991226
		[email] => 
		[currency] => 1
		[nologo] => 1
		[ConfirmationCode] => 9328881
		[cardtype] => 1
		[expyear] => 16
		[npay] => 
		[supplier] => ayit2
		[remarks] => ...
		[address] => ...
		[sum] => 1
		[benid] => p0trhnpae1pc86q853qld4ria0
		[o_cred_type] => 1
		[maxpay] => 6
		[lang] => il
		[order_id] => 28
		[tel1] => 43243232
		[cred_type] => 1
		[ccno] => 4076
		[o_npay] => 
		[cardaquirer] => 6
		[lname] => שם מ
		[cardissuer] => 1
		[tranmode] => 
		[tz] => 8877
		[index] => 24453
		[Tempref] => 23640001
	)
	*/
?>