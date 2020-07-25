<?php
$engine = new engine();

//if(!isset($_GET['m'])) {
   // $cotactm = htmlentities("");
//} else {
   // $cotactm = htmlentities($_GET['m']);
//}

//switch($cotactm) {
        //case "1":
       // $contactchoice = "plugin/admin/pages/message/message.php";
       // break;
        
        //case "2":
        $contactchoice = "plugin/admin/pages/message/log.php";
        //break;

        
        //default:
       // $contactchoice = "plugin/admin/pages/message/message.php";
     //   break;

//};

//if ($cotactm == "1" || $cotactm == "") {$m = "activemenu";}else{$m = "profilebg";}
//if ($cotactm == "2") {$s = "activemenu";}else{$s = "profilebg";}
?>

<div  class="profilebg" style="border:solid 1px #DDDDDD; border-bottom:none; overflow:hidden; padding:3px 5px;">System Logs </div>

<?php
	include ($contactchoice);
?>
