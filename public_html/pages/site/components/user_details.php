<?
	
?>
	<div class="user_detail">	
	
		<div class="form-group">
			<label for="username">(דואר אלקטרוני(שם משתמש</label>
			<input type="email" required name="username" id="username" value="<?=$this->data->user->get_user_detail("username")?>"/>
		</div>	
        <div class="form-group">
			<label for="username">שם פרטי</label>
			<input type="first_name" required name="first_name" id="first_name" value="<?=$this->data->user->get_user_detail("first_name")?>"/>
        </div>
        <div class="form-group">
			<label for="first_name">שם משפחה</label>
			<input type="text" required name="last_name" id="last_name" value="<?=$this->data->user->get_user_detail("last_name")?>"/>
        </div>
		
		<div class="form-group">
			<label for="pass">סיסמא</label>
			<input type="password" required name="pass" id="pass" value="<?=$this->data->user->get_user_detail("pass")?>"/>
        </div>
		
	 <?/*  <input type="text" placeholder="ת.ז" name="tz" id="tz" value="<?=$this->data->user->get_user_detail("tz")?>"/>  */?>
		<div class="form-group">
			<label for="phone">טלפון</label>
			<input type="text" required name="phone" id="phone" value="<?=$this->data->user->get_user_detail("phone")?>"/>
		</div>
		<div class="form-group">
			<label for="birthday">תאריך לידה</label>
			<input type="text" class="datepicker" name="birthday" id="birthday" value="<?=$this->data->user->get_user_detail("birthday")?>"/>
        </div>

		<?/*
		<div class="form-group">
			<label for="pass">סיסמה</label>
			<input type="password" name="pass" id="pass" value="<?=$this->data->user->get_user_detail("pass")?>"/>
        </div>
		*/?>

		 <div class="form-group">
			<label for="city">עיר</label>
		<input type="text" name="city" id="city" value="<?=$this->data->user->get_user_detail("city")?>"/>
		</div>
		  <div class="form-group">
			<label for="address">רחוב</label>
			<input type="text" required name="address" id="address" value="<?=$this->data->user->get_user_detail("address")?>"/>
		</div>

	  <?/*
      <div class="form-group2">
        <label for="city">עיר</label>
		<input type="text" name="city" id="city" value="<?=$this->data->user->get_user_detail("city")?>"/>
         </div>
      	<label for="sex">מין</label>
	   <div class="styled-select">
       	<select name="sex">
			<option value="1" <?=($this->data->user->get_user_detail("sex")==1?"selected":"")?>>זכר</option>
			<option value="0" <?=($this->data->user->get_user_detail("sex")==0?"selected":"")?>>נקבה</option>
		</select>
       </div>

	   <input type="text" placeholder="משקל" name="weight" id="weight" value="<?=$this->data->user->get_user_detail("weight")?>"/>  */?>
	</div>
