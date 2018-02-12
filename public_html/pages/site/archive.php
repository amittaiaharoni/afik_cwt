<?
$top_banner = "pics/100.jpg";
$cart = array();
$date = array();
$ocart = array();
//$orders = $this->data->order->get_by_column("user_id",$this->data->user->loged_in_user->id);
$orders = $this->data->user->loged_in_user->get_orders();
$credits = $this->data->user->loged_in_user->get_credits();
error_log("credits ".print_r($credits,1));
// error_log("obj ".print_r($orders,1));
if(!empty($orders)){
	foreach($orders as $order){
		$ocart[] = $order->serialized_cart;
		//$cart[] = unserialize(base64_decode($order->serialized_cart));
		$cart[] = unserialize_from_db($order->serialized_cart);
		$date[] = $order->created_date;
		$ofile[] = $order->attch_file_invoice;
	}
}
// error_log(print_r($cart,1));
?>
<script>
$(function(){
	$(".link_to_cart").on("click", function(e){
		e.preventDefault();
		cart_code = $(this).prev(".preshow_cart").data("cart");
		$.ajax({
			type: "post",
			data:{
				action: "cart_fill",
				cart: cart_code
			},
			success: function(ret){
				$("#top_basket").load(location.href+"  #top_basket>*");
			}
		});
	});
	$(".preshow_cart").on("click", function(e){
		e.preventDefault();
		var that = this;
		$(".preshow_cart").each(function(){
			if(this == that){return true;}
			$(this).removeClass("clicked");
		});
		$(".cart_preshow").slideUp(400);

		if($(this).hasClass("clicked")){
			$(this).toggleClass('clicked');
		}
		else{
			$(this).toggleClass('clicked');
			$(this).parent(".archive_row").next(".cart_preshow").slideDown(400);
		}
	});
});
</script>

<div id="contain_side">
<div class="top_pic">
	<?
	if (!empty($top_banner)){ ?>
		<img src="<?=$top_banner?>">
	<?}?>
</div>
<div  id="info_page_holder">
<div class="title">
<h1>ארכיון הזמנות</h1><span><img src="pics/archive.png" alt="" /></span>
</div>
<div id="archive_table">
<?
if(!empty($orders)){
	$i = 0;
	foreach($date as $d){
		?>
            <?php //if (!empty($ofile[$i])): ?>
            <div class="invoice-btn">
                <a href="<?=(!empty($ofile[$i]))?site_config::get_value("site_base_url").site_config::get_value("upload_files_folder").$ofile[$i]:"#"?>" class="link-to-invoice"  target="_blank" >
                <?php /*<a target="_blank" href="<?=site_config::get_value("site_url").site_config::get_value("upload_files_folder").$ofile[$i]?>" class="link_to_cart"  >*/?>
                    <span>חשבונית</span>
                </a>
            </div>
            <?php //endif; ?>
		<div class="archive_row">
			<a href="#" class="preshow_cart" data-cart='<?=$ocart[$i]?>' >
					<span><?=date("d/m/Y", strtotime($d))?></span>
			</a>
			<button class="link_to_cart">הוסף לסל</button>
		</div>
		<div class="cart_preshow" style="display:none;">
            <div class="cart_row">
                <div class="cart_row2">
                 <div class="cart_img" style="padding-top:70px;">
                     <h2>תמונה</h2>
                 </div>
                 <div class="cart_name">
                     <h2>שם</h2>
                 </div>
                <div class="cart_price">
                     <h2>מחיר</h2>
                </div>
                <div class="cart_quan">
                     <h2>כמות</h2>
                </div>
                </div>
            </div>
			<?
			$not_exists = false;
			foreach($cart[$i]['prods'] as $entry_id => $cart_entry){
				$prod_id = $cart_entry['prod_id'];
				$prod = $this->data->product->get_by_id($prod_id);
				if(!empty($prod)){
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
									<?=( $det_data['name'] . ( !empty($det_data['code']) ? '('.$det_data['code'].')' : "") )?>
									<?=( !empty($det_data['price'])?$det_data['price']:"")?>
								</div>
								<?
								}
								?>
							</div>
							<?
							}
							?>
						</div>
						<div class="cart_price"><span>&#8362;</span><?=$cart_entry['prod_price']?> </div>
						<div class="cart_quan"><?=$cart_entry['quantity']?></div>
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
									<input type="hidden" name="action" value="prod_attachment"/>
									<?
									if (!empty($cart_entry['attachments']) && !empty($cart_entry['attachments'][$attachment->id])){
									?>
										<div>
											<img src="<?=site_config::get_value('upload_user_files_folder').$cart_entry['attachments'][$attachment->id]?>" style="max-height:50px;"/>
										</div>
									<?
									}
									?>
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
				</div>
			<?

				}
				else{
					$not_exists = true;
				}
				unset($prod);
            }?>
            <div class="total"><span>&#8362;</span><span>סה"כ כולל מע"מ <?=round($cart[$i]['price_to_pay'],2,PHP_ROUND_HALF_DOWN	)?> </span></div>
            <?
			if($not_exists){
				?>
				<div>
					מוצר/ים לא קיימים
				</div>
				<?
			}
			?>
		</div>
		<?
		$i++;
	}
}
?>
</div>
</div>
</div>
