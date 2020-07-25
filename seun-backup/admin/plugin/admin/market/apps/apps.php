<?php
	if (!isset($_GET['n'])) {
    $getn = htmlentities("");
} else {
    $getn = htmlentities($_GET['n']);
}
switch ($getn) {
            case "1":
                $npage = "plugin/admin/market/apps/home.php";
                break;

            case "2":
                $npage = "plugin/admin/market/apps/manage.php";
                break;

            default:
                $npage = "plugin/admin/market/apps/home.php";
                break;
}
?>

<div style="overflow: hidden; padding:0px 30px;"><?php	include $npage;?></div>