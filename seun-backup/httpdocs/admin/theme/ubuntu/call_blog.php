<?php
include "../../engine.autoloader.php";


require '../../google/gapi.class.php';
define('ga_profile_id','170855831');
$ga = new gapi("nextcashandcarry@nextcashandcarry-175412.iam.gserviceaccount.com", "key.p12");


$ga->requestReportData(ga_profile_id,array('socialNetwork'),array('users'),null,null,date("Y-m-d", strtotime("-30 day", strtotime(date("Y-m-d")))),date("Y-m-d"));
?>
<div class="shadow" style="float:right; margin:3px; background-color: white; margin-top:10px; width: 49%;">
<div style="font-size: 150%;">Social media unique visitors</div>
<table class="tablesorter">
<tr>
  <th>Social Netwotk</th>
  <th>Users</th>
</tr>
<?php
foreach($ga->getResults() as $result){
?>
<tr>
  <td><?php echo $result ?></td>
  <td><?php echo $result->getUsers() ?></td>
</tr>
<?php
}
?>
</table>
</div>
</div>







<div style="clear: both;"></div>







<div style="overflow:hidden;">
<?php // $ga->getTotalResults()  $ga->getUsers() 
$ga->requestReportData(ga_profile_id,array('date'),array('Users'),'date',null,date("Y-m-d", strtotime("-30 day", strtotime(date("Y-m-d")))),date("Y-m-d"));
$chart = array();
foreach($ga->getResults() as $result){
 
    $y = substr((string)$result, 0,-4);
    $ma = substr((string)$result, 0,-2);
    $m = substr($ma,4);
    $d = substr((string)$result, 6);
    
    
$chart[date("M j", strtotime("+0 day", strtotime("$y-$m-$d")))] = $result->getUsers();
}
?>
<script type="text/javascript">
jQuery(document).ready(function($){

Highcharts.chart('container4', {
    chart: {
        type: 'column'
    },credits: {
enabled: false
},
    title: {
        text: 'Audience Overview'
    },
    subtitle: {
        text: 'Users'
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: ''
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: '{point.y}</b>'
    },
    series: [{
        name: 'Population',
        data: [<?php foreach($chart AS $key => $array){echo "['$key', $array],";};?>],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});

 })
</script>
<div id="container4" style="float:right; width: 64%; height: 300px; margin:3px"></div>



<div style="float: left; width:35%;">
<?php
$ga->requestReportData(ga_profile_id,array('keyword'),array('users','timeOnPage'),'-users',null,date("Y-m-d", strtotime("-30 day", strtotime(date("Y-m-d")))),date("Y-m-d"),1,10);
?>
<div class="shadow" style="margin:3px; background-color: white;">
<div style="font-size: 150%;">Top keywords (non-branded)</div>
<table  id="myTable" class="tablesorter">
<tr>
  <th>keywords</th>
  <th>Users</th>
  <th>Time On page</th>
</tr>
<?php
foreach($ga->getResults() as $result):
?>
<tr>
  <td><?php echo $result ?></td>
    <td><?php echo $result->getUsers() ?></td>
  <td><?php echo $result->getTimeOnPage() ?></td>
</tr>
<?php
endforeach
?>
</table>
</div>



</div>

</div>





















