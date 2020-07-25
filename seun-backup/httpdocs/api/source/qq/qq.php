<?php

class qq extends Api
{


    public function que($parameter)
    {

        $accountnumber = $parameter['m'];
      
#
#
#
#$url = "https://eko.phcnpins.com/API/vproxy.asmx?op=FetchCust";  
#$post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
#  <soap:Body>
#    <FetchCust xmlns="http://IKEDC_API/vproxy/">
#      <MeterNo>'.$accountnumber.'</MeterNo>
#      <hashstring>'.hash('sha512',$accountnumber."EK0134").'</hashstring>
#      <api_key>46374a1d-2b9d-4ede-a7f3-731367d345cf</api_key>
#    </FetchCust>
#  </soap:Body>
#</soap:Envelope>';
#  
#        
#		$header = array(
#		"Content-type:text/xml;charset=\"utf-8\"",
#		"Accept:application/xml",
#		"Cache-Control:no-cache",
#		"Pragma:no-cache",
#	//	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
#		"Content-length:".strlen($post_string)
#        );
#        
#		 $soap_do = curl_init();
#		 curl_setopt($soap_do, CURLOPT_URL,$url );
#		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
#		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
#		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
#		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
#		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
#		 curl_setopt($soap_do, CURLOPT_POST,           true );
#		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
#		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
#		// curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
#		 $result = curl_exec($soap_do);
#		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
#		 $err = curl_error($soap_do);
#         
#         
#            
#         $result = str_replace(array("soap:",":soap"),array("",""),$result);
#         $xml = simplexml_load_string($result);
#         $json = json_encode($xml);
#         $array = json_decode($json,TRUE);
#         $array = self::array_change_value_case($array);
#         
#         
#         if ($code != 200) {
#            return array("status" => "100", "message" =>
#                    "Invalid meter details or network Error, try again1");
#        }
#         
#         
#         
#        if (!isset($array['body'])) {
#            return array("status" => "100", "message" =>
#                    "Invalid meter details or network Error, try again1");
#        }
#        
#        if (!isset($array['body']['fetchcustresponse'])) {
#            return array("status" => "100", "message" =>
#                    "Invalid meter details or network Error, try again2");
#        }
#        
#        if (!isset($array['body']['fetchcustresponse']['fetchcustresult'])) {
#            return array("status" => "100", "message" =>
#                    "Invalid meter details or network Error, try again3");
#        }
#      
#         $response = $array['body']['fetchcustresponse']['fetchcustresult'];
#         
#         
#//00_accountno: 026742255101, accounttype: 2, address: N/A, Balance: , ContactNo: , MeterNo: 026742255101, MinAmount: , Name: ALH KAREEM WAHAB
#        
#        $exp = explode("_",$response);
#        
#       
#         if (trim($exp[0]) != "00") {
#            return array("status" => "100", "message" =>
#                    "Invalid meter details or network Error, try again1");
#        }
#        
#        $exp = explode(",",$response);
#        
#        
#        
#        
#if(!isset($exp[1])){
#         return array("status" => "100", "message" =>
#                    "Invalid meter details or network Error, try again1");  
#        }
#        
#    $ex = explode(":",$exp[0]);
#    $re = trim($ex[1]);
#      if(empty($re)){
#        return array("status" => "100", "message" =>$response); 
#      }
#        
#
      

        $row = self::db_query("SELECT setting_value FROM settings WHERE setting_key = 'mobifin' LIMIT 1",
            array());
        $key = $row[0]['setting_value'];


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://clients.primeairtime.com/api/billpay/electricity/BPE-NGEK-OR/validate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"meter\" : \"$accountnumber\"}",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer $key",
                "cache-control: no-cache",
                "content-type: application/json"),
            ));

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);
        $array = json_decode($response, true);
        
         if ($code != 200) {
            return array("status"=>100,"message"=>"Invalid Meter/Network Problem Try again"); 
        }

        if ($err) {
            return array("status"=>100,"message"=>"cURL Error #:" . $err);
        }
        
if(!isset($array['name'])){
   return array("status"=>100,"message"=>"Invalid Meter/Network Problem Try again");  
}

        $name = $array['name'];
        $acnum = $array['number'];
        $address = $array['address'];


        $array = array(
            "ac" => $acnum,
            "name" => $name,
            "address" => $address);
        return array("status"=>200,"message"=>$array);



    }
    
    
    }

?>