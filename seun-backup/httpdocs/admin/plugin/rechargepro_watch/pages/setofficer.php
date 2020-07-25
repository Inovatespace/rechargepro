<?php
include "../../../engine.autoloader.php";
$adminid = $engine->get_session("adminid");



$permission =	$engine->admin_permission("rechargepro_watch","index");
if($permission < 3){ exit;}


$id = $_REQUEST['id'];
$row = $engine->db_query2("SELECT profile_creator,rechargeproid, name, email, officer FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
$name = $row[0]['name'];
$email = $row[0]['email'];
$officer = $row[0]['officer'];
$profile_creator = $row[0]['profile_creator'];
if(!isset($_SESSION['adminme'])){exit;};







if(isset($_REQUEST['what'])){
    
  $value = $_REQUEST['what'];

$details = "Officer_".$officer."_".$value."_".$id;
$engine->db_query2("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",array($adminid,"OFFICER",$details)); 

$engine->db_query2("UPDATE rechargepro_account SET officer = ? WHERE rechargeproid = ? LIMIT 1",array($value,$id));

}
?>

<script type="text/javascript">
function saveme(){
var what = $("#what").val();


 
 $("#prevd").prepend('<img src="images/loading6.gif"  />'); 

   $.ajax({
    url : "plugin/rechargepro_watch/pages/setofficer.php",
    type: "POST",
    data : {id:"<?php echo $id;?>",what:what},
    success: function(data, textStatus, jqXHR)
    {
       window.location.reload();
    }
    });
    
}

  jQuery(document).ready(function($){
    $('#what').val("<?php echo $officer;?>");
  });


</script>
<div class="barmenu" style="padding: 10px; margin:-15px -5px 0px -5px;">Set Account Officer</div>
<div class="profilebg" style="padding: 10px;">
<div style="margin-bottom: 5px; text-align: left;"><?php echo $name;?> {<?php echo $officer;?>}</div>
<div style="margin-bottom: 5px;">
<select class="input" id="what" style="width: 100%;">
<?php
$row = $engine->db_query("SELECT name,adminid FROM admin WHERE adminid != '1'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$adminname = $row[$dbc]['name'];
$adminid = $row[$dbc]['adminid'];
?>
    <option value="<?php echo $adminid;?>"><?php echo $adminname;?></option>
<?php
	}
?>
</select>
</div>
<div id="prevd"><input type="button" onclick="saveme()" class="activemenu shadow" style="cursor:pointer; border: none; width:100%; padding:5px 0px;" value="Assign Officer" /></div>
</div>