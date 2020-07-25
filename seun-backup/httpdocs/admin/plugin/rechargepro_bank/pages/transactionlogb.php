<?php
include "../../../engine.autoloader.php";


?>


<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>transaction type</th>
<th>amount</th>
<th>date Time</th>
<th>Ref</th>
<th>Account</th>
<th>status</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<?php

$permission =	$engine->admin_permission("rechargepro_bank","index");

$type = $_REQUEST['type'];


$per_page = 80;

$page = 0;
if (isset($_REQUEST['page'])){$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$row = $engine->db_query("SELECT id,transaction_type,refid,amount,naration,acnumber,status,date FROM bank_alert WHERE refid LIKE ? OR naration LIKE ? ORDER BY id DESC LIMIT 50",array("%$q%","%$q%")); 
	}else{
	   if($type == "All"){
$row = $engine->db_query("SELECT id,transaction_type,refid,amount,naration,acnumber,status,date FROM bank_alert ORDER BY id DESC LIMIT $start, $per_page",array());
}else{
$row = $engine->db_query("SELECT id,transaction_type,refid,amount,naration,acnumber,status,date FROM bank_alert WHERE transaction_type = ? ORDER BY id DESC LIMIT $start, $per_page",array($type));
}
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
   $id = $row[$dbc]['id'];
   $transaction_type = $row[$dbc]['transaction_type']; 
   $refid = $row[$dbc]['refid']; 
   $amount = $row[$dbc]['amount']; 
   $naration = $row[$dbc]['naration']; 
   $acnumber = $row[$dbc]['acnumber']; 
   $status = $row[$dbc]['status']; 
   $date = $row[$dbc]['date'];
   
    $st = "";
    if($status == "1"){ $st = "SYNC";}
    
?>
<tr >
<td><?php echo $transaction_type;?></td>
<td><?php echo $amount;?></td>
<td><?php echo $date;?></td>
<td><?php echo $refid;?></td>
<td><?php echo $acnumber;?></td>
<td><?php echo $st;?></td>
<td><?php echo $naration;?></td>



</tr>
<?php
	}
    
    
 	if(!isset($id)){ echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>


</tbody>
</table>

</div>





