<?php global $show_side_menu;?>
<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
	<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-111547287-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-111547287-2');
</script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
		<meta content="telephone=no" name="format-detection">
		<meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
		<meta http-equiv="Cache-control" content="public">
		 <meta name="theme-color" content="#1a3c71" />
		 <link rel="Xicon" sizes="192x192" href="pics/cart_icon.png">
		 <link rel="shortcut icon" href="https://developers.google.com/_static/6db4302793/images/favicon.png">
		<meta property="og:image" content="{__meta-og-image__}" />
		<meta property="og:title" content="{__meta-og-title__}" />
		<meta property="og:type" content="website" />
		<link rel="image_src" href="{__meta-og-image__}" />

		<title>{__meta-title__}</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="stylesheet" type="text/css" href="font.css">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="js/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" />
		<link href="https://fonts.googleapis.com/css?family=Heebo:300,400,500" rel="stylesheet">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script async  class="rs-file" src="js/royal_slider/jquery.royalslider.js"></script>
		<script   class="rs-file" src="js/jquery.elevateZoom-3.0.8.min.js"></script>
        <link class="rs-file" href="js/royal_slider/royalslider.css" rel="stylesheet">
        <script async src="js/superfish.js"></script>
		
   <!--script src="js/modernizr.custom.js"></script
		<script src="js/modernizr-custom_prefixed.js"></script> -->

   
		<script src="js/jquery.form.js"></script>
        <link rel="stylesheet" type="text/css" href="css/component.css" />
        <link rel="stylesheet" type="text/css" href="css/component_mw.css" />
        <link rel="stylesheet" type="text/css" href="css/default_mw.css" />
       <link rel="stylesheet" type="text/css" href="css/style2.css" />
		<?
		if($is_mobile){
		?>

			<link rel="stylesheet" type="text/css" href="css/component_search.css" />
		<?
		}
		?>

		<!-- <script src="js/modernizr.custom2.js"></script> -->
	   <!--	<script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js"></script>-->
		<!-- <script src="js/modernizr.custom.js"></script> -->
		<script type="text/javascript" src="js/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>

	   <!--	<script src="js/modernizr.custom3.js"></script> -->
	   	<script async src="js/modernizr.custom.04022.js"></script>

		<?/*
		<!-- link to magiczoom.css file -->
		<link href="js/magiczoom/magiczoom.css" rel="stylesheet" type="text/css" media="screen"/>
		<!-- link to magiczoom.js file -->
		<script src="js/magiczoom/magiczoom.js" type="text/javascript"></script>
		*/?>

    <!-- slider stylesheets -->
    <link class="rs-file" href="js/royal_slider/rs-minimal-white.css" rel="stylesheet">

		<script type="text/javascript">
		function onBlur(el) {
			if (el.value == '') {
				el.value = el.defaultValue;
			}
		}
		function onFocus(el) {
			if (el.value == el.defaultValue) {
				el.value = '';
			}
		}

		$(window).load(function() {
			$(".rsImg").show();
			$(".infoBlock").show();
			$('#full-width-slider').royalSlider({
				autoPlay: {
					enabled: true,
					delay: 3000,
                    pauseOnHover: false
				},
				arrowsNav: false,
				loop: true,
				keyboardNavEnabled: true,
				controlsInside: false,
				imageScaleMode: 'fill',
				arrowsNavAutoHide: false,
				autoScaleSlider: true,
				autoScaleSliderWidth: 1200,
				autoScaleSliderHeight: 300,
				controlNavigation: 'bullets',
				thumbsFitInViewport: false,
				navigateByClick: true,
                arrowsNavHideOnTouch: true,
				transitionType:'move',
				globalCaption: false,
				deeplinking: {
				  enabled: true,
				  change: false
				},
				/* size of all images http://help.dimsemenov.com/kb/royalslider-jquery-plugin-faq/adding-width-and-height-properties-to-images */
				imgWidth: 1200,
				imgHeight: 300
			  });
		});

		$(document).ready(function(){
			$(".datepicker").datepicker({
				// dateFormat: 'yy-mm-dd',
				dateFormat: 'dd-mm-yy',
				changeMonth: true,
				changeYear: true,
				// showButtonPanel: true,
				yearRange: "-120:+10", 
			});
			$(".side_menu_lvl_1_link").click(function(){

				$(".side_menu_lvl_1_list").slideUp();
				$(".side_menu_lvl_2_list").slideUp();

				if(!$(this).next().is(":visible")){
					$(this).next().slideDown();
				}
			});

			$(".side_menu_lvl_2_link").click(function(){
				if(!$(this).next().is(":visible")){
					$(this).next().slideDown();
				}
				else{
					$(this).next().slideUp();
				}
			});

			<?
				if (!empty($_GET['cat'])){
				?>
				$(".side_menu_lvl_1_list").slideUp();
				$(".side_menu_lvl_2_list").slideUp();
				$(".side_menu_link_<?=(int)$_GET['cat']?>").parents("ul").slideDown();
				$(".side_menu_link_<?=(int)$_GET['cat']?>").addClass("active");
				<?
				}
			?>
			// side menu
			// $("#accordian h3").click(function(){
				// $("#accordian ul ul").slideUp();
				// if(!$(this).next().is(":visible")){
					// $(this).next().slideDown();
				// }
			// });

			$("#accordian2 ul li div").slideUp();
			$("#accordian2 ul li div").first().slideDown();
			$("#accordian2 h3").click(function(){
				//slide up all the link lists
				$("#accordian2 ul li div").slideUp();
				//slide down the link list below the h3 clicked - only if its closed
				if(!$(this).next().is(":visible"))
				{
					$(this).next().slideDown();
				}
			});
			
			$(window).scroll(function(){
				if ($(this).scrollTop() > 100) {
					$('#scrollup').fadeIn();
				} else {
					$('#scrollup').fadeOut();
				}
			});

			$("#scrollup").click(function(e) {
				e.preventDefault();
				$("html, body").animate({ scrollTop: 0 }, "slow");
				return false;
			});
		})
        </script>

	</head>
		<body>
			<?
			if(isset($_SESSION['sent'])){
				unset($_SESSION['sent']);
			?>
			<div id="mail_sent">
				<div id="close_me"><img src="pics/close.png" alt="" /></div>
				<h2>ההודעה נשלחה בהצלחה.<br>
                תודה על פנייתך.
				</h2>
			</div>
			<?
			}
			?>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>



        	<div id="wrapper" >

            <div id="header" class="cf">
					<div class="cbp-af-header">
				<div class="cbp-af-inner">
             <div class="width-wrapper cf">

            <div class="logo"><a href="index.php"><img src="pics/logo.png" /></a></div>
		  <!--	<div class="top_new_phone">משלוח מהיר עד הבית
			 <span>מוקד שרות</span>
			 <a style="text-decoration:none;" href="tel://052-403-3849">052-403-3849</a>
			</div>-->
       <!--     <div id="top_right">


			<?
                $cart_count = 0;
                $wishlist_count = 0;
                $cart = cart::get_cart();
                $wishlist = wishList::get_wishList();
                if (!empty($cart) && !empty($cart['prods']))
					$cart_count = count($cart['prods']);
                if (!empty($wishlist) && !empty($wishlist['prods']))
					$wishlist_count = count($wishlist['prods']);
			?>
			<div id="top_basket">
            	<a href="index.php?page=cart">
				<span>סל קניות</span>
                <span class="cart_icon"><i class="fa fa-shopping-cart"></i></span>
				<span class="count"><?=$cart_count?></span>
					</a>
            	<a href="index.php?page=wishlist" id="wish">
				<span>WISHLIST</span>
                <span class="cart_icon"><i class="fa fa-heart"></i></span>
				<span class="count"><?=$wishlist_count?></span>
			</a>

			</div>
               <div id="logo_mob"><a href="index.php"><img src="pics/logo.png" /></a></div>
			<?
			if ($this->data->user->loged_in){
			?>
				<div id="top_login">
					<a href="index.php?page=my_account">חשבון שלי</a>
					<span>&nbsp;<?=$this->data->user->loged_in_user->first_name?>&nbsp;</span>
					<a href="index.php?action=logout">התנתק</a>
				</div>
			<?
			}
			else{
			?>
				<div id="top_login">
					<a href="index.php?page=login">הכנס/הרשם</a>
				</div>
			<?
				}
			?>



            </div>-->

             </div>

       <div id="main_menu">
	<div class="width-wrapper cf">

        <div id="search">
<div id="search_toggle_icon"><button class="md-trigger" data-modal="modal-2"><img src="pics/search.png" alt="" /></button></div>
	<div class="md-modal md-effect-2" id="modal-2">
		<div class="md-content" class="cf">
         <form method="get" action="index.php">
		<input type="submit"  value="חיפוש" id="search_button">
		<input  name="search" value="חפש מוצר" type="text"  onfocus="onFocus(this)" onblur="onBlur(this)">
	</form>
	  <button class="md-close"><img src="pics/close.png" alt="" /></button>
	  <div class="clear"></div>
			</div>
		</div>
</div>




		{__top_menu__}


	</div>
	</div>
	</div>

     </div>
     </div>
    	<script src="js/classie.js"></script>
		<script src="js/cbpAnimatedHeader.min.js"></script>


<?
$popup_ad = $this->data->popup_ad->get_active_popups();
if(!empty($popup_ad)){
    if(is_array($popup_ad))
        $popup_ad = $popup_ad[array_rand($popup_ad,1)];
    else
        $popup_ad = $popup_ad[0];
}
//error_log(print_r($popup_ad,1));
if(!empty($popup_ad) && $popup_ad->active == 1){
?>
<script>
	$(function(){
		$(".banner").show();
		if(sessionStorage.closed){
			$(".banner").remove();
			$(".over_banner").hide();
		}
		else{
			$(".over_banner").show();
		}
		$(".banner .banner_close").click(function(){
			$(".over_banner").hide();
			$(this).parent().remove();clickBanner();
		});
		$(".over_banner").click(function(){
			$(".banner_close").parent().remove();
			$(this).hide();
			clickBanner();
		});

		//Hide rs until imgs fully loaded
		/* $("#scrollup").click(function(e) {
			e.preventDefault();
			$("html, body").animate({ scrollTop: 0 }, "slow");
			return false;
		}); */
		
		

	});
	function clickBanner() {
		sessionStorage.removeItem(closed);
		console.log(sessionStorage.closed);
		if(typeof(Storage) !== "undefined") {
			if (sessionStorage.closed) {
				sessionStorage.closed = 1;
				// $(".over_banner").hide();
			} else {
				sessionStorage.closed = 0;
				// $(".over_banner").hide();
			}
		}
	}

/* 	$(window).scroll(function(){
		if ($(this).scrollTop() > 100) {
			$('#scrollup').fadeIn();
		} else {
			$('#scrollup').fadeOut();
		}
	}); */
	


</script>

<?/*
    <?php if (!empty($popup_ad->image)){ ?>
	<div class="banner">

   		<button title="סגור חלון" class="banner_close" ><i class="fa fa-close"></i></button>
		<div>
		 <!--  <h1><?=$popup_ad->name?></h1>   -->
		</div>
        <?php if (!empty($popup_ad->link)){ ?>
        <a href="<?=$popup_ad->link?>">
           <img src='<?=site_config::get_value("upload_images_folder").$popup_ad->image?>' />
        </a>
		<!--<a href="<?=$popup_ad->link?>">
           <button><?=$popup_ad->name?></button>
        </a>-->
        <?php }else{ ?>
		<img src='<?=site_config::get_value("upload_images_folder").$popup_ad->image?>' />
        <?php } ?>
	   <!--	<div>
		   <h2><?//=$popup_ad->text?></h2>
		</div>-->
		<div class="clear"></div>
   </div>

 <div class="over_banner"></div>
    <?php } ?>
*/?>
<?
}
?>



<?
	if ($is_main){
?>
<div id="main_gallery">
<img src="pics/main_bg_pic.jpg" alt="" width="100%" />
    <!--<div class="width-wrapper">
	<div class="sliderContainer fullWidth clearfix">
		<div id="full-width-slider" class="royalSlider heroSlider rsMinW">
			  <?
				$home_page = $this->data->infoPage->get_by_id("3");
				$gallery = "";
				if (!empty($home_page)){
                    $images = $home_page->get_gallery();
					if (!emptY($images)){
						$gallery = $images;
					}
				}
				if (!empty($gallery)){
					$video = null;
					foreach($gallery as $check){
						if(!empty($check->video)){
							$video = $check;
							break;
						}
					}

					if(!empty($video) && !empty($video->video)){
					?>

						<div id="player" data-rsDelay="72000" ></div>
						<script>
							  var tag = document.createElement('script');

							  tag.src = "https://www.youtube.com/iframe_api";
							  var firstScriptTag = document.getElementsByTagName('script')[0];
							  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

							  var player;
							  function onYouTubeIframeAPIReady() {
								player = new YT.Player('player', {
								  height: '100%',
								  width: '100%',
								  playerVars: {
											rel : 0,
											autoplay: 1,
											loop: 1,
											controls: 0,
											showinfo: 0,
											autohide: 1,
											modestbranding: 1,
											vq: 'hd1080'},
								  videoId: "<?php echo $video->video?>",
								  events: {
									'onReady': onPlayerReady,
									'onStateChange': onPlayerStateChange
								  }
								});
							  }

							  function onPlayerReady(event) {
								var dur = player.getDuration();
								event.target.playVideo();
								player.mute();
								console.log($("#player").data());
								$("#player").data("rsdelay",dur * 1000);
							  }

							  var done = false;
							  function onPlayerStateChange(event) {
								 done = true;
								 if(done)
									event.target.playVideo();
							  }
							  function stopVideo() {
								player.stopVideo();
							  }
						</script>
					</div>
					<?
					}
					else{
					foreach ($gallery as $img){
						?>
						<div class="rsContent">
						<?
						if (false || !empty($img->video)){

						}else{
							?>
							<img class="rsImg" src="<?=site_config::get_value('upload_images_folder') . $img->image?>" alt="<?=$img->name?>" />
							<?
						}
						if (!empty($img->name) || !empty($img->desc)){
						?>
							<div class="infoBlock infoBlockLeftBlack rsABlock" data-fade-effect="" data-move-offset="10" data-move-effect="bottom" data-speed="200">


									<?
									if (!empty($img->link)){
									?>
										<a href="<?=$img->link?>">
                                <?
									}
									if (!empty($img->name)){
										echo "".$img->name;
									}
									if (!emptY($img->desc)){
									?>
										<h2><?=$img->desc?></h2>
									<?
									}
									if (!empty($img->link)){
									?>
										</a>
									<?
									}
									?>


							</div>
						<?
						}
						?>
						</div>
					<?
					}
					}

				}
				else {
				?>
			  <div class="rsContent">
				<img class="rsImg" src="pics/pic1.png" alt="" />
					<div class="infoBlock infoBlockLeftBlack rsABlock" data-fade-effect="" data-move-offset="10" data-move-effect="bottom" data-speed="200">
                  <div class="gal_title">
                  <div class="gal_logo"><img src="pics/flaminal_heb.png" alt="" /></div>
				  <h2>טיפול במגוון סוגי פצעים</h2>
				  </div>

               </div>
			  </div>
             	  <div class="rsContent">
				<img class="rsImg" src="pics/pic2.png" alt="" />
					<div class="infoBlock infoBlockLeftBlack rsABlock" data-fade-effect="" data-move-offset="10" data-move-effect="bottom" data-speed="200">
                  <div class="gal_title">
                  <div class="gal_logo"><img src="pics/revamil.png" alt="" /></div>
				  <h2>טיפול במגוון סוגי פצעים</h2>
				  </div>

               </div>
			  </div>

				<?
				}
				?>


		</div>
		</div>-->
	</div>
	<script>
		$(".rsImg").hide();
		$(".rsImg").first().show();
		$(".infoBlock").hide();
		$(".infoBlock").first().show();
	</script>
</div>

<? } ?>
    <?
	if (!$is_main){
?>
  <Div class="pnim_pic"><img src="pics/main_bg_pic.jpg" alt="" width="100%" /> </Div>
<?}?>
	<div id="holder">
    <div class="<?=$is_main?'width-wrapper_inner':'width-wrapper'?> cf">
            <?php if ($show_side_menu){ ?>
				{__page__}
                {__side_menu__}
            <?php }else{ ?>
            <div id="contain_info">
				{__page__}
            </div>

            <?php } ?>
			</div>
           	</div>

<footer class="cf">


<!--<div class="width-wrapper" >
<div id="footer_newsleter">
	<h2>להצטרפות לניוזלטר:</h2>
	<form method="post" action="">
		<input name="email" class="icon_inp" placeholder="כתובת האימייל" type="text" />

		<button name="send_quick" class="btn-default">הרשם</button>
	</form>
</div>




 <div id="footer_cards">


<div>SSL הקניה באתר מאובטחת בתקן</div>
<div class="cards"><img src="pics/cards.jpg" alt="" /></div>
</div>



<div class="clear"></div>

</div>-->
  <div class="footer_menu">

   <ul>
        <?
        $ips = $this->data->infoPage->get_by_column("show_in_footer_menu",1);
        if(!empty($ips)){
            //sorter::sort($ips,"display_order","asc");
            foreach($ips as $ip){
                ?>
                <li><a href="index.php?page=info_page&id=<?=$ip->id?>"><?=$ip->name?></a></li>
                <?
            }
        }
        ?>
        </ul>
</div>

  <div class="footer_address nomob">
       שרות לקוחות: 08-9287778 &nbsp;&nbsp;&nbsp;
        <a href="mailto:info@cwt.org.il">דוא”ל: info@cwt.org.il </a>
       </div>
	   <div class="footer_address mob">

	    <a href="tel:08-9287778">שרות לקוחות: 08-9287778 </a> <br>
        <a href="mailto:info@cwt.org.il">דוא”ל: info@cwt.org.il </a>
       </div>
  <div class="footer_social">
   <a href="https://www.facebook.com/cwtorgil" target="_blank"><i class="fa fa-facebook-square"></i></a>
    <a href="#" target="_blank"><i class="fa fa-youtube-square"></i></a>
  </div>
</footer>




        <div id="footer_bottom">
         <div id="credit">
         <a href="http://www.afik-p.co.il">אפיק פרסום בניית אתרים</a>
		</div>

        </div>
        </div>
        <div id="scrollup"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
        <!-------------------- GOOGLE ANALYTICS DANIEL ------------------------->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-69710738-1', 'auto');
  ga('send', 'pageview');

</script>
		<script async src="js/classie.js"></script>
		<script async src="js/modalEffects.js"></script>
 
        </body>
</html>
<?php unset($show_side_menu);?>
