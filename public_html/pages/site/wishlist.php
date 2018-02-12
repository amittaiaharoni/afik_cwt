<script>
$(function(){
    $("a[href=#]").on("click",function(e){
        e.preventDefault();
    });
});
function goBack(num) {
    if(typeof num === "undefined")
        window.history.back();
    else
        window.history.go( -Math.abs(num) );
}
</script>
<div id="contain_side">
   
    {__wishlist_table__}
    <div id="go_back_btn">
        <a href="#" onclick="goBack(2)" ><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp; חזרה לקניות</a>
    </div>
</div>

