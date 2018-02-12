<?
class wishList{

	private static $wishList_data = array();
	private static $con;

	protected function __construct(){

	}

	public static function init_wishList() {
		global $data;
		global $defaults;

		// unset($_SESSION['wishList']);
        if(empty(self::$con))
            self::$con = db_con::get_con();
        //self::get_wishList();

        if(!empty($data->user->loged_in_user) && !empty(self::$con)){
            $q = "SELECT * FROM `wishlists` WHERE `uid` = '".$data->user->loged_in_user->id."'";
            $result = self::$con->query($q);
            if(!empty($result) && $result->num_rows == 1){
                while($row = $result->fetch_assoc()){
                    if(!empty($row['wishlist'])){
                    self::$wishList_data = unserialize(base64_decode($row['wishlist']));
                    self::calculate_wishList_price();
                    //error_log("get_wishList db" . print_r(self::$wishList_data,1));
                    }
                }
            }
        }else if (!empty($_SESSION['wishList'])){
            $wishList_data = unserialize(base64_decode($_SESSION['wishList']));
            //error_log("get_wishList session" . print_r(self::$wishList_data,1));

            self::$wishList_data = $wishList_data;
            self::calculate_wishList_price();
        }else{
            self::$wishList_data['prods'] = array();
            self::$wishList_data['total_price'] = 0;
            //error_log("initialized");
        }

		if (!empty($_REQUEST['action'])){
			switch ($_REQUEST['action']){
				case "add_to_wishList":
					if (!empty($_REQUEST['prod_id'])){

						$prod_id = $_REQUEST['prod_id'];
						$quantity = (empty($_REQUEST['quantity'])?1:$_REQUEST['quantity']);
						$comments = (empty($_REQUEST['comments'])?"":$_REQUEST['comments']);
						$prod_options = array();

						if (!empty($_REQUEST['prod_det'])){
							foreach ($_REQUEST['prod_det'] as $prod_det){
								$prod_options[] = $prod_det;
							}
						}

						self::add_to_wishList($prod_id, $quantity, $comments, $prod_options);
					}
				break;
				case "delete_from_wishList":
					if (!empty($_POST['id'])){
						self::delete_from_wishList($_POST['id']);
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

							$wishList_search = array_filter(self::$wishList_data["prods"], function($entry) use ($prod_id){
								if ($entry['prod_id'] == $prod_id)
									return true;
								return false;
							});

							if (!empty($wishList_search)){ // the prod is realy in the wishList
								$wishList_search_keys = array_keys($wishList_search);
								$first_wishList_search_key = $wishList_search_keys[0];
								$attachments = $data->productFile->get_by_prod_id($prod_id);
								$attachments_search = array_filter($attachments, function($entry) use ($attachment_id){
									if ($entry->id == $attachment_id)
										return true;
									return false;
								});

								if (!empty($attachments_search)){ // the attachment belong to this prod
									$attachment = reset($attachments_search);
									$wishList_entry = &self::$wishList_data["prods"][$wishList_search[$first_wishList_search_key]['id']];
									if (!empty($wishList_entry)){
										$file_name = "";

										$is_image = is_image($first_file_key);

										$old_file = "";
										if (!empty($wishList_entry['attachments'][$attachment->id]))
											$old_file = $wishList_entry['attachments'][$attachment->id];

										if ($is_image)
											$file_name = upload_image($first_file_key, 0, site_config::get_value('upload_user_files_folder'));
										else
											$file_name = upload_file($first_file_key, site_config::get_value('upload_user_files_folder'));

										if (!empty($file_name)){
											$wishList_entry['attachments'][$attachment->id] = $file_name;

											if (!emptY($old_file)){
												unlink(site_config::get_value('upload_user_files_folder').$old_file);
											}
										}

										echo site_config::get_value('upload_user_files_folder').$file_name;

										$_SESSION['wishList'] = base64_encode(serialize(self::$wishList_data));
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

	public static function empty_wishList(){
        global $data;
		if (!emptY($_SESSION['wishList']))
			unset($_SESSION['wishList']);
        //if wishlist in wishlists table for current user delete row
        if(!empty($data->user->loged_in_user) && !empty(self::$con)){
            $q = "DELETE FROM `wishlists` WHERE `uid` = '".$data->user->loged_in_user->id."'";
            // error_log($q);
            $result = self::$con->query($q,true);
        }
		self::$wishList_data = null;
    }

	protected static function build_wishList_entry($prod_id, $quantity, $comments, $prod_options = array()){
		global $data;

		$wishList_entry = array();

		$wishList_entry['id'] = uniqid();
		$wishList_entry['prod_id'] = $prod_id;
		$wishList_entry['quantity'] = $quantity;
		$wishList_entry['comments'] = $comments;

		$prod = $data->product->get_by_id($wishList_entry['prod_id']);

		if (!empty($prod)){
			$options_price = 0;
			$prod_price = $prod->price;

			$wishList_entry['prod_name'] = $prod->name->to_string();
			$wishList_entry['prod_price'] = $prod->price;
			$wishList_entry['prod_prepay_price'] = $prod->price3 * $quantity;
			$wishList_entry['prod_image'] = $prod->image;

			if (!empty($prod_options)){
				foreach ($prod_options as $detail_data_id){
					$det_data = $data->details2product->get_by_id($detail_data_id);

					if (!empty($det_data)){
						$wishList_entry['prod_options'][$det_data->id]['name'] = $det_data->detail->name->to_string();
						$wishList_entry['prod_options'][$det_data->id]['price'] = $det_data->price;

						$options_price += $det_data->price;
					}
					else{

					}
				}
			}

			$entry_price = $prod_price + $options_price;
			$entry_price *= $quantity;

			$wishList_entry['total_price'] = $entry_price;

			return $wishList_entry;
		}
		else{
			return array();
		}
	}

	public static function add_to_wishList($prod_id, $quantity, $comments, $prod_options = array()){ // 	$prod_options = array of detail2prod ids
		global $data;

		$wishList_entry = "";
		$wishList_entry = self::build_wishList_entry($prod_id, $quantity, $comments, $prod_options);
        //error_log(print_r(self::$wishList_data,1));

		if (!empty($wishList_entry)){

            if(!empty(self::$wishList_data["prods"])){

                $last_entry = end(self::$wishList_data["prods"]);

                foreach (self::$wishList_data["prods"] as $id  => $prod) {
                    if($wishList_entry['prod_id'] === $prod['prod_id']){
                        self::$wishList_data["prods"][$id]['quantity'] = self::$wishList_data["prods"][$id]['quantity'] + $wishList_entry['quantity'];
                        continue;
                    }else if($prod['id'] === $last_entry['id'])
                        self::$wishList_data["prods"][$wishList_entry['id']] = $wishList_entry;
                }

                unset($last_entry);

            }else
                self::$wishList_data["prods"][$wishList_entry['id']] = $wishList_entry;


			self::calculate_wishList_price();

			$_SESSION['wishList'] = base64_encode(serialize(self::$wishList_data));
            //error_log(print_r($data->user->loged_in_user,1));
            if(!empty($data->user->loged_in_user) && !empty(self::$con)){
                $q = "SELECT * FROM `wishlists` WHERE `uid` = '".$data->user->loged_in_user->id."'";
                $result = self::$con->query($q);
                if(!empty($result) && $result->num_rows > 0){
                    //error_log("add_to_wishList exists");
                    $q = "UPDATE `wishlists` SET `wishlist` = '".base64_encode(serialize(self::$wishList_data))."'".
                         " WHERE `uid` = '".$data->user->loged_in_user->id."'";
                    //error_log(print_r($q,1));
                    $result = self::$con->query($q,true);
                    //error_log(print_r($result,1));
                }else{
                    //error_log("add_to_wishList not exists");
                    $q = "INSERT INTO `wishlists` (`uid`,`wishlist`) VALUES ('".$data->user->loged_in_user->id."','".base64_encode(serialize(self::$wishList_data))."')";
                    //error_log(print_r($q,1));
                    $result = self::$con->query($q,true);
                    //error_log(print_r($result,1));
                }
                unset($q);
            }
		}
	}

	public static function get_wishList(){
        return self::$wishList_data;
	}

	public static function delete_from_wishList($entry_id){
		global $defaults;
        global $data;

		if (!empty(self::$wishList_data["prods"][$entry_id])){
			if (!empty(self::$wishList_data["prods"][$entry_id]['attachments'])){
				foreach (self::$wishList_data["prods"][$entry_id]['attachments'] as $file_name){
					unlink(site_config::get_value('upload_user_files_folder').$file_name);
				}
			}
			unset(self::$wishList_data["prods"][$entry_id]);
			self::calculate_wishList_price();
			$_SESSION['wishList'] = base64_encode(serialize(self::$wishList_data));

            //update with new data `whishlists` where current uid
            if(!empty($data->user->loged_in_user) && !empty(self::$con)){
                $q = "SELECT * FROM `wishlists` WHERE `uid` = '".$data->user->loged_in_user->id."'";
                $result = self::$con->query($q);
                if(!empty($result) && $result->num_rows > 0){
                    // error_log("delete_from_wishList exists");
                    $q = "UPDATE `wishlists` SET `wishlist` = '".base64_encode(serialize(self::$wishList_data))."'".
                         " WHERE `uid` = '".$data->user->loged_in_user->id."'";
                    // error_log(print_r($q,1));
                    $result = self::$con->query($q,true);
                    //error_log(print_r($result,1));
                }
                unset($q);
            }
		}
	}

	public static function calculate_wishList_price(){
		global $data;

		self::$wishList_data["total_price"] = 0;
		self::$wishList_data["prepay_price"] = 0;
		self::$wishList_data["discount"] = 0;
		self::$wishList_data["total_price_after_discount"] = 0;
		self::$wishList_data["price_to_pay"] = 0;
		if (!empty(self::$wishList_data["prods"])){

			$prod_in_cat_count = array();
			$prod_in_cat_count[1] = 0;
			$prod_in_cat_count[2] = 0;

			$discount = 0;

			foreach (self::$wishList_data["prods"] as $entry_id => $wishList_entry){

				/////////////////////////////////////////////////////////////////////////////////////////
				//	CUSTOM CALCULATION FOR TOSEE
				$prod = $data->product->get_by_id($wishList_entry['prod_id']);
				if (!empty($prod)){
					$cats_array = $prod->get_parent_cats_array();

					if (in_array("1", $cats_array)){
						$prod_in_cat_count[1] += $wishList_entry['quantity'];

						if ($prod_in_cat_count[1] == 2){
							// self::$wishList_data["total_price"] += 150;
							self::$wishList_data["discount"] += $wishList_entry['prod_price'] - 150;
						}
						else if ($prod_in_cat_count[1] > 2){
							// self::$wishList_data["total_price"] += 200;
							self::$wishList_data["discount"] += $wishList_entry['prod_price'] - 200;
						}
						else{
							// self::$wishList_data["total_price"] += $wishList_entry['total_price'];
						}
					}
					else if (in_array("2", $cats_array)){
						$prod_in_cat_count[2] += $wishList_entry['quantity'];

						if ($prod_in_cat_count[2] == 2){
							// self::$wishList_data["total_price"] += 79;
							self::$wishList_data["discount"] += $wishList_entry['prod_price'] - 79;
						}
						else if ($prod_in_cat_count[2] > 2){
							// self::$wishList_data["total_price"] += 100;
							self::$wishList_data["discount"] += $wishList_entry['prod_price'] - 100;
						}
						else{
							// self::$wishList_data["total_price"] += $wishList_entry['total_price'];
						}
					}
					else if (in_array("3", $cats_array)){
						self::$wishList_data["additional_info_needed"] = true;
					}
					else{
					}
				}
				/////////////////////////////////////////////////////////////////////////////////////////
        self::$wishList_data["total_price"] += $wishList_entry['total_price'];
        self::$wishList_data["prepay_price"] += $wishList_entry['prod_prepay_price'];
			}

			$coupon = $data->order->get_coupon();
			if (!empty($coupon)){
				if (empty($coupon->type)){ // discoun %
					$discount += ( $coupon->amount / 100 * self::$wishList_data["total_price"] );
				}
				else{ // discount fixed amount
					$discount += $coupon->amount;
				}
			}

			self::$wishList_data["discount"] += $discount;
			self::$wishList_data["total_price_after_discount"] = self::$wishList_data["total_price"] - self::$wishList_data["discount"];

			self::$wishList_data["price_to_pay"] = self::$wishList_data["total_price_after_discount"];
			if (!empty(self::$wishList_data["prepay_price"])){
				if (self::$wishList_data["total_price_after_discount"] > self::$wishList_data["prepay_price"])
					self::$wishList_data["price_to_pay"] = self::$wishList_data["prepay_price"];
			}
		}
	}

    public static function send_mail_to_finish(){
        //send mail to all user emails from `carts`
    }

	public static function is_empty(){
		if (empty(self::$wishList_data["prods"]))
			return 1;

		return (count(self::$wishList_data["prods"]) == 0);
	}

	public static function change_quantity($entry_id, $quantity){
		// if (!empty(self::$wishList_data[$entry_id])){
			// self::$wishList_data[$entry_id]['quantity'] =  $quantity;
			// $_SESSION['wishList'] = serialize(self::$wishList_data);
		// }
	}
}
?>
