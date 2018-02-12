<?
	session_start();
	header("Cache-Control: max-age=290304000"); //
	spl_autoload_register(function ($class) {
		$parts = explode("_", $class);
        if(count($parts)>2 && $parts[0] !== "product"){
            if(count($parts)>3)
                include_once 'includes/classes/' . $parts[0].'_'.$parts[1].'_'.$parts[2] . '.php';
            else{
                include_once 'includes/classes/' . $parts[0].'_'.$parts[1] . '.php';
            }
        }
        else
			include_once 'includes/classes/' . $parts[0] . '.php';
	});

	include "includes/config.php";
	include "includes/functions.php";
	// include "includes/variables.php";
	include "includes/logic.php";

	// include $main_template;
?>
