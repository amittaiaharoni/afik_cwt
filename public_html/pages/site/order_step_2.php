<?
if (site_config::get_value("use_tranzila") || site_config::get_value("use_paypal"))
	$action_url = "index.php?page=order_step_3";
else
	$action_url = "index.php?page=order_end";
$cart = cart::get_cart();
?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("input[name='shipping_price']").val(<?=site_config::get_value('shipping_price')?>);
			$("input[name=self_pickup]").first().attr('checked',true);
			$("input[name='payment_type']").first().attr('checked',true);		
/* 			$("#city").val($("#delivery_places").find("option:selected").text());
			$("#delivery_places").on("change", function(){
				update_prices();
				$("#city").val($(this).find("option:selected").text());
			}); */
			$("button[name='submit_order']").click(function(e){
				$("input[value='use_credits']").remove();
				// $("input[value='delete_from_cart']").remove();
				// $("input[value='prod_attachment']").remove();
			});
			
			$("#self_pickup").on("change", function(){
				$('#self_pickup_div').toggle();
				$('.deliveryInfoRow').toggle();
				// if ($('#self_pickup:checked').length){
				if ($(this).is(":checked")){
					update_prices();
					$('.togReq').removeAttr('required');
					$("#pickup_date").prop('required',true);
				}
				else{
					update_prices();
					$('.togReq').prop('required',true);
					$("#pickup_date").removeAttr('required');
				}
			});
			$($('input[name="branch"]')[0]).attr('checked', true);
      $('input[name="medical_info"]').change(function(){
          $("#medical_info").slideToggle($(this).checked);
      });
      $('input[name="use_credits"]').change(function(){
          $("#credits-form").slideToggle(/* $(this).checked */);
      });
			$("#coupon_btn").on("click", function(){
				console.log("click");
				var coupon_code = $("#coupon_code").val();
				console.log(coupon_code);
				if (coupon_code){
				console.log("in");
					$.ajax({
						method: "POST",
						url: "index.php",
						data: { coupon_code: coupon_code },
						async: false
					})
					.done(function( msg ) {
						update_prices();
						window.location = window.location;
					});
				}
			});
			
			$(document).on("click", "#credits_btn" , function(){
				console.log("click");
				var credit_amount = $("#credits_amount").val();
				console.log(credit_amount);
				if (credit_amount){
				console.log("in");
					$.ajax({
						type: "POST",
						url: "index.php",
						data: { 
							action: 'use_credits',
							credit_amount: credit_amount
						}
					})
					.done(function( msg ) {
						// window.location = window.location;
						console.log(msg);
						$("#credits-amount").load(location.href + " #credits-amount > *");
						// $("#cart_table").load(location.href + " #cart_table > *");
						
						update_prices();
						$("#credits_message").html(msg);
					});
				}
			});
		});
		
		function PaymentSwith(value){
			if (value == 1){
				$('.creditCardRow').hide();
				$('.togReq2').removeAttr('required');
			}
			else {
				$('.creditCardRow').show();
				$('.togReq2').prop('required',true);
			}
		}
	</script>
<div>
<?php //if (!empty($show_side_menu)){ ?>
<div class="order_left_side in_cart2">
<?php //} ?>
  <div class="order_details">
   <div  class="form_side2">
   <div class="order_step2">
   <form method="post" action="<?=$action_url?>" id="fuck">
   <!----------------------- פרטי מקבל ההזמנה -------------------------------->
    <div id="order_user_details">
            <div class="order_title"><span>1</span>פרטי מקבל ההזמנה</div>
       <div class="form-group">
          <label> שם  <font class='form-required'>*</font>    </label>
           <input type="text" required name="name" id="name" class="togReq" value="<?=$this->data->order->get_order_detail("name")?>">
       </div>
       <div class="form-group">
           <label>עיר<font class='form-required'>*</font>   </label>
           <input type="text" required name="city" id="city" class="togReq" value="<?=$this->data->order->get_order_detail("city")?>">
       </div>
       <div class="form-group">
          <label>כתובת<font class='form-required'>*</font>  </label>
           <input type="text" required name="street" id="street" class="togReq" value="<?=$this->data->order->get_order_detail("street")?>">
       </div>
            <div class="form-group">
                <label>נייד <font class='form-required'>*</font>  </label>
                <input type="text" required name="mobile" id="mobile" class="togReq" value="<?=$this->data->order->get_order_detail("mobile")?>">
            </div>
            <div class="form-group">
               <label> טלפון </label>
                <input type="text" name="phone" id="phone" class="input" value="<?=$this->data->order->get_order_detail("phone")?>">
            </div>
			<div class="form-group">
               <label> מייל </label>
                <input type="email" name="email" id="email" class="input" value="<?=$this->data->order->get_order_detail("email")?>">
            </div>
            <div class="form-group">
                <label for="notes">הערות או בקשות מיוחדות</label>
                <textarea name="notes" id="notes" ><?=$this->data->order->get_order_detail("notes")?></textarea>
            </div>
	   <?
        if (!empty($cart["additional_info_needed"])){
    ?>
    <label><input style="float:right" type="checkbox" name="medical_info">בחר למילוי פרטים רפואיים להתאמת המשקפיים</label>
      <div id="medical_info" style="display:none">
            <div style="clear:both"></div>
            <div class="order_title">
               פרטים רפואים:
            </div>
            <div class="letter_doctor">R :</div>
            <div class="form-group doctor">
                <label for="rcph">CPH (מספר)</label>
                <input type="text" name="rcph" id="rcph" class="input"  >
            </div>
            <div class="form-group doctor">
                <label for="rcyl">CYL (צילינדר)</label>
                <input type="text" name="rcyl" id="rcyl" class="input" >
            </div>
            <div class="form-group doctor">
                <label for="raxe">AXE (מעלות)</label>
                <input type="text" name="raxe" id="raxe" class="input" >
            </div>
            <div style="clear:both;"></div>
            <div class="letter_doctor">L :</div>
            <div class="form-group doctor">
                <label for="lcph">CPH (מספר)</label>
                <input type="text" name="lcph" id="lcph" class="input" >
            </div>
            <div class="form-group doctor">
                <label for="lcyl">CYL (צילינדר)</label>
                <input type="text" name="lcyl" id="lcyl" class="input" >
            </div>
            <div class="form-group doctor">
                <label for="laxe">AXE (מעלות)</label>
                <input type="text" name="laxe" id="laxe" class="input" >
            </div>
            <div style="clear:both;"></div>
            <div class="form-group doctor pd" >
                <label for="pd">PD</label>
                <input type="text" name="pd" id="pd" class="input" >
            </div>
      <div style="clear:both;"></div>
      </div>
        <?
        }
    ?>
	</div>
		<?
		$payment_options = array();
		if (site_config::get_value("online_payment")){
			if (site_config::get_value("use_tranzila") && !site_config::get_value("use_paypal")){
				$payment_options["tranzila"] = true;
			}
			else if (!site_config::get_value("use_tranzila") && site_config::get_value("use_paypal")){
				$payment_options = array("credit_card" => true, "paypal" => true);
			}
			else if (site_config::get_value("use_tranzila") && site_config::get_value("use_paypal")){
				$payment_options = array("tranzila" => true, "paypal" => true);
			}
			else{
				$payment_options['credit_card'] = true;
			}
		}
		else{
			$payment_options['credit_card'] = true;
		}
		if (count($payment_options) > 1){
		?>
			<div class="pay_type">
				<div class="order_title"><span>2</span>בחר שיטת תשלום</div>
				<?
				if (!empty($payment_options['tranzila'])){
				?>
					<div class="icons_card">
						<label for="payment_type<?=paymentType::tranzila?>">
						   שלם בצורה מאובטחת בכרטיס אשראי
						</label>
            <!--<img src="https://www.zionorphanage.com/img/tranzila.png" width="100" alt="Tranzila" />-->
						<input type="radio" name="payment_type" id="payment_type<?=paymentType::tranzila?>" value="<?=paymentType::tranzila?>" required />
					</div>
				<?
				}
				if (!empty($payment_options['paypal'])){
				?>
					<div class="icons_card">
						<img src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/PP_logo_h_100x26.png" alt="PayPal" />
						<label for="payment_type<?=paymentType::paypal?>">
						   שלם עם 
						</label>
            
						<input type="radio" name="payment_type" id="payment_type<?=paymentType::paypal?>" value="<?=paymentType::paypal?>" required />
					</div>
				<?
				}
				if (!empty($payment_options['credit_card'])){
				?>
					<div class="icons_card">
						<label for="payment_type<?=paymentType::credit_card?>">
						    שלם עם כרטיס השראי
						</label>
						<input type="radio" name="payment_type" id="payment_type<?=paymentType::credit_card?>" value="<?=paymentType::credit_card?>"/>
					</div>
				<?
				}
				?>
		<?
		}
		else{
			$payment_type = 0;
			if (!empty($payment_options['tranzila'])){
				$payment_type = paymentType::tranzila;
			?>
			<div class="order_title">
				<span>2</span> תשלום יבוצע עם טרנזילה
			</div>
			<?
			}else if (!empty($payment_options['paypal'])){
			$payment_type = paymentType::paypal;
			?>
			<div class="order_title">
				<span>2</span> התשלום יבוצע עם PAYPAL
			</div>
			<?
			}else if (!empty($payment_options['credit_card'])){
				$payment_type = paymentType::credit_card;
			?>
			<div class="order_title">
				<span>2</span>התשלום יבוצע עם כרטיס אשראי
			</div>
			<input type="hidden" name="payment_type" value="<?=$payment_type?>"/>
			<?
			}
		}
		if (!empty($payment_options['credit_card'])){
		?>
		<div class="login_table3">
		   <div class="order_title">  פרטי תשלום  </div>
			<table  border="0" width="100%" cellspacing="4">
				<tr>
					<td valign="top"align='right' width="200">סוג תשלום:</td>
					<td align='right' colspan="2">
						<input onClick="PaymentSwith(this.value)" name="payment_type" type="radio" value="<?=paymentType::call_me?>">
						ברצוני שנציג מטעמכם יצור אתי קשר <br>
						<input onClick="PaymentSwith(this.value)" name="payment_type" type="radio" checked value="<?=paymentType::credit_card?>">
						תשלום עם כרטיס השראי
					</td>
				</tr>
				<tr class="creditCardRow">
					<td align="right"><font>סוג כרטיס<font class='form-required'>*</font></font></td>
					<td align="right">
						<select name="card_type" required width="150" style="width: 200" class="togReq2">
							<option value="" selected>סוג כרטיס</option>
							<option value="Visa" >ויזה</option>
							<option value="AmericanExpress" > אמריקן אקספרס</option>
							<option value="Diners" >דיינרס</option>
							<?/*<option value="MasterCard" >מאסטרכרט</option>*/?>
							<option value="Other" >אחר</option>
						</select>
					</td>
				</tr>
				<tr class="creditCardRow">
					<td align="right">
						<font>מספר כרטיס <font class='form-required'>*</font></font>
					</td>
					<td align="right">
						<input required name="card_number" autocomplete="off" size="24" maxlength="19" dir="ltr" value="" class="togReq2">
					</td>
				</tr>
				<tr class="creditCardRow">
					<td align="right"><font>תוקף:&nbsp;<font class='form-required'>*</font></font></td>
					<td align="right">
						<select dir="ltr" required name="expire_year" class="togReq2">
							<option value="" selected>שנה</option>
							<?
							for($i=0;$i<10;$i++){
							$year = $i + date("y");
							?>
							<option value="<?=$year?>"><?=$year?></option>
							<?
							}
							?>
						</select> /
						<select dir="ltr" required name="expire_month" class="togReq2">
							<option value="" selected>חודש</option>
							<?
							for($i=1;$i<=12;$i++){
							?>
							<option value="<?=$i?>"><?=$i?></option>
							<?
							}
							?>
						</select>
					</td>
				</tr>
			 <!--	<tr class="creditCardRow">
					<td align="right">
						<font>מספר ת.ז.&nbsp;<font class='form-required'>*</font></font>
					</td>
					<td align="right">
						<input required name="owner_tz" autocomplete="off" size="24" maxlength="19" dir="ltr" value="" class="togReq2">
					</td>
				</tr>-->
				<tr class="creditCardRow">
					<td align="right">
						<font>שם של בעל הכרטיס&nbsp;<font class='form-required'>*</font></font>
					</td>
					<td align="right">
						<input required name="owner_name" autocomplete="off" size="24" maxlength="19" dir="ltr" value="" class="togReq2">
					</td>
				</tr>
				<tr class="creditCardRow">
					<td align="right">
						<font>3 ספרות בטחון(CVV):&nbsp;<font class='form-required'>*</font></font>
					</td>
					<td align="right">
                    	<div style="position: relative;">
                    		<input required name="cvv"  autocomplete="off" size="3" dir="ltr" value="" class="togReq2">
								<img src="pics/cvv.gif" style="vertical-align: bottom"/>
						</div>
					</td>
				</tr>
				<tr class="creditCardRow">
					<td align="right">תשלומים</td>
					<td align="right">
						<select name="num_of_payments" width="150" style="width: 150px">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
            <input type="checkbox" name="rules"><a target="_blank" href="#" class="side_menu" style="text-decoration:underline; display: inline;">קראתי את תנאי שימוש באתר ומסכים לתנאים</a>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right">
						<font class='form-required'>*</font> שדה חובה.
					</td>
				</tr>
			</table>
		</div>
		<?
		}
		?>
<div id="order_delivery_type">
<?
$shipping_enabled = site_config::get_default_value("shipping_enabled");
$self_pickup_enabled = site_config::get_default_value("self_pickup_enabled");
if ($shipping_enabled && $self_pickup_enabled){
?>
	<div class="order_title">
		<span>3</span>בחר/י שיטת משלוח:
	</div>
	<!--div class="delivery_holder">
		<?
		$delivery_types = $this->data->deliveryType->get_all();
		if(!empty($delivery_types)){
			foreach ($delivery_types as $delivery_type){
				?>
				<label for="self_pickup_btnX" style="directon:rlt;">
					<?=$delivery_type->name?>
					<?
					if ( $delivery_type->id === 1 ){
					?>
						<input type="radio" name="self_pickup" id="self_pickup_btn" value="<?$delivery_type->id?>" required/>
					<?}
					else{?>
						<input type="radio" name="self_pickup" id="opt_<?=$delivery_type->id?>" value="<?=$delivery_type->id?>"/>
					<?
					}
					?>			
				</label>
				<?
			}
		unset($delivery_types);
		} ?>
	</div-->
<script>
$(function(){
	$(".delivery_type_block").hide().first().show();
	update_prices();
	$("input[name=self_pickup]").on("change", function(){
		var id = $(this).val();
		$(".delivery_type_block").hide();
		$("#dtb"+id).show();
		update_prices();
	});
});
var dt = 1;
$(".delivery_holder input[type='radio']").click(function(){
	dt = $(this).data('dt_id');
});
function update_prices(){
	var shipping_price = 0;
	var shipping_added_price = 0;
	var new_price = 0;
	
	var credits = $("#credits_amount").val();
	var that = '';
	$(".delivery_type_block").each(function(i){
		console.log($(this).attr('id') + " -> "+$(this).css('display')+ " -> "+$(this).is(":visible"));
		if($(this).is(":visible")){
			that = this;
			dt = $(this).data('dt_id');
			shipping_price = $(this).find("#shippingPrice").text();
			if($(this).find("input[name='shipping_price_extra']").length)
				new_price = $(this).find("input[name='shipping_price_extra']").val();
			$(this).find("input[name='shipping_price']").val(<?=site_config::get_value('shipping_price')?>);
			$(this).find("input[name='shipping_address']").attr('required',true);
		}
		else{
			$(this).find("input[name='shipping_price']").val('');
			$(this).find("input[name='shipping_address']").attr('required',false);
			$(this).find("input[name='shipping_address']").val('');
		}
	});
	if(isNaN(shipping_price))
		shipping_price = 0;
	console.log('DT '+dt);
	$.ajax({
		type: 'post',
		data:{
			action: 'update_credits_cart',
			shipping_price: shipping_price,
			dt_id: dt
		},
		async: false,
		success: function(ret){
			console.log('I return ' + ret);
			shipping_added_price = ret;
			if(ret > -1){
				$("#cart_table").load(location.href + " #cart_table > *");
			}
		}
	});
	if(shipping_price > 0){
		total_price = <?=$cart['total_price_after_discount']?>;/* price_to_pay */
		if(credits > 0){
			total_price -= credits;
		}
		price_and_shipping = parseFloat(total_price) + parseFloat(shipping_added_price);
		$(that).find('#price_plus_shipping').html(price_and_shipping  + '&nbsp;' + "&#8362;");
		$(that).find('#shippingPrice').html(shipping_added_price);
		$(that).find('.nis').html('&nbsp;' + "&#8362;");
	}
}
</script>
	<div class="delivery_holder">
		<?
		$delivery_types = $this->data->deliveryType->get_all();
		if(!empty($delivery_types)){
			foreach ($delivery_types as $delivery_type){
				?>
				<label for="dt<?=$delivery_type->id?>">
					<input type="radio" name="self_pickup" id="dt<?=$delivery_type->id?>" value="<?=$delivery_type->id?>" data-dt_id="<?=$delivery_type->id?>" required/>
					<?=$delivery_type->name?>
					<span>
						<?
						if($cart['total_price_after_discount'] >= $delivery_type->from_order_amount)
							echo $delivery_type->new_delivery_from;
						else
							echo $delivery_type->price;
						?>
					</span>
					<span class="nis">&#8362;</span>
				</label>
				<?
			}
		
		 ?>
	</div>
	<?
	foreach ($delivery_types as $delivery_type){
		if($delivery_type->id == 1){
			?>
			<div id="dtb<?=$delivery_type->id?>" class="login_table self_pickup_block delivery_type_block" style="display:none" data-dt_id="0">
				<h5>
					איסוף עצמי
				</h5>
				<div class="delivery_row">
					מקום איסוף
					<select id="pickup_location" name="pickup_location">
						<?
						$branches = $this->data->branch->get_all();
						if(!empty($branches)){
						foreach ($branches as $branch){
						?>
							<option value="<?=$branch->id?>"><?=$branch->name?></option>
						<?
						}
						}
						?>
					</select>
				</div>
				<div class="delivery_row">
					תאריך איסוף
					<input type="text" id="pickup_date" name="pickup_date" class="datepicker"/>
				</div>
			</div>
			<br />
			<?
		}
		else{
	?>
		<div id="dtb<?=$delivery_type->id?>" class="login_table shipping_block delivery_type_block" data-dt_id="<?=$delivery_type->id?>">
			<div>
				<div class="delivery_row" style="display:none;">
					מחיר משלוח:<span id="shippingPrice"/><?=$delivery_type->price?></span><span class="nis"></span>
				</div>
				
				<?/* 
				if($delivery_type->id != 2 || $delivery_type->name != 'שליח עד הבית'){
				?>
				<label>כתובת למשלוח</label>
				<input type="text" name="shipping_address" required/>
				<?
				} */
				?>
				<input type="hidden" name="shipping_price" />
				<input type="hidden" name="shipping_price_extra" value="<?=$delivery_type->new_delivery_from?>"/>
				<!--
				<div class="delivery_row">
					סה"כ לתשלום כולל דמי משלוח: &nbsp;
					<span id="price_plus_shipping"></span>
				</div>
				-->
			</div>
		</div>
		<br />
	<?
		}
	}
		unset($delivery_types);
		}
}
if ($shipping_enabled){
}
?>
</div>
			</div>
            <div class="clear"></div>
          <div id="order_credits" class="cf">
<button id="submit_order" name="submit_order" class="btn btn-default" style="display:none;">המשך לתשלום&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i></button>
			
   </div>
	</form>
			 <div id="cart_table2" class="step2">
       	 <div class="order_title">
           <span>4</span>ההזמנה שלך
        </div>
        {__cart_table__}
		<div id="credits_amount2">
        <div id="credits-amount" >
        <div class="order_title">
        יתרת קרדיט
        </div>
        <div class="credit_rest"> יתרת קרדט שלך: <?php echo $credits = (!empty($this->data->user->loged_in_user))?$this->data->user->loged_in_user->get_credits():'0'; ?> &#8362;</div>
		<br/>
		<div class="credit_rest">מינימום קרדיטים לניצול :&nbsp; <span><?=site_config::get_value('percent_of_deal_credits'); ?></span><span class="shekel">&#8362;</span>
		</div>
        <?php //if (!empty($credits) && site_config::get_value('percent_of_deal_credits') <= $credits){ ?>
     <div class="clear">
			<!--<label>
                תרצה לנצל קרדיטים?
            </label>
            <input type="checkbox" name="use_credits" id="use_credits"/></div>-->
            <div id="credits-form" style="display:block">
                <form action="" method="post">
                    <input type="hidden" name="action" value="use_credits"/>
                    <input type="text" name="credits_amount" id="credits_amount" placeholder="<?=$credits?>" value="" />
                    <button type="button" id="credits_btn" class="btn-default">
                        נצל קרטידים
                    </button>
                </form>
            </div>
        <?php //} ?>
        </div>
		  <div id="credits_message"></div>
</div>
        <? $coupon = $this->data->order->get_coupon();
				if (empty($coupon) && !isset($_SESSION['coupon_id'])){
				?>
				<div id="coupon">
					<input type="hidden" name="action" value="apply_coupon"/>
					<div class="order_title">
						יש ברשותך קופון?
					</div>
					<?
						$coupon_code = "";
						if (!empty($data->order->coupon))
							$coupon_code = $data->order->coupon->number;
					?>
					<input type="text" name="coupon_code" id="coupon_code" value="<?=$coupon_code?>" />
					<button type="button" id="coupon_btn" class="btn-default">
						פדה קופון
					</button>
					<div style="clear:both;"></div>
				</div>
				<?
				}
				?>
</div>
	   </div>
          <div class="clear"></div>
	      <div id="checkout_btn3">
	   <script>
		$(function(){
			/* $("button[name='submit_order']").click(function(e){
				// $("#fuck").submit();
			}); */
		});
	   </script>
	   <!--<button name="submit_order" class="btn btn-default">המשך לתשלום&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i></button>-->
	   <label for="submit_order" class="btn btn-default">המשך לתשלום&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i></label>
		</div>
	  </div>
   </div>
   </div>
<?php //if (!empty($show_side_menu)){ ?>
</div>
<?php //} ?>
<?php //if (!empty($show_side_menu)){ ?>
<div id="order_side">
<!-- ------------------INCLUDE --------------->
        {__order_side__}
<!-- ----------------INCLUDE----------------- -->
</div>
<?php //}?>
</div>