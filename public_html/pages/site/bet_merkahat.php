<div id="contain_side">
  <div class="prod_title"><h1>מרפאות ובתי מרקחת</h1> 	</div>
  <div>
<button id="map_sh"><span>הצג</span> מפה</button>
</div>
<div class="branch_map" >
	<div id="google_map" style="width:100%;height:0px;"></div>
</div>

<script>
$(function(){
	$('#map_sh').click(function(){
		var that = this;
		if($("#google_map").height() == 0){
			$("#google_map").animate({
				height: 450
			},1000,function(){
				initMap();
				$(that).find("span").text("הסתר");
			});
		}
		else{
			$("#google_map").animate({
				height: 0
			},1000, function(){
				$(that).find("span").text("הצג");
			});
		}
	});
});
</script>
<div>
	<form method="post">
		<select name="city">
			<option value="-1">בחר עיר</option>
		</select>
		<button>Search</button>
	</form>
</div>
  <div id="merkahat_table">
  <div id="m_holder">
  <span>שם</span>
  <span>עיר</span>
  <span>כתובת</span>
  <span>טלפון</span>
  </div>
<?
if(!empty($_POST) && !empty($_POST['city']))
	if($_POST['city'] == '-1'){
		echo 'Da';
		$pharms = $this->data->pharm->get_all();
	}
else{
	echo 'Net';
	$pharms = $this->data->pharm->get_all();
}
if(!empty($pharms)){
	foreach($pharms as $pharm){
?>
	<div class="m_row">
		<span><?=$pharm->name?></span>
		<span><?=$pharm->city?></span>
		<span><?=$pharm->address?></span>
		<span>
			<a href="tel:<?=$pharm->phone?>"><?=$pharm->phone?></a>
		</span>
	</div>
<?
	}
}
?>
<!--
   <div class="m_row">
  <span>
ביג אילת</span>
  <span>אילת</span>
  <span>הסתת 14 מרכז ביג </span>
  <span><a href="tel:077-8881120">077-8881120</a></span>
  </div>

   <div class="m_row">
  <span>
ביג אילת</span>
  <span>אילת</span>
  <span>הסתת 14 מרכז ביג </span>
  <span><a href="tel:077-8881120">077-8881120</a></span>
  </div>
-->
  
  </div>
  </div>
  
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBYldrnYJC0yRATRXPZWgM715vSPO4yjr4&language=iw"
  type="text/javascript"></script>
<script>
function detectmob() { 
 if( navigator.userAgent.match(/Android/i)
 || navigator.userAgent.match(/webOS/i)
 || navigator.userAgent.match(/iPhone/i)
 || navigator.userAgent.match(/iPad/i)
 || navigator.userAgent.match(/iPod/i)
 || navigator.userAgent.match(/BlackBerry/i)
 || navigator.userAgent.match(/Windows Phone/i)
 ){
    return true;
  }
 else {
    return false;
  }
}

function initMap() {
	var map = new google.maps.Map(document.getElementById("google_map"), {
		center: new google.maps.LatLng(32.079722, 34.796709),
		scrollwheel: true,
		zoom: 7
	});
<?
$c = 0;
$pharms = $this->data->pharm->get_all();
// if(!empty($pharms)){
	foreach($pharms as $pharm){
	if($pharm->lat==0.0 && $pharm->lng==0.0){
		$coords = get_coords($pharm->address);
		$pharm->lat = $coords[0];
		$pharm->lng = $coords[1];
		$pharm->save();
		continue;
	}
	if(!empty($pharm->lat) && !empty($pharm->lng)){
?>
	if(detectmob())
		var content = "<p><?=addslashes($pharm->name)?></p><p><?=addslashes($pharm->address)?></p><a href='waze://?q=<?=addslashes($pharm->address)?>'><img src='pics/waze.png' /></a>";
	else
		var content = "<p><?=addslashes($pharm->name)?></p><p><?=addslashes($pharm->address)?></p>";
	var myLatLng<?=$c?> = {lat: <?=$pharm->lat?>, lng: <?=$pharm->lng?>};
	var infowindow<?=$c?> = new google.maps.InfoWindow({
		content: content
	});
	var marker<?=$c?> = new google.maps.Marker({
		map: map,
		position: myLatLng<?=$c?>,
		title: '<?=addslashes($pharm->address)." - ".addslashes($pharm->name)?>'
	});
	marker<?=$c?>.addListener('click', function() {
		infowindow<?=$c?>.open(map, marker<?=$c?>);
	});
<?
	$c++;
	}
}
?>
}
$(function(){
	// initMap();
});
</script>



<?
function get_coords($address){
	// error_log("Func ".$address);
	if (!empty($address)){
		// if(!empty($this->branch_city_id)){
			// $city = $this->data->branch_city->get_by_id($this->branch_city_id)->name;
			$url = "http://maps.google.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false";
			// error_log($url);
		// }
		// else
			// $url = "http://maps.google.com/maps/api/geocode/json?address=" . urlencode(clear_string($_REQUEST['address'])) . "&sensor=false";

		$responce = file_get_contents($url);
		$json = json_decode($responce, true);
		if ($json['status'] != "ZERO_RESULTS" && !empty($json['results'][0])){
			return array($json['results'][0]['geometry']['location']['lat'] , $json['results'][0]['geometry']['location']['lng']);
		}
		else{
			error_log(print_r($json, true));
		}
	}
}
?>