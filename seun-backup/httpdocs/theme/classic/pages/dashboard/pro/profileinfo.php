<?php 
include "../../../../../engine.autoloader.php";
$id = $_REQUEST['id'];
?>

<table style="margin-top: -20px;" id="myTable" class="tablesorter">
<thead>
<tr>
<th>Name</th>
<th>Email</th>
<th>Mobile</th>
<th>Wallet</th>
<th>Call Back URL</th>
<th>Bank Name</th>
<th>Bank AC Name</th>
<th>Bank AC Number</th>
<th>Reg Date</th>
</tr>
</thead>
<tbody>

<?php
$row = $engine->db_query("SELECT name,email,mobile,ac_ballance,call_back_url,bank_name,bank_ac_name,bank_ac_number,created_date FROM recharge4_account WHERE recharge4id = ?",array($id));

for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){

  
  $name = $row[$dbc]['name']; 
  $email = $row[$dbc]['email']; 
  $mobile = $row[$dbc]['mobile']; 
  $ac_ballance = $row[$dbc]['ac_ballance']; 
  $call_back_url = $row[$dbc]['call_back_url']; 
  $bank_name = $row[$dbc]['bank_name']; 
  $bank_ac_name = $row[$dbc]['bank_ac_name']; 
  $bank_ac_number = $row[$dbc]['bank_ac_number']; 
  $created_date = $row[$dbc]['created_date'];

?>
<tr>
<td><?php echo $name;?></td>
<td><?php echo $email;?></td>
<td><?php echo $mobile;?></td>
<td><?php echo $ac_ballance;?></td>
<td><?php echo $call_back_url;?></td>
<td><?php echo $bank_name;?></td>
<td><?php echo $bank_ac_name;?></td>
<td><?php echo $bank_ac_number;?></td>
<td><?php echo $created_date;?></td>
</tr>
<?php
	}
?>
</tbody>
</table>
