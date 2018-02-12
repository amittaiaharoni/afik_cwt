<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>מערכת ניהול אפיק</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/sb-admin.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">

    <!-- Morris Charts CSS 
    <link href="css/plugins/morris.css" rel="stylesheet">-->

    <!-- Custom Fonts
    <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"> -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<link href="css/datatable/demo_table.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.css">
	<link href="css/datatable/dataTables.bootstrap.css" rel="stylesheet">
	<link href="css/datatable/demo_validation.css" rel="stylesheet">
	<link href="css/datatable/jquery-ui-1.7.2.custom.css" rel="stylesheet">
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/black-tie/jquery-ui.min.css" rel="stylesheet">
	<link rel="stylesheet" href="js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />

	<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/jquery.form.js"></script>
	<script type="text/javascript" src="js/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
	<script>
		$(document).ready(function() {
			$(document).ajaxStart(function(){
			 $("#ajax_spinner").show();
			});

			$(document).ajaxComplete(function(){
			 $("#ajax_spinner").hide();
			});

			$(".fancybox").fancybox();
			$(".datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				// dateFormat: 'dd-mm-yy',
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				yearRange: "-120:+10", 
			});
		});
	</script>
</head>

<body>

	<div id="ajax_spinner" style="display:none; z-index:1000;  width: 200px; height: 100px; margin-left: -50px; margin-top: -50px; position: absolute; left: 50%; top: 50%;">
		<img src="images/ajax_spinner.gif"/>
	</div>
    <div id="wrapper">
        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.html">מערכת ניהול אפיק</a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i> <b class="caret"></b></a>
                    <ul class="dropdown-menu message-dropdown">
                        <li class="message-preview">
                            <a href="#">
                                <div class="media">
                                    <span class="pull-left">
                                        <img class="media-object" src="http://placehold.it/50x50" alt="">
                                    </span>
                                    <div class="media-body">
                                        <h5 class="media-heading"><strong>John Smith</strong>
                                        </h5>
                                        <p class="small text-muted"><i class="fa fa-clock-o"></i> Yesterday at 4:32 PM</p>
                                        <p>Lorem ipsum dolor sit amet, consectetur...</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="message-preview">
                            <a href="#">
                                <div class="media">
                                    <span class="pull-left">
                                        <img class="media-object" src="http://placehold.it/50x50" alt="">
                                    </span>
                                    <div class="media-body">
                                        <h5 class="media-heading"><strong>John Smith</strong>
                                        </h5>
                                        <p class="small text-muted"><i class="fa fa-clock-o"></i> Yesterday at 4:32 PM</p>
                                        <p>Lorem ipsum dolor sit amet, consectetur...</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="message-preview">
                            <a href="#">
                                <div class="media">
                                    <span class="pull-left">
                                        <img class="media-object" src="http://placehold.it/50x50" alt="">
                                    </span>
                                    <div class="media-body">
                                        <h5 class="media-heading"><strong>John Smith</strong>
                                        </h5>
                                        <p class="small text-muted"><i class="fa fa-clock-o"></i> Yesterday at 4:32 PM</p>
                                        <p>Lorem ipsum dolor sit amet, consectetur...</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="message-footer">
                            <a href="#">Read All New Messages</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i> <b class="caret"></b></a>
                    <ul class="dropdown-menu alert-dropdown">
                        <li>
                            <a href="#">Alert Name <span class="label label-default">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-primary">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-success">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-info">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-warning">Alert Badge</span></a>
                        </li>
                        <li>
                            <a href="#">Alert Name <span class="label label-danger">Alert Badge</span></a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">View All</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> John Smith <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="index.php?page=admin&action=logout"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li class="active">
                        <a href="index.html"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                    </li>
					<!-- <li>
						<a href="index.php?page=admin_orders"><i class="fa fa-fw fa-table"></i> הזמנות</a>
					</li> -->
					<li>
						<a href="index.php?page=admin_products"><i class="fa fa-fw fa-table"></i> מוצרים</a>
					</li>
					<li>
						<a href="index.php?page=admin_productManage"><i class="fa fa-fw fa-table"></i>פקדי מוצר</a>
					</li>
					<li>
						<a href="index.php?page=admin_categories"><i class="fa fa-fw fa-table"></i> קטגוריות מוצר</a>
					</li>
					<li>
						<a href="index.php?page=admin_pharm"><i class="fa fa-fw fa-table"></i>בתי מרקחת</a>
					</li>
					<!-- <li>
						<a href="index.php?page=admin_popup_ad"><i class="fa fa-fw fa-table"></i> חלון קופץ</a>
					</li> -->
					<!-- <li>
						<a href="index.php?page=admin_mail_template"><i class="fa fa-fw fa-table"></i>שבלונות מיילים</a>
					</li> -->
					<li>
						<a href="index.php?page=admin_options"><i class="fa fa-fw fa-table"></i> אפשרויות מוצר</a>
					</li>
					<!-- <li>
						<a href="index.php?page=admin_manufacturers"><i class="fa fa-fw fa-table"></i> יצרנים</a>
					</li> -->
					<li>
						<a href="index.php?page=admin_info_pages"><i class="fa fa-fw fa-table"></i> דפי מידע</a>
					</li>
					<li>
						<a href="index.php?page=admin_info_cats"><i class="fa fa-fw fa-table"></i> קטגוריות דפי מידע</a>
					</li>
					<!-- <li>
						<a href="index.php?page=admin_coupons"><i class="fa fa-fw fa-table"></i>קופונים</a>
					</li> -->
					<!-- <li>
						<a href="index.php?page=admin_branches"><i class="fa fa-fw fa-table"></i>סניפים</a>
					</li> -->
                    <?php /*<li>
						<a href="index.php?page=admin_delivery_areas"><i class="fa fa-fw fa-table"></i>אזורי משלוח</a>
                        </li> */?>
                    <!-- <li>
						<a href="index.php?page=admin_delivery_types"><i class="fa fa-fw fa-table"></i>סוגי משלוח</a>
                    </li> -->
					<!-- <li>
						<a href="index.php?page=admin_delivery_places"><i class="fa fa-fw fa-table"></i>מקומות משלוח</a>
					</li> -->
					<!-- <li>
						<a href="index.php?page=admin_users"><i class="fa fa-fw fa-table"></i>משתמשים</a>
					</li> -->
					<!-- <li>
						<a href="index.php?page=admin_newsletter"><i class="fa fa-fw fa-table"></i>newsletter</a>
					</li> -->
					<!-- <li>
						<a href="index.php?page=admin_banners"><i class="fa fa-fw fa-table"></i>באנרים</a>
					</li> -->
					
					<!-- <li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#tools_submenu2">
							<i class="fa fa-fw fa-wrench"></i> מבצעים <i class="fa fa-fw fa-caret-down"></i>
						</a>
                        <ul id="tools_submenu2" class="collapse">
                            <li>
								<a href="index.php?page=admin_sale"><i class="fa fa-fw fa-table"></i>מבצעי קטגוריה</a>
							</li>
							<li>
								<a href="index.php?page=admin_sale_per_product"><i class="fa fa-fw fa-table"></i>מבצעי כמות מוצרים</a>
							</li>
                        </ul>
                    </li> -->
					<li>
						<a href="index.php?page=admin_site_config"><i class="fa fa-fw fa-table"></i> הגדרות אתר</a>
					</li>
					<li>
                        <a href="javascript:;" data-toggle="collapse" data-target="#tools_submenu">
							<i class="fa fa-fw fa-wrench"></i> כלים <i class="fa fa-fw fa-caret-down"></i>
						</a>
                        <ul id="tools_submenu" class="collapse">
                            <li>
                                <a href="index.php?page=admin_migrate_table">יבוא טבלה</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">
			{__page__}
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->



    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Morris Charts JavaScript 
    <script src="js/plugins/morris/raphael.min.js"></script>
    <script src="js/plugins/morris/morris.min.js"></script>
    <script src="js/plugins/morris/morris-data.js"></script>-->

	<script src="js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/responsive/1.0.7/js/dataTables.responsive.min.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/tabletools/2.2.3/js/dataTables.tableTools.min.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/f2c75b7247b/pagination/input.js"></script>
	<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/f2c75b7247b/pagination/select.js"></script>

	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
	<script src="js/jquery.validate.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
</body>

</html>
