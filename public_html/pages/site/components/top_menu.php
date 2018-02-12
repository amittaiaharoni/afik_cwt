<?php
$selected_prods = $this->data->product->get_by_column("show_on_top_menu","1");
$selected_cats = array();
foreach ($selected_prods as $slprod) {
    $selected_prods_array[$slprod->id] = $slprod->get_parent_cats_array();
}
unset($selected_prods);
?>
<ul class="sf-menu" id="example">
    <?
    $menu_cats = $this->data->category->get_top_menu();
    sorter::sort($menu_cats,"display_order","ASC");
    foreach ($menu_cats as $cat){
        if (!empty($cat)){
            $subcats = $cat->get_subcats();
            sorter::sort($subcats,"display_order","ASC");
    ?>
    <li>
		<?
		if(empty($subcats)){
		?>
        <a href="index.php?page=category&cat=<?=$cat->id?>">
        <?=$cat->name?>
        <span></span>

        </a>
            <?
		}
		else{
			?>
			<a href="#">
			<?=$cat->name?>
			<span></span>

			</a>
            <?
		}
		?>
		<div class="sf-mega small">
		<?
		if (!emptY($subcats)){
			foreach($subcats as $subcat){
				$simage = '';
				if(!empty($subcat->image) && file_exists(site_config::get_value('upload_images_folder').$subcat->image))
					$simage = '<img class="menu_img" src="'.site_config::get_value('upload_images_folder').$subcat->image.'">';
		?>
				
					<div class="inner_menu">
						<a href="index.php?page=category&cat=<?=$subcat->id?>"><h2><?=$subcat->name?></h2></a>
						<div class="innermenu_cats">
						<?
						$sscats = $subcat->get_subcats();
						if(!empty($sscats)){
						?>
						<ul class="categories">
							<?
							foreach($sscats as $sscat){
								$img = "";
								if(!empty($sscat->image) && file_exists(site_config::get_value('upload_images_folder').$sscat->image))
									$img = '<img src="'.site_config::get_value('upload_images_folder').$sscat->image.'">';
								?>
								<li><a href="index.php?page=category&cat=<?=$sscat->id?>"><?=$sscat->name?><?=$img?></a></li>
								<?
							}
							?>
						</ul>
						<?
						}
						?>
						</div>
						<div class="innermenu_img">
							<?=$simage?>
						</div>
					</div>
			<?
			}
			?>	
		</div>
			<?
            }
            ?>
    </li>
    <?
        }
    }
	$infocats = $this->data->infoCat->get_top_menu();
    sorter::sort($infocats,"display_order","ASC");
    foreach ($infocats as $cat){
        if (!empty($cat)){
            $subcats = $cat->get_subcats();
            sorter::sort($subcats,"display_order","ASC");
    ?>
   <li>
		<?
		?>
        <a href="index.php?page=info_category&id=<?=$cat->id?>">
        <?=$cat->name?>
        <span></span>
        </a>
            <?

            ?>
    </li>
		<?
		}
	}
    ?>
	<li><a href="?page=contact">צור קשר	<span></span>    </a></li>
</ul>
<div id="main_menu_mob">
<div id="dl-menu" class="dl-menuwrapper dl-menu">
    <button class="dl-trigger">Open Menu</button>
    <ul class="dl-menu">
    <?
    sorter::sort($menu_cats,"display_order","ASC");
    foreach ($menu_cats as $cat){
        if (!empty($cat)){
            $subcats = $cat->get_subcats();
            sorter::sort($subcats,"display_order","ASC");
    ?>
    <li>
        <a href="index.php?page=category&cat=<?=$cat->id?>"><?=$cat->name?></a>
        <?
        if (!emptY($subcats)){
            ?>
			<ul class='dl-submenu'><li class='dl-back'><a href='#'>חזור</a></li>
			<?
            foreach($subcats as $subcat){
        ?>
            <li>
                <a href="index.php?page=category&cat=<?=$subcat->id?>"><?=$subcat->name?></a>
            </li>
        <?
            }
            echo "</ul>";
        ?>
    </li>
    <?
        }
    }}
    $infocats = $this->data->infoCat->get_top_menu();
    sorter::sort($infocats,"display_order","ASC");
    foreach ($infocats as $cat){
        if (!empty($cat)){
            $subcats = $cat->get_pages();
            sorter::sort($subcats,"display_order","ASC");
    ?>
   <li>
		<?
		if(empty($subcats)){
			?>
			<a href="index.php?page=info_category&id=<?=$cat->id?>" title="<?=$cat->name?>">
				<?=$cat->name?>
			</a>
            <?
		}
		else{
			if(count($subcats)==1){
			?>
			<a href="index.php?page=info_page&id=<?=$subcats[0]->id?>" title="<?=$cat->name?>">
				<?=$cat->name?>
			</a>
			<?
			}
			else{
				?>
				<a href="index.php?page=info_category&id=<?=$cat->id?>" title="<?=$cat->name?>">
					<?=$cat->name?>
				</a>
				<?
				if (!emptY($subcats) && $cat->id != 4 && $cat->id != 3){
					?>
					<ul class='dl-submenu'><li class='dl-back'><a href='#'>חזור</a></li>
				   <?
					foreach ($subcats as $subcat){
						?>
						<li>
							<a href="index.php?page=info_page&id=<?=$subcat->id?>"><?=$subcat->name?></a>
						</li>
						<?
					}
				   ?>
				   </ul>
				   <?
				}
			}
		}
		?>
		<?
            /* if (!emptY($subcats)){
            ?>
	   		<div class="innermenu_cats">
								<a href="index.php?page=info_category&id=<?=$subcat->id?>">
									<?=$subcat->name?>
								</a>
						</div>
			<?
            } */
            ?>
    </li>
		<?
		}
	} 
	?>
  	<li><a href="?page=contact">צור קשר</a></li>
    </ul>
</div>
</div>
	<script src="js/modernizr-custom_prefixed.js"></script>
    <script src="js/jquery.dlmenu.js"></script>
    <script>
        $(function() {
            $( '#dl-menu' ).dlmenu({
                animationClasses : { classin : 'dl-animate-in-2', classout : 'dl-animate-out-2' }
            });
        });

    </script>
<?php ?>
