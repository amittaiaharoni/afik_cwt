 <?php

$email = 'info@cwt.org.il';
$err='';

$values = array();
$values['name'] = htmlentities($_POST['name'], null, 'UTF-8');
$values['phone'] = htmlentities($_POST['phone'], null, 'UTF-8');
$values['mail'] = htmlentities($_POST['mail'], null, 'UTF-8');




$values['subject'] = 'Chronic Wound Treatment';


$values['body'].= 		'שם פרטי: ' . " " . $values['name'] . " ";
$values['body'].= 		'טלפון: ' . " " . $values['phone'] . " ";
$values['body'].= 		'מייל: ' . " " . $values['mail'] . " ";








//$headers  = 'MIME-Version: 1.0' . "\r\n";
//$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

// Additional headers
//$headers .= 'To: '.$email. "\r\n";
//$headers .= 'From: '.$values['name'].' <'.$values['name'].'>' . "\r\n";
$to = $email;
$from = "info@cwt.org.il";
$subject = $values['subject'];

mail($email, $values['subject'], $values['body'], $headers);


?>



<!DOCTYPE>
<html>
<head>
   <title>Chronic Wound Treatment</title>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="TEXT/HTML; CHARSET=UTF-8">
	<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
	<meta content="telephone=no" name="format-detection">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="keywords" content="">
	<meta name="description" content="">
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" type="text/javascript"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="font.css" type="text/css" charset="utf-8" />
    <link rel="stylesheet" href="css/font-awesome.css">
	<link href="https://fonts.googleapis.com/css?family=Heebo:300,400,500,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Merriweather:300,400,400i" rel="stylesheet">
	<script src="modernizr.custom.js"></script>
	<script class="rs-file" src="js/royal_slider/jquery.royalslider.js"></script>
 <link class="rs-file" href="js/royal_slider/rs-minimal-white.css" rel="stylesheet">
  <link class="rs-file" href="js/royal_slider/royalslider.css" rel="stylesheet">

  <script>
         	$(window).load(function() {

			$(".rsImg").show();
			$(".infoBlock").show();
			$('#full-width-slider').royalSlider({
				autoPlay: {
				   enabled: true,
					delay: 3000,
                    pauseOnHover: false
				},
				arrowsNav: false,
				loop: true,
				keyboardNavEnabled: false,
				controlsInside: false,
				imageScaleMode: 'fill',
				arrowsNavAutoHide: false,
				autoScaleSlider: true,
				autoScaleSliderWidth: 1600,
				autoScaleSliderHeight: 700,
				controlNavigation: 'none',
				thumbsFitInViewport: false,
				navigateByClick: true,
                arrowsNavHideOnTouch: true,
				transitionType:'fade',
				globalCaption: false,
				deeplinking: {
				  enabled: true,
				  change: false
				},
				/* size of all images http://help.dimsemenov.com/kb/royalslider-jquery-plugin-faq/adding-width-and-height-properties-to-images */
				imgWidth: 1600,
				imgHeight: 700
			  });
		});
		 </script>

    	<link rel="stylesheet" href="css/animate.css">
	<script src="js/wow.min.js"></script>
              <script>
              new WOW().init();
              </script>

</head>
<body>
<div id="wrapper">
<div id="logo"><img src="pics/logo.png" alt="" /></div>
<Div id="underlogo">
<h1 class="nomob">מזרנים  דינמיים למניעה וטיפול בפצעי לחץ | מזרנים  סטטיים למניעה וטיפול בפצעי לחץ  <br> כסא גלגלים/ כורסא </h1>
<h1 class="mob">מזרנים  דינמיים למניעה וטיפול בפצעי לחץ <br>מזרנים  סטטיים למניעה וטיפול בפצעי לחץ  <br> כסא גלגלים/ כורסא </h1>
</Div>
<div id="contact_holder" class="cf">
     <Div id="info_holder">
	 <h3>האתר בבניהה...</h3>
<h3>אתר חדש יעלה בקרוב</h3>
</Div>
	<div class="video_h left_side">
<Div class="video-container">
<iframe width="560" height="315" src="https://www.youtube.com/embed/ewmFvO3P3vE" frameborder="0" allowfullscreen></iframe>
</Div>
</div>
</div>
<Div id="video_holder" class="cf">
	<div class="contact_form">
          <h2 class="nomob">תודה על פנייתך</h2>

	</div>



<div class="video_h right_side">
<Div class="video-container">
<iframe width="560" height="315" src="https://www.youtube.com/embed/7Y6xPuWN-Nc" frameborder="0" allowfullscreen></iframe>
</Div>
</div>
</Div>

<!-- <div>

	 	<div id="full-width-slider" class="royalSlider heroSlider rsMinW nomob">


			  <div class="rsContent">
              <img class="rsImg" src="pic1.jpg" alt="" />
			  <div class="infoBlock infoBlockLeftBlack rsABlock" data-fade-effect="" data-move-offset="10" data-move-effect="bottom" data-speed="200">
                  <h1>דירות 4 ו-5 חדרים עם מרפסת</h1>

				</div>
              </div>

			  <div class="rsContent">
              <img class="rsImg" src="pic2.jpg" alt=""  />
			   <div class="infoBlock infoBlockLeftBlack rsABlock" data-fade-effect="" data-move-offset="10" data-move-effect="bottom" data-speed="200">
                  <h1>דירות 4 ו-5 חדרים עם גינה</h1>

				</div>
              </div>

			  <div class="rsContent">
              <img class="rsImg" src="pic3.jpg" alt=""  />
			   <div class="infoBlock infoBlockLeftBlack rsABlock" data-fade-effect="" data-move-offset="10" data-move-effect="bottom" data-speed="200">
                  <h1>פנטהאוזים עם מרפסות גדולות וברכה פרטית ונוף לים</h1>

				</div>
              </div>

			   <div class="rsContent">
              <img class="rsImg" src="pic2.jpg" alt=""  />
			   <div class="infoBlock infoBlockLeftBlack rsABlock" data-fade-effect="" data-move-offset="10" data-move-effect="bottom" data-speed="200">
                  <h1>קוטג׳ים 5 חדרים עם גינות גדולות</h1>

				</div>
              </div>




		</div>




	</div>
	<script>
		$(".rsImg").hide();
		$(".rsImg").first().show();
		$(".infoBlock").hide();
		$(".infoBlock").first().show();
	</script>-->








</div>


</body>
</html>