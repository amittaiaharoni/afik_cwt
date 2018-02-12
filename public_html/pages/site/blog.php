<div id="contain_side_pnim_blog">
	<div class="info_blog">
<?
if(!empty($_GET) && !empty($_GET['bid'])){
	$blog = $this->data->blog->get_by_id((int)$_GET['bid']);
	if(!empty($blog)){
		$gal_images = $blog->get_gallery();
		?>
		<div><?=$blog->text?></div>
		<?
		foreach($gal_images as $gimage){
			if(!empty($gimage->image)){
			?>
			<img src="<?=site_config::get_value("upload_images_folder").$gimage->image?>" />
			<?}?>
			<p><?=$gimage->desc?></p>
			<?
		}
		if($blog->id < $this->data->blog->get_max_id()){
			for($i = ($blog->id + 1) ; $i <= $this->data->blog->get_max_id() ; $i++){
				$next_blog = $this->data->blog->get_by_id($i);
				if(!empty($next_blog)){
					echo "<a href='index.php?page=blog&bid=$i'>פוסט הבא</a>";
					break;
				}
			}
		}
		if($blog->id >= 2){
			for($i = ($blog->id - 1) ; $i >= 1 ; $i--){
				$prev_blog = $this->data->blog->get_by_id($i);
				if(!empty($prev_blog)){
					echo "<a href='index.php?page=blog&bid=$i'>פוסט הקודם</a>";
					break;
				}
			}
		}
	}
}
?>
</div>
</div>
