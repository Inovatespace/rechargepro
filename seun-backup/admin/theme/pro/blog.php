<?php
$engine = new engine();
if(isset($_REQUEST['today'])){
$today = $_REQUEST['today'];
}else{
$today = date("Y-m-d");    
}

function rangeMonth($datestr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d 23:23:59', strtotime('last day of this month', $dt));
    return $res;
}
 
$range = rangeMonth($today);
$start = $range['start'];
$end = $range['end'];
  
 

///////////////////////////////mobifinnnnnnnnnnnnnnnnn

$GLOBALS['engine'] = $engine;


function fund_transfer(){

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.ravepay.co/v2/gpx/balance",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n\"currency\": \"NGN\",\n\"seckey\": \"FLWSECK-efba7abe0decca4441c236caf91d9c76-X\"\n}",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  return $err;
} else {
    $response = json_decode($response,true);
  return $response["data"]['AvailableBalance'];
}
}

function mobifin_auth(){
    
$data_string = '{
 "username" : "info@vertistechnologies.com",
 "password": "Vertis@Tech888"
}';

$access = mobifin_post("auth",$data_string,true); 

 if(isset($access['token'])){
$date = date("Y-m-d H:i:s", strtotime("+0 day", strtotime($access['expires'])));
$GLOBALS['engine']->db_query2("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
array(
$access['token'],
$date,
"mobifin"));
return array("status" => "200", "message" => $access['token']);
}else{
 return array("status" => "100", "message" => "Network Error");   
}
                    
}




function mobifin_post($path, $data_string, $post = true){
    $auth = "1";
    
if($path != "auth"){
        $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = $GLOBALS['engine']->db_query2("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "mobifin"));
        if (!empty($rmk[0]['setting_value'])) {

        $auth =  $rmk[0]['setting_value'];

        }else{
            $access = mobifin_auth();
            if($access['status'] == "100"){
                return $access;
            }else{
                $auth =  $access['message'];
            }
        }
}


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://clients.primeairtime.com/api/'.$path);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "accept: application/json, application/*+json",                                                                      
    "accept-encoding: gzip,deflate",
    "Authorization: Bearer $auth",
    "cache-control: no-cache",
    "connection: Keep-Alive",
    "content-type: application/json",
));

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
if($post){
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
}
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 

    
$result = curl_exec($ch);
$response = json_decode($result, true);  

return $response;
};



        $mobifinballance = "";
        $nowdate = date("Y-m-d H:i:s");
        $rmk = $GLOBALS['engine']->db_query2("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "mobifin_bal"));
        if (!empty($rmk[0]['setting_value'])) {

        $mobifinballance =  $rmk[0]['setting_value'];

        }else{
            $response = mobifin_post("status","",false);
//print_r($response);
            //$response = json_decode($response, true);
            if(empty($response)){
             $mobifinballance = 0;
            }else{
               $mobifinballance = $response['balance'];
               $date = date("Y-m-d H:i:s", strtotime("+30 minutes", strtotime(date("Y-m-d H:i:s"))));
$GLOBALS['engine']->db_query2("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
array(
$mobifinballance,
$date,
"mobifin_bal"));
            }
        }
//////////////////////////////mobifinnnnnnnnnnnnnnnnnnn



function pau_bal(){

       
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://mcapi-server.herokuapp.com/account/details",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_USERPWD=> "vertis:nVfQeKTn4c",
  CURLOPT_HTTPAUTH=> CURLAUTH_BASIC,  
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
 // CURLOPT_CUSTOMREQUEST => "POST",
 // CURLOPT_POSTFIELDS => '',
  CURLOPT_HTTPHEADER => array(
                    "Connection: Keep-Alive",
                "Keep-Alive: 300",
    "cache-control: no-cache",
    "content-type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);


$response = json_decode($response, true);

//print_r($response);
return $response[0]['balance'];
        }





function capricon_bal(){

        $requestBody = '{}';


        $httpMethod = "GET";
        $restPath = "/rest/consumer/finance/status";
        $date = gmdate('D, d M Y H:i:s T');


        $hashedRequestBody = base64_encode(hash('sha256', utf8_encode($requestBody), true));

        $signedData = $httpMethod . "\n" . $hashedRequestBody . "\n" . $date . "\n" . $restPath;

        $signature = hash_hmac('sha1', $signedData, base64_decode('zcFyHpA06K2dch6439QmkOHVlmsc074dbrEtOKJVLQGOEgLw1EQnGVgCvYS3b+j1z96gv48gQZSed4AQ4Xjk1g=='), true);

        $encodedsignature = base64_encode($signature);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://baxi.capricorndigi.com/app" . $restPath,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_POSTFIELDS => $requestBody,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "authorization: MSP vertis:" . $encodedsignature,
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        return $response = json_decode($result, true);
        }




function eko_bal()
    {
$url = "https://eko.phcnpins.com/API/vproxy.asmx?op=FetchDealerBalance";  
$post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchDealerBalance xmlns="http://IKEDC_API/vproxy/">
      <dealer_code>EK0134</dealer_code>
      <hashstring>'.hash('sha512',"EK0134EK0134").'</hashstring>
      <api_key>46374a1d-2b9d-4ede-a7f3-731367d345cf</api_key>
    </FetchDealerBalance>
  </soap:Body>
</soap:Envelope>';


		$header = array(
		"Content-type:text/xml;charset=\"utf-8\"",
		"Accept:application/xml",
		"Cache-Control:no-cache",
		"Pragma:no-cache",
	//	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
		"Content-length:".strlen($post_string)
        );
        
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,$url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		// curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
		 $result = curl_exec($soap_do);
		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
		 $err = curl_error($soap_do);
         
   
return $result;

                        
    }

function ikeja_bal()
    {
$url = "https://www.iepins.com.ng/API/vproxy.asmx?op=FetchDealerBalance";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchDealerBalance  xmlns="http://IKEDC_API/vproxy/">
      <dealer_code>IE5273</dealer_code>
      <hashstring>'.md5("IE5273IE5273").'</hashstring>
      <api_key>E146A2C4460B6511AEA043565D605C6A</api_key>
    </FetchDealerBalance>
  </soap:Body>
</soap:Envelope>';


		$header = array(
		"Content-type:text/xml;charset=\"utf-8\"",
		"Accept:application/xml",
		"Cache-Control:no-cache",
		"Pragma:no-cache",
	//	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
		"Content-length:".strlen($post_string)
        );
        
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,$url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		// curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
		 $result = curl_exec($soap_do);
		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
		 $err = curl_error($soap_do);

if($code != "200"){
    return "-".$code;
}

return $result;

                        
    }
    
    function ibadan_bal()
    {//https://www.iepins.com.ng/API/vproxy.asmx?op=FetchDealerBalance
$url = "http://fets.phcnpins.com/api/vproxy.asmx?op=FetchDealerBalance";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchDealerBalance xmlns="http://IKEDC_API/vproxy/">
      <dealer_code>IB0024</dealer_code>
      <hashstring>'.hash('sha512',"IB0024IB0024").'</hashstring>
      <api_key>0510770c-d3c7-4a27-8452-f55545c12f1b</api_key>
    </FetchDealerBalance>
  </soap:Body>
</soap:Envelope>';


		$header = array(
		"Content-type:text/xml;charset=\"utf-8\"",
		"Accept:application/xml",
		"Cache-Control:no-cache",
		"Pragma:no-cache",
	//	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
		"Content-length:".strlen($post_string)
        );
        
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,$url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		// curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
		 $result = curl_exec($soap_do);
		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
		 $err = curl_error($soap_do);

if($code != "200"){
    return "-".$code;
}

return $result;

                        
    }

	function eedc_bal()
    {
$url = "http://eedc.phcnpins.com/api/vproxy.asmx?op=FetchDealerBalance";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchDealerBalance xmlns="http://localhost/eedc/vproxy/">
      <dealer_code>EE0174</dealer_code>
      <hashstring>'.md5("EE0174EE0174").'</hashstring>
      <api_key>579B5A722C993135F3E0F906AB84FBBE</api_key>
    </FetchDealerBalance>
  </soap:Body>
</soap:Envelope>';


		$header = array(
		"Content-type:text/xml;charset=\"utf-8\"",
		"Accept:application/xml",
		"Cache-Control:no-cache",
		"Pragma:no-cache",
	//	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
		"Content-length:".strlen($post_string)
        );
        
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,$url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		// curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
		 $result = curl_exec($soap_do);
		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
		 $err = curl_error($soap_do);

if($code != "200"){
    return "-";
}
$result = explode("_",$result);
return $result[1];

                        
    }
    
   
   

function aunthenticate_kalac($engine)
    {
        $username = '34b61d03f814_live';
        $password = 'Brqtech@1';
        $url = 'https://api.kvg.com.ng/auth/live';


        $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = $engine->db_query2("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "AEDC_key"));
        if (!empty($rmk[0]['setting_value'])) {

            return array("status" => "200", "message" => $rmk[0]['setting_value']);

        } else {


            $payload = array("username" => $username, //terminal ID MAX 7
                    "password" => $password);


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $responseData = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($responseData, true);
            if ($response['ResponseCode'] == "100") {
                $date = date("Y-m-d H:i:s", strtotime("+0 day", strtotime($response["validUntil"])));
                $accesscode = $response["accessCode"];


               $engine->db_query2("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
                    array(
                    $accesscode,
                    $date,
                    "AEDC_key"));

                return array("status" => "200", "message" => $accesscode);
            } else {
                return array("status" => "100", "message" => $response['responseMessage']);
            }
        }

    }
    
    
function aedc($engine){
    
            $setting = aunthenticate_kalac($engine);
        if ($setting['status'] == "100") {
            return array("status" => "100", "message" =>
                    "An error occured please contact the administrator");
        }

        $token = "bearer " . $setting['message'];
        
        
        $Url = "https://api.kvg.com.ng/utilities/balance/hash/8379ED34B811251AAFFD1F557";
        $baseUrl = $Url;


        $httpMethod = "GET";
        $date = gmdate('D, d M Y H:i:s T');


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "AUTHORIZATION:$token",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
       $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);
 return $response;
} 
?>

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />

<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>PRIME</th>
<th>PAYU</th>
<th>FUND TRANSFER</th>
<th>SMS</th>
<th>Capricon</th>
<th>AEDC</th>
<th>EEDC</th>
<th>EKEDC</th>
<th>IE</th>
<th>IBEDC</th>
<th>GLO</th>
<th>MCB WALLET</th>
<th>Total Services</th>
<th>Total Members</th>
<th>Total Agent</th>
<th>Total Distributor</th>
</tr>
</thead>
<tbody>

<tr>
<td><?php echo $mobifinballance;?></td>
<td><?php echo pau_bal();?></td>
<td><?php echo fund_transfer();?></td>
<td><?php echo $engine->sms_ballance();?></td>
<td><?php $result = capricon_bal(); if(isset($result['balance'])){ echo $result['balance']; };?></td>
<td><?php $b = aedc($engine); if(isset($b['Balance'])){ echo $b['Balance'];};?></td>
<td><?php echo eedc_bal();?></td>
<td><?php echo eko_bal();?></td>
<td><?php echo ikeja_bal();?></td>
<td><?php echo ibadan_bal();?></td>
<td><?php $rmk = $GLOBALS['engine']->db_query2("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1",array("glo_bal")); echo $rmk[0]['setting_value'];?></td>

<td><?php $row = $engine->db_query2("SELECT SUM(ac_ballance) AS sm FROM rechargepro_account",array()); echo $row[0]['sm']?></td>
<td><?php 
$row = $engine->db_query2("SELECT COUNT(id) AS ct, status FROM rechargepro_services GROUP BY status ORDER BY status DESC",array());

echo $row[0]['ct']."/";
if(isset($row[1]['ct'])){echo $row[1]['ct'];}else{echo "0";}
?></td>
<td>
<?php 
$row = $engine->db_query2("SELECT COUNT(rechargeproid) AS ct, active FROM rechargepro_account GROUP BY active ORDER BY active DESC",array());

echo $row[0]['ct']."/";
if(isset($row[1]['ct'])){echo $row[1]['ct'];}else{echo "0";}
?></td>
<td><?php echo $engine->db_query2("SELECT rechargeproid FROM rechargepro_account WHERE rechargeprorole = ?",array(2), true);?></td>
<td><?php echo $engine->db_query2("SELECT rechargeproid FROM rechargepro_account WHERE rechargeprorole = ?",array(1), true);?></td>
</tr>

</tbody>
</table>

</div>

<?php
$chartarray = array();
$row = $engine->db_query2("SELECT COUNT(transactionid) AS ccount, DATE_FORMAT(transaction_date, '%d') AS day FROM rechargepro_transaction_log WHERE transaction_date BETWEEN ? AND ? GROUP BY day ORDER BY day ASC",array($start,$end));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    
         $chartarray[$row[$dbc]['day']] = $row[$dbc]['ccount'];   
        
    
    }	
?>
<script type="text/javascript">
    $(function () {
        
        
Highcharts.chart('container1', {
    chart: {
        type: 'column'
    },credits: {
enabled: false
},
    title: {
        text: 'Daily transaction for current month'
    },
    subtitle: {
        text: ''
    },
    xAxis: {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Transaction'
        }
    },
    legend: {
        enabled: false
    },
    tooltip: {
        pointFormat: 'Transaction {point.y:.1f}'
    },
    series: [{
        name: 'Transaction',
        colorByPoint: true,
        data: [<?php  foreach($chartarray AS $key => $val){echo "['$key', $val],";} ?>],
        dataLabels: {
            enabled: true,
            rotation: -90,
            color: '#FFFFFF',
            align: 'right',
            format: '{point.y:.1f}', // one decimal
            y: 10, // 10 pixels down from the top
            style: {
                fontSize: '13px',
                fontFamily: 'Verdana, sans-serif'
            }
        }
    }]
});
        })
</script>

<div id="container1" class="shadow"  style="margin:3px; width: 100%; height: 300px;"></div>



