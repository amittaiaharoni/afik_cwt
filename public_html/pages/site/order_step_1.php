<div class="right">

<div id="order_side">
<!-- ------------------INCLUDE --------------->
	{__order_side__}
<!-- ----------------INCLUDE----------------- -->
    </div>
<div class="order_holder">


  <div class="form_side">
  	<?
		/*if (!$this->data->user->loged_in){
		?>
	<div class="login_table cf">
		<form action="index.php?page=order_step_2" method="post">
			<h1>
			 התחבר
			</h1>
			<?
				// printMessage("login");
			?>
			<form role="form" action="" method="post">
			  <div class="form-group">
				<label for="username">מייל</label>
				<input type="email" class="form-control" id="username" name="username">
			  </div>
			  <div class="form-group">
				<label for="pass">סיסמה</label>
				<input type="password" class="form-control" id="pass" name="pass">
			  </div>
			  <button type="submit" name="login" class="btn btn-default">התחבר</button>
			</form>
		</form>
	</div>
		<?
		}*/
	?>

  	{__cart_table__}
	<div class="login_table cf">
		<form action="index.php?page=order_step_2" method="post">
		<?
		if ($this->data->user->loged_in){
		?>
			<!--<h1>
			   עריכת פרטים
			</h1>-->
		<?
		}
		else{
		?>
			<h1>
			  פרטים אישיים
			  {__user_details__}
			</h1>
		<?
		}
		?>



			<?
			if ($this->data->user->loged_in){
			?>
				<button name="update_user_details" class="btn-default">
				  המשך
				</button>
			<?
			}
			else{
			?>
				<button name="register" class="btn-default">
				   המשך
				</button>
			<?
			}
			?>
		</form>
	</div>
  </div>
   <div class="login_bg"><img src="pics/reg.png" /></div>
    </div>
</div>
