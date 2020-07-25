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
<th>bank name</th>
<th>bank code</th>
<th>bank ac</th>
<th>bank ac_name</th>
<th>Amount</th>
<th>status</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php
$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])){
$q = $_REQUEST['q'];
$row = $engine->db_query2("SELECT id,bank_name,bank_code,credit_bank_ac,credit_name,credit_amount,status,credit_date FROM rechargepro_bulkpay_transactions_log WHERE credit_bank_ac LIKE ? OR bank_ac_name LIKE ? LIMIT 50",array("%$q%","%$q%")); 
	}else{
$row = $engine->db_query2("SELECT id,bank_name,bank_code,credit_bank_ac,credit_name,credit_amount,status,credit_date FROM rechargepro_bulkpay_transactions_log LIMIT $start, $per_page",array());
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id'];

$bank_name = $row[$dbc]['bank_name']; 
$bank_code = $row[$dbc]['bank_code']; 
$credit_bank_ac = $row[$dbc]['credit_bank_ac']; 
$credit_name = $row[$dbc]['credit_name']; 
$credit_amount = $row[$dbc]['credit_amount']; 
$status = $row[$dbc]['status']; 
$credit_date = $row[$dbc]['credit_date'];
    
    $aprovall = "Pending";
    if($status == 1){$aprovall = "Approved";}

    $stats = "";
    if($dbc % 2 == 0){ $stats = "stats2";}
?>
<tr >
<td><?php echo $bank_name;?></td>
<td><?php echo $bank_code;?></td>
<td><?php echo $credit_bank_ac;?></td>
<td><?php echo $credit_name;?></td>
<td><?php echo $credit_amount;?></td>
<td><?php echo $aprovall;?></td>
<td><?php echo $credit_date;?></td>
</tr>


<?php
	}
 	if(!isset($bank_name)){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>


</tbody>
</table>

</div>





