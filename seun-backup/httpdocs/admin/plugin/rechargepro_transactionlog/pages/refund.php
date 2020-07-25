<?php
$engine = new engine();
//require "plugin/parking_core/parking_core.php";


//when pickup/delivery enter payment method
//show next or phamarcy for approved oders


if(isset($_REQUEST['today'])){
$today = $_REQUEST['today'];
}else{
$today = date("Y-m-d");    
}

?>
<script type="text/javascript">
$(document).ready(function () {

    $("#search").keyup(function () {
        var searchbox = $(this).val();
        var active = "0";
        $("input[id='active']:checked").each(function(i){
active = "1";
});
        var dataString = 'q=' + searchbox+"&active="+active;

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_transactionlog/pages/refundb.php",
                data: dataString,
                cache: false,
                success: function (html) {
                      $("#pagination").hide();
                      $("#page-content").html(html);

                }


            });
        }
        return false;

    });


});

</script>

<div class="shadow" style="margin: 10px 0px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="search" type="text" placeholder="Ref / Mobile / TransactionID" style="width: 90%;" class="input" /></div>
</div>



<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Refund Log</div></div>



<div class="profilebg" id="acholder" style="padding:10px; border:solid 1px #EEEEEE; overflow:hidden;">
<style type="text/css">
.stats{position: relative; overflow:hidden; border-bottom:1px solid #EEEEEE; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #F1F1F1; border-bottom:1px solid #DDDDDD; padding:1px;}
.stats:hover {background: #F2F2F2; color:#F9C93A;}
</style>
<div>

<?php
function rangeMonth($datestr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d 23:23:59', strtotime('last day of this month', $dt));
    return $res;
}
 
$range = rangeMonth($today);
$start = $range['start'];
$end = $range['end'];
  
$chartarray = array();


$wallet = 0;
$card = 0;
$row = $engine->db_query2("SELECT COUNT(transactionid) AS ccount, payment_method FROM rechargepro_refund WHERE transaction_date BETWEEN ? AND ? AND (payment_method = ? OR payment_method = ? )GROUP BY payment_method",array($start,$end,"1","2"));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    if($row[$dbc]['payment_method'] == "1"){
     $wallet = $row[$dbc]['ccount'];   
    }
    
    if($row[$dbc]['payment_method'] == "2"){
    $card = $row[$dbc]['ccount'];    
    }
    
    }
    
    
    
$row = $engine->db_query2("SELECT COUNT(transactionid) AS ccount, rechargepro_status, DATE_FORMAT(transaction_date, '%d') AS day FROM rechargepro_refund WHERE transaction_date BETWEEN ? AND ? GROUP BY day,rechargepro_status ORDER BY day ASC",array($start,$end));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $day = $row[$dbc]['day'];
    $ccount = $row[$dbc]['ccount'];
    
    if($row[$dbc]['rechargepro_status'] == "PAID"){
        //if(key_exists($chartarray,$day)){
      if(isset($chartarray[$day])){
       $suc = $chartarray[$day]['success']+$ccount;
       $attm = $chartarray[$day]['attempt'];
       $chartarray[$day] = array("success"=>$suc,"attempt"=>$attm);
       }else{
        $chartarray[$day] = array("success"=>$ccount,"attempt"=>0);
       }
    }else{
        //if(key_exists($chartarray,$day)){
            if(isset($chartarray[$day])){
       $suc = $chartarray[$day]['success'];
       $attm = $chartarray[$day]['attempt']+$ccount;
       $chartarray[$day] = array("success"=>$suc,"attempt"=>$attm);
        }else{
         $chartarray[$day] = array("success"=>0,"attempt"=>$ccount);   
        }
    }
    }

?>
<script type="text/javascript"> 
    $(function () {
    $('#container').highcharts({
        chart: {
            zoomType: 'xy'
        },credits: {
enabled: false
},
        title: {
            text: 'Attempted Sales VS Successful Sales by Day'
        },
        subtitle: {
            text: ''
        },
        xAxis: [{
            categories: [<?php  foreach($chartarray AS $key => $val){echo "'".$key."',";} ?>],
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
                text: 'Attempt',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'Success',
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
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 5,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [{
            name: 'Attempt',
            type: 'column',
            yAxis: 1,
            data: [<?php  foreach($chartarray AS $key => $val){echo $val['attempt'].",";} ?>],
            tooltip: {
                valueSuffix: ''
            }

        }, {
            name: 'Success',
            type: 'spline',
            data: [<?php  foreach($chartarray AS $key => $val){echo $val['success'].",";} ?>],
            tooltip: {
                valueSuffix: ''
            }
        }, {
            type: 'pie',
            name: 'Payment Method',
            data: [{
                name: 'Card',
                y: <?php echo $card;?>,
                color: Highcharts.getOptions().colors[2] // Jane's color
            }, {
                name: 'Wallet',
                y: <?php echo $wallet;?>,
                color: Highcharts.getOptions().colors[3] // John's color
            }],
            center: [100, 80],
            size: 100,
            showInLegend: true,
            dataLabels: {
                enabled: true
            }
        }
        
        
        ]
    });
});
</script>

<div id="container" style="width: 100%; height: 300px;"></div>




</div>
<div style="overflow: hidden;">
<form action="plugin/rechargepro_transactionlog/pages/pro/exportdownload.php">
<input autocomplete="off" type="text" id="calendar2" name="date2" placeholder="End Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right;" class="input" />
<input autocomplete="off" type="text" id="calendar3" name="date1" placeholder="Start Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right; margin-right:5px;" class="input" />
<input type="submit" value="Download Sales Report" style="margin:3px; float: right; color:white; border:none; padding:5px 10px; margin-right:5px;" class="greenmenu shadow"/>
</form>
</div>
<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />





<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>
<?php $rowcount = $engine->db_query2("SELECT transactionid FROM rechargepro_refund", array(), true);?>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "plugin/rechargepro_transactionlog/pages/refundb.php",
    type: "POST",
    data : {page:page},
    success: function(data, textStatus, jqXHR)
    {
        $("#page-content").html(data);
    }
});
        }
    });
    })
</script>

<div id="page-content"></div>
<div style="clear: both;"></div>
<ul style="margin-left: 10px;" id="pagination" class="pagination-sm"></ul>

<?php 	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>






