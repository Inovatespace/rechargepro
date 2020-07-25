<?php 
include "../../../engine.autoloader.php";
?>
 
<style type="text/css">
.srow {
  flex-wrap: wrap;
  padding:0px;
}

.scolumn {
  flex: 13%;
  max-width: 13%;
  border: solid 1px #EEEEEE; 
  padding:0.5%;
  margin: 1%;
}

@media (max-width: 1100px) {
  .scolumn {
    flex: 16%;
  max-width: 16%;
  }
}

@media (max-width: 800px) {
  .scolumn {
    flex: 21%;
  max-width: 21%;
  }
}

@media (max-width: 500px) {
  .scolumn {
    flex: 28%;
    max-width: 28%;
  }
}


@media (max-width: 400px) {
  .scolumn {
    flex: 46%;
    max-width: 46%;
  }
}
</style>
<div class="srow"> 
<?php
function catkey($category){

switch ($category){ 
	case "2":
    case "3": return "airtime";
	break;

	case "7": return "bills";
	break;
    
    
	case "8": return "sendmoney";
	break;

	case "5": return "tv";
	break;
    
	case "1": return "utility";
	break;
}

return "";
}

$s = "";
if(isset($_REQUEST['s'])){
$s = $_REQUEST['s'];}

$row = $engine->db_query("SELECT id,services_key,service_name,services_category FROM rechargepro_services WHERE (services_key LIKE ? OR service_name LIKE ?)  AND status = '1' ORDER BY id LIMIT 12",array("%$s%","%$s%")); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $id = $row[$dbc]['id'];
    $services_key = $row[$dbc]['services_key'];
    $service_name = $row[$dbc]['service_name'];
    $services_category = $row[$dbc]['services_category'];
    
    
?>
<div class="scolumn profilebg menu" style="float:left; cursor: pointer;">
<a class="" href="/home&cat=<?php echo $services_category;?>&key=<?php echo $services_key;?>#<?php echo catkey($services_category);?>">
<img title="<?php echo $service_name;?>" style="width: 100%; height:100%;" src="<?php echo $engine->icon_picture($id);?>" />
<div style="font-size: 10px; font-weight:bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width:98%;"><?php echo $service_name;?></div>
</a>
</div>
<?php
}	if(!isset($services_key)){
    ?>
    <div style='padding:5%; margin:5%; overflow:hidden; color:white;' class=''> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>
    <?php
}
?>
</div>