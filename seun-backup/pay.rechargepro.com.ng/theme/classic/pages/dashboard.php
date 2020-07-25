<?php
$engine = new engine();
if(!$engine->get_session("quickpayid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};

	$quickpayrole = $engine->get_session("quickpayrole"); 
?>

<div style="clear: both;"></div>
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px;">



<script type="text/javascript">
function call_page(Id){
    window.location.href = "/index#"+Id;
}
</script>
<style type="text/css">
.menud{font-size:200%;}
.menuc{text-align:center; font-size:120%; margin-bottom: 20px;}
</style>
<div style="float:left; z-index:1; position: relative; color: #214673; background-color:#EFF2FA; border: solid 1px #8798CB; margin-top:40px; width:10%;" class="radious5">
<div class="menuc" style="margin-top: 40px;"><a href="/dashboard&h=history"><div class="menud fas fa-history"></div><br />History</a></div>
<?php
if($quickpayrole < 3){	
?>
<div class="menuc"><a href="/dashboard&h=agent"><div class="menud fas fa-user-tie"></div><br />Agent</a></div>
<div class="menuc"><a href="/dashboard&h=report"><div class="menud fas fa-chalkboard-teacher"></div><br />Report</a></div>
<?php
	}
?>
<div class="menuc"><a href="/dashboard&h=setting"><div class="menud fas fa-cogs"></div><br />Settings</a></div>
</div>



<div style="float: right; overflow: hidden; width:85%; padding-top:30px;">
<?php
$h = "";
if(isset($_REQUEST['h'])){$h = htmlentities($_REQUEST['h']);}
switch ($h){ 
	case "agent": $include = "agent.php";
	break;

	case "report": $include = "payment_daily.php";
	break;

	case "history": $include = "history.php";
	break;
    
    case "setting": $include = "setting.php";
	break;

	default :$include = "history.php";
}
	include $include;
?>
<div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>