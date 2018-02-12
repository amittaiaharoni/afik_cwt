<?
error_log('order_step_3 - in the page');
?>
<div class="secure_page_block">
	<?
	if(!empty($this->data->banner->get_by_id("8")->image)){
	?>
  <div class="secure_bg"><img src="<?=site_config::get_value('upload_images_folder') . $this->data->banner->get_by_id("8")->image?>" /></div>
  <?
  }
  ?>
</div>
<div class="right">
<div id="order_side">
<!-- ------------------INCLUDE --------------->
	{__order_side__}
<!-- ----------------INCLUDE----------------- -->
    </div>
<?
$cart = cart::get_cart();
?>
<div  class="order_holder">
	<div id="secure_title">
	  הנכם נמצאים בשרת מאובטח<i class="fa fa-lock"></i>
</div>
<!--
  <form action="https://secure5.tranzila.com/cgi-bin/tranzila31p.cgi" method="POST">

  <input type="hidden" name="supplier" value="tosee">

  <input type="hidden" name="currency">

  <table align="center">

  <tr>

    <td>סכום העיסקה</td>

    <td><input type="text" name="sum" value="2"></td>

  </tr>

  <tr>

    <td>תאור מוצר</td>

    <td><input type="text" name="pp_DESC"></td>

  </tr>

  <tr>

    <td colspan="2" align="center">

      <input type="submit" value="שלח">

    </td>                                               

  </tr>                          

  </table>

  </form>
-->
  <?
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//		TRANZILA
	if (site_config::get_value("use_tranzila") && (!empty($_POST['payment_type']) && $_POST['payment_type'] == paymentType::tranzila)){
    //error_log(print_r($cart,1));
		if (!empty($_SESSION['last_order_id'])){
			$order = $this->data->order->get_by_id($_SESSION['last_order_id']);
			$gorder = unserialize(base64_decode($order->serialized_order));
			if (!empty($order)){
				$sum_to_pay = 0;
				$sum_to_pay = $order->price_to_pay;

				// $sum_to_pay = 1;
				if ($sum_to_pay > 0){
					
					$get_string =
							"nologo=1".
							"&lang=il".
							"&sum=".$sum_to_pay.
							(site_config::get_value('payments_num')!=""?"&maxpay=".site_config::get_value('payments_num'):"").
							"&lang=".site_config::get_value("tranzila_language").
							"&currency=".site_config::get_value("tranzila_currency").
							"&cred_type=1".
							"&order_id=".$_SESSION['last_order_id'];
					if(!isset($_SESSION['guest'])){
						error_log('Not Guest');
					$get_string .=	"&email=".urlencode($this->data->user->get_user_detail("email")).
							// "&tz=".urlencode($this->data->user->get_user_detail("tz")).
							"&contact=".urlencode($this->data->user->get_user_detail("first_name"))." ".urlencode($this->data->user->get_user_detail("last_name")).
							// "&lname=".urlencode($this->data->user->get_user_detail("last_name")).
							"&address=".urlencode($this->data->user->get_user_detail("city").", ".$this->data->user->get_user_detail("address")).
							"&phone=".urlencode($this->data->user->get_user_detail("phone")).
							// "&remarks=".urlencode($this->data->user->get_user_detail("notes")).
							"&cred_type=8&maxpay=4";
					}
					else{
						error_log('Is Guest');
					$get_string .=
							"&email=".urlencode($_POST['email']).
							"&contact=".urlencode($_POST['name']).
							"&address=".urlencode($_POST['city']).", ".$_POST['street'].
							"&phone=".urlencode($_POST['phone']).
							"&city=".urlencode($_POST['city']).
							"&remarks=".urlencode($_POST['notes']).
							"&cred_type=8&maxpay=4";					
					}

					if (site_config::get_value('tranzila_use_delayed_payment') == true)
						$get_string .= "&tranmode=V";

					error_log("tranzila get string : " . $get_string);
			?>
			<div>
				<iframe src="<?=site_config::get_value("tranzila_url")?>?<?=$get_string?>" scrolling="no" style='width:100%; height:480px; border: 0;'></iframe>
      </div>
			<?
				}
				else{
					error_log("order step 3 -> sum is 0 for user_id = " . $_SESSION['user_id'] . ", order_id = " . $_SESSION['last_order_id']);
				}
			}
			else{
				error_log("order step 3 -> order not found for user_id = " . $_SESSION['user_id'] . ", order_id = " . $_SESSION['last_order_id']);
			}
		}
		else{
			error_log("order step 3 -> no order id for user " . $_SESSION['user_id']);
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//		PAYPAL
	//		https://developer.paypal.com/webapps/developer/docs/classic/express-checkout/integration-guide/ECGettingStarted/#idde509e1a-af2a-412a-b9ab-829b844986c5
	//		https://developer.paypal.com/webapps/developer/docs/classic/api/#merchant

	else if (site_config::get_value("use_paypal") && (!empty($_POST['payment_type']) && $_POST['payment_type'] == paymentType::paypal)){
		if (!empty($_SESSION['last_order_id'])){
			$order = $this->data->order->get_by_id($_SESSION['last_order_id']);
			if (!empty($order)){
				$sum_to_pay = 0;
				$sum_to_pay = $order->price_to_pay;

				// $sum_to_pay = 1;
				if ($sum_to_pay > 0){
					//	1 - SetExpressCheckout

					$data = array(
									'METHOD' 							=> 'SetExpressCheckout',
									'VERSION' 							=> site_config::get_value("paypal_api_version"),
									'USER' 								=> site_config::get_value("paypal_user"),
									'PWD' 								=> site_config::get_value("paypal_pass"),
									'SIGNATURE' 						=> site_config::get_value("paypal_signature"),
									'PAYMENTREQUEST_0_PAYMENTACTION' 	=> 'Sale',
									'PAYMENTREQUEST_0_AMT' 				=> $sum_to_pay,
									'PAYMENTREQUEST_0_CURRENCYCODE' 	=> site_config::get_value("paypal_currency"),
									'RETURNURL' 						=> site_config::get_value("site_url").'index.php?page=paypal_return&order_id='.$_SESSION['last_order_id'],
									'CANCELURL' 						=> site_config::get_value("site_url").'index.php?page=paypal_cancel&order_id='.$_SESSION['last_order_id']
								);

					/*
						SAMPLE RESPONSE

						TIMESTAMP=2007%2d04%2d05T23%3a23%3a07Z
						&CORRELATIONID=63cdac0b67b50
						&ACK=Success
						&VERSION=XX%2e000000
						&BUILD=1%2e0006
						&TOKEN=EC%2d1NK66318YB717835M
					*/
					$post_request = '';
					foreach($data as $k=>$v){
						$post_request .= $k.'='.$v.'&';
					}
					$post_request = rtrim($post_request, ' &');
					$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, site_config::get_value("paypal_url"));
						curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $post_request);
						$out = curl_exec($curl);
					error_log('My Post '.$post_request);
					error_log('Request '.print_r($out,1));
					// error_log('To URL '.site_config::get_value("paypal_url"));
					$response_str = send_post_request(site_config::get_value("paypal_url"), $data);
					// error_log('Response '.print_r($response_str,1));	
					if (!emptY($response_str)){
						parse_str($response_str, $response);

						if ($response['ACK'] == "Success"){

							$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=".$response['TOKEN'];
							header("Location: $paypal_url");
							exit();
						}
						else{
							error_log("order step 3 -> paypal SetExpressCheckout failed");
							error_log(print_r($response, true));
						}
					}
				}
				else{
					error_log("order step 3 -> sum is 0 for user_id = " . $_SESSION['user_id'] . ", order_id = " . $_SESSION['last_order_id']);
				}
			}
			else{
				error_log("order step 3 -> order not found for user_id = " . $_SESSION['user_id'] . ", order_id = " . $_SESSION['last_order_id']);
			}
		}
		else{
			error_log("order step 3 -> no order id for user " . $_SESSION['user_id']);
		}
	}
	?>
</div>
</div>
