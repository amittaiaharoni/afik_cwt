<?
if (isset($_POST['save'])){
	// error_log(print_r($_POST,true));
	$con = db_con::get_con();
}
?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$(".delete_image_btn").on("click", function(){
			var container = $(this).parent();
			var image_name = $(this).data("image_name");
			var row_update_id = $("#update_id").val();

			$.ajax({
				type: "POST",
				data: { action: "admin_config_delete_image", key: image_name }
			}).done(function( msg ) {
				container.siblings(".admin_img_container").remove();
				container.remove();
			});
		});
	});
</script>

<div id="page_wraper" class="container-fluid" style="position: relative;">
	<div class="page-header">
        <h2>הגדרות אתר</h2>
    </div>
	<div id="" class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">

			</div>
			<div class="panel-body">
				<div class="form">
					<form id="formAddEditNewRow" method="post" action="" enctype="multipart/form-data"  role="form">
						<?
						$fields = site_config::get_all();
						if (!empty($fields)){
							$fields_in_row = 4;
							$fields_count = 0;
							foreach ($fields as $field){
								if ($field['type'] == "text"){
									?>
									<div class="form-group col-md-3" >
										<label for="<?=$field['key']?>"><?=$field['name']?></label>
										<div><?=$field['desc']?></div>
										<input type="text" class="form-control" id="<?=$field['key']?>" name="<?=$field['key']?>" placeholder="<?=$field['name']?>" value="<?=$field['value']?>">
									</div>
									<?
								}
								else if ($field['type'] == "bool"){
									?>
									<div class="form-group col-md-3" >
										<label for="<?=$field['key']?>"><?=$field['name']?></label>
										<div><?=$field['desc']?></div>
										<select class="form-control" id="<?=$field['key']?>" name="<?=$field['key']?>">
											<option value="1" <?=($field['value']?"selected":"")?>>כן</option>
											<option value="0" <?=($field['value']?"":"selected")?>>לא</option>
										</select>
									</div>
									<?
								}
								else if ($field['type'] == "file"){
								?>
									<div class="form-group col-md-3" >
										<div>
											<label for="<?=$field['key']?>"><?=$field['name']?></label>
											<div><?=$field['desc']?></div>
										</div>
										<?
										$file_name = $field['value'];
										if (!empty($file_name)){
										?>
											<div class="admin_img_container" style="float: right; margin-left: 10px;">
												<image src="<?=site_config::get_value('upload_thumbs_folder').$file_name?>" style="max-height: 70px; max-width: 120px;"/>
											</div>
											<div style="float: right; margin-bottom: 20px;">
												<button type="button" class="delete_image_btn" data-image_name="<?=$field['key']?>">
													מחק תמונה
												</button>
											</div>
										<?
										}
										?>
										<input type="file" id="<?=$field['key']?>" name="<?=$field['key']?>" />
									</div>
								<?
								}

								$fields_count++;
								if ($fields_count == $fields_in_row){
									$fields_count = 0;
									?>
									<div style="clear: both;"></div>
									<hr style="border-color: #aaa;"/>
									<?
								}
							}
						}
						?>
						<div style="clear: both;"></div>
						<button type="submit" class="btn btn-default" style="margin-top: 25px;">
							שמור
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
