<?php
include "../../../../engine.autoloader.php";
$id = $_REQUEST['id'];
$sentid = $_REQUEST['sentid'];
$myname = $_REQUEST['name'];



$delete = "";

$row = $engine->db_query2("SELECT vertisservice_charge,bill_rechargeprofull_percentage,cordinator_percentage,id,services_key,service_name,percentage,bill_formular FROM rechargepro_services WHERE id = ? LIMIT 1",array($id));
$sub = array();

    $id = $row[0]['id']; 
    $services_key = $row[0]['services_key']; 
    $service_name = $row[0]['service_name']; 
    $percentage = $row[0]['percentage']; 
    $bill_formular = $row[0]['bill_formular']; 
    $cordinator_percentage = $row[0]['cordinator_percentage'];
    $bill_rechargeprofull_percentage = $row[0]['bill_rechargeprofull_percentage'];
    $vertisservice_charge = $row[0]['vertisservice_charge'];
    
    $total = $bill_rechargeprofull_percentage;
    
    
    
    
    if(isset($_REQUEST['delete'])){
       
$mypeople = array();
$row = $engine->db_query2("SELECT rechargeproid FROM rechargepro_account WHERE profile_creator = ? AND rechargeprorole < '4' ",array($sentid));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $mypeople[] = $row[$dbc]['rechargeproid'];
}
    
  
     
//delete all if no cordinator
$engine->db_query2("DELETE FROM rechargepro_services_agent WHERE services_key=? AND rechargeproid = ?",array($services_key,$sentid));
$engine->db_query2("DELETE FROM rechargepro_services_fixed WHERE services_key=? AND rechargeproid = ?",array($services_key,$sentid));
if(count($mypeople) > 0){
   // echo "hhhhhhhhh";
$array = implode(",",$mypeople);
$engine->db_query2("DELETE FROM rechargepro_services_agent WHERE services_key=? AND rechargeproid IN ($array)",array($services_key));
$engine->db_query2("DELETE FROM rechargepro_services_fixed WHERE services_key=? AND rechargeproid IN ($array)",array($services_key));
}

exit;
        
        }
    
    
 


if(isset($_REQUEST['servicecharge'])){
    
    
$agent= $_REQUEST['agent']; 
$cordinator= $_REQUEST['cordinator']; 
$what= $_REQUEST['what']; 
$servicecharge= $_REQUEST['servicecharge'];
$newvertise = $total-($agent+$cordinator);

$d = 0;
$t = 0;

if($agent == $percentage){
$d++;   
$t++; 
}

if($cordinator == $cordinator_percentage){
$d++;   
$t++;
}

if($what == $bill_formular){
$d++;   
$t++;
}


if($servicecharge == $vertisservice_charge){
$d++;   
}



$mypeople = array();
$row = $engine->db_query2("SELECT rechargeproid FROM rechargepro_account WHERE  profile_creator = ? AND rechargeprorole < '4' ",array($sentid));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $mypeople[] = $row[$dbc]['rechargeproid'];
}




$engine->db_query2("DELETE FROM rechargepro_services_agent WHERE services_key=? AND rechargeproid = ?",array($services_key,$sentid));

if(count($mypeople) > 0){
$array = implode(",",$mypeople);
$engine->db_query2("DELETE FROM rechargepro_services_agent WHERE services_key=? AND rechargeproid IN ($array)",array($services_key));
}




 $engine->db_query2("INSERT INTO rechargepro_services_agent (cordinator_percentage,percentage,bill_formular,rechargeproid,services_key,bill_rechargeprofull_percentage) VALUES (?,?,?,?,?,?)",array($cordinator,$agent,$what,$sentid,$services_key,$newvertise));   




foreach($mypeople AS $sentidb){
 $engine->db_query2("INSERT INTO rechargepro_services_agent (cordinator_percentage,percentage,bill_formular,rechargeproid,services_key,bill_rechargeprofull_percentage) VALUES (?,?,?,?,?,?)",array($cordinator,$agent,$what,$sentidb,$services_key,$newvertise));   
}
    
    
    

//used to be ==

$engine->db_query2("DELETE FROM rechargepro_services_fixed WHERE services_key=? AND rechargeproid = ?",array($services_key,$sentid));

if(count($mypeople) > 0){
$array = implode(",",$mypeople);
$engine->db_query2("DELETE FROM rechargepro_services_fixed WHERE services_key=? AND rechargeproid IN ($array)",array($services_key));
}



//SERVICE CHARGE
if($servicecharge > 0){
 $engine->db_query2("INSERT INTO rechargepro_services_fixed (fixedfee,rechargeproid,services_key) VALUES (?,?,?)",array($servicecharge,$sentid,$services_key));   



foreach($mypeople AS $sentidb){
 $engine->db_query2("INSERT INTO rechargepro_services_fixed (fixedfee,rechargeproid,services_key) VALUES (?,?,?)",array($servicecharge,$sentidb,$services_key));   
 
}
    
    }









exit;
}



















   
    
$rowb = $engine->db_query2("SELECT id,services_key,cordinator_percentage,percentage,bill_formular,dateadeded FROM rechargepro_services_agent WHERE rechargeproid = ? AND services_key = ?",array($sentid,$services_key));
if(!empty($rowb[0]['percentage'])){
$percentage = $rowb[0]['percentage']; 
$bill_formular = $rowb[0]['bill_formular'];  
$cordinator_percentage = $rowb[0]['cordinator_percentage'];
$delete = 1;
}
  
    
    
$rowc = $engine->db_query2("SELECT id,services_key,fixedfee,dateadeded FROM rechargepro_services_fixed WHERE rechargeproid = ? AND services_key = ?",array($sentid,$services_key));
if(!empty($rowc[0]['fixedfee'])){
$vertisservice_charge = $rowc[0]['fixedfee'];  
$delete = 1;
}
 
    

    switch ($bill_formular){ 
	case "0":
    $what = '<option value="0">%</option>';
	break;
    
	case "1":
    $what = '<option value="1">+</option>';
	break;

      
	default : $what = "";
}


?>


<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />





<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Service Charge for {<?php echo $myname;?>}</div></div>



<div class="profilebg" id="acholder" style="border:solid 1px #EEEEEE; overflow:hidden;">







<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>service name</th>
<th>Agent</th>
<th>Distributor</th>
<th>Type</th>
<th>Service Charge</th>
</tr>
</thead>
<tbody>
<tr>
<td><?php echo $service_name;?></td>
<td><input class="input" value="<?php echo $percentage;?>" id="agent" style="width: 70px; border: solid 1px #0E4DAA;" /></td>
<td><input class="input" value="<?php echo $cordinator_percentage;?>" id="cordinator" style="width: 70px; border: solid 1px #0E4DAA;" /></td>
<td><select class="input" id="what" style="width: 70px; border: solid 1px #0E4DAA;">
<?php echo $what;?>
	<option value="0">%</option>
    <option value="1">+</option>
</select></td>
<td><input class="input" value="<?php echo $vertisservice_charge;?>" id="servicecharge" style="width: 70px; border: solid 1px #0E4DAA;" /> <?php if($delete == 1){echo '<span class="fas fa-trash" onclick="deleteit();" style="color:#A71717; font-size:150%;"></span>';}?></td>
</tr>


<tr >
<td colspan="3">-</td>
<td>-</td>
<td><input class="activemenu shadow" onclick="save()" value="SAVE" style="text-align:center; padding:4px 0px; width: 100%; border: none; margin:3px; cursor: pointer;" /></td>
</tr>

</tbody>
</table>

</div>



<script type="text/javascript">
function save(){
    
var agent = $("#agent").val();
var cordinator = $("#cordinator").val();
var what = $("#what").val();
var servicecharge = $("#servicecharge").val();

if(empty(agent) ||empty(cordinator) ||empty(what) ||empty(servicecharge)){
    $.alert("All fields are compulsory");
}

 var topost = "agent="+agent+"&cordinator="+cordinator+"&what="+what+"&servicecharge="+servicecharge+"&id=<?php echo $id;?>&sentid=<?php echo $sentid;?>&name=<?php echo urlencode($myname);?>";


            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/servicecharge/servicecharge.php",
                data: topost,
                cache: false,
                success: function (html) {
                    // console.log(html);
                     window.location.reload();

                }
                });

}


function deleteit(){
    
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to Delete this User?',
    buttons: {
        confirm: function () {
            

 var topost = "delete=delete&id=<?php echo $id;?>&sentid=<?php echo $sentid;?>&name=<?php echo $myname;?>";


            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/servicecharge/servicecharge.php",
                data: topost,
                cache: false,
                success: function (html) {
                     console.log(html);
                     window.location.reload();

                }
                });
                
                
                    
    },
        cancel: function () {
            
        }
    }
}); 

}
</script>





</div>