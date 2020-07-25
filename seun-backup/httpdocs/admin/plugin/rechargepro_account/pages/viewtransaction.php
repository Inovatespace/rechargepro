<?php
include "../../../engine.autoloader.php";
$id = $_REQUEST['id'];
$row = $engine->db_query2("SELECT rechargeproid, name, email, active, created_date, mobile, rechargeprorole, ac_ballance,rechargeproid FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));

if(!isset($_SESSION['adminme'])){exit;};
$permission =	$engine->admin_permission("rechargepro_account","index");

?>
    <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>




<div class="barmenu" style="padding: 10px; margin:-15px -5px 0px -5px;">Transaction History</div>
<div class="profilebg" style="padding: 10px;">


<div style="overflow: hidden; text-align: left;">

<div style="margin-bottom: 5px; text-align: left; float:left;"><?php echo $row[0]['name'];?> {<?php echo $row[0]['ac_ballance'];?>}</div>

<div style="float: right;">
<?php if($permission >= 3 || in_array($engine->get_session("adminid"),array(22,1,2))){ ?>
<div style="text-align: right;">
<span class="fa fa-plus tunnel" name="plugin/rechargepro_account/pages/upload.php?width=500&id=<?php echo $id;?>" style="cursor:pointer; font-size: 150%; color:#1D8E32;"></span>
</div><?php
	}
?>
<?php

        if(file_exists("../../../../avater/".$id.".jpg")){
            
            $sweet = "avater/".$id.".jpg";
        
            
	   echo '<div style="float: left; padding:5px;">
        <div>Photo</div>
        <img class="profilebg" style="padding: 3px; border: solid 1px #CCCCCC;" src="../'.$sweet.'" height="50" />
        </div>';
        }
        
        
	function servi($id){
	   
       $array = array("a"=>"ID Card","b"=>"Utility Bill","c"=>"Certificate","d"=>"Agent onboarding");
       
       
       foreach($array  AS $a =>$b ){
       
       $arrayinner = array("pdf","jpg","jpeg","png");
       foreach($arrayinner AS $v){
        
        $sweet = $a."_".$id.".".$v;
        
        if(file_exists("../../../../uploade/".$sweet)){
            
            $sweet = "uploade/".$a."_".$id.".".$v;
            if($v == "pdf"){
               $sweet = "theme/classic/images/pdf.png"; 
            }
            
	   echo '<div style="float: left; padding:5px;">
        <div>'.$b.'</div>
        <img class="profilebg" style="padding: 3px; border: solid 1px #CCCCCC;" src="../'.$sweet.'" height="50" />
        </div>';
        }
        }
        
        }


	}
    
    
    servi($id);
?>




</div>
</div>






<div style="text-align: left; margin-bottom: 20px;">
<div class="barmenu" style="overflow: hidden; padding:4px 0px;">
<div style="float: left; width:19%; margin-right:1%;">Reference Number</div>
<div style="float: left; width:19%; margin-right:1%;">Transaction Type</div>
<div style="float: left; width:19%; margin-right:1%;">Transaction Status</div>
<div style="float: left; width:19%; margin-right:1%;">Transaction Date</div>
<div style="float: left; width:19%;">Transaction Amount</div>
</div>
<div style="max-height: 250px; overflow: auto;">
<?php
$row = $engine->db_query2("SELECT transactionid,transaction_reference,rechargepro_service,rechargepro_status,amount,transaction_date,account_meter FROM rechargepro_transaction_log WHERE rechargeproid = ? ORDER BY transactionid DESC LIMIT 250", array($id));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $id = $row[$dbc]['transactionid']; 
    $t_type = $row[$dbc]['rechargepro_service']; 
    $t_status = $row[$dbc]['rechargepro_status']; 
    $t_amount = $row[$dbc]['amount']; 
    $t_date = $row[$dbc]['transaction_date'];
    $ref = $row[$dbc]['transaction_reference'];
    	   $bg = '';
       if($dbc % 2 == 0){
        $bg = "background-color:#F5F5F5;";
       }
  ?>
  <div class="" style="overflow: hidden; padding:4px 0px;<?php echo $bg;?>">
<div style="float: left; width:19%; margin-right:1%;">&nbsp;<?php echo $ref;?></div>
<div style="float: left; width:19%; margin-right:1%;">&nbsp;<?php echo $t_type;?></div>
<div style="float: left; width:19%; margin-right:1%;">&nbsp;<?php echo $t_status;?></div>
<div style="float: left; width:19%; margin-right:1%;">&nbsp;<?php echo $t_date;?></div>
<div style="float: left; width:19%;">&nbsp;<?php echo $t_amount;?></div>
</div>
  <?php  
    }
?>
</div>
</div>


</div>