<?
////////////////////////////////////////////////////////////////////////////////////////////////////
//		IMAGE UPLOAD
////////////////////////////////////////////////////////////////////////////////////////////////////

function is_image($file_name){
	if (!emptY($file_name) && !empty($_FILES[$file_name]['tmp_name'])){
		// $info = getimagesize($_FILES[$file_name]['tmp_name']);
		// if ($info === FALSE) {
		   // return false;
		// }


		// exif_imagetype throws "Read error!" if file is too small
		if (filesize($_FILES[$file_name]['tmp_name']) < 11)
			return false;

		$allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
		$detectedType = exif_imagetype($_FILES[$file_name]['tmp_name']);
		$error = !in_array($detectedType, $allowedTypes);
		if ($error)
			return false;
		return true;
	}
   return false;
}

function upload_file($file_name, $directory = ""){
	if (empty($directory))
		$directory = site_config::get_value("upload_files_folder");

	if (!empty($_FILES[$file_name]['name'])){
		$ret = false;

		$allowedExts = array("pdf", "doc", "docx", "xls", "xlsx");
		$exp = explode(".", $_FILES[$file_name]["name"]);
		$extension = end($exp);
		if ((($_FILES[$file_name]["type"] == "application/pdf") ||
			($_FILES[$file_name]["type"] == "application/msword") ||
			($_FILES[$file_name]["type"] == "application/octet-stream") ||
			($_FILES[$file_name]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document")) &&
			($_FILES[$file_name]["type"] == "application/vnd.ms-excel") ||
			($_FILES[$file_name]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") ||
			($_FILES[$file_name]["size"] < site_config::get_value("upload_max_file_size")*1024) &&
			in_array($extension, $allowedExts))

		{
			if ($_FILES[$file_name]["error"] > 0)
			{
				error_log("file upload error : " . $_FILES[$file_name]["error"]);
			}
			else
			{
				$file = str_replace(' ', '_', $_FILES[$file_name]['name']);

				$date = new DateTime();
				$file = $date->getTimestamp() . $file;

				$success = false;
				if (!file_exists($directory . $file)) {
					// move the file to the upload folder and rename it
					$success = move_uploaded_file($_FILES[$file_name]['tmp_name'], $directory . $file);
				} else {
					$result = 'A file of the same name already exists.';
				}

				if ($success) {
					$result = "$file uploaded successfully.";
					$ret = $file;
				} else {
					$result = "Error uploading $file. Please try again.";
				}
			}
		}
		return $ret;

	}
	else return false;
}


function upload_image($img_name , $thumb_width = 0, $directory = ""){
	if (empty($directory))
		$directory = site_config::get_value("upload_images_folder");

	if (!empty($_FILES[$img_name]['name'])){
		$ret = false;

		// replace any spaces in original filename with underscores
		$file = str_replace(' ', '_', $_FILES[$img_name]['name']);

		$date = new DateTime();
		$file = $date->getTimestamp() . $file;

		// create an array of permitted MIME types
		$permitted = array('image/gif', 'image/jpeg', 'image/pjpeg','image/png');

		// upload if file is OK
		if (in_array($_FILES[$img_name]['type'], $permitted)
					&& $_FILES[$img_name]['size'] > 0 && $_FILES[$img_name]['size'] <= (site_config::get_value("upload_max_image_size")*1024)) {
			switch($_FILES[$img_name]['error']) {
				case 0:
					// check if a file of the same name has been uploaded
					$success = false;
					if (!file_exists($directory . $file)) {
						// move the file to the upload folder and rename it
						if (!empty($thumb_width)){
							create_thumb($img_name, $file, $thumb_width);
						}
						$success = move_uploaded_file($_FILES[$img_name]['tmp_name'], $directory . $file);
					} else {
						$result = 'A file of the same name already exists.';
					}

					if ($success) {
						$result = "$file uploaded successfully.";
						$ret = $file;
					} else {
						$result = "Error uploading $file. Please try again.";
					}
					break;
				case 3:
				case 6:
				case 7:
				case 8:
					$result = "Error uploading $file. Please try again.";
					break;
				case 4:
					$result = "You didn't select a file to be uploaded.";
			}
		}
		else {
			$result = "$file is either too big or not an image.";
		}
		// error_log($result);
		// echo $result;
		return $ret;
	}
	else return false;
}

function create_thumb($img_name, $thumb_name, $thumb_width){

	if($_FILES[$img_name]['type'] == 'image/jpeg' || $_FILES[$img_name]['type'] == 'image/pjpeg')
	{
		$uploadedfile = $_FILES[$img_name]['tmp_name'];
		$src = imagecreatefromjpeg($uploadedfile);
	}
	else if($_FILES[$img_name]['type'] == 'image/png')
	{
		$uploadedfile = $_FILES[$img_name]['tmp_name'];
		$src = imagecreatefrompng($uploadedfile);
	}
	else
	{
		$src = imagecreatefromgif($uploadedfile);
	}

	list($width,$height)=getimagesize($uploadedfile);

	//  NEW SIZE //
	$newwidth= $thumb_width;
	$newheight=($height/$width)*$newwidth;
	$tmp=imagecreatetruecolor($newwidth,$newheight);

	imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);

	$filename = site_config::get_value("upload_thumbs_folder") . $thumb_name;

	imagejpeg($tmp,$filename,100);
	//imagejpeg($tmp1,$filename1,100);

	imagedestroy($src);
	imagedestroy($tmp);
	//imagedestroy($tmp1);
}

function upload_with_thumb($img_name, $thumb_width){
	return upload_image($img_name, $thumb_width);
}

function remove_image_with_thumb($image_name){
	if (!emptY($image_name)/*  && file_exists(site_config::get_value("upload_images_folder").$image_name) */){
		unlink(site_config::get_value("upload_images_folder").$image_name);

		if (file_exists(site_config::get_value("upload_thumbs_folder").$image_name))
			unlink(site_config::get_value("upload_thumbs_folder").$image_name);

		return true;
	}
	return false;
}
//		END IMAGE UPLOAD
////////////////////////////////////////////////////////////////////////////////////////////////////
function clear_string($string){
	$con = db_con::get_con();

	if (get_magic_quotes_gpc()){
		$string = stripslashes($string);
	}
	$string = $con->real_escape_string($string);
	return $string;
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}

function random_string($length = 10) {
    // $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function get_tranzila_response_text($response_code){
	$tranzila_codes = array(
		'000' => 'עיסקה תקינה',
		'001' => 'חסום,החרם כרטיס',
		'002' => 'גנוב,החרם כרטיס',
		'003' => 'התקשר לחברת האשראי',
		'004' => 'סירוב',
		'005' => 'מזויף,החרם כרטיס',
		'006' => 'ת.ז או CVV שגויים',
		'007' => 'חובה להתקשר לחברת האשראי',
		'008' => 'תקלה בבניית מפתח גישה לקובץ חסומים',
		'009' => 'לא הצליח להתקשר,התקשר לחברת האשראי',
		'010' => 'תוכנית הופסקה על פי הוראת המפעיל (ESC)',
		'011' => 'אין אישור סולק למטבע',
		'012' => 'אין אישור למותג למטבע',
		'013' => 'אין אישור לעסקת פריקה /טעינה.',
		'014' => 'כרטיס לא נתמך.',
		'015' => 'אין התאמה בין המספר שהוקלד לפס המגנטי',
		'017' => 'לא הוקלדו 4 ספרות אחרונות',
		'019' => 'רשומה בקובץ INTֹIN קצרה מ-16 תווים',
		'020' => 'קובץ קלט (INTֹIN) לא קיים',
		'021' => 'קובץ חסומים (NEG) לא קיים או לא מעודכן - בצע שידור או בקשה לאישור עבור כל עיסקה',
		'022' => 'אחד מקבצי פרמטרים או ווקטורים לא קיים',
		'023' => 'קובץ תאריכים (DATA) לא קיים',
		'024' => 'קובף אתחול (START) לא קיים',
		'025' => 'הפרש בימים בקליטת חסומים גדול מדי-בצע שידור או בקשה לאישור עבור כל עיסקה',
		'026' => 'הפרש דורות בקליטת חסומים גדול מידי-בצע שידור או בקשה לאישור עבור כל עיסקה',
		'027' => 'כאשר לא הוכנס פס מגנטי כולו הגדר עיסקה כעיסקה טלפונית או כעיסקת חתימה בלבד',
		'028' => 'מספר מסוף מרכזי לא הוכנס למסוף המוגדר לעבודה כרב ספק',
		'029' => 'מספר מוטב לא הוכנס למסוף המוגדר לעבודה כרב מוטב',
		'030' => 'מסוף שאינו מעודכן כרב ספק/רב מוטב והוקלד מספר ספק/מספר מוטב',
		'031' => 'מסוף מעודכן כרב ספק והוקלד גם מספר מוטב',
		'032' => 'תנועות ישנות בצע שידור או בקשה לאישור עבור כל עיסקה',
		'033' => 'כרטיס לא תקין',
		'034' => 'כרטיס לא רשאי לבצע במסוף זה או אין אישור לעיסקה כזאת',
		'035' => 'כרטיס לא רשאי לבצע עיסקה עם סוג אשראי זה',
		'036' => 'פג תוקף',
		'037' => 'שגיאה בתשלומים -סכום עיסקה צריך להיות שווה תשלום ראשון +(תשלום קבוע כפול מספר תשלומים)',
		'038' => 'לא ניתן לבצע עיסקה מעל תקרה לכרטיס לאשראי חיוב מיידי',
		'039' => 'סיפרת בקורת לא תקינה',
		'040' => 'מסוף שמוגדר כרב מוטב הוקלד מספר ספק',
		'041' => 'מעל תקרה כאשר קובץ הקלט מכיל J3 או J2 או J1 (אסור להתקשר)',
		'042' => 'כרטיס חסום בספק כאשר קובץ הקלט מכיל J3 או J2 או J1 (אסור להתקשר)',
		'043' => 'אקראית כאשר קובץ הקלט מכיל J1 (אסור להתקשר)',
		'044' => 'מסוף לא רשאי לבקש אישור ללא עיסקה (J5)',
		'045' => 'מסוף לא רשאי לבקש אישור ביוזמת קמעונאי(J6)',
		'046' => 'מסוף חייב לבקש אישור כאשר קובץ הקלט מכיל J3 או J2 או J1 (אסור להתקשר)',
		'047' => 'חייב להקליד מספר סודי,כאשר קובץ הקלט מכיל J3 או J2 או J1 (אסור להתקשר)',
		'051' => 'מספר רכב לא תקין',
		'052' => 'מד מרחק לא הוקלד',
		'053' => 'מסוף לא מוגדר כתחנת דלק(הועבר כרטיס דלק או קוד עיסקה לא מתאים)',
		'057' => 'לא הוקלד מספר תעודת זהות',
		'058' => 'לא הוקלד CVV2',
		'059' => 'לא הוקלדו מספר תעודת הזהות וה-CVV2',
		'060' => 'צרוף ABS לא נמצא בהתחלת נתוני קלט בזיכרון',
		'061' => 'מספר כרטיס לא נמצא או נמצא פעמיים',
		'062' => 'סוג עיסקה לא תקין',
		'063' => 'קוד עיסקה לא תקין',
		'064' => 'סוג אשראי לא תקין',
		'065' => 'מטבע לא תקין',
		'066' => 'קיים תשלום ראשון ו.או תשלום קבוע לסוג אשראי שונה מתשלומים',
		'067' => 'קיים מספר תשלומים לסוג אשראי שאינו דורש זה',
		'068' => 'לא ניתן להצמיד לדולר או למדד לסוג אשראי שונה מתשלומים',
		'069' => 'אורך הפס המגנטי קצר מידי',
		'070' => 'לא מוגדר מכשיר להקשת מספר סודי',
		'071' => 'חובה להקליד מספר סודי',
		'072' => 'קכח לא זמין-העבר בקורא מגנטי',
		'073' => 'הכרטיס נושא שבב ויש להעבירו דרך הקכח',
		'074' => 'דחייה-כרטיס נעול',
		'075' => 'דחייה-פעולה עם קכח לא הסתיימה בזמן הראוי',
		'076' => 'דחייה-נתונים אשר התקבלו מקכח אינם מוגדרים במערכת',
		'077' => 'הוקש מספר סודי שגוי',
		'079' => 'מטבע לא קיים בוקטור 59.',
		'080' => 'הוכנס קוד מועדון לסוג אשראי לא מתאים',
		'090' => 'עסקת ביטול אסורה בכרטיס. יש לבצע עסקת טעינה.',
		'091' => 'עסקת ביטול אסורה בכרטיס. יש לבצע עסקת פריקה.',
		'092' => 'עסקת ביטול אסורה בכרטיס. יש לבצע עסקת זיכוי.',
		'099' => 'לא מצליח לקרוא/לכתוב/לפתוח קובץ TRAN',
		'100' => 'לא קיים מכשיר להקשת קוד סודי',
		'101' => 'אין אישור מחברת האשראי לעבודה',
		'106' => 'למסוף אין אישור לביצוע שאילתא לאשראי חיוב מיידי',
		'107' => 'סכום העיסקה גדול מידי-חלק במספר העיסקאות',
		'108' => 'למסוף אין אישור לבצע עסקאות מאולצות',
		'109' => 'למסוף אין אישור לכרטיס עם קוד השרות 587',
		'110' => 'למסוף אין אישור לכרטיס חיוב מיידי',
		'111' => 'למסוף אין אישור לעיסקה בתשלומים',
		'112' => 'למסוף אין אישור לעיסקה טלפון/חתימה בלבד בתשלומים',
		'113' => 'למסוף אין אישור בעיסקה טלפונית',
		'114' => 'למסוף אין אישור לעיסקה חתימה בלבד',
		'115' => 'למסוף אין אישור לעיסקה בדולרים',
		'116' => 'למסוף אין אישור לעסקת מועדון',
		'117' => 'למסוף אין אישור לעיסקת כוכבים/נקודות מיילים',
		'118' => 'למסוף אין אישור לאשראי ישראקרדיט',
		'119' => 'למסוף אין אישור לאשראי אמקס קרדיט',
		'120' => 'למסוף אין אישור להצמדה לדולר',
		'121' => 'למסוף אין אישור להצמדה למדד',
		'122' => 'למסוף אין אישור להצמדה למדד לכרטיסי חוץ לארץ',
		'123' => 'למסוף אין אישור לעסקת כוכבים /נקודות/מיילים/ לסווג אשראי זה',
		'124' => 'למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיס ישרכארט',
		'125' => 'למסוף אין אישור לאשראי קרדיט בתשלומים לכרטיסי אמקס',
		'126' => 'למסוף אין אישור לקוד מועדון זה',
		'127' => 'למסוף אין אישור לעסקת חיוב מיידי פרט לכרטיסי חיוב מיידי',
		'128' => 'למסוף אין אישור לקבל כרטיסי ויזה אשר מתחילים ב-3',
		'129' => 'למסוף אין אישור לבצע עסקת זכות מעל תקרה',
		'130' => 'כרטיס לא רשאי לבצע עסקת מועדון',
		'131' => 'כרטיס לא רשאי לבצע עסקת כוכבים/נקודות/מיילים',
		'132' => 'כרטיס לא רשאי לבצע עסקאות בדולרים(רגילות או טלפוניות(',
		'133' => 'כרטיס לא תקף על פי רשימת כרטיסים תקפים של ישרכארט',
		'134' => 'כרטיס לא תקין על פי הגדרות המערכת(VECTOR1 של ישראכרט)-מספר הספרות בכרטיס שגוי',
		'135' => 'כרטיס לא רשאי לבצע עסקאות דולריות על פי הגדרת המערכת (VECTOR1 של ישרכארט)',
		'136' => 'הכרטיס שייך לקבוצת כרטיסים אשר אינה רשאית לבצע עיסקאות על פי הגדרת המערכת(VECTOR20 של ויזה)',
		'137' => 'קידומת הכרטיס (7 ספרות) לא תקפה על פי הגדרת המערכת(VECTOR 21 של דיינרס)',
		'138' => 'כרטיס לא רשאי לבצע עסקאות בתשלומים על פי רשימת כרטיסים תקפים של ישרכארט',
		'139' => 'מספר תשלומים גדול מידי על פי רשימת כרטיסים תקפים של ישראכרט',
		'140' => 'כרטיסי ויזה ודיינרס לא רשאים לבצע עיסקאות מועדון בתשלום',
		'141' => 'סידרת כרטיסים לא תקפה על פי הגדרת המערכת(VECTOR5 של ישראכרט)',
		'142' => 'קוד שרות לא תקף על פי הגדרת המערכת (VECTOR7 של ישראכרט)',
		'143' => 'קידומת הכרטיס (2 ספרות) לא תקפה על פי הגדרת המערכת)VECTOR7 של ישראכרט(',
		'144' => 'קוד שרות לא תקף על פי הגדרת המערכת (VECTOR12 של ויזה)',
		'145' => 'קוד שרות לא תקף על פי הגדרת המערכת (VECTOR13 של ויזה)',
		'146' => 'לכרטיס חיוב מיידי אסור לבצע עסקת זכות',
		'147' => 'כרטיס לא רשאי לבצע עיסקאות בתשלומים על פי וקטור 31 של לאומיקארד',
		'148' => 'כרטיס לא רשאי לבצע עיסקאות טלפוניות וחתימה בלבד על פי וקטור 31 של לאומיקארד',
		'149' => 'כרטיס אינו רשאי לבצע עיסקאות טלפוניות על פי וקטור 31 של לאומיקארד',
		'150' => 'אשראי לא מאושר לכרטיס חיוב מיידי',
		'151' => 'אשראי לא מאושר לכרטסי חול',
		'152' => 'קוד מועדון לא תקין',
		'153' => 'כרטיס לא רשאי לבצע עסקאות גמיש (עדיף/30+(',
		'154' => 'כרטיס לא רשאי לבצע עסקאות חיוב מיידי על פי הגדרת המערכת )VECTOR21 של דיינרס(',
		'155' => 'סכום לתשלום בעסקת קרדיט קטן מידי',
		'156' => 'מספר תשלומים לעסקת קרדיט לא תקין',
		'157' => 'תקרה 0 לסוג כרטיס זה בעיסקה עם אשראי רגיל או קרדיט',
		'158' => 'תקרה 0 לסוג כרטיס זה בעיסקה עם אשראי חיוב מיידי',
		'159' => 'תקרה 0 לסוג כרטיס זה בעסקת חיוב מיידי בדולרים',
		'160' => 'תקרה 0 לסוג כרטיס זה בעיסקה טלפונית',
		'161' => 'תקרה 0 לסוג כרטיס זה בעסקת זכות',
		'162' => 'תקרה 0 לסוג כרטיס זה בעסקת תשלומים',
		'163' => 'כרטיס אמריקן אקספרס אשר הונפק בחוץ לארץ לא רשאי לבצע עסקאות תשלומים',
		'164' => 'כרטיס JCB רשאי לבצע עסקאות רק באשראי רגיל',
		'165' => 'סכום בכוכבים/נקודות/מיילים גדול מסכום העיסקה',
		'166' => 'כרטיס מועדון לא בתחום של המסוף',
		'167' => 'לא ניתן לבצע עסקת כוכבים/נקודות /מיילים בדולרים',
		'168' => 'למסוף אין אישור לעיסקה דולרית עם סוג אשראי זה',
		'169' => 'לא ניתן לבצע עסקת זכות עם אשראי שונה מהרגיל',
		'170' => 'סכום הנחה בכוכבים/נקודות/מיילים גדול מהמותר',
		'171' => 'לא ניתן לבצע עיסקה מאולצת לכרטיס/אשראי חיוב מיידי',
		'172' => 'לא ניתן לבצע עיסקה קודמת|(עיסקה זכות או מספר כרטיס אינו זהה)',
		'173' => 'עיסקה כפולה',
		'174' => 'למסוף אין אישור להצמדה למדד לאשראי זה',
		'175' => 'למסוף אין אישור להצמדה לדולר לאשראי זה',
		'176' => 'כרטיס אינו תקף על פי הגדרת מערכת (וקטור 1 של ישראכרט)',
		'177' => 'בתחנות דלק לא ניתן לבצע שרות עצמי אלא שרות עצמי בתחנות דלק',
		'178' => 'אסור לבצע עיסקת זכות בכוכבים/נקודות/מיילים',
		'179' => 'אסור לבצע עיסקת זכות בדולר בכרטיס תייר',
		'180' => 'בכרטיס מועדון לא ניתן לבצע עיסקה טלפונית',
		'200' => 'שגיאה יישומית',
		'700' => 'עסקה ניסיונית מאושרת',
		'701' => 'מספר בנק לא תקין',
		'702' => 'מספר סניף לא תקין',
		'703' => 'מספר חשבון לא תקין',
		'704' => 'מספרי בנק/סניף/חשבון לא תקינים',
		'705' => 'שגיאת ביישום',
		'706' => 'לא הוגדרה ספריה עבור הסוחר',
		'707' => 'קובץ הגדרות לא קיים לסוחר',
		'708' => 'סכום אפס או שלילי',
		'709' => 'קובץ הגדרות לא תקין',
		'710' => 'תאריך לא תקין',
		'711' => 'תקלה בבסיס הנתונים',
		'712' => 'פרמטר חסר או מוסד לא קיים',
		'800' => 'עסקה בוטלה',
		'900' => '3D Secure לא תקין',
		'903' => 'חשד להונאה',
		'951' => 'פרטי עסקה שגוים',
		'952' => 'לא בוצע תשלום',
		'954' => 'שגיאה בביצוע התשלום',
		'955' => 'שגיאה בסטטוס תשלום',
		'959' => 'ביצוע התשלום נכשל'
	);

	if (!empty($response_code) && !empty($tranzila_codes[$response_code])){
		return $tranzila_codes[$response_code];
	}
	return "שגיאה כללית";
}

function find_key_value_pair($array, $key, $value){
	if (!empty($array) && !empty($array[$key]) && $array[$key] == $value)
		return true;
	return false;
}

function send_post_request($url, $data){
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data),
		),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	return $result;
}

function get_meta($object, $meta_type){

	if (!empty($object)){
		switch ($meta_type){
			case "meta_title":
				if (!emptY($object->meta_title))
					return $object->meta_title->to_string();
				if (!emptY($object->name))
					return $object->name->to_string();
			break;
			case "meta_description":
				if (!emptY($object->meta_description))
					return $object->meta_description->to_string();
			break;
			case "meta_keywords":
				if (!emptY($object->meta_keywords))
					return $object->meta_keywords->to_string();
			break;
		}
	}

	return "";
}
function serialize_for_db($data){
	if ($data){
		return base64_encode(serialize($data));
	}
	return "";
}

function unserialize_from_db($data){
	if ($data){
		return unserialize(base64_decode($data));
	}
	return "";
}

function full_cur_url()
{
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}

function strleft($s1, $s2) { return substr($s1, 0, strpos($s1, $s2)); }

function printMessage($messageName){
	global $site_message;
    // error_log(print_r($site_message,1));
    //error_log(print_r("from login",1));

	if (!empty($site_message[$messageName])){
		$message_type = 'warning';
		$message_text = $site_message[$messageName]['text'];

		if (!empty($site_message[$messageName]['type']))
			$message_type = $site_message[$messageName]['type'];

		switch ($message_type){
			case 'success':
				?>
				<div class="alert alert-success">
					<span class="icon medium" data-icon="C"></span>
					<?=$message_text?>
					<a href="#close" class="icon close" data-icon="x"></a>
				</div>
				<?
			break;
			case 'warning':
				?>
				<div class="alert alert-warning">
					<span class="icon medium" data-icon="!"></span>
					<?=$message_text?>
					<a href="#close" class="icon close" data-icon="x"></a>
				</div>
				<?
			break;
			case 'error':
				?>
				<div class="alert alert-danger">
					<span class="icon medium" data-icon="X"></span>
					<?=$message_text?>
					<a href="#close" class="icon close" data-icon="x"></a>
				</div>
				<?
			break;
			default:
				?>
				<div class="alert alert-warning">
					<span class="icon medium" data-icon="!"></span>
					<?=$message_text?>
					<a href="#close" class="icon close" data-icon="x"></a>
				</div>
				<?
			break;
		}
	}
}
?>
