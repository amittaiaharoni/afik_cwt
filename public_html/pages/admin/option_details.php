<?
	$option_name = "";
	if (!empty($_GET['opt_id'])){
		$opt = $this->data->option->get_by_id((int)$_GET['opt_id']);
		if (!empty($opt)){
			$option_name = $opt->option_name;
		}
	}
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
				// parent_cat
				{
					"data": "image",
					"render": function ( data, type, full, meta ) {
					  return 	'<img src="<?=site_config::get_value('upload_thumbs_folder')?>'+data+'" style="max-width: 60px;"/>';
					}
				},
				{
					"data": "id" ,
					"render": function ( data, type, full, meta ) {
					  return 	'<a href="#" data-opt_det_id='+data+' class="connect_to_all">' +
									'קשור לכל המוצרים'+
								'</a> ';
					}
				},

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

		$("#page_wraper").on("click", ".connect_to_all",function(e){
			e.preventDefault();
			var option_det_id = $(this).data("opt_det_id");
			$.ajax({
				type: "POST",
				data: { action: "admin_connect_option_det_to_prods", opt_det_id: option_det_id }
			}).done(function( msg ) {

			});
		});
	});
</script>

<div id="page_wraper" class="container-fluid" style="position: relative;">

	<div class="text-center col-md-4 col-md-offset-4" style="position: absolute; left: 0; top: -20px; z-index: 1000; background: white; border: 1px solid #333; border-radius: 7px; padding: 10px;">
		<div id="search_form_container" class="form well" style="display: none;">
			<form id="search_form" action="" method="post" role="form">
				<div class="form-group" >

				</div>
				<input type="submit" value="חפש"/>
			</form>
		</div>
		<div class="clearfix"></div>
		<button type="button" id="show_search" style="width: 50%; font-size: 1.2em;">חיפוש מתקדם</button>
	</div>

	<div class="page-header">
        <h2>פרטים עבור אפשרות מוצר - <?=$option_name?></h2>
    </div>
	<div id="" class="col-md-4">
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
							<label for="name">שם</label>
							<input type="text" class="form-control" id="name" name="name" placeholder="שם" value="<?=$this->get_edited_data("name")?>">
						</div>
						<div class="form-group" >
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
								<div style="float: right; margin-bottom: 20px;">
									<button type="button" class="delete_image_btn" data-image_name="image">
										מחק תמונה
									</button>
								</div>
							<?
							}
							?>
							<input type="file" id="image" name="image" />
						</div>
						<div class="form-group" >
							<label for="added_price">תוספת מחיר</label>
							<input type="text" class="form-control" id="added_price" name="added_price" placeholder="תוספת מחיר" value="<?=$this->get_edited_data("added_price")?>">
						</div>
						<div style="clear: both;"></div>
						<input type="hidden" name="option_id" value="<?=(int)$_GET["opt_id"]?>"/>
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
	<div id="" class="col-md-8">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">רשימת תמונות</h3>
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
									<th>תמונה</th>
									<th></th>
									<th>פעולות</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>לפרטים</th>
									<th>מספר רשומה</th>
									<th>שם</th>
									<th>תמונה</th>
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
