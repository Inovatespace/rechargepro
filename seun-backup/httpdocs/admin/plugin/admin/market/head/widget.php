<?php
	if (!isset($_GET['n'])) {
    $getn = htmlentities("");
} else {
    $getn = htmlentities($_GET['n']);
}
switch ($getn) {
            case "1":
                $npage = "plugin/admin/market/widget/home.php";
                $subtitle = "Browse Widget";
                break;

            case "2":
                $npage = "plugin/admin/market/widget/manager.php";
                $subtitle = "Manage Widget";
                break;         
     
            default:
                $npage = "plugin/admin/market/widget/home.php";
                $subtitle = "Browse Widget";
                break;
}

if ($getn == "1" || $getn == "") {$activepage1a = 'class="blacklink"';}else{$activepage1a = 'class="coloredlink"';}
if ($getn == "2") {$activepage2a = 'class="blacklink"'; }else{$activepage2a = 'class="coloredlink"';}
if ($getn == "3") {$activepage3a = 'class="activemenu"';}else{$activepage3a = 'class="coloredlink"';}

?>
<div <?php echo $activepage1a;?> style="float: left; padding:0px 9px;"><a href="admin&p=market&m=2&n=1">Widgets Home</a></div>
<div <?php echo $activepage2a;?> style="float: left; padding:0px 9px;"><a href="admin&p=market&m=2&n=2">Widget Manager</a></div>