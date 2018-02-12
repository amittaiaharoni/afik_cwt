<div id="brands">
	<?
		$brands = $this->data->manufacturer->get_all();
		if (!empty($brands)){
			foreach ($brands as $brand){
			?>
			<div class="brand_logo">
				<a href="index.php?page=products&brand=<?=$brand->id?>">
					<img src="<?=site_config::get_value('upload_images_folder').$brand->image?>" />
				</a>
			</div>
			<?
			}
		}
	?>
</div>