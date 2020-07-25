<?php
$engine = new engine();



$recharge4role = $engine->get_session("recharge4role"); 
$profile_creator = $engine->get_session("recharge4id");

if(isset($_REQUEST['today'])){
$today = $engine->safe_html($_REQUEST['today']);
}else{
$today = date("Y-m-d");    
}


function rangeMonth($daclassicr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($daclassicr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d 23:23:59', strtotime('last day of this month', $dt));
    return $res;
}
 
$range = rangeMonth($today);
$start = $range['start'];
$end = $range['end'];



?>




<div style="width:100%; background-color: white;">
<div style="padding:10px; overflow:hidden;">


<div style="background-color: white;">
<div style="margin-bottom:10px;">








<div id="acholder" style="overflow:hidden;">


<link rel="stylesheet" href="/java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />

<script type="text/javascript" src="/java/sort/jquery.tablesorter.js"></script>




<div style="font-size: 80%;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>service name</th>
<th>Commission</th>
<th>Service Charge</th>
<th>Category</th>
<th>Status</th>

</tr>
</thead>
<tbody>
<?php
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



$myservicearray = array();
$row = $engine->db_query("SELECT id,services_key,cordinator_percentage,percentage,bill_formular,dateadeded,bill_recharge4full_percentage FROM recharge4_services_agent WHERE recharge4id = ?",array($profile_creator));
$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $percentage = $row[$dbc]['percentage']; 
    $bill_formular = $row[$dbc]['bill_formular'];  
    $dateadeded = $row[$dbc]['dateadeded'];
    $cordinator_percentage = $row[$dbc]['cordinator_percentage'];
    $bill_recharge4full_percentage = $row[$dbc]['bill_recharge4full_percentage'];
    
  $myservicearray[$services_key] = array($id,$services_key,$percentage,$bill_formular,$dateadeded,$cordinator_percentage,$bill_recharge4full_percentage);  
    
    }
    
$mychargearray = array();
$row = $engine->db_query("SELECT id,services_key,fixedfee,dateadeded FROM recharge4_services_fixed WHERE recharge4id = ?",array($profile_creator));
$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $fixedfee = $row[$dbc]['fixedfee'];  
    $dateadeded = $row[$dbc]['dateadeded'];
    
  $mychargearray[$services_key] = array($id,$services_key,$fixedfee,$dateadeded);  
    
    }   

$row = $engine->db_query("SELECT recharge4service_charge,bill_recharge4full_percentage,cordinator_percentage,id,services_key,service_name,percentage,bill_formular,services_category,status,dateadeded FROM recharge4_services WHERE services_category != '7' ORDER BY id DESC LIMIT 500",array());
$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $service_name = $row[$dbc]['service_name']; 
    $percentage = $row[$dbc]['percentage']; 
    $bill_formular = $row[$dbc]['bill_formular']; 
    $services_category = category($row[$dbc]['services_category']); 
    $status = $row[$dbc]['status']; 
    $dateadeded = $row[$dbc]['dateadeded'];
    $cordinator_percentage = $row[$dbc]['cordinator_percentage'];
    $bill_recharge4full_percentage = $row[$dbc]['bill_recharge4full_percentage'];
    $recharge4service_charge = $row[$dbc]['recharge4service_charge'];
    
  
    $serviceid = "";
    if(isset($myservicearray[$services_key])){
    $serviceid = $myservicearray[$services_key][0];    
    $percentage = $myservicearray[$services_key][2]; 
    $bill_formular = $myservicearray[$services_key][3];  
    $dateadeded = $myservicearray[$services_key][4];
    $cordinator_percentage = $myservicearray[$services_key][5];
    $bill_recharge4full_percentage = $myservicearray[$services_key][6];
    }
    
    
    $fixedid = "";
    if(isset($mychargearray[$services_key])){
    $fixedid = $mychargearray[$services_key][0]; 
    $recharge4service_charge = $mychargearray[$services_key][2];  
    $dateadeded = $mychargearray[$services_key][3];
    }
    

    switch ($bill_formular){ 
	case "0":
    $what = "%";
	break;
    
	case "1":
    $what = "+";
	break;

      
	default : $what = "";
}


     $st = '<div style="display:inline-block; overflow:hidden; color:#C01F1F; font-weight:bold;">Not Active</div>'; 
     
          
    if($status == 1){
     $st = '<div style="display:inline-block; overflow:hidden; color:#26AC38; font-weight:bold;">Active</div>'; 
     $del = '<span title="Disable Account" onclick="setactive(\''.$id.'\',\'0\')" class="fa fa-power-off" style="cursor:pointer; color:#2DE910; font-size:150%"></span>';  
    }
    
?>
<tr >
<td><?php echo $service_name;?></td>
<th><?php if($recharge4role == "1"){ echo ($cordinator_percentage+$percentage).$what;}else{echo $percentage.$what;}?></th>
<td><?php echo $recharge4service_charge;?></td>
<td><?php echo $services_category;?></td>

<td><?php echo $st?></td>

</tr>
<?php
	}?>


</tbody>
</table>

</div>




</div></div>

</div></div></div>



