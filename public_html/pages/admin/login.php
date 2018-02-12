<div class="container-fluid" style="min-height: 500px;">
	<div class="col-md-4 col-md-offset-4" style="padding-top: 20px; float: left;">
		<?
			// printMessage("login");
		?>
		<form role="form" action="index.php?page=admin" method="post">
		  <div class="form-group">
			<label for="username">שם משתמש</label>
			<input type="text" class="form-control" id="username" name="username" placeholder="שם משתמש">
		  </div>
		  <div class="form-group">
			<label for="pass">סיסמה</label>
			<input type="password" class="form-control" id="pass" name="pass" placeholder="סיסמה">
		  </div>
		  <button type="submit" name="login" class="btn btn-default">הכנס</button>
		</form>
	</div>
</div>