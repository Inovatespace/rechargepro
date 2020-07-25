<?php
require "../../../engine.autoloader.php";
require "../../../plugin/parking_core/parking_core.php";

$what = $_REQUEST["what"]; 
$today = $_REQUEST["today"];
$type = $_REQUEST["type"];



  $range = $engine->Dayrange($today);
  $start = $range['monthstart'];
  $end = date("Y-m-d 23:59:59", strtotime("+0 day", strtotime($range['monthend'])));
  
  
$todaystart = $today;
$todayend = date("Y-m-d 23:59:59", strtotime("+0 day", strtotime($today)));

$monthstart = $start;
$monthend = $end;

$yearstart = date("Y-01-01", strtotime("+0 day", strtotime($today)));
$yearend = date("Y-12-31 23:59:59", strtotime("+0 day", strtotime($yearstart)));


$weekstart = $range['weekstart'];
$weekend = $range['weekend'];


$thedatestart = $todaystart;
$thedateend = $todayend;

if ($what == "Weekly") {
$thedatestart = $weekstart;
$thedateend = $weekend;	
}

if ($what == "Monthly") {
$thedatestart = $monthstart;
$thedateend = $monthend;	
}

if ($type == "BUSINESS PERMIT") {
$type = "REGISTRATION/RENEWAL OF BUSINESS PREMISES (BUREAU OF IGR)";	
}
?>

<script type="text/javascript">
$(document).ready(function(){$('.tunnel').tunnel();})
</script>














<div id="mdril2b" class="nInformation" style="position:relative; overflow:hidden; margin: 5px; text-align: right;">Resident that paid for <?php echo $type;?> between <?php echo $thedatestart;?> and <?php echo $thedateend;?> </div>

<div style="overflow:hidden">
<div id="containerdaily" style="float: left; width:49.5%; height:400px;">1</div>
<div id="containerweekly" style="float: right; width:49.5%; height:400px;">2</div>
</div>





<style type="text/css">
.stats{position: relative; overflow:hidden; background-color:#E7E7E7; border-bottom:1px solid white; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #DDDDDD; border-bottom:1px solid white; padding:1px;}
.stats:hover {background: #F3F3F3; color:#F9C93A;}
.inndiv{position: relative; float:left; border-right:1px solid white; padding-left:0.4%; white-space: nowrap; overflow:hidden; }
</style>

<div class="adminheader shadow" style="position: relative; overflow:hidden; border-top: 1px solid #DDDDDD; border-bottom: 1px solid #AEBEBD; margin-top:10px;">
<div class="inndiv" style="width:10%;">Residentid</div>
<div class="inndiv" style="width:16%;">Name</div>
<div class="inndiv" style="width:10%;">Mobile</div>
<div class="inndiv" style="width:12%;">Email</div>
<div class="inndiv" style="width:8%;">D O B</div>
<div class="inndiv" style="width:8%;">Sex</div>
<div class="inndiv" style="width:15%;">Street</div>
<div class="inndiv" style="width:10%;">Zone</div>
<div class="inndiv" style="width:8%;">Reg Date <img style="vertical-align: middle;" src="images/Clear Green.png" width="14" /></div>
<div class="inndiv" style="width:2%;"></div>
</div>
<div  class="outline scrollbar" style="overflow: auto; position: relative; max-height: 400px;">
<?php
$rowa = $engine->db_query("SELECT DISTINCT revenue_account.residentid, revenue_account_resident.id, revenue_account_resident.name, revenue_account_resident.mobile, revenue_account_resident.email, revenue_account_resident.dob, revenue_account_resident.sex, revenue_account_resident.address, revenue_account_resident.district, revenue_account_resident.zone, revenue_account_resident.ballance, revenue_account_resident.sms, revenue_account_resident.registered_date, revenue_account_resident.date, revenue_account_resident.sync FROM revenue_account_resident JOIN revenue_account ON revenue_account.residentid = revenue_account_resident.residentid JOIN revenue_payment_log ON revenue_payment_log.account = revenue_account.account WHERE revenue_account.house_category = ? AND revenue_payment_log.paymentdate BETWEEN ? AND ? ORDER BY name ASC",array($type,$thedatestart,$thedateend));

$rowb = $engine->db_query("SELECT DISTINCT revenue_account.residentid, revenue_account_resident.id, revenue_account_resident.name, revenue_account_resident.mobile, revenue_account_resident.email, revenue_account_resident.dob, revenue_account_resident.sex, revenue_account_resident.address, revenue_account_resident.district, revenue_account_resident.zone, revenue_account_resident.ballance, revenue_account_resident.sms, revenue_account_resident.registered_date, revenue_account_resident.date, revenue_account_resident.sync FROM revenue_account_resident JOIN revenue_account ON revenue_account.residentid = revenue_account_resident.tmp_id JOIN revenue_payment_log ON revenue_payment_log.account = revenue_account.account WHERE revenue_account.house_category = ? AND revenue_payment_log.paymentdate BETWEEN ? AND ? ORDER BY name ASC",array($type,$thedatestart,$thedateend));

$row = array_merge($rowa,$rowb);

$color=1;
$streetarray = array();
$agentarray = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$residentid = $row[$dbc]['residentid'];
 $id = $row[$dbc]['id'];
$name = $row[$dbc]['name']; 
$mobile = $row[$dbc]['mobile']; 
$email = $row[$dbc]['email']; 
$dob = $row[$dbc]['dob']; 
$sex = $row[$dbc]['sex']; 
$address = $row[$dbc]['address']; 
$district = $row[$dbc]['district']; 
$zone = $row[$dbc]['zone']; 
$ballance = $row[$dbc]['ballance']; 
$sms = $row[$dbc]['sms']; 
$registered_date = date("Y-m-d", strtotime("+0 day", strtotime($row[$dbc]['registered_date']))); 
$date = date("Y-m-d", strtotime("+0 day", strtotime($row[$dbc]['date']))); 
$sync= $row[$dbc]['sync'];

if(array_key_exists($zone,$agentarray)){$agentarray[$zone] = $agentarray[$zone]+1;}else{ $agentarray[$zone] = 1;}
if(array_key_exists($district,$streetarray)){$streetarray[$district] = $streetarray[$district]+1;}else{ $streetarray[$district] = 1;}
$c = '<img style="vertical-align: middle;" src="images/deny.png" title="unconfirmed" width="14" />';
if(!empty($residentid)){$c = '<img style="vertical-align: middle;" title="confirmed" src="images/Clear Green.png" width="14" />';}

if(empty($residentid)){$residentid = $row[$dbc]['tmp_id'];}

if($color==1){
?>
<div class="stats">
<div class="inndiv" style="width:10%;">&nbsp;<?php echo $residentid;?></div>
<div class="inndiv" style="width:16%;">&nbsp;<?php echo $name;?></div>
<div class="inndiv" style="width:10%;">&nbsp;<?php echo $mobile;?></div>
<div class="inndiv" style="width:12%;">&nbsp;<?php echo $email;?></div>
<div class="inndiv" style="width:8%;">&nbsp;<?php echo $dob;?></div>
<div class="inndiv" style="width:8%;">&nbsp;<?php echo $sex;?></div>
<div class="inndiv" style="width:15%;">&nbsp;<?php echo $district;?></div>
<div class="inndiv" style="width:10%;">&nbsp;<?php echo $zone;?></div>
<div class="inndiv" style="width:8%;">&nbsp;<?php echo $registered_date;?> <?php echo $c;?></div>
<div class="inndiv" style="width:3%; border-right:none;"><img style="margin-left:3px; cursor:pointer; float:left;" src="images/small_icons/magnify.png" width="12" class="tunnel" name="plugin/revenue_account/pages/pro/residentiddetails.php?width=800&id=<?php echo $residentid;?>" /> <img title="Edit Record" class="tunnel" name="plugin/revenue_account/pages/pro/editreg_residentid.php?width=500&id=<?php echo $id;?>" style="cursor:pointer; float:right;"  src="images/small_icons/Edit2.png" width="12" /></div>
</div>
<?php
$color=2;
}else{
?>
<div class="stats stats2">
<div class="inndiv" style="width:10%;">&nbsp;<?php echo $residentid;?></div>
<div class="inndiv" style="width:16%;">&nbsp;<?php echo $name;?></div>
<div class="inndiv" style="width:10%;">&nbsp;<?php echo $mobile;?></div>
<div class="inndiv" style="width:12%;">&nbsp;<?php echo $email;?></div>
<div class="inndiv" style="width:8%;">&nbsp;<?php echo $dob;?></div>
<div class="inndiv" style="width:8%;">&nbsp;<?php echo $sex;?></div>
<div class="inndiv" style="width:15%;">&nbsp;<?php echo $district;?></div>
<div class="inndiv" style="width:10%;">&nbsp;<?php echo $zone;?></div>
<div class="inndiv" style="width:8%;">&nbsp;<?php echo $registered_date;?> <?php echo $c;?></div>
<div class="inndiv" style="width:3%; border-right:none;"><img style="margin-left:3px; cursor:pointer; float:left;" src="images/small_icons/magnify.png" width="12" class="tunnel" name="plugin/revenue_account/pages/pro/residentiddetails.php?width=800&id=<?php echo $residentid;?>" /> <img title="Edit Record" class="tunnel" name="plugin/revenue_account/pages/pro/editreg_residentid.php?width=500&id=<?php echo $id;?>" style="cursor:pointer; float:right;"  src="images/small_icons/Edit2.png" width="12" /></div>

</div>
<?php   
$color=1;    
}
	}?>
  
</div>











<script type="text/javascript">
$(function () {
    // Build the chart
    $('#containerdaily').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            borderRadius: 0,
			backgroundColor: "transparent",
        },
            exporting: {
                enabled: false
            },	credits: {
			enabled: false
		},
        title: {
            text: 'Browser market shares. January, 2015 to May, 2015'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    },
                    connectorColor: 'silver'
                },
                    showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            data: [
                 <?php foreach($agentarray AS $key => $value){ echo "{name: '$key', y: $value },";}?> ]
        }]
    });
    
    
    
    $('#containerweekly').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            borderRadius: 0,
			backgroundColor: "transparent",
        },
            exporting: {
                enabled: false
            },	credits: {
			enabled: false
		},
        title: {
            text: 'Browser market shares. January, 2015 to May, 2015'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '{point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    },
                    connectorColor: 'silver'
                },
                    showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            data: [<?php foreach($streetarray AS $key => $value){ echo "{name: '$key', y: $value },";}?> ]
        }]
    });
});
</script>