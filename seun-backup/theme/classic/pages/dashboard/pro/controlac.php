<?php 
include "../../../../../engine.autoloader.php";
$id = $_REQUEST['id'];
$recharge4role = $engine->get_session("recharge4role"); 
$profile_creator = $engine->get_session("recharge4id");

if($recharge4role > 2){exit;}


if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
       exit;
    }
}


//if($id == "172"){exit;}



if($recharge4role == $id){exit;}


$row = $engine->db_query("SELECT ac_ballance,profile_creator,profile_agent,mobile,recharge4role FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($id));
$ballance = $row[0]['ac_ballance'];
$user_profile_creator = $row[0]['profile_creator'];
$user_profile_agent = $row[0]['profile_agent'];
$mobile = $row[0]['mobile'];
$myrecharge4role = $row[0]['recharge4role'];

if($profile_creator == $id && $recharge4role != 1){exit;}

if($recharge4role != 1){
 if($profile_creator != $user_profile_agent){
     if($user_profile_agent != $profile_creator){
        exit;
        }
 }

}


if($myrecharge4role > 3){exit;}





if(isset($_REQUEST['assign'])){
 $assign = $_REQUEST['assign'];
 $amount = $_REQUEST['amount'];
 $date = date("Y-m-d H:i:s");
 $channel = $_REQUEST['channel'];
 
 
 $ac = "ac_ballance";
 if($channel == 2){
  $ac = "profit_bal";  
 }
 
$row = $engine->db_query("SELECT recharge4_cordinator,$ac, profile_agent FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($profile_creator));
$myballance = $row[0][$ac];
$adminprofile_agent = $row[0]['profile_agent'];
$recharge4_cordinator = $row[0]['recharge4_cordinator'];
if($recharge4role == 1){
$recharge4_cordinator = 1;
}

if($assign == "Addition"){



if($profile_creator == $id && $profile_creator == 1){
$myballance = $myballance+$amount; 
}elseif($amount > $myballance){
echo "bad";exit;
}


$nb = $ballance + $amount;
$engine->db_query("UPDATE recharge4_account SET ac_ballance = ?, last_payout=?  WHERE recharge4id = ? LIMIT 1",array($nb,$date,$id));


if($profile_creator == $id && $profile_creator == 1){
    
    }else{
$nb = $myballance - $amount;
$engine->db_query("UPDATE recharge4_account SET $ac = ? WHERE recharge4id = ? LIMIT 1",array($nb,$profile_creator)); 
}

//$details = $assign."_".$ballance."_".$amount."_".$id."_".$ac;
//$engine->db_query("INSERT INTO log_log (recharge4id,what,details) VALUES (?,?,?)",array($profile_creator,"PAYOUT",$details)); 

//$engine->db_query("INSERT INTO payout_log (recharge4id,officer,amount,ptype) VALUES (?,?,?,?)",array($id,$profile_creator,$amount,$assign)); 

$myip = $engine->getRealIpAddr();
$engine->db_query("INSERT INTO recharge4_transaction_log (cordinator_id,agent_id,recharge4id,transaction_reference,recharge4_subservice,recharge4_service,recharge4_status_code,ip,amount,recharge4_status,recharge4_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($recharge4_cordinator,$adminprofile_agent,$profile_creator,"TRANSFER","TRANSFER","TRANSFER","1",$myip,$amount,"PAID",'{"details":{"TRANSFER":"'.$amount.'","TRANSACTION STATUS","DONE"}}',$id)); 

$engine->db_query("INSERT INTO recharge4_transaction_log (cordinator_id,agent_id,recharge4id,transaction_reference,recharge4_subservice,recharge4_service,recharge4_status_code,ip,amount,recharge4_status,recharge4_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($recharge4_cordinator,$user_profile_agent,$id,"TOPUP","TOPUP","TOPUP","1",$myip,$amount,"PAID",'{"details":{"TOPUP":"'.$amount.'","TRANSACTION STATUS","DONE"}}',$profile_creator)); 
  
}

if($assign == "Subtraction"){

if($amount > $ballance){
echo "bad";exit;
}
      
$nb = $ballance - $amount;
$engine->db_query("UPDATE recharge4_account SET ac_ballance = ?, last_payout=?  WHERE recharge4id = ? LIMIT 1",array($nb,$date,$id));     

$nb = $myballance + $amount;
$engine->db_query("UPDATE recharge4_account SET $ac = ? WHERE recharge4id = ? LIMIT 1",array($nb,$profile_creator)); 

//$details = $assign."_".$ballance."_".$amount."_".$id."_".$ac;
//$engine->db_query("INSERT INTO log_log (recharge4id,what,details) VALUES (?,?,?)",array($profile_creator,"PAYOUT",$details));


//$engine->db_query("INSERT INTO payout_log (recharge4id,officer,amount,ptype) VALUES (?,?,?,?)",array($id,$profile_creator,$amount,$assign)); 

$myip = $engine->getRealIpAddr();
$engine->db_query("INSERT INTO recharge4_transaction_log (cordinator_id,agent_id,recharge4id,transaction_reference,recharge4_subservice,recharge4_service,recharge4_status_code,ip,amount,recharge4_status,recharge4_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($recharge4_cordinator,$adminprofile_agent,$profile_creator,"WITHDRAW","WITHDRAW","WITHDRAW","1",$myip,$amount,"PAID",'{"details":{"WITHDRAW":"'.$amount.'","TRANSACTION STATUS","DONE"}}',$id)); 

$engine->db_query("INSERT INTO recharge4_transaction_log (cordinator_id,agent_id,recharge4id,transaction_reference,recharge4_subservice,recharge4_service,recharge4_status_code,ip,amount,recharge4_status,recharge4_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($recharge4_cordinator,$user_profile_agent,$id,"WITHDRAW","WITHDRAW","WITHDRAW","1",$myip,$amount,"PAID",'{"details":{"WITHDRAW":"'.$amount.'","TRANSACTION STATUS","DONE"}}',$profile_creator)); 

}


echo "ok";exit;
}




$row = $engine->db_query("SELECT ac_ballance,profit_bal FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($profile_creator));
$meballance = $row[0]['ac_ballance'];
$meprofit_bal = $row[0]['profit_bal'];
?>

<script type="text/javascript">
function savesetting(){
    var assign =  $("#assign").val();
    var amount =  $("#amount").val();

        if(empty(amount)){
            $.alert("Invalid Amount"); return false;
        }
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/dashboard/pro/controlac.php",
		data: "id=<?php echo $id;?>&assign="+assign+"&amount="+amount+"&channel="+$("input[name='channel']:checked").val(),
		cache: false,
		success: function(html) {
		  if(html.trim() == "bad"){
		      $.alert("Insuficient Fund");
               //$.alert(html);
		      }else{
		        //  $.alert(html);
           window.location.reload();
           }
		}
	});
}

</script>

<div class="whitemenu" style="padding: 10px; margin-top:-15px;">
<div style="font-size: 150%; margin-bottom:10px;">Account Ballance :: N<?php echo $ballance;?></div>
<div>Option</div>
<div style="margin-bottom: 5px;">
<select class="input" id="assign" style="padding:5px; width:100%;" >
<option value="Subtraction">Deduct from wallet</option>
<option value="Addition">Add to wallet</option>
</select>
</div>


<div>Amount</div>
<div style="margin-bottom: 5px;"><input type="text" class="input" id="amount" style="padding:5px; width:100%;" /></div>

<div style="margin-bottom: 5px;">
Main : N<?php echo $meballance;?> <input name="channel" type="radio" value="1" checked="checked" /> || Profit : N<?php echo $meprofit_bal;?> <input name="channel" type="radio" value="2" /> 
</div>



<div style="padding: 5px; text-align:center; cursor:pointer;" onclick="savesetting();" class="mainbg">PROCEED</div>



</div>








