<?php
include "../../../engine.autoloader.php";
$email = $engine->get_session("rechargeproemail");
?>
<style type="text/css">
ol.row {
	list-style:none;
    margin:0px;
    padding:0px;
}
ol.row li {
	overflow: hidden;
    padding:0px;
}
ol.row li:hover {
	background-color:#F7F7F7;
    
}
ol.row li:first-child {
}
</style>
<ol class="row" id="updates">
<?php
$per_page = 14;
$lastdate = date("Y-m-d", strtotime("-2 day", strtotime(date("Y-m-d"))));
$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;
if (isset($_REQUEST['q'])) {
$q = $_REQUEST['q'];
$status = "";//$_REQUEST['status'];
$ac = "";
switch ($status){ 
	case "Status":
    case "All": $ac = "";
	break;

	case "New":$ac = " AND dt > $lastdate";
	break;

	case "Closed":$ac = " AND locked = '1'";
	break;
}


$row = $engine->db_query2("SELECT * FROM contact_tickets WHERE (trackid LIKE ? OR subject = ?)  ORDER BY locked, admin_status,lastupdate ASC LIMIT 50",array("%$q%","%$q%")); 
	}else{

$department = $engine->get_session("department");


switch ($department){ 
	case "admin": $row = $engine->db_query2("SELECT * FROM contact_tickets WHERE admin_status = '0' ORDER BY locked, admin_status,lastupdate  DESC LIMIT $start, $per_page", array());
	break;

	case "finance":$row = $engine->db_query2("SELECT * FROM contact_tickets WHERE admin_status = '0' AND category IN (1,3)  ORDER BY locked, admin_status,lastupdate  DESC LIMIT $start, $per_page", array());
	break;

	case "support": $row = $engine->db_query2("SELECT * FROM contact_tickets  WHERE admin_status = '0' AND category IN (2,3)  ORDER BY locked, admin_status,lastupdate  DESC LIMIT $start, $per_page", array());
	break;

	default :$row = array();
}

}


$trackid = "";
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $trackid = $row[$dbc]['trackid'];
    $subject = htmlentities($row[$dbc]['subject']);
    $locked = $row[$dbc]['locked'];
    $status = $row[$dbc]['admin_status'];
    $id = $row[$dbc]['id'];
    $email = htmlentities($row[$dbc]['email']);
    $lastupdate = $row[$dbc]['lastupdate'];
    $is_attachment = $row[$dbc]['is_attachment'];
    $name = htmlentities($row[$dbc]['name']);
   // $lock = $row[$dbc]['lock'];
    
    
    
    $ip = $row[$dbc]['ip'];
    $loc = '';
    if($locked == "1"){
        $loc = '<span class="fas fa-lock">&nbsp;</span>';
        
    }
    
    if($status == "0"){
    $subject = '<img src="plugin/rechargepro_support/pages/images/unread.png" style=" vertical-align: middle;" width="10" /> '.$subject;
    }
    $td = 'me';
    if($dbc % 2 == 0){ $td = 'you';}
 ?>
 <li class="<?php echo $td;?>">
 
 <div style="<?php if($status == "0"){echo "font-weight:bold;";}?> background:url(../theme/classic/images/body_dot.png) repeat; background-color:#FBFBF3; padding:5px 0.5%; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 
  <div style="float: left; width:20%;  margin-right:1%; cursor: pointer;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"> <input  value="<?php echo $id;?>" type="checkbox" id="ab<?php echo $id;?>" /><label for="ab<?php echo $id;?>"><span></span><a  onclick="showticket('<?php echo $id;?>')"><?php if($is_attachment == 1){ echo '<a class="fas fa-paperclip"></a> ';}; echo $trackid;?></a></label> &nbsp;</div>
  
  <div style="float: left; width:20%; cursor: pointer;"  onclick="showticket('<?php echo $id;?>')"><?php echo $loc." ".$name;?>&nbsp;</div>
  
 <div style="float: left; width:49%; cursor: pointer;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" onclick="showticket('<?php echo $id;?>')">&nbsp;<?php echo $subject;?></div>
 
 <div style="float: right; 20%;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">&nbsp;<?php echo $lastupdate;?></div>
 </div> 
   
   </li> 
<div style="height: 10px;">&nbsp;</div>
<?php   
    }
    
    
    if (empty($trackid)) {
echo '<div class="nWarning" style="text-align:center; background-color:#F7F0C3; border: solid green 1px;">No support ticket found</div>';	
}
?>

    </ol>
