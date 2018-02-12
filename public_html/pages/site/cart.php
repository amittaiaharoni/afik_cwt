<div class="order_left_side in_cart">

	{__cart_table__}
<!--	{__giftcard_form__}

<div id="cart_side">
<?

	$cart = cart::get_cart();

	if ($this->data->order->enabled()){
		if (!emptY($cart['prods'])){
	?>
	<!--	<div id="checkout_btn">
			<a href="index.php?page=order">המשך לקופה&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;	<i class="fa fa-shopping-cart fa-flip-horizontal"></i></a>
		</div>---
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
</div>
-->
</div>
<!--
<div id="order_side">

<div id="checkout_btn">
			<a href="index.php?page=order">המשך לקופה&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;	<i class="fa fa-shopping-cart fa-flip-horizontal"></i></a>
		</div>
<!-- ------------------INCLUDE ---------------
{__order_side__}
<!-- ----------------INCLUDE----------------- 
 </div>
-->


<?


	if ($this->data->order->enabled()){
		if (!emptY($cart['prods'])){
	?>
		<div id="checkout_btn2">
			<a href="index.php?page=order">המשך לקופה&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;	<i class="fa fa-shopping-cart fa-flip-horizontal"></i></a>
		</div>
	<?
		}
	}
?>
