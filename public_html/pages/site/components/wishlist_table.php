<div class="wishlist">
<div class="ttitle"> WISHLIST </div>
<?
	$wishlist = wishList::get_wishList();
	$cnt = 1;
?>
    <div class="message">whishlist <?php echo $wishlist_count = '(' . count($wishlist['prods']) . ')'; ?></div>
<?
	if (emptY($wishlist) || empty($wishlist["prods"])){
	?>
	<div class="no_items_wish">        אין עדיין מוצרים ב-WISHLIST	</div>
	<?
	}
    else{?>
    <div id="wish_table">
        <div class="wish_head">
            <span>מס' פריט</span>
            <span>שם</span>
            <span>תמונה</span>
           <!-- <span>quanity</span>     -->
            <span>מחיר</span>
            <span>&nbsp;</span>
        </div>
	   	<?
		foreach ($wishlist["prods"] as $entry_id => $cart_entry){
			$prod = $this->data->product->get_by_id($cart_entry['prod_id']);
		?>
        <div class="wish_row">
            <span><?php echo $cnt?></span>
            <span><?php echo $cart_entry['prod_name']?></span>
            <span><img src="<?php echo site_config::get_value('upload_thumbs_folder').$cart_entry['prod_image']?>" alt="<?php echo $cart_entry['prod_image']?>"></span>
           <!-- <td><?php echo "x " . $cart_entry['quantity']?></td>   -->
            <span>&#8362;&nbsp;<?php echo $cart_entry['total_price']?></span>
            <span>
            <form action="<?php echo site_config::get_value('site_url').'index.php?page=wishlist'?>" method="POST">
			<input type="hidden" name="id" value="<?=$entry_id?>"/>
            <button class="delete-from-wishList" name="action" value="delete_from_wishList" >
			<i class="fa fa-angle-left"></i><i class="fa fa-angle-right"></i>  </button>
            </form>
            <form action="<?php echo site_config::get_value('site_url').'index.php?page=cart'?>" method="POST">
			<input type="hidden" name="prod_id" value="<?=$cart_entry['prod_id']?>"/>
			<input type="hidden" name="quantity" value="<?=$cart_entry['quantity']?>"/>
			<?
			if($prod->in_stock == 1 && $prod->stock_count > 0){
				?>
				<button class="add_to_cart" name="action" value="add_to_cart" ><i class="fa fa-plus"></i> &nbsp;הוסף לסל&nbsp;<i class="fa fa-shopping-cart"></i></button>
				<?
			}
			else{
			?>
				<!-- TUTA -->
				<a href="index.php?page=product&prod_id=<?=$prod->id?>" class="not_in_stock">לא במלאי <!--<br> להשארת פרטים--></a>
			<?
			}
			?>
            </form>
            </span>
        </div>
    <?
            $cnt++;
    }
    ?>
    </div>
		<div style="clear:both;"></div>
		<?
	}
    unset($wishlist);
?>
</div>
