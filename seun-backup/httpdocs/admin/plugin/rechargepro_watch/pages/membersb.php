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
                url: "plugin/rechargepro_watch/pages/pro/login.php",
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


  function call_log(Id){
$("#tholder").html('<img src="images/loading.gif" width="124" height="124" />');
            
             $.ajax({
                type: "POST",
                url: "plugin/rechargepro_watch/pages/transactionlog.php",
                data: "id="+Id,
                cache: false,
                success: function (html) {
                   $("#tholder").html(html);
                }
            });     
      

}
</script>



<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>Name</th>
<th>Role</th>
<th>Agent</th>
<th>Email</th>
<th>Mobile</th>
<th>Active</th>
<th>Regdate</th>
<th>ac ballance</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php
$permission = 0;

function  myname($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
}



function usercount($officer,$rechargeproid,$engine){
    if($officer > 1){
$row = $engine->db_query2("SELECT COUNT(rechargeproid) AS ct FROM rechargepro_account WHERE rechargepro_cordinator = ? OR profile_agent = ? LIMIT 1",array($rechargeproid,$rechargeproid)); 

$engine->db_query2("UPDATE rechargepro_account SET officer = ? WHERE rechargepro_cordinator = ? OR profile_agent = ? LIMIT 1",array($officer,$rechargeproid,$rechargeproid)); 

    return $row[0]['ct'];  
    
  }    
}

function  officer($id,$engine){
    if($id == 0){return "-";}
$row = $engine->db_query("SELECT name FROM admin WHERE adminid = ? LIMIT 1",array($id)); 
    return $row[0]['name'];
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
$row = $engine->db_query2("SELECT officer,profile_agent,rechargeproid, name, email, active, created_date, mobile, rechargeprorole,ac_ballance,rechargeproid FROM rechargepro_account WHERE (name LIKE ? OR email LIKE ? OR name = ?) $ac AND officer = ? LIMIT 50",array("%$q%","%$q%",$q,$engine->get_session("adminid"))); 
	}else{
$row = $engine->db_query2("SELECT officer,profile_agent,rechargeproid, name, email, active, created_date, mobile, rechargeprorole,ac_ballance,rechargeproid FROM rechargepro_account WHERE officer = ? ORDER BY rechargeprorole ASC LIMIT $start, $per_page",array($engine->get_session("adminid")));
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
    $officer = $row[$dbc]['officer'];
    
    

    
    
    $incharge = officer($officer,$engine);
    
    $profile_agent = myname($row[$dbc]['profile_agent'],$engine);
    
    
        

$engine->db_query2("UPDATE rechargepro_account SET officer = ? WHERE rechargepro_cordinator = ? OR profile_agent = ?",array($engine->get_session("adminid"),$rechargeproid,$rechargeproid)); 



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
    $role = "Coordinator";
	break;

	case "2":
    $role = "Agent";
	break;

	case "3":
    $role = "Cashier";
	break;

	case "4":
    $role = "User";
	break;
    
	case "5":
    $role = "User";
	break;

      
	default : $role = "User";
}


$myimg = "../theme/classic/images/small_default.png";
if(file_exists("../../../../avater/".$rechargeproid.".jpg")){$myimg = "../avater/$rechargeproid.jpg";}
?>
<tr >
<td>
<div style="overflow: hidden;">
<div style="float: left; width:50px; margin-right: 2%; overflow: hidden;">
<img src="<?php echo $myimg;?>" width="100%" />
</div>

<div style="float: left; width:150px">
<div>[<?php echo usercount($officer,$rechargeproid,$engine);?>]  <a onclick="call_log('<?php echo $rechargeproid;?>')" style="cursor: pointer;"><?php echo ucfirst(strtolower($name));?> <span class="fa fa-eye"></span></a></div>
</div>

</div>


</td>




<td><?php echo $role;?> <?php if($permission >= 3){ ?><span class="fas fa-edit tunnel" name="plugin/rechargepro_watch/pages/setrole.php?width=300&id=<?php echo $rechargeproid;?>" style="cursor: pointer;"></span><?php  };?></td>
<td><?php echo $profile_agent;?></td>

<td><?php echo $email;?></td>
<td><?php echo $mobile;?></td>
<td><label for="all<?php echo $rechargeproid;?>"><span></span></label> <?php echo $status;?></td>
<td><?php echo $regdate;?></td>

<td><span class="fa fa-eye tunnel" name="plugin/rechargepro_watch/pages/viewtransaction.php?width=800&id=<?php echo $rechargeproid;?>" title="View Log" style="cursor:pointer; color:#605959;"></span> &nbsp; <?php echo $ac_ballance;?> &nbsp; <?php if($permission >= 3){ ?><span  title="Add Fund" style="color: #25A242; cursor: pointer;" class="fa fa-plus tunnel" name="plugin/rechargepro_watch/pages/addfund.php?width=300&id=<?php echo $rechargeproid;?>"></span><?php  };?></td>
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







