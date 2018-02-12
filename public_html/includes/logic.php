<?
// error_log(print_r($_COOKIE,1));
if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
    $_POST['login'] = 1;
    $_POST['username'] = base64_decode($_COOKIE['username']);
    $_POST['pass'] = base64_decode($_COOKIE['password']);
}
//error_log(print_r($_POST,1));
// $start = microtime(true);
$is_admin 	= false;
$is_main 	= false;
$is_mobile 	= false;

require_once 'includes/Mobile_Detect.php';
$mobile_detect = new Mobile_Detect;

if ( $mobile_detect->isMobile() ) {
    $is_mobile 	= true;
}

//////////////////////////////////////////////////////////////////////////////////////////////
////////	MAIN CAT
if (empty($_SESSION['main_cat_id'])){
    $_SESSION['main_cat_id'] = site_config::get_value('main_cat_id');
}
if (!empty($_GET['main_cat'])){
    $_SESSION['main_cat_id'] = (int)$_GET['main_cat'];

}
switch ($_SESSION['main_cat_id']){
case 1:
    $_SESSION['main_info_cat_id'] = 1;
    break;
case 2:
    $_SESSION['main_info_cat_id'] = 2;
    break;
case 3:
    $_SESSION['main_info_cat_id'] = 3;
    break;
case 4:
    $_SESSION['main_info_cat_id'] = 4;
    break;
case 5:
    $_SESSION['main_info_cat_id'] = 5;
    break;
case 6:
    $_SESSION['main_info_cat_id'] = 6;
    break;
case 7:
    $_SESSION['main_info_cat_id'] = 7;
    break;
}
//////////////////////////////////////////////////////////////////////////////////////////////

if (!empty($_GET['page'])){
    if (startsWith($_GET['page'], "admin"))
        $is_admin = true;
}

$data = new data();

language::init(); // load languages

//////////////////////////////////////////////////////////////////////////////////////////////
////////	USER

$data->user->controller->init(); // check login, register user, update user ......

$data->order->init_order();
// unset($_SESSION['cart']);
cart::init_cart(); // add/delete from cart

wishList::init_wishList(); // add/delete from wishlist

contact::send_contact(); // check if there is contact msgs to be sent and send them

//////////////////////////////////////////////////////////////////////////////////////////////
////////	COUPONS

if (!empty($_POST['coupon_code'])){
    $data->order->add_coupon($_POST['coupon_code']);
    exit(); // ajax;
}
if(isset($_POST['add_giftcard_code_credits']) && !empty($_POST['giftcard_code']) && !empty($data->user->loged_in_user)){
	// error_log('Code is '.$_POST['giftcard_code']);
	if(!$data->coupon->add_credits($_POST['giftcard_code'])){
		$_SESSION['giftcard_used_error'] = 'כבר השתמשת בקוד של גיפטקארד';
	}
}
// if(!empty($data->user->loged_in_user)){
	// error_log('asdasdasdsa '.print_r($data->user->loged_in_user,1));
// }

//////////////////////////////////////////////////////////////////////////////////////////////
////////	CHOSE INCLUDE PAGE
$meta_title 		= site_config::get_value("meta_title");
$meta_keywords 		= site_config::get_value("meta_keywords");
$meta_description 	= site_config::get_value("meta_description");
$meta_og_image 		= site_config::get_value("meta_og_image");
$meta_og_title 		= site_config::get_value("meta_og_title");

$template_path = $site_path."template.php";
$show_side_menu = true;

if (!empty($_REQUEST['action'])){
    switch ($_REQUEST['action']){
	case "get_prods_linked_options_details":
        if(!empty($_POST['prod_id']) && !empty($_POST['option_id']) && !empty($_POST['det_id'])){
          $option = $data->option->get_by_id((int)$_POST['option_id']);
          $prod = $data->product->get_by_id((int)$_POST['prod_id']);
          $ret = array();
          if (!emptY($prod) && !empty($option)){
            $cats = $prod->categories;
            $linked_options = $option->get_linked_options($cats);

            if (!empty($linked_options)){
              foreach ($linked_options as $linked_option){
                $linked_details = $linked_option->get_details();

                foreach ($linked_details as $linked_detail){
                  $linked_details_data = $data->details2details->get_linked_detail_data($linked_detail->id, (int)$_POST['det_id'], $prod->id);
                  if (!empty($linked_details_data)) {
                    //$ret['linked_options']['by_id'][$linked_option->id]['active_details']['by_id'][$linked_detail->id] = $linked_details_data->detail_id_1;
                    $ret['active_details_linked_details'][] = $linked_details_data->detail_id_1;
                  }
                }
              }
            }
          }
          echo json::encode($ret);
        }
        exit();
      break;
	  case "get_images_details_from_color_option_for_prod":
        if(!empty($_POST['prod_id'])){
          $option = $data->option->get_by_id('8');
          $details = $option->get_details_for_product((int)$_POST['prod_id']);
          $ret = array();
          foreach($details as $detail_data){
            for ($i = 1; $i < 7; $i++){
              if(isset($_POST['option_id']) && !empty($_POST['option_id'])){
                if($detail_data->id == $_POST['option_id']){
                  if (isset($detail_data->{"image".$i}) && !empty($detail_data->{"image".$i})){
                    $ret[] = array('src' => $detail_data->{"image".$i});
                  }
                }
              }
            }
          }
          // error_log($_POST['prod_id']." , ".$_POST['option_id']." ,".json::encode($ret));
          echo json::encode($ret);
        }
				exit();
      break;
    case "admin_connect_option_to_prods":
        if (!empty($_POST['opt_id'])){
            $option = $data->option->get_by_id($_POST['opt_id']);
            if (!empty($option))
                $option->connect_to_all_relevant_prods();
        }
        exit();
        break;
    case "admin_connect_option_det_to_prods":
        if (!empty($_POST['opt_det_id'])){
            $option_det = $data->optionDetail->get_by_id($_POST['opt_det_id']);
            if (!empty($option_det))
                $option_det->connect_to_all_relevant_prods();
        }
        exit();
        break;
	case 'save_address':
		$user = $data->user->loged_in_user;

		if(!empty($_POST['address'])){
			$user->address = $_POST['address'];
		}
		if(!empty($_POST['username'])){
			$user->username = $_POST['username'];
		}
		if(!empty($_POST['pass'])){
			$user->pass = $_POST['pass'];
		}
		if(!empty($_POST['first_name'])){
			$user->first_name = $_POST['first_name'];
		}
		if(!empty($_POST['last_name'])){
			$user->last_name = $_POST['last_name'];
		}
		if(!empty($_POST['phone'])){
			$user->phone = $_POST['phone'];
		}
		if(!empty($_POST['city'])){
			$user->city = $_POST['city'];
		}
		if(!empty($_POST['birthday'])){
			$user->birthday = date('Y-m-d',strtotime($_POST['birthday']));
		}
		$user->save();
	break;
	case 'add_comps':
		if(!empty($_POST['ids'])){
			$ids = explode('-',$_POST['ids']);
			// error_log(print_R($ids,1));
			foreach($ids as $id){
				$add_id = $data->product->get_by_id((int)$id);
				cart::add_to_cart($add_id->id,1,'');
			}
			exit('true');
		}
		exit('false');
	break;
    }
}

if (!empty($_GET['page'])){
    switch ($_GET['page']){
        //////////////////////////////////////////////////////////////////////////////////////////////
        ////////	ADMIN
    case 'admin_login':
        $page = new view($admin_path."login.php");
        break;
    case 'admin_users':
        $data->user->controller->process_request();
        $page = $data->user->controller->get_view($admin_path."users.php");
        break;
    case 'admin_banners':
        $data->banner->controller->process_request();
        $page = $data->banner->controller->get_view($admin_path."banners.php");
        break;
	case 'admin_barcodes':
		$data->barcode->controller->process_request();
		$page = $data->barcode->controller->get_view($admin_path."barcodes.php");
		break;
	case 'admin_pharm':
		$data->pharm->controller->process_request();
		$page = $data->pharm->controller->get_view($admin_path."pharm.php");
		break;
    case 'admin_popup_ad':
        $data->popup_ad->controller->process_request();
        $page = $data->popup_ad->controller->get_view($admin_path."popup_ad.php");
        break;
    case 'admin_mail_template':
        $data->mail->controller->process_request();
        $page = $data->mail->controller->get_view($admin_path."mail_templates.php");
        break;
	 case 'admin_newsletter':
        $data->newsletter->controller->process_request();
        $page = $data->newsletter->controller->get_view($admin_path."newsletter.php");
        break;
    case 'admin_orders':
        $data->order->controller->process_request();
        $page = $data->order->controller->get_view($admin_path."orders.php");
        break;
    case 'admin_edit_order':
        $data->order->controller->process_request();
        $page = new view($admin_path."order_edit.php");
        break;
    case 'admin_products':
        $data->product->controller->process_request();
        $page = $data->product->controller->get_view($admin_path."products.php");
        break;
    case 'admin_product_files':
        $data->productFile->controller->process_request();
        $page = $data->productFile->controller->get_view($admin_path."product_files.php");
        break;
    case 'admin_productManage':
        $controller = new product_manage_controller();
        $controller->process_request();
        $page = new view($admin_path."productManage.php");
        break;
    case 'admin_coupons':
        $data->coupon->controller->process_request();
        $page = $data->coupon->controller->get_view($admin_path."coupons.php");
        break;
    case 'admin_products_to_options':
        if (!empty($_GET['prod_id'])){
            $data->details2product->controller->process_request();
            $page = $data->details2product->controller->get_view($admin_path."products_to_options.php");
        }
        break;
    case 'admin_options':
        $data->option->controller->process_request();
        $page = $data->option->controller->get_view($admin_path."options.php");
        break;
    case 'admin_option_details':
        $data->optionDetail->controller->process_request();
        $page = $data->optionDetail->controller->get_view($admin_path."option_details.php");
        break;
    case 'admin_manufacturers':
        $data->manufacturer->controller->process_request();
        $page = $data->manufacturer->controller->get_view($admin_path."manufacturers.php");
        break;
    case 'admin_categories':
        $data->category->controller->process_request();
        $page = $data->category->controller->get_view($admin_path."categories.php");
        break;
    case 'admin_info_cats':
        $data->infoCat->controller->process_request();
        $page = $data->infoCat->controller->get_view($admin_path."info_cats.php");
        break;
    case 'admin_info_pages':
        $data->infoPage->controller->process_request();
        $page = $data->infoPage->controller->get_view($admin_path."info_pages.php");
        break;
    case 'admin_galleries':
        if (!empty($_REQUEST['action'])){
            $data->gallery->controller->process_request();
        }
        if (!empty($_GET['id'])){ // only existing galleries can be accessed
            $page = $data->gallery->controller->get_view($admin_path."galleries.php");
        }
        break;
    case 'admin_galleryImage':
        $data->galleryImage->controller->process_request();
        $page = $data->galleryImage->controller->get_view($admin_path."galleryImages.php");
        break;
    case 'admin_branches':
        $data->branch->controller->process_request();
        $page = $data->branch->controller->get_view($admin_path."branches.php");
        break;
    case 'admin_delivery_areas':
        $data->deliveryArea->controller->process_request();
        $page = $data->deliveryArea->controller->get_view($admin_path."delivery_areas.php");
        break;
    case 'admin_delivery_places':
        $data->deliveryPlace->controller->process_request();
        $page = $data->deliveryPlace->controller->get_view($admin_path."delivery_places.php");
        break;
    case 'admin_delivery_types':
        $data->deliveryType->controller->process_request();
        $page = $data->deliveryType->controller->get_view($admin_path."delivery_types.php");
        break;
    case 'admin_sale':
        $data->sale->controller->process_request();
        $page = $data->sale->controller->get_view($admin_path."sale.php");
        break;
	case 'admin_sale_per_product':
        $data->sale_per_product->controller->process_request();
        $page = $data->sale_per_product->controller->get_view($admin_path."sale_per_product.php");
        break;
    case 'admin_site_config':
        site_config::update_values();
        $page = $data->product->controller->get_view($admin_path."site_config.php");
        break;
    case 'admin_migrate_table':
        $page = $data->product->controller->get_view($admin_path."migrate_table.php");
        break;
        //////////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////////////////////////////////////////////////////////////////////
        ////////	SITE
    case 'login':
        $show_side_menu = false;
        if ($data->user->loged_in){
            header('Location: index.php'); // was ?page=cart
            exit();
        }

        $page = new view($site_path."login.php");

        $user_details = new view($site_path."components/user_details.php");
        $page->register_include("user_details", $user_details);
        break;
        // case 'register':
        // if ($data->user->loged_in){
        // header('Location: index.php');
        // exit();
        // }

        // $page = new view($site_path."register.php");

        // $user_details = new view($site_path."components/user_details.php");
        // $page->register_include("user_details", $user_details);
        // break;
    case 'info_category':
        $page = $data->infoCat->controller->get_view($site_path."info_cat.php");
        if (!empty($_GET['id'])){
            $cat = $data->infoCat->get_by_id((int)$_GET['id']);
            if (!emptY($cat)){
                $meta_title 		= get_meta($cat, "meta_title");
                $meta_keywords 		= get_meta($cat, "meta_keywords");
                $meta_description 	= get_meta($cat, "meta_description");
                $meta_og_title 		= get_meta($cat, "meta_title");
            }
        }
        break;
    case 'info_page':
		$show_side_menu = true;
        $page = $data->infoPage->controller->get_view($site_path."info_page.php");
        if (!empty($_GET['id'])){
            $info_page = $data->infoPage->get_by_id((int)$_GET['id']);
            if (!emptY($info_page)){
                $meta_title 		= get_meta($info_page, "meta_title");
                $meta_keywords 		= get_meta($info_page, "meta_keywords");
                $meta_description 	= get_meta($info_page, "meta_description");
                $meta_og_title 		= get_meta($info_page, "meta_title");
            }
        }
        break;
    case 'category':
        $page = $data->category->controller->get_view($site_path."category.php");
        if (!empty($_GET['cat_id'])){
            $cat = $data->category->get_by_id((int)$_GET['cat_id']);
            if (!emptY($cat)){
                $meta_title 		= get_meta($cat, "meta_title");
                $meta_keywords 		= get_meta($cat, "meta_keywords");
                $meta_description 	= get_meta($cat, "meta_description");
                $meta_og_title 		= get_meta($cat, "meta_title");
            }
        }

        $products = new view($site_path."components/products.php");
        $page->register_include("products", $products);
        break;
    case 'products':
        $page = new view($site_path."products.php");

        $products = new view($site_path."components/products.php");
        $page->register_include("products", $products);
        break;
    case 'product':
        $show_side_menu = false;
        $page = $data->product->controller->get_view($site_path."product.php");
        if (!empty($_GET['prod_id'])){
            $prod = $data->product->get_by_id((int)$_GET['prod_id']);

            if (!emptY($prod)){
                if (!empty($prod->image)){
                    $meta_og_image 	= site_config::get_value('upload_images_folder').$prod->image;
                }
                if (!empty($prod->meta_title) && $prod->meta_title->to_string() != ""){
                    $meta_title 	= $prod->meta_title->to_string();
                    $meta_og_title 	= $prod->meta_title->to_string();
                }
                else{
                    if (!empty($prod->name)){
                        $meta_title 	= $prod->name->to_string();
                        $meta_og_title 	= $prod->name->to_string();
                    }
                }
                if (!empty($prod->meta_keywords) && $prod->meta_keywords->to_string() != "")
                    $meta_keywords = $prod->meta_keywords->to_string();
                if (!empty($prod->meta_description) && $prod->meta_description->to_string() != "")
                    $meta_description = $prod->meta_description->to_string();

                if (empty($_SESSION["viewed_prods"]) || empty($_SESSION["viewed_prods"][(int)$_GET['prod_id']])){
                    $prod->views_count++;
                    $prod->save();
                    $_SESSION["viewed_prods"][(int)$_GET['prod_id']] = 1;
                }

                $comp_look = new view($site_path."components/complete_the_look.php",$prod);
                $page->register_include("linked_prods", $comp_look);
            }

        }
        break;
    case 'sale':
        $page = new view($site_path."sale.php");
        break;
	case 'bet_merkahat':
        $page = new view($site_path."bet_merkahat.php");
	break;
	case 'maamar':
		$show_side_menu = true;
        $page = new view($site_path."maamar.php");
	break;
    case 'wishlist':
        //$show_side_menu = false;
        $page = new view($site_path."wishlist.php");

        $data->user->controller->check_login();
        $wishlist_table = new view($site_path."components/wishlist_table.php");
        $page->register_include("wishlist_table", $wishlist_table);

        break;
    case 'my_orders':
        $data->user->controller->check_login();

        //$page = new view($site_path."my_orders.php");
        $page = new view($site_path."archive.php");
        break;
    case 'my_account':
        $show_side_menu = false;
        $data->user->controller->check_login();

        $page = new view($site_path."account.php");

        $wishlist_table = new view($site_path."components/wishlist_table.php");
        $page->register_include("wishlist_table", $wishlist_table);

        $credits = new view($site_path."components/credits.php");
        $page->register_include("credits", $credits);

        $orders_hist = new view($site_path."components/orders_hist_list.php");
        $page->register_include("orders_archive", $orders_hist);
		
		$orders_hist = new view($site_path."components/orders_hist_list2.php");
        $page->register_include("orders_archive_2", $orders_hist);

        $user_profile = new view($site_path."components/user_profile.php");
        $page->register_include("user_profile", $user_profile);
        break;
    case 'contact':
        $page = new view($site_path."contact.php");
		 $show_side_menu = false; 
        break;

   case 'bet_merkahat':
        $page = new view($site_path."bet_merkahat.php");
        break;
	case 'giftcard':
		$show_side_menu = false;
		$page = new view($site_path."send_giftcard.php");
	break;
    case 'cart':
        $show_side_menu = false;

        $page = new view($site_path."cart.php");

        $cart_table = new view($site_path."components/cart_table.php");
        $page->register_include("cart_table", $cart_table);

        $giftcard_form = new view($site_path."components/gift_card.php");
        $page->register_include("giftcard_form", $giftcard_form);
		
		// $order_side = new view($site_path."components/order_side_block.php");
        // $order_side = new view($site_path."components/cart_table.php");
        // $page->register_include("order_side", $order_side);
        break;
    case 'order':
        $show_side_menu = false;

        if (!$data->order->enabled() || cart::is_empty()){
            header('Location: index.php?page=cart');
            exit();
        }
        if ($data->user->loged_in || isset($_GET['guest']) ) {
			if(empty($data->user->loged_in))
				$_SESSION['guest'] = true;
            $page = new view($site_path."order_step_2.php");
        }
        else {
            //$page = new view($site_path."order_step_1.php");
            echo '<script language="javascript">';
            //echo 'alert("התחבר/הרשם");';
            echo 'location.replace("index.php?page=login");';
            echo '</script>';
            //header('Location: index.php?page=login');
            exit();
        }

        $cart_table = new view($site_path."components/cart_table.php");
        $page->register_include("cart_table", $cart_table);

        $user_details = new view($site_path."components/user_details.php");
        $page->register_include("user_details", $user_details);

        // $order_side = new view($site_path."components/order_side_block.php");
        // $order_side = new view($site_path."components/cart_table.php");
        // $page->register_include("order_side", $order_side);
        break;
    case 'order_step_2':
        $show_side_menu = false;
		
		if(!isset($_SESSION['guest']))
			$data->user->controller->check_login(1, "index.php?page=order");

        if (!$data->order->enabled() || cart::is_empty()){
            header('Location: index.php?page=cart');
            exit();
        }
        $page = new view($site_path."order_step_2.php");

        $cart_table = new view($site_path."components/cart_table.php");
        $page->register_include("cart_table", $cart_table);

        // $order_side = new view($site_path."components/order_side_block.php");
        $order_side = new view($site_path."components/cart_table.php");
        $page->register_include("order_side", $order_side);
        break;
    case 'order_step_3':
        $show_side_menu = false;
		error_log('GUEST VAR '.isset($_SESSION['guest'])?"exists":"not exists");
		if(!isset($_SESSION['guest']))
			$data->user->controller->check_login(1, "index.php?page=order");

        if (!$data->order->enabled() || cart::is_empty() ||
            (!site_config::get_value("use_tranzila") && !site_config::get_value("use_paypal"))){
                header('Location: index.php?page=cart');
                exit();
            }
		if(isset($_POST['submit_order'])){
			$data->order->process_order_details();
			$data->order->execute_order();
		}
        $page = new view($site_path."order_step_3.php");
        $cart_table = new view($site_path."components/cart_table.php");
        $page->register_include("cart_table", $cart_table);
        break;
    case 'order_end':
        $show_side_menu = false;
		if(!isset($_SESSION['guest']))
			$data->user->controller->check_login();

        // $data->order->process_order_details();
        // $data->order->execute_order();
        $page = new view($site_path."order_end.php");
        break;
    case 'tranzila_success':
        error_log("tranzila_success");
        error_log(print_r($_POST,true));

        tranzila::process_report();
        //increase credits
		cart::empty_cart();
        $template_path = $site_path."tranzila_frame.php";
        $page = new view($site_path."tranzila_success.php");
        break;
    case 'tranzila_error':
        error_log("tranzila_error");
        error_log(print_r($_POST,true));

        tranzila::process_report();
		// cart::empty_cart();
        $template_path = $site_path."tranzila_frame.php";
        $page = new view($site_path."tranzila_error.php");
        break;
    case 'tranzila_report':
        error_log("tranzila_report");
        error_log(print_r($_POST,true));

        // tranzila::process_report();

        exit();
        break;
    case 'paypal_return':
        error_log("paypal_return");
        error_log(print_r($_REQUEST, true));

        if (!empty($_GET['order_id'])){
            $order = $this->data->order->get_by_id($_GET['order_id']);
            if (!empty($order)){
                $sum_to_pay = 0;
                $sum_to_pay = $order->price_to_pay;

                // $sum_to_pay = 1;
                if ($sum_to_pay > 0){

                    // get transaction details
                    $data = array(
                        'METHOD' 	=> 'GetExpressCheckoutDetails',
                        'VERSION' 	=> site_config::get_value("paypal_api_version"),
                        'USER' 		=> site_config::get_value("paypal_user"),
                        'PWD' 		=> site_config::get_value("paypal_pass"),
                        'SIGNATURE' => site_config::get_value("paypal_signature"),
                        'TOKEN' 	=> $_REQUEST['token']
                    );

                            /*
                                SAMPLE RESPONSE

                                TIMESTAMP=2007%2d04%2d05T23%3a44%3a11Z
                                &CORRELATIONID=6b174e9bac3b3
                                &ACK=Success
                                &VERSION=XX%2e000000
                                &BUILD=1%2e0006
                                &TOKEN=EC%2d1NK66318YB717835M
                                &EMAIL=YourSandboxBuyerAccountEmail
                                &PAYERID=7AKUSARZ7SAT8
                                &PAYERSTATUS=verified
                                &FIRSTNAME=...
                                &LASTNAME=...
                                &COUNTRYCODE=US
                                &BUSINESS=...
                                &PAYMENTREQUEST_0_SHIPTONAME=...
                                &PAYMENTREQUEST_0_SHIPTOSTREET=...
                                &PAYMENTREQUEST_0_SHIPTOCITY=...
                                &PAYMENTREQUEST_0_SHIPTOSTATE=CA
                                &PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=US
                                &PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME=United%20States
                                &PAYMENTREQUEST_0_SHIPTOZIP=94666
                                &PAYMENTREQUEST_0_ADDRESSID=...
                                &PAYMENTREQUEST_0_ADDRESSSTATUS=Confirmed
                             */

                    $response_str = send_post_request(site_config::get_value("paypal_url"), $data);

                    if (!emptY($response_str)){
                        parse_str($response_str, $response);

                        error_log("paypal GetExpressCheckoutDetails response");
                        error_log(print_r($response, true));
                    }

                    // execute transaction
                    $data = array(
                        'METHOD' 	=> 'DoExpressCheckoutPayment',
                        'VERSION' 	=> site_config::get_value("paypal_api_version"),
                        'USER' 		=> site_config::get_value("paypal_user"),
                        'PWD' 		=> site_config::get_value("paypal_pass"),
                        'SIGNATURE' => site_config::get_value("paypal_signature"),
                        'TOKEN' 	=> $_REQUEST['token'],
                        'PAYERID' 	=> $_REQUEST['payerid'],
                        'PAYMENTREQUEST_0_PAYMENTACTION' 	=> 'Sale',
                        'PAYMENTREQUEST_0_AMT' 				=> $sum_to_pay,
                        'PAYMENTREQUEST_0_CURRENCYCODE' 	=> site_config::get_value("paypal_currency")
                    );

                            /*
                                SAMPLE RESPONSE

                                TIMESTAMP=2007%2d04%2d05T23%3a30%3a16Z
                                &CORRELATIONID=333fb808bb23
                                ACK=Success
                                &VERSION=XX%2e000000
                                &BUILD=1%2e0006
                                &TOKEN=EC%2d1NK66318YB717835M
                                &PAYMENTREQUEST_0_TRANSACTIONID=043144440L487742J
                                &PAYMENTREQUEST_0_TRANSACTIONTYPE=expresscheckout
                                &PAYMENTREQUEST_0_PAYMENTTYPE=instant
                                &PAYMENTREQUEST_0_ORDERTIME=2007%2d04%2d05T23%3a30%3a14Z
                                &PAYMENTREQUEST_0_AMT=19%2e95
                                &PAYMENTREQUEST_0_CURRENCYCODE=USD
                                &PAYMENTREQUEST_0_TAXAMT=0%2e00
                                &PAYMENTREQUEST_0_PAYMENTSTATUS=Pending
                                &PAYMENTREQUEST_0_PENDINGREASON=authorization
                                &PAYMENTREQUEST_0_REASONCODE=None
                             */

                    $response_str = send_post_request(site_config::get_value("paypal_url"), $data);

                    if (!emptY($response_str)){
                        parse_str($response_str, $response);

                        error_log("paypal DoExpressCheckoutPayment response");
                        error_log(print_r($response, true));

                        if ($response['ACK'] == "Success"){
                            // payment is ok
                            error_log("paypal payment is ok");
                        }
                    }
                }
                else{
                    error_log("paypal_return -> sum is 0 for user_id = " . $_SESSION['user_id'] . ", order_id = " . $_SESSION['last_order_id']);
                }
            }
            else{
                error_log("paypal_return -> order not found for user_id = " . $_SESSION['user_id'] . ", order_id = " . $_SESSION['last_order_id']);
            }
        }
        else{
            error_log("paypal_return -> no order id for user " . $_SESSION['user_id']);
        }

        break;
        //////////////////////////////////////////////////////////////////////////////////////////////
    default: // main page
        $page = $data->category->controller->get_view(($is_admin?$admin_path:$site_path)."main.php");
        $is_main = true;

        $brands = new view($site_path."components/brands.php");
        $page->register_include("brands", $brands);
        break;
    }
}
else{
    $page = $data->category->controller->get_view($site_path."main.php");
    $is_main = true;

    $brands = new view($site_path."components/brands.php");
    $page->register_include("brands", $brands);
}

if(empty($page))
    $page = new view("404.html");


/////////////////////////////////////////////////////////////////////////////////////////////////
//		BUILD TEMPLATE
if ($is_admin){
    $template_path = $admin_path."template.php";
    $template = new view($template_path);
}else{
    /////////////////////////////////////////////////////////////////////////////////////////////////
    //		SITE INCLUDES
    $template = new view($template_path);

    // TOP MENU
    $top_menu = new view($site_path."components/top_menu.php");
    $template->register_include("top_menu", $top_menu);

    // MAIN GAL
    if ($is_main){
        $main_gal = new view($site_path."components/main_gal.php");
        $template->register_include("main_gal", $main_gal);
    }
    else{
        if ($show_side_menu){
            // side_menu
            $side_menu = new view($site_path."components/side_menu.php");
            $template->register_include("side_menu", $side_menu);
        }

        // BRANDS
        $brands = new view($site_path."components/brands.php");
        $template->register_include("brands", $brands);

    }
}

// $time_elapsed_us = microtime(true) - $start;
// error_log("before get_html : " . $time_elapsed_us);

if (empty($meta_title))
    $meta_title 		= site_config::get_value("meta_title");
if (empty($meta_keywords))
    $meta_keywords 		= site_config::get_value("meta_keywords");
if (empty($meta_description))
    $meta_description 	= site_config::get_value("meta_description");
if (empty($meta_og_title))
    $meta_og_title 		= site_config::get_value("meta_og_title");
if (empty($meta_og_image))
    $meta_og_image 		= site_config::get_value("meta_og_image");

$meta_og_image = site_config::get_value("site_url").$meta_og_image;

$template->register_include("meta-title", 		$meta_title);
$template->register_include("meta-keywords", 	$meta_keywords);
$template->register_include("meta-description", $meta_description);
$template->register_include("meta-description", $meta_description);
$template->register_include("meta-description", $meta_description);
$template->register_include("meta-og-image", 	$meta_og_image);
$template->register_include("meta-og-title", 	$meta_og_title);

$template->register_include("page", $page);
$output_html = $template->get_html();
/////////////////////////////////////////////////////////////////////////////////////////////////

/*if(!empty($_POST['action'])){
    header('Location:'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
    die();
}*/
if(!empty($_POST['action'])){
    header('Location:'.full_cur_url());
    die();
}


if (!empty($output_html)){
    echo $output_html;
}else{
    header('Location: 404.html');
}


// $product = $data->product->get_by_id("1");
// $products = $data->product->get_all();

// sorter::sort($products,'date','desc');

// error_log($product->name);
// error_log(print_r($product, true));

// $product->name = "new name";
// $product->save();

// error_log($product->name);
// error_log(print_r($product, true));

// $time_elapsed_us = microtime(true) - $start;
// error_log("logic end : " . $time_elapsed_us);

?>
