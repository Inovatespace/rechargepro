<?php
include "../../../../engine.autoloader.php";

?>

<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px; overflow: hidden;">

<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>#</th>
<th>NAME</th>
<th>EMAIL</th>
<th>#</th>
<th>SOURCE</th>
<th>STATUS</th>
<th>DATE ADDED</th>
</tr>
</thead>
<tbody>

<?php
$email = $engine->get_session("rechargeproemail");
$per_page = 30; 
$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

if (isset($_REQUEST['q'])){
$q = $_REQUEST['q'];  
$row = $engine->db_query("SELECT id,invite_sourse,sentemail,sentname,toemail,toname,status,date FROM myinvite WHERE sentemail = ? AND (toemail LIKE ? OR toname LIKE ?)  LIMIT 50",array($email,"%$q%","%$q%")); 
	}else{
$row = $engine->db_query("SELECT id,invite_sourse,sentemail,sentname,toemail,toname,status,date FROM myinvite WHERE sentemail = ? ORDER BY id DESC LIMIT $start, $per_page", array($email));
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $id = $row[$dbc]['id']; 
    $invite_sourse = $row[$dbc]['invite_sourse']; 
    $sentemail = $row[$dbc]['sentemail']; 
    $sentname = $row[$dbc]['sentname']; 
    $toemail = $row[$dbc]['toemail']; 
    $toname = $row[$dbc]['toname']; 
    $status = $row[$dbc]['status']; 
    $date = $row[$dbc]['date'];
    
    switch ($invite_sourse){ 
	case "csv": $dt = 'fas fa-file-csv';
	break;

	case "yahoo": $dt = 'fab fa-yahoo';
	break;

	case "google": $dt = 'fab fa-google';
	break;
    
    case "outlook": $dt = 'fas fa-envelope';
	break;

	default : $dt = 'fas fa-share-alt-square';
}

    $st = "Invite Processing";
    if($status == "1"){
       $st = "Invite Sent <span class='fas fa-check-circle' style='color:#24A20B;'></span>"; 
    }
?>
<tr >
<td>#</td>
<td><?php echo $toname;?></td>
<td><?php echo $toemail;?></td>
<td><span class='<?php echo $dt;?>'></span></td>
<td><?php echo $invite_sourse;?></td>
<td><?php echo $st;?></td>
<td><?php echo $date;?></td>
</tr>
<?php
	}
?>

    
</tbody>
</table>

</div>

