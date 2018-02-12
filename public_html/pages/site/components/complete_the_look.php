<?
$prod = $this->view_data;
if (!empty($prod->linked_prods)){ ?>
    <div id="main_items2">
    <div class="title"><h2>עוד במשפחה</h2></div>
<div id="complete_the_look" class="cf">
    <?

        $comp_look = array();
		$comp_look_ids = '';
        foreach ($prod->linked_prods as $lp) {
             $comp_look[] = $this->data->product->get_by_id($lp);
        }
		// error_log(print_r($comp_look,1));
        if (!empty($comp_look)){
            $i = 0;
            foreach ($comp_look as $prod){
				if($prod->in_stock == 1 && $prod->stock_count > 0){
					$i++;
					if(!empty($prod) && !empty($prod->id))
						$comp_look_ids .= $prod->id.'-';
					$prod_block = new view($site_path."components/product_block2.php", $prod);
					$this->register_include("prod_block_".$i, $prod_block);
				?>
					{__prod_block_<?=$i?>__}
				<?
					unset($prod_block);
				}
            }
        }
        unset($comp_look);
		$comp_look_ids = rtrim($comp_look_ids,'-');
    ?>
</div>
	<!-- Script is in product.php 
	<div id="add_linked_holder"><button id="add_linked_to_cart" class="btn-default" data-comp_ids="<?=$comp_look_ids?>">הוסף כל המוצרים לסל</button></div>
	-->
    </div>
<?
}
unset($prod); ?>
