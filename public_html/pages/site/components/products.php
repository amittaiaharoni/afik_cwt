<?
	$prods_to_show = 15;
	$current_page = 1;

	if (!empty($_GET['page_num']) && (int)$_GET['page_num'] > 1)
		$current_page = (int)$_GET['page_num'];

	$prods_count = 0;
	$pages_count = 1;
	$paging_html = "";

	$search_text = "";
	$search_cat_id = 0;
	$search_is_sale = 0;
	$search_in_stock = 0;
	$search_min_price = 0;
	$search_max_price = 0;
	$search_manufacturer_id = 0;
	$search_option_details = array();
	$search_num_to_show = $prods_to_show;
	$search_num_to_skip = $prods_to_show * ($current_page - 1);

	if (!emptY($_REQUEST['search']))
		$search_text = $_REQUEST['search'];

	if (!emptY($_REQUEST['cat']))
		$search_cat_id = $_REQUEST['cat'];

	if (!emptY($_REQUEST['is_sale']))
		$search_is_sale = $_REQUEST['is_sale'];

	if (!emptY($_REQUEST['in_stock']))
		$search_in_stock = $_REQUEST['in_stock'];

	if (!emptY($_REQUEST['price_range'])){
		$prices = explode("-",$_REQUEST['price_range']);
		if (!empty($prices[0]))
			$search_min_price = $prices[0];
		if (!empty($prices[1]))
			$search_max_price = $prices[1];
	}

	if (!emptY($_REQUEST['min_price']))
		$search_min_price = $_REQUEST['min_price'];

	if (!emptY($_REQUEST['max_price']))
		$search_max_price = $_REQUEST['max_price'];

	if (!emptY($_REQUEST['brand']))
		$search_manufacturer_id = $_REQUEST['brand'];

	if (!emptY($_REQUEST['cat_id']))
		$search_cat_id = $_REQUEST['cat_id'];

	if (!empty($_REQUEST['search_opt_det']))
		$search_option_details = $_REQUEST['search_opt_det'];

	$prods_count = $this->data->product->search_products($search_text, $search_cat_id, $search_is_sale, $search_in_stock, $search_min_price, $search_max_price, $search_manufacturer_id, $search_option_details, $search_num_to_show, $search_num_to_skip, true);
	$pages_count = (int)ceil($prods_count / $prods_to_show);

	$prods = $this->data->product->search_products($search_text, $search_cat_id, $search_is_sale, $search_in_stock, $search_min_price, $search_max_price, $search_manufacturer_id, $search_option_details, $search_num_to_show, $search_num_to_skip);
	//sorter::sort($prods,"display_order", "asc");
	/*if ($pages_count > 1){
		$qs = "";
		unset($_GET['page_num']);
		$qs = http_build_query($_GET);

		ob_start();

		for ($i = 1; $i <= $pages_count; $i++){
		?>
		<div style="display: inline-block;">
			<a href="index.php?<?=$qs?>&page_num=<?=$i?>">
				<?=$i?>
			</a>
		</div>
		<?
		}

		$paging_html = ob_get_clean();
	}*/

	$pagination = new view($site_path."components/pagination.php", $pages_count);
	$this->register_include("pagination_top", $pagination);
	$this->register_include("pagination_bottom", $pagination);

	?>
   <!--	<div class="pages_count">
		{__pagination_top__}
	</div>-->
	<div id="items_holder" class="cf">
		<?
		if (!empty($prods)){
			$i = 0;
			foreach ($prods as $prod){

				$prod_block = new view($site_path."components/product_block.php", $prod);
				$this->register_include("prod_block_".$i, $prod_block);
		?>
			{__prod_block_<?=$i++?>__}
			<?/*
			<div class="item">
				<div class="item_inner">
					<div class="item_pic pnim"><a href="index.php?page=product&prod_id=<?=$prod->id?>"><img src="<?=site_config::get_value('upload_images_folder').$prod->image?>"></a></div>
					<div class="item_title"><h4><?=$prod->name?></h4></div>
					<div class="item_price"><span>&#8362;</span><?=$prod->price?></div>
					<div class="item_order"><a href="index.php?page=product&prod_id=<?=$prod->id?>"><i class="fa fa-shopping-cart fa-flip-horizontal"></i>&nbsp;&nbsp;&nbsp;הוסף לסל</a></div>
				</div>
			</div>
			*/?>
		<?
			}
		}
		?>
		<div class="clear"></div>
	</div>
  <!--	<div class="pages_count">
		{__pagination_bottom__}
	</div>-->
