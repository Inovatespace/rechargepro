<?php
if(!isset($_COOKIE['language'])){$systemlanguage ="en";} else {$systemlanguage = $_COOKIE['language'];};
switch($systemlanguage){
	case "en":  $chaset = "UTF-8"; $flang="English"; $dbchaset="UTF-8"; $language ="en"; break;
	case "fr":  $chaset = "UTF-8"; $flang="French"; $dbchaset="UTF-8"; $language ="fr"; break;
	case "es":  $chaset = "UTF-8"; $flang="Spanish"; $dbchaset="UTF-8"; $language ="es"; break;
    case "swc":  $chaset = "UTF-8"; $flang="Swahili"; $dbchaset="UTF-8"; $language ="swc"; break;
	default:   $chaset = "UTF-8"; $flang="English"; $dbchaset="UTF-8"; $language ="en"; break;
}
?>