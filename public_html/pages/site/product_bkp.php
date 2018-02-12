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
        $("#to_cart").hide();

        var main_pic = $(".main_pic_small");

		main_pic.elevateZoom({zoomWindowPosition: 6});

		$(".additional_img_link,.gallery_img>a").on("click", function(e){
			e.preventDefault();
			var small_img = $(this).data("small_image");
			var big_img = $(this).data("big_image");

			// $(".MagicZoom a").attr("href", big_img);
            main_pic.fadeOut('fast',function(){
                main_pic.attr("src", small_img);
                main_pic.data("zoom-image", big_img);
                main_pic.fadeIn("fast");

                $(".zoomContainer").remove(); // prevent problem with diferent img sizes
                main_pic.elevateZoom({zoomWindowPosition: 6});
            });

		});

		$(".big_item_color input").on("change", function(){
			$(".big_item_color label").removeClass("active");

			$(".big_item_color input:checked").each(function(){
				$($(this).siblings("label")[0]).addClass("active");
			});
		});

		$("form#prod_form").on("submit",(function(e){
			e.preventDefault();
            $.post("",$(this).serialize()).done(function(){
                $("#top_basket").load(location.href+" #top_basket>*");
                $("#add_to_cart_buy span").text("????? ???? ???");
                $("#add_to_cart_buy").addClass("added_to_cart");
                $("#add_to_cart_buy").prop("disabled",true);
                $("#to_cart").show();
            }).fail(function(err){
                console.log("got error" + err);
            });

			return false;
		}));

		$("#size_table_link").on("click", function(e){
			e.preventDefault();
			$("#sizes_table").slideToggle();
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
            <?php /*
				<a href="<?=site_config::get_value('upload_images_folder').$image->image?>" class="fancybox" rel="gal">
					<img src="<?=site_config::get_value('upload_images_folder').$image->image?>"/>
				</a>
              */ ?>
				<a href="#" data-small_image="<?=site_config::get_value('upload_images_folder').$image->image?>" data-big_image="<?=site_config::get_value('upload_images_folder').$image->image?>" >
					<img src="<?=site_config::get_value('upload_images_folder').$image->image?>"/>
				</a>
			</div>
			<?
				}
			}
		?>
		</div>
		<?
			/*$color_option_id = 171;
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
			}*/
		?>
	</div>
	<div id="description">
		<form id="prod_form" action="index.php?page=cart" method="post">
			<input type="hidden" name="action" value="add_to_cart"/>
			<input type="hidden" name="prod_id" value="<?=$prod->id?>"/>
            <div class="prod_title"> <h1><?=$prod->name?></h1></div>
			<div style="padding:2% 0;">
			  <?/*	<div style="float: right;">
					??"?: <?=$prod->barcode?>
				</div>*/?>
				<div style="float: left;">
					<div class="fb-share-button" data-href="<?=site_config::get_value("site_url")?>index.php?page=product&amp;prod_id=<?=$prod->id?>" data-layout="button"></div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="line"></div>
			<?
				$options = $prod->get_options();
				if (!empty($options)){
					foreach ($options as $option){
						$details = $option->get_details_for_product($prod->id);

                        //check stock for product and option_details for product
                        //if stock is 3 or less show message "???? ????? ?????? ????" and send admin an email
                        //containing:
                        //  1. product name
                        //  2. the link to product
                        //  3. text "???? ????? ???? ? 3"

						/*if (!empty($details)){
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
                        }*/
						if (!empty($details)){
							?>
							<div class="big_item_color">
								<h3>???? ?? ?<?=$option->name?></h3>
                                <select id=big_item_color" name="">
							<?
							foreach ($details as $detail_data){
								if (!empty($detail_data->detail)){
							?>
								<span>
                                    <?

									$image = "";
									if (!empty($detail_data->image1)){
										$image = $detail_data->image1;
									}
									else
									if (!empty($detail_data->detail->image)){
                                        if(strpos($detail_data->detail->image,".jpg") > 0)
										$image = $detail_data->detail->image . ".jpg";
                                        else
										$image = $detail_data->detail->image;
									}

									if (!emptY($image) && $detail_data->link){
									?>
									<label for="prod_det_<?=$detail_data->id?>">
                                        <a href="<?=site_config::get_value("site_url").$detail_data->link?>">
										<img src="<?=site_config::get_value('upload_images_folder').$image?>" style="width:100%;"/>
                                        </a>
									</label>
									<?
									}
									else{
									?>
                                        <option value="">
                                            <label for="prod_det_<?=$detail_data->id?>">
                                                <a href="<?=site_config::get_value("site_url").$detail_data->link?>">
                                                <?=$detail_data->detail->name?>
                                                </a>
                                            </label>
                                        </option>
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
                                </select>
							</div>
							<div class="line"></div>
							<?
                        }
					}
				}
			?>
			<p><?=$prod->desc?></p>
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
			<div class="sizes_table" style="float: left; padding: 4% 0;">
				<a href="#" id="size_table_link">???? ??????&nbsp;&nbsp;<i class="fa fa-th-large"></i></a>
			</div>
			<div class="clearfix"></div>
			<div id="sizes_table" style="display: none">
        <?
            $color_option = $this->data->option->get_by_id("8");
            //$linked_options = $color_option->get_linked_options();
            //if(!empty($linked_options)){
            if(!empty($color_option)){
        ?>
				<?//=$linked_options[0]->size_table?>
                <img src="<?="http://placehold.it/400x350"?>" alt=""/>
				<?//=$prod->size_table?>
        <?
            }
        ?>
			</div>
			<div class="line"></div>
			<div style="clear: both;"></div>
			<?
			// if ( !empty( $_GET['prod_id'] ) ) {
				// $sale = $this->data->product->get_sale_by_prod_id($_GET['prod_id']);
				// if( !empty( $sale ) ) {
					// error_log(print_r($sale,1));
					// if ( !empty( $sale ) ) {
						// ?>
						<div>
							 <div> <?//=$sale->name?> </div> <span><?//=$sale->price?></span>
						</div>
						<?
					// }
				// }
			// }
			?>
			<div></div>
			<?
			if ($prod->in_stock){
			?>

				<div class="prod_comments_block">
					<div>?????</div>
					<textarea name="comments" style="width: 100%;"></textarea>
				</div>
				<?
				if (site_config::get_value('allow_prod_file_attachments')){
					if (!empty($prod->attachment_files)){
					?>
					<div>
					?? ????? ????? ???? ???? ???? ?????? ?? ?????
					</div>
					<?
					}
				}
				?>
				<div class="clear"></div>

				<div class="big_item_order">
					<div class="quantity">
						??? ????:&nbsp;
						<input type="number" name="quantity" id="quantity" min="1" value="1"/>
					</div>
					<div class="order_btn">
						<button id="add_to_cart_buy">
						<i class="fa fa-shopping-cart fa-flip-horizontal"></i>&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<span>???? ???</span>
						</button>
					</div>
                    <div id="to_cart" class="order_btn">
                        <a href="index.php?page=cart"><i class="fa fa-angle-double-left"></i><span>&nbsp;&nbsp;????? ??? ??????</span></a>
                    </div>
					<div class="clear"></div>
				</div>
				<div class="line"></div>
		</form>
        <div class="order_btn">
		<form action="index.php?page=wishlist" method="POST">
			<input type="hidden" name="prod_id" value="<?=$prod->id?>"/>
            <button name="action" value="add_to_wishList">
            <i class="fa fa-heart fa-flip-horizontal"></i>&nbsp;&nbsp;<i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<span>???? ? WISHLIST</span>
            </button>
        </form>
        </div>
			 <?
			 }
			 else{
			 ?>
			 <div class="line"></div>
		</form>
			 <div class="clear"></div>
			<div class="line"></div>
			<div>
				<h2>????? ?? ?????, ?????? ????? ?????? ???? ???? ?????? ?????</h2>
				<form action="index.php" method="post">
					<input type="hidden" name="product_name" value="<?=$prod->name?>"/>
					<div class="form-group" >
						<div>
							<input type="text" class="form-control" id="name" name="name" placeholder="??" required/>
						</div>
						<div>
							<input type="text" class="form-control" id="phone" name="phone" placeholder="?????" required/>
						</div>
						<div>
							<input type="text" class="form-control" id="email" name="email" placeholder="??????"/>
						</div>
						<button name="send_not_in_stock">
							???
						</button>
					</div>
				</form>
			</div>
			<div class="line"></div>
			 <?
			 }
			 ?>
	</div>
	<div class="clear"></div>

	<div id="main_items"  style="float: left;margin-top: 7%;">
		<div class="title"><h1>?????? ?????? ????? </h1></div>
		<?
			// $most_sold = $this->data->product->get_most_sold(4);
			$most_sold = $this->data->product->get_chosen(4);
			if (!empty($most_sold)){
				$i = 0;
				foreach ($most_sold as $prod){
					$i++;

					$prod_block = new view($site_path."components/product_block.php", $prod);
					$this->register_include("prod_block_".$i, $prod_block);
				?>
					{__prod_block_<?=$i?>__}
				<?

				}
			}
		?>

		<div class="clear"></div>

	</div>
</div>

 <?
	}
 }
 ?>
