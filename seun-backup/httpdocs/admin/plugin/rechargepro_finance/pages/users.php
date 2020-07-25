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
<th>TOTALSALES</th>
<th>rechargepro PROFIT</th>
<th>DISTRIBUTOR PROFIT</th>
<th>AGENT PROFIT</th>
<th>REFEREER PROFIT</th>
</tr>
</thead>
<tbody>
<?php
$startdate = $_REQUEST['start'];
$enddate = date('Y-m-d 23:23:23', strtotime('+0 day', strtotime($_REQUEST['end'])));


$mytid = array();
$row = $engine->db_query2("SELECT tid FROM refund_process",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $mytid[] = $row[$dbc]['tid'];
    
    }

$mytid = implode(",",$mytid);


$permission =	$engine->admin_permission("rechargepro_transactionlog","index");


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

$row = $engine->db_query2("SELECT * FROM (
  SELECT rechargepro_transaction_log.rechargeproid, SUM(rechargepro_transaction_log.amount) amt, rechargepro_transaction_log.rechargepro_service, rechargepro_transaction_log.rechargepro_subservice, SUM(rechargepro_transaction_log.refererprofit) AS rp, SUM(rechargepro_transaction_log.agentprofit) AS ap, SUM(rechargepro_transaction_log.cordprofit) AS cp, SUM(rechargepro_transaction_log.rechargeproprofit) as bp FROM rechargepro_transaction_log JOIN rechargepro_account ON rechargepro_account.rechargeproid = rechargepro_transaction_log.rechargeproid WHERE transaction_date BETWEEN ? AND ? AND  rechargepro_transaction_log.transactionid NOT IN ($mytid) AND rechargepro_account.name LIKE ? AND rechargepro_subservice NOT IN('PROFIT','Debit','Credit','TRANSFER','TOPUP','REWARD')  AND rechargepro_status = 'PAID'
  GROUP BY rechargeproid
) v  ORDER BY amt DESC LIMIT 50",array($startdate,$enddate,"%$q%"));
	}else{
$row = $engine->db_query2("SELECT * FROM (
  SELECT rechargepro_transaction_log.rechargeproid, SUM(rechargepro_transaction_log.amount) amt, rechargepro_transaction_log.rechargepro_service, rechargepro_transaction_log.rechargepro_subservice, SUM(rechargepro_transaction_log.refererprofit) AS rp, SUM(rechargepro_transaction_log.agentprofit) AS ap, SUM(rechargepro_transaction_log.cordprofit) AS cp, SUM(rechargepro_transaction_log.rechargeproprofit) as bp FROM rechargepro_transaction_log JOIN rechargepro_account ON rechargepro_account.rechargeproid = rechargepro_transaction_log.rechargeproid WHERE transaction_date BETWEEN ? AND ? AND rechargepro_subservice NOT IN('PROFIT','Debit','Credit','TRANSFER','TOPUP','REWARD') AND rechargepro_transaction_log.transactionid NOT IN ($mytid)  AND rechargepro_status = 'PAID'
  GROUP BY rechargeproid
) v  ORDER BY amt DESC LIMIT $start, $per_page",array($startdate,$enddate));
}
                
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){

    $rechargeproprofit = $row[$dbc]['bp'];
    $cordprofit = $row[$dbc]['cp'];
    $agentprofit = $row[$dbc]['ap'];
    $refererprofit = $row[$dbc]['rp'];
    $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];
    $amount = $row[$dbc]['amt'];
    $rechargeproid = $row[$dbc]['rechargeproid'];

?>
<tr >
<td><?php echo myname($rechargeproid,$engine);?></td>
<td><?php echo "&#8358;".$amount;?></td>
<td><?php echo "&#8358;".$rechargeproprofit;?></td>
<td><?php echo "&#8358;".$cordprofit;?> </td>
<td><?php echo "&#8358;".$agentprofit;?></td>
<td><?php echo "&#8358;".$refererprofit;?></td>
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





