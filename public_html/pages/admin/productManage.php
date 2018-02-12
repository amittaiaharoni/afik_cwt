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
			"dom": '<"top"flp<"clear">>rt<"bottom"fp<"clear">><"info"i>',
			"pagingType": "listbox",
			"serverSide": true,
			"order": [ 1, 'desc' ],
			"ajax": "index.php?page=admin_productManage&action=get_dt_data",
			"columns": [
				// details button
				{
					"data": "id",
					"orderable": false,
					"render": function ( data, type, full, meta ) {
					  return '<a href="#" data-row_id="'+data+'"><img class="details_img" src="css/datatable/images/details_open.png"/></a>';
					}
				},
////////////////////////////////////////////////////////////////////////
/////////////////			FIELDS DEFINITION


				// id
				{ "data": "id" },
				// id
				{ "data": "image",
					"render": function ( data, type, row ) {
						return '<a href="upload/images/'+data+'" class="fancybox" rel="gal"><img src="upload/images/'+data+'" style="max-height: 70px; max-width:100px;"/></a>';
					}
				},
				// name
				{ "data": "name" , "sClass": "editable"},
				// makat
				{ "data": "barcode" , "sClass": "editable"},
				// price
				{ "data": "price" , "sClass": "editable" },
				{ "data": "price2" , "sClass": "editable" },
				{ "data": "hide_it" , "sClass": "editable_checkbox" ,
					"render": function ( data, type, row ) {
								var name = "hide_it"+'__'+row.id;
								var checked = "";

								if(data!=0)
									checked = "checked";

								return '<input name="'+name+'" type="checkbox" '+checked+'>';
					}

				},
				{ "data": "in_stock" , "sClass": "editable_checkbox",
					"render": function ( data, type, row ) {
							var name = "in_stock"+'__'+row.id;
							var checked = "";

							if(data!=0)
								checked = "checked";

							return '<input name="'+name+'" type="checkbox" '+checked+'>';
					}
				},
				{ "data": "is_sale" , "sClass": "editable_checkbox",
					"render": function ( data, type, row ) {
							var name = "is_sale"+'__'+row.id;
							var checked = "";

							if(data!=0)
								checked = "checked";

							return '<input name="'+name+'" type="checkbox" '+checked+'>';
					}
				},
				{"data": "display_order" , "sClass": "editable"},
				// options
				{
					"data": "id" ,
					"orderable": false,
					"render": function ( data, type, full, meta ) {
					  return 	'<a target="_blank" href="index.php?page=admin_products_to_options&prod_id='+data+'">תוספות</a> ';
					}
				},


////////////////////////////////////////////////////////////////////////
				// action buttons
				{
					"data": "id",
					"orderable": false,
					"render": function ( data, type, full, meta ) {
					  return 	'<a target="_blank" href="index.php?page=admin_products&id='+data+'" class="grid_edit" id="edit_link_'+data+'">' +
									'<span class="glyphicon glyphicon-pencil"></span>'+
								'</a> ';
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
			  url: "index.php?page=admin_productManage&action=save_dt_data",
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

////////////////////////////////////////////////////////////////////////
//		INLINE EDITING	CHECKBOX

		//delete input field and sumit the data
		$("#data_grid").on("change" , "input[type=checkbox]", function(){

			var input = $(this);
			var checked = input.is(":checked");
			var edited_data = "false";
			if (checked)
				edited_data = "1";
			var field_name = input.attr("name");

			$.ajax({
			  type: "POST",
			  url: "index.php?page=admin_productManage&action=save_dt_data",
			  data: "edited_field="+field_name+"&edited_data="+edited_data,
			  success: function(){



			  }
			});

		});

//		END INLINE EDITING	CHECKBOX
////////////////////////////////////////////////////////////////////////

		$('#search_form').on( 'submit', function (e) {
			e.preventDefault();
			$("#search_form_container").slideToggle();
			oTable.draw();
		});

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
	<div id="" class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">רשימת מוצרים</h3>
			</div>
			<div class="panel-body">
				<div class="table">
					<div class="add_delete_toolbar" ></div>
					<div class="spacer" style="height: 15px;"></div>
					<div id="grid_wrapper">
						<table id="data_grid" class="display" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<th></th>
									<th>ID</th>
									<th></th>
									<th>שם</th>
									<th>מקט</th>
									<th>מחיר</th>
									<th>מחיר מחוק</th>
									<th>מוסתר</th>
									<th>במלאי</th>
									<th>במבצע</th>
									<th>סדר תצוגה</th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th></th>
									<th>ID</th>
									<th></th>
									<th>שם</th>
									<th>מקט</th>
									<th>מחיר</th>
									<th>מחיר מחוק</th>
									<th>מוסתר</th>
									<th>במלאי</th>
									<th>במבצע</th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
