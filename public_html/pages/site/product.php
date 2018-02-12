<?
	if (!emptY($_GET['prod_id'])){
		$prod = $this->data->product->get_by_id($_GET['prod_id']);
		$barcodes = $this->data->barcode->get_by_column('prod_id',$prod->id);
		$color_stock = false;
		if (!empty($prod)/*  && $prod->stock_count > 0 && $prod->in_stock */){
?>
<script>
	function calculate_price(){
		var price = <?=$prod->price?>;
		var quantity = parseFloat($("#quantity").val());
		if(quantity <= 0 || isNaN(quantity))
			quantity = 1.00;
		// console.log(quantity);
		price *= parseFloat(quantity);
		$("#total_price").text(parseFloat(price).toFixed(2));

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
		$("a.additional_img_link").fancybox();
        $("#to_cart").hide();


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
                $("#add_to_cart_buy span").text("הפריט נוסף לסל");
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
		$("#add_linked_to_cart").click(function(){
			var ids = $(this).data('comp_ids');
			$.ajax({
				type: 'post',
				data: {
					action: 'add_comps',
					ids: ids
				},
				success: function(ret){
					if(ret){
						$("#top_basket").load(location.href + " #top_basket>*");
					}
				}
			});
		});

	});
</script>
<div id="contain_side_pnim" class="cf">
<!--	<div id="back_btn">
		<a href="javascript:history.go(-1)"><i class="fa fa-angle-left"></i>&nbsp;&nbsp;חזרה לקטגוריה</a>
	</div>-->

	<div id="description">


            <div class="prod_title"> <h1><?=$prod->name?></h1></div>
			<div style="padding:2% 0;">

				<div class="clear"></div>

    <section class="tabs">
		<div id="big_item_pic">
		<div class="main_pic">
			<a rel ="huj" class="additional_img_link" href="<?=site_config::get_value('upload_images_folder').$prod->image?>">
				<img class="main_pic_small" src="<?=site_config::get_value('upload_images_folder').$prod->image?>" data-zoom-image="<?=site_config::get_value('upload_images_folder').$prod->image?>"/>
			</a>
		</div>
		<div class="additional_img_thumbs">
		<?
			$images = $prod->get_gallery();
			if (!empty($images)){
				foreach ($images as $image){
			?>
			<div class="gallery_img">
				<a rel ="huj" href="<?=site_config::get_value('upload_images_folder').$image->image?>" class="additional_img_link" data-small_image="<?=site_config::get_value('upload_images_folder').$image->image?>" data-big_image="<?=site_config::get_value('upload_images_folder').$image->image?>" >
					<img src="<?=site_config::get_value('upload_thumbs_folder').$image->image?>"/>
				</a>
			</div>
			<?
				}
			}
		?>
		</div>


    <div class="clear"></div>

	</div>
		<p><?=$prod->text?></p>
    </section>
	</div>
    	<div style="float: left;">
					<div class="fb-share-button" data-href="<?=site_config::get_value("site_url")?>index.php?page=product&amp;prod_id=<?=$prod->id?>" data-layout="button"></div>
				</div>
	<div class="clear"></div>

<?
if(!empty($barcodes)){
	?>
	<div id="barcodes_table">
	<div id="barcode_header">
		<span class="bar_name">שם המוצר</span>
		<span class="bar_bar">מק"ט</span>
		<span class="bar_qua">כמות באריזה</span>
	</div>
	<?
	foreach($barcodes as $barcode){
?>
	<div class="barcode_row">
		<span class="bar_name"><?=$barcode->name?></span>
		<span class="bar_bar"><?=$barcode->barcode?></span>
		<span class="bar_qua"><?=$barcode->pack_quantity?></span>
	</div>
<?
	}
	?>
    </div>
	<?
}
?>


</div>
 {__linked_prods__}
 </div>

 <?
	}
 }
 ?>
