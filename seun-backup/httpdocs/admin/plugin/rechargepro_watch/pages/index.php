<?php
$engine = new engine();
//require "plugin/parking_core/parking_core.php";







if(isset($_REQUEST['today'])){
$today = $_REQUEST['today'];
}else{
$today = date("Y-m-d");    
}
?>
<div id="tholder">
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
                url: "plugin/rechargepro_watch/pages/membersb.php",
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



function setactive(Id,what){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_watch/pages/pro/statuschange.php",
                data: "buserid="+Id+"&status="+what,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}
</script>

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />
<?php 
$arrayid= array();
$row = $engine->db_query2("SELECT rechargeproid FROM rechargepro_account WHERE officer = ?", array($engine->get_session("adminid")));
$rowcount = $engine->array_count($row);
for($dbc = 0; $dbc < $rowcount; $dbc++){
    $arrayid[] = $row[$dbc]['rechargeproid'];
    }

?>


<?php
$totalsailes = 0;
$totalprofit = 0;
if(count($arrayid) > 0){
$firstday = date("2019-09-01");
$lastDay = date("2019-09-30 23:55:55");
$array = implode(",",$arrayid);
$row = $engine->db_query2("SELECT SUM(amount) as am, SUM(rechargeproprofit) as rp FROM rechargepro_transaction_log WHERE rechargeproid IN ($array) AND rechargepro_status_code = '1' AND rechargepro_status = 'PAID' AND transaction_date BETWEEN ? AND ?",array($firstday,$lastDay)); 
$row = $row[0];

$totalsailes = $row['am'];
$totalprofit = $row['rp'];
}
?>
<div style="color:#0DAC42; background-color: white; font-size: 150%; padding:10px 0px;">
Total Accounts : <?php echo $rowcount;?>; Total Sales for <?php echo date("F");?> : <?php echo $totalsailes;?>; Total Profit for <?php echo date("F");?> : <?php echo $totalprofit;?>
</div>



<div class="shadow" style="margin: 10px 0px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="search" type="text" placeholder="Name / Email / Mobile" style="width: 90%;" class="input" /></div>

</div>



<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Account Manager</div></div>



<div class="profilebg" id="acholder" style="border:solid 1px #EEEEEE; overflow:hidden;">


<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>

<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "plugin/rechargepro_watch/pages/membersb.php",
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
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>




</div>







