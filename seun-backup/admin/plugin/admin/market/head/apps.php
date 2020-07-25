<?php
	if (!isset($_GET['n'])) {
    $getn = htmlentities("");
} else {
    $getn = htmlentities($_GET['n']);
}
switch ($getn) {
            case "1":
                $npage = "applications/widget/home.php";
                break;

            case "2":
                $npage = "applications/widget/manager.php";
                break;
                
            case "3":
                $npage = "applications/widget/addwidget.php";
                break;             
     
            default:
                $npage = "applications/widget/home.php";
                break;
}

if ($getn == "1" || $getn == "") {$activepage1a = 'class="blacklink"';}else{$activepage1a = 'class="coloredlink"';}
if ($getn == "2") {$activepage2a = 'class="blacklink"'; }else{$activepage2a = 'class="coloredlink"';}
if ($getn == "3") {$activepage3a = 'class="blacklink"';}else{$activepage3a = 'class="coloredlink"';}

?>
<div <?php echo $activepage1a;?> style="float: left; padding:0px 9px; border-right:1px solid #5892CD;"><a href="admin&p=market&m=1">Plugin central</a></div>
<div <?php echo $activepage2a;?> style="float: left; padding:0px 9px; border-right:1px solid #5892CD;"><a href="admin&p=market&m=1&n=2">Plugin Manager</a></div>