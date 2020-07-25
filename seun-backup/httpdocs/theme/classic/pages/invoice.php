<?php 
if(!isset($_SESSION)){
include "../../../engine.autoloader.php";    
}
$engine = new engine();
//if(!$engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};

if(isset($_REQUEST['repeat'])){
    

$insertid = $engine->db_query("INSERT INTO rechargepro_transaction_log (cordinator_id,agent_id,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,service_charge,phone,email,payment_method) SELECT cordinator_id,agent_id,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,service_charge,phone,email,payment_method FROM rechargepro_transaction_log WHERE transactionid = ?",array($_REQUEST['repeat']));

$engine->put_session("cartid",$insertid);

$row = $engine->db_query("SELECT account_meter,name,address FROM rechargepro_transaction_log WHERE transactionid = ?",array($_REQUEST['repeat']));
$name = $row[0]['name'];
$account_meter = $row[0]['account_meter'];
$address = $row[0]['address'];

if(empty($name)){
    $name = $account_meter;
}

if(!empty($address)){
    $account_meter = $address;
}

    echo "&detail1=$name&detail2=$account_meter";
    exit;
}
?>

<style type="text/css">
.btt{}
</style>

<?php
if(!isset($_REQUEST['id']) && !isset($_REQUEST['tid'])){
   echo "<meta http-equiv='refresh' content='0;url=home'>";	exit; 
    }
    
    
if(isset($_REQUEST['id'])){
    
$id = htmlentities(trim($_REQUEST['id']));
$id = str_replace(" ", "_",$id);
$explode = explode("_",$id);
//$explode = preg_split( "/ (_| ) /",$id);


if(count($explode) > 1){
    $id = $explode[1];
}


$row = $engine->db_query("SELECT transactionid,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,phone,email,payment_method,transaction_status,transaction_code,transaction_reference,rechargepro_status,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ?",array($id));
	
if(empty($row[0]['transactionid'])){
echo "<meta http-equiv='refresh' content='0;url=home'>";	exit;
}


$transactionid = $row[0]['transactionid'];
$rechargepro_service = $row[0]['rechargepro_service'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$account_meter = $row[0]['account_meter'];
$business_district = $row[0]['business_district'];
$thirdPartycode = $row[0]['thirdPartycode'];
$address = $row[0]['address'];
$name = $row[0]['name'];
$phcn_unique = $row[0]['phcn_unique'];
$amount = $row[0]['amount'];
$phone = $row[0]['phone'];
$email = $row[0]['email'];
$payment_method = $row[0]['payment_method'];
$transaction_status = $row[0]['transaction_status'];
$transaction_code = $row[0]['transaction_code'];
$transaction_reference = $row[0]['transaction_reference'];
$rechargepro_status = $row[0]['rechargepro_status'];
$rechargepro_status_code = $row[0]['rechargepro_status_code'];
$rechargepro_print = $row[0]['rechargepro_print'];
$transaction_date = $row[0]['transaction_date'];


?>

<script type="text/javascript">
function repeat(){
    
    $(".fa-redo").addClass("fa-spin");
    
        $.ajax({
                type: "POST",
                url: "/theme/classic/pages/invoice.php",
                data: "repeat=<?php echo $id;?>",
                cache: false,
                success: function (html) {
                    window.location.href = "/confirmation"+html;
                }


            });//$id
}
</script>

<div style="line-height: 25px;">




<div class="sitewidth" style="overflow:hidden; margin-right: auto; margin-left: auto; margin-bottom:30px;"id="printDivContent">

<span style="margin-top:40px; float:left; margin-right:20px; cursor:pointer; color:#119D26; font-size:200%" onclick="repeat()">
<span class="fa fa-redo" title="Repeat"></span> Repeat</span>


<span class="fa fa-print" title="Print"  onclick="javascript:Print('printDivContent');" style="margin-top:40px; float:left; cursor:pointer; color:#0F73C9; font-size:200%"> Print</span>

<?php
if($rechargepro_status_code == "1"){
?>
<?php
$row = $engine->db_query("SELECT id FROM rechargepro_services WHERE services_key = ?",array($rechargepro_subservice));
?>

<?php
$img = $row[0]['id'];
if(file_exists("theme/classic/icons/".$img.".jpg")){	
?>
<img style="cursor: pointer; width:100px; float:right; margin-top:20px;" src="/theme/classic/icons/<?php echo $row[0]['id'];?>.jpg" />
<?php
if ($img == 2 || $img == 12) {
	?>
    <img style="cursor: pointer; width:70px;  float:right; margin-top:30px;" src="/theme/classic/images/kallak.png" />
    <?php
}
	}
?>
<div style="clear: both;"></div>

<div class="nextcolor" style="border-bottom:1px #CCCCCC solid; border-top:1px #CCCCCC solid; font-size:25px; padding: 20px 0px; font-weight:bold;"><?php echo $rechargepro_service;?> RECEIPT</div>


<?php
	}else{
	   ?>
       <div style="clear: both;"></div>
       <div class="nWarning"><?php echo $transaction_status;?><br />Please Contact the Administrator</div>
       <?php
	}
?>

<div style="clear: both;"></div>


<?php
 $array = $engine->myarray();
            
            
            
            
            
            
        $response = json_decode($rechargepro_print, true);
        
        
        
        
        $response = self::array_flatten($response);
        
   
        //$response = array_change_key_case($response , CASE_LOWER);
        if(in_array($rechargepro_subservice,array("2351","2352","2353","2354"))){
          $response["Recharged Account"] = $account_meter;  
        }
        
        if($rechargepro_subservice == "AED"){
        $response["Account Type"] = "Prepaid";
        }
                if($rechargepro_subservice == "AEP"){
        $response["Account Type"] = "Postpaid";
        }

        //$response["Transaction Status"] = "Successful";
        $response["Transaction Date"] = $transaction_date;
        
        if(isset($response['details'])){
        foreach($response['details'] AS $key => $value){
            $response[$key] = $value;
            unset($response['details']);
            }
            }

        
        
      


        $arrayreturn = array();
        foreach ($response as $key => $value) {
        if(!is_array($value)){
        $arrayreturn[$key] = $value;
        }else{
         foreach ($value as $keya => $valuea) {
            $arrayreturn[$keya] = $valuea;
            }
        }
        }


        $response = $arrayreturn;
  

  foreach($array AS $a){
          if(array_key_exists($a,$response)){
            unset($response[$a]);
          }  
        }
        
        
        

function return_value($value){
    if(!is_array($value)){
    return ucwords($value);
    }else{
     foreach($value AS $valuea => $valueb){
        return ", $valuea : $valueb";
        }
    }
    
    }

function return_name($key,$value){
    
if(!is_array($key)){
    
$key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);
if(!empty($value)){
    if(trim($key) == "Token"){$key = '<span style="font-size:150%">'.ucwords($key).'</span>'; $value = '<span style="font-size:150%">'.$value.'</span>';}
  return '<div  class="btt" style="font-family: Trebuchet MS, Verdana, Arial, Helvetica, sans-serif; padding:10px 2px 10px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5; overflow: hidden; margin-bottom:3px;">
    <div style="float: left; width:30%;">'.ucwords($key).'  </div>
    <div style="float:left; width:68%">'.return_value($value).'</div>
    </div>';
    }
    
    

    
}else{
    
foreach($keya AS $keyb => $valueb){
    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
    return return_name($keyb,$valueb);
    } 
}

}

               
        if(isset($response['Address'])){
          $engine->move_to_top($response, 'Address'); 
        }
        
        if(isset($response['Name'])){
           $engine->move_to_top($response, 'Name'); 
        }
        
        if(isset($response['MeterNumber'])){
           $engine->move_to_top($response, 'MeterNumber'); 
        }
        
        if(isset($response['Token'])){
           $engine->move_to_top($response, 'Token'); 
        }
        
        
        

if(count($response) > 0){
foreach($response AS $key => $value){
    
    if(!is_array($key)){
        
$key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);

echo return_name($key,$value);

}else{
foreach($key AS $keya => $valuea){
    
if(!is_array($keya)){
$keya = preg_replace('/([A-Z])(?<!^)/', ' $1', $keya);
echo return_name($keya,$valuea);
}else{
    
foreach($keya AS $keyb => $valueb){
    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
    echo return_name($keyb,$valueb);
    }
    
}



    }
}

}
}





?>




</div>
</div>

<?php
	}
    
    if(isset($_REQUEST['tid'])){
        ?>
        
        <div style="margin:10px 30px; padding:10px;" class="nInformation">
        <div><strong>Transaction Reference:</strong><?php echo htmlentities($_REQUEST['tid']);?></div>
        <div><strong>Transaction ID</strong><?php echo htmlentities($_REQUEST['rechargepro']);?></div>
        <div><?php echo htmlentities($_REQUEST['er']);?></div>
        </div>
        <?php
    }
?>


<script type="text/javascript">
function isChrome() {
  var isChromium = window.chrome,
    winNav = window.navigator,
    vendorName = winNav.vendor,
    isOpera = winNav.userAgent.indexOf("OPR") > -1,
    isIEedge = winNav.userAgent.indexOf("Edge") > -1,
    isIOSChrome = winNav.userAgent.match("CriOS");

  if (isIOSChrome) {
    return true;
  } else if (
    isChromium !== null &&
    typeof isChromium !== "undefined" &&
    vendorName === "Google Inc." &&
    isOpera === false &&
    isIEedge === false
  ) {
    return true;
  } else { 
    return false;
  }
}

function Print(elementId)
{
data = $("#"+elementId).html();
    var mywindow = window.open();
    var is_chrome = Boolean(mywindow.chrome);
    mywindow.document.write(data);

   if (is_chrome) {
     setTimeout(function() { // wait until all resources loaded 
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10
        mywindow.print(); // change window to winPrint
        mywindow.close(); // change window to winPrint
     }, 250);
   } else {
        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();
   }

    return true;
}

jQuery(document).ready(function($){
//$(function () {
//setTimeout(Print('printDivContent'),10);
})

</script>


