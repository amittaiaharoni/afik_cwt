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
				{ "data": "BusinessName" , "sClass": "editable"},
				// phone
				{ "data": "NumberID" , "sClass": "editable"},
				// phone2
				{ "data": "CustomerName" , "sClass": "editable"},
				<?/*
				{ "data": "company_name" , "sClass": "editable"}
				*/?>
				
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
			"aLengthMenu": [[20, 50, 100 , -1], [20, 50, 100, "All"]],
			"iDisplayLength" : 20,
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
			  url: "<?=$this->page_path?>&action=save_data",
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
		
		// oTable.fnSetColumnVis( 0, false ); // hide "id" column
		
		//Delete event
		$('#grid_wrapper').on('click', 'a.grid_delete', function (e) {  
			e.preventDefault(); //prevent loop back			
		});				
		
		// details row defenition
		
		// details row open event
		$('#grid_wrapper').on( 'click', ".details_img", function () {
			var nTr = $(this).parents('tr')[0];			
		} );				
	} );
</script>

<div id="page_wraper" class="container-fluid">	
	<div class="page-header">
        <h2>לקוחות</h2>
    </div>
	<div id="" class="col-md-8">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">רשימת לקוחות</h3>
			</div>
			<div class="panel-body">
				<div class="table">
					<div class="add_delete_toolbar" ></div>
					<div class="spacer" style="height: 15px;"></div>
					<?/*
					<div style="text-align: right; padding-bottom: 5px;">
						מתאריך: <input type="text" class="datepicker" id="from_date" />
						עד תאריך: <input type="text" class="datepicker" id="to_date" />
					</div>
					*/?>
					<div id="grid_wrapper">
						<table id="data_grid" class="display" cellspacing="0" cellpadding="0">
							<thead>
								<tr>				
									<th>לפרטים</th>
									<th>מספר רשומה</th>
									<th>שם</th>
									<th>מק"ק</th>
									<th>מחיר</th>
									<th>פעולות</th>
								</tr>
							</thead>
							 <tfoot>
								<tr>
									<th>לפרטים</th>
									<th>מספר רשומה</th>
									<th>שם</th>
									<th>מק"ק</th>
									<th>מחיר</th>
									<th>פעולות</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="" class="col-md-4">
		<div class="hide">
			<?////////		form used for row deletion 	//////////////?>
			<form id="delete_form" actin="" method="post">
				<input type="hidden" name="table_name" value="table_name"/>
				<input type="hidden" name="row_id" value=""/>
				<input type="hidden" name="action" value="delete"/>
			</form>
			
			<?////////		detail rows html 	//////////////?>
			<div id="row_details">			
						<table class="details_table_id" cellpadding="5" cellspacing="0" border="0" style="background: white; width: 100%;">';
							<tr>
								<td>שם:</td><td>1</td>							
							</tr>
							<tr>
								<td>כותרת:</td><td>2</td>							
							</tr>
							<tr>
								<td>מוצג:</td><td>3</td>							
							</tr>
							<tr>
								<td>טקסט:</td><td>4</td>							
							</tr>
						</table>				
			</div>
		</div>
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
						<?/*
						<div class="form-group" >
							<label for="title">כותרת</label>
							<input type="text" class="form-control" id="title" name="title" placeholder="כותרת" value="<?=$this->get_edited_data("title")?>">
						</div>
						<div class="form-group" >
							<label for="display">להציג</label>
							<select class="form-control" name="display">
								<option value="1" <?=$this->is_selected("display",1)?>>כן</option>
								<option value="0" <?=$this->is_selected("display",0)?>>לא</option>
							</select>						
						</div>
						*/?>
						<div style="clear: both;"></div>
						<?/*
						<div class="form-group">
							<label for="text">טקסט</label>
							<textarea class="form-control editor" id="text" name="text" placeholder="text"><?=$this->get_edited_data("text")?></textarea>
						</div>
						*/?>
						<div style="clear: both;"></div>
						<input type="hidden" name="action" value="<?=(empty($this->edited_object)?"insert_data":"edit_data")?>"/>		
						<input type="hidden" name="update_id" id="update_id" value="<?=$this->get_edited_data("id")?>"/>
						<button type="submit" class="btn btn-default" style="margin-top: 25px;">
							שמור
						</button>
					</form>				
				</div>
			</div>
		</div>	
	</div>	
</div>