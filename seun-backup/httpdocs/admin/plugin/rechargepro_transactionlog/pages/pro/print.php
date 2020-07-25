<?php include "../../../../engine.autoloader.php";?>
<style type="text/css">
#content_body{background: url(theme/classicnext/images/innerbg.png) center repeat;}
</style>
<?php
if(!isset($_REQUEST['id'])){
   exit; 
    }
    
    

$id = htmlentities($_REQUEST['id']);

$row = $engine->db_query2("SELECT transactionid,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,phone,email,payment_method,transaction_status,transaction_code,transaction_reference,rechargepro_status,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ?",array($id));
	
if(empty($row[0]['transactionid'])){
exit;
}


$transactionid = $row[0]['transactionid'];
$rechargepro_service = $row[0]['rechargepro_service'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$account_meter = $row[0]['account_meter'];
$business_district = $row[0]['business_district'];
$thirdPartycode = $row[0]['thirdPartycode'];
$address = $row[0]['address'];
$name = $row[0]['name'];
$phcn_unique = $row[0]['phcn_unique'];
$amount = $row[0]['amount'];
$phone = $row[0]['phone'];
$email = $row[0]['email'];
$payment_method = $row[0]['payment_method'];
$transaction_status = $row[0]['transaction_status'];
$transaction_code = $row[0]['transaction_code'];
$transaction_reference = $row[0]['transaction_reference'];
$rechargepro_status = $row[0]['rechargepro_status'];
$rechargepro_status_code = $row[0]['rechargepro_status_code'];
$rechargepro_print = $row[0]['rechargepro_print'];
$transaction_date = $row[0]['transaction_date'];


?>

  

<div style="">
<div style="text-align: right; font-size: 180%;"><a class="fas fa-print"onclick="javascript:Print('printDivContent');"></a></div>

<style type="text/css">
/*//////////////////////////////////////////////////////////////////
[ Table ]*/

.spectable table {
  width: 100%;
}

.spectable table td, table th {
  padding-left: 8px;
    padding:10px;
}
.spectable table thead tr {
 
  background: url(/theme/test/images/bg1.png) repeat;
  color:white;
}

.spectable table tbody tr:last-child {
  border: 0;
}
.spectable table td, table th {
  text-align: left;
}
.spectable table td.l, table th.l {
  text-align: right;
}
.spectable table td.c, table th.c {
  text-align: center;
}
.spectable table td.r, table th.r {
  text-align: center;
}



.spectable tbody tr:nth-child(even) {
  background-color: #f5f5f5;
}

.spectable tbody tr {
  font-family: OpenSans-Regular;
  font-size: 15px;
  color: #808080;
}

.spectable tbody tr:hover {
  color: #555555;
  background-color: #f5f5f5;
  cursor: pointer;
}


</style>


<div id="printDivContent" style="padding:10px;" class="profilebg">

<div class="spectable" style="overflow-x:auto;">
<table>
<thead>
<tr style="text-transform: uppercase;">
<th>REFERENCE</th>
<th>LOG</th>
</tr>
</thead>
<tbody>
<tr>
	<td><?php echo $transaction_reference;?></td>
	<td><?php echo $rechargepro_print;?></td>
</tr>
</tbody>
</table>
</div>
</div>