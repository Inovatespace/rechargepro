<div style="" class="title_api4">Get list of Banks</div>
 <style>
         pre {
            overflow-x: auto;
            white-space: pre-wrap;
            white-space: -moz-pre-wrap;
            white-space: -pre-wrap;
            white-space: -o-pre-wrap;
            word-wrap: break-word;
         }
         .pst{color:white; font-size:19px;}
         
         .codemenu{float: left; padding:2px 5px; border-right: 1px #CCCCCC solid; cursor: pointer;}
      </style>

<script type="text/javascript">
function show(Id){
    $(".thecode").hide();
    $("#"+Id).show();
}
</script>


<?php
$array = array("private_key"=>"1234QWER5678TYUI","token"=>"1234:QWER:5678:TYUI");
$link = 'https://quickpay.com.ng/api/public/transaction/bank_list.json';
?>

<div style="padding: 10px; font-size:85%;">
<div class="pst">POST</div>

<div style="overflow: hidden; margin-bottom:-11px;">
<div class="profilebg codemenu" onclick="show('php')" >PHP</div>
<div class="profilebg codemenu" onclick="show('java')" >JAVA</div>
<div class="profilebg codemenu" onclick="show('node')" >NODEJS</div>

</div>
<pre id="php" class="thecode">
<code>
$payload = <?php echo json_encode($array)?>;
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "<?php echo $link;?>",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $payload,
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "Content-Type:application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
</code>
</pre>

<pre id="java" class="thecode" style="display: none;"><code>
OkHttpClient client = new OkHttpClient();

MediaType mediaType = MediaType.parse("application/json");
RequestBody body = RequestBody.create(mediaType, "<?php echo str_replace('"',"'",json_encode($array));?>");
Request request = new Request.Builder()
  .url("<?php echo $link;?>")
  .post(body)
  .addHeader("content-type", "application/json")
  .addHeader("cache-control", "no-cache")
  .build();

Response response = client.newCall(request).execute();


</code></pre>



<pre id="node" class="thecode" style="display: none;">
<code>
var request = require("request");

var options = { method: 'POST',
  url: '<?php echo $link;?>',
  headers: 
   { 'cache-control': 'no-cache',
     'content-type': 'application/json' },
  formData: <?php echo str_replace('"',"'",json_encode($array));?> };

request(options, function (error, response, body) {
  if (error) throw new Error(error);

  console.log(body);
});
</code>
</pre>




<div class="pst">RESPONSE</div>
<div style="color: white;">
<div class="radious10" style="background-color: green; width:10px; height:10px; display: inline-block; vertical-align: middle;">&nbsp;</div> 200
</div>
<pre>
<code class="plaintext">
{
    "100001": "FET",
    "100002": "Paga",
    "100003": "Parkway-ReadyCash",
    "100004": "Paycom",
    "100005": "Cellulant",
    "100006": "eTranzact",
    "100007": "StanbicMobileMoney",
    "100008": "Ecobank Xpress Account",
    "100009": "GTMobile",
    "100010": "TeasyMobile",
    "100011": "Mkudi",
    "100012": "VTNetworks",
    "100013": "AccessMobile",
    "100014": "FBNMobile",
    "100015": "Kegow",
    "100016": "FortisMobile",
    "100017": "Hedonmark",
    "100018": "ZenithMobile",
    "100019": "Fidelity Mobile",
    "100020": "MoneyBox",
    "100021": "Eartholeum",
    "100022": "Sterling Mobile",
    "100023": "TagPay",
    "100024": "Imperial Homes Mortgage Bank",
    "100025": "Zinternet Nigera Limited",
    "100026": "One Finance",
    "100027": "Intellifin",
    "100028": "AG Mortgage Bank",
    "100029": "Innovectives Kesh",
    "100030": "EcoMobile",
    "100031": "FCMB Mobile",
    "100032": "Contec Global Infotech Limited (NowNow)",
    "110001": "PayAttitude Online",
    "110002": "Flutterwave Technology Solutions Limited",
    "400001": "FSDH Merchant Bank",
    "999999": "NIP Virtual Bank",
    "090133": " AL-Barakah Microfinance Bank",
    "090116": "AMML MFB",
    "090001": "ASOSavings & Loans",
    "070010": "Abbey Mortgage Bank",
    "000014": "Access Bank",
    "000005": "Access Bank (Diamond)",
    "090134": "Accion Microfinance Bank",
    "090160": "Addosser Microfinance Bank",
    "090131": "Allworkers Microfinance Bank",
    "090143": "Apeks Microfinance Bank",
    "090127": "BC Kash Microfinance Bank",
    "090117": "Boctrust Microfinance Bank",
    "090148": "Bowen Microfinance Bank",
    "070015": "Brent Mortgage Bank",
    "090144": "CIT Microfinance Bank",
    "090141": "Chikum Microfinance Bank",
    "000009": "Citi Bank",
    "090130": "Consumer Microfinance Bank",
    "060001": "Coronation Merchant Bank",
    "070006": "Covenant MFB",
    "090159": "Credit Afrique Microfinance Bank",
    "000010": "Ecobank Bank",
    "090097": "Ekondo MFB",
    "090114": "Empire trust MFB",
    "000019": "Enterprise Bank",
    "060002": "FBN Merchant Bank",
    "090107": "FBN Mortgages Limited",
    "090153": "FFS Microfinance Bank",
    "000007": "Fidelity Bank",
    "090126": "Fidfund Microfinance Bank",
    "090111": "FinaTrust Microfinance Bank",
    "000016": "First Bank of Nigeria",
    "000003": "First City Monument Bank",
    "070014": "First Generation Mortgage Bank",
    "070002": "Fortis Microfinance Bank",
    "090145": "Fullrange Microfinance Bank",
    "090158": "Futo Microfinance Bank",
    "000013": "GTBank Plc",
    "070009": "Gateway Mortgage Bank",
    "090122": "Gowans Microfinance Bank",
    "090147": "Hackman Microfinance Bank",
    "090121": "Hasal Microfinance Bank",
    "000020": "Heritage",
    "090118": "IBILE Microfinance Bank",
    "090149": "IRL Microfinance Bank",
    "090157": "Infinity Microfinance Bank",
    "070016": "Infinity Trust Mortgage Bank",
    "000006": "JAIZ Bank",
    "090003": "JubileeLife Microfinance  Bank",
    "000002": "Keystone Bank",
    "070012": "Lagos Building Investment Company",
    "090136": "Microcred Microfinance Bank",
    "090129": "Money Trust Microfinance Bank",
    "090151": "Mutual Trust Microfinance Bank",
    "070001": "NPF MicroFinance Bank",
    "090128": "Ndiorah Microfinance Bank",
    "090108": "New Prudential Bank",
    "090119": "Ohafia Microfinance Bank",
    "090161": "Okpoga Microfinance Bank",
    "070007": "Omoluabi savings and loans",
    "070008": "Page Microfinance Bank",
    "090004": "Parralex Microfinance bank",
    "090137": "PecanTrust Microfinance Bank",
    "090135": "Personal Trust Microfinance Bank",
    "070013": "Platinum Mortgage Bank",
    "000023": "Providus Bank ",
    "000024": "Rand Merchant Bank",
    "070011": "Refuge Mortgage Bank",
    "090125": "Regent Microfinance Bank",
    "090132": "Richway Microfinance Bank",
    "090138": "Royal Exchange Microfinance Bank",
    "090006": "SafeTrust ",
    "090140": "Sagamu Microfinance Bank",
    "090112": "Seed Capital Microfinance Bank",
    "000008": "Skye Bank",
    "000012": "StanbicIBTC Bank",
    "000021": "StandardChartered",
    "000001": "Sterling Bank",
    "000022": "Suntrust Bank",
    "090115": "TCF MFB",
    "090146": "Trident Microfinance Bank",
    "090005": "Trustbond Mortgage Bank",
    "000018": "Union Bank",
    "000004": "United Bank for Africa",
    "000011": "Unity Bank",
    "090110": "VFD MFB",
    "090123": "Verite Microfinance Bank",
    "090150": "Virtue Microfinance Bank",
    "090139": "Visa Microfinance Bank",
    "000017": "Wema Bank",
    "090120": "Wetland Microfinance Bank",
    "090124": "Xslnce Microfinance Bank",
    "090142": "Yes Microfinance Bank",
    "000015": "Zenith Bank Plc",
    "090156": "e-Barcs Microfinance Bank"
}
</code>
</pre>



</div>


