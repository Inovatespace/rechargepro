<?php 
include "../../../../../engine.autoloader.php";
$id = $_REQUEST['id'];
$profile_role = $engine->get_session("recharge4role"); 
$profile_creator = $engine->get_session("recharge4id");
?>

<script type="text/javascript">
function loadvoucher(){
    var voucher =  $("#voucher").val();

        if(empty(voucher)){
            $.alert("Invalid voucher"); return false;
        }
    
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/dashboard/pro/controlac.php",
		data: "id=<?php echo $id;?>&voucher="+voucher,
		cache: false,
		success: function(html) {
		  if(html.trim() != "ok"){
		      $.alert(html);
		      }else{
           window.location.reload();
           }
		}
	});
}
</script>

<div class="whitemenu" style="padding: 10px; margin-top:-15px;">
<div style="overflow: hidden; padding:5px;">
<input style="float: left; width:35%;" id="voucher" class="input" type="text" placeholder="Enter Voucher" />
<div style="padding: 5px; text-align:center; cursor:pointer; float:left; width:30%;" onclick="loadvoucher();" class="middlemenu">LOAD VOUCHER</div>
</div>


</div>












<table style="margin-top:10px;" id="myTable" class="tablesorter">
<thead>
<tr>
<th>Transaction Date</th>
<th>Amount</th>
<th>Type</th>
<th>Offficer</th>
</tr>
</thead>
<tbody>
<?php
function merchant($id,$engine){
 $row = $engine->db_query("SELECT name FROM recharge4_account WHERE recharge4id = ? LIMIT 1",array($id));
 return $row[0]['name'];
}

$row = $engine->db_query("SELECT recharge4id,officer,amount,ptype,date FROM payout_log WHERE recharge4id = ?",array($id));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
 $officer = $row[$dbc]['officer']; 
 $amount = $row[$dbc]['amount']; 
 $ptype = $row[$dbc]['ptype']; 
 $date = $row[$dbc]['date'];
?>
<tr>
<td><?php echo $date;?></td>
<td><?php echo $amount;?></td>
<td><?php echo $ptype;?></td>
<td><?php echo merchant($officer,$engine);?></td>
</tr>
<?php
	}
?>
</tbody>
</table>
