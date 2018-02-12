<script>
$(function(){
    $edit_link = $("#edit-address");
    $address_form = $("#address-form");

    $edit_link.on("click",function(e){
        e.preventDefault();
        $address_form.toggle();
    });
});
</script>
<div class="ttitle">פרטי התחברות</div>
<div class="edit_address"><a id="edit-address" href="#" class="btn-default">עריכה</a></div>
<div>
<div id="detail-info">
<div><span>שם פרטי:</span><?=$this->data->user->loged_in_user->first_name?> </div>
<div><span>שם משפחה:</span><?=$this->data->user->loged_in_user->last_name?> </div>
<div><span>מייל (שם משתמש):</span><?=$this->data->user->loged_in_user->username?> </div>
<div><span>תאריך לידה:</span><?=date("d/m/Y",strtotime($this->data->user->loged_in_user->birthday)) ?></div>
<div><span>כתובת משלוח:</span>

    <?
	if (!empty($this->data->user->loged_in_user->address)){

		echo  ($this->data->user->loged_in_user->city);
	}
    else{
		echo  "לא הגדרת עדיין כתובת חיוב ברירת מחדל.";
	}
	?>
	</div>
</div>
<div id="address-info">
    <div id="address-form" style="display:none">
	<div class="address_info_title">עריכת פרטים</div>
        <form action="" method="post">
 <div class="half_form">
 		  <div class="field">
                <label for="first_name" class="required">שם פרטי</label>
                <div class="input-box">
                    <input type="text" name="first_name" value="" title="שם פרטי" class="input-text city-validation ui-autocomplete-input" id="first_name" autocomplete="off" placeholder="<?=$this->data->user->loged_in_user->first_name?>">
                </div>
            </div>
			 <div class="field">
                <label for="last_name" class="required">שם משפחה</label>
                <div class="input-box">
                    <input type="text" name="last_name" value="" title="שם פרטי" class="input-text city-validation ui-autocomplete-input" id="last_name" autocomplete="off" placeholder="<?=$this->data->user->loged_in_user->last_name?>">
                </div>
            </div>
				 <div class="field">
                <label for="last_name" class="required">מייל (שם משתמש)</label>
                <div class="input-box">
                    <input type="text" name="username" value="" title="מייל (שם משתמש)" class="input-text city-validation ui-autocomplete-input" id="username" autocomplete="off" placeholder="<?=$this->data->user->loged_in_user->username?>">
                </div>
            </div>
				 <div class="field">
                <label for="pass" class="required">סיסמא</label>
                <div class="input-box">
                    <input type="password" name="pass" value="" title="שם פרטי" class="input-text city-validation ui-autocomplete-input" id="pass" autocomplete="off" placeholder="<?=$this->data->user->loged_in_user->pass?>">
                </div>
            </div>
 </div>
	 <div class="half_form">
     				 <div class="field">
                <label for="phone" class="required">טלפון</label>
                <div class="input-box">
                    <input type="text" name="phone" value="" title="שם פרטי" class="input-text city-validation ui-autocomplete-input" id="phone" autocomplete="off" placeholder="<?=$this->data->user->loged_in_user->phone?>">
                </div>
            </div>
            <div class="field">
                <label for="city" class="required">עיר</label>
                <div class="input-box">
                    <input type="text" name="city" value="" title="עיר" class="input-text city-validation ui-autocomplete-input" id="city" autocomplete="off" placeholder="<?=$this->data->user->loged_in_user->city?>">
                </div>
            </div>
            <div class="field">
                <label for="street" class="required">רחוב</label>
                <div class="input-box">
                    <input type="text" name="address" value="" title="רחוב" class="input-text city-validation ui-autocomplete-input" id="street" autocomplete="off" placeholder="<?=$this->data->user->loged_in_user->address?>">
                </div>
            </div>
			 <div class="field">
                <label for="birthday" class="required">תאריך לידה</label>
                <div class="input-box">
                    <input type="text" name="birthday" value="" title="תאריך לידה" class="input-text city-validation ui-autocomplete-input datepicker" id="birthday" autocomplete="off" placeholder="<?=date("d/m/Y",strtotime($this->data->user->loged_in_user->birthday)) ?>">
                </div>
            </div>
	 </div>
			<input type="hidden" name="action" value="save_address" />
            <button class="btn-default" name="save_address" type="submit">עדכן</button>
        </form>
    </div>
</div>
</div>
