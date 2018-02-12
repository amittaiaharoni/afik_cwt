<?
$con = db_con::get_con();
$tables = $con->fetchData("SHOW TABLES");

if (!empty($_GET['action'])){
	if ($_GET['action'] == "get_fields"){
		if (!emptY($_POST['table1']) && !emptY($_POST['table2'])){
			$table1_columns = $con->fetchData("SHOW COLUMNS FROM `" . clear_string($_POST['table1'] ."`"));
			$table2_columns = $con->fetchData("SHOW COLUMNS FROM `" . clear_string($_POST['table2'] ."`"));
			
			$ret = array();
			
			foreach ($table1_columns['rows'] as $col){
				$ret["table1"][] = $col['Field'];
			}
			foreach ($table2_columns['rows'] as $col){
				$ret["table2"][] = $col['Field'];
			}
			
			echo json_encode($ret);
			exit();
		}
	}
}

if (isset($_POST['migrate'])){
	// error_log(print_r($_POST,true));
	
	$con = db_con::get_con();
	
	if (!emptY($_POST['fields'])){
		$lang 		= (int)$_POST['language'];
		$table_from = clear_string($_POST['table_from']);
		$table_to 	= clear_string($_POST['table_to']);
		
		$insert_fields = "";
		$select_fields = "";
		
		$translator_max_id = $this->data->translator->get_max_id();
		
		foreach ($_POST['fields'] as $field_from => $field_to){
			if (empty($field_to)) continue;
			
			$field_from = clear_string($field_from);			
			$select_fields .= "`".$field_from."`,";
		}
		$select_fields = rtrim($select_fields, ',');
		
		$select_sql = "SELECT " . $select_fields . " FROM `" . $table_from . "`";
		$result = $con->fetchData($select_sql);
		
		if (!empty($result['rows'])){					
			foreach ($result['rows'] as $row){				
				$insert_fields = "";
				$insert_values = "";
				foreach ($row as $col_name => $col_value){
					if (!empty($_POST['fields'][$col_name])){
						
						$insert_field_name = $_POST['fields'][$col_name];
						
						if (substr($insert_field_name, -9) === "_trans_id"){ // insert translator
							$translator_max_id += 1;
							
							$key = $translator_max_id;
							
							$translator = $this->data->translator->add_new(
								array(
									"key" => $key, 
									"value" => $col_value, 
									"lang" => $lang
								));
								
							$col_value = $key;
						}
						
						$insert_fields .= "`" . clear_string($insert_field_name) . "`,";
						$insert_values .= "'" . $col_value . "',";
						
					}
				}
				if (!empty($insert_fields)){
					$insert_fields = rtrim($insert_fields, ',');
					$insert_values = rtrim($insert_values, ',');
					
					$insert_sql = "INSERT INTO `" . $table_to . "` (" . $insert_fields . ") VALUES (". $insert_values . ")";
					$con->query($insert_sql ,true);	
				}
			}
		}
	}
}

?>
<style>
	#table1_fields div{
		padding: 1%;
	}
	#table1_fields label{		
		margin-bottom: 6px;
	}
	#table2_fields div{
		padding: 1%;
	}
	#table2_fields select{
		height: 26px;
	}
</style>
<script>
	$(document).ready(function(){
		$("#load_fields").on("click", function(){
			$.ajax({
			  type: "POST",
			  dataType: "json",
			  url: "index.php?page=admin_migrate_table&action=get_fields",
			  data: { table1: $( "#table_from option:selected" ).text(), table2: $( "#table_to option:selected" ).text() }
			})
			.done(function( data ) {
				// alert( "Data Saved: " + data );
				$.each( data, function( i, item ) {
					var table_index = i;
					var fields_container = $( "#"+i+"_fields" );
					fields_container.empty();
					if (i == "table1"){
						$.each( item, function( i, field ) {					
							var input = $("<label>");
							// input.attr("type","text");
							// input.attr("name",table_index+"_"+field);
							// input.val(field);
							input.text(field);
							var input_div = $("<div>");
							input_div.append(input);
							fields_container.append(input_div);							
						});
					}
					else{
						var base_select = $("<select>");
						base_select.append($("<option value='0'>לא נבחר</option>"));
						
						$.each( data.table2, function( i, field ) {					
							var option = $("<option>");
							option.val(field);
							option.text(field);
							base_select.append(option);							
						});
						
						$.each( data.table1, function( i, field ) {					
							var select = base_select.clone();
							select.attr("name","fields["+field+"]");
							var select_div = $("<div>");
							select_div.append(select);
							fields_container.append(select_div);							
						});
					}
				});
			});
		});
		
		$("#user_guide").on("click",function(e){
			e.preventDefault();
			$($(this).siblings()[0]).slideToggle();
		});
	});
</script>
<div class="container-fluid" style="direction: ltr; margin: 1% 0;">
	<div class="well">
		<h2 style="text-align:center">
			יבוא מטבלה ישנה
		</h2>		
		<div style="direction:rtl; text-align: center;">
			<a id="user_guide" href="#">הוראות הפעלה</a>
			<p style="display:none">
				לפני השימוש בדף זה יש לייבא טבלה מבסיס נתונים ישן ולתת לה שם זמני, לאחר יבוא יש למחוק את הטבלה הזמנית.
				<br/>
				בוחרים טבלת מקור וטבלת יעד ולוחצים LOAD FIELDS
				<br/>
				יש למפה את השדות של הטבלה הישנה (מצד שמאל) לשדות של הטבלה החדשה (צד ימין)
				<br/>
				אפשר ליבא שדות רק בספה אחת. לדוגמא אם קיימים שדות name ו name_eng רק שדה אחד מבין השניים יועבר לטבלה החדשה
				<br/>
				יש לבחור את הספה של הטקסטים בטבלה הישנה
				<br/>
				לוחצים "שמור" ומכווים לטוב
			</p>
			<br/><br/>
		</div>
		<form action="" method="post">
			<div id="" class="col-md-6" style="text-align:right;">
				From : 
				<select id="table_from" name="table_from">
					<?
					if (!empty($tables['rows'])){
						
						foreach ($tables['rows'] as $row){
							// $table_name = array_shift(array_values($row));
							$row_values = array_values($row);
							$table_name = $row_values[0];
					?>
						<option value="<?=$table_name?>"><?=$table_name?></option>
					<?
						}
					}
					?>
				</select>
			</div>
			<div id="" class="col-md-6" style="text-align:left;">
				To : 
				<select id="table_to" name="table_to">
					<?
					if (!empty($tables['rows'])){
						
						foreach ($tables['rows'] as $row){
							// $table_name = array_shift(array_values($row));
							$row_values = array_values($row);
							$table_name = $row_values[0];
					?>
						<option value="<?=$table_name?>"><?=$table_name?></option>
					<?
						}
					}
					?>
				</select>
			</div>
			<div class="clearfix"></div>
			<div style="text-align: center; margin: 1% 0;">
				<button type="button" id="load_fields">Load fields</button>
			</div>
			<div class="fields_container">
				<div id="table1_fields" class="col-md-6" style="text-align:right;">
					
				</div>
				<div id="table2_fields" class="col-md-6" style="text-align:left;">
					
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="clearfix" style="text-align: center; margin: 2% 0;direction: rtl;">
				הטקסט הוא בספה : 
				<select name="language">
					<?				
						$langs = $this->data->language->get_all();
						if (!emptY($langs))
							foreach($langs as $lang){
								?>
								<option value="<?=$lang->id?>"><?=$lang->name?></option>
								<?
							}
					?>
				</select>
				<br/>
				<br/>
				<button name="migrate" id="migrate">שמור</button>
			</div>
		</form>
	</div>
</div>