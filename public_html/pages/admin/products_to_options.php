<?
	$options_by_id = array();
	if (!empty($_GET['prod_id'])){
		$prod = $this->data->product->get_by_id((int)$_GET['prod_id']);
		if (!emptY($prod)){
			$cats = $prod->categories;
			foreach ($cats as $cat_id){
				$cat_options = $this->data->option->get_by_cat_id($cat_id);
				foreach ($cat_options as $option){
					$options_by_id[$option->id] = $option;
				}
			}
		}
	}
?>
<script type="text/javascript" charset="utf-8">
	$(document).ready( function () {
		/*$('#options_container form').ajaxForm(function() {
			// alert("Thank you for your comment!");
		});*/

		// submit data
		$("#submit_btn").on("click", function(){
			var requestsOut = 0;
			$("#options_container").find("form").each(function(){
				// $($("form")[1]).find("input[type=checkbox][name=option_det_id]").is(":checked")

				var is_checked = 0;
				var row_id = $(this).find("input[name=update_id]").val();
				var checkbox = $(this).find("input[type=checkbox][name=option_det_id]");
				if (checkbox)
					is_checked = checkbox.is(":checked");

				if (is_checked){ // checked = save
					requestsOut++;
					//$(this).submit();

					$(this).submit(function(e) {
						var formObj = $(this);
						var formURL = formObj.attr("action");
						var formData = new FormData(this);
						$.ajax({
							url: formURL,
							type: 'POST',
							data:  formData,
							mimeType:"multipart/form-data",
							contentType: false,
							cache: false,
							processData:false,
							success: function(data, textStatus, jqXHR) {

							},
							error: function(jqXHR, textStatus, errorThrown) {
							}
						});
						e.preventDefault(); //Prevent Default action.
						// return false;
						// $(e).unbind();
					});
					$(this).submit(); //Submit the form
				}
				else{ // unchecked = delete
				  if (row_id){
					$.ajax({
					  type: "POST",
					  async: false,
					  url: "<?=$this->page_path?>&action=delete_dt_data",
					  data: "delete_id=" + row_id,
					  success: function(){
					  }
					});
				  }
				}
			});
			$( window ).ajaxComplete(function() {
				requestsOut--;
				console.log( "Triggered ajaxComplete handler.", requestsOut );
				if (requestsOut == 0) {
					window.location = window.location;
				}
			});
		});

		$(".delete_image_btn").on("click", function(){
			var container = $(this).parent();
			var image_name = $(this).data("image_name");
			var row_update_id = $($(this).parents("form")[0]).find("#update_id").val();

			$.ajax({
				type: "POST",
				url: "<?=$this->page_path?>&action=admin_delete_image",
				data: { image: image_name, update_id: row_update_id }
			}).done(function( msg ) {
				var image_input = container.find("input[type=file]").clone();
				container.parent().append(image_input);
				container.siblings(".admin_img_container").remove();
				container.remove();
			});
		});
	});
</script>

<div id="page_wraper" class="container-fluid" style="position: relative;">
	<div class="page-header">
        <h2>אפשרויות מוצר</h2>
    </div>
	<div class="col-md-12" id="options_container">
		<div class="panel panel-primary">
			<div class="panel-body">
				<div class="form">
					<?
					if (!empty($options_by_id))
						foreach ($options_by_id as $oid => $option){
							$details = $option->get_details();
							$linked_options = $option->get_linked_options();
						?>
						<div>
							<h1><?=$option->name?></h1>

                            <div class="row">
							<?
								foreach ($details as $detail){
									$detail_data = $this->data->details2product->get_detail_data_for_product((int)$_GET['prod_id'], $detail->id);

									$added_price = $detail->added_price;
									if (!empty($detail_data->price))
										$added_price = $detail_data->price;

									$stock = 0;
									if (!empty($detail_data->stock))
                                        $stock = $detail_data->stock;
									$linked_prod = $detail_data->linked_prod;
							?>
								<div class="col-md-3 col-md-offset-1 well">
									<form method="post" action="" enctype="multipart/form-data"  role="form">
										<input type="hidden" name="action" value="<?=(empty($detail_data)?"admin_insert_data":"admin_edit_data")?>"/>
										<input type="hidden" name="update_id" id="update_id" value="<?=(!empty($detail_data)?$detail_data->id:"")?>"/>
										<div class="form-group">
											<div class="col-md-12">
												<div>
													<label>צבע ראשי</label>
														<input type='hidden' value='0' name='option_main'>
														<input type="checkbox" class="form-control" name="option_main" value="1" <?=(!empty($detail_data->option_main)?"checked":"")?>>
													</div>
													<div class="clearfix"></div>
													<div style="display: inline-block; width: 30px;">
														<input type='hidden' value='0' name='option_det_id'>
														<input type="checkbox" class="form-control" name="option_det_id" value="<?=$detail->id?>" <?=(!empty($detail_data)?"checked":"")?>>
													</div>
													<div style="display: inline-block; width: 30px;">
													
													<div style="width: 30px;">
													<?
													if (!empty($detail->image)){
														?>
														<img src="<?=site_config::get_value('upload_thumbs_folder').$detail->image?>" style="max-width: 60px;"/>
														<?
													}
													?>
													<label>
														<?=$detail->name?>
													</label>
                                                    </div>
												</div>
												<div>
													<label>
														תוספת מחיר: <input type="text" name="price" class="form-control" style="width: 100px;" value="<?=$added_price?>"/>
													</label>
													<label>
														מלאי: <input type="text" name="stock" class="form-control" style="width: 100px;" value="<?=$stock?>"/>
													</label>
												</div>
											</div>
											<div class="col-md-12">
                                            <div class="form-group">
                                                <label>
                                                <?php /*if (!empty($detail_data->link)): ?>
                                                    לינק למוצר: <input type="text" name="link" class="form-control" dir="ltr" value="<?=$detail_data->link?>"/>
                                                <?php else: ?>
                                                    לינק למוצר: <input type="text" name="link" class="form-control" dir="ltr" placeholder="index.php" value=""/>
                                                <?php endif;*/ ?>
                                                </label>
                                            </div>
                                            <div class="form-group">
													<div>
														<label for="image">תמונה 1</label>
													</div>
													<?
													if (!empty($detail_data->image1)){
													?>
														<div class="admin_img_container" style="float: right; margin-left: 10px;">
															<image src="<?=site_config::get_value('upload_thumbs_folder').$detail_data->image1?>" style="height: 70px;"/>
														</div>
														<div style="float: right;">
															<button type="button" style="margin-bottom: 20px;" class="delete_image_btn" data-image_name="image1">
																מחק תמונה
															</button>
															<input type="file" id="image1" name="image1" />
														</div>
														<div class="clearfix"></div>
													<?
													}
													else{
													?>
													<input type="file" id="image1" name="image1" />
													<?
													}
													?>
												</div>
												<div class="form-group">
													<div>
														<label for="image">תמונה 2</label>
													</div>
													<?
													if (!empty($detail_data->image2)){
													?>
														<div class="admin_img_container" style="float: right; margin-left: 10px;">
															<image src="<?=site_config::get_value('upload_thumbs_folder').$detail_data->image2?>" style="height: 70px;"/>
														</div>
														<div style="float: right;">
															<button type="button" style="margin-bottom: 20px;" class="delete_image_btn" data-image_name="image2">
																מחק תמונה
															</button>
															<input type="file" id="image2" name="image2" />
														</div>
														<div class="clearfix"></div>
													<?
													}
													else{
													?>
													<input type="file" id="image2" name="image2" />
													<?
													}
													?>
												</div>
												<div class="form-group">
													<div>
														<label for="image">תמונה 3</label>
													</div>
													<?
													if (!empty($detail_data->image3)){
													?>
														<div class="admin_img_container" style="float: right; margin-left: 10px;">
															<image src="<?=site_config::get_value('upload_thumbs_folder').$detail_data->image3?>" style="height: 70px;"/>
														</div>
														<div style="float: right;">
															<button type="button" style="margin-bottom: 20px;" class="delete_image_btn" data-image_name="image3">
																מחק תמונה
															</button>
															<input type="file" id="image3" name="image3" />
														</div>
														<div class="clearfix"></div>
													<?
													}
													else{
													?>
													<input type="file" id="image3" name="image3" />
													<?
													}
													?>
												</div>
												<div class="form-group">
													<div>
														<label for="image">תמונה 4</label>
													</div>
													<?
													if (!empty($detail_data->image4)){
													?>
														<div class="admin_img_container" style="float: right; margin-left: 10px;">
															<image src="<?=site_config::get_value('upload_thumbs_folder').$detail_data->image4?>" style="height: 70px;"/>
														</div>
														<div style="float: right;">
															<button type="button" style="margin-bottom: 20px;" class="delete_image_btn" data-image_name="image4">
																מחק תמונה
															</button>
															<input type="file" id="image4" name="image4" />
														</div>
														<div class="clearfix"></div>
													<?
													}
													else{
													?>
													<input type="file" id="image4" name="image4" />
													<?
													}
													?>
												</div>
												<div class="form-group">
													<div>
														<label for="image">תמונה 5</label>
													</div>
													<?
													if (!empty($detail_data->image5)){
													?>
														<div class="admin_img_container" style="float: right; margin-left: 10px;">
															<image src="<?=site_config::get_value('upload_thumbs_folder').$detail_data->image5?>" style="height: 70px;"/>
														</div>
														<div style="float: right;">
															<button type="button" style="margin-bottom: 20px;" class="delete_image_btn" data-image_name="image5">
																מחק תמונה
															</button>
															<input type="file" id="image5" name="image5" />
														</div>
														<div class="clearfix"></div>
													<?
													}
													else{
													?>
													<input type="file" id="image5" name="image5" />
													<?
													}
													?>
												</div>
												<div class="form-group">
													<div>
														<label for="image">תמונה 6</label>
													</div>
													<?
													if (!empty($detail_data->image6)){
													?>
														<div class="admin_img_container" style="float: right; margin-left: 10px;">
															<image src="<?=site_config::get_value('upload_thumbs_folder').$detail_data->image6?>" style="height: 70px;"/>
														</div>
														<div style="float: right;">
															<button type="button" style="margin-bottom: 20px;" class="delete_image_btn" data-image_name="image6">
																מחק תמונה
															</button>
															<input type="file" id="image3" name="image6" />
														</div>
														<div class="clearfix"></div>
													<?
													}
													else{
													?>
													<input type="file" id="image6" name="image6" />
													<?
													}
													?>
												</div>
											</div>
											<div class="clearfix"></div>
											<!--<div class="col-md-12">
												<div>
													<label>קשור למוצר</label>
													<select name="linked_prod" required>
														<option value="0">בחר מוצר</option>
														<?
														$prods = $this->data->product->get_all();
														foreach($prods as $prod){
															?>
															<option value="<?=$prod->id?>" <?=$linked_prod==$prod->id?'selected':''?>><?=$prod->name?></option>
															<?
														}
														?>
													</select>
												</div>
											</div>-->
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<?
											if (!empty($linked_options)){
												?>
												<hr style="border-color: #aaa;"/>
												<h3>
													אפשרויות קשורות
												</h3>
												<div>
												<?
												foreach ($linked_options as $linked_option){
													$linked_details = $linked_option->get_details();
													?>
													<div class="col-md-6">
														<h4>
															<?=$linked_option->name?>
														</h4>
													<?
													foreach ($linked_details as $linked_detail){
														$linked_details_data = $this->data->details2details->get_linked_detail_data($linked_detail->id, $detail->id, (int)$_GET['prod_id']);

														$added_price = $linked_detail->added_price;
														if (!empty($linked_details_data->price))
															$added_price = $linked_details_data->price;
														?>
														<div>
															<label>
																<input type="checkbox" name="linked_detail_id[]" value="<?=$linked_detail->id?>" <?=(!empty($linked_details_data)?"checked":"")?>>
																<?=$linked_detail->name?>
															</label>
															<label>
																תוספת מחיר: <input type="text" name="linked_price[<?=$linked_detail->id?>]" value="<?=$added_price?>" style="width: 60px;"/>
															</label>
														</div>
														<?
													}
													?>
													</div>
													<?
												}
												?>
												</div>
												<div class="clearfix"></div>
												<?
											}
											?>
										</div>
									</form>
								</div>
							<?
								}
							?>
                            </div>
						</div>
						<?
						}
					?>
						<div class="clearfix"></div>
						<hr style="border-color: #aaa;"/>
					<button type="button" id="submit_btn" class="btn btn-default" style="margin-top: 25px;">
						שמור
					</button>

				</div>
			</div>
		</div>
	</div>
</div>
