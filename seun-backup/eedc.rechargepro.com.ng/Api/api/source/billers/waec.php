<?php
class waec extends Api
{
    
/**
 * {
    "count": 1,
    "products": [
        {
            "code": "700",
            "name": "N700 PIN",
            "price": 665,
            "currency": "NGN"
        }
    ]
}
https://clients.primeairtime.com/api/billpay/misc/BPM-NGCA-ASA
 */

    public function __construct($method)
    {
        $this->transaction_fee = 100;
    //	$this->loginid = "22809384";
      //  $this->publickey = "04888568"; 
        $this->proccess_count = 0;        
    }


    
    

    public function complete_transaction($parameter)
    {
        
        
 
 $statusreference = $parameter['ref'];
 $key = $parameter['key'];   
     
$requestBody = '{"customer_reference":"'.$statusreference.'"}';
$response = self::mobifin_post("api/billpay/misc/BPM-NGCA-ASA/700", $requestBody, true);   


$response = json_decode($response,true);








         if (isset($response['client_apiresponse'])) {
        $response = self::json_clean_decode($response['client_apiresponse'],true);
        if(!isset($response['reference'])){
          $response['reference'] = $accountnumber;  
        }
        }
        
        
        
        if (!isset($response['status'])) {
                
return array("status" => "300", "message" =>"Try Again");
        }
        
        
        
        if ($response['code'] == "RECHARGE_FAILED") {

return array("status" => "100", "message" =>"Transaction Failed");

        }
        
        

        if ($response['status'] == "200" || $response['status'] == "201" || $response['status'] == "429") {
            
          
            $message = "Token:".$response['pins'][0]['pin']."\r\n visit rechargepro.com.ng, For print out";
            self::curlit($phone, $message);
           

            return array("status" => "200", "message" => array("details" => $response,"Token"=>$response['pins'][0]['pin']));
                    
        } else if($response['status'] == "500") {
            
            return self::verify_mobifin($parameter);
            
            } else {
                
                
                
     if (isset($response['client_apiresponse'])) {

        $response = self::json_clean_decode($response['client_apiresponse'],true);
        

        if ($response['status'] == "500") {
return self::verify_mobifin($parameter);
           
           }
        
        }
        
     
        }


    }


    function mobifin_auth()
    {
        $data_string = '{
 "username" : "' . self::config('mobiusername') . '",
 "password": "' . self::config('mobipassword') . '"
}';
        $access = self::mobifin_post("auth", $data_string, true);


        if (isset($access['token'])) {
            $date = date("Y-m-d H:i:s", strtotime("+0 day", strtotime($access['expires'])));
            self::db_query("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
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
    
    
     ///////////////////////////////
    public function verify_mobifin($parameter)
    {

       $statusreference = $parameter['ref'];

        $response = self::mobifin_post("topup/log/byref/" . $statusreference, "", false);
        

         if(!isset($response['boy'])){
             return array("status" => "100", "message" =>"Transaction Failed");
            }
                
      
         if (!isset($response['client_apiresponse'])) {
         
            return array("status" => "300", "message" => "Pending Transaction");
        }
        
        
        //$response = array();
        //$response = self::json_clean_decode($response['client_apiresponse']);
        
        $response = self::json_clean_decode($response['client_apiresponse'],true);
        

        if ($response['status'] == "200" || $response['status'] == "201") {
            
             
            $message = "Token:".$response['pins'][0]['pin']." \r\n visit rechargepro.com.ng, For print out";
            self::curlit($phone, $message);
           
           
return array("status" => "200", "message" => array("details" => $response,"Token"=>$response['pins'][0]['pin']));
        } else{
            
  
return array("status" => "100", "message" =>"Transaction Failed");

           
           }
        
        
        
        
        
    }
          


    function mobifin_post($path, $data_string, $post = true)
    {
        $auth = "1";

        if ($path != "auth") {
            $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
            $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
                array($nowdate, "mobifin"));
            if (!empty($rmk[0]['setting_value'])) {

                $auth = $rmk[0]['setting_value'];

            } else {
                $access = self::mobifin_auth();
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
            
            return array("boy"=>"boy");
        }

        return $response;
    }




}

$postfield = array("paymentcode"=>$_REQUEST['secondary'],"customerid"=>$_REQUEST['primary'],"customermobile"=>"08183874966","customeremail"=>"seuntech@yahoo.com","amount"=>$_REQUEST['amount'],"ref"=>$_REQUEST['ref'],"key"=>$_REQUEST['key']);
$waec = new waec("POST");

return $waec->complete_transaction($postfield);
?>