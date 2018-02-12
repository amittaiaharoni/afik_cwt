<?
	if (!emptY($_GET['prod_id'])){
		$prod = $this->data->product->get_by_id($_GET['prod_id']);
		
		if (!empty($prod)){
?>
<script>
	function calculate_price(){
		var price = <?=$prod->price?>;
		var quantity = parseFloat($("#quantity").val());
		
		$(".option_det:checked").each(function(){
			price += parseFloat($(this).data("price"));
		});
		
		price *= quantity;
		$("#total_price").text(price);		
		
		var prepay = parseFloat($("#prepay").data("prepay_price"));
		$("#prepay").text( prepay * quantity );
	}

	$(document).ready(function(){
		$(".option_det").on("change", function(){
			calculate_price();
		});

		$(".quantity").on("change", function(){
			calculate_price();
		});
		
		$(".fancybox").fancybox();
	});
	
	$(document).ready(function(){
		
		$(".additional_img_link").on("click", function(e){
			e.preventDefault();
			var small_img = $(this).data("small_image");	
			var big_img = $(this).data("big_image");	
			
			// $(".MagicZoom a").attr("href", big_img);
			$(".main_pic_small").attr("src", small_img);
			$(".main_pic_small").data("zoom-image", big_img);
			
			$(".zoomContainer").remove(); // prevent problem with diferent img sizes
		});
		
		$(".big_item_color input").on("change", function(){
			$(".big_item_color label").removeClass("active");
			
			$(".big_item_color input:checked").each(function(){
				$($(this).siblings("label")[0]).addClass("active");
			});			
		});
	});
</script>
<div id="contain_side">

	<div id="big_item_pic">
		<div class="main_pic" style="position: relative;">
			<img class="main_pic_small" src="<?=site_config::get_value('upload_images_folder').$prod->image?>" data-zoom-image="<?=site_config::get_value('upload_images_folder').$prod->image?>"/>
		</div>
		<div class="gallery_hold">
		<?
			$images = $prod->get_gallery();
			if (!empty($images)){
				foreach ($images as $image){
			?>
			<div class="gallery_img">
				<a href="<?=site_config::get_value('upload_images_folder').$image->image?>" class="fancybox" rel="gal">
					<img src="<?=site_config::get_value('upload_images_folder').$image->image?>"/>
				</a>
			</div>
			<?
				}
			}
		?>
		</div>
		<?
			$color_option_id = 171;
			$option = $this->data->option->get_by_id($color_option_id);
			if (!empty($option)){
				$details = $option->get_details_for_product($prod->id);
				if (!empty($details)){
					foreach ($details as $detail_data){
						for ($i = 1; $i < 5; $i++){
							if (!empty($detail_data->{"image".$i})){
								?>
								<a href="#" class="additional_img_link" data-big_image="<?=site_config::get_value('upload_images_folder').$detail_data->{"image".$i}?>"  data-small_image="<?=site_config::get_value('upload_images_folder').$detail_data->{"image".$i}?>">
									<img style="max-width:100px; max-height:100px;" src="<?=site_config::get_value('upload_images_folder').$detail_data->{"image".$i}?>"/>
								</a>
								<?
							}						
						}
						break;
					}
				}
			}
		?>
	</div>
	<div id="description">
		<form action="index.php?page=cart" method="post">
			<input type="hidden" name="action" value="add_to_cart"/>
			<input type="hidden" name="prod_id" value="<?=$prod->id?>"/>
            <div class="prod_title"> <h1><?=$prod->name?></h1></div>		
			<div class="line"></div>			
			<?
				$options = $prod->get_options();
				if (!empty($options)){
					foreach ($options as $option){
						$details = $option->get_details_for_product($prod->id);
						
						if (!empty($details)){
							?>
							<div class="big_item_color">
								<h3><?=$option->name?></h3>
							<?
							foreach ($details as $detail_data){
								if (!empty($detail_data->detail)){									
							?>
								<span>												
									<?
									if ($option->is_multiple){
										?>
										<input type="checkbox" name="prod_det[<?=$detail_data->id?>]" id="prod_det_<?=$detail_data->id?>" class="option_det" data-price="<?=$detail_data->price?>" value="<?=$detail_data->id?>" data-image1="<?=$detail_data->image1?>" data-image2="<?=$detail_data->image2?>" data-image3="<?=$detail_data->image3?>" data-image4="<?=$detail_data->image4?>"/>
										<?
									}
									else{
										?>
										<input type="radio" name="prod_det[<?=$option->id?>]" id="prod_det_<?=$detail_data->id?>" class="option_det" data-price="<?=$detail_data->price?>" value="<?=$detail_data->id?>" data-image1="<?=$detail_data->image1?>" data-image2="<?=$detail_data->image2?>" data-image3="<?=$detail_data->image3?>" data-image4="<?=$detail_data->image4?>"/>
										<?
									}
									
									$image = "";
									if (!empty($detail_data->image1)){
										$image = $detail_data->image1;
									}
									else 
									if (!empty($detail_data->detail->image)){
										$image = $detail_data->detail->image . ".jpg";
									}
									
									if (!emptY($image)){
									?>
									<label for="prod_det_<?=$detail_data->id?>">
										<img src="<?=site_config::get_value('upload_images_folder').$image?>" style="width:100%;"/>
									</label>
									<?
									}
									else{
									?>
									<label for="prod_det_<?=$detail_data->id?>">
										<?=$detail_data->detail->name?>
									</label>
									<?
									}
									
									
									if ($detail_data->price > 0){
									?>
									<br/>
									<span>
										<b>
										<?=$detail_data->price?>
										<span>&#8362;</span>
										</b>
									</span>
									<?
									}
									
									?>
								</span>
							<?
								}
							}
							?>
							</div>
							<div class="line"></div>
							<?
						}
					}
				}
			?>
						
			<div class="big_item_price" style="float: right;">				
				<span>&#8362;</span><span id="total_price" style="font-size: inherit;"><?=$prod->price?></span>
			</div>
			<?
			if ($prod->price2 > 0){
			?>
			<div class="big_item_price2" style="float: right;">				
				<span>&#8362;</span><?=$prod->price2?>
			</div>
			
			
			<?
			}
			?>
			<p><?=$prod->desc?></p>			
			
			<div class="prod_comments_block">
				<div>הערות</div>
				<textarea name="comments" style="width: 100%;"></textarea>
			</div>				
			<div class="clear"></div>
			
			<div class="big_item_order">
				<div class="quantity">
					בחר כמות:&nbsp;
					<input type="number" name="quantity" id="quantity" min="1" value="1"/>
				</div>
				<div class="order_btn">
					<button>
					<i class="fa fa-shopping-cart fa-flip-horizontal"></i>&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<span>הוסף לסל</span>
					</button>
				</div>
				<div class="clear"></div>
			</div>
			 <div class="line"></div>
		</form>
	</div>
	<div class="clear"></div>
</div>

 <?
	}
 }
 ?>