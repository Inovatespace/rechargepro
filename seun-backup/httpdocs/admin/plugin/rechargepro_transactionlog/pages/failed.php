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
                url: "plugin/rechargepro_transactionlog/pages/failedb.php",
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
<div style="float: left;">Transaction Log</div></div>



<div class="profilebg" id="acholder" style="padding:10px; border:solid 1px #EEEEEE; overflow:hidden;">
<style type="text/css">
.stats{position: relative; overflow:hidden; border-bottom:1px solid #EEEEEE; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #F1F1F1; border-bottom:1px solid #DDDDDD; padding:1px;}
.stats:hover {background: #F2F2F2; color:#F9C93A;}
</style>


<script type="text/javascript">
function updateme(){
   var chice = $("#chice").val();
   
   window.location.href = "rechargepro_transactionlog&p=index&t="+chice; 
}


</script>

<script type="text/javascript">
jQuery(document).ready(function($){
    
      <?php if(isset($_REQUEST['t'])){?>
        $("#chice").val("<?php echo $_REQUEST['t'];?>");
   <?php }
    ?>
    
    })
</script>


<div style="overflow: hidden;">
<select id="chice" style="float: left;" class="input" onchange="updateme()">
<option value="All">All</option>
<option value="Transfer">Transfer</option>
<option value="Credit">Credit</option>
<option >SMS</option>
<option >BULK_AIRTIME</option>
<option >BANK WITHDRAWAL</option>
<?php
$row = $engine->db_query2("SELECT services_key, service_name FROM rechargepro_services",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
?>
<option value="<?php echo $row[$dbc]['services_key'];?>"><?php echo $row[$dbc]['service_name'];?></option>
<?php
	}
?>
</select>


<div style="float: right;">
<form action="plugin/rechargepro_transactionlog/pages/pro/exportdownload.php">
<input autocomplete="off" type="text" id="calendar2" name="date2" placeholder="End Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right;" class="input" />
<input autocomplete="off" type="text" id="calendar3" name="date1" placeholder="Start Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right; margin-right:5px;" class="input" />
<input type="submit" value="Download Sales Report" style="margin:3px; float: right; color:white; border:none; padding:5px 10px; margin-right:5px;" class="greenmenu shadow"/>
</form>
</div>
</div>
<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />





<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>
<?php if(isset($_REQUEST['t'])){
    if($_REQUEST['t'] == "All"){
         $rowcount = $engine->db_query2("SELECT transactionid FROM rechargepro_transaction_log WHERE  rechargepro_status_code = '0' AND rechargepro_status = 'PAID'", array(), true);
    }else{
    $rowcount = $engine->db_query2("SELECT transactionid FROM rechargepro_transaction_log WHERE rechargepro_subservice = ? AND  rechargepro_status_code = '0' AND rechargepro_status = 'PAID'", array($_REQUEST['t']), true);
    }
    }else{
        $rowcount = $engine->db_query2("SELECT transactionid FROM rechargepro_transaction_log WHERE  rechargepro_status_code = '0' AND rechargepro_status = 'PAID'", array(), true);
    }
    ?>
<script type="text/javascript">
jQuery(document).ready(function($){
    
    var type = "All";
    <?php if(isset($_REQUEST['t'])){?>
        type = "<?php echo $_REQUEST['t'];?>";
   <?php }
    ?>
    
    
    
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "plugin/rechargepro_transactionlog/pages/failedb.php",
    type: "POST",
    data : {page:page,type:type},
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






