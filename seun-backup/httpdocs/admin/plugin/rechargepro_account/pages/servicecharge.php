<?php
$engine = new engine();
$sentid = $_REQUEST['id'];
$myname = $_REQUEST['name'];
?>
<script type="text/javascript">
$(document).ready(function () {

    $("#search").keyup(function () {
        var searchbox = $(this).val();
        var active = "0";
        $("input[id='active']:checked").each(function(i){
active = "1";
});
        var dataString = 'q=' + searchbox+"&active="+active;

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/membersb.php",
                data: dataString,
                cache: false,
                success: function (html) {
                      $("#pagination").hide();
                      $("#page-content").html(html);

                }


            });
        }
        return false;

    });


});

</script>

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />





<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Service Charge for {<?php echo $myname;?>}</div></div>



<div class="profilebg" id="acholder" style="border:solid 1px #EEEEEE; overflow:hidden;">







<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>#</th>
<th>services code</th>
<th>service name</th>
<th>Vertise</th>
<th>Agent</th>
<th>Distributor</th>
<th>Total</th>
<th>Service Charge</th>
<th>Category</th>
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



$myservicearray = array();
$row = $engine->db_query2("SELECT id,services_key,cordinator_percentage,percentage,bill_formular,dateadeded,bill_rechargeprofull_percentage FROM rechargepro_services_agent WHERE rechargeproid = ?",array($sentid));
$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $percentage = $row[$dbc]['percentage']; 
    $bill_formular = $row[$dbc]['bill_formular'];  
    $dateadeded = $row[$dbc]['dateadeded'];
    $cordinator_percentage = $row[$dbc]['cordinator_percentage'];
    $bill_rechargeprofull_percentage = $row[$dbc]['bill_rechargeprofull_percentage'];
    
  $myservicearray[$services_key] = array($id,$services_key,$percentage,$bill_formular,$dateadeded,$cordinator_percentage,$bill_rechargeprofull_percentage);  
    
    }
    
    
$mychargearray = array();
$row = $engine->db_query2("SELECT id,services_key,fixedfee,dateadeded FROM rechargepro_services_fixed WHERE rechargeproid = ?",array($sentid));
$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $fixedfee = $row[$dbc]['fixedfee'];  
    $dateadeded = $row[$dbc]['dateadeded'];
    
  $mychargearray[$services_key] = array($id,$services_key,$fixedfee,$dateadeded);  
    
    }   


$row = $engine->db_query2("SELECT vertisservice_charge,bill_rechargeprofull_percentage,cordinator_percentage,id,services_key,service_name,percentage,bill_formular,services_category,status,dateadeded FROM rechargepro_services ORDER BY id DESC LIMIT 500",array());
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
    $bill_rechargeprofull_percentage = $row[$dbc]['bill_rechargeprofull_percentage'];
    $vertisservice_charge = $row[$dbc]['vertisservice_charge'];
    
    $serviceid = "";
    if(isset($myservicearray[$services_key])){
    $serviceid = $myservicearray[$services_key][0];    
    $percentage = $myservicearray[$services_key][2]; 
    $bill_formular = $myservicearray[$services_key][3];  
    $dateadeded = $myservicearray[$services_key][4];
    $cordinator_percentage = $myservicearray[$services_key][5];
   // $bill_rechargeprofull_percentage = $myservicearray[$services_key][6];
    }
    
    $fixedid = "";
    if(isset($mychargearray[$services_key])){
    $fixedid = $mychargearray[$services_key][0]; 
    $vertisservice_charge = $mychargearray[$services_key][2];  
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


     $st = '<div class="redmenu radious3 shadow" style="margin:5px; padding:2px 4px; overflow:hidden; display:inline-block; ">Not Active</div>'; 
     
          
    if($status == 1){
     $st = '<div class="greenmenu radious3 shadow" style="margin:5px; padding:2px 4px; display:inline-block; overflow:hidden;">Active</div>'; 
     $del = '<span title="Disable Account" onclick="setactive(\''.$id.'\',\'0\')" class="fa fa-power-off" style="cursor:pointer; color:#2DE910; font-size:150%"></span>';  
    }
    
?>
<tr >
<td><img src="../theme/classic/icons/<?php echo $id;?>.jpg" style="padding: 2px; border: solid 1px #EEEEEE;" width="40"/></td>
<td><?php echo $services_key;?> <?php if($permission >= 3){ ?><a class="tunnel" name="plugin/rechargepro_account/pages/servicecharge/servicecharge.php?width=700&id=<?php echo $id;?>&name=<?php echo urlencode($myname);?>&sentid=<?php echo $sentid;?>"><span class="fas fa-edit"></span></a><?php }?></td>
<td><?php echo $service_name;?></td>
<th><?php echo $bill_rechargeprofull_percentage-$percentage-$cordinator_percentage;?></th>
<th><?php echo $percentage;?><?php echo $what;?></th>
<th><?php echo $cordinator_percentage;?></th>
<th style="font-weight: bold; color:purple;"><?php echo $bill_rechargeprofull_percentage;?></th>
<td><?php echo $vertisservice_charge;?></td>
<td><?php echo $services_category;?></td>

<td><?php echo $st?></td>
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










</div>












