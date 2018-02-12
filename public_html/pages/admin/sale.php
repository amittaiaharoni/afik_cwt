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
			"serverSide": true,
			"ajax": "<?=$this->page_path?>&action=get_dt_data",
			"columns": [ 
				// details button
				{
					"data": "id",
					"render": function ( data, type, full, meta ) {
					  return '<a href="#" data-row_id="'+data+'"><img class="details_img" src="css/datatable/images/details_open.png"/></a>';
					}
				},
////////////////////////////////////////////////////////////////////////		
/////////////////			FIELDS DEFINITION	
			
				// id
				{ "data": "id" },
				// name
				{ "data": "name" , "sClass": "editable"},
				// sale to category
				{ "data": "sale_to_cat" },
				// price
				{ "data": "price", "sClass": "editable"},
				// is sale active
				{ "data": "is_active"},
////////////////////////////////////////////////////////////////////////				
				// action buttons
				{
					"data": "id",
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

				var cell = $(this).parent();
				var row = cell.parent();
				var row_id = oTable.row(row).data().id;
				console.log(row_id);
				
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
		
		editableBlocks = $('.editor');
		for (var i = 0; i < editableBlocks.length; i++) {
			CKEDITOR.replace(editableBlocks[i]);
		}	
	});
</script>

<div id="page_wraper" class="container-fluid" style="position: relative;">

	<div class="text-center col-md-4 col-md-offset-4" style="position: absolute; left: 0; top: -20px; z-index: 1000; background: white; border: 1px solid #333; border-radius: 7px; padding: 10px;">
		<div id="search_form_container" class="form well" style="display: none;">
			<form id="search_form" action="" method="post" role="form">
				<div class="form-group" >
					<label for="parent_cat_id">קטגוריית אב</label>
					<select name="parent_cat_id" class="form-control">
						<option value="0">בחר קטגוריית אב</option>
						<?
						$cats = $this->data->category->get_all();
						if (!emptY($cats)){
							foreach($cats as $cat){
								if ($cat->id == $this->get_edited_data("id"))
									continue;
									
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
        <h2>מבצעים</h2>
    </div>	
	<div id="" class="col-md-7" style="width: 50%;">
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
						</div>

						<div style="clear: both;"></div>
						
						<div class="form-group" >
							<label for="sale_to_cat">קטגוריה</label>
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
						<div style="clear: both;"></div>
						<div class="form-group" >
							<div class="col-md-12">
								<label for="buy_from">קנה ב-</label>
								<input type="text" class="form-control" id="buy_from" name="buy_from" placeholder="קנה ב-" value="<?=$this->get_edited_data("buy_from")?>">
							</div>
						</div>
						<div style="clear: both;"></div>
						<!--
						<div class="form-group" >
							<label for="receive_prod">מוצר במתנה</label>
							<select name="receive_prod" class="form-control">
								<?
								$prods = $this->data->product->get_all();
								if (!emptY($prods)){
									foreach($prods as $prod){
									?>
									<option value="<?=$prod->id?>" <?=$this->get_edited_data("receive_prod")==$prod->id?'selected':''?>><?=$prod->name?></option>
									<?
									}
								}
								?>
							</select>
						</div>
						-->
						<hr style="border-color: #aaa;"/>
						<div style="clear: both;"></div>
						<div class="form-group" >
							<label for="quantity">מחיר הנחה</label>
							<input type="text" class="form-control" id="price" name="price" placeholder="כמות" value="<?=$this->get_edited_data("price")?>">
						</div>	
						
						<hr style="border-color: #aaa;"/>
						<div style="clear: both;"></div>
						<div class="form-group" >
							<div class="checkbox">
								<label>
									<input type='hidden' value='0' name='is_active'>
									<input type="checkbox" name="is_active" value="1" <?=$this->is_checked("is_active",1,0)?>> 
									מופעל
								</label>
							</div>
						</div>
						<hr style="border-color: #aaa;"/>
											
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
	<div id="" class="col-md-6">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">רשימת קטגוריות</h3>
			</div>
			<div class="panel-body">
				<div class="table">
					<div class="add_delete_toolbar" ></div>
					<div class="spacer" style="height: 15px;"></div>
					<div id="grid_wrapper">
						<table id="data_grid" class="display" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<th>לפרטים</th>
									<th>מספר רשומה</th>
									<th>שם</th>
									<th>קטגוריה</th>
									<th>מחיר הנחה</th>
									<th>מופעל</th>
									
									<th>פעולות</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>לפרטים</th>
									<th>מספר רשומה</th>
									<th>שם</th>
									<th>קטגוריה</th>
									<th>מחיר הנחה</th>
									<th>מופעל</th>
									
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