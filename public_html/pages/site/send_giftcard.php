<?
$gift_cards = "";
$gift_cards = $this->data->product->get_by_column('barcode','GIFT CARD');
// error_log(print_r($gift_cards,1));
?>
<div id="send_giftcard">
<div id="send-giftcard-form">
<div class="ttitle">GIFT CARD</div>
    <form id="send_giftcard" action="index.php?page=cart" method="post">
		<div>
			<label>שווי כרטיס המתנה</label>
			<select id="giftcart-amount" name="prod_id">
				<?
				if(!empty($gift_cards)){
					foreach($gift_cards as $gcard){
					?>
					<option value="<?=$gcard->id?>"> <?=(int)$gcard->price?> ש"ח</option>
					<?
					}
				}
				?>
				<!--
				<option value="40"> 40 ש"ח</option>
				<option value="50"> 50 ש"ח</option>
				<option value="100"> 100 ש"ח</option>
				-->
			</select>
		</div>
		<div>
			<label>שם המזמינה</label>
			<input type="text" name="gift_orderer" />
		</div>
		<div>
			<label>שם מקבלת המתנה</label>
			<input type="text" name="gift_receiver" />
		</div>
		<div>
			<label>כתובת מייל של מקבלת המתנה</label>
			<input type="text" name="gift_receiver_email" />
		</div>
		<div>
			<label>הקדשה אישית</label>
			<textarea name="gift_message"></textarea>
		</div>
        <input type="hidden" name="action" value="add_to_cart" />

        <div class="order_btn giftcard"><button type="submit" ><span>הוסף לסל</span></button></div>
    </form>
</div>
<div id="send_giftcard_img">
<img src="pics/gift.jpg" alt="" />
</div>
</div>
<!-- Taken From AdikaStyle
<div class="product-shop left-column">
                
                <div class="product-name">
                    <h1>Gift Card</h1>
                </div>
    <div class="giftcard-info">
        <fieldset id="giftcard-fieldset" class="giftcard-fieldset">
                            <br>
                <label for="card-amount" class="required">שווי כרטיס המתנה</label><br>
                <select id="card-amount" name="card_amount" class="validate-select required-entry" onchange="updatePriceBox()">
                    <option></option>
                                            <option value="50">₪50&nbsp;</option>
                                            <option value="75">₪75&nbsp;</option>
                                            <option value="100">₪100&nbsp;</option>
                                            <option value="150">₪150&nbsp;</option>
                                            <option value="200">₪200&nbsp;</option>
                                            <option value="250">₪250&nbsp;</option>
                                            <option value="300">₪300&nbsp;</option>
                                            <option value="400">₪400&nbsp;</option>
                                            <option value="500">₪500&nbsp;</option>
                                            <option value="750">₪750&nbsp;</option>
                                            <option value="1000">₪1,000&nbsp;</option>
                                    </select>
                        <div class="field">
                <label for="mail-from" class="required">שם המזמינה</label><br>
                <input type="text" id="mail-from" name="mail_from" class="input-text required-entry">
            </div>
            <div class="field">
                <label for="mail-to" class="required">שם מקבלת המתנה</label><br>
                <input type="text" id="mail-to" name="mail_to" class="input-text required-entry">
            </div>
                            <div class="field">
                    <label for="mail-to-email" class="required">כתובת מייל של מקבלת המתנה</label><br>
                    <input type="text" id="mail-to-email" name="mail_to_email" class="input-text required-entry validate-email">
                </div>
                        <div class="field">
                <label for="mail-message">הקדשה אישית</label><br>
                <textarea id="mail-message" name="mail_message"></textarea>
            </div>

            
                <div class="field">

                    <label for="mail_delivery_date" class="">תאריך שליחת המתנה (לבחירת תאריך יש ללחוץ על אייקון לוח השנה)</label><br>
                    <img src="http://www.adikastyle.com/skin/frontend/base/default/images/grid-cal.gif" alt="" class="v-middle" id="mail_delivery_date_button" title="תאריך שליחת המתנה (לבחירת תאריך יש ללחוץ על אייקון לוח השנה)"><br>
                    <input type="text" id="mail_delivery_date" name="mail_delivery_date" readonly="readonly" value="" title="תאריך שליחת המתנה (לבחירת תאריך יש ללחוץ על אייקון לוח השנה)" class="validate-gdate input-text">
                </div>

    </div>
                                    
                
            </div>
-->