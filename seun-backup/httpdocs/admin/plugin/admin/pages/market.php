<?php
$generalid = 1;
if (!isset($_GET['m'])) {
    $getm = htmlentities("");
} else {
    $getm = htmlentities($_GET['m']);
}
switch ($getm){
            case "2":
                $ppage = "plugin/admin/market/widget/widget.php";
                $addpage = '<img src="plugin/admin/market/images/12.png" />';
                $headpage = 'plugin/admin/market/head/widget.php';
                break;

            case "1":
                $ppage = "plugin/admin/market/apps/apps.php";
                $addpage = '<img src="plugin/admin/market/images/apptop.png" />';
                $headpage = 'plugin/admin/market/head/apps.php';
                break;
                
                
            default:
                $ppage = "plugin/admin/market/apps/apps.php";
                $addpage = '<img src="plugin/admin/market/images/apptop.png" />';
                $headpage = 'plugin/admin/market/head/apps.php';
                break;
}
?>
<link href="plugin/admin/market/css/main.css" rel="stylesheet" type="text/css" />
<link href="plugin/admin/market/css/frontpage.css" rel="stylesheet" type="text/css" />


<div style="overflow:hidden; ">

<?php
	if ($getm == "1" || $getm == "") {$activepage1b="activemenu whitelink";}else{$activepage1b="blacklink";}
    if ($getm == "2") {$activepage2b="activemenu whitelink";}else{$activepage2b="blacklink";}
?>

<div  class="tsprite" style="border:solid 1px #DDDDDD; background-position:0px -485px; background-repeat: repeat-x; border-bottom:none; overflow:hidden; padding:3px 5px;">
<div class="<?php echo $activepage1b;?>" style="font-size:11px; font-weight:bold;  float:left; position:relative; padding:5px 70px;"><a href="admin&p=market&m=1">Plugins</a></div>
<div class="<?php echo $activepage2b;?>" style="font-size:11px; font-weight:bold; margin-left:5px; float:left; padding:5px 70px;?>"><a href="admin&p=market&m=2">Widgets</a></div>
<div class="<?php echo $activepage2b;?>" style="font-size:11px; font-weight:bold; margin-left:5px; float:left; padding:5px 70px;?>"><a href="admin&p=market&m=2">My Downloads</a></div>
</div>
</div>



<div class="submenu" style="overflow: hidden; padding:5px;">
<?php include $headpage;?>
</div>



<div style="background-color:white; border: solid 1px #F0F0F0; border-top: none;">
<div style="background-color:white; text-align:right; padding:20px; padding-bottom:10px;">
<?php	echo $addpage;?>
</div>

<?php
	include $ppage;
?>
</div>