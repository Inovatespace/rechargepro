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
                url: "plugin/rechargepro_billers/pages/pro/login.php",
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



<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>#</th>
<th>services code</th>
<th>service name</th>
<th>wallet</th>
<th>Min/Max Amount</th>
<th>Dist/Agent Per</th>
<th>Category</th>
<th>Subcategory</th>
<th>Status</th>
<th>Dateadeded</th>
</tr>
</thead>
<tbody>
<?php
$permission =	$engine->admin_permission("rechargepro_billers","index");

function  category($id){
switch ($id){ 
	case "0": $return = "-";
	break;

	case "1":$return = "ELECTRICITY";
	break;

	case "2":$return = "AIRTIME";
	break;
    
    	case "3":$return = "DATA";
	break;
    
    	case "4":$return = "PIN";
	break;
    
    	case "5":$return = "TV";
	break;
    
    	case "6":$return = "LOTTERY/BETTING";
	break;
    
    	case "7":$return = "BILLS";
	break;

	default :$return = "-";
}

return $return;
}


function  sub_category($id,$engine){
if($id == 0){return "-";}


$row = $engine->db_query2("SELECT name FROM rechargepro_subcategory WHERE subcategory_id = ? LIMIT 1",array($id));

return $row[0]['name'];
}

$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$row = $engine->db_query2("SELECT minimumsales_amount,maximumsales_amount,wallet,cordinator_percentage,id,services_key,service_name,bill_return_url,bill_verify_url,percentage,bill_formular,services_category,service_subcategory,status,dateadeded FROM rechargepro_services WHERE (service_name LIKE ? OR services_key LIKE ?)  LIMIT 50",array("%$q%","%$q%")); 
	}else{
$row = $engine->db_query2("SELECT minimumsales_amount,maximumsales_amount,wallet,cordinator_percentage,id,services_key,service_name,bill_return_url,bill_verify_url,percentage,bill_formular,services_category,service_subcategory,status,dateadeded FROM rechargepro_services ORDER BY id DESC LIMIT $start, $per_page",array());
}

$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    
    if(!array_key_exists($row[$dbc]['service_subcategory'],$sub)){
    $sub[$row[$dbc]['service_subcategory']] = sub_category($row[$dbc]['service_subcategory'],$engine); 
    }
    
    
    
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $service_name = $row[$dbc]['service_name']; 
    $bill_return_url = $row[$dbc]['bill_return_url']; 
    $bill_verify_url = $row[$dbc]['bill_verify_url']; 
    $percentage = $row[$dbc]['percentage']; 
    $bill_formular = $row[$dbc]['bill_formular']; 
    $services_category = category($row[$dbc]['services_category']); 
    $service_subcategory = $sub[$row[$dbc]['service_subcategory']];
    $status = $row[$dbc]['status']; 
    $dateadeded = $row[$dbc]['dateadeded'];
    
    $wallet = $row[$dbc]['wallet'];
    $cordinator_percentage = $row[$dbc]['cordinator_percentage'];
    
     $minimumsales_amount = $row[$dbc]['minimumsales_amount']; 
     $maximumsales_amount = $row[$dbc]['maximumsales_amount'];
    
    switch ($bill_formular){ 
	case "0":
    $what = "%";
	break;
    
	case "1":
    $what = "+";
	break;

      
	default : $what = "";
}


     $st = '<div class="redmenu radious3 shadow" style="margin:5px; padding:2px 4px; overflow:hidden; display:inline-block; ">Not Active</div>'; 
     $del = '<span onclick="setactive(\''.$id.'\',\'1\')" title="Enable Account" class="fa fa-power-off" style="cursor:pointer; color:#B72C2C; font-size:150%"></span>';
          
    if($status == 1){
     $st = '<div class="greenmenu radious3 shadow" style="margin:5px; padding:2px 4px; display:inline-block; overflow:hidden;">Active</div>'; 
     $del = '<span title="Disable Account" onclick="setactive(\''.$id.'\',\'0\')" class="fa fa-power-off" style="cursor:pointer; color:#2DE910; font-size:150%"></span>';  
    }
    
?>
<tr >
<td><a href="rechargepro_billers&p=biller_log&id=<?php echo $id;?>"><img src="../theme/classic/icons/<?php echo $id;?>.jpg" style="padding: 2px; border: solid 1px #EEEEEE;" width="40"/></a></td>
<td><?php echo $services_key;?> <?php if($permission >= 3){ ?><a href="rechargepro_billers&p=editbiller&id=<?php echo $id;?>"><span class="fas fa-edit"></span></a><?php }?></td>
<td><?php echo $service_name;?></td>
<td><?php echo $wallet;?></td>
<td><?php echo $minimumsales_amount." / ".$maximumsales_amount;?></td>
<td><?php echo $cordinator_percentage." / ".$percentage;?><?php echo $what;?></td>
<td><?php echo $services_category;?></td>
<td><?php echo $service_subcategory;?></td>
<td><?php echo $st." ";  if($permission >= 3){ echo $del;}?></td>
<td><?php echo $dateadeded;?></td>
</tr>
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







