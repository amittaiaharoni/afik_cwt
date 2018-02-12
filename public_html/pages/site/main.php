<section id="main_about">
<div class="width-wrapper cf">
<div class="about_text">
 CWT-  טיפול ולמניעה של פצעי לחץ, פצעים קשיי ריפוי וכוויות בכל הדרגות<br>
חברת  CWT מתמחה בפתרונות  טיפול ומניעה של  פצעים, פצעי לחץ, כוויות, סטומה ועוד, באמצעות טכנולוגיה מתקדמת הכוללת מכשור רפואי ייעודי לטיפול ומניעה של פצעים.<br><br>
סל המוצרים שלנו כולל חבישות מתקדמות, תמיסות חיטוי, משחות, מזרנים ומוצרים נלווים, מוצרי סטומה, צנתרים ועוד, לחולה הביתי ולמרפאות ובתי חולים.<br><br>
כל המוצרים שלנו עומדים בקדמת הטכנולוגיה ובסטנדרטים ותקני איכות המחמירים ביותר.<br>
 חברתינו  מחזיקה בתקן ISO 13845-2003 ובתקן ISO 9001-2008 מטעם מכון התקנים הישראלי.<br>
<br>יש לכם שאלה?
<a href="?page=contact">נשמח לעמוד לרשותכם</a>

</div>
<div class="about_img">
<div class="about_img_holder">
<img src="pics/about_main.jpg" alt="" />
</div>
</div>
</div>
</section>

<section id="menia">
<div class="width-wrapper cf">
<div class="menia_text">
<h2 class="main_title">חטיבת המניעה </h2>
<p>
<strong>מוצרי חטיבת המניעה  כוללים- </strong>


<ul>
<li>מזרנים למניעה וטיפול בפצעי לחץ  </li>
<li>כריות ישיבה לכיסא גלגלים/ כורסא למניעה וטיפול בפצעי לחץ  </li>
<li>מגני עקב למניעה וטיפול בפצעי לחץ</li>
<li>מגוון אביזרי עזר – הגנה למקלחת, משטחי העברה
</li>
</ul>  <br>
<a href="?page=contact">צרו קשר</a> ונציג של CWT יגיע  ללא עלות או התחייבות לבית החולה, לבחינת סוג המוצר הדרוש בהתאם למצב הרפואי.   <br><br>
<a class="more_btn" href="?page=contact">לפרטים והצעת מחיר</a>
</p>
</div>
<div class="menia_video">
<div class="video-container">
<iframe width="560" height="315" src="https://www.youtube.com/embed/ewmFvO3P3vE" frameborder="0" allowfullscreen></iframe>
</div>
</div>
</div>
</section>

<section id="tipul">
<div class="width-wrapper cf">
<div class="tipul_text">
<div class="tipul_text_holder">
<h2 class="main_title">חטיבת הטיפול</h2>
<p>חטיבת הטיפול של   CWT  כוללת חבישות מתקדמות לפצעים, חבישות אקטיביות, חבישות עם חומרים פעילים,  ג'לים ומשחות, תמיסות חיטוי, משחות לריפוי כוויות ופצעים כרונים וסדרה של משחות לטיפול ביתי.<br>
חברת CWT מייבאת  ומשווקת   באופן בלעדי מוצרים ממספר חברות רב לאומיות כגון B –Brown    -  תאגיד בין לאומי ענק, Faln Health, ,B Factory   אפקס מדיקל ועוד.
 </p>
<Div class="tipul_img">
<img src="pics/prods.png" alt="" />
</Div>
</div>
</div>
<div id="tipul_cats">
<?
$cats = $this->data->category->get_by_column('parent_cat_id', 2);
sorter::sort($cats,'display_order','asc');

$i = 0;
if(!empty($cats)){
	foreach($cats as $cat){
		if($i == 0){
		?>
		<div class="tipul_left">
		<?
		}
		if($i == 2){
		?>
		</div>
		<div class="tipul_right">
		<?
		}
		$banner  ='';
		$img  ='';
		if(!empty($cat->banner_image) && file_exists(site_config::get_value('upload_images_folder').$cat->banner_image)){
			$banner = site_config::get_value('upload_images_folder').$cat->banner_image;
		}
		if(!empty($cat->side_image) && file_exists(site_config::get_value('upload_images_folder').$cat->side_image)){
			$img = site_config::get_value('upload_images_folder').$cat->side_image;
		}
		if($i++ <= 1){
?>
	
		<Div class="catblock big cf">
			<a href="index.php?page=category&cat=<?=$cat->id?>">
				<img src="<?=$img?>" alt="" />
				<div class="txt">
					<p><?=$cat->desc?></p>
				</div>
				<Div class="poc"><img src="<?=$banner?>" alt="" /></Div>
			</a>
		</Div>
<!--
		<Div class="catblock big cf">
			<a href="#">

				<img src="pics/logo2.png" alt="" />
				<div class="txt">
					<p>מוצרי חבישה
					עם יכולת ספיגת
					הפרשות גבוהה</p>
				</div>
				<Div class="poc"><img src="pics/cat2.jpg" alt="" /></Div>
			</a>
		</Div>
	-->
	
<?
		}
		else{
?>
	
		<Div class="catblock cf">
			<a href="index.php?page=category&cat=<?=$cat->id?>">
				<div class="txt">
					<img src="<?=$img?>" alt="" />
					<p><?=$cat->desc?></p>
				</div>
				<Div class="poc"><img src="<?=$banner?>" alt="" /></Div>
			</a>
		</Div>
	<!--
		<Div class="catblock cf">
		<a href="#">
		<div class="txt">
		<img src="pics/logo4.png" alt="" />
		<p>תמיסה סטרילית אנטיספטית
		לניקוי והרטבה של
		פצעים וכוויות</p>
		</div>
		<Div class="poc"><img src="pics/cat4.jpg" alt="" /></Div>
		</a>
		</Div>

		<Div class="catblock cf">
		<a href="#">
		<div class="txt">
		<h2>רימות</h2>
		<p>ניקוי פצעים כרוניים
		מן הרקמה הנמקית</p>
		</div>
		<Div class="poc"><img src="pics/cat5.jpg" alt="" /></Div>
		</a>
		</Div>
	-->
	
<?
		}
}
}
?>
	</div>
</div>


</div>


</section>