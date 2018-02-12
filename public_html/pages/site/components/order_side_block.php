<div id="cart_side_holder">
	<div class="secure">הנך נמצא בשרת מאובטח</div>
	<h2>צריכים עזרה?</h2>
	<div>טלפון: 052-403-3849</div>
	<div><a href="mailto: sale@tosee.co.il"> sale@tosee.co.il </a></div>
	
	<?
	$page_title_1 = "";
	$info_page = $this->data->infoPage->get_by_id("1");
	if (!empty($info_page))
		$page_title_1 = $info_page->name;
		
	$page_title_2 = "";
	$info_page = $this->data->infoPage->get_by_id("2");
	if (!empty($info_page))
		$page_title_2 = $info_page->name;
		
	$page_title_3 = "";
	$info_page = $this->data->infoPage->get_by_id("3");
	if (!empty($info_page))
		$page_title_3 = $info_page->name;
	?>
	<h2>החזרת מוצרים</h2>
	<div>
		<a href="index.php?page=info_page&id=1"><?=$page_title_1?></a>
	</div>
	<div>
		<a href="index.php?page=info_page&id=2"><?=$page_title_2?></a>
	</div>

	<h2>משלוח ודמי משלוח</h2>
	<div>
		<a href="index.php?page=info_page&id=3"><?=$page_title_3?></a>
	</div>
	<div class="secure_logos">
		<img src="pics/secure.jpg" />
	</div>
</div>