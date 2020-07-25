<?php
include "../../../engine.autoloader.php";
//require "../../../plugin/parking_core/parking_core.php";

$id = $_REQUEST['id'];
?>
   <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
  
 
  function process_ticket(Id){
              $.ajax({
                type: "POST",
                url: "https://rechargepro.com/api/v1/myapp/try_again.json",
                data: "tid="+Id,
                cache: false,
                success: function (html) {
                  window.location.reload();
                }
            });
  }
  
  
 function process_refund(Id){
              $.ajax({
                type: "POST",
                url: "plugin/rechargepro_transactionlog/pages/pro/refundpro.php",
                data: "tid="+Id,
                cache: false,
                success: function (html) {
                window.location.reload();//.href = "rechargepro_transactionlog&p=refund";
                }
            });
  }
  
  
  function verify_process(Id){
              $.ajax({
                type: "POST",
                url: "plugin/rechargepro_transactionlog/pages/pro/verifypro.php",
                data: "tid="+Id,
                cache: false,
                success: function (html) {
                  $.alert(html);
                }
            });
  }
  
</script>

<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>Agent Name</th>
<th>Phone/Account{TID}</th>
<th>Biller Ref</th>
<th>Bank Ref</th>
<th>Amount</th>
<th>status</th>
<th>message</th>
<th>payment method</th>
<th>IP</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php

$permission =	3;

$type = $_REQUEST['type'];

function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}
$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])){$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$row = $engine->db_query2("SELECT rechargepro_subservice, rechargepro_service,rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE rechargepro_transaction_log.bank_ref LIKE ? OR rechargepro_transaction_log.account_meter LIKE ? OR rechargepro_transaction_log.transaction_reference LIKE ? LIMIT 50",array("%$q%","%$q%","%$q%")); 
	}else{
	   if($type == "All"){
$row = $engine->db_query2("SELECT rechargepro_subservice, rechargepro_service,rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE rechargepro_transaction_log.rechargeproid = ? ORDER BY rechargepro_transaction_log.transactionid DESC LIMIT $start, $per_page",array($id));
}else{
$row = $engine->db_query2("SELECT rechargepro_subservice, rechargepro_service,rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE 	rechargepro_transaction_log.rechargepro_subservice = ? AND rechargepro_transaction_log.rechargeproid = ? ORDER BY rechargepro_transaction_log.transactionid DESC LIMIT $start, $per_page",array($type,$id));
}
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $mainrechargeproid = $row[$dbc]['rechargeproid'];
    $rechargeproid = myname($mainrechargeproid,$engine); 
    $phone = $row[$dbc]['phone']; 
    $transaction_reference = $row[$dbc]['transaction_reference']; 
    $bank_ref = $row[$dbc]['bank_ref']; 
    $amount = $row[$dbc]['amount']; 
    $status = $row[$dbc]['rechargepro_status']; 
    $payment_method = $row[$dbc]['payment_method']; 
    $ip = $row[$dbc]['ip']; 
    $transaction_status = $row[$dbc]['transaction_status']; 
    $rechargepro_status = $row[$dbc]['rechargepro_status']; 
    $transaction_date = $row[$dbc]['transaction_date'];
    $agent_id = $row[$dbc]['agent_id'];
    $rechargepro_status_code = $row[$dbc]['rechargepro_status_code'];
    $account_meter = $row[$dbc]['account_meter'];
    $transactionid = $row[$dbc]['transactionid'];
    $rechargepro_service = $row[$dbc]['rechargepro_service'];
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];
     
     
    $paidwith = "Pending";
    if($payment_method == "1"){ $paidwith = "Card";}
    if($payment_method == "2"){ $paidwith = "Wallet";}
    
    
    $process = "";
    if($status == "PAID" && $rechargepro_status_code == 0){
    $process = '<span style="color: #F36B0A; cursor: pointer;" title="Retry" onclick="process_ticket(\''.$transactionid.'\')" class="fas fa-sync"></span>';
    }
    
    
    $refund = "";
    if($status == "PAID" && $rechargepro_status_code == 0 && $mainrechargeproid != 0){
    $refund = '<span style="color: #FF088C; cursor: pointer;" title="Refund" onclick="process_refund(\''.$transactionid.'\')" class="fas fa-redo"></span>';
    }

    $vtransaction = "";
    if(!empty($bank_ref)){$vtransaction = '<span style="color: #2C972B; cursor: pointer;" name="plugin/rechargepro_transactionlog/pages/pro/process_payment.php?id='.$transactionid.'&width=500&flw_ref='.$bank_ref.'" class="fas fa-sync tunnel"></span>';}   
    
    $viewprint = "";
    if($rechargepro_status_code == 1){$viewprint = "fas fa-print tunnel";}
    
    
    

if(in_array($rechargepro_service,array("BULK_AIRTIME","SMS"))){
  $refund = "";  
  $process = "";
}



    $stats = "";
    if($dbc % 2 == 0){ $stats = "stats2";}
    
        
    $stn = '<span style="color: #FF088C; cursor: pointer;" title="verify" onclick="verify_process(\''.$transactionid.'\')" class="fas fa-circle-notch"></span>';
    
        if(in_array($rechargepro_service,array("Credit","transfer","topup","PROFIT","REWARD","2351","ACC","2352","AEC","2354","ADC","ADD","JOD","JOP","ADC","","2354","ALC","2353"))){
        $stn = "";
    }
    
        if(in_array($rechargepro_subservice,array("Credit","transfer","topup","PROFIT","REWARD","2351","ACC","2352","AEC","2354","ADC","ADD","JOD","JOP","ADC","","2354","ALC","2353"))){
        $stn = "";
    }
?>
<tr >
<td><?php echo $stn.$rechargeproid;?></td>
<td><?php echo $phone."<br /><strong style='color:#0C8F35;'>".$account_meter."</strong> <strong style='color:#8C2AF4;' >{".$transactionid."}</strong><br />". substr($rechargepro_service, 0, 13);?></td>
<td><?php echo $transaction_reference;?></td>
<td><?php echo $bank_ref. $vtransaction;?> </td>
<td><?php echo $amount;?></td>
<td><?php echo $status; if($permission >= 3){ echo " ".$process;};?> <span style="color: #250EB6; cursor: pointer;" name="plugin/rechargepro_transactionlog/pages/pro/print.php?id=<?php echo $transactionid;?>&width=700" title="Print" class="<?php echo $viewprint;?>"></span></td>
<td><?php echo $transaction_status;?></td>
<td><?php echo $paidwith;?></td>
<td><?php echo $ip;?></td>
<td><?php echo $transaction_date;?></td>
</tr>
<?php
	}
    
    
 	if(!isset($transaction_date)){ echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>


</tbody>
</table>

</div>





