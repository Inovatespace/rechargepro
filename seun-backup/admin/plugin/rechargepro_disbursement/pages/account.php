<?php
include "../../../engine.autoloader.php";


?>
   <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
  
  function loguser(Id){
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'You are about to log the sellected user in, proceed?',
    buttons: {
        confirm: function () {
            
             $.ajax({
                type: "POST",
                url: "plugin/rechargepro_disbursement/pages/pro/login.php",
                data: "buserid="+Id,
                cache: false,
                success: function (html) {
                   window.open('https://rechargepro.com.ng', '_blank');
                }
            });     
      
        },
        cancel: function () {
            
        }
    }
});
  
}
</script>
<style type="text/css">
.tablesorter .yes:nth-child(even) {background: #FAF9F9; color:#094C87;}
.tablesorter .yes:nth-child(odd) {background: #FAF9F9; color:#094C87;}
</style>


<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>#</th>
<th>Admin</th>
<th>Access Level</th>
<th>Bank Name</th>
<th>Bank AC Name</th>
<th>Bank AC</th>
<th>Bank Code</th>
</tr>
</thead>
<tbody>
<?php
$permission =	$engine->admin_permission("rechargepro_disbursement","index");




function  name($id,$engine){
if($id == 0){return "-";}


$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));

return $row[0]['name'];
}

function role($role){
    switch ($role){ 
	case 1: return "APPROVAL";
	break;

	case 2: return "REVIEWER";
	break;

	case 3: return "OPERATOR";
	break;
}
}

$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$row = $engine->db_query2("SELECT id,rechargeproid,bank_ac_name,bank_ac,bank_name,accesslevel,bank_code FROM rechargepro_bulkpay_ac WHERE (bank_firstname LIKE ? OR bank_ac LIKE ?)  LIMIT 50",array("%$q%","%$q%")); 
	}else{
$row = $engine->db_query2("SELECT id,rechargeproid,bank_ac_name,bank_ac,bank_name,accesslevel,bank_code FROM rechargepro_bulkpay_ac ORDER BY id DESC LIMIT $start, $per_page",array());
}

$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    
  $id = $row[$dbc]['id']; 
  $rechargeproid = name($row[$dbc]['rechargeproid'],$engine); 
  $bank_ac_name = $row[$dbc]['bank_ac_name']; 
  $bank_ac = $row[$dbc]['bank_ac']; 
  $bank_name = $row[$dbc]['bank_name']; 
  $accesslevel = $row[$dbc]['accesslevel'];
  $bank_code = $row[$dbc]['bank_code'];
    


    
?>
<tr style="font-weight: bold;">
<td>#</td>
<td><?php echo $rechargeproid;?></td>
<td><?php echo $accesslevel;?></td>
<td><?php echo $bank_name;?></td>
<td><?php echo $bank_ac_name;?></td>
<td><?php echo $bank_ac;?></td>
<td><?php echo $bank_code;?></td>
<td><span class="fa fa-power-off" style="cursor:pointer; color:#2DE910; font-size:150%"></span></td>
</tr>
<?php
	$rowb = $engine->db_query2("SELECT rechargeproid,permission FROM rechargepro_bulkpay_access WHERE rechargepro_bulkpay_ac_id = ?",array($id));
for($dbcb = 0; $dbcb < $engine->array_count($rowb); $dbcb++){
    $subadmin = name($rowb[$dbcb]['rechargeproid'],$engine); ;
    $permission = role($rowb[$dbcb]['permission']);
?>
<tr class="yes">
<td >#</td>
<td ><?php echo $subadmin;?></td>
<td colspan="5"><?php echo $permission;?></td>
</tr>
<?php
	}
?>
<?php
	}
 	if(!isset($id)){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>






</tbody>
</table>

</div>







