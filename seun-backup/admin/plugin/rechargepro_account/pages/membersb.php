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
                url: "plugin/rechargepro_account/pages/pro/login.php",
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
<th>Name</th>
<th>loan</th>
<th>Role</th>
<th>Agent</th>
<th>Email</th>
<th>Mobile</th>
<th>S-Charge</th>
<th>A-Topup</th>
<th>Topup Amount</th>
<th>Trans</th>
<th>Regdate</th>
<th>ac ballance</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php
$permission =	$engine->admin_permission("rechargepro_account","index");

function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}

function  officer($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query("SELECT name FROM admin WHERE adminid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}

function usercount($officer,$rechargeproid,$engine){
    if($officer > 1){
$row = $engine->db_query2("SELECT COUNT(rechargeproid) AS ct FROM rechargepro_account WHERE rechargepro_cordinator = ? OR profile_agent = ? LIMIT 1",array($rechargeproid,$rechargeproid)); 

$engine->db_query2("UPDATE rechargepro_account SET officer = ? WHERE rechargepro_cordinator = ? OR profile_agent = ?",array($officer,$rechargeproid,$rechargeproid)); 

    return $row[0]['ct'];  
    
  }    
}

$per_page = 30;

$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

$color=1;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$active = $_REQUEST['active'];
$ac = "";
if($active == 1){
    $ac = "AND active = '1'";
}

		
$row = $engine->db_query2("SELECT merge_ac,is_service_charge,auto_feed_cahier_account,feed_cahier_account_amount,profit_bal,rechargepro_cordinator,loan_ballance,officer,profile_agent,rechargeproid, name, email, active, created_date, mobile, rechargeprorole,ac_ballance,rechargeproid,sms_activation,transfer_activation FROM rechargepro_account WHERE (mobile LIKE ? OR name LIKE ? OR  email LIKE ? OR name = ?) $ac  LIMIT 50",array("%$q%","%$q%","%$q%",$q)); 
	}else{
$row = $engine->db_query2("SELECT merge_ac,is_service_charge,auto_feed_cahier_account,feed_cahier_account_amount,profit_bal,rechargepro_cordinator,loan_ballance,officer,profile_agent,rechargeproid, name, email, active, created_date, mobile, rechargeprorole,ac_ballance,rechargeproid,sms_activation,transfer_activation FROM rechargepro_account ORDER BY rechargeproid DESC LIMIT $start, $per_page",array());
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $name = $row[$dbc]['name'];
    $rechargeproid = $row[$dbc]['rechargeproid'];
    $email = $row[$dbc]['email'];
    $mobile = $row[$dbc]['mobile'];
    $regdate = $row[$dbc]['created_date'];
    $active = $row[$dbc]['active'];
    $rechargeprorole = $row[$dbc]['rechargeprorole'];    
    $rechargeprorole = $row[$dbc]['rechargeprorole'];    
    $ac_ballance = $row[$dbc]['ac_ballance']; 
    $profit =    $row[$dbc]['profit_bal']; 
    $officer = $row[$dbc]['officer'];
    $sms_activation = $row[$dbc]['sms_activation'];
    $transfer_activation = $row[$dbc]['transfer_activation'];
    $loan_ballance = $row[$dbc]['loan_ballance'];
    $rechargepro_cordinator = $row[$dbc]['rechargepro_cordinator'];
    $is_service_charge = $row[$dbc]['is_service_charge'];
    $auto_feed_cahier_account = $row[$dbc]['auto_feed_cahier_account'];
    $feed_cahier_account_amount = $row[$dbc]['feed_cahier_account_amount'];
    $merge_ac = $row[$dbc]['merge_ac'];
    
    $incharge = officer($officer,$engine);
    $profile_agent = $row[$dbc]['profile_agent'];
    $profile_agent_name = myname($profile_agent,$engine);



     $status = '<div class="redmenu radious3 shadow" style="margin:5px; padding:2px 4px; overflow:hidden; display:inline-block; ">Not Active</div>'; 
     $del = '<span onclick="setactive(\''.$rechargeproid.'\',\'1\')" title="Enable Account" class="fa fa-power-off" style="cursor:pointer; color:#B72C2C; font-size:150%"></span>';
          
    if($active == 1){
     $status = '<div class="greenmenu radious3 shadow" style="margin:5px; padding:2px 4px; display:inline-block; overflow:hidden;">Active</div>'; 
     $del = '<span title="Disable Account" onclick="setactive(\''.$rechargeproid.'\',\'0\')" class="fa fa-power-off" style="cursor:pointer; color:#2DE910; font-size:150%"></span>';  
    }
    
    $check = "";
    if($active == 1){
       $check = 'checked="checked"';  
        }
        
    $stats = "";
    if($dbc % 2 == 0){ $stats = "stats2";}
    
    $sex = ' <span class="fas fa-male" style="color: #5B9BD1 !important;"></span>';
   
    
    switch ($rechargeprorole){ 
	case "0":
    case "1":
    $role = "Coo";
	break;

	case "2":
    $role = "Age";
	break;

	case "3":
    $role = "Cas";
	break;

	case "4":
    $role = "Use";
	break;
    
	case "5":
    $role = "Use";
	break;

      
	default : $role = "Use";
}



$transstatus = '-';
$scharge = "-";
$autofeed = "-";
if(in_array($engine->get_session("adminid"),array(1,2,9,10))){
$transstatus = '<input onclick="set_transfer(\'1\',\''.$rechargeproid.'\')" id="acb'.$rechargeproid.'" type="checkbox" value="0" /> <label for="acb'.$rechargeproid.'"><span></span></label>';
$scharge ='<input onclick="set_scharge(\'1\',\''.$rechargeproid.'\')" id="acc'.$rechargeproid.'" type="checkbox" value="0" /> <label for="acc'.$rechargeproid.'"><span></span></label>';
$autofeed = '<input onclick="set_autofeed(\'1\',\''.$rechargeproid.'\')" id="acd'.$rechargeproid.'" type="checkbox" value="0" /> <label for="acd'.$rechargeproid.'"><span></span></label>';

if($transfer_activation == 1){$transstatus = '<input onclick="set_transfer(\'0\',\''.$rechargeproid.'\')"  id="acb'.$rechargeproid.'" checked="checked" type="checkbox" value="1" /> <label for="acb'.$rechargeproid.'"><span></span></label>';}

if($is_service_charge == 1){$scharge = '<input onclick="set_scharge(\'0\',\''.$rechargeproid.'\')"  id="acc'.$rechargeproid.'" checked="checked" type="checkbox" value="1" /> <label for="acc'.$rechargeproid.'"><span></span></label>';}

if($auto_feed_cahier_account == 1){$autofeed = '<input onclick="set_autofeed(\'0\',\''.$rechargeproid.'\')"  id="acd'.$rechargeproid.'" checked="checked" type="checkbox" value="1" /> <label for="acd'.$rechargeproid.'"><span></span></label>';}
}


$smsstatus = '-';
if(in_array($engine->get_session("adminid"),array(1,2,9,10))){
$smsstatus = '<input onclick="set_sms(\'1\',\''.$rechargeproid.'\')" type="checkbox" value="0"  id="acc'.$rechargeproid.'" /> <label for="acc'.$rechargeproid.'"><span></span></label>';
if(!empty($sms_activation)){$smsstatus = '<input onclick="set_sms(\'0\',\''.$rechargeproid.'\')" checked="checked"  id="acc'.$rechargeproid.'" type="checkbox" value="1" /> <label for="acc'.$rechargeproid.'"><span></span></label>';}
}



$vmerge_acstatus = '<input onclick="set_merge(\'1\',\''.$rechargeproid.'\')" type="checkbox" value="0"  id="merge'.$rechargeproid.'" /> <label for="merge'.$rechargeproid.'"><span></span></label>';
if($merge_ac > 0){$vmerge_acstatus = '<input onclick="set_merge(\'0\',\''.$rechargeproid.'\')" checked="checked"  id="merge'.$rechargeproid.'" type="checkbox" value="1" /> <label for="merge'.$rechargeproid.'"><span></span></label>';}




$myimg = "../theme/classic/images/small_default.png";
if(file_exists("../../../../avater/".$rechargeproid.".jpg")){$myimg = "../avater/$rechargeproid.jpg";}
?>
<tr >
<td>
<div style="overflow: hidden;">
<div style="overflow: hidden; display: none;">
<img src="<?php echo $myimg;?>" width="50px" />
</div>


[<?php echo usercount($officer,$rechargeproid,$engine);?>][<?php echo $incharge;?>]<br /> <?php echo ucfirst(strtolower(substr($name,0,20)));?>
<span style="font-weight: bold;"><?php if($permission >= 3){ ?> <span class="fas fa-edit tunnel" name="plugin/rechargepro_account/pages/setofficer.php?width=300&id=<?php echo $rechargeproid;?>" style="cursor: pointer;"></span><?php  };?></span>
<?php if($rechargepro_cordinator == 0 && $profile_agent == 0){ ?>
       &nbsp; <a href="rechargepro_account&p=servicecharge&id=<?php echo $rechargeproid;?>&name=<?php echo ucfirst(strtolower($name));?>"><span class="fas fa-play"></span></a>
       <?php
	}
?>

</div>


</td>

<td><span class="fa fa-eye tunnel" name="plugin/rechargepro_account/pages/viewloantransaction.php?width=800&id=<?php echo $rechargeproid;?>" title="View Log" style="cursor:pointer; color:#605959;"></span> <?php echo $loan_ballance;?> <?php if(in_array($engine->get_session("adminid"),array(1,2,3,9,10))){ ?><span  title="Add loan" style="color: #25A242; cursor: pointer;" class="fa fa-plus tunnel" name="plugin/rechargepro_account/pages/addloan.php?width=300&id=<?php echo $rechargeproid;?>"></span><?php  };?></td>


<td><?php echo $role;?> <?php if(in_array($engine->get_session("adminid"),array(1,2,3,4,9,10))){ ?><span class="fas fa-edit tunnel" name="plugin/rechargepro_account/pages/setrole.php?width=300&id=<?php echo $rechargeproid;?>" style="cursor: pointer;"></span><?php  };?></td>
<td><?php echo substr($profile_agent_name,0,10);?></td>

<td><?php echo substr($email,0,20);?></td>
<td><?php echo $mobile;?></td>


<td><?php echo $scharge;?></td>
<td><?php if($rechargepro_cordinator == 0 && $profile_agent == 0){ echo $autofeed;}else{echo "-";}?></td>
<td><?php if($rechargepro_cordinator == 0 && $profile_agent == 0){ ?><input type="text" style="width:60px;" id="autofeedamount<?php echo $rechargeproid;?>" value="<?php echo $feed_cahier_account_amount;?>"/><input style="width: 50px;" type="button" onclick="set_autofeed_amount('<?php echo $rechargeproid;?>')" value="SAVE" /><?php }else{echo "-";}?></td>
<td><?php echo $transstatus;?></td>

<td><?php echo substr($regdate,0,10);?></td>
<td><span style="font-weight: bold;"><strong style="color: black;"><?php echo $ac_ballance;?></strong><br /><span style="color: #8C2222;"><?php echo $profit;?></span></span> &nbsp; <?php echo $vmerge_acstatus;?> <span class="fa fa-eye tunnel" name="plugin/rechargepro_account/pages/viewtransaction.php?width=800&id=<?php echo $rechargeproid;?>" title="View Log" style="cursor:pointer; color:#605959;"></span> &nbsp;  <?php  if(in_array($engine->get_session("adminid"),array(1,2,3,4,9,10))){ ?><span  title="Add Fund" style="color: #25A242; cursor: pointer;" class="fa fa-plus tunnel" name="plugin/rechargepro_account/pages/addfund.php?width=300&id=<?php echo $rechargeproid;?>"></span><?php  };?></td>



<td><?php if($permission >= 3){ ?><span class="fa fa-eye" title="View Account" onclick="loguser('<?php echo $rechargeproid;?>')" style="cursor:pointer; color:#605959; font-size:150%"></span> | <?php echo $del;?><?php  };?></td>
</tr>
<?php
	}
 	if(!isset($rechargeproid)){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>






</tbody>
</table>

</div>







