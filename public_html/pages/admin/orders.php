<?
$cart = unserialize(base64_decode($this->get_edited_data("serialized_cart")));
// $order = unserialize(base64_decode($this->get_edited_data("serialized_order")));
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
            "stateSave": true,
			"columns": [

////////////////////////////////////////////////////////////////////////
/////////////////			FIELDS DEFINITION
				{ "data": "id"},
				// name
				{ "data": "name"},
				// final_price
				{ "data": "final_price"},
				// phone
				{ "data": "phone"},
				// created_date
				{ "data": "created_date"},
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
			 "order": [[ 3, "desc" ]]
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

		$(".sendtrakingcodeemail").on("click", function(){
			tcode = $('.trakingcodeis').val();
			temailis = $(".emailit").val();
			$.ajax({url: 'trackcodeajax.php?theemailis='+temailis+'&tcode='+tcode, success: function(result){
			}});
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
        <h2>
			הזמנות
			<?
			if (!empty($_GET['user_id'])){
				$user = $this->data->user->get_by_id($_GET['user_id']);
				if (!emptY($user)){
					echo " של " . $user->first_name . " " . $user->last_name;
				}
			}
			?>
		</h2>
    </div>
	<div id="" class="col-lg-7">
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
						<div class="form-group col-md-4" >
							<label for="tracking_code">קוד מעקב</label>
							<input type="text" class="form-control trakingcodeis" id="tracking_code" name="tracking_code" placeholder="קוד מעקב" value="<?=$this->get_edited_data("tracking_code")?>">
						</div>
						<div class="form-group col-md-4" >
							<button type="submit" class="btn btn-default sendtrakingcodeemail" style="margin-top: 25px;">
								שמור
							</button>
						</div>
						<div class="clearfix"></div>
						<hr style="border: 1px solid #aaa;"/>
						<div>
							<?
								$payment_type = $this->get_edited_data("payment_type");
								$payment_type_text = paymentType::get_display_text($payment_type);
								if (emptY($payment_type_text))
									$payment_type_text = "לא שילם";
							?>
							<h2>
								אופן תשלום : <?=$payment_type_text?>
							</h2>
							<?
							if ($payment_type == paymentType::tranzila){
							?>
								<div class="col-md-4">
									<label>אינדקס טרנזילה: </label>
									<?=$this->get_edited_data("tranzila_index")?>
								</div>
								<div class="col-md-4">
									<label>שולם בפועל: </label>
									<?=$this->get_edited_data("tranzila_sum_paid")?>
								</div>
								<div class="col-md-4">
									<label>סטטוס: </label>
									<?=$this->get_edited_data("tranzila_status")?>
								</div>
								<div class="clearfix"></div>
							<?
							}
							else if ($payment_type == paymentType::credit_card){
							?>
								<div class="col-md-4">
									<label>סוג כרטיס: </label>
									<?=$this->get_edited_data("card_type")?>
								</div>
								<div class="col-md-4">
									<label>תוקף: </label>
									<?=($this->get_edited_data("expire_month")."/".$this->get_edited_data("expire_year"))?>
								</div>
								<div class="col-md-4">
									<label>מספר כרטיס: </label>
									<?=$this->get_edited_data("card_number")?>
								</div>
								<div class="clearfix"></div>

								<div class="col-md-4">
									<label>שם על כרטיס: </label>
									<?=$this->get_edited_data("card_owner_name")?>
								</div>
								<div class="col-md-4">
									<label>ת.ז: </label>
									<?=$this->get_edited_data("card_owner_tz")?>
								</div>
								<div class="col-md-4">
									<label>cvv: </label>
									<?=$this->get_edited_data("cvv")?>
								</div>
								<div class="clearfix"></div>
							<?
							}
							else if ($payment_type == paymentType::call_me){

							}
							else{

							}
							?>

						</div>
						<hr style="border: 1px solid #aaa;"/>
						<div>
							<h2>
								פרטי המזמין
							</h2>
							<?
								$user = $this->data->user->get_by_id($this->get_edited_data("user_id"));
								if (!empty($user)){
								?>
								<div class="col-md-4">
									<label>שם: </label>
									<?=($user->first_name . " " . $user->last_name)?>
								</div>
								<div class="col-md-4">
									<label>ת.ז: </label>
									<?=$user->tz?>
								</div>
								<div class="col-md-4">
									<label>טלפון: </label>
									<?=$user->phone?>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-4">
									<label>עיר: </label>
									<?=$user->city?>
								</div>
								<div class="col-md-4">
									<label>כתובת: </label>
									<?=$user->address?>
								</div>
								<div class="col-md-4">
									<label>email: </label>
									<?=$user->email?>
									<input class='emailit' hidden value="<?=$user->email?>"  />
								</div>
								<div class="clearfix"></div>
								<?
								}
							?>
						</div>
						<hr style="border: 1px solid #aaa;"/>
						<div>
							<h2>
								קבלה ב: <?=(empty($cart['shipping_price'])/* $this->get_edited_data("self_pickup") */?" איסוף עצמי":" משלוח")?>
							</h2>
							<?
							if (empty($cart['shipping_price'])/* $this->get_edited_data("self_pickup") */){
							?>
								<div class="col-md-4">
									<label>איסוף מסניף: </label>
									<?
										$branch = $this->data->branch->get_by_id($this->get_edited_data("pickup_location"));
										if (!empty($branch)){
											echo $branch->name;
										}
									?>
								</div>
								<div class="col-md-4">
									<label>בתאריך: </label>
									<?=$this->get_edited_data("pickup_date")?>
								</div>
							<?
							}
							else{
							?>
								<div class="col-md-4">
									<label>כתובת למשלוח: </label>
									<?=$cart['shipping_address']/* $this->get_edited_data("send_date") */?>
								</div>
							<?
							}
							?>
							<div class="clearfix"></div>
						</div>
						<?
						$order = unserialize(base64_decode($this->get_edited_data("serialized_order")));
						error_log(print_r($order,1));
							// if ($order->self_pickup){
						?>
						<hr style="border: 1px solid #aaa;"/>
						<div>
							<h2>
								פרטי מקבל ההזמנה
							</h2>

							<div class="col-md-4">
								<label>שם: </label>
								<?=$order['name']?>
							</div>
							<div class="col-md-4">
								<label>נייד: </label>
								<?=$order['mobile']?>
							</div>
							<div class="col-md-4">
								<label>טלפון: </label>
								<?=$order['phone']?>
							</div>
							<div class="col-md-4">
								<label>מייל: </label>
								<?=$order['email']?>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4">
								<label>עיר: </label>
								<?=$order['city']?>
							</div>
							<div class="col-md-4">
								<label>רחוב: </label>
								<?=$order['street']?>
							</div>
							<div class="col-md-4">
								<label>מס' בית: </label>
								<?=$order->recipient_house?>
							</div>
							<div class="clearfix"></div>

							<div class="col-md-4">
								<label>דירה: </label>
								<?=$order->apartment?>
							</div>

							<div class="clearfix"></div>
						</div>
						<?
						// }
						?>
						<hr style="border: 1px solid #aaa;"/>
						<div>
							<div class="col-md-12">
								<h2>הערות</h2>
								<?=$this->get_edited_data("notes")?>
							</div>
							<div class="clearfix"></div>
						</div>
						<?
						

						if (!empty($order) && !empty($order['raxe'])){
						?>
						<hr style="border: 1px solid #aaa;"/>
						<div>
							<h2>פרטים רפואים</h2>
							<div class="col-md-4">
								<label>rcph: </label>
								<?=$order["rcph"]?>
							</div>
							<div class="col-md-4">
								<label>rcyl: </label>
								<?=$order["rcyl"]?>
							</div>
							<div class="col-md-4">
								<label>raxe: </label>
								<?=$order["raxe"]?>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4">
								<label>lcph: </label>
								<?=$order["rcph"]?>
							</div>
							<div class="col-md-4">
								<label>lcyl: </label>
								<?=$order["rcyl"]?>
							</div>
							<div class="col-md-4">
								<label>laxe: </label>
								<?=$order["raxe"]?>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4">
								<label>pd: </label>
								<?=$order["pd"]?>
							</div>
							<div class="clearfix"></div>
						</div>
						<?
						}
						?>
						<hr style="border: 1px solid #aaa;"/>
						<div>
							<h2>פרטי הזמנה</h2>

							<?
							
							// error_log(print_r($cart,true));
							if (emptY($cart) || empty($cart["prods"])){
							?>
							<h1>
								אין מוצרים
							</h1>
							<?
							}
							else{
								foreach ($cart["prods"] as $entry_id => $cart_entry){
								?>
								<div class="cart_row">
									<div>
										<a href="index.php?page=product&prod_id=<?=$cart_entry['prod_id']?>" target="_blank">קישור למוצר</a>
										<a href="index.php?page=admin_edit_order&order_id=<?=$this->get_edited_data("id")?>&prod_id=<?=$cart_entry['prod_id']?>">ערוך מוצר</a>
									</div>
									<div class="cart_img ib" style="float: right; margin: 0 15px;">
										<img src="<?=site_config::get_value('upload_images_folder').$cart_entry['prod_image']?>" style="max-width:100px;"/>
									</div>
									<div class="ib prod_inf" style="float: right; margin: 0 15px;">
										<h2><?=$cart_entry['prod_name']?></h2>
										<h3><?=$cart_entry['prod_price']?></h3><br />
										<b>כמות :</b> &nbsp;<?=$cart_entry['quantity']?>
									</div>
									<div style="float: right; margin: 0 15px;">
										<?
										if (!empty($cart_entry['attachments'])){
											foreach ($cart_entry['attachments'] as $attachment){
												?>
												<div style="float: right; marging: 5px;">
													<img src="<?=site_config::get_value("upload_user_files_folder").$attachment?>" style="max-widht: 100px; max-height: 70px;"/>
													<br/>
													<a href="<?=site_config::get_value("upload_user_files_folder").$attachment?>" target="_blank" download>להורדה</a>
												</div>
												<?
											}
										}
										?>
									</div>
									<div class="clear"></div>
									<div class="ib comment">
										<b>הערות :</b><br /> <?=$cart_entry['comments']?>
									</div>
									<?
									if(!empty($cart["shipping_price"])){
									?>
									<div class="clear"></div>
									<div class="ib comment">
										<b>כתובת למשלוח :</b><br /> <?=$cart['shipping_address']?>
									</div>
									<?
									}
									if (!emptY($cart_entry['prod_options'])){
									?>
									<div>
										<?
										foreach ($cart_entry['prod_options'] as $det_data_id => $det_data){
										?>
										<div class="cart_opt">
											<?=$det_data['name']?>
											<?=$det_data['price']?>
										</div>
										<?
										}
										?>
									</div>
									<?
									}
									?>
									<div class="total"><h3>סה"כ:<?=$cart_entry['total_price']?></h3></div>
									<div class="clear"></div>
								</div>
								<?
								}
								?>
								<div>
									<div class="total"><h2>סה"כ:<?=$cart["total_price"]?></h2></div>
									<?
									if (!empty($cart["discount"])){
									?>
										<div class="clear"></div>
										<div class="total">
											<h1>הנחת קופון: <?=$cart["discount"]?></h1>
										</div>
										 <div class="clear"></div>
										<div class="total">
											<h1>מחיר לאחר הנחה: <?=$cart["total_price_after_discount"]?></h1>
										</div>
										 <div class="clear"></div>

									<?
									}
									?>
									<div class="total">
											<h1>משלוח: <?=$cart["shipping_price"]?></h1>
										</div>
										 <div class="clear"></div>
										<div class="total">
											<h1>סה"כ כולל משלוח: <?=$cart["price_to_pay"]?></h1>
										</div>
								</div>
								<div style="clear:both;"></div>
								<?
							}
						?>
						</div>
						<input type="hidden" name="action" value="<?=(empty($this->edited_object)?"admin_insert_data":"admin_edit_data")?>"/>
						<input type="hidden" name="update_id" id="update_id" value="<?=$this->get_edited_data("id")?>"/>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="" class="col-md-5">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">רשימת הזמנות</h3>
			</div>
			<div class="panel-body">
				<div class="table">
					<div class="add_delete_toolbar" ></div>
					<div class="spacer" style="height: 15px;"></div>
					<div id="grid_wrapper">
						<table id="data_grid" class="display dt-responsive" width="100%" cellspacing="0" cellpadding="0">
							<thead>
								<tr><th>מספר</th>
									<th>שם</th>
									<th>מחיר</th>
									<th>טלפון</th>
									<th>תאריך</th>
									<th>פעולות</th>
								</tr>
							</thead>
							<tfoot>
								<tr><th>מספר</th>
									<th>שם</th>
									<th>מחיר</th>
									<th>טלפון</th>
									<th>תאריך</th>
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
