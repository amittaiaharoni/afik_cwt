<script>
	$(document).ready(function(){
		$('.prod_attachments_form').ajaxForm({ success: attachments_form_response });

		function attachments_form_response(responseText, statusText, xhr, $form)  {
			if (responseText){
				console.log($form);
				if ($form.find(".img_div img").length){
					$form.find("img").attr("src",responseText);
				}
				else{
					$form.find(".img_div").append('<img src="'+responseText+'" style="max-height:50px;"/>');
				}
			}
		}

		$(".prod_attachment").on("change", function(){
			$(this).parents("form").submit();
		});
	});
</script>
<div id="cart_table">
<?
	// cart::calculate_cart_price();
	$cart = cart::get_cart();
	// error_log(print_r($cart,1));

	if (emptY($cart) || empty($cart["prods"])){
	?>
	<h1>
		אין מוצרים בסל
	</h1>
	<?
	}
	else{
    	?>

     <div class="cart_title">
     <div class="cart_img">מוצר</div>
     <div class="cart_name">שם המוצר</div>
     <div class="cart_price">מחיר</div>
     <div class="cart_quan">כמות</div>
     <div class="cart_comm">הערות</div>
     </div>
	   	<?
		foreach ($cart["prods"] as $entry_id => $cart_entry){
			$comment = '';
			if($this->data->product->get_by_id($cart_entry['prod_id'])->barcode == 'GIFT CARD'){
				$comment = 'ישלח ל-'.$cart_entry['gift_receiver'];
			}
			else{
				$comment = $cart_entry['comments'];
			}
		?>

		<div class="cart_row">
		  <div class="cart_row2">
          	<div class="cart_img"><img src="<?=site_config::get_value('upload_images_folder').$cart_entry['prod_image']?>"/> </div>
			<div class="cart_name">
            <?=$cart_entry['prod_name']?>
            	<?
			if (!emptY($cart_entry['prod_options'])){
			?>
			<div id="cart_options">
				<?
				foreach ($cart_entry['prod_options'] as $det_data_id => $det_data){
				?>
				<div class="cart_opt">
					<?=$det_data['name']?>
					<?=($det_data['price']>0?$det_data['price']." &#8362;":"")?>
				</div>
				<?
				}
				?>
			</div>
			<?
			}
			?>
            </div>
            <div class="cart_price"><span><?=$cart_entry['prod_price']?></span> <span>&#8362;</span></div>
             <div class="cart_quan"><?=$cart_entry['quantity']?></div>
			<div class="cart_comm"> <?=$comment?></div>
          </div>

			<?
				if (site_config::get_value('allow_prod_file_attachments')){
					$prod = $this->data->product->get_by_id($cart_entry['prod_id']);
					if (!empty($prod) && !empty($prod->attachment_files)){
			?>
				<div class="cart_bottom_row">
					<?
						foreach ($prod->attachment_files as $attachment){
					?>
						<form action="index.php?page=cart" method="post" enctype="multipart/form-data" class="prod_attachments_form">
							<?=$attachment->name?>
							<input type="hidden" name="action" value="prod_attachment"/>
							<div class="img_div">
							<?
							if (!empty($cart_entry['attachments']) && !empty($cart_entry['attachments'][$attachment->id])){
							?>
								<img src="<?=site_config::get_value('upload_user_files_folder').$cart_entry['attachments'][$attachment->id]?>" style="max-height:50px;"/>
							<?
							}
							?>
							</div>
							<div>
								<label for="<?=$attachment->id."__".$prod->id?>"><?=$attachment->label?></label>
								<input type="file" name="<?=$attachment->id."__".$prod->id?>" id="<?=$attachment->id."__".$prod->id?>" class="prod_attachment" <?=($attachment->mandatory?"required":"")?>/>
							</div>
						</form>
					<?
						}
					?>
				</div>
			<?
					}
				}
			?>
			<div class="cart_bottom_row">
				<div class="total">סה"כ: <span><?=$cart_entry['total_price']?></span> <span>&#8362;</span></div>
				<?
				if(!empty($cart_entry['sale_per_product_discount'])){
				?>
				<div class="total">הנחה: <span><?=$cart_entry['sale_per_product_discount']?></span> <span>&#8362;</span></div>
				<div class="total">מחיר סופי: <span><?=$cart_entry['total_after_discount']?></span> <span>&#8362;</span></div>
				<?
				}
				?>
				<div class="del">
					<form action="" method="post">
						<input type="hidden" name="action" value="delete_from_cart"/>
						<input type="hidden" name="id" value="<?=$cart_entry['id']?>"/>
						<button class="btn-default delete"><i class="fa fa-close"></i>מחק</button>
					</form>
				</div>
				<div class="clear"></div>
			</div>
           <div class="clear"></div>
		</div>
		<?
		}
		?>
		<div>
			<div class="total">סה"כ: <span><?=$cart["total_price"]?></span> <span>&#8362;</span></div>
		</div>
		<div style="clear:both;"></div>
		<?

		if (!empty($cart["prepay_price"])){
		?>
		<div>
			<div class="total">מקדמה לתשלום: <span><?=$cart["prepay_price"]?></span> <span>&#8362;</span></div>
		</div>
		<?
		}

		if (!empty($cart["discount"])){
		?>
			<div class="clear"></div>
			<div class="total">
				הנחה: <span><?=$cart["discount"]?></span> 
				<span>&#8362;</span>
			</div>
			 <div class="clear"></div>
			<div class="total">
				מחיר לאחר הנחה: <span><?=$cart["total_price_after_discount"]?></span> 
				<span>&#8362;</span>
			</div>
		<?
		}
		if(!empty($cart['shipping_price']) && !empty($_GET) && !empty($_GET['page']) && $_GET['page'] != 'cart'){
			?>
			<div class="clear"></div>
			<div class="total shipping_price">
				משלוח: <span><?=$cart["shipping_price"]?></span> 
				<span>&#8362;</span>
			</div>
			<div class="clear"></div>
			<div class="total shipping_price">
				סה"כ כולל משלוח: <span><?=$cart["price_to_pay"]?></span> 
				<span>&#8362;</span>
			</div>
			<?
		}
		?>
		<div style="clear:both;"></div>
		<?
	}
?>
</div>

