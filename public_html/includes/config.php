<?

///////////////////////////////////////////////////////////////////////////////////////////////
//	GENERAL CONFIG	///////////////////////////////////////////////////////////////////////////

	site_config::add_default_value(	'lang_id', 		1);
	site_config::add_default_value(	'main_cat_id', 	1);

	site_config::add_default_value(	'site_name', 		"");

	site_config::add_default_value(	'meta_title', 		"");
	site_config::add_default_value(	'meta_keywords', 	"");
	site_config::add_default_value(	'meta_description', "");
	site_config::add_default_value(	'meta_og_image', "pics/logo.png");
	site_config::add_default_value(	'meta_og_title', "");

	site_config::add_default_value(	'sub_folder', 			"");
	site_config::add_default_value(	'site_base_url', 		"http://afik11.com/~cwt");
	site_config::add_default_value(	'site_url', 			site_config::get_default_value("site_base_url").site_config::get_default_value("sub_folder"));
	site_config::add_default_value(	'site_email_from', 		"info@cwt.org.il");		// used in emails going from the site to the user
	site_config::add_default_value(	'site_contact_email',	"aharoni.amittai@gmail.com");	// used as contact email to display to the user
	site_config::add_default_value(	'site_phone', 			"08-9287778");			// contact phone that will be displayed

///////////////////////////////////////////////////////////////////////////////////////////////
//	CART/ORDER CONFIG	///////////////////////////////////////////////////////////////////////

	site_config::add_default_value(	'order_enabled', 		true); // if false, the order process will stop at the shopping cart

	site_config::add_default_value(	'online_payment', 		true); // if true, user will be charged by tranzila/paypal, if false tranzila/paypal config will have no effect
	site_config::add_default_value(	'use_tranzila', 		true && site_config::get_default_value("online_payment")); 	// if true, credit card will be charged as soon as order is submited
	site_config::add_default_value(	'use_paypal', 			false && site_config::get_default_value("online_payment")); // if true, user will be redirected to paypal

	// tranzila config

	site_config::add_default_value(	'tranzila_terminal_name', 		"cwt"); // terminal_name
	site_config::add_default_value(	'tranzila_url', 		"https://direct.tranzila.com/".site_config::get_default_value("tranzila_terminal_name")."/iframe.php"); // tranzila iframe url
	site_config::add_default_value(	'tranzila_language', 	"il");	// language of the iframe - ( il, us, ru, es, de, fr, jp )
	site_config::add_default_value(	'tranzila_currency', 	"1");	// currency of the iframe - ( NIS - 1, USD - 2, EU - 978, GBP - 826)

	site_config::add_default_value(	'tranzila_use_delayed_payment', 	false);	// if true, tranzila will NOT charge the user, and the charge will have to be done manualy by admin
	site_config::add_default_value(	'tranzila_delayed_payment_url', 	"https://secure5.tranzila.com/cgi-bin/tranzila38.cgi");	// url for delayed transaction execution

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

	site_config::add_default_value("paypal_url",		( site_config::get_default_value("paypal_use_sandbox") ? site_config::get_default_value("paypal_sandbox_url") : site_config::get_default_value("paypal_live_url") ));

	site_config::add_default_value("paypal_user", 		( site_config::get_default_value("paypal_use_sandbox") ? site_config::get_default_value("paypal_sandbox_user") : site_config::get_default_value("paypal_live_user") ));
	site_config::add_default_value("paypal_pass", 		( site_config::get_default_value("paypal_use_sandbox") ? site_config::get_default_value("paypal_sandbox_pass") : site_config::get_default_value("paypal_live_pass") ));
	site_config::add_default_value("paypal_signature", 	( site_config::get_default_value("paypal_use_sandbox") ? site_config::get_default_value("paypal_sandbox_signature") : site_config::get_default_value("paypal_live_signature") ));

	site_config::add_default_value("paypal_currency", 		"USD"); 	// https://developer.paypal.com/webapps/developer/docs/classic/api/currency_codes/#paypal
	site_config::add_default_value("paypal_api_version", 	"119.0");

	// general

	site_config::add_default_value('payments_num', 			"6"); 	// num of max credit card payments

	site_config::add_default_value('shipping_enabled', 		true); 	// is shipping enabled, if false the shipping block will not display on the order page
	site_config::add_default_value('self_pickup_enabled', 	true);
	site_config::add_default_value('free_shipping_price', "300" ,	false); // free shipping from 300 nis

	site_config::add_default_value('allow_prod_file_attachments', true);  // allow users to attach files to their order

///////////////////////////////////////////////////////////////////////////////////////////////
//	SYSTEM CONFIG (do not change if you dont know what it is)	///////////////////////////////

	// DB
	site_config::add_default_value("db_host", 			'localhost', 	"text", false);
	site_config::add_default_value("db_name", 			'cwt_main', 	"text", false);

	site_config::add_default_value("db_reader_user", 	'cwt_reader', 	"text", false);
	site_config::add_default_value("db_reader_pass", 	'2XB!u#COsusd', "text", false);

	site_config::add_default_value("db_writer_user", 	'cwt_writer' , 	"text", false);
	site_config::add_default_value("db_writer_pass", 	'Od_&qlb.%FsF', "text", false);

	// General
	site_config::add_default_value("login_error_for_block", 	30, 						"text", false);
	site_config::add_default_value("login_error_block_time", 	6, 					"text", false); //sec

	site_config::add_default_value('upload_max_image_size', 	1000, 					"text"); // kb
	site_config::add_default_value('upload_max_file_size', 		15000, 					"text", false);	// kb
	site_config::add_default_value('upload_images_folder', 		"upload/images/", 		"text");
	site_config::add_default_value('upload_thumbs_folder', 		"upload/images/thumbs/","text");
	site_config::add_default_value('upload_files_folder', 		"upload/files/", 		"text", false);
	site_config::add_default_value('upload_user_files_folder', 	"upload/user_files/", 	"text", false);


	$user_ranks["user"] 		= 1;
	$user_ranks["superuser"] 	= 2;
	$user_ranks["admin"] 		= 3;

	site_config::add_default_value("minimum_user_rank_for_admin", 	3, 	"text", false);

	$admin_path = "pages/admin/";
	$site_path 	= "pages/site/";
////////////////////////////////////////////////////////////////////////////////////////////////////
?>
