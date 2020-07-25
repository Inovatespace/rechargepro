<?php
include "../../../engine.autoloader.php";
$adminid = $engine->get_session("adminid");


$permission =	$engine->admin_permission("rechargepro_account","index");
if($permission < 3){ exit;}

$id = $_REQUEST['id'];
$row = $engine->db_query2("SELECT profile_creator,rechargeproid, loan_ballance, name, email, ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
$name = $row[0]['name'];
$email = $row[0]['email'];
$ac_ballance = $row[0]['ac_ballance'];
$loan_ballance = $row[0]['loan_ballance'];
$profile_creator = $row[0]['profile_creator'];
if(!isset($_SESSION['adminme'])){exit;};







if(isset($_REQUEST['what'])){
    
  $value = $_REQUEST['value'];
  if($_REQUEST['what'] == "1"){
    



$details = "Admin_Credit_".$ac_ballance."_".$value."_".$id;
$engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($adminid,"PAY LOAN",$details)); 

$engine->db_query2("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",array($id,$adminid,$value,"loan")); 

$myip = $engine->getRealIpAddr();
$engine->db_query2("INSERT INTO rechargepro_transaction_log (rechargepro_status,account_meter,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_print) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array("PAID",$adminid,$profile_creator,$id,"loan Credit","loan Credit","loan Credit","1",$myip,$value,'{"details":{"CREDIT":"'.$value.'","TRANSACTION STATUS","DONE"}}'));

$newballance = $ac_ballance+$value;
$newloan = $loan_ballance+$value;
$engine->db_query2("UPDATE rechargepro_account SET ac_ballance = ?, loan_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($newballance,$newloan,$id));


$message = "Hey $name,<br />
We just deposited $value in your wallet.<br />
Thank you,<br />
rechargepro";

$engine->notification($id,$message,1);


echo $engine->send_mail(array('noreply@rechargepro.com.ng','rechargepro!'),$email,"Money has been added to your wallet",$message);
  
  }else{
    
$details = "Admin_Debit_".$ac_ballance."_".$value."_".$id;
$engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($adminid,"PAY LOAN",$details)); 

$engine->db_query2("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",array($id,$adminid,$value,"loan Subtraction")); 

$myip = $engine->getRealIpAddr();
$engine->db_query2("INSERT INTO rechargepro_transaction_log (rechargepro_status,account_meter,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_print) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array("PAID",$adminid,$profile_creator,$id,"loan Debit","loan_ballance Debit","loan Debit","1",$myip,$value,'{"details":{"DEBIT":"'.$value.'","TRANSACTION STATUS","DONE"}}'));

$newballance = $ac_ballance-$value;
$newloan = $loan_ballance-$value;
$engine->db_query2("UPDATE rechargepro_account SET ac_ballance = ?, loan_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($newballance,$newloan,$id));
 
  
  $message = "Hey $name,<br />
$value just left your wallet!<br />
Thank you,<br />
rechargepro";

$engine->notification($id,$message,1);

echo $engine->send_mail(array('noreply@rechargepro.com.ng','rechargepro!'),$email,"Money has been spent from your wallet",$message);
  }
  


 
 
}
?>

<script type="text/javascript">
function saveme(){
var value = $("#value").val();
var what = $("#what").val();

if(empty(value)){
$.alert('Please enter amount'); return false;
 } 
 
 $("#prevd").prepend('<img src="images/loading6.gif"  />'); 

   $.ajax({
    url : "plugin/rechargepro_account/pages/addloan.php",
    type: "POST",
    data : {id:"<?php echo $id;?>",value:value,what:what},
    success: function(data, textStatus, jqXHR)
    {
       window.location.reload();
    }
    });
    
}
</script>
<div class="barmenu" style="padding: 10px; margin:-15px -5px 0px -5px;">Add Fund</div>
<div class="profilebg" style="padding: 10px;">
<div style="margin-bottom: 5px; text-align: left;"><?php echo $name;?> {<?php echo $ac_ballance;?>}{<?php echo $loan_ballance;?>}</div>
<div style="margin-bottom: 5px;">
<select class="input" id="what" style="width: 100%;">
	<option value="1">Credit loan Account</option>
    <option value="2">Debit loan Account</option>
</select>
</div>

<div style="margin-bottom: 5px;"><input id="value" type="text" style="width: 100%;" class="input" /></div>
<div id="prevd"><input type="button" onclick="saveme()" class="activemenu shadow" style="cursor:pointer; border: none; width:100%; padding:5px 0px;" value="Perform Action" /></div>
</div>