<?php 
$engine = new engine();

$profile_creator = $engine->get_session("recharge4id");
$recharge4role = $engine->get_session("recharge4role");
$row = $engine->db_query("SELECT ac_ballance,profile_creator,profile_agent,recharge4role FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($profile_creator));
$ballance = $row[0]['ac_ballance'];


$server = "";
if(isset($_REQUEST['choice']) || isset($_REQUEST['ac']) || isset($_REQUEST['date1'])){
if(!isset($_REQUEST['choice'])){$_REQUEST['choice'] = "All";}   
if(!isset($_REQUEST['ac'])){$_REQUEST['ac'] = "All";}   
if(!isset($_REQUEST['date1'])){$_REQUEST['date1'] = "";}   
if(!isset($_REQUEST['date2'])){$_REQUEST['date2'] = "";}   

$server = $engine->safe_html($_REQUEST['date1'])."@@".$engine->safe_html($_REQUEST['date2'])."@@".$engine->safe_html($_REQUEST['ac'])."@@".$engine->safe_html($_REQUEST['choice']);  
}




$post = 0;
if(isset($_REQUEST['profile_creator'])){
if($engine->get_session("recharge4role") == "1"){
$profile_creator = $_REQUEST['profile_creator'];
$post = 1;
}
}
 
if(isset($_REQUEST['today'])){
$today = $_REQUEST['today'];
}else{
$today = date("Y-m-d");    
}


?>
<style type="text/css">
#myTable table td{ max-width:10%; width:10%; word-wrap:break-word;}
	td:before { 
		max-width: 15%; 
word-wrap:break-word;
		white-space: nowrap;
	}
</style>


<div style="width:100%; background-color: white; min-height: 500px;">
<div style="padding:10px; overflow:hidden;">


<div>



<script type="text/javascript">
function camlink(){

$("#loadcontent").html('<div style="text-align:center; padding:20px;"><img src="/theme/classic/images/recharge4.gif" width="124" height="124" /></div>');

$.ajax({
    url : "/theme/classic/pages/dashboard/transactionlog/payment_transactionloga.php",
    type: "POST",
    data : {today:"<?php echo $today;?>",profile_creator:"<?php echo $profile_creator;?>",post:"<?php echo $post;?>",server:"<?php echo $server;?>"},
    success: function(data, textStatus, jqXHR){
        $("#loadcontent").html(data);
        }
    });


}


  jQuery(document).ready(function($){
    camlink();
  });

</script>

<div id="loadcontent"></div>
</div>

</div>
</div>