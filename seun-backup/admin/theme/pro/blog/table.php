<?php
require "../../../engine.autoloader.php";
require "../../../plugin/parking_core/parking_core.php";

$row = $engine->db_query("SELECT paymentdate FROM revenue_payment_log ORDER BY transactionid DESC LIMIT 1",array()); 
$today = date("Y-m-d", strtotime("+0 day", strtotime($row[0]['paymentdate'])));

  $range = $engine->Dayrange($today);
  $start = $range['monthstart'];
  $end = date("Y-m-d 23:59:59", strtotime("+0 day", strtotime($range['monthend'])));
  
  
$todaystart = $today;
$todayend = date("Y-m-d 23:59:59", strtotime("+0 day", strtotime($today)));

$monthstart = $start;
$monthend = $end;

$yearstart = date("Y-01-01", strtotime("+0 day", strtotime($today)));
$yearend = date("Y-12-31 23:59:59", strtotime("+0 day", strtotime($yearstart)));
?>
<style type="text/css">
.content{
	overflow: auto;
	position: relative;
	padding:1%; 
	max-height: 400px;
	-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;
}
.content img{
	margin: 0;
	-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;
	padding: 3px;
}
</style>
<link rel="stylesheet" href="theme/diamond_bank/scrollbar/jquery.mCustomScrollbar.css"/>
<script src="theme/diamond_bank/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
<script>
		$.mCustomScrollbar.defaults.scrollButtons.enable=true; //enable scrolling buttons by default
		
        $(document).ready(function() {
				$(".scrollbar").mCustomScrollbar({
					/* keyboard default options */
                    theme:"inset-2-dark",
					keyboard:{
						enable:true,
						scrollType:"stepless",
						scrollAmount:"auto"
					}
				});
			})	
		
        
	</script>

<div style="overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #FFF;  background: #4c4f53; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #4c4f53; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> Breakdown by revenue stream</header>


<div class="shadow radious3" style="float: left; width:15.5%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> SANITATION</header>
<div class="content scrollbar" style="">
<?php
	$row = $engine->db_query("SELECT SUM(revenue_payment_log.amount) AS sum, revenue_account.house_category FROM revenue_payment_log JOIN revenue_account ON revenue_account.account = revenue_payment_log.account WHERE  revenue_payment_log.itemid = ? GROUP BY revenue_account.house_category ORDER BY sum DESC",array("SANITATION LEVY")); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $i = rand(1,8);
	   echo '<div style="overflow:hidden;">
       <div style="float:left; width:72%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['house_category'].'"><img src="theme/diamond_bank/images/balls/'.$i.'.png" style="verticle-align:middle;" width="16" /> '.$row[$dbc]['house_category'].' </div>
       <div style="float:right; width:25%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['sum'].'">&raquo; '.$row[$dbc]['sum'].'</div>
       </div>';
	}
?>
</div></div>
<!-- ----------------------------- -->
<div class="shadow radious3" style="float: left; width:15.5%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> BUSINESS PREMISES</header>
<div class="content scrollbar" style="">
<?php
	$row = $engine->db_query("SELECT SUM(revenue_payment_log.amount) AS sum, revenue_account.house_category FROM revenue_payment_log JOIN revenue_account ON revenue_account.account = revenue_payment_log.account WHERE  revenue_payment_log.itemid = ? GROUP BY revenue_account.house_category ORDER BY sum DESC",array("REGISTRATION/RENEWAL OF BUSINESS PREMISES (BUREAU OF IGR)")); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $i = rand(1,8);
	   echo '<div style="overflow:hidden;">
       <div style="float:left; width:72%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['house_category'].'"><img src="theme/diamond_bank/images/balls/'.$i.'.png" style="verticle-align:middle;" width="16" /> '.$row[$dbc]['house_category'].' </div>
       <div style="float:right; width:25%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['sum'].'">&raquo; '.$row[$dbc]['sum'].'</div>
       </div>';
	}
?>
</div></div>
<!-- ----------------------------- -->
<div class="shadow radious3" style="float: left; width:15.5%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> DEVELOPMENT LEVY</header>
<div class="content scrollbar" style="">
<?php
	$row = $engine->db_query("SELECT SUM(revenue_payment_log.amount) AS sum, revenue_account.house_category FROM revenue_payment_log JOIN revenue_account ON revenue_account.account = revenue_payment_log.account WHERE  revenue_payment_log.itemid = ? GROUP BY revenue_account.house_category ORDER BY sum DESC",array("DEVELOPMENT LEVY")); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $i = rand(1,8);
	   echo '<div style="overflow:hidden;">
       <div style="float:left; width:72%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['house_category'].'"><img src="theme/diamond_bank/images/balls/'.$i.'.png" style="verticle-align:middle;" width="16" /> '.$row[$dbc]['house_category'].' </div>
       <div style="float:right; width:25%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['sum'].'">&raquo; '.$row[$dbc]['sum'].'</div>
       </div>';
	}
?>
</div></div>
<!-- ----------------------------- -->
<div class="shadow radious3" style="float: left; width:15.5%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> STALLAGE FEE</header>
<div class="content scrollbar" style="">
<?php
	$row = $engine->db_query("SELECT SUM(revenue_payment_log.amount) AS sum, revenue_account.house_category FROM revenue_payment_log JOIN revenue_account ON revenue_account.account = revenue_payment_log.account WHERE  revenue_payment_log.itemid = ? GROUP BY revenue_account.house_category ORDER BY sum DESC",array("STALLAGE FEE")); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $i = rand(1,8);
	   echo '<div style="overflow:hidden;">
       <div style="float:left; width:72%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['house_category'].'"><img src="theme/diamond_bank/images/balls/'.$i.'.png" style="verticle-align:middle;" width="16" /> '.$row[$dbc]['house_category'].' </div>
       <div style="float:right; width:25%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['sum'].'">&raquo; '.$row[$dbc]['sum'].'</div>
       </div>';
	}
?>
</div></div>
<!-- ----------------------------- -->
<div class="shadow radious3" style="float: left; width:15.5%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> TRADERS TAX</header>
<div class="content scrollbar" style="">
<?php
	$row = $engine->db_query("SELECT SUM(revenue_payment_log.amount) AS sum, revenue_account.house_category FROM revenue_payment_log JOIN revenue_account ON revenue_account.account = revenue_payment_log.account WHERE  revenue_payment_log.itemid = ? GROUP BY revenue_account.house_category ORDER BY sum DESC",array("TRADERS TAX")); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $i = rand(1,8);
	   echo '<div style="overflow:hidden;">
       <div style="float:left; width:72%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['house_category'].'"><img src="theme/diamond_bank/images/balls/'.$i.'.png" style="verticle-align:middle;" width="16" /> '.$row[$dbc]['house_category'].' </div>
       <div style="float:right; width:25%; overflow:hidden; white-space: nowrap;" title="'.$row[$dbc]['sum'].'">&raquo; '.$row[$dbc]['sum'].'</div>
       </div>';
	}
?>
</div></div>
<!-- ----------------------------- -->
<div class="shadow radious3" style="float: left; width:15.5%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> VIO FUNCTIONS</header>
<div class="content scrollbar" style="">-</div></div>
<!-- ----------------------------- -->

</div>