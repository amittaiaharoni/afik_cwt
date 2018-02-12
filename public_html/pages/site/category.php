<?
	$prods_to_show = 15;
	$current_page = 1;
	if (!empty($_GET['page_num']) && (int)$_GET['page_num'] > 1)
		$current_page = (int)$_GET['page_num'];
	$pages_count = 1;

	$page_cats = array();

	$top_banner = "";
	$gallery_images = null;
	$page_title = "";

	if (!emptY($_GET['cat'])){
		
		$cat = $this->data->category->get_by_id($_GET['cat']);
		if (!emptY($cat)){
			$pages_count = $cat->get_pages_count($prods_to_show);

			if (!empty($cat->cat_image))
				$top_banner = site_config::get_value('upload_images_folder').$cat->cat_image;

			$cat_images = $cat->get_gallery();
			if (!empty($cat_images))
				$gallery_images = $cat_images;

	?>


	<div id="contain_side">

		<?
		$saved = null;
		$sales = $this->data->sale->get_all();
		if(!empty($sales)){
			foreach($sales as $sale){
				if(in_array($cat->id,$sale->categories)){
					$saved = $sale;
					break;
				}
			}
		}
		?>
		<div id="top_sell">
			<div class="title"><h1><?=$cat->name?></h1> 	</div>
		   <div class="top_cat_holder cf">

            <div class="category_text">
             		<div class="top_pic">
			<?
				if(!empty($top_banner)){
				?>
					<img src="<?=$top_banner?>">
				<?
				}
			?>
		</div>
			<?=$cat->text?></div>
		   </div>


            <?
				/*
			<div class="pages_count">
				if ($pages_count > 1){
					$pagination = new view($site_path."components/pagination.php", $pages_count);
					$this->register_include("pagination_top", $pagination);
					$this->register_include("pagination_bottom", $pagination);
					?>
					{__pagination_top__}
					<?
				}
				?>
			</div>
			<div id="items_holder">
				<?
				$prods = $cat->get_products($prods_to_show, ($current_page-1)*$prods_to_show);

				if (!empty($prods)){
					$i = 0;
					foreach ($prods as $prod){
						if(empty($prod))
							continue;
						$i++;
						//error_log("page ".$i." d_o ".$prod->display_order);
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
			<div class="pages_count">
				<?
				if ($pages_count > 1){
					?>
					{__pagination_bottom__}
					<?
                }
            </div>
                 */
				?>
			{__products__}
		</div>
	</div>

	<?
		}
	}
?>
