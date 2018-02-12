<!--   <div class="dumm cf"></div>   -->
  <div class="title"><h1>התחבר</h1></div>

	<div class="login_form">
		<?
         printMessage("login");
	?>
		<form role="form" action="index.php?page=login" method="post">
			<div class="form-group">

				<input type="text" class="form-control" id="username" name="username" placeholder="דואר אלקרוני">
			</div>
			<div class="form-group">

				<input type="password" class="form-control" id="pass" name="pass" placeholder="סיסמה">
			</div>
			<button type="submit" name="login" class="btn btn-default">הכנס</button>
		</form>
	</div>
<?php /*
<script>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<div id="fb-root"></div>
<div class="fb-login-button" data-max-rows="1" data-size="large" data-show-faces="false" data-auto-logout-link="false"></div>
*/?>
  <div class="login_bg"><img src="<?=site_config::get_value('upload_images_folder') . $this->data->banner->get_by_id("5")->image?>" /></div>

<?//=site_config::get_value('upload_images_folder') . $this->data->banner->get_by_id("5")->image?>

 <div class="dumm cf"></div>
   <div class="title">	<h1>
		הרשמת משתמש חדש
	</h1></div>
	<div class="registration_form">
		<form action="index.php?page=login" method="post">
			{__user_details__}

			<button name="register" class="btn-default">
				הרשם
			</button>
		</form>
	</div>
    <div class="login_bg"><img src="<?=site_config::get_value('upload_images_folder') . $this->data->banner->get_by_id("6")->image?>" /></div>
	<div class="clear"></div>
<?php $cart = cart::get_cart(); ?>
<?php if (!empty($cart['prods'])){ ?>
 <div class="cf guest_form">
   <div class="title">	<h1>
		הזמנה כאורח	</h1></div>
        <div class="registration_form">
			<a href="index.php?page=order&guest" class="btn-default">
                הזמנה כאורח
			</a>
        </div>
 </div>
<?php } ?>
<?php unset($cart); ?>

<!--div id="contain_side2"><a href="index.php?page=register">לקוח חדש? הרשם כאן</a></div-->
