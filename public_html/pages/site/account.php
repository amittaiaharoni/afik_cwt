<script>
$( function() {
    $( "#tabs" ).tabs({
		// collapsible: true,
	});
	$("#tabs, #tabs *")
/*.removeClass('ui-widget ui-widget-content ui-widget-header ui-tabs-panel ui-corner ui-tabs ui-corner-all ui-tabs-nav ui-tabs-tab ui-corner-top ui-state-default ui-tab ui-tabs-active ui-state-active ui-state-hover');     */
  } );
</script>

<div id="tabs">
<div id="tabs_side">
<div class="tab_title">אזור אישי</div>
  <ul>
    <li><a href="#my_account">החשבון שלי</a></li>
    <li><a href="#personal_details">פרטי התחברות</a></li>
    <li><a href="#orders_hist">הסטוריית הזמנות</a></li>
    <li><a href="#wishlist">WISHLIST </a></li>
    <li><a href="#credits">קרדיטים</a></li>
  </ul>
</div>
<div id="tabs_holder">
	<div id="my_account">
		
		<div id="m_a_inner">
		{__orders_archive__}
		<div class="on_archive_half">{__wishlist_table__}</div>
		<div class="on_archive_half lside">{__credits__}</div>
		</div>
	</div>
  <div id="personal_details">
      
      {__user_profile__}
  </div>
  <div id="orders_hist">
      
      {__orders_archive_2__}
  </div>
  <div id="wishlist">
      
      {__wishlist_table__}
  </div>
  <div id="credits">
      
      {__credits__}
  </div>
</div>
</div>
