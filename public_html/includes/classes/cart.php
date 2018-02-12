<?
class cart{

	private static $cart_data = array();
    private static $con;

	protected function __construct(){
	}

	public static function init_cart() {
		global $data;
		global $defaults;

        if(empty(self::$con))
            self::$con = db_con::get_con();

		// unset($_SESSION['cart']);
        if(!empty($data->user->loged_in_user) && !empty(self::$con)){
            $q = "SELECT * FROM `carts` WHERE `uid` = '".$data->user->loged_in_user->id."'";
            $result = self::$con->query($q);
            if(!empty($result) && $result->num_rows == 1){
                while($row = $result->fetch_assoc()){
                    if(!empty($row['cart'])){
                    self::$cart_data = unserialize(base64_decode($row['cart']));
                    self::calculate_cart_price();
                    //error_log("get_cart db" . print_r(self::$cart_data,1));
                    }
                }
            }
        }
		else if (!empty($_SESSION['cart'])){
			$cart_data = unserialize(base64_decode($_SESSION['cart']));

			self::$cart_data = $cart_data;
			self::calculate_cart_price();
		}
		else{
			self::$cart_data['prods'] = array();
			self::$cart_data['total_price'] = 0;
			self::$cart_data['is_shipping'] = 1;
			self::$cart_data['discount'] = 0;
			self::$cart_data['shipping_added_price'] = 0;
			self::$cart_data['shipping_price'] = 0;
		}

		if (!empty($_REQUEST['action'])){
			switch ($_REQUEST['action']){
				case 'update_credits_cart':
					$dt = $data->deliveryType->get_by_id((int)$_POST['dt_id']);
					self::calculate_cart_price();
					if(!empty($dt) && self::$cart_data['total_price_after_discount'] >= $dt->from_order_amount){
						$discount = $dt->new_delivery_from;
						if($_POST['shipping_price'] == 0){
							self::$cart_data['shipping_price'] = 0;
							self::calculate_cart_price();
							exit(0);
						}
						self::$cart_data['shipping_price'] = $discount;
						self::calculate_cart_price();
						exit(self::$cart_data['shipping_price']);
					}
					else{
						if($_POST['shipping_price'] == 0){
							self::$cart_data['shipping_price'] = 0;
							self::calculate_cart_price();
							exit(0);
						}
						self::$cart_data['shipping_price'] = $_POST['shipping_price'];
						self::calculate_cart_price();
						exit(self::$cart_data['shipping_price']);
					}
				break;
				case "add_to_cart":
					if (!empty($_POST['prod_id'])){

						$prod_id = $_POST['prod_id'];
						$quantity = (empty($_POST['quantity'])?1:$_POST['quantity']);
						$comments = (empty($_POST['comments'])?"":$_POST['comments']);
						$prod_options = array();

						if (!empty($_REQUEST['prod_det'])){
							foreach ($_REQUEST['prod_det'] as $prod_det){
								$prod_options[] = $prod_det;
							}
						}
						self::add_to_cart($prod_id, $quantity, $comments, $prod_options);
					}
				break;
				case "delete_from_cart":
					if (!empty($_POST['id'])){
						self::delete_from_cart($_POST['id']);
					}
				break;
				case "prod_attachment":
					if (!empty($_FILES)){
						$file_names = array_keys($_FILES);
						$first_file_key = $file_names[0];
						$input_name_parts = explode("__",$first_file_key);
						if (!empty($input_name_parts) && count($input_name_parts) == 2){
							$attachment_id = $input_name_parts[0];
							$prod_id = $input_name_parts[1];

							$cart_search = array_filter(self::$cart_data["prods"], function($entry) use ($prod_id){
								if ($entry['prod_id'] == $prod_id)
									return true;
								return false;
							});

							if (!empty($cart_search)){ // the prod is realy in the cart
								$cart_search_keys = array_keys($cart_search);
								$first_cart_search_key = $cart_search_keys[0];
								$attachments = $data->productFile->get_by_prod_id($prod_id);
								$attachments_search = array_filter($attachments, function($entry) use ($attachment_id){
									if ($entry->id == $attachment_id)
										return true;
									return false;
								});

								if (!empty($attachments_search)){ // the attachment belong to this prod
									$attachment = reset($attachments_search);
									$cart_entry = &self::$cart_data["prods"][$cart_search[$first_cart_search_key]['id']];
									if (!empty($cart_entry)){
										$file_name = "";

										$is_image = is_image($first_file_key);

										$old_file = "";
										if (!empty($cart_entry['attachments'][$attachment->id]))
											$old_file = $cart_entry['attachments'][$attachment->id];

										if ($is_image)
											$file_name = upload_image($first_file_key, 0, site_config::get_value('upload_user_files_folder'));
										else
											$file_name = upload_file($first_file_key, site_config::get_value('upload_user_files_folder'));

										if (!empty($file_name)){
											$cart_entry['attachments'][$attachment->id] = $file_name;

											if (!emptY($old_file)){
												unlink(site_config::get_value('upload_user_files_folder').$old_file);
											}
										}

										echo site_config::get_value('upload_user_files_folder').$file_name;

										$_SESSION['cart'] = base64_encode(serialize(self::$cart_data));
									}
								}
							}
						}
						exit();
					}
				break;
			}
		}
	}

	public static function empty_cart(){
        global $data;
		if (!emptY($_SESSION['cart']))
			unset($_SESSION['cart']);
		if(!empty($_SESSION['coupon_id']))
			unset($_SESSION['coupon_id']);
        //if cart in carts table for current user delete row
        if(!empty($data->user->loged_in_user) && !empty(self::$con)){
            $q = "DELETE FROM `carts` WHERE `uid` = '".$data->user->loged_in_user->id."'";
            // error_log($q);
            $result = self::$con->query($q,true);
        }
		unset($_SESSION['coupon_id']);
		unset($_SESSION['credit_amount']);
		unset($_SESSION);
		self::$cart_data = null;
		// error_log('Cart deleted');
	}

	protected static function build_cart_entry($prod_id, $quantity, $comments, $prod_options = array()){
		global $data;
		$opt_prod_image = '';
		$opt_prod = '';
		if(!empty($prod_options) && count($prod_options) == 1){
			$opt_prod = $data->details2product->get_by_id($prod_options[0]);
			$opt_prod_image = $opt_prod->image1;
			$opt_prod->stock -= 1;
			$opt_prod->save();
		}
		// error_log('add tocart');
		$cart_entry = array();

		$cart_entry['id'] = uniqid();
		$cart_entry['prod_id'] = $prod_id;
		$cart_entry['quantity'] = $quantity;
		$cart_entry['comments'] = $comments;
		$cart_entry['sale_per_product'] = 0;
		$cart_entry['option_det_id'] = !empty($opt_prod->id)?$opt_prod->id:'';
		// $index = 1;
		// error_log('Count '.count(self::$cart_data['prods']));

		$prod = $data->product->get_by_id($cart_entry['prod_id']);

		if (!empty($prod)){
			$options_price = 0;
			$prod_price = $prod->price;

			$cart_entry['prod_name'] = $prod->name->to_string();
			$cart_entry['prod_price'] = $prod->price;
			$cart_entry['prod_prepay_price'] = $prod->price3 * $quantity;
			if(!empty($opt_prod_image))
				$cart_entry['prod_image'] = $opt_prod_image;
			else
				$cart_entry['prod_image'] = $prod->image;
			$cart_entry['is_shipping'] = $prod->is_shipping;
			$cart_entry['shipping_added_price'] = $prod->shipping_added_price;
			if($prod->barcode == 'GIFT CARD'){
				$cart_entry['gift_orderer'] = $_POST['gift_orderer'];
				$cart_entry['gift_receiver'] = $_POST['gift_receiver'];
				$cart_entry['gift_receiver_email'] = $_POST['gift_receiver_email'];
				$cart_entry['gift_message'] = $_POST['gift_message'];
				// $cart_entry['gift_send_date'] = $_POST['gift_send_date'];
			}

			if (!empty($prod_options)){
				foreach ($prod_options as $detail_data_id){
					$det_data = $data->details2product->get_by_id($detail_data_id);

					if (!empty($det_data)){
						$cart_entry['prod_options'][$det_data->id]['name'] = $det_data->detail->name->to_string();
						$cart_entry['prod_options'][$det_data->id]['price'] = $det_data->price;

						$options_price += $det_data->price;
					}
					else{

					}
				}
			}

			$entry_price = $prod_price + $options_price;
			$entry_price *= $quantity;

			$cart_entry['total_price'] = $entry_price;

			return $cart_entry;
		}
		else{
			return array();
		}
	}

	public static function add_to_cart($prod_id, $quantity, $comments, $prod_options = array()){ // 	$prod_options = array of detail2prod ids
		global $data;
		$prod = $data->product->get_by_id($prod_id);
	/* 	if($prod->barcode != 'GIFT CARD'){
			if(($prod->stock_count - $quantity) >= 0){
				$prod->stock_count -= $quantity;
				if($prod->stock_count == 0){
					$prod->in_stock = 0;
				}
				$prod->save();
			}
			else{
				return false;
			}
		} */
		
		$cart_entry = self::build_cart_entry($prod_id, $quantity, $comments, $prod_options);

		if (!empty($cart_entry)){
            /* if(!empty(self::$cart_data["prods"])){

                $last_entry = end(self::$cart_data["prods"]);

                foreach (self::$cart_data["prods"] as $id  => $prod) {
                    if($cart_entry['prod_id'] === $prod['prod_id']){
                        self::$cart_data["prods"][$id]['quantity'] = self::$cart_data["prods"][$id]['quantity'] + $cart_entry['quantity'];
                        continue;
                    }else if($prod['id'] === $last_entry['id'])
                        self::$cart_data["prods"][$cart_entry['id']] = $cart_entry;
                }

                unset($last_entry);

            }else */
                self::$cart_data["prods"][$cart_entry['id']] = $cart_entry;
			
			if (!$cart_entry['is_shipping'])
				self::$cart_data["is_shipping"] = 0;

			if ($cart_entry['shipping_added_price'])
				self::$cart_data["shipping_added_price"] += $cart_entry['shipping_added_price'];

			self::calculate_cart_price();

			$_SESSION['cart'] = base64_encode(serialize(self::$cart_data));
            if(!empty($data->user->loged_in_user) && !empty(self::$con)){
                $q = "SELECT * FROM `carts` WHERE `uid` = '".$data->user->loged_in_user->id."'";
                $result = self::$con->query($q);
                if(!empty($result) && $result->num_rows > 0){
                    //error_log("add_to_cart exists");
                    $q = "UPDATE `carts` SET `cart` = '".base64_encode(serialize(self::$cart_data))."'".
                         " WHERE `uid` = '".$data->user->loged_in_user->id."'";
                    $result = self::$con->query($q,true);
                }else{
                    //error_log("add_to_cart not exists");
                    $q = "INSERT INTO `carts` (`uid`,`cart`) VALUES ('".$data->user->loged_in_user->id."','".base64_encode(serialize(self::$cart_data))."')";
                    $result = self::$con->query($q,true);
                }
                unset($q);
                unset($result);
            }
		}
		// error_log(print_r(self::$cart_data,1));
	}

	public static function get_cart(){
		// self::calculate_cart_price();
		return self::$cart_data;
	}

	public static function delete_from_cart($entry_id){
        global $data;
		global $defaults;
		// error_log(print_r(self::$cart_data,1));
		if (!empty(self::$cart_data["prods"][$entry_id])){
			$prod = $data->product->get_by_id(self::$cart_data["prods"][$entry_id]['prod_id']);
			if(!empty($prod)){
				/* $prod->stock_count += self::$cart_data["prods"][$entry_id]['quantity'];
				$prod->in_stock = 1;
				$prod->save(); */
				if (!empty(self::$cart_data["prods"][$entry_id]['attachments'])){
					foreach (self::$cart_data["prods"][$entry_id]['attachments'] as $file_name){
						unlink(site_config::get_value('upload_user_files_folder').$file_name);
					}
				}
			}
			if(!empty(self::$cart_data["prods"][$entry_id]['option_det_id'])){
				$opt_det = $data->details2product->get_by_id(self::$cart_data["prods"][$entry_id]['option_det_id']);
				$opt_det->stock += 1;
				$opt_det->save();
			}
			unset(self::$cart_data["prods"][$entry_id]);
			self::calculate_cart_price();
			$_SESSION['cart'] = base64_encode(serialize(self::$cart_data));

            //update with new data `whishlists` where current uid
            if(!empty($data->user->loged_in_user) && !empty(self::$con)){
                $q = "SELECT * FROM `carts` WHERE `uid` = '".$data->user->loged_in_user->id."'";
                $result = self::$con->query($q);
                if(!empty($result) && $result->num_rows > 0){
                    //error_log("delete_from_cart exists");
                    $q = "UPDATE `carts` SET `cart` = '".base64_encode(serialize(self::$cart_data))."'".
                         " WHERE `uid` = '".$data->user->loged_in_user->id."'";
                    $result = self::$con->query($q,true);
                }
                unset($q);
            }
		}
	}
	public static function sort_by_price($a, $b){
		return $a['prod_price'] < $b['prod_price'];
	}
	public static function calculate_cart_price(){
		global $data;
		// custom function for uasort - sort array and keep the keys
		
		self::$cart_data["total_price"] = 0;
		self::$cart_data["prepay_price"] = 0;
		self::$cart_data["discount"] = 0;
		self::$cart_data["cat_discount"] = 0;
		self::$cart_data['user_credits'] = 0;
		self::$cart_data['user_coupon_id'] = 0;
		self::$cart_data['user_coupon_discount'] = 0;
		self::$cart_data["total_price_after_discount"] = 0;
		self::$cart_data["price_to_pay"] = 0;
		// self::$cart_data["shipping_price"] = 0;
		if (!empty(self::$cart_data["prods"])){
			// Reorder cart entries by item price from most expensive to low cost
			uasort(self::$cart_data["prods"],'self::sort_by_price');

			$prod_in_cat_count = array();
			$prod_in_cat_count[1] = 0;
			$prod_in_cat_count[2] = 0;

			$discount = 0;
			$sale_discount = 0;
			$sales = $data->sale->get_by_column('is_active',1);
			$sales_per_product = $data->sale_per_product->get_by_column('is_active',1);
			$sales_correlation = array();
			$index = 0;
			
			// Build array of prod categories and fill with quantity of products ordered
			$prod_cats = array();
			$prod_cats_price = array();
			foreach (self::$cart_data["prods"] as $entry_id => $cart_entry){
				$prod = $data->product->get_by_id($cart_entry['prod_id']);
				if (!empty($prod)){
					$cats = $prod->categories;
					foreach($cats as $cat){
						$prod_cats[$cat] += 1;
						foreach($sales_per_product as $spp){
							if(!empty($spp->categories)){
								if(in_array($cat,$spp->categories)){
									$price = self::$cart_data["prods"][$entry_id]['total_price'];
									if($prod_cats[$cat] <=5){
										$spp_discount_percent = $spp->{'sale_prod'.$prod_cats[$cat]};
									}
									else{
										$spp_discount_percent = $spp->sale_prod5;
									}
									$calc_spp_discount = $price * $spp_discount_percent / 100;
									if(self::$cart_data["prods"][$entry_id]['sale_per_product_discount'] < $calc_spp_discount){
										self::$cart_data["prods"][$entry_id]['sale_per_product_discount'] = 
											$calc_spp_discount;
										self::$cart_data["prods"][$entry_id]['total_after_discount'] = $price - $calc_spp_discount;
									}
								}
								else{
									self::$cart_data["prods"][$entry_id]['sale_per_product_discount'] = 0;
								}
							}
						}
						$prod_cats_price[$cat] += $prod->price * $cart_entry['quantity'];
					}
				}
			}
			$sales_price = array();
			foreach (self::$cart_data["prods"] as $entry_id => $cart_entry){
				$prod = $data->product->get_by_id($cart_entry['prod_id']);
				if (!empty($prod)){
					// error_log('Prod cats '.print_r($prod->categories,1));
					
					foreach($sales as $sale){
						$sale_cats = $sale->categories;
						// error_log('Sale cats '.print_r($sale->categories,1));
						if(!empty($sale_cats)){
							foreach($sale_cats as $sale_cat){
								if(in_array($sale_cat,$prod->categories)){
									$sales_price[$sale->id] += $prod->price;
									break;
								}
							}
						}
					}
				}
			}
			// error_log(print_r($sales_price,1));
			foreach (self::$cart_data["prods"] as $entry_id => $cart_entry){
				$prod = $data->product->get_by_id($cart_entry['prod_id']);
				if (!empty($prod)){
					$cats = $prod->categories;
					foreach($sales as $sale){
						$sale_cats = $sale->categories;
						if(!empty($sale_cats)){
							foreach($sale_cats as $sale_cat){
								// error_log($sales_price[$sale->id]);
								if(in_array($sale_cat,$prod->categories) && $sale->buy_from <= $sales_price[$sale->id]){
									$sales_correlation[] = $sale->price;
								}
							}
						}
					}
					
				}
			}
			// error_log(print_r($sales_correlation,1));
			foreach (self::$cart_data["prods"] as $entry_id => $cart_entry){

				/////////////////////////////////////////////////////////////////////////////////////////
				//	CUSTOM CALCULATION FOR TOSEE
				
				
				/////////////////////////////////////////////////////////////////////////////////////////
				if(!empty($cart_entry['total_after_discount']))
					self::$cart_data["total_price"] += $cart_entry['total_after_discount'] * $cart_entry['quantity'];
				else
					self::$cart_data["total_price"] += $cart_entry['total_price'] * $cart_entry['quantity'];
				self::$cart_data["prepay_price"] += $cart_entry['prod_prepay_price'];
			}
			if(!empty($sales_correlation))
				$sale_discount = max($sales_correlation);
			self::$cart_data["cat_discount"] = $sale_discount;
			$discount += $sale_discount;
			
			$coupon = $data->order->get_coupon();
			// error_log('C1 '.print_R($coupon,1));
			// error_log('C2 '.print_R($_SESSION['coupon_id'],1));
			if(empty($coupon) && isset($_SESSION['coupon_id'])){
				$coupon = $data->coupon->get_by_id($_SESSION['coupon_id']);
				// unset($_SESSION['coupon_id']);
			}
			if (!empty($coupon)/*  && !isset($_SESSION['coupon_id']) */){
				self::$cart_data["user_coupon_id"] = $coupon->id;
				if (empty($coupon->type)){ // discoun %
					$discount += ( $coupon->amount / 100 * self::$cart_data["total_price"] );
					self::$cart_data["user_coupon_discount"] = ( $coupon->amount / 100 * self::$cart_data["total_price"] );
				}
				else{ // discount fixed amount
					if(self::$cart_data["total_price"] > $coupon->amount){
						$discount += $coupon->amount;
						// error_log('C3 '.print_R($coupon->amount,1));
					}
					else{
						$discount += self::$cart_data["total_price"];
						$diff = $coupon->amount - self::$cart_data["total_price"];
						$data->user->loged_in_user->credits += $diff;
						$data->user->loged_in_user->save();
					}
					self::$cart_data["user_coupon_discount"] = $coupon->amount;
				}
			}
			
			if(isset($_SESSION['credit_amount'])){
				self::$cart_data["user_credits"] = $_SESSION['credit_amount'];
				$discount += $_SESSION['credit_amount'];
				// unset($_SESSION['credit_amount']);
			}
			self::$cart_data["discount"] += $discount;
			/* if(isset($_POST['shipping_price'])){
				self::$cart_data["shipping_price"] = $_POST['shipping_price'];
				if(!empty($_POST['shipping_address']))
					self::$cart_data['shipping_address'] = $_POST['shipping_address'];
			}
			else{} */
				// self::$cart_data["shipping_price"] = 0;
			
			self::$cart_data["total_price_after_discount"] = self::$cart_data["total_price"] - self::$cart_data["discount"];

			self::$cart_data["price_to_pay"] = self::$cart_data["total_price_after_discount"] + self::$cart_data["shipping_price"];
			if (!empty(self::$cart_data["prepay_price"])){
				if (self::$cart_data["total_price_after_discount"] > self::$cart_data["prepay_price"])
					self::$cart_data["price_to_pay"] = self::$cart_data["prepay_price"];
			}
			$_SESSION['cart'] = base64_encode(serialize(self::$cart_data));
			// error_log(print_r(self::$cart_data,1));
			if(!empty($data->user->loged_in_user) && !empty(self::$con)){
                $q = "SELECT * FROM `carts` WHERE `uid` = '".$data->user->loged_in_user->id."'";
                $result = self::$con->query($q);
                if(!empty($result) && $result->num_rows > 0){
                    $q = "UPDATE `carts` SET `cart` = '".base64_encode(serialize(self::$cart_data))."'".
                         " WHERE `uid` = '".$data->user->loged_in_user->id."'";
                    $result = self::$con->query($q,true);
                }else{
                    $q = "INSERT INTO `carts` (`uid`,`cart`) VALUES ('".$data->user->loged_in_user->id."','".base64_encode(serialize(self::$cart_data))."')";
                    $result = self::$con->query($q,true);
                }
                unset($q);
                unset($result);
            }
		}
	}

    public static function send_mail_to_finish(){
        //send mail to all user emails from `carts`
        //change "placeholders to respective subs from array"

        /*$q = "SELECT cart,email,username FROM `carts` as c inner join `users` as u
              ON u.`id` = c.`uid`  ";*/
    }

    public static function get_cart_html($order_id)
    {
        // error_log("get_cart_html");
        return null;
    }


	public static function is_empty(){
		if (empty(self::$cart_data["prods"]))
			return 1;

		return (count(self::$cart_data["prods"]) == 0);
	}

	public static function change_quantity($entry_id, $quantity){
		// if (!empty(self::$cart_data[$entry_id])){
			// self::$cart_data[$entry_id]['quantity'] =  $quantity;
			// $_SESSION['cart'] = serialize(self::$cart_data);
		// }
	}
	public static function change_discount($amount){
		$cart = unserialize(base64_decode($_SESSION['cart']));
		if (!empty($cart)){
			$cart['discount'] +=  $amount;//$_SESSION['credit_amount'];
			$_SESSION['cart'] = base64_encode(serialize($cart));
			unset($_SESSION['credit_amount']);
		}
	}
}
?>