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
<th>naration</th>
<th>level</th>
<th>approval</th>
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
$row = $engine->db_query2("SELECT id,rechargepro_bulkpay_ac_id,bank_name,bank_code,bank_ac,bank_ac_name,totalamount,naration,level,aproval,datecreated FROM rechargepro_bulkpay_transactions WHERE bank_ac LIKE ? OR bank_ac_name LIKE ? LIMIT 50",array("%$q%","%$q%")); 
	}else{
$row = $engine->db_query2("SELECT id,rechargepro_bulkpay_ac_id,bank_name,bank_code,bank_ac,bank_ac_name,totalamount,naration,level,aproval,datecreated FROM rechargepro_bulkpay_transactions LIMIT $start, $per_page",array());
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id'];
    $rechargepro_bulkpay_ac_id = $row[$dbc]['rechargepro_bulkpay_ac_id'];
    $bank_name = $row[$dbc]['bank_name'];
    $bank_code = $row[$dbc]['bank_code'];
    $bank_ac = $row[$dbc]['bank_ac'];
    $bank_ac_name = $row[$dbc]['bank_ac_name'];
    $totalamount = $row[$dbc]['totalamount'];
    $naration = $row[$dbc]['naration'];
    $level = $row[$dbc]['level'];
    $aproval = $row[$dbc]['aproval'];
    $datecreated = $row[$dbc]['datecreated'];
    
    
    $aprovall = "Pending";
    if($aproval == 1){$aprovall = "Approved";}

    $stats = "";
    if($dbc % 2 == 0){ $stats = "stats2";}
?>
<tr >
<td><?php echo $bank_name;?></td>
<td><?php echo $bank_code;?></td>
<td><?php echo $bank_ac;?></td>
<td><?php echo $bank_ac_name;?></td>
<td><?php echo $totalamount;?></td>
<td><?php echo $naration;?></td>
<td><?php echo $level;?></td>
<td><?php echo $aprovall;?></td>
<td><?php echo $datecreated;?></td>
<td><span class="fa fa-eye"></span></td>
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





