<?/* 
    $gift_cards = "";
    //error_log(print_r($this->data->translator->get_translator("GIFT CARD")->id+1,1));
    $gift_cards = $this->data->product->get_by_column("name_trans_id",$this->data->translator->get_translator("GIFT CARD")->id+1);
    //error_log(print_r($gift_cards,1));
    if(!empty($gift_cards)){
?>
<div id="gift_card">
<?php
        foreach ($gift_cards as $gift_card) {
?>
        <form action="" method="POST">
        <select id="select_giftcard" name="prod_id">
            <option value="0">בחר GIFTCARD</option>
            <option value="<?php echo $gift_card->id ?>"><?php echo $gift_card->name ?></option>
        </select>
        <button name="action" value="add_to_cart">הוסף GIFT CARD</button>
        </form>
<?php
        }
?>
</div>
<?php
    }
    unset($gift_cards); */
?>
