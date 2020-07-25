<div style="" class="title_api4">ACCOUNT BALANCE/STATUS</div>
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
$link = 'https://rechargepro.com.ng/api/public/transaction/status.json';
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
    "status": 200,
    "message": {
        "balance": "549.26",
        "profit": "166.88"
    }
}
</code>
</pre>




</div>


