<div id="contain_side">
<?
	if (!empty($_GET['id'])){
		$info_page = $this->data->infoPage->get_by_id((int)$_GET['id']);
		
		if (!emptY($info_page)){
		?>	
	<div  class="info_page_tab">
	  <h1><?=$info_page->name?></h1> 
	   <?
	   if(!empty($info_page->image)){
	   ?>
       <?=$info_page->text?>
      <img src="<?=site_config::get_value('upload_images_folder').$info_page->image?>" />
		<?
		}
		else{
			?>
			<?=$info_page->text?>
			<?
		}
		?>

	</div>
	<div style="clear:both"></div>
 <div class="clear"></div>
 <?
		}
	}
	else 
		include('contact.php');
?>
</div>
<!--
<div id="side_menu">
<div>
    <div class="pnim_banner">
     <div class="pnim_banner_text">
     <?=$this->data->banner->get_by_id("9")->html?>
  <a href="<?=$this->data->banner->get_by_id("9")->link?>">קנה עכשיו</a>
 </div>
    <img src="<?=site_config::get_value('upload_images_folder').$this->data->banner->get_by_id("9")->image?>" />
    <!-- <img src="pics/pnim_banner.jpg" /> ->
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
	</div>
</div>
-->