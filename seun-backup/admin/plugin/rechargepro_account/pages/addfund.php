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
    
    $myip = $engine->getRealIpAddr();
    
    
if($loan_ballance > 0){

if($loan_ballance > $value){
$paidloan = $value;
$loan_ballance = $loan_ballance - $value;
$value = 0;
}else{
$paidloan = $loan_ballance; 
$value = $value - $loan_ballance;
$loan_ballance = 0; 
}

$engine->db_query2("INSERT INTO rechargepro_transaction_log (rechargepro_status,account_meter,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_print) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array("PAID",$adminid,$profile_creator,$id,"Loan Payment","Loan Payment","Loan Payment","1",$myip,$paidloan,'{"details":{"CREDIT":"'.$value.'","TRANSACTION STATUS","DONE"}}'));

}


$details = "Admin_Credit_".$ac_ballance."_".$value."_".$id;
$engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($adminid,"PAYOUT",$details)); 

$engine->db_query2("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",array($id,$adminid,$value,"Addition")); 


$engine->db_query2("INSERT INTO rechargepro_transaction_log (rechargepro_status,account_meter,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_print) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array("PAID",$adminid,$profile_creator,$id,"Credit","Credit","Credit","1",$myip,$value,'{"details":{"CREDIT":"'.$value.'","TRANSACTION STATUS","DONE"}}'));

$newballance = $ac_ballance+$value;
$engine->db_query2("UPDATE rechargepro_account SET ac_ballance = ?, loan_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($newballance,$loan_ballance,$id));



  
  $message = "Hey $name,<br />
We just deposited $value in your wallet.<br />
Thank you,<br />
rechargepro";

$engine->notification($id,$message,1);


echo $engine->send_mail(array('noreply@rechargepro.com.ng','rechargepro!'),$email,"Money has been added to your wallet",$message);
  }else{
    
    
    
$details = "Admin_Debit_".$ac_ballance."_".$value."_".$id;
$engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($adminid,"PAYOUT",$details)); 

$engine->db_query2("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",array($id,$adminid,$value,"Subtraction")); 

$myip = $engine->getRealIpAddr();
$engine->db_query2("INSERT INTO rechargepro_transaction_log (rechargepro_status,account_meter,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_print) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array("PAID",$adminid,$profile_creator,$id,"Debit","Debit","Debit","1",$myip,$value,'{"details":{"DEBIT":"'.$value.'","TRANSACTION STATUS","DONE"}}'));

$newballance = $ac_ballance-$value;
$engine->db_query2("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($newballance,$id));
 
  
  $message = "Hey $name,<br />
$value just left your wallet!<br />
Thank you,<br />
rechargepro";

$engine->notification($id,$message,1);

echo $engine->send_mail(array('noreply@rechargepro.com.ng','rechargepro!'),$email,"Money has been spent from your wallet",$message);
  }
  


 
 
}
?>
<script>
var th = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];
var dg = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
var tn = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
var tw = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
function toWords(s) {
	s = s.toString();
	s = s.replace(/[\, ]/g, '');
	if (s != parseFloat(s)) return 'not a number';
	var x = s.indexOf('.');
	if (x == -1) x = s.length;
	if (x > 15) return 'too big';
	var n = s.split('');
	var str = '';
	var sk = 0;
	for (var i = 0; i < x; i++) {
		if ((x - i) % 3 == 2) {
			if (n[i] == '1') {
				str += tn[Number(n[i + 1])] + ' ';
				i++;
				sk = 1;
			} else if (n[i] != 0) {
				str += tw[n[i] - 2] + ' ';
				sk = 1;
			}
		} else if (n[i] != 0) {
			str += dg[n[i]] + ' ';
			if ((x - i) % 3 == 0) str += 'Hundred ';
			sk = 1;
		}
		if ((x - i) % 3 == 1) {
			if (sk) str += th[(x - i - 1) / 3] + ' ';
			sk = 0;
		}
	}
	if (x != s.length) {
		var y = s.length;
		str += 'point ';
		for (var i = x + 1; i < y; i++) str += dg[n[i]] + ' ';
	}
	return str.replace(/\s+/g, ' ');
}

//Enter Only Numbers
$(".numbers").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
               return false;
     }
});



	function per() {
		$("#h").html(toWords($("#value").val())+" Naira");
	}

</script>


<script type="text/javascript">
function saveme(){
var value = $("#value").val();
var what = $("#what").val();

if(empty(value)){
$.alert('Please enter amount'); return false;
 } 
 
 $("#prevd").hide();
 
 $("#prevd").prepend('<img src="images/loading6.gif"  />'); 

   $.ajax({
    url : "plugin/rechargepro_account/pages/addfund.php",
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
<div style="margin-bottom: 5px; text-align: left;"><?php echo $name;?> {<?php echo $ac_ballance;?>} {<?php echo $loan_ballance;?>}</div>
<div id="h" style="font-weight: bold;"></div>
<div style="margin-bottom: 5px;">
<select class="input" id="what" style="width: 100%;">
	<option value="1">Credit Account</option>
    <option value="2">Debit Account</option>
</select>
</div>

<div style="margin-bottom: 5px;"><input id="value"  onkeyup="per()" type="text" style="width: 100%;" class="input" /></div>
<div id="prevd"><input type="button" onclick="saveme()" class="activemenu shadow" style="cursor:pointer; border: none; width:100%; padding:5px 0px;" value="Perform Action" /></div>
</div>