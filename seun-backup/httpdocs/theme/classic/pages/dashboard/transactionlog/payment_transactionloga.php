<?php 
include "../../../../../engine.autoloader.php";
if(isset($_REQUEST['today'])){
$today = $engine->safe_html($_REQUEST['today']);
}else{
$today = date("Y-m-d");    
}

$profile_creator = $engine->get_session("recharge4id");
$myprofile_role = $engine->get_session("recharge4role");

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


<link rel="stylesheet" href="/java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />
<script type="text/javascript" src="/java/sort/jquery.tablesorter.js"></script>



<div id="fil" style="overflow: hidden; margin-bottom: 10px; padding:10px 10px 0px 10px;" class="profilebg">
<form method="POST" >
<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<select id="choice" name="choice" style="padding:5px 0px; height:30px;" class="input">
<option value="All">All Transaction</option>
<option value="Transfer">Transfer</option>
<option value="Credit">Credit</option>
<?php
$row = $engine->db_query("SELECT services_key, service_name FROM recharge4_services WHERE services_category < '7'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
?>
<option value="<?php echo $row[$dbc]['services_key'];?>"><?php echo $row[$dbc]['service_name'];?></option>
<?php
	}
?>
</select>
<span class="focus-border"><i></i></span>
</div>

<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<select class="input" id="ac" name="ac" style="padding:5px 0px; height:30px;">
<option value="All">All Agents</option>
<?php
if($engine->get_session("recharge4role") < 3){
$row = $engine->db_query("SELECT recharge4id,name FROM recharge4_account WHERE recharge4_cordinator = ? OR recharge4id = ? OR profile_creator = ?",array($engine->get_session("recharge4id"),$engine->get_session("recharge4id"),$engine->get_session("recharge4id")));
}else{
$row = $engine->db_query("SELECT recharge4id,name FROM recharge4_account WHERE recharge4id = ?",array($engine->get_session("recharge4id")));
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $recharge4id = $row[$dbc]['recharge4id']; 
    $name = $row[$dbc]['name']; 
?>
<option value="<?php echo $recharge4id;?>"><?php echo $name;?></option>
<?php	}?>
</select>
<span class="focus-border"><i></i></span>
</div>


<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input autocomplete="off" type="text" id="calendar4" name="date1" placeholder="Start Date" value="" style="width:100px; padding:5px 0px; height:30px;" class="input"/><span class="focus-border"><i></i></span>
</div>

<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input autocomplete="off" type="text" id="calendar5" name="date2" placeholder="End Date" value="" style="width:100px; padding:5px 0px; height:30px;" class="input"/><span class="focus-border"><i></i></span>
</div>

<div style="float: left; margin-right:10px; position: relative; overflow: hidden;">
<input type="submit" value="View Sales Report" style="color:white; border:none; padding:5px 10px; height:30px;" class="mainbg shadow"/><span class="focus-border"><i></i></span>
</div>
</form>
</div>


<script type="text/javascript" src="/java/jquery.twbsPagination.js"></script>
<?php 
if(!empty($user)){$profile_creator = $user;}
switch ($myprofile_role){
	case "1":
$b = "(cordinator_id = '$profile_creator' ||  recharge4id = '$profile_creator')";
	break;

	case "2":
$b = "(agent_id = '$profile_creator' ||  recharge4id = '$profile_creator')";
	break;

	case "3":
$b = "recharge4id = '$profile_creator'";
	break;

	default :
    $b = "recharge4id = '$profile_creator'";
}


$call = "$b";

if(empty($user) && empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND transaction_date BETWEEN '$start' AND '$end'";
}


if(empty($user) && !empty($service) && !empty($start) && !empty($end)){
    $call = "$b AND recharge4_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'";
}


if(!empty($user) && empty($service) && !empty($start) && !empty($end)){
    //depend
      $call = "recharge4id = '$profile_creator' AND transaction_date BETWEEN '$start' AND '$end'";  
}


if(!empty($user) && !empty($service) && empty($start) && empty($end)){
    //depend
    $call = "recharge4id = '$profile_creator' AND recharge4_subservice = '$service'";
}

if(!empty($user) && empty($service) && empty($start) && empty($end)){
    //depend
    $call = "recharge4id = '$profile_creator'";
}


if(empty($user) && !empty($service) && empty($start) && empty($end)){
    $call = "$b AND recharge4_subservice = '$service'";
}

if(!empty($user) && !empty($service) && !empty($start) && !empty($end)){
    //depend
   $call = "recharge4id = '$profile_creator' AND recharge4_subservice = '$service' AND transaction_date BETWEEN '$start' AND '$end'"; 
}




$rowcount = $engine->db_query("SELECT agent_id FROM recharge4_transaction_log WHERE $call  AND recharge4_status = 'PAID'", array(), true);

if($rowcount > 0){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    
    $("#totalresult").html("<?php echo $rowcount;?>, Transactions");
    
    
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "/theme/classic/pages/dashboard/transactionlog/payment_transactionlogb.php",
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

<div id="page-content"><div style="text-align:center; padding:20px;"><img src="/theme/classic/images/recharge4.gif" width="124" height="124" /></div></div>
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
    <img src='/theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>