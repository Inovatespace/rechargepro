<?php
include "../../../engine.autoloader.php";
//require "../../../plugin/parking_core/parking_core.php";
?>
   <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
  
  function process_ticket(Id){
              $.ajax({
                type: "POST",
                url: "https://rechargepro.com.ng/api/pro/myapp/try_again.json",
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
  
  
   function process_refundb(Id){
    
        
    $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to refund this User?',
    buttons: {
        confirm: function () {

              $.ajax({
                type: "POST",
                url: "plugin/rechargepro_transactionlog/pages/pro/refundprob.php",
                data: "tid="+Id,
                cache: false,
                success: function (html) {
                window.location.reload();//.href = "rechargepro_transactionlog&p=refund";
                }
            });
              
    },
        cancel: function () {
            
        }
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
$banklist = json_decode('{"000001":"Sterling Bank",
"000002":"Keystone Bank",
"000003":"First City Monument Bank",
"000004":"United Bank for Africa",
"000005":"Access Bank (Diamond)",
"000006":"JAIZ Bank",
"000007":"Fidelity Bank",
"000008":"Skye Bank",
"000009":"Citi Bank",
"000010":"Ecobank Bank",
"000011":"Unity Bank",
"000012":"StanbicIBTC Bank",
"000013":"GTBank Plc",
"000014":"Access Bank",
"000015":"Zenith Bank Plc",
"000016":"First Bank of Nigeria",
"000017":"Wema Bank",
"000018":"Union Bank",
"000019":"Enterprise Bank",
"000020":"Heritage",
"000021":"StandardChartered",
"000022":"Suntrust Bank",
"000023":"Providus Bank ",
"000024":"Rand Merchant Bank",
"400001":"FSDH Merchant Bank",
"060001":"Coronation Merchant Bank",
"060002":"FBN Merchant Bank",
"070007":"Omoluabi savings and loans",
"090001":"ASOSavings & Loans",
"090005":"Trustbond Mortgage Bank",
"090006":"SafeTrust ",
"090107":"FBN Mortgages Limited",
"100024":"Imperial Homes Mortgage Bank",
"100028":"AG Mortgage Bank",
"070009":"Gateway Mortgage Bank",
"070010":"Abbey Mortgage Bank",
"070011":"Refuge Mortgage Bank",
"070012":"Lagos Building Investment Company",
"070013":"Platinum Mortgage Bank",
"070014":"First Generation Mortgage Bank",
"070015":"Brent Mortgage Bank",
"070016":"Infinity Trust Mortgage Bank",
"090108":"New Prudential Bank",
"070001":"NPF MicroFinance Bank",
"070002":"Fortis Microfinance Bank",
"070006":"Covenant MFB",
"070008":"Page Microfinance Bank",
"090003":"JubileeLife Microfinance  Bank",
"090004":"Parralex Microfinance bank",
"090097":"Ekondo MFB",
"090110":"VFD MFB",
"090111":"FinaTrust Microfinance Bank",
"090112":"Seed Capital Microfinance Bank",
"090114":"Empire trust MFB",
"090115":"TCF MFB",
"090116":"AMML MFB",
"090117":"Boctrust Microfinance Bank",
"090118":"IBILE Microfinance Bank",
"090119":"Ohafia Microfinance Bank",
"090120":"Wetland Microfinance Bank",
"090121":"Hasal Microfinance Bank",
"090122":"Gowans Microfinance Bank",
"090123":"Verite Microfinance Bank",
"090124":"Xslnce Microfinance Bank",
"090125":"Regent Microfinance Bank",
"090126":"Fidfund Microfinance Bank",
"090127":"BC Kash Microfinance Bank",
"090128":"Ndiorah Microfinance Bank",
"090129":"Money Trust Microfinance Bank",
"090130":"Consumer Microfinance Bank",
"090131":"Allworkers Microfinance Bank",
"090132":"Richway Microfinance Bank",
"090133":" AL-Barakah Microfinance Bank",
"090134":"Accion Microfinance Bank",
"090135":"Personal Trust Microfinance Bank",
"090136":"Microcred Microfinance Bank",
"090137":"PecanTrust Microfinance Bank",
"090138":"Royal Exchange Microfinance Bank",
"090139":"Visa Microfinance Bank",
"090140":"Sagamu Microfinance Bank",
"090141":"Chikum Microfinance Bank",
"090142":"Yes Microfinance Bank",
"090143":"Apeks Microfinance Bank",
"090144":"CIT Microfinance Bank",
"090145":"Fullrange Microfinance Bank",
"090146":"Trident Microfinance Bank",
"090147":"Hackman Microfinance Bank",
"090148":"Bowen Microfinance Bank",
"090149":"IRL Microfinance Bank",
"090150":"Virtue Microfinance Bank",
"090151":"Mutual Trust Microfinance Bank",
"090153":"FFS Microfinance Bank",
"090156":"e-Barcs Microfinance Bank",
"090157":"Infinity Microfinance Bank",
"090158":"Futo Microfinance Bank",
"090159":"Credit Afrique Microfinance Bank",
"090160":"Addosser Microfinance Bank",
"090161":"Okpoga Microfinance Bank",
"100001":"FET",
"100002":"Paga",
"100003":"Parkway-ReadyCash",
"100004":"Paycom",
"100005":"Cellulant",
"100006":"eTranzact",
"100007":"StanbicMobileMoney",
"100008":"Ecobank Xpress Account",
"100009":"GTMobile",
"100010":"TeasyMobile",
"100011":"Mkudi",
"100012":"VTNetworks",
"100013":"AccessMobile",
"100014":"FBNMobile",
"100015":"Kegow",
"100016":"FortisMobile",
"100017":"Hedonmark",
"100018":"ZenithMobile",
"100019":"Fidelity Mobile",
"100020":"MoneyBox",
"100021":"Eartholeum",
"100022":"Sterling Mobile",
"100023":"TagPay",
"100025":"Zinternet Nigera Limited",
"100026":"One Finance",
"100029":"Innovectives Kesh",
"100030":"EcoMobile",
"100031":"FCMB Mobile",
"100032":"Contec Global Infotech Limited (NowNow)",
"100027":"Intellifin",
"110001":"PayAttitude Online",
"110002":"Flutterwave Technology Solutions Limited",
"999999":"NIP Virtual Bank"}',true);
$permission =	$engine->admin_permission("rechargepro_transactionlog","index");
$adminid = $engine->get_session("adminid");

$type = "ALL";
if(isset($_REQUEST['type'])){
$type = $_REQUEST['type'];}

function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}
$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])){$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;


$mytid = array();
$row = $engine->db_query2("SELECT tid FROM refund_process",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $mytid[] = $row[$dbc]['tid'];
    }
$mytid = implode(",",$mytid);


$color=1;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$row = $engine->db_query2("SELECT 	thirdPartycode,refund,inter_ref,rechargepro_subservice, rechargepro_service,rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE rechargepro_transaction_log.bank_ref LIKE ? OR rechargepro_transaction_log.account_meter LIKE ? OR rechargepro_transaction_log.transaction_reference LIKE ? OR rechargepro_transaction_log.transactionid = ?  ORDER BY transactionid DESC LIMIT 50",array("%$q%","%$q%","%$q%",$q)); 
	}else{
	   if($type == "All"){
$row = $engine->db_query2("SELECT 	thirdPartycode,refund,inter_ref,rechargepro_subservice, rechargepro_service,rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid  WHERE rechargepro_transaction_log.transactionid NOT IN ($mytid) ORDER BY rechargepro_transaction_log.transactionid DESC LIMIT $start, $per_page",array());
}else{
$row = $engine->db_query2("SELECT 	thirdPartycode,refund,inter_ref,rechargepro_subservice, rechargepro_service,rechargepro_transaction_log.transactionid , rechargepro_transaction_log.account_meter, rechargepro_transaction_log.phone, rechargepro_transaction_log.rechargeproid,  rechargepro_transaction_log.transaction_reference, rechargepro_transaction_log.bank_ref, rechargepro_transaction_log.amount, rechargepro_transaction_log.rechargepro_status, rechargepro_transaction_log.payment_method, rechargepro_transaction_log.ip, rechargepro_transaction_log.transaction_status, rechargepro_transaction_log.agent_id, rechargepro_transaction_log.transaction_date, rechargepro_transaction_log.rechargepro_status_code FROM rechargepro_transaction_log LEFT JOIN rechargepro_account ON rechargepro_transaction_log.rechargeproid = rechargepro_account.rechargeproid WHERE  rechargepro_transaction_log.transactionid NOT IN ($mytid) AND 	rechargepro_transaction_log.rechargepro_subservice = ? ORDER BY rechargepro_transaction_log.transactionid DESC LIMIT $start, $per_page",array($type));
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
    $inter_ref = $row[$dbc]['inter_ref'];
    $systemrefund = $row[$dbc]['refund'];
    $thirdPartycode = $row[$dbc]['thirdPartycode'];
    	
    
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
    if(!empty($bank_ref)){//$vtransaction = '<span style="color: #2C972B; cursor: pointer;" name="plugin/rechargepro_transactionlog/pages/pro/process_payment.php?id='.$transactionid.'&width=500&flw_ref='.$bank_ref.'" class="fas fa-sync tunnel"></span>';
    }   
    
    $viewprint = "fas fa-print tunnel";
    
    
    $myrefund = "";
    if($systemrefund == 0 && $status == "PAID"){
     $myrefund = ' <span class="fas fa-window-restore"  title="Refund" onclick="process_refundb(\''.$transactionid.'\')" style="color: #0A17D4; cursor: pointer;"></span>';
    }
    
    
    if($adminid != 1){$myrefund = "";}
    if(in_array($rechargepro_service,array("BANK WITHDRAWAL","SMS","BULK_AIRTIME","PROFIT","REWARD","TOPUP","TRANSFER"))){
  $myrefund = "";
    }
    if($payment_method == "1"){
  $myrefund = "";
    }
    



if(in_array($rechargepro_service,array("BULK_AIRTIME","SMS",))){
  $refund = "";  
  $process = "";
}


if(in_array($rechargepro_service,array("BANK WITHDRAWAL"))){
  $refund = "";   $vtransaction = "";
}




    $stats = "";
    if($dbc % 2 == 0){ $stats = "stats2";}
    
    
    $stn = '<span style="color: #FF088C; cursor: pointer;" title="verify" onclick="verify_process(\''.$transactionid.'\')" class="fas fa-circle-notch"></span>';
    
    if(in_array($rechargepro_service,array("Credit","transfer","topup","PROFIT","REWARD","ADD","JOD","JOP","Debit","TOPUP","TRANSFER","AUTO PAY"))){
        $stn = "";
    }
    
        if(in_array($rechargepro_subservice,array("Credit","transfer","topup","PROFIT","REWARD","ADD","JOD","JOP","Debit","TOPUP","TRANSFER","AUTO PAY"))){
        $stn = "";
    }
    
    
    $leftac = "";
    if($rechargepro_subservice == "BANK TRANSFER" && !empty($inter_ref)){
        $leftac = ' <span class="fas fa-code" style="color:#339014; font-size:18px;"></span>';
    }
    
    
        if($rechargepro_subservice == "BANK TRANSFER"){
        $phone = $banklist[$thirdPartycode];
    }
    
    
    
       if($rechargepro_status != "PAID"){
  $myrefund = ""; $refund = "";   $vtransaction = ""; $stn = "";
    }

?>
<tr >
<td> <?php echo $stn.$rechargeproid.$leftac;?></td>
<td><?php echo $phone."<br /><strong style='color:#0C8F35;'>".$account_meter."</strong> <strong style='color:#8C2AF4;' >{".$transactionid."}</strong><br />". substr($rechargepro_service, 0, 13);?></td>
<td><?php echo $transaction_reference;?></td>
<td><?php echo $bank_ref. $vtransaction;?> </td>
<td><?php echo $amount;?></td>
<td><?php echo $status; if($permission >= 3){echo " ".$process;};?><span style="color: #250EB6; cursor: pointer;" name="plugin/rechargepro_transactionlog/pages/pro/print.php?id=<?php echo $transactionid;?>&width=700" title="Print" class="<?php echo $viewprint;?>"></span></td>
<td><?php echo $transaction_status;?></td>
<td><?php echo $paidwith.$myrefund;?>  </td>
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





