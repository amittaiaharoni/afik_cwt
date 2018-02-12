<?

///////////////////////////////////////////////////////////////////////////////////////////////
//	GENERAL CONFIG	///////////////////////////////////////////////////////////////////////////

	$defaults['lang_id'] 		= 1;	
	$defaults['main_cat_id'] 	= 1;

	$defaults["site_name"] 			= 	"קשרי הפקות - הטאץ' הקטן לעסק שלך";
	
	$defaults["meta_title"]			=	"קשרי הפקות";
	$defaults["meta_keywords"]		=	"קשרי הפקות";
	$defaults["meta_description"]	=	"קשרי הפקות";

	$defaults["sub_folder"] 		= 	"2015"; 		
	$defaults["site_base_url"] 		= 	"http://afik10.co.il/~k55/"; 			
	$defaults["site_url"] 			= 	$defaults["site_base_url"].$defaults["sub_folder"]; 			
	$defaults["site_email_from"] 	= 	"sale@k55.co.il"; 		// used in emails going from the site to the user
	$defaults["site_contact_email"] = 	"dima.afik@gmail.com"; 	// used as contact email to display to the user
	$defaults["site_phone"] 		= 	"08-9570057"; 		// contact phone that will be displayed

///////////////////////////////////////////////////////////////////////////////////////////////
//	CART/ORDER CONFIG	///////////////////////////////////////////////////////////////////////

	$defaults["order_enabled"] 	= 	true; 	// if false, the order process will stop at the shopping cart

	$defaults["online_payment"] = 	true; 									// if true, user will be charged by tranzila/paypal, if false tranzila/paypal config will have no effect
	$defaults["use_tranzila"] 	= 	true && $defaults["online_payment"]; 	// if true, credit card will be charged as soon as order is submited
	$defaults["use_paypal"] 	= 	false && $defaults["online_payment"]; 	// if true, user will be redirected to paypal

	// tranzila config

	$defaults["tranzila_url"] 		= 	"https://direct.tranzila.com/k55/iframe.php"; 	// tranzila iframe url
	$defaults["tranzila_language"] 	= 	"il"; 	// language of the iframe - ( il, us, ru, es, de, fr, jp )
	$defaults["tranzila_currency"] 	= 	"1"; 	// currency of the iframe - ( NIS - 1, USD - 2, EU - 978, GBP - 826)
	
	// paypal config
	
	$defaults["paypal_live_url"] 	= 	"https://api-3t.paypal.com/nvp"; 			// paypal url for live transactions
	$defaults["paypal_sandbox_url"] = 	"https://api-3t.sandbox.paypal.com/nvp"; 	// paypal url for tests
	$defaults["paypal_use_sandbox"] = 	true; 	// is paypal in test mode

	$defaults["paypal_live_user"] 		= 	""; // paypal api user name for live transactions
	$defaults["paypal_live_pass"] 		= 	""; 		// paypal api password for live transactions
	$defaults["paypal_live_signature"] 	= 	""; 	// paypal api signature for live transactions

	$defaults["paypal_sandbox_user"] 		= 	""; // paypal api user name for tests 
	$defaults["paypal_sandbox_pass"] 		= 	""; 					// paypal api password for tests
	$defaults["paypal_sandbox_signature"] 	= 	""; // paypal api signature for tests	

	$defaults["paypal_url"] 		=	( $defaults["paypal_use_sandbox"] ? $defaults["paypal_sandbox_url"] : $defaults["paypal_live_url"] );

	$defaults["paypal_user"] 		= 	( $defaults["paypal_use_sandbox"] ? $defaults["paypal_sandbox_user"] : $defaults["paypal_live_user"] );
	$defaults["paypal_pass"] 		= 	( $defaults["paypal_use_sandbox"] ? $defaults["paypal_sandbox_pass"] : $defaults["paypal_live_pass"] );
	$defaults["paypal_signature"] 	= 	( $defaults["paypal_use_sandbox"] ? $defaults["paypal_sandbox_signature"] : $defaults["paypal_live_signature"] );

	$defaults["paypal_currency"] 	= 	"USD"; 	// https://developer.paypal.com/webapps/developer/docs/classic/api/currency_codes/#paypal
	$defaults["paypal_api_version"] = 	"119.0"; 

	// general
	
	$defaults['payments_num'] 	= 	"6"; 	// num of max credit card payments
	$defaults['shipping_enabled'] = true; 	// is shipping enabled, if false the shipping block will not display on the order page
	
	$defaults['allow_prod_file_attachments'] = true;  // allow users to attach files to their order

	
///////////////////////////////////////////////////////////////////////////////////////////////
//	SYSTEM CONFIG (do not change if you dont know what it is)	///////////////////////////////

	$user_ranks["user"] 		= 1;
	$user_ranks["superuser"] 	= 2;
	$user_ranks["admin"] 		= 3;

	$admin_path = "pages/admin/";
	$site_path 	= "pages/site/"; 
	
	$defaults["login_error_for_block"] 	= 3;
	$defaults["login_error_block_time"] = 600; //sec

	$defaults['upload_max_image_size'] 	= 1000; // kb
	$defaults['upload_max_file_size'] 	= 2000;	// kb
	$defaults['upload_images_folder'] 	= "upload/images/";
	$defaults['upload_thumbs_folder'] 	= "upload/images/thumbs/";
	$defaults['upload_files_folder'] 	= "upload/files/";
	$defaults['upload_user_files_folder'] 	= "upload/user_files/";

////////////////////////////////////////////////////////////////////////////////////////////////////
?>