<?php 
$engine = new engine();
if(!$engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};
?>

<div style="width:100%;">
<div class="sitewidth" style="max-width:800px; margin-left:auto; margin-right:auto; padding:5px 0px; overflow:hidden;">

<?php
	include "invite/yahoo/yahoo_class.php";
?>


</div></div>