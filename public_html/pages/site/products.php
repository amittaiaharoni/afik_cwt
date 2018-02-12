
<div id="contain_side">
  <div id="top_sell">
   <?
if(isset($_GET['is_sale']) && !empty($_GET['is_sale']) && $_GET['is_sale'] == 1 && $_GET['page'] == 'products'){
?>
		<div class="title"><h1>SALE</h1></div>
<?
    }else{
?>
    <div class="title"><h1>תוצאות חיפוש</h1></div>
    <? } ?>
		{__products__}
	</div>
</div>
