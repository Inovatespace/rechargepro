<?php
include "../../../../engine.autoloader.php";
$rechargeproid = $engine->get_session("rechargeproid");
?>
<script type="text/javascript">
function view_e(Id){
    $("#e"+Id).prepend("<span class='fas fa-spinner fa-spin'></span>");
    
    $.ajax({
    url : "/theme/classic/pages/myinvite/earning.php",
    type: "POST",
    data : {id:Id},
    success: function(data, textStatus, jqXHR)
    {
        $("#e"+Id).html(data);
    }
});
}
</script>

<?php
$per_page = 12; 
$page = 0;
if (isset($_REQUEST['page'])) {$page = htmlentities($_REQUEST['page']);}
$start = ($page-1)*$per_page;

if (isset($_REQUEST['q'])){
$q = $_REQUEST['q'];  
$row = $engine->db_query("SELECT rechargeproid, name, rechargeprorole FROM rechargepro_account WHERE profile_creator = ? AND (name LIKE ? OR email LIKE ? or mobile LIKE ?)  LIMIT 50",array($rechargeproid,"%$q%","%$q%","%$q%")); 
	}else{
$row = $engine->db_query("SELECT rechargeproid, name, rechargeprorole FROM rechargepro_account WHERE profile_creator = ?  LIMIT $start, $per_page", array($rechargeproid));
}
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    
    $name = $row[$dbc]['name'];
    $rechargeproid = $row[$dbc]['rechargeproid']; 
    $rechargeprorole = $row[$dbc]['rechargeprorole']; 
    
    
    $float = "left";
  
  if($dbc%2==0){$float = "left";}
  
  
  $level = "<span style='color:#249324;'>Level 1</span>";
  if($rechargeprorole < 4){
    $level = "<span style='color:#BE3939;'>Level 2</span>";
  }
?>
<div class="radious5" style="border: solid 1px #EEEEEE; background-color:white; padding: 10px 2%; margin:1%; width:18%; float:<?php echo $float;?>">
<div style="font-weight: bold; margin-bottom:5px;"><?php echo $name;?></div>
<div style="overflow:hidden;">
<img src="<?php echo $engine->picture($rechargeproid);?>" class="radious50" style="float: left; width:50px; height:50px; border:solid 0.3px #EEEEEE; padding:0.5;" />
<div style="float: right; width:71%;">
<div style="overflow: hidden; font-weight:bold;"><?php echo $level;?></div>
<div style="clear: both;"></div>
<div style="font-weight: bold; font-size:85%; margin-top:5px; cursor: pointer;" id="e<?php echo $rechargeproid;?>" onclick="view_e('<?php echo $rechargeproid;?>')">View Earning</div>
</div>

</div>
</div>


<?php
	}
?>