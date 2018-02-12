<?
	if (!empty($_GET['id'])){
		/* if($_GET['id'] == 3){
			header('Location: index.php?page=bet_merkahat');
		} */
		if($_GET['id'] == 4){
			header('Location: index.php?page=maamar');
		}
		$cat = $this->data->infoCat->get_by_id((int)$_GET['id']);
		
		if (!emptY($cat)){
		?>
		
	<script>
		$(document).ready(function(){
			$(".info_page_tab").hide();
			$(".info_page_tab").first().show();
			$(".info_page_tab_link").on("click", function(e){
				e.preventDefault();
				$(".info_page_tab").hide();
				$("#"+$(this).data("tab_id")+"").show();
			});
			
			<?
			if (!empty($_GET['page_id'])){
			?>
			$(".page_<?=(int)$_GET['page_id']?>_link").click();
			<?
			}
			?>
		});
	</script>
	
   <!--	<div class="info">
		<h1><?=$cat->name?></h1>
		<?=$cat->text?>
	</div>-->

	<?
	$info_pages = $cat->get_pages();
	if (!empty($info_pages)){
		?>
	<!--	<div style="float:right; width: 30%;">
		<?
		foreach ($info_pages as $info_page){
			?>
			<div class="info_page_link">
				<a href="#" class="info_page_tab_link page_<?=$info_page->id?>_link" data-tab_id="info_page_<?=$info_page->id?>"><?=$info_page->name?></a>
			</div>
			<?
		}
		?>
		</div>-->
	   <div id="contain_side">
		<?
		foreach ($info_pages as $info_page){
			?>
			<div class="info_page_tab" id="info_page_<?=$info_page->id?>">
			 <h1>
					<?=$info_page->name?>
				</h1>

					<?=$info_page->desc?>

				<?
				if (!empty($info_page->image)){
				?>
				<img src="<?=site_config::get_value('upload_images_folder').$info_page->image?>"/>
				<?
				}
				?>

					<?=$info_page->text?>

			</div>
			<?
		}
		?>
		</div>
		<?
	}
	?>

<!--<div class="items_holder">
<?
	$prods = $this->data->product->search_products("",0,1);
	foreach ($prods as $prod){
	?>
	<div class="item">
		<div class="item_pic">
			<a href="index.php?page=product&prod_id=<?=$prod->id?>">
				<img src="<?=site_config::get_value('upload_images_folder').$prod->image?>" />
			</a>
		</div>
		<a href="index.php?page=product&prod_id=<?=$prod->id?>">
			<h1><?=$prod->name?></h1>
		</a>
		<?
		if (!empty($prod->barcode)){
		?>
		<a href="index.php?page=product&prod_id=<?=$prod->id?>">
			<h2><?=$prod->barcode?></h2>
		</a>
		<?
		}
		if (!$prod->price_only_on_page){
		?>
			<div class="price"> <a href="index.php?page=product&prod_id=<?=$prod->id?>"><?=$prod->price?><span> ש”ח</span></a></div>
		<?
			if (!emptY($prod->price2) && $prod->price2 > 0){
		?>
			<div class="price2">
				<a href="index.php?page=product&prod_id=<?=$prod->id?>">
					במקום
					<span class="price2_inner">
						<?=$prod->price2?>
					</span>
					<span> ש”ח</span>
				</a>
			</div>
		<?
			}
		}
		?>
		<a href="index.php?page=product&prod_id=<?=$prod->id?>">
			<p>	<?=$prod->desc?>  </p>
		</a>
		<div class="item_det">
			<a href="index.php?page=product&prod_id=<?=$prod->id?>">הזמן עכשיו</a>
		</div>
	</div>
	<?
	}
?>

</div>-->

 <?
		}
	}
?>