<?php
include "../../../engine.autoloader.php";
//require "../../../plugin/parking_core/parking_core.php";


?>
   <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
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
function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}

$key = $_REQUEST['key'];
$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$row = $engine->db_query2("SELECT rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE (rechargepro_transaction_log.phone LIKE ? OR rechargepro_transaction_log.phone LIKE ? OR rechargepro_transaction_log.bank_ref LIKE ? OR rechargepro_transaction_log.account_meter LIKE ? OR rechargepro_transaction_log.transaction_reference LIKE ?) AND rechargepro_transaction_log.rechargepro_subservice =? LIMIT 50",array("%$q%","%$q%","%$q%","%$q%","%$q%",$key)); 
	}else{
$row = $engine->db_query2("SELECT rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE rechargepro_transaction_log.rechargepro_subservice = ? ORDER BY rechargepro_transaction_log.transactionid DESC LIMIT $start, $per_page",array($key));
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $rechargeproid = $row[$dbc]['rechargeproid']; 
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
    $agent_id = myname($row[$dbc]['agent_id'],$engine);
    $rechargepro_status_code = $row[$dbc]['rechargepro_status_code'];
    $account_meter = $row[$dbc]['account_meter'];
    $transactionid = $row[$dbc]['transactionid'];
    
    
    $paidwith = "Pending";
    if($payment_method == "1"){ $paidwith = "Wallet";}
    if($payment_method == "2"){ $paidwith = "Card";}
    

    $vtransaction = "";
    if(!empty($bank_ref)){$vtransaction = "fas fa-sync tunnel";}   
    
    $viewprint = "";
    if($rechargepro_status_code == 1){$viewprint = "fas fa-print tunnel";}


    $stats = "";
    if($dbc % 2 == 0){ $stats = "stats2";}
?>
<tr >
<td><?php echo $agent_id;?></td>
<td><?php echo $phone."<br /><strong style='color:#0C8F35;'>".$account_meter."</strong> <strong style='color:#8C2AF4;' >{".$transactionid."}</strong>";?></td>
<td><?php echo $transaction_reference;?></td>
<td><?php echo $bank_ref;?> <span style="color: #2C972B; cursor: pointer;" name="plugin/rechargepro_transactionlog/pages/pro/process_payment.php?id=<?php echo $transactionid;?>&width=500&flw_ref=<?php echo $bank_ref;?>" class="<?php echo $vtransaction;?>"></span></td>
<td><?php echo $amount;?></td>
<td><?php echo $status;?> <span style="color: #250EB6; cursor: pointer;" name="plugin/rechargepro_transactionlog/pages/pro/print.php?id=<?php echo $transactionid;?>&width=700" class="<?php echo $viewprint;?>"></span></td>
<td><?php echo $transaction_status;?></td>
<td><?php echo $paidwith;?></td>
<td><?php echo $ip;?></td>
<td><?php echo $transaction_date;?></td>
</tr>
<?php
	}
 	if(!isset($transaction_date)){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>


</tbody>
</table>

</div>





