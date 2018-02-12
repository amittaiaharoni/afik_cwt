<?
	if (!empty($this->view_data)){
		$prod_id 	= "";
		$prod_name	= "";
		$prod_desc	= "";
		$prod_image = "";
		$prod_price = "";
		$prod_price2 = "";

		if (!emptY($this->view_data->id))
			$prod_id = $this->view_data->id;
		if (!emptY($this->view_data->name))
			$prod_name = $this->view_data->name;
		if (!emptY($this->view_data->desc)){
			$prod_desc = $this->view_data->desc;
			$desc_parts = explode(" ", $prod_desc);
			$prod_desc = "";
			for ($i = 0; $i < 10; $i++){
				if (!empty($desc_parts[$i])){
					$prod_desc .= $desc_parts[$i] . " ";
				}
			}
			$prod_desc .= "...";
		}
		if (!emptY($this->view_data->image))
			$prod_image = $this->view_data->image;
		if (!emptY($this->view_data->price))
			$prod_price = $this->view_data->price;
		if (!emptY($this->view_data->price))
			$prod_price2 = $this->view_data->price2;
?>
<div class="item_pnim">
	<div class="item_inner cf">
		<div class="item_pic">
			<a href="index.php?page=product&prod_id=<?=$prod_id?>">
				<img src="<?=site_config::get_value('upload_images_folder').$prod_image?>">
			</a>
		</div>
		<div class="item_title">
			<h5><?=$prod_name?></h5>
		</div>

	</div>
</div>
<?
}
?>