<?

///////////////////////////////////////////////////////////////////////////////////////////////
//	GENERAL CONFIG	///////////////////////////////////////////////////////////////////////////
	
	site_config::add_default_value(	'lang_id', 		1);
	site_config::add_default_value(	'main_cat_id', 	1);
	
	site_config::add_default_value(	'site_name', 		"קשרי הפקות - הטאץ' הקטן לעסק שלך");
	
	site_config::add_default_value(	'meta_title', 		"קשרי הפקות");
	site_config::add_default_value(	'meta_keywords', 	"קשרי הפקות");
	site_config::add_default_value(	'meta_description', "קשרי הפקות");
	
	site_config::add_default_value(	'sub_folder', 			"");
	site_config::add_default_value(	'site_base_url', 		"http://afik10.co.il/~tosee/");
	site_config::add_default_value(	'site_url', 			site_config::get_value("site_base_url").site_config::get_value("sub_folder"));
	site_config::add_default_value(	'site_email_from', 		"sale@tosee.co.il");		// used in emails going from the site to the user
	site_config::add_default_value(	'site_contact_email',	"dima.afik@gmail.com");	// used as contact email to display to the user
	site_config::add_default_value(	'site_phone', 			"08-9570057");			// contact phone that will be displayed
	
	$defaults['lang_id'] 		= 1;	
	$defaults['main_cat_id'] 	= 1;

	$defaults["site_name"] 			= 	"קשרי הפקות - הטאץ' הקטן לעסק שלך";
	
	$defaults["meta_title"]			=	"קשרי הפקות";
	$defaults["meta_keywords"]		=	"קשרי הפקות";
	$defaults["meta_description"]	=	"קשרי הפקות";

	$defaults["sub_folder"] 		= 	""; 		
	$defaults["site_base_url"] 		= 	"http://afik10.co.il/~tosee/"; 			
	$defaults["site_url"] 			= 	$defaults["site_base_url"].$defaults["sub_folder"]; 			
	$defaults["site_email_from"] 	= 	"sale@tosee.co.il"; 		// used in emails going from the site to the user
	$defaults["site_contact_email"] = 	"dima.afik@gmail.com"; 	// used as contact email to display to the user
	$defaults["site_phone"] 		= 	"08-9570057"; 		// contact phone that will be displayed
	
///////////////////////////////////////////////////////////////////////////////////////////////
//	CART/ORDER CONFIG	///////////////////////////////////////////////////////////////////////
	
	site_config::add_default_value(	'order_enabled', 		true); // if false, the order process will stop at the shopping cart
	
	site_config::add_default_value(	'online_payment', 		true); // if true, user will be charged by tranzila/paypal, if false tranzila/paypal config will have no effect
	site_config::add_default_value(	'use_tranzila', 		true && site_config::get_value("online_payment")); 	// if true, credit card will be charged as soon as order is submited
	site_config::add_default_value(	'use_paypal', 			false && site_config::get_value("online_payment")); // if true, user will be redirected to paypal
	
	// tranzila config
	
	site_config::add_default_value(	'tranzila_url', 		"https://direct.tranzila.com/tosee/iframe.php"); // tranzila iframe url
	site_config::add_default_value(	'tranzila_language', 	"il");	// language of the iframe - ( il, us, ru, es, de, fr, jp )
	site_config::add_default_value(	'tranzila_currency', 	"1");	// currency of the iframe - ( NIS - 1, USD - 2, EU - 978, GBP - 826)
	
	// paypal config
	
	site_config::add_default_value("paypal_live_url", 		"https://api-3t.paypal.com/nvp"); 			// paypal url for live transactions
	site_config::add_default_value("paypal_sandbox_url",	"https://api-3t.sandbox.paypal.com/nvp"); 	// paypal url for tests
	site_config::add_default_value("paypal_use_sandbox",	true); 	// is paypal in test mode

	site_config::add_default_value("paypal_live_user", 		""); // paypal api user name for live transactions
	site_config::add_default_value("paypal_live_pass", 		""); 		// paypal api password for live transactions
	site_config::add_default_value("paypal_live_signature", ""); 	// paypal api signature for live transactions

	site_config::add_default_value("paypal_sandbox_user",		""); // paypal api user name for tests 
	site_config::add_default_value("paypal_sandbox_pass", 		""); 					// paypal api password for tests
	site_config::add_default_value("paypal_sandbox_signature", 	""); // paypal api signature for tests	

	site_config::add_default_value("paypal_url",		( site_config::get_value("paypal_use_sandbox") ? site_config::get_value("paypal_sandbox_url") : site_config::get_value("paypal_live_url") ));

	site_config::add_default_value("paypal_user", 		( site_config::get_value("paypal_use_sandbox") ? site_config::get_value("paypal_sandbox_user") : site_config::get_value("paypal_live_user") ));
	site_config::add_default_value("paypal_pass", 		( site_config::get_value("paypal_use_sandbox") ? site_config::get_value("paypal_sandbox_pass") : site_config::get_value("paypal_live_pass") ));
	site_config::add_default_value("paypal_signature", 	( site_config::get_value("paypal_use_sandbox") ? site_config::get_value("paypal_sandbox_signature") : site_config::get_value("paypal_live_signature") ));

	site_config::add_default_value("paypal_currency", 		"USD"); 	// https://developer.paypal.com/webapps/developer/docs/classic/api/currency_codes/#paypal
	site_config::add_default_value("paypal_api_version", 	"119.0"); 
	
	// general
	
	site_config::add_default_value('payments_num', 		"6"); 	// num of max credit card payments
	site_config::add_default_value('shipping_enabled', 	true); 	// is shipping enabled, if false the shipping block will not display on the order page
	
	site_config::add_default_value('allow_prod_file_attachments', true);  // allow users to attach files to their order
	
	
	
	$defaults["order_enabled"] 	= 	true; 	// if false, the order process will stop at the shopping cart

	$defaults["online_payment"] = 	true; 									// if true, user will be charged by tranzila/paypal, if false tranzila/paypal config will have no effect
	$defaults["use_tranzila"] 	= 	true && $defaults["online_payment"]; 	// if true, credit card will be charged as soon as order is submited
	$defaults["use_paypal"] 	= 	false && $defaults["online_payment"]; 	// if true, user will be redirected to paypal

	// tranzila config

	$defaults["tranzila_url"] 		= 	"https://direct.tranzila.com/tosee/iframe.php"; 	// tranzila iframe url
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
	
	site_config::add_default_value("login_error_for_block", 	3, 						"text", false);
	site_config::add_default_value("login_error_block_time", 	600, 					"text", false); //sec

	site_config::add_default_value('upload_max_image_size', 	1000, 					"text", false); // kb
	site_config::add_default_value('upload_max_file_size', 	2000, 					"text", false);	// kb
	site_config::add_default_value('upload_images_folder', 	"upload/images/", 		"text", false);
	site_config::add_default_value('upload_thumbs_folder', 	"upload/images/thumbs/","text", false);
	site_config::add_default_value('upload_files_folder', 		"upload/files/", 		"text", false);
	site_config::add_default_value('upload_user_files_folder', "upload/user_files/", 	"text", false);
	
	
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
	
	define ("MAX_SIZE", $defaults['upload_max_image_size']);
	define ("MAX_SIZE_FILE", $defaults['upload_max_file_size']);
	define ('UPLOAD_DIR', $defaults['upload_images_folder']);
	define ('UPLOAD_DIR_THUMBS', $defaults['upload_thumbs_folder']);
	define ('UPLOAD_DIR_FILES', $defaults['upload_files_folder']);
////////////////////////////////////////////////////////////////////////////////////////////////////
?>