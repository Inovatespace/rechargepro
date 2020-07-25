<?php

        
        $run = 0;
        if(in_array($rechargepro_service,array("ADD","JOD","JOP"))){
          $run = 1;
        }  
        
        
          if(in_array($rechargepro_subservice,array("2351","ACC","2352","AEC","ANB","ANA","WEC","BGA","2353","ALC","ADC","2354"))){
        $run = 1;
        print_r(verify_mobifin($tid,$engine));
        }
        
        
             if(in_array($rechargepro_subservice,array("EPP","EKP","IKP","IPP","IBB","IBP","BOA","BOB"))){
        $run = 1;
        $vref = $transaction_date.$tid;
        print_r(verify_vertibra($vref,$rechargepro_subservice,$engine));
        
        }
       
       
         if(in_array($rechargepro_subservice,array("AEPP","AEDD"))){
            $run = 1;
            $vref = $transaction_date.$tid;
            print_r(search_paga($vref));
            
        } 
        
        
        
                if(in_array($rechargepro_subservice,array("AEP"))){
            $run = 1;
            $vref = $transaction_date.$tid;
            print_r(search_power_aedc_post($vref,$tid,$engine));
            //{"responseCode":0,"message":"Transaction completed successfully","referenceNumber":"20191204128965","transactionId":"XZR8X","fee":105.0,"transactionStatus":"SUCCESSFUL"}
        } 
 
        if(in_array($rechargepro_subservice,array("AED","AEF","AEE"))){
            $run = 1;
            $vref = $transaction_date.$tid;
            print_r(search_power_aedc($vref,$tid,$engine));
            
        } 
        
                
       if(in_array($rechargepro_subservice,array("BIA","BIB"))){// starttimes ,"AWA"
        $run = 1;
        $vref = $transaction_date.$tid;
        print_r(confirm_vtpass(array("tid" => $vref),$engine));
        }      
        
        
       if(in_array($rechargepro_subservice,array("AQA","AQC"))){// starttimes ,"AWA"
        $run = 1;
        print_r(confirm_cap(array("tid" => $tid),$engine));
        }
        
        
        if(in_array($rechargepro_subservice,array("AQA","AQC"))){// starttimes ,"AWA"
        //$run = 1;
        //$vref = $transaction_date.$tid;
      //  print_r(confirm_payyou($vref));
        }
        
        
         if(in_array($rechargepro_subservice,array("2353","ALC"))){
        $run = 1;
        print_r(vendMtn($tid, fix_phone($account_meter),$amount_to_charge,$thirdPartyCode));
        }
        
        
       if(in_array($rechargepro_subservice,array("BANK TRANSFER"))){
            $run = 1;
       // $statusreference = $transaction_date ."_". $tid;
      //  print_r(post_switch($statusreference,$tid,$engine));
 
        }
        
        
        
        function search_paga($vref)
    {


        
        $requestBody = '{
"referenceNumber":"' . $vref . '"}';

        //MY001
        $baseUrl = "https://mypaga.com/paga-webservices/business-rest/secured/getOperationStatus";


        $httpMethod = "POST";



        
        $hashed = hash("sha512", $vref . "f4c9f47fc23d46a38eb7361e0fe7bfb5fc384cd39798426cb5d9c5907880cc213f1a9195a6394db4acbd882bff4c15df3287956f87aa49cca6f54584636b24c7");
        //{"responseCode":-1,"message":"Failure","referenceNumber":"22341115632","merchantTransactionReference":null,"transactionId":null,"currency":null,"exchangeRate":null,"fee":null}
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_POSTFIELDS => $requestBody,
            //CURLOPT_USERPWD => $username.":" .$password,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "principal:8A82C8F9-22AF-4E77-95E1-B57458CC39EE",
                "credentials:tB3+f7xK=WpWzvH",
                "hash:$hashed",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json"),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
      return $result;


    }
        
        
        
        function confirm_payyou($parameterref)
    {

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://mcapi-server.herokuapp.com/transactions/single/$parameterref",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_USERPWD=> "vertis:nVfQeKTn4c",
  CURLOPT_HTTPAUTH=> CURLAUTH_BASIC,  
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //CURLOPT_CUSTOMREQUEST => "POST",
  //CURLOPT_POSTFIELDS => 'xml=<PayUVasRequest Ver="1.0"><MerchantId>'.$this->payu_username.'</MerchantId><MerchantReference>'.$vedorres.'</MerchantReference><TransactionType>SINGLE</TransactionType><VasId>MCA_ACCOUNT_SQ_NG</VasId><CountryCode>NG</CountryCode><AmountInCents>'.$amount.'</AmountInCents><CustomerId>'.$accountnumber.'</CustomerId><CustomFields><Customfield Key="BasketId" Value="'.$address.'" /></CustomFields></PayUVasRequest>',
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



if(in_array($httpcode,array("404","503"))){
            return $httpcode;
}

return $response;
}


    
    
    function verify_vertibra($tref,$service,$engine){
        //"EPP","EKP","IKP","IPP","IBB","IBP","BOA","BOB"
        if(in_array($service,array("EPP","EKP"))){
  $url = "https://eko.phcnpins.com/API/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."EK0134").'</hashstring>
      <api_key>46374a1d-2b9d-4ede-a7f3-731367d345cf</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
}


        if(in_array($service,array("IKP","IPP"))){
  $url = "https://www.iepins.com.ng/API/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."IE5273").'</hashstring>
      <api_key>E146A2C4460B6511AEA043565D605C6A</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
}


        if(in_array($service,array("IBB","IBP"))){
  $url = "https://www.iepins.com.ng/API/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://IBEDC_API/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."IB0024").'</hashstring>
      <api_key>0510770c-d3c7-4a27-8452-f55545c12f1b</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
}


        if(in_array($service,array("BOA","BOB"))){
        
  $url = "http://eedcstaging.phcnpins.com/api/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."EE0174").'</hashstring>
      <api_key>579B5A722C993135F3E0F906AB84FBBE</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
}




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


//file_put_contents("dd.xml",$result);
 
            
            
         $result = str_replace(array("soap:",":soap"),array("",""),$result);
         $xml = simplexml_load_string($result);
         $json = json_encode($xml);
         $array = json_decode($json,TRUE);
         $array = $engine->array_change_value_case($array);
         
       
          return $array;
       
    }
    
    
        
        
        function fix_phone($mobile)
    {
       $mobile = "234" . substr($mobile, 1);  
       return $mobile;
    }
        
    function confirm_cap($parameter,$engine)
    {

        $tid = $parameter['tid'];


        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }


        $tid = urldecode($parameter['tid']);


        $row = $engine->db_query("SELECT rechargeproid, rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
        $rechargeproid = $row[0]['rechargeproid'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['rechargepro_subservice'];
        $accountnumber = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $result = $row[0]['rechargepro_print'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

        
        
                
        //if(!in_array($service,array("AQA","AQC","AWA","BANK TRANSFER"))){
       // $statusreference = $transaction_date . $tid;
       // return post_switch($statusreference);
       // }
        
        
        
        $baseUrl = $engine->config('brixurl');
        $username = $engine->config('brixusername');
        $token = $engine->config('brixtoken');
        
       
       
       
     
        $requestBody = '{"id": '.$transaction_date.$tid.'}';



        $httpMethod = "POST";
        $restPath = "/rest/consumer/v2/exchange/query";
        $date = gmdate('D, d M Y H:i:s T');


        $hashedRequestBody = base64_encode(hash('sha256', utf8_encode($requestBody), true));

        $signedData = $httpMethod . "\n" . $hashedRequestBody . "\n" . $date . "\n" . $restPath;

        $signature = hash_hmac('sha1', $signedData, $token, true);

        $encodedsignature = base64_encode($signature);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl . $restPath,
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
                "authorization: MSP " . $username . ":" . $encodedsignature,
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);


        //return array("status" => "100", "message" =>$result.$err);

        //if (!isset($response['details'])) {
         return $response;//array("status" => "100", "message" =>"dddddddddddddd");
        //}




    }





    
function post_switch($unique,$engine){
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.ravepay.co/v2/gpx/transfers?seckey=FLWSECK-efba7abe0decca4441c236caf91d9c76-X&reference=".$unique,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  return "cURL Error #:" . $err;
} else {
  return json_decode($response,true);
}
    }
    
    
    
    
    
    
    
    
//

function search_power_aedc($vref,$tid,$engine)
    {


  $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = $engine->db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "AEDC_key"));


        $token = "bearer ".$rmk[0]['setting_value'];


        //01011150565667


       // $vref = $transaction_date . $tid;
        $Url = "https://api.kvg.com.ng/live/energy/aedc/prepaid/requery/vref/$vref";
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
    
    
    function search_power_aedc_post($vref,$tid,$engine)
    {


  $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = $engine->db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "AEDC_key"));


        $token = "bearer ".$rmk[0]['setting_value'];


        //01011150565667


       // $vref = $transaction_date . $tid;
        $Url = "https://api.kvg.com.ng/live/energy/aedc/postpaid/payment/$vref";
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
    
    
    
    	function vendMtn($sequence, $destMsisdn, $amount = 0, $tariffTypeId = 1)
    {
		//echo $sequence." -- ".$destMsisdn." -- ".$amount."<br/>"; //die();
        //1 or 9
        
           // return array("status" => "100", "message" => $destMsisdn."_".$amount);
     
		$username="rechargepro-AFRICA_!46";
		$password="dyap_yuwy!hyd56";
		$origMsisdn="2348137266424";
		$url = "https://rechargepro-AFRICA_!46:dyap_yuwy!hyd56@41.220.77.137:443/axis2/services/rechargepro-AFRICAService";
                
		$post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://hostif.vtm.prism.co.za/xsd"><soapenv:Header/><soapenv:Body><vend xmlns="http://hostif.vtm.prism.co.za/xsd"><sequence>'.$sequence.'</sequence><origMsisdn>'.$origMsisdn.'</origMsisdn><destMsisdn>'.$destMsisdn.'</destMsisdn><amount>'.$amount.'</amount><tariffTypeId>'.$tariffTypeId.'</tariffTypeId></vend></soapenv:Body></soapenv:Envelope>';
        
                        
	
		$header = array(
		"Content-type:text/xml;charset=\"utf-8\"",
		"Accept:application/xml",
		"Cache-Control:no-cache",
		"Pragma:no-cache",
		"SOAPAction:https://rechargepro-AFRICA_!46:dyap_yuwy!hyd56@41.220.77.137:443/axis2/services/rechargepro-AFRICAService",
		"Content-length:".strlen($post_string));
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,            $url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		 curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
		 $result = curl_exec($soap_do);
		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
		 $err = curl_error($soap_do);
        curl_close($soap_do);
		 //print_r($result, true); //die();
         //$json = json_encode($result);
         //file_put_contents("myxmlfile1.xml", $result);
         //file_put_contents("myxmlfile2.xml", $json);
         //soapenv: :soapenv
         
         $eml = array();
        $result = str_replace(array("soapenv:",":soapenv"),array("",""),$result);
         $xml = simplexml_load_string($result);
        
		return $xml;
    }
    
    
    
	function vendglo($sequence, $destMsisdn, $amount = 0, $tariffTypeId = 1, $dataplan = "")
    {
		//echo $sequence." -- ".$destMsisdn." -- ".$amount."<br/>"; //die();
        //1 or 9
        
     $airtime = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ext="http://external.interfaces.ers.seamless.com/">
   <soapenv:Header/>
   <soapenv:Body>
      <ext:requestTopup>
         <!--Optional:-->
         <context>
            <!--Optional:-->
            <channel>WSClient</channel>
            <!--Optional:-->
            <clientComment>prod xml for ers</clientComment>
            <!--Optional:-->
            <clientId>ERS</clientId>
            <!--Optional:-->
            <clientReference>'.$sequence.'</clientReference>
            <clientRequestTimeout>500</clientRequestTimeout>
            <!--Optional:-->
            <initiatorPrincipalId>
               <!--reseller id for parent:-->
               <id>WEB7054389333</id>
               <!--Optional:-->
               <type>RESELLERUSER</type>
               <!--Optional:-->
               <userId>9900</userId>
            </initiatorPrincipalId>
            <!--password for parent:-->
            <password>K1@nbegty</password>
         </context>
         <!--Optional:-->
         <senderPrincipalId>
            <!--reseleer id for parent:-->
            <id>WEB7054389333</id>
            <!--Optional:-->
            <type>RESELLERUSER</type>
            <!--user for the reseller:-->
            <userId>9900</userId>
         </senderPrincipalId>
         <!--Optional:-->
         <topupPrincipalId>
            <!--user to be topup:-->
            <id>'.$destMsisdn.'</id>
            <!--Optional:-->
            <type>SUBSCRIBERMSISDN</type>
            <!--Optional:-->
            <userId>9900</userId>
         </topupPrincipalId>
         <!--Optional:-->
         <senderAccountSpecifier>
            <!--reselleer id for parent:-->
            <accountId>WEB7054389333</accountId>
            <!--Optional:-->
            <accountTypeId>RESELLER</accountTypeId>
         </senderAccountSpecifier>
         <!--Optional:-->
         <topupAccountSpecifier>
            <!--user to be toped up:-->
            <accountId>'.$destMsisdn.'</accountId>
            <!--Optional:-->
            <accountTypeId>AIRTIME</accountTypeId>
         </topupAccountSpecifier>
         <!--Optional:-->
         <productId>TOPUP</productId>
         <!--Optional:-->
         <amount>
            <!--currency to be toped up:-->
            <currency>NGN</currency>
            <!--amount to be toped up:-->
            <value>'.$amount.'</value>
         </amount>
      </ext:requestTopup>
   </soapenv:Body>
</soapenv:Envelope>';


$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ext="http://external.interfaces.ers.seamless.com/">
 <soapenv:Header/>
 <soapenv:Body>
    <ext:requestTopup>
       <!--Optional:-->
       <context>
          <!--Optional:-->
          <channel>WSClient</channel>
          <!--Optional:-->
          <clientComment>WEB7054389333</clientComment>
          <!--Optional:-->
          <clientId>ERS</clientId>
          <!--Optional:-->
          <prepareOnly>false</prepareOnly>
          <!--Optional:-->
          <clientReference>'.$sequence.'</clientReference>
          <clientRequestTimeout>500</clientRequestTimeout>
          <!--Optional:-->
          <initiatorPrincipalId>
             <!--Optional:-->
             <id>WEB7054389333</id>
             <!--Optional:-->
             <type>RESELLERUSER</type>
             <!--Optional:-->
             <userId>9900</userId>
          </initiatorPrincipalId>
          <!--Optional:-->
          <password>K1@nbegty</password>
          <!--Optional:-->
          <transactionProperties>
             <!--Zero or more repetitions:-->
             <entry>
                <!--Optional:-->
                <key>TRANSACTION_TYPE</key>
                <!--Optional:-->
                <value>PRODUCT_RECHARGE</value>
             </entry>
          </transactionProperties>
       </context>
       <!--Optional:-->
       <senderPrincipalId>
          <!--Optional:-->
          <id>WEB7054389333</id>
          <!--Optional:-->
          <type>RESELLERUSER</type>
          <!--Optional:-->
          <userId>9900</userId>
       </senderPrincipalId>
       <!--Optional:-->
       <topupPrincipalId>
          <!--Optional:-->
          <id>'.$destMsisdn.'</id>
          <!--Optional:-->
          <type>SUBSCRIBERMSISDN</type>
          <!--Optional:-->
          <userId>9900</userId>
       </topupPrincipalId>
       <!--Optional:-->
       <senderAccountSpecifier>
          <!--Optional:-->
          <accountId>WEB7054389333</accountId>
          <!--Optional:-->
          <accountTypeId>RESELLER</accountTypeId>
       </senderAccountSpecifier>
       <!--Optional:-->
       <topupAccountSpecifier>
          <!--Optional:-->
          <accountId>'.$destMsisdn.'</accountId>
          <!--Optional:-->
          <accountTypeId>DATA_BUNDLE</accountTypeId>
       </topupAccountSpecifier>
       <!--Optional:-->
       <productId>'.$dataplan.'</productId>
       <!--Optional:-->
       <amount>
          <!--Optional:-->
          <currency>NGN</currency>
          <!--Optional:-->
          <value>'.$amount.'</value>
       </amount>
    </ext:requestTopup>
 </soapenv:Body>
</soapenv:Envelope>';




$post_string = $airtime;
if($tariffTypeId == "9"){
    $post_string = $data;
}
		$url = "http://41.203.65.10:8913/topupservice/service?wsdl";

	
		$header = array("Content-type:text/xml;charset=\"utf-8\"",
		"Accept:application/xml",
		"Cache-Control:no-cache",
		"Pragma:no-cache");
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,            $url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		 $result = curl_exec($soap_do);
		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
		 $err = curl_error($soap_do);
        curl_close($soap_do);

            $response = str_replace(array("&lt;","&"),array("<","and"),$result); 

            $all_data = array();
            $exil = array();
            $sPattern = "/<return>(.*?)<\/return>/s";
            preg_match($sPattern, $response, $aMatch);
            $data = $aMatch[1];
            $old = array('<![CDATA[', ']]>');
            $new = array('', '');
            $finals = str_replace($old, $new, $data);
            $final = html_entity_decode($finals);
            $final = "<seuntech>$final</seuntech>";
            $simpleXml = simplexml_load_string($final);
            $json = json_encode($simpleXml);
            $all_data = json_decode($json, true);
         
         return $all_data;
    }
    
    
    
    
     ///////////////////////////////
    function verify_mobifin($tid,$engine)
    {


        $row = $engine->db_query("SELECT rechargeproid, rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
            $rechargeproid = $row[0]['rechargeproid'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['rechargepro_subservice'];
        $accountnumber = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $result = $row[0]['rechargepro_print'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));


        $response = mobifin_post("topup/log/byref/RECH" . $transaction_date.$tid, "", false,$engine);
        
        return $response;

    }
    
    

    function mobifin_post($path, $data_string, $post = true,$engine)
    {
        $auth = "1";

        if ($path != "auth") {
            $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
            $rmk = $engine->db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
                array($nowdate, "mobifin"));
            if (!empty($rmk[0]['setting_value'])) {

                $auth = $rmk[0]['setting_value'];

            } else {
                $access = mobifin_auth($engine);
                if ($access['status'] == "100") {
                    return $access;
                } else {
                    $auth = $access['message'];
                }
            }
        }





        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://clients.primeairtime.com/api/' . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "accept: application/json, application/*+json",
            "accept-encoding: gzip,deflate",
            "Authorization: Bearer $auth",
            "cache-control: no-cache",
                            "Connection: Keep-Alive",
                "Keep-Alive: 300",
            "content-type: application/json",
            ));

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = json_decode($result, true);

        
        if(in_array($httpcode,array("404","503"))){
            return $httpcode;
        }

        return $response;
    }

    function mobifin_auth($engine)
    {
        $data_string = '{
 "username" : "' . $engine->config('mobiusername') . '",
 "password": "' . $engine->config('mobipassword') . '"
}';
        $access = mobifin_post("auth", $data_string, true);

        if (isset($access['token'])) {
            $date = date("Y-m-d H:i:s", strtotime("+0 day", strtotime($access['expires'])));
            $engine->db_query("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
                array(
                $access['token'],
                $date,
                "mobifin"));
            return array("status" => "200", "message" => $access['token']);
        } else {
            return array("status" => "100", "message" => "Network Error");
        }

    }
    
    function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    if(version_compare(phpversion(), '5.4.0', '>=')) { 
        return json_decode($json, $assoc, $depth, $options);
    }
    elseif(version_compare(phpversion(), '5.3.0', '>=')) { 
        return json_decode($json, $assoc, $depth);
    }
    else {
        return json_decode($json, $assoc);
    }
    }
    
    
     function confirm_vtpass($parameter)
    {

        $tid = $parameter['tid'];


                $url = "https://vtpass.com/api/query";
        $post_string = array(
  'request_id' =>$tid // unique for every transaction
    );


        $header = array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "cache-control: no-cache",
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "content-type: application/json");

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_USERPWD,"nebolisa@vertistechnologiesltd.com:Change@123");
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        //curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        // curl_setopt($soap_do, CURLOPT_USERPWD, $this->username.":".$this->password);
        $result = curl_exec($soap_do);
        $httpcode = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);

        if (in_array($httpcode, array("404", "503"))) {
            return $err;
        }

return $result;
    }
    
    

    
?>