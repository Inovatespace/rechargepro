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

$maparray = array();
$row = $engine->db_query("SELECT SUM(revenue_payment_log.amount) AS sum, revenue_account.house_category, COUNT(revenue_payment_log.amount) AS thecount FROM revenue_payment_log JOIN revenue_account ON revenue_account.account = revenue_payment_log.account WHERE  revenue_payment_log.itemid = ? AND revenue_payment_log.paymentdate BETWEEN ? AND ? GROUP BY revenue_account.house_category ORDER BY sum DESC",array($type,$thedatestart,$thedateend)); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $maparray[$row[$dbc]['house_category']] = array($row[$dbc]['sum'],$row[$dbc]['thecount']);
	}
?>
<script type="text/javascript">
function calldetails2(senttype){
   $("#mdril").append('<img class="chartload" style="position:absolute; top:10px; left:10px;" src="images/loading.gif" width="124" height="124" />');
            $.ajax({
                type: "POST",
                url: "theme/diamond_bank/blog/drill2.php",
                data: "what=<?php echo $what;?>&today=<?php echo $today;?>&type="+senttype,
                cache: false,
                success: function (html) {
                    $('.chartload').remove();
                    $("#mdril").hide();
                    $("#mdril2").show();
$("#mdril2").html(html);


 
setTimeout(function() {
$("#mdril2b").append('<img class="chartload" onclick="$(\'#mdril\').show(); $(\'#mdril2\').hide();"  style="z-index:999; position:absolute; top:0px; left:25px; cursor:pointer;" src="images/go-back-icon.png" width="25" />');

 $("#mdril").append('<img class="chartload" onclick="$(\'#stat\').show(); $(\'.chartload\').remove(); $(\'#stat2\').hide();" style="z-index:999; position:absolute; top:45px; left:25px; cursor:pointer;" src="images/go-back-icon.png" width="40" />');
 
}, 500);

                }
        });  
}


$(function () {
    $('#mdril').highcharts({
        chart: {
            zoomType: 'xy'
        },
            exporting: {
                enabled: false
            },	credits: {
			enabled: false
		},
        title: {
            text: '<?php echo $what;?> Statistical breakdown on <?php echo $type;?>'
        },
        subtitle: {
            text: ''
        },
        xAxis: [{
            categories: [<?php  foreach($maparray AS $key => $value){echo "'".$key."',";} ?>],
            crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'INCOME',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'NUMBER OF BUILDINGS',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
            align: 'left',
            x: 120,
            verticalAlign: 'top',
            y: 100,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [{
            name: 'INCOME',
            type: 'column',
            yAxis: 1,
            data: [<?php  foreach($maparray AS $key => $value){echo $value[0].",";} ?>],
            tooltip: {
                valueSuffix: ''
            },                    cursor: 'pointer',
                      point: {
                        events: {
                            click: function(e) {
calldetails2(this.category);
                            }
                        }
                    },

        }, {
            name: 'NUMBER OF BUILDINGS',
            type: 'spline',
            data: [<?php  foreach($maparray AS $key => $value){echo $value[1].",";} ?>],
            tooltip: {
                valueSuffix: ''
            }
        }]
    });
});
</script>
<div class="shadow radious3" style="background-color:#F9F9F9;  margin:0.5%; overflow:hidden;">
<header role="heading" style="display: block; box-sizing: border-box; color: #000;  background: #fafafa; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #fafafa; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px;">
<span class="fa fa-bar-chart-o">  </span> <?php echo $what;?> Revenue Stream</header>
<div id="mdril" style="width: 99.9%; height: 400px; margin: 0px; position:relative; overflow: hidden;"></div>
<div id="mdril2" style="display:none; width: 99.9%; margin: 0px; position:relative; overflow: hidden;"></div>
</div>
<!-- dddddddddddddddddddddddddd -->
