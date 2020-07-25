<style type="text/css">
.title_api{font-weight: bold; font-size:20px; color:blue; text-transform: uppercase;}
.title_api2{font-weight: bold; font-size:15px;}
.title_api4{font-weight: bold; font-size:15px; color:orange; margin-top:10px;}
.title_api3{font-weight: bold; font-size:15px; color: green;}
.get{margin-left:20px; }
.alink{color:#265C9E; text-decoration: underline;}
.samplecode{font-weight: bold; font-size:11px; cursor: pointer; color:#C41414;}
</style>
<div style="" class="title_api">TEST CREDENTIAL</div>
<div style="overflow: hidden;"><div style="font-weight: bold;">API KEY</div><div>1234QWER5678TYUI</div></div>
<div style="overflow: hidden; margin-bottom: 40px;">
<div style="font-weight: bold;">API TOKEN</div><div>1234:QWER:5678:TYUI</div>
</div>

<?php
	$demo = "https://quickpay.com.ng/api/public/transaction/";
?>
<div style="overflow: hidden;"><div style="font-weight:bold;">TEST URL</div><div style="">https://quickpay.com.ng/api/public/transaction/{services}/.json</div></div>
<div style="overflow: hidden; margin-bottom:20px;"><div style="font-weight:bold;">LIVE URL</div><div style="">https://quickpay.com.ng/api/public/transaction/{services}/.json</div></div>

<div style="font-weight: bold;">Available Extension</div>
<div style="  margin-bottom: 50px;"><strong style="color: violet;">json</strong>, <strong style="color: green;">xml</strong>, <strong style="color: blue;">txt</strong>, <strong style="color: firebrick;">printr</strong></div>


<div style="" class="title_api">STATUS CODE</div>
<table style="width: 100%; margin-bottom:30px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">CODE</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">100</td>
	<td style="border: solid 1px #CCCCCC;">Transaction Failed</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">200</td>
	<td style="border: solid 1px #CCCCCC;">Transaction Successful</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">300</td>
	<td style="border: solid 1px #CCCCCC;">Transaction Pending</td>
</tr>
</table>

<script type="text/javascript">
function sample_code(Id){
$('#sample').html("Loading");
var data = {i:Id};
    $.post("/theme/classic/pages/developer/sample.php", data).done(
    function(response){
        

         //window.history.pushState("QuickPay", "QuickPay", "/index#"+Id);
        $('#sample').html(response);
        
         var iScrollHeight = $(window).scrollTop();
         
         $('#sample').css("padding-top",iScrollHeight);
         $('#sample').css("min-height",(3850-iScrollHeight));
         $('#sample').css("height",(3850-iScrollHeight));

     
     }
   ).error(
    function(jqXHR, textStatus, errorThrown) {
          $.alert("Please Check your network");
     }
 );
 

}
</script>


<div style="" class="title_api" id="airtime">AIRTIME</div>
<div style="" class="title_api2">Get list of Airtime Network</div>
<div style="margin-bottom:10px;">{services} = airtime_list <span onclick="sample_code('airtime')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>airtime_list.json</div>





<div style="" class="title_api" id="data">DATA</div>
<div style="" class="title_api2">Get list of Data NetWork</div>
<div style="margin-bottom:10px;">{services} = data_list  <span onclick="sample_code('data1')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:5px;"><?php echo $demo;?>data_list.json</div>
<div style="" class="title_api2">Get Data bundle</div>
<div style="margin-bottom:10px;">{services} = data_bundle_list  <span onclick="sample_code('data2')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>data_bundle_list.json</div>









<div style="" class="title_api" id="bills">BILLS</div>
<div style="" class="title_api2">Get Bills Categories</div>
<div style="margin-bottom:10px;">{services} = bills_cat_list  <span  onclick="sample_code('bills1')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:10px;"><?php echo $demo;?>bills_cat_list.json</div>


<div style="" class="title_api2">Get list of Bills</div>
<div style="margin-bottom:10px;">{services} = bills_list  <span  onclick="sample_code('bills2')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">cat</td>
	<td style="border: solid 1px #CCCCCC;">Category to show, from bills_cat_list</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>bills_list.json</div>







<div style="" class="title_api" id="tv">CABLE/TV</div>
<div style="" class="title_api2">Get list of Cable/TV</div>
<div style="margin-bottom:10px;">{services} = tv_list  <span  onclick="sample_code('tv1')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:5px;"><?php echo $demo;?>tv_list.json</div>
<div style="" class="title_api2">Get Network Banquet</div>
<div class="margin-bottom:10px;">{services} = tv_banquet_list  <span  onclick="sample_code('tv2')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">service</td>
	<td style="border: solid 1px #CCCCCC;">Code gotten from tv_list</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>

<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>tv_banquet_list.json</div>










<div style="" class="title_api" id="electricity">ELECTRICITY</div>
<div style="" class="title_api2">Get list of Electricity</div>
<div style="margin-bottom:10px;">{services} = electricity_list  <span  onclick="sample_code('electricity')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>electricity_list.json</div>








<div style="" class="title_api" id="bank">BANK TRANSFER</div>
<div style="" class="title_api2">Get list of Banks</div>
<div style="margin-bottom:10px;">{services} = bank_list  <span  onclick="sample_code('banktransfer')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>bank_list.json</div>








<div style="" class="title_api" id="vac">Verify Account</div>
<div style="margin-bottom:10px;">{services} = initiate_transaction <span  onclick="sample_code('initiate')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">service</td>
	<td style="border: solid 1px #CCCCCC;">service Code</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">mobile</td>
	<td style="border: solid 1px #CCCCCC;">Customer Mobile Number{We dont send sms}</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">amount</td>
	<td style="border: solid 1px #CCCCCC;">Amount Required</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">accountnumber</td>
	<td style="border: solid 1px #CCCCCC;">Meter Number/Smart Card Number/Phone Number/Account Number</td>
</tr>

<tr>
	<td style="border: solid 1px #CCCCCC; font-weight:bold; color:purple;">bankcode {Fund Transfer only}</td>
	<td style="border: solid 1px #CCCCCC;">Gotten from Get list of Banks</td>
</tr>

<tr>
	<td style="border: solid 1px #CCCCCC; font-weight:bold; color:purple;">bundle {Data only}</td>
	<td style="border: solid 1px #CCCCCC;">Gotten from data_bundle_list</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>initiate_transaction.json</div>




<div style="" class="title_api" id="mp">Make Payment</div>
<div style="margin-bottom:10px;">{services} = complete_transaction <span  onclick="sample_code('complete')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">tid</td>
	<td style="border: solid 1px #CCCCCC;">Gotten from account verification above</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>complete_transaction.json</div>












<div style="" class="title_api" id="vp">Verify Payment</div>
<div style="margin-bottom:10px;">{services} = verify_transaction <span  onclick="sample_code('verify')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">tid</td>
	<td style="border: solid 1px #CCCCCC;">Gotten from account verification above</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>verify_transaction.json</div>












<div style="" class="title_api" id="ab">Account Balance/Status</div>
<div style="margin-bottom:10px;">{services} = status <span   onclick="sample_code('status')" class="samplecode">[Get sample code]</span></div>
<table style="width: 100%; margin-bottom:5px;">
<tr>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">PARAMETER</td>
	<td style="border: solid 1px #CCCCCC; font-weight: bold;">DESCRIPTION</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">private_key</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
<tr>
	<td style="border: solid 1px #CCCCCC;">token</td>
	<td style="border: solid 1px #CCCCCC;">To be provided by quickpay</td>
</tr>
</table>
<div class="alink" style="margin-bottom:20px;"><?php echo $demo;?>status.json</div>
