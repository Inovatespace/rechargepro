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
<th>USER</th>
<th>TOTAL DEPOSITE</th>
</tr>
</thead>
<tbody>
<?php
$startdate = "2018-01-01";
$enddate = date('Y-m-d 23:23:23', strtotime('+0 day', strtotime(date("Y-m-d"))));



$permission =	$engine->admin_permission("rechargepro_transactionlog","index");


function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}
$per_page = 530;

$page = 1;
//if (isset($_REQUEST['page'])){$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;

$row = $engine->db_query2("SELECT * FROM (
  SELECT rechargepro_transaction_log.rechargeproid, rechargepro_account.rechargeprorole, SUM(rechargepro_transaction_log.amount) amt, rechargepro_transaction_log.rechargepro_service, rechargepro_transaction_log.rechargepro_subservice, SUM(rechargepro_transaction_log.refererprofit) AS rp, SUM(rechargepro_transaction_log.agentprofit) AS ap, SUM(rechargepro_transaction_log.cordprofit) AS cp, SUM(rechargepro_transaction_log.rechargeproprofit) as bp FROM rechargepro_transaction_log JOIN rechargepro_account ON rechargepro_account.rechargeproid = rechargepro_transaction_log.rechargeproid WHERE transaction_date BETWEEN ? AND ? AND rechargepro_account.rechargeprorole < '6' AND rechargepro_transaction_log.rechargepro_service = 'Credit'  GROUP BY rechargeproid
) v  ORDER BY rechargeprorole DESC",array($startdate,$enddate));


                
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    

    $rechargeproprofit = $row[$dbc]['bp'];
    $cordprofit = $row[$dbc]['cp'];
    $agentprofit = $row[$dbc]['ap'];
    $refererprofit = $row[$dbc]['rp'];
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];
    $amount = $row[$dbc]['amt'];
    $rechargeproid = $row[$dbc]['rechargeproid'];
$rechargeprorole = $row[$dbc]['rechargeprorole'];

$r = "User";
if($rechargeprorole == "1"){ $r = "Distributor";}
if($rechargeprorole == "2"){ $r = "Agent";}
if($rechargeprorole == "3"){ $r = "Cashier";}
?>
<tr >
<td><?php echo myname($rechargeproid,$engine);?></td>
<td><?php echo $r;?></td>
<td><?php echo "&#8358;".$amount;?></td>
</tr>
<?php
	}
    
    
 	if(!isset($amount)){  echo "<div style='overflow:hidden; padding:5%;'> 
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>";}?>


</tbody>
</table>

</div>





