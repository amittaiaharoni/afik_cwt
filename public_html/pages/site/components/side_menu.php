<?
	$side_menu_cats = $this->data->infoCat->get_side_menu();
	$parent_cat_id = 0;
	if($_GET['page'] == 'info_page'){
		$page = $this->data->infoPage->get_by_id((int)$_GET['id']);
		$parent_cat_id = $page->cat_id;
	}
	$side_menu_pages = array();
	if(!empty($side_menu_cats)){
		sorter::sort($side_menu_cats,'name','asc');
		$spages = array();
		foreach($side_menu_cats as $scat){
			if(
				($_GET['page'] == 'info_category' && $scat->id == (int)$_GET['id']) ||
				(!empty($parent_cat_id) && $_GET['page'] == 'info_page' && $scat->id == $parent_cat_id)
			){
				$ipages = $scat->get_pages();
				foreach($ipages as $ipage){
					$spages[] = $ipage;
				}
			}
		}
	}
	if($_GET['page'] == 'maamar'){
        unset($spages);
        $side_menu_maamar_cats = $this->data->infoCat->get_by_column('parent_cat_id',4);
	}
	


	function print_search_hiddens($excluded_fields = array()){
		if (!empty($_GET))
			foreach ($_GET as $key => $value){
				$key 	= strip_tags($key);

				if (in_array($key, $excluded_fields))
				// if ($key == "price_range")
					continue;

				if (is_array($value)){
					foreach ($value as $val_key => $val){
						$val 	= strip_tags($val);
						?>
							<input type="hidden" name="<?=$key?>[<?=$val_key?>]" value="<?=$val?>"/>
						<?
					}
				}
				else{
					$value 	= strip_tags($value);
					?>
						<input type="hidden" name="<?=$key?>" value="<?=$value?>"/>
					<?
				}
			}
	}
?>
<script>
	$(document).ready(function(){
		$(".search_filter").on("click", function(){
			$(this).parents("form")[0].submit();
		});

		$(".search_color a").on("click", function(e){
			e.preventDefault();
		});
	});
</script>
<div id="side_menu">
	<?
	// if (!empty($side_menu_cats)){
	if (!empty($spages) && ($_GET['page'] == 'info_category' || $_GET['page'] == 'info_page')){
	?>
	<div id="accordian">
		<ul>
			<?
			// foreach($side_menu_cats as $cat){
			foreach($spages as $cat){
				$subcats = '';//$cat->get_subcats();
			?>
			<li>
				<h3 class="side_menu_lvl_1_link">
					<a href="index.php?page=info_page&id=<?=$cat->id?>">
						<?=$cat->name?>
					</a>
				</h3>
				<?
				if (!empty($subcats)){
				?>
				<ul class="side_menu_lvl_1_list">
					<?
					foreach ($subcats as $subcat){
						$subsubcats = $subcat->get_subcats();
					?>
					<li>

						<?
						if (!empty($subsubcats)){
						?>
						<h3 class="side_menu_lvl_2_link"><?=$subcat->name?></h3>
						<ul class="side_menu_lvl_2_list">
							<?
							foreach ($subsubcats as $subsubcat){
							?>
							<li>
								<a class="side_menu_link_<?=$subsubcat->id?>" href="index.php?page=info_category&id=<?=$subsubcat->id?>"><?=$subsubcat->name?></a>
							</li>
							<?
							}
							?>
						</ul>
						<?
						}
						else{
						?>
						<a class="side_menu_link_<?=$subsubcat->id?>" href="index.php?page=info_category&id=<?=$subcat->id?>"><?=$subcat->name?></a>
						<?
						}
						?>
					</li>
					<?
					}
					?>
				</ul>
				<?
				}
				?>
			</li>
			<?
			}
			?>

		</ul>
	</div>
	<?
	}
    else if(!empty($side_menu_maamar_cats)){ ?>
    	<div id="accordian">
			<ul>
	<?
        foreach($side_menu_maamar_cats as $cat){
            ?>
				<li>
					<h3 class="side_menu_lvl_1_link">
            <a href="index.php?page=maamar&id=<?=$cat->id?>">

                    <?=$cat->name?>

            </a>
			</h3>
				</li>
            <?
        } ?>
			</ul>
        </div>
		<?
    }
	else if (!empty($side_menu_cats)){
	?>
	<div id="accordian">
		<ul>
			<?
			foreach($side_menu_cats as $cat){
				if($cat->id != $_GET['id'])
					continue;
			// foreach($spages as $cat){
				// $subcats = '';//$cat->get_subcats();
				$subcats = $cat->get_subcats();
			?>
			<li>
				<!--
				<h3 class="side_menu_lvl_1_link">
					<a href="index.php?page=info_page&id=<?=$cat->id?>">
						<?=$cat->name?>
					</a>
				</h3>
				-->
				<?
				if (!empty($subcats)){
				?>
				<ul class="side_menu_lvl_1_list" style="display: block;">
					<?
					foreach ($subcats as $subcat){
						$subsubcats = $subcat->get_subcats();
					?>
					<li>

						<?
						if (!empty($subsubcats)){
						?>
						<h3 class="side_menu_lvl_2_link"><?=$subcat->name?></h3>
						<ul class="side_menu_lvl_2_list">
							<?
							foreach ($subsubcats as $subsubcat){
							?>
							<li>
								<a class="side_menu_link_<?=$subsubcat->id?>" href="index.php?page=info_category&id=<?=$subsubcat->id?>"><?=$subsubcat->name?></a>
							</li>
							<?
							}
							?>
						</ul>
						<?
						}
						else{
						?>
						<a class="side_menu_link_<?=$subsubcat->id?>" href="index.php?page=info_category&id=<?=$subcat->id?>"><?=$subcat->name?></a>
						<?
						}
						?>
					</li>
					<?
					}
					?>
				</ul>
				<?
				}
				?>
			</li>
			<?
			}
			?>

		</ul>
	</div>
	<?
	}
	?>
    <!--<div id="accordian2">
		<ul>

			<li>
				<h3><i class="fa fa-plus"></i><span>חיפוש לפי מחיר</span></h3>
				<div id="search_price">
					<form method="get" action="index.php">
						<?
						print_search_hiddens(array("price_range"));
						?>

						<input type="radio" name="price_range" class="search_filter" value="0-50" <?=(find_key_value_pair($_GET,"price_range","0-50")?"checked":"")?>>עד 50 <span> &#8362;</span><br />
						<input type="radio" name="price_range" class="search_filter" value="50-100" <?=(find_key_value_pair($_GET,"price_range","50-100")?"checked":"")?>>50-100 <span> &#8362;</span><br />
						<input type="radio" name="price_range" class="search_filter" value="100-200" <?=(find_key_value_pair($_GET,"price_range","100-200")?"checked":"")?>>100-200<span> &#8362;</span><br />
						<input type="radio" name="price_range" class="search_filter" value="200-300" <?=(find_key_value_pair($_GET,"price_range","200-300")?"checked":"")?>>200-300<span> &#8362;</span><br />
						<input type="radio" name="price_range" class="search_filter" value="300-500" <?=(find_key_value_pair($_GET,"price_range","300-500")?"checked":"")?>>300-500<span> &#8362;</span><br />
						<input type="radio" name="price_range" class="search_filter" value="500-" <?=(find_key_value_pair($_GET,"price_range","500-")?"checked":"")?>>מעל 500<span> &#8362;</span>
					</form>
				</div>
			</li>
			<?
				if (!empty($_GET['cat'])){
					$cat = $this->data->category->get_by_id((int)$_GET['cat']);
					if (!empty($cat)){
						$options = $cat->get_options();
						if (!empty($options)){
							foreach ($options as $option){
							?>
							<li class="active">
								<h3><i class="fa fa-plus"></i><span>חיפוש לפי <?=$option->name?></span></h3>
								<div class="search_color">
									<form method="get" action="index.php">
									<?
									print_search_hiddens(array("search_opt_det"));


										$details = $option->get_details();

										if (!empty($details)){
											foreach ($details as $detail){
											?>


											<span>

												<input type="radio" name="search_opt_det[<?=$detail->id?>]" id="search_opt_det<?=$detail->id?>"  class="search_filter" value="<?=$detail->id?>" <?//=(!empty($_GET["search_opt_det"][$detail->id])?"checked":"")?>/>

												<?
												$image = "";
												if (!empty($detail->image)){
													$image = $detail->image;
												}

												if (!empty($image)){
												?>
												<label for="search_opt_det<?=$detail->id?>" <?=(!empty($_GET["search_opt_det"][$detail->id])?"style='outline: #F00 solid 2px;'":"")?>>
													<img src="<?=site_config::get_value('upload_images_folder').$image?>"/>
												</label>
                                                   <h5>	<?=$detail->name?> </h5>
												<?
												}
												else{
												?>
												<label for="search_opt_det<?=$detail->id?>" <?=(!empty($_GET["search_opt_det"][$detail->id])?"style='outline: #F00 solid 2px;'":"")?>>
													<?=$detail->code?>
												</label>
													<h5>	<?=$detail->name?> </h5>
												<?
												}
												?>
											</span>
											<?
											}
										}
									?>
									</form>
								</div>
							</li>
							<?
							}
						}
					}
				}
			?>


			<li class="active">
				<h3><i class="fa fa-plus"></i><span>חיפוש לפי צבע</span></h3>
				<div id="search_color">
				<a href="#" title="color name" style="background-color: #FF0033"></a>
				<a href="#" title="color name" style="background-color: #FFCC00"></a>
				<a href="#" title="color name" style="background-color: #3399FF"></a>
				<a href="#" title="color name" style="background-color: #33CC66"></a>
				<a href="#" title="color name" style="background-color: #FF3300"></a>
				<a href="#" title="color name" style="background-color: #9933CC"></a>
				<a href="#" title="color name" style="background-color: #999999"></a>
				<a href="#" title="color name" style="background-color: #000000"></a>
				<a href="#" title="color name" style="background-color: #FFFFFF"></a>
				</div>
			</li>


		</ul>
	</div>-->

    <!--<div>
    <div class="pnim_banner">
     <div class="pnim_banner_text">
     <?=$this->data->banner->get_by_id("9")->html?>
  <a href="<?=$this->data->banner->get_by_id("9")->link?>">קנה עכשיו</a>
 </div>
    <img src="<?=site_config::get_value('upload_images_folder').$this->data->banner->get_by_id("9")->image?>" />

    </div>
    <?
		$facebook_bnr = $this->data->banner->get_by_id("1");
		$instagram_bnr = $this->data->banner->get_by_id("2");
		if (!empty($facebook_bnr)){
		?>
		<a href="<?=$facebook_bnr->link?>" target="_blank"><img src="<?=site_config::get_value('upload_images_folder').$facebook_bnr->image?>" width="100%"  /></a>
		<?
		}
		else{
		?>
		<a href="https://www.facebook.com/toseesun" target="_blank"><img src="pics/facebook_btn.jpg"  /></a>
		<?
		}
		if (!empty($instagram_bnr)){
		?>
		<a href="<?=$instagram_bnr->link?>" target="_blank" class="inst"><img src="<?=site_config::get_value('upload_images_folder').$instagram_bnr->image?>"width="100%" /></a>
		<?
		}
		else{
		?>
		<a href="https://instagram.com/tosee_sunglasses/" target="_blank" class="inst"><img src="pics/instagram_btn.jpg" /></a>
		<?
		}
		?>
	</div>-->
<div id="side_cats">
<?
$cats = $this->data->category->get_by_column('parent_cat_id', 2);
sorter::sort($cats,'display_order','asc');
if(!empty($cats)){
	foreach($cats as $cat){
		$banner  ='';
		$img  ='';
		if(!empty($cat->banner_image) && file_exists(site_config::get_value('upload_images_folder').$cat->banner_image)){
			$banner = site_config::get_value('upload_images_folder').$cat->banner_image;
		}
		if(!empty($cat->side_image) && file_exists(site_config::get_value('upload_images_folder').$cat->side_image)){
			$img = site_config::get_value('upload_images_folder').$cat->side_image;
		}
?>
	<Div class="catblock cf">
		<a href="index.php?page=category&cat=<?=$cat->id?>">
			<div class="txt">
				<img src="<?=$img?>" alt="" />
				<p><?=$cat->desc?></p>
			</div>
			<Div class="poc">
				<img src="<?=$banner?>" alt="" />
			</Div>
		</a>
	</Div>
<?
	}
}
?>
<!--
	<Div class="catblock cf">
	<a href="#">


	<div class="txt">
	<img src="pics/logo2.png" alt="" />
	<p>מוצרי חבישה
	עם יכולת ספיגת
	הפרשות גבוהה</p>
	</div>
	<Div class="poc"><img src="pics/cat2.jpg" alt="" /></Div>
	</a>
	</Div>

	<Div class="catblock cf">
	<a href="#">
	<div class="txt">
	<img src="pics/logo3.png" alt="" />
	<p>מבוססים על דבש טהור
	לריפוי פצעים וכוויות</p>
	</div>
	<Div class="poc"><img src="pics/cat3.jpg" alt="" /></Div>
	</a>
	</Div>

	<Div class="catblock cf">
	<a href="#">
	<div class="txt">
	<img src="pics/logo4.png" alt="" />
	<p>תמיסה סטרילית אנטיספטית
	לניקוי והרטבה של
	פצעים וכוויות</p>
	</div>
	<Div class="poc"><img src="pics/cat4.jpg" alt="" /></Div>
	</a>
	</Div>

	<Div class="catblock cf">
	<a href="#">
	<div class="txt">
	<h2>רימות</h2>
	<p>ניקוי פצעים כרוניים
	מן הרקמה הנמקית</p>
	</div>
	<Div class="poc"><img src="pics/cat5.jpg" alt="" /></Div>
	</a>
	</Div>
-->
</div>

	<div id="side_contact">
   <!--	<?//include('contact.php'); ?>  -->
	<h2>לכל שאלה והצעת מחיר<br>
     צרו אתנו קשר
	</h2>

     <form method="post" action="">
		<input name="name"  placeholder="שם" type="text" />
		<input name="phone" placeholder="טלפון" type="text" />
		<input name="email" placeholder="כתובת האימייל" type="text" />
		<button name="send_quick" class="btn-default">שלח פרטים</button>
	</form>

	</div>
</div>

