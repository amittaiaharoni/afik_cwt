
	{__cart_table__}
<?
	
	$cart = cart::get_cart();
	
	if ($this->data->order->enabled()){
		if (!emptY($cart['prods'])){
	?>
	<div class="next">
		<a href="index.php?page=order">המשך לקופה</a>
	</div>
	<?
		}
	}
	else{	
	?>
	<h1>
		ברגע זה לא ניתן לבצע הזמנות באתר <br/>
		להזמנות חייגו : <?=site_config::get_value("site_phone")?>
	</h1>
	<?
	}
	?>