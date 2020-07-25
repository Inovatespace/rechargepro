<?php
include "../../../engine.autoloader.php";
?>
   <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>

<div style="overflow-x:auto;">

<?php
$q = $_REQUEST['q'];
$state = $_REQUEST['state'];

$row = $engine->db_query("SELECT name,mobile,companyaddress,companystate,companylga FROM rechargepro_account WHERE companyaddress LIKE ? AND companystate = ?",array("%$q%",$state)); 

for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $name = $row[$dbc]['name']; 
    $mobile = $row[$dbc]['mobile']; 
    $companyaddress = $row[$dbc]['companyaddress']; 
    $companystate = $row[$dbc]['companystate']; 
    $companylga = $row[$dbc]['companylga']; 

?>
<div style="border-bottom: solid 1px #944F4F; padding:5px 0px">
<div><?php echo $name;?> - <?php echo $companystate;?> &raquo; <?php echo $companylga;?></div>
<div><?php echo $companyaddress;?></div>
<div><?php echo $mobile;?></div>
</div>
<?php
	}
 	if(!isset($name)){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>


</div>





