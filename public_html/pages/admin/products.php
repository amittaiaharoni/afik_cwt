<?

?>
<script type="text/javascript" charset="utf-8">
	function UrlExists(url)	{
		var http = new XMLHttpRequest();
		http.open('HEAD', url, false);
		http.send();
		return http.status!=404;
	}

	$(document).ready( function () {

////////////////////////////////////////////////////////////////////////
//		TABLE DEFINITION
		// search fields
		$('#data_grid').on('preXhr.dt', function ( e, settings, data ) {
			if ($("#search_form"))
				data.custom_search = $("#search_form").serializeArray();
			// console.log(data);
		});

		var oTable = $('#data_grid').DataTable({
			"pagingType": "listbox",
			"responsive": true,
			"serverSide": true,
			"order": [ 1, 'desc' ],
			"ajax": "<?=$this->page_path?>&action=get_dt_data",
			"columns": [
				// details button
        {
          "data": "id",
          "orderable": false,
          "render": function ( data, type, full, meta ) {
            //console.log(data);
            return '<a href="#" data-row_id="'+data+'"><img class="details_img" src="css/datatable/images/details_open.png"/></a>';
          }
        },
////////////////////////////////////////////////////////////////////////
/////////////////			FIELDS DEFINITION

				// id
				{ "data": "id" },
				// name
				{ "data": "name" , "sClass": "editable"},
				// barcode
				{ "data": "barcode" , "sClass": "editable"},
				// price
				{ "data": "price" },
				// options
				{
					"data": "id" ,
					"orderable": false,
					"render": function ( data, type, full, meta ) {
					  return 	'<a href="index.php?page=admin_products_to_options&prod_id='+data+'">תוספות</a> ';
					}
				},
				// Barcode
				{ 
					"data": "id" ,
					"orderable": false,
					"render": function ( data, type, full, meta ) {
					  return 	'<a href="index.php?page=admin_barcodes&prod_id='+data+'">מק"ט</a> ';
					}
				},
				// gallery
				{
					"data": "gallery_id",
					"orderable": false,
					"render":
						function ( data, type, full, meta ) {
							if (data)
								return 	'<a href="index.php?page=admin_galleryImage&gal_id='+data+'">גלריה</a> ';
							else
								return "";
						}
				},
////////////////////////////////////////////////////////////////////////
				// action buttons
				{
					"data": "id",
					"orderable": false,
					"render": function ( data, type, full, meta ) {
					  return 	'<a href="<?=$this->page_path?>&id='+data+'" class="grid_edit" id="edit_link_'+data+'">' +
									'<span class="glyphicon glyphicon-pencil"></span>'+
								'</a> '+
								'<a href="#" class="grid_delete">'+
									'<span class="glyphicon glyphicon-trash"></span>'+
								'</a>';
					}
				}
			],
			// "dom": '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>',
			"aLengthMenu": [[20, 50, 100 , -1], [20, 50, 100, "All"]],
			"iDisplayLength" : 20,
			// "oLanguage": {
				// "sProcessing": "<img src='images/ajax_spinner.gif'/>"
			// }
			// "aaSorting": []
		});

////////////////////////////////////////////////////////////////////////
//		INLINE EDITING

		// create input field
		$('#data_grid').on( 'click', 'tbody td.editable:not(.now_editing)', function (e) {
			var cell = this;
			var col_idx = oTable.cell(cell).index().column;
			var cell_data_src = oTable.column( col_idx ).dataSrc();
			var row = $(this).parent();
			var row_id = oTable.row(row).data().id;

			// console.log( cell_data_src );
			// console.log( 'row id = ' + oTable.row($(this).parent()).data().id );

			var field_width = $(this).width();
			var text = $(this).text();

			$(this).text("");
			$(this).addClass("now_editing");
			$('<input type="text" name="'+cell_data_src+'__'+row_id+'" class="dt_edit_field" style="width:'+field_width+'px;">')
				.appendTo(this)
				.val(text)
				.focus();
		});

		// delete input field and sumit the data
		$("#data_grid").on("focusout" , "input.dt_edit_field", function(){
			var input = $(this);
			var text = input.val();
			var field_name = input.attr("name");

			$.ajax({
			  type: "POST",
			  url: "<?=$this->page_path?>&action=save_dt_data",
			  data: "edited_field="+field_name+"&edited_data="+ text,
			  success: function(){
				input.parent()
					.removeClass("now_editing")
					.text(text);
				input.remove();
			  }
			});

		});

//		END INLINE EDITING
////////////////////////////////////////////////////////////////////////

		$('#search_form').on( 'submit', function (e) {
			e.preventDefault();
			$("#search_form_container").slideToggle();
			oTable.draw();
		});

    // oTable.fnSetColumnVis( 0, false ); // hide "id" column

		//Delete event
		$('#grid_wrapper').on('click', 'a.grid_delete', function (e) {
			e.preventDefault(); //prevent loop back

			if (confirm("Confirm deletion?")) {

				/* var cell = $(this).parent();
				var row = cell.parent();
				var row_id = oTable.row(row).data(); */
				var row_id = $(this).parents('tr').prev().find('a').data('row_id');
				// var tr = $(this).parent('tr').prev();   
			   //get the real row index, even if the table is sorted
				// var index = DataTable.fnGetPosition(tr[0]);
				if($(this).parents('tr').attr('class') == 'child')
					var row_id = $(this).parents('tr').prev().find('a').data('row_id');
				else
					var row_id = $(this).parents('tr').find('a').data('row_id');
				// return false;
				$.ajax({
				  type: "POST",
				  url: "<?=$this->page_path?>&action=delete_dt_data",
				  data: "delete_id=" + row_id,
				  success: function(){
					oTable.draw();
				  }
				});
			}
		});

		// details row defenition

		// details row open event
		$('#grid_wrapper').on( 'click', ".details_img", function () {
			var nTr = $(this).parents('tr')[0];
		} );

		$('#show_search').on( 'click', function (e) {
			$("#search_form_container").slideToggle();

			if ( $("#search_form_container").is(":visible") ) {
				$("#search_form input:first").focus();
			}
		});

		$(document).mouseup(function (e){
			var container = $("#search_form_container");

			if (!container.is(e.target) // if the target of the click isn't the container...
				&& container.has(e.target).length === 0 // ... nor a descendant of the container
				&& e.target.id != 'show_search')
			{
				container.slideUp();
			}

			$("input[type=checkbox]").on("change", function(){
				if ($(this).is(":checked")){
					$(this).siblings("input[type=hidden]").attr("disabled","disabled");
				}
				else{
					$(this).siblings("input[type=hidden]").removeAttr("disabled");
				}
			});
		});

		$(".delete_image_btn").on("click", function(){
			var container = $(this).parent();
			var image_name = $(this).data("image_name");
			var row_update_id = $("#update_id").val();

			$.ajax({
				type: "POST",
				data: { action: "admin_delete_image", image: image_name, update_id: row_update_id }
			}).done(function( msg ) {
				container.siblings(".admin_img_container").remove();
				container.remove();
			});
		});

		$(".delete_file_btn").on("click", function(e){
			e.preventDefault();

			var this_link = $(this);
			var file_name = $(this).data("file_name");
			var row_update_id = $("#update_id").val();
			console.log(file_name);

			$.ajax({
				type: "POST",
				data: { action: "admin_delete_file", file: file_name, update_id: row_update_id }
			}).done(function( msg ) {
				this_link.siblings("a").remove();
				this_link.remove();
			});
		});

		editableBlocks = $('.editor');
		for (var i = 0; i < editableBlocks.length; i++) {
			CKEDITOR.replace(editableBlocks[i]);
		}

	});

    $(function(){
        var last_valid_selection = null;
        $('select[name="prods_ids[]"]').change(function(event) {
            if ($(this).val().length > 3) {
                alert('You can only choose 3!');
                $(this).val(last_valid_selection);

            } else {
                last_valid_selection = $(this).val();

            }

          });
    });
</script>

<div id="page_wraper" class="container-fluid" style="position: relative;">

	<div class="text-center col-md-4 col-md-offset-4" style="position: absolute; left: 0; top: -20px; z-index: 1000; background: white; border: 1px solid #333; border-radius: 7px; padding: 10px;">
		<div id="search_form_container" class="form well" style="display: none;">
			<form id="search_form" action="" method="post" role="form">
				<div class="form-group" >
					<label for="cat_id">קטגורייה</label>
					<select name="cat_id" class="form-control">
						<option value="0">בחר קטגורייה</option>
						<?
						$cats = $this->data->category->get_all();
						if (!emptY($cats)){
							foreach($cats as $cat){
								$cat_parent_path = $this->data->category->get_parent_cat_name($cat, true);
							?>
							<option value="<?=$cat->id?>"><?=$cat_parent_path." -> ".$cat->name?></option>
							<?
							}
						}
						?>
					</select>
				</div>
				<input type="submit" value="חפש"/>
			</form>
		</div>
		<div class="clearfix"></div>
		<button type="button" id="show_search" style="width: 50%; font-size: 1.2em;">חיפוש מתקדם</button>
	</div>

	<div class="page-header">
        <h2>מוצרים</h2>
    </div>
	<div id="" class="col-md-7">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title pull-right">
					<?
						if (!empty($this->edited_object)){
							echo "עריכה של רשומה מספר " . $this->get_edited_data("id");
						}
						else{
							echo "הוספת רשומה חדשה";
						}
					?>
				</h3>
				<button class="btn btn-xs pull-left">
					<a href="<?=$this->page_path?>">הוסף רשומה חדשה</a>
				</button>
				<div class="clearfix"></div>
			</div>
			<div class="panel-body">
				<div class="form">
					<form id="formAddEditNewRow" method="post" action="" enctype="multipart/form-data"  role="form">
						<div class="form-group" >
							<div class="col-md-8">
								<label for="name">שם</label>
								<input type="text" class="form-control" id="name" name="name" placeholder="שם" value="<?=$this->get_edited_data("name")?>">
							</div>
							<div class="col-md-4">
								<label for="barcode">מק"ט</label>
								<input type="text" class="form-control" id="barcode" name="barcode" placeholder="מק'ט" value="<?=$this->get_edited_data("barcode")?>">
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="form-group" >
							<label for="cat_ids">קשור לקטגוריות</label>
							<select multiple name="cat_ids[]" class="form-control" style="min-height: 200px;">
								<?
								$cats = $this->data->category->get_all();
								if (!emptY($cats)){
									foreach($cats as $cat){
										$cat_parent_path = $this->data->category->get_parent_cat_name($cat, true);
									?>
									<option value="<?=$cat->id?>" <?=$this->is_cat_selected($cat->id)?>><?=$cat_parent_path." -> ".$cat->name?></option>
									<?
									}
								}
								?>
							</select>
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group" >
							<label for="prods_ids">קשור למוצר</label>
							<select multiple name="prods_ids[]" class="form-control" style="min-height: 200px;">
								<?
								$prods = $this->data->product->get_all();
								if (!emptY($prods)){
									foreach($prods as $prod){
                                        if( empty($prod->id) || empty($prod->name) || $prod->id === $this->get_edited_data("id") )
                                            continue;
									?>
									<option value="<?=$prod->id?>" <?=$this->is_linked_prod_selected($prod->id)?>><?=$prod->name?></option>
									<?
									}
								}
								?>
							</select>
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group">
							<div class="col-md-4">
								<div class="checkbox">
									<label>
										<input type='hidden' value='0' name='is_for_sale'>
										<input type="checkbox" name="is_for_sale" value="1" <?=$this->is_checked("is_for_sale",1,1)?>>
										למכירה
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="checkbox">
									<label>
										<input type='hidden' value='0' name='in_stock'>
										<input type="checkbox" name="in_stock" value="1" <?=$this->is_checked("in_stock",1,1)?>>
										במלאי
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="checkbox">
									<label>
										<input type='hidden' value='0' name='is_sale'>
										<input type="checkbox" name="is_sale" value="1" <?=$this->is_checked("is_sale",1)?>>
										במבצע
									</label>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4">
								<div class="checkbox">
									<label>
										<input type='hidden' value='0' name='show_on_chosen'>
										<input type="checkbox" name="show_on_chosen" value="1" <?=$this->is_checked("show_on_chosen",1)?>>
										הצג במובחרים
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="checkbox">
									<label>
										<input type='hidden' value='0' name='hide_it'>
										<input type="checkbox" name="hide_it" value="1" <?=$this->is_checked("hide_it",1)?>>
										מוסתר
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="checkbox">
									<label>
										<input type='hidden' value='0' name='is_shipping'>
										<input type="checkbox" name="is_shipping" value="1" <?=$this->is_checked("is_shipping",1,1)?>>
										יש משלוח
									</label>
								</div>
							</div>
							<div class="col-md-4">
								<div class="checkbox">
									<label>
										<input type='hidden' value='0' name='show_on_top_menu'>
										<input type="checkbox" name="show_on_top_menu" value="1" <?=$this->is_checked("show_on_top_menu",1)?>>
										הצג בתפריט עליון
									</label>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="form-group" >
							<div class="col-md-4">
								<label for="stock_count">כמות במלאי</label>
								<input type="text" class="form-control" id="stock_count" name="stock_count" placeholder="כמות במלאי" value="<?=$this->get_edited_data("stock_count")?>">
							</div>
						</div>
						<div class="clearfix"></div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group" >
							<div class="col-md-4">
								<label for="price">מחיר</label>
								<input type="text" class="form-control" id="price" name="price" placeholder="מחיר" value="<?=$this->get_edited_data("price")?>">
							</div>
							<div class="col-md-4">
								<label for="price2">מחיר מחוק</label>
								<input type="text" class="form-control" id="price2" name="price2" placeholder="מחיר 2" value="<?=$this->get_edited_data("price2")?>">
							</div>
							<div class="col-md-4">
								<label for="price3">מחיר מקדמה</label>
								<input type="text" class="form-control" id="price3" name="price3" placeholder="מחיר 3" value="<?=$this->get_edited_data("price3")?>">
							</div>
							<div class="clearfix"></div>
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group" >
							<div class="col-md-6">
								<label for="shipping_added_price">תוספת מחיר למשלוח</label>
								<input type="text" class="form-control" id="shipping_added_price" name="shipping_added_price" placeholder="תוספת מחיר למשלוח" value="<?=$this->get_edited_data("shipping_added_price")?>">
							</div>
							<div class="col-md-6">
								<label for="weight">משקל</label>
								<input type="text" class="form-control" id="weight" name="weight" placeholder="משקל" value="<?=$this->get_edited_data("weight")?>">
							</div>
							<div class="clearfix"></div>
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group" >
							<div class="col-md-4">
								<div>
									<label for="image">תמונה</label>
								</div>
								<?
								$file_name = $this->get_edited_data("image");
								if (!empty($file_name)){
								?>
									<div class="admin_img_container" style="float: right; margin-left: 10px;">
										<image src="<?=site_config::get_value('upload_thumbs_folder').$file_name?>" style="height: 70px;"/>
									</div>
									<div style="float: right;">
										<button type="button" style="margin-bottom: 20px;" class="delete_image_btn" data-image_name="image">
											מחק תמונה
										</button>
										<input type="file" id="image" name="image" />
									</div>
									<div class="clearfix"></div>
								<?
								}
								else{
								?>
								<input type="file" id="image" name="image" />
								<?
								}
								?>
							</div>
							<div class="col-md-4">
								<div>
									<label for="image">תמונה 2</label>
								</div>
								<?
								$file_name = $this->get_edited_data("image2");
								if (!empty($file_name)){
								?>
									<div class="admin_img_container" style="float: right; margin-left: 10px;">
										<image src="<?=site_config::get_value('upload_thumbs_folder').$file_name?>" style="height: 70px;"/>
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
							<div class="col-md-4">
								<div>
									<label for="image">תמונה 3</label>
								</div>
								<?
								$file_name = $this->get_edited_data("image3");
								if (!empty($file_name)){
								?>
									<div class="admin_img_container" style="float: right; margin-left: 10px;">
										<image src="<?=site_config::get_value('upload_thumbs_folder').$file_name?>" style="height: 70px;"/>
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
							<div class="clearfix"></div>
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group" >
							<div class="col-md-4">
								<div>
									<label for="attch_file_path1">קובץ מצורף 1</label>
								</div>
								<?
								$file_name = $this->get_edited_data("attch_file_path1");
								if (!empty($file_name)){
								?>
									<a href="<?=site_config::get_value('upload_files_folder').$file_name?>"><i class="fa fa-fw fa-table"></i>להורדה</a>
									<a href="#" class="delete_file_btn" data-file_name="attch_file_path1">מחק קובץ</a>
								<?
								}
								?>
								<input type="text" class="form-control" id="attch_file_name1" name="attch_file_name1" placeholder="שם קובץ" value="<?=$this->get_edited_data("attch_file_name1")?>">
								<br/>
								<input type="file" id="attch_file_path1" name="attch_file_path1" />
							</div>
							<div class="col-md-4">
								<div>
									<label for="attch_file_path1">קובץ מצורף 2</label>
								</div>
								<?
								$file_name = $this->get_edited_data("attch_file_path2");
								if (!empty($file_name)){
								?>
									<a href="<?=site_config::get_value('upload_files_folder').$file_name?>"><i class="fa fa-fw fa-table"></i>להורדה</a>
									<a href="#" class="delete_file_btn" data-file_name="attch_file_path2">מחק קובץ</a>
								<?
								}
								?>
								<input type="text" class="form-control" id="attch_file_name2" name="attch_file_name2" placeholder="שם קובץ" value="<?=$this->get_edited_data("attch_file_name2")?>">
								<br/>
								<input type="file" id="attch_file_path2" name="attch_file_path2" />
							</div>
							<div class="col-md-4">
								<div>
									<label for="attch_file_path1">קובץ מצורף 3</label>
								</div>
								<?
								$file_name = $this->get_edited_data("attch_file_path3");
								if (!empty($file_name)){
								?>
									<a href="<?=site_config::get_value('upload_files_folder').$file_name?>"><i class="fa fa-fw fa-table"></i>להורדה</a>
									<a href="#" class="delete_file_btn" data-file_name="attch_file_path3">מחק קובץ</a>
								<?
								}
								?>
								<input type="text" class="form-control" id="attch_file_name3" name="attch_file_name3" placeholder="שם קובץ" value="<?=$this->get_edited_data("attch_file_name3")?>">
								<br/>
								<input type="file" id="attch_file_path3" name="attch_file_path3" />
							</div>
							<div class="clearfix"></div>
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group">
							<label for="comments_placeholder">טקסט להערות</label>
							<input type="text" class="form-control" id="comments_placeholder" name="comments_placeholder" placeholder="טקסט להערות" value="<?=$this->get_edited_data("comments_placeholder")?>">
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group" >
							<label for="desc">תאור</label>
							<textarea name="desc" id="desc" class="form-control editor"><?=$this->get_edited_data("desc")?></textarea>
						</div>
						<div class="form-group" >
							<label for="text">טקסט</label>
							<textarea name="text" id="text" class="form-control editor"><?=$this->get_edited_data("text")?></textarea>
						</div>
						<hr style="border-color: #aaa;"/>
						<div class="form-group" >
							<label for="meta_title">meta title</label>
							<input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="meta title" value="<?=$this->get_edited_data("meta_title")?>">
						</div>
						<div class="form-group" >
							<label for="meta_description">meta description</label>
							<input type="text" class="form-control" id="meta_description" name="meta_description" placeholder="meta description" value="<?=$this->get_edited_data("meta_description")?>">
						</div>
						<div class="form-group" >
							<label for="meta_keywords">meta keywords</label>
							<input type="text" class="form-control" id="meta_keywords" name="meta_keywords" placeholder="meta title" value="<?=$this->get_edited_data("meta_keywords")?>">
						</div>
						<div class="form-group" >
							<label for="display_order">סדר תצוגה</label>
							<input type="text" class="form-control" id="display_order" name="display_order" placeholder="סדר תצוגה" value="<?=$this->get_edited_data("display_order")?>">
						</div>
						<div style="clear: both;"></div>
						<input type="hidden" name="action" value="<?=(empty($this->edited_object)?"admin_insert_data":"admin_edit_data")?>"/>
						<input type="hidden" name="update_id" id="update_id" value="<?=$this->get_edited_data("id")?>"/>
						<button type="submit" class="btn btn-default" style="margin-top: 25px;">
							שמור
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="" class="col-md-5">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">רשימת מוצרים</h3>
			</div>
			<div class="panel-body">
				<div class="table">
					<div class="add_delete_toolbar" ></div>
					<div class="spacer" style="height: 15px;"></div>
					<div id="grid_wrapper">
						<table id="data_grid" width="100%" class="display" cellspacing="0" >
							<thead>
								<tr>
									<th></th>
									<th>ID</th>
									<th>שם</th>
									<th>מקט</th>
									<th>מחיר</th>
									<th></th>
									<th></th>
									<th></th>
									<th>פעולות</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th></th>
									<th>ID</th>
									<th>שם</th>
									<th>מקט</th>
									<th>מחיר</th>
									<th></th>
									<th></th>
									<th></th>
									<th>פעולות</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
