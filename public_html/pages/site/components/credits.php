<?php

$credits = $this->data->user->loged_in_user->get_credits();

?>
<div class="ttitle">קרדיטים</div>
<div id="credit_balance">
<div class="credit_rest">
יתרת הקרדיטים הנוכחי:&nbsp; <span><?php echo htmlentities($credits);?> </span><span class="shekel">&#8362;</span>
<br/>
מינימום קרדיטים לניצול :&nbsp; <span><?=site_config::get_value('percent_of_deal_credits'); ?></span><span class="shekel">&#8362;</span>

</div>
<!--
<div class="gift_card_enter">
<p>במידה וברשותך קוד Gift Card יש להזין אותו כאן: </p>

<form class="" action="#credits" method="post">
   <input type="text" name="giftcard_code" id="giftcard_code" required />
   <button name="add_giftcard_code_credits" type="submit">אישור</button>
</form>
</div>
-->
</div>
