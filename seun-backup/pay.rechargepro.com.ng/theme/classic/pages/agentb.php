<?php
include "../../../engine.autoloader.php";
$quickpayrole = $engine->get_session("quickpayrole"); 
$profile_creator = $engine->get_session("quickpayid");


if(isset($_REQUEST['merge'])){
  $id = $_REQUEST['id'];
  
$row = $engine->db_query("SELECT ac_ballance,profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($id));
$ac_ballance = $row[0]["ac_ballance"];
$profit_bal = $row[0]['profit_bal'];  
    
    $newbal = $ac_ballance+$profit_bal;
    $engine->db_query("UPDATE quickpay_account SET ac_ballance=?, profit_bal='0'  WHERE quickpayid = ? LIMIT 1",array($newbal,$id));
    exit;
}


?>
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function($){
$('.tunnel').tunnel();
});
</script>
<script type="text/javascript">
$(document).ready(function(){$("#myTable").tablesorter();});
</script>


<script type="text/javascript">
function sync_account(Id){
  
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Profit will be moved to main account',
    buttons: {
        confirm: function () {
            
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/agentb.php",
		data: "id="+Id+"&merge=merge",
		cache: false,
		success: function(html){
		  //alert(html);
		  window.location.reload();
		}
	   });
    
    },
        cancel: function () {
            
        }
    }
}); 
  
       
}


function delete_access(Id){
    
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to Delete this agent?',
    buttons: {
        confirm: function () {
            
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/profile/pro/deleteagent.php",
		data: "id="+Id,
		cache: false,
		success: function(html){
		  //alert(html);
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


<table id="myTable" class="tablesorter" style="font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;; font-size:85%;">
<thead>
<tr style="text-transform: uppercase;">
<th>Image</th>
<th>Name</th>
<th>Delete</th>
<th>Email</th>
<th>Mobile</th>
<th>Wallet</th>
<th>Profit</th>
<th>Last Topup</th>
<th >T Log</th>
<th >#</th>
</tr>
</thead>
<tbody>
<?php

$per_page = 30; 
$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])){
$q = $_REQUEST['q'];  
$row = $engine->db_query("SELECT profit_bal,quickpayid,name,username,password,public_key,public_secret,active,email,created_date,mobile,ac_ballance,last_payout,quickpayrole,  profile_creator, profile_agent FROM quickpay_account WHERE (name LIKE ? OR email = ?) AND profile_creator = ?  LIMIT 50",array("%$q%","%$q%",$profile_creator)); 
	}else{


switch ($quickpayrole){
	case "1":
    $row = $engine->db_query("SELECT profit_bal,quickpayid,name,username,password,public_key,public_secret,active,email,created_date,mobile,ac_ballance,last_payout,quickpayrole,profile_creator,profile_agent FROM quickpay_account WHERE profile_creator = ? LIMIT $start, $per_page",array($profile_creator));
	break;

	case "2":
   $row = $engine->db_query("SELECT profit_bal,quickpayid,name,username,password,public_key,public_secret,active,email,created_date,mobile,ac_ballance,last_payout,quickpayrole,profile_creator,profile_agent FROM quickpay_account WHERE profile_agent = ? LIMIT $start, $per_page",array($profile_creator));
	break;

	default :
    $row = array();
    };

}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    
    //$profile_percentage = $row[$dbc]['profile_percentage'];
    $quickpayid = $row[$dbc]['quickpayid']; 
    $name = $row[$dbc]['name']; 
    $username = $row[$dbc]['username']; 
    $public_key = $row[$dbc]['public_key']; 
    $public_secret = $row[$dbc]['public_secret']; //
    $email = $row[$dbc]['email']; 
    $created_date = $row[$dbc]['created_date'];
    $mobile = $row[$dbc]['mobile'];
    $ac_ballance = $row[$dbc]['ac_ballance'];
    $last_payout = $row[$dbc]['last_payout'];
    $ac_quickpayrole = $row[$dbc]['quickpayrole'];
    $main_profile_creator = $row[$dbc]['profile_creator'];
    $profit_bal = $row[$dbc]['profit_bal']; //is account live or test
    $active = $row[$dbc]['active']; //active account
    //$profile_process_transaction = $row[$dbc]['profile_process_transaction']; //who bear t cost
    $profile_agent = $row[$dbc]['profile_agent'];
    

    
    $check1 = "";$check2 = "";$check3 = "";
   // if($profile_live_account == 1){
       $check1 = 'checked="checked"';  
       // }
        
       if($active == 1){
       $check2 = 'checked="checked"';  
        }
        
            //if($profile_process_transaction == 1){
       $check3 = 'checked="checked"';  
       // }     
        
        
    $stats = "";
    if($dbc % 2 == 0){ $stats = "stats2";}
    
 




$lt = ' <span style="cursor: pointer;" class="fa fa-edit tunnel" name="theme/classic/pages/profile/pro/controlacb.php?id='.$quickpayid.'&width=350"></span>';
if($ac_quickpayrole < 4 && $profile_creator != $quickpayid){
    $lt = ' <span style="cursor: pointer;" class="fa fa-edit tunnel" name="theme/classic/pages/profile/pro/controlac.php?id='.$quickpayid.'&width=350"></span>';
}

if($profile_creator == $quickpayid && $profile_creator == 1){
   $lt = ' <span style="cursor: pointer;" class="fa fa-edit tunnel" name="theme/classic/pages/profile/pro/controlac.php?id='.$quickpayid.'&width=350"></span>';
}



$slf = '';
if($profile_creator != $quickpayid){
    $slf = ' <span style="cursor: pointer;" class="fa fa-edit tunnel" name="theme/classic/pages/profile/pro/editprofile.php?id='.$quickpayid.'&width=350"></span>';
}


    $profile_percentage = "-";


$log = '<a href="/history&ac='. $quickpayid.'"><span style="cursor: pointer;" class="fa fa-eye"></span></a>';
if($ac_quickpayrole > 3){
  $slf = '';
  $lt = '';
  $log = '-';
}




$del = '<span class="fas fa-trash-alt" style="cursor: pointer;" onclick="delete_access('.$quickpayid.')"></span>';
if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
  $slf = '';
  $lt = '';
  $log = '-';
  $del = "-";
    }
}
    
?>

<tr style="font-size: 130%;">
<td><img src="<?php echo $engine->picture($quickpayid);?>" style="height:45px; vertical-align: middle;" /></td>
<td><?php echo ucfirst(strtolower($name)); echo $slf;?> </td>
<td><?php echo $del;?></td>
<td><?php echo $email;?></td>
<td><?php echo $mobile;?></td>
<td><?php echo $ac_ballance;?></td>
<td><?php echo $profit_bal;?></td>
<td><?php echo $last_payout.$lt;?> </td>
<td><?php echo $log;?></td>
<td><?php if($profit_bal > 0){?><span onclick="sync_account('<?php echo $quickpayid;?>')" style="cursor:pointer;" class="fas fa-sync"></span><?php }?></td>
</tr>
<?php
	}
 	if(!isset($name)){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'>Agent Not Found</div>";}?>






</tbody>
</table>


