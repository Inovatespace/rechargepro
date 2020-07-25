<?php
include "../../../engine.autoloader.php";
$adminid = $engine->get_session("adminid");



$permission =	$engine->admin_permission("rechargepro_watch","index");
if($permission < 3){ exit;}


$id = $_REQUEST['id'];
$row = $engine->db_query2("SELECT profile_creator,rechargeproid, name, email, rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
$name = $row[0]['name'];
$email = $row[0]['email'];
$rechargeprorole = $row[0]['rechargeprorole'];
$profile_creator = $row[0]['profile_creator'];
if(!isset($_SESSION['adminme'])){exit;};







if(isset($_REQUEST['what'])){
    
  $value = $_REQUEST['what'];

$details = "Admin_Credit_".$rechargeprorole."_".$value."_".$id;
$engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($adminid,"UPGRAGE",$details)); 


$engine->db_query2("UPDATE rechargepro_account SET rechargeprorole = ? WHERE rechargeproid = ? LIMIT 1",array($value,$id));

switch ($value){ 
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
  
  $message = "Hey $name,<br />
Your Account have been Upgraded to $role account.<br />
Thank you,<br />
rechargepro";
echo $engine->send_mail(array('noreply@rechargepro.com.ng','rechargepro!'),$email,"Account have been Upgraded",$message);


}


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
?>

<script type="text/javascript">
function saveme(){
var what = $("#what").val();


 
 $("#prevd").prepend('<img src="images/loading6.gif"  />'); 

   $.ajax({
    url : "plugin/rechargepro_watch/pages/setrole.php",
    type: "POST",
    data : {id:"<?php echo $id;?>",what:what},
    success: function(data, textStatus, jqXHR)
    {
       window.location.reload();
    }
    });
    
}

  jQuery(document).ready(function($){
    $('#what').val("<?php echo $rechargeprorole;?>");
  });


</script>
<div class="barmenu" style="padding: 10px; margin:-15px -5px 0px -5px;">Add Fund</div>
<div class="profilebg" style="padding: 10px;">
<div style="margin-bottom: 5px; text-align: left;"><?php echo $name;?> {<?php echo $role;?>}</div>
<div style="margin-bottom: 5px;">
<select class="input" id="what" style="width: 100%;">
    <option value="1">Coordinator</option>
	<option value="2">Agent</option>
    <option value="3">Cashier</option>
    <option value="4">User</option>
</select>
</div>
<div id="prevd"><input type="button" onclick="saveme()" class="activemenu shadow" style="cursor:pointer; border: none; width:100%; padding:5px 0px;" value="Upgrade Account" /></div>
</div>