<?php
require "../../../engine.autoloader.php";
require "../../../plugin/parking_core/parking_core.php";

$today = $_REQUEST['date'];

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


//
$daily = array();
$row = $engine->db_query("SELECT SUM(amount) AS avg, itemid FROM revenue_payment_log WHERE date BETWEEN ? AND ? GROUP BY itemid",array($todaystart,$todayend)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    if($row[$dbc]['itemid'] == "REGISTRATION/RENEWAL OF BUSINESS PREMISES (BUREAU OF IGR)"){$row[$dbc]['itemid'] = "BUSINESS PERMIT";}
    $daily[$row[$dbc]['itemid']] = $row[$dbc]['avg'];
}


$weekly = array();
$row = $engine->db_query("SELECT SUM(amount) AS avg, itemid FROM revenue_payment_log WHERE date BETWEEN ? AND ? GROUP BY itemid",array($weekstart,$weekend)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    if($row[$dbc]['itemid'] == "REGISTRATION/RENEWAL OF BUSINESS PREMISES (BUREAU OF IGR)"){$row[$dbc]['itemid'] = "BUSINESS PERMIT";}
    $weekly[$row[$dbc]['itemid']] = $row[$dbc]['avg'];
}


$monthly = array();
$row = $engine->db_query("SELECT SUM(amount) AS avg, itemid FROM revenue_payment_log WHERE date BETWEEN ? AND ? GROUP BY itemid",array($monthstart,$monthend)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    if($row[$dbc]['itemid'] == "REGISTRATION/RENEWAL OF BUSINESS PREMISES (BUREAU OF IGR)"){$row[$dbc]['itemid'] = "BUSINESS PERMIT";}
    $monthly[$row[$dbc]['itemid']] = $row[$dbc]['avg'];
}
?>
<script type="text/javascript">
function calldetailchartb(senttype,what){
$("#stat").append('<img class="chartload" style="position:absolute; top:10px; left:10px;" src="images/loading.gif" width="124" height="124" />');
            $.ajax({
                type: "POST",
                url: "theme/diamond_bank/blog/drill.php",
                data: "what="+what+"&today=<?php echo $today;?>&type="+senttype,
                cache: false,
                success: function (html) {
                    $('.chartload').remove();
                    $("#stat").hide();
                    $("#stat2").show();
$("#stat2").html(html);

setTimeout(function() {
$("#stat2").append('<img class="chartload" onclick="$(\'#stat\').show(); $(\'.chartload\').remove(); $(\'#stat2\').hide();" style="z-index:999; position:absolute; top:45px; left:25px; cursor:pointer;" src="images/go-back-icon.png" width="40" />');
}, 500);

                }
        }); 
}


$(document).ready(function () {
$('#daily').highcharts({
           	chart: {
			borderRadius: 0,
			backgroundColor: "transparent",
		},
            title: {
                text: ''
            },tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}% {point.y} Ticket</b>'
            },
            exporting: {
                enabled: false
            },	credits: {
			enabled: false
		},
            plotOptions: {
                pie: {
                     shadow: true,
                    allowPointSelect: true,
                    cursor: 'pointer',
                      point: {
                        events: {
                            click: function(e) {
calldetailchartb(this.name,"Daily");
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                    connectorColor: '#000000',
                    format: '{point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'Revenue share',
                data: [<?php  foreach($daily AS $key => $value){echo "['".$key."',".$value."],";} ?>]
            }]
        });


//////////////////////////////
var chart = $('#weekly').highcharts({
           	chart: {
			borderRadius: 0,
			backgroundColor: "transparent",
		},
            title: {
                text: ''
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}% {point.y} Ticket</b>'
            },
            exporting: {
                enabled: false
            },	credits: {
			enabled: false
		},
            plotOptions: {
                pie: {
                     shadow: true,
                    allowPointSelect: true,
                    cursor: 'pointer',
                      point: {
                        events: {
                            click: function(e) {
calldetailchartb(this.name,"weekly");
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                    connectorColor: '#000000',
                    format: '{point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'Revenue share',
                data: [<?php  foreach($weekly AS $key => $value){echo "['".$key."',".$value."],";} ?>]
            }]
        });
 



  var chart = $('#monthly').highcharts({
           	chart: {
			borderRadius: 0,
			backgroundColor: "transparent",
		},
            title: {
                text: ''
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage:.1f}% {point.y} Ticket</b>'
            },
            exporting: {
                enabled: false
            },	credits: {
			enabled: false
		},
            plotOptions: {
                pie: {
                     shadow: true,
                    allowPointSelect: true,
                    cursor: 'pointer',
                      point: {
                        events: {
                            click: function(e) {
calldetailchartb(this.name,"Monthly");
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                    connectorColor: '#000000',
                    format: '{point.percentage:.1f} %'
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'Revenue share',
                data: [<?php  foreach($monthly AS $key => $value){echo "['".$key."',".$value."],";} ?>]
            }]
        });
          
})
		</script>


<div class="shadow radious3" style="float: left; width:32%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> Daily Revenue Stream</header>

<div id="daily"></div>
</div>
<!-- dddddddddddddddddddddddddd -->

<div class="shadow radious3" style="float: left; width:32%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> Weekly Revenue Stream</header>

<div id="weekly"></div>
</div>
<!-- dddddddddddddddddddddddddd -->

<div class="shadow radious3" style="float: left; width:32.5%; background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> Monthly Revenue Stream</header>

<div id="monthly"></div>
</div>
<!-- dddddddddddddddddddddddddd -->
