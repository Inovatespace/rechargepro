<?php
$engine = new engine();
if(!$engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};




$rechargeprorole = $engine->get_session("rechargeprorole"); 
$profile_creator = $engine->get_session("rechargeproid");

if(isset($_REQUEST['today'])){
$today = $engine->safe_html($_REQUEST['today']);
}else{
$today = date("Y-m-d");    
}

if($rechargeprorole > 2){ echo "<meta http-equiv='refresh' content='0;url=/transactionlog'>"; exit;};

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
<script type="text/javascript">
$(document).ready(function () {

    $("#searchb").keyup(function () {
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
                url: "/theme/classic/pages/agentb.php",
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


function updaclassicatus(Id,what){
    
    var status = 1;
    if($("#all"+what+Id).prop("checked") == true){
        status = 2;
    }
    
    var my_proile = "<?php echo $profile_creator;?>";

	if(my_proile != Id){

            $.ajax({
                type: "POST",
                url: "/theme/classic/pages/profile/pro/statuschange.php",
                data: "trechargeproid="+Id+"&status="+status+"&what="+what,
                cache: false,
                success: function (html) {
                     window.location.reload();
                }
            }); 
          
	}

}

</script>



<div style="width:100%;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:5px 0px; overflow:hidden;">

<div style="background-color: white;">
<div style="margin-bottom:10px;">







<div class="profilebg" style="overflow:hidden; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Agent Account Manager</div></div>
<div class="shadow" style="margin-bottom: 10px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="searchb" type="text" placeholder="Name / Email / Mobile" style="padding:5px 5px; width: 90%;" class="input" /></div>

<?php
	
if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        
    }else{
     ?>
<button style="cursor:pointer; float: right; border: none; padding:5px 10px; margin:3px;"  name="theme/classic/pages/profile/pro/newprofile.php?width=300" class="tunnel mainbg shadow"><span class="fas fa-user-tie"></span> New Profile</button>
<?php   
    }
}else{
?>
<button style="cursor:pointer; float: right; border: none; padding:5px 10px; margin:3px;"  name="theme/classic/pages/profile/pro/newprofile.php?width=300" class="tunnel mainbg shadow"><span class="fas fa-user-tie"></span> New Profile</button>
<?php
	}
?>
<a href="payment_daily"><button style="cursor:pointer; float: right; border: none; padding:5px 10px; margin:3px;" class="greenmenu shadow" ><span class="fas fa-calendar-alt"></span> Daily Sales</button></a>
</div>






<div id="acholder" style="overflow:hidden;">


<link rel="stylesheet" href="/java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />

<script type="text/javascript" src="/java/sort/jquery.tablesorter.js"></script>

<script type="text/javascript" src="/java/jquery.twbsPagination.js"></script>
<?php  
switch ($rechargeprorole){
	case "1":
    $rowcount = $engine->db_query("SELECT rechargeproid FROM rechargepro_account WHERE profile_agent = ? ", array($profile_creator), true);
	break;

	case "2":
   $rowcount = $engine->db_query("SELECT rechargeproid FROM rechargepro_account WHERE profile_agent = ?", array($profile_creator), true);
	break;
    
    case "3":
   $rowcount = $engine->db_query("SELECT rechargeproid FROM rechargepro_account WHERE profile_creator = ?", array($profile_creator), true);
	break;

	default :
    $rowcount = 0;
};


if($rowcount > 0){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "theme/classic/pages/agentb.php",
    type: "POST",
    data : {page:page},
    success: function(data, textStatus, jqXHR)
    {
        $("#page-content").html(data);
    }
});
        }
    });
    })
</script>

<div id="page-content"></div>
<div style="clear: both;"></div>
<ul style="margin-left: 10px;" id="pagination" class="pagination-sm"></ul>

<?php }	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;' >Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>































<div style="font-size: 180%;">Agent Commission</div>
<div style="overflow-x:auto; max-height: 300px; margin-top: 20px; font-size: 80%;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>#</th>
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
$row = $engine->db_query("SELECT id,services_key,cordinator_percentage,percentage,bill_formular,dateadeded,bill_rechargeprofull_percentage FROM rechargepro_services_agent WHERE rechargeproid = ?",array($profile_creator));
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
$row = $engine->db_query("SELECT id,services_key,fixedfee,dateadeded FROM rechargepro_services_fixed WHERE rechargeproid = ?",array($profile_creator));
$sub = array();
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $id = $row[$dbc]['id']; 
    $services_key = $row[$dbc]['services_key']; 
    $fixedfee = $row[$dbc]['fixedfee'];  
    $dateadeded = $row[$dbc]['dateadeded'];
    
  $mychargearray[$services_key] = array($id,$services_key,$fixedfee,$dateadeded);  
    
    }   

$row = $engine->db_query("SELECT vertisservice_charge,bill_rechargeprofull_percentage,cordinator_percentage,id,services_key,service_name,percentage,bill_formular,services_category,status,dateadeded FROM rechargepro_services WHERE services_category != '7' ORDER BY id DESC LIMIT 500",array());
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
    $bill_rechargeprofull_percentage = $myservicearray[$services_key][6];
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


     $st = '<div class="redmenu radious3 shadow" style="margin:3px; padding:1px 4px; overflow:hidden; display:inline-block; ">Not Active</div>'; 
     
          
    if($status == 1){
     $st = '<div class="greenmenu radious3 shadow" style="margin:3px; padding:1px 4px; display:inline-block; overflow:hidden;">Active</div>'; 
     $del = '<span title="Disable Account" onclick="setactive(\''.$id.'\',\'0\')" class="fa fa-power-off" style="cursor:pointer; color:#2DE910; font-size:150%"></span>';  
    }
    
?>
<tr >
<td><img src="../theme/classic/icons/<?php echo $id;?>.jpg" style="padding: 2px; border: solid 1px #EEEEEE;" width="25"/></td>
<td><?php echo $service_name;?></td>
<th><?php if($rechargeprorole == "1"){ echo ($cordinator_percentage+$percentage).$what;}else{echo $percentage.$what;}?></th>
<td><?php echo $vertisservice_charge;?></td>
<td><?php echo $services_category;?></td>

<td><?php echo $st?></td>

</tr>
<?php
	}?>


</tbody>
</table>

</div>




</div></div>





