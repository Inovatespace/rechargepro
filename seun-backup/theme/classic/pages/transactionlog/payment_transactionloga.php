<?php 
include "../../../../engine.autoloader.php";
if(isset($_REQUEST['today'])){
$today = $engine->safe_html($_REQUEST['today']);
}else{
$today = date("Y-m-d");    
}

$profile_creator = $engine->get_session("rechargeproid");
$myprofile_role = $engine->get_session("rechargeprorole");

$start = "";
$end = "";
$service = "";
$user = "";
$tmpuser = "";
$tmpservice = "";
$server = $_REQUEST['server'];
if(!empty($server)){
    $ex = explode("@@",$server);
    
    $start = $ex[0];
    
    $end =  date('Y-m-d 23:23:59', strtotime('+0 days', strtotime($ex[1])));
    if(empty($ex[1])){$end = "";}
    $service = $ex[3];
    $user = $ex[2];
    
$tmpuser = $user;
$tmpservice = $service;
    
    if($user == "All"){$user = "";}
    if($service == "All"){$service = "";}
    
    ?>
    <script type="text/javascript">
jQuery(document).ready(function($){
    
    
    $("#choice").val("<?php echo $tmpservice;?>");
    $("#ac").val("<?php echo $tmpuser;?>");
    $("#calendar4").val("<?php echo $start;?>");
    $("#calendar5").val("<?php echo $end;?>");
    
    });
</script>
    
    
    <?php
}
?>



<script type="text/javascript">
jQuery(document).ready(function($){
    
    
    $("#choice").val("<?php echo $tmpservice;?>");
    $("#ac").val("<?php echo $tmpuser;?>");
    $("#calendar4").val("<?php echo $start;?>");
    $("#calendar5").val("<?php echo $end;?>");
    
    
    
var myCalendar;
myCalendar = new dhtmlXCalendarObject(["calendar1", "calendar2", "calendar3","calendar4","calendar5","calendar6","calendar7","calendar8","calendar9"]);
myCalendar.hideTime();
});


  
  function try_again(Id){
   //  
//private_key
//tid 
$.ajax({
type: "POST",
url: "<?php echo $engine->config("website_root")."api/pro/myapp/try_again_new.json";?>",
data: "tid="+Id,
cache: false,
success: function(obj){
    //console.log(obj);
   // var obj = $.parseJSON(html);
    if(obj["status"] == "200"){
       window.location.href = "/invoice&id=<?php echo $profile_creator;?>_"+Id;
    }
    
    
   if(obj["status"] == "100"){
        $.alert(obj["message"]);
    }
    
    }
    });

  }
</script>


<div class="nInformation" style="text-align: left;">NOTE: Service charge is not included in the profit, to view profit including service charge visit agent section</div>


<div style="overflow: hidden; margin-bottom: 10px;">
<div style="float: left; font-weight:bold;" id="totalresult"></div>
<div onclick="$('#fil').toggle()" style="float: right; padding:3px 5px; margin:4px; cursor: pointer;" class="profilebg radious3 shadow"><span class="fas fa-filter"></span> Filter Result</div>
</div>


<div id="fil" style="overflow: hidden; margin-bottom: 10px; padding:10px 10px 0px 10px; display: none;" class="profilebg">
<form method="POST" >
<select id="choice" name="choice" style="float: left; margin-right:10px; padding:5px 0px;" class="input">
<option value="All">All Transaction</option>
<option value="Transfer">Transfer</option>
<option value="Credit">Credit</option>
<?php
$row = $engine->db_query("SELECT services_key, service_name FROM rechargepro_services WHERE services_category < '7'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
?>
<option value="<?php echo $row[$dbc]['services_key'];?>"><?php echo $row[$dbc]['service_name'];?></option>
<?php
	}
?>
</select>


<select class="input" id="ac" name="ac" style="float: left; margin-right:10px; padding:5px 0px;">
<option value="All">All Agents</option>
<?php
if($engine->get_session("rechargeprorole") < 3){
$row = $engine->db_query("SELECT rechargeproid,name FROM rechargepro_account WHERE rechargepro_cordinator = ? OR rechargeproid = ? OR profile_creator = ?",array($engine->get_session("rechargeproid"),$engine->get_session("rechargeproid"),$engine->get_session("rechargeproid")));
}else{
$row = $engine->db_query("SELECT rechargeproid,name FROM rechargepro_account WHERE rechargeproid = ?",array($engine->get_session("rechargeproid")));
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $rechargeproid = $row[$dbc]['rechargeproid']; 
    $name = $row[$dbc]['name']; 
?>
<option value="<?php echo $rechargeproid;?>"><?php echo $name;?></option>
<?php	}?>
</select>



<input autocomplete="off" type="text" id="calendar4" name="date1" placeholder="Start Date" value="" style="width:100px; float:left; margin-right:5px; padding:5px 0px;" class="input"/>
<input autocomplete="off" type="text" id="calendar5" name="date2" placeholder="End Date" value="" style="width:100px; float:left; margin-right:10px; padding:5px 0px;" class="input"/>
<input type="submit" value="View Sales Report" style="float:left; color:white; border:none; margin-right:5px; padding:5px 10px;" class="mainbg shadow"/>
</form>




<input type="text" class="input" id="search" placeholder="Search Transactions" style="float: right; padding:5px;" />
</div>


<script type="text/javascript" src="/java/jquery.twbsPagination.js"></script>
<?php 
if(!empty($user)){$profile_creator = $user;}
switch ($myprofile_role){
	case "1":
$b = "(cordinator_id = '$profile_creator' ||  rechargeproid = '$profile_creator')";
	break;

	case "2":
$b = "(agent_id = '$profile_creator' ||  rechargeproid = '$profile_creator')";
	break;

	case "3":
$b = "rechargeproid = '$profile_creator'";
	break;

	default :
    $b = "rechargeproid = '$profile_creator'";
}


$call = "$b";

if(empty($user) && empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND transaction_date BETWEEN '$start' AND '$end'";
}


if(empty($user) && !empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND rechargepro_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'";
}


if(!empty($user) && empty($service) && !empty($start) && !empty($end)){
    //depend
      $call = "rechargeproid = '$profile_creator' AND transaction_date BETWEEN '$start' AND '$end'";  
}


if(!empty($user) && !empty($service) && empty($start) && empty($end)){
    //depend
    $call = "rechargeproid = '$profile_creator' AND rechargepro_subservice = '$service'";
}

if(!empty($user) && empty($service) && empty($start) && empty($end)){
    //depend
    $call = "rechargeproid = '$profile_creator'";
}


if(empty($user) && !empty($service) && empty($start) && empty($end)){
    $call = "$b AND rechargepro_subservice = '$service'";
}

if(!empty($user) && !empty($service) && !empty($start) && !empty($end)){
    //depend
   $call = "rechargeproid = '$profile_creator' AND rechargepro_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'"; 
}




$rowcount = $engine->db_query("SELECT agent_id FROM rechargepro_transaction_log WHERE $call  AND rechargepro_status = 'PAID'", array(), true);

if($rowcount > 0){
    
?>
<script type="text/javascript">
$(document).ready(function () {

    $("#search").keyup(function () {
        var searchbox = $(this).val();

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "theme/classic/pages/transactionlog/payment_transactionlogb.php",
                data : {q:searchbox,profile_creator:"<?php echo $profile_creator;?>",role:"<?php echo $myprofile_role;?>",server:"<?php echo $server;?>"},
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
<script type="text/javascript">
jQuery(document).ready(function($){
    
    $("#totalresult").html("<?php echo $rowcount;?>, Transactions");
    
    
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "/theme/classic/pages/transactionlog/payment_transactionlogb.php",
    type: "POST",
    data : {page:page,profile_creator:"<?php echo $profile_creator;?>",role:"<?php echo $myprofile_role;?>",server:"<?php echo $server;?>"},
    success: function(data, textStatus, jqXHR)
    {
        $("#page-content").html(data);
    }
});
        }
    });
    })
</script>

<div id="page-content"><div style="text-align:center; padding:20px;"><img src="/theme/classic/images/rechargepro.gif" width="124" height="124" /></div></div>
<div style="clear: both;"></div>
<ul style="margin-top: 5px;" id="pagination" class="pagination-sm"></ul>
<?php
	}
?>
<?php 	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden; background-color:white;' class=''> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>
    
    
    
    
    