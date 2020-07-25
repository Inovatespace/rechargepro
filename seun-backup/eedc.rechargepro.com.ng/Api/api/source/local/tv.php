<?php
class tv extends Api
{
    //KEDCO
    public function __construct($method)
    {
        $this->baseUrl = self::config('brixurl');
        $this->username = self::config('brixusername');
        $this->token = self::config('brixtoken');

        $this->proccess_count = 0;
        $this->transaction_fee = 100;
        
        
        
    }

    public function network_list($parameter)
    {
        $row = self::db_query("SELECT services_key,service_name FROM rechargepro_services WHERE services_category = ?  AND status = '1' ORDER BY id",
            array(5));
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = $row[$dbc]['service_name'];
        }
        return $return;
    }


    


    public function auth_transaction($parameter)
    {

        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        if (!isset($parameter['accountnumber'])) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }

        if (!isset($parameter['code'])) {
            return array("status" => "100", "message" => "Invalid Code");
        }
             if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }

        $service = urldecode($parameter['service']);
        $accountnumber = urldecode($parameter['accountnumber']);
        $mobile = urldecode(trim($parameter['mobile']));
        $code = urldecode($parameter['code']);


        $available_bounquet = self::available_bounquet(array("service" => $service));


        if (isset($available_bounquet['message']['items'])) {

            $amount = "";
            for ($i = 0; $i < count($available_bounquet['message']['items']); $i++) {
                if ($available_bounquet['message']['items'][$i]['code'] == $code) {
                    $amount = $available_bounquet['message']['items'][$i]['price'];
                }
            }

            if (empty($amount)) {
                return array("status" => "100", "message" => "Invalid Amount1");
            }


        } else {
            return array("status" => "100", "message" => "Invalid Amount2");
        }


        if ($amount < 100) {
            return array("status" => "100", "message" => "Invalid Amount3");
        }

        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        if (strlen($mobile) > 11 || strlen($mobile) < 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }

        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }


        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $rechargepro_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status'];

        if ($status == 0) {
            return array("status" => "100", "message" =>
                    "This service is curently Not Active");
        }

        if ($minimumsales_amount > $amount) {
            return array("status" => "100", "message" => "Minimum Amount Allowed: $minimumsales_amount");
        }

        if ($amount > $maximumsales_amount) {
            return array("status" => "100", "message" => "Maximum Amount Allowed: $maximumsales_amount");
        }

        if (empty($rechargepro_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }


        if (in_array($service, array(
            "AWA"))) {
            return self::auth_startimes($parameter);
        }
        
    

        $requestBody = '{
"details": {
"number": "' . $accountnumber . '",
"requestType": "VALIDATE_DEVICE_NUMBER"
},
"paymentCollectorId": "CDL",
"paymentMethod": "PREPAID",
"serviceId": "' . $service . '"
}';



        $httpMethod = "POST";
        $restPath = "/rest/consumer/v2/exchange/proxy";
        $date = gmdate('D, d M Y H:i:s T');


        $hashedRequestBody = base64_encode(hash('sha256', utf8_encode($requestBody), true));

        $signedData = $httpMethod . "\n" . $hashedRequestBody . "\n" . $date . "\n" . $restPath;

        $signature = hash_hmac('sha1', $signedData, $this->token, true);

        $encodedsignature = base64_encode($signature);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . $restPath,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_POSTFIELDS => $requestBody,
            CURLOPT_HTTPHEADER => array(
            "Connection: Keep-Alive",
  "Keep-Alive: 300",
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "authorization: MSP " . $this->username . ":" . $encodedsignature,
                "cache-control: no-cache",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);


        // return array("status" => "100", "message" => $result . "hhh");


        if (!isset($response['details'])) {
            return array("status" => "100", "message" =>
                    "Invalid card details or network Error, try again");
        }


        //{"transactionNumber":9965,"details":{"":"6453258859733574","errorMessage":null,"":null,"utilityName":"Eskom","status":"ACCEPTED"}}

        $namearray = array(
            "firstName",
            "lastName",
            "customerName");
        $numberarray = array("customerNumber");


        $name = "";
        if (isset($response['details']['firstName'])) {
            $name = $response['details']['firstName'];
        }

        if (isset($response['details']['lastName'])) {
            $name .= " " . $response['details']['lastName'];
        }

        if (isset($response['details']['customerName'])) {
            $name = $response['details']['customerName'];
        }

        $number = "";
        if (isset($response['details']['customerNumber'])) {
            $number = $response['details']['customerNumber'];
        }



        #LASER
        $rechargeproid = "0";
        $rechargeprorole = 4;
        $totalmount = $amount;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid,rechargeprorole,service_charge,is_service_charge FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
                        
            if($rechargeprorole < 4){
            if($is_service_charge == 1){
             $totalmount = $amount+$service_charge; 
             $myservice_charge = $service_charge;  
            }
            
             }
             
             
             //invalid key
            if (empty($rechargeproid)) {
              if($parameter['private_key'] != "web"){  
                return array("status" => "100", "message" => "Invalid Key");
                }
               $rechargeproid = "0"; 
            }
        }

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (service_charge,rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email,address,name,thirdPartycode) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $myservice_charge,
            $rechargeproid,
            $ip,
            $rechargepro_service,
            $service,
            $accountnumber,
            $amount,
            $mobile,
            $email,
            $number,
            $name,
            $code));

        $tfee = 0;
        if ($rechargeprorole > 3) {
            $tfee = $this->transaction_fee;
        }
        
        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount"=>$amount,
                "totalamount"=>$totalmount,
                "tfee" => $tfee,
                "number" => $number,
                "tid" => $insertid));
    }


private function auth_startimes($parameter){
    if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        if (!isset($parameter['accountnumber'])) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }

        if (!isset($parameter['code'])) {
            return array("status" => "100", "message" => "Invalid Code");
        }
        
                     if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }


        $service = urldecode($parameter['service']);
        $accountnumber = urldecode($parameter['accountnumber']);
        $accountnumber = explode("-",$accountnumber);
        $accountnumber = $accountnumber[0];
        
        $mobile = urldecode(trim($parameter['mobile']));
        $code = urldecode($parameter['code']);


        $available_bounquet = self::available_bounquet(array("service" => $service));


        if (isset($available_bounquet['message']['items'])) {

            $amount = "";
            for ($i = 0; $i < count($available_bounquet['message']['items']); $i++) {
                if ($available_bounquet['message']['items'][$i]['code'] == $code) {
                    $amount = $available_bounquet['message']['items'][$i]['price'];
                }
            }

            if (empty($amount)) {
                return array("status" => "100", "message" => "Invalid Amount1");
            }


        } else {
            return array("status" => "100", "message" => "Invalid Amount2");
        }


        if ($amount < 100) {
            return array("status" => "100", "message" => "Invalid Amount3");
        }

        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        if (strlen($mobile) > 11 || strlen($mobile) < 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }

        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }


        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $rechargepro_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status'];

        if ($status == 0) {
            return array("status" => "100", "message" =>
                    "This service is curently Not Active");
        }

        if ($minimumsales_amount > $amount) {
            return array("status" => "100", "message" => "Minimum Amount Allowed: $minimumsales_amount");
        }

        if ($amount > $maximumsales_amount) {
            return array("status" => "100", "message" => "Maximum Amount Allowed: $maximumsales_amount");
        }

        if (empty($rechargepro_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }




$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8080",
  CURLOPT_URL => "http://62.173.36.18:8080/stariboss-haiwai_proxy/electronicPaymentService",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.haiwai.sms.star.com" xmlns:con="http://condition.haiwai.model.sms.star.com">
<soapenv:Header>
  <CALLCENTER_USERNAME xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_USERNAME>
  <CALLCENTER_PASSWORD xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_PASSWORD>
 </soapenv:Header>
   <soapenv:Body>
      <ser:querySubscriberInfo>
         <ser:in0>
            <con:payerID>9600</con:payerID>
            <con:payerPwd>0c7eb6021ef8f4dba0a63a2fc03fdf2f</con:payerPwd>
            <con:smartCardCode>'.$accountnumber.'</con:smartCardCode>
            <con:transactionNo>'.date("YmdHis").rand(0000000,9999999).'</con:transactionNo>
         </ser:in0>
      </ser:querySubscriberInfo>
   </soapenv:Body>
</soapenv:Envelope>',
  CURLOPT_HTTPHEADER => array(
   "Connection: Keep-Alive",
                "Keep-Alive: 300",
    "cache-control: no-cache"
  ),
));

$result = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);

curl_close($curl);





$result = str_replace(array("soap:",":soap","ser:","con:"),array("","","",""),$result);
$result = preg_replace("/\p{Han}+/u", '', $result);
$xml = simplexml_load_string($result);
$json = json_encode($xml,true);
$response = json_decode($json,true);


        if (!isset($response['Body']['querySubscriberInfoResponse']['out']['customerName'])) {
            return array("status" => "100", "message" =>
                    "Invalid card details or network Error, try again");
        }
        
        $name = $response['Body']['querySubscriberInfoResponse']['out']['customerName'];

        $number = $accountnumber;

        
        if(empty($name)){
        return array("status" => "100", "message" => "Invalid Decoder Number");
        }


        #LASER
        $rechargeproid = "0";
        $rechargeprorole = 4;
                $totalmount = $amount;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid,rechargeprorole,service_charge,is_service_charge FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $rechargeprorole = $row[0]['rechargeprorole'];
                        $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
                        
            if($rechargeprorole < 4){
            if($is_service_charge == 1){
             $totalmount = $amount+$service_charge; 
             $myservice_charge = $service_charge;  
            }
            
             }
             
             
             //invalid key
            if (empty($rechargeproid)) {
              if($parameter['private_key'] != "web"){  
                return array("status" => "100", "message" => "Invalid Key");
                }
                 $rechargeproid = "0"; 
            }
        }

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (service_charge,rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email,address,name,thirdPartycode) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $myservice_charge,
            $rechargeproid,
            $ip,
            $rechargepro_service,
            $service,
            $accountnumber,
            $amount,
            $mobile,
            $email,
            $number,
            $name,
            $code));

        $tfee = 0;
        if ($rechargeprorole > 3) {
            $tfee = $this->transaction_fee;
        }
        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount"=>$amount,
                "totalamount"=>$totalmount,
                "tfee" => $tfee,
                "number" => $number,
                "tid" => $insertid));  
}


    private function startimes_bounquet()
    {
        $startimeb = array();

        $startimeb['items'][] = array(
            "code" => "UNIQUE1",
            "invoicePeriods" => array("1"),
            "price" => 3800,
            "name" => "Antenna Unique",
            "description" => " ");
        $startimeb['items'][] = array(
            "code" => "CLASSIC1",
            "invoicePeriods" => array("1"),
            "price" => 2600,
            "name" => "Antenna Classic",
            "description" => " ");
        $startimeb['items'][] = array(
            "code" => "BASIC1",
            "invoicePeriods" => array("1"),
            "price" => 1300,
            "name" => "Antenna Basic",
            "description" => " ");
        $startimeb['items'][] = array(
            "code" => "NOVA1",
            "invoicePeriods" => array("1"),
            "price" => 900,
            "name" => "Antenna Nova",
            "description" => " ");
        $startimeb['items'][] = array(
            "code" => "SUPER1",
            "invoicePeriods" => array("1"),
            "price" => 3800,
            "name" => "Dish Super",
            "description" => " ");
        $startimeb['items'][] = array(
            "code" => "SMART1",
            "invoicePeriods" => array("1"),
            "price" => 1900,
            "name" => "Dish Smart",
            "description" => " ");
        $startimeb['items'][] = array(
            "code" => "NOVA2",
            "invoicePeriods" => array("1"),
            "price" => 900,
            "name" => "Dish Nova",
            "description" => " ");

        return $startimeb;
    }

    public function available_bounquet($parameter)
    {

        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        $service = $parameter['service'];

        if ($service == "AWA") {
            return array("status" => "200", "message" => self::startimes_bounquet());
        }


        //chek
        $date = date("Y-m-d");
        $row = self::db_query("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1",array($service));// AND setting_date > ? , $date
        $setting_value = $row[0]['setting_value'];


        if (empty($setting_value)) {
            $row = self::db_query("SELECT service_resuest FROM rechargepro_services WHERE 	services_key = ? LIMIT 1",
                array($service));
            $setting_value = $row[0]['service_resuest'];
        }


        if (!empty($setting_value)) {

            $startimeb = array();
            $j = json_decode($setting_value,true);
            for ($i = 0; $i < count($j["items"]); $i++) {

                $startimeb['items'][] = array(
                    "code" => $j["items"][$i]["code"],
                    "invoicePeriods" => $j["items"][$i]["invoicePeriods"],
                    "price" => $j["items"][$i]["price"],
                    "name" => $j["items"][$i]["name"],
                    "description" => $j["items"][$i]["description"]);


            }
            return array("status" => "200", "message" => $startimeb);
        }





        $requestBody = '
{
"details": {
"requestType": "FIND_STANDALONE_PRODUCTS"
},
"paymentCollectorId": "CDL",
"paymentMethod": "PREPAID",
"serviceId": "' . $service . '"
}';


        $httpMethod = "POST";
        $restPath = "/rest/consumer/v2/exchange/proxy";
        $date = gmdate('D, d M Y H:i:s T');


        $hashedRequestBody = base64_encode(hash('sha256', utf8_encode($requestBody), true));

        $signedData = $httpMethod . "\n" . $hashedRequestBody . "\n" . $date . "\n" . $restPath;

        $signature = hash_hmac('sha1', $signedData, $this->token, true);

        $encodedsignature = base64_encode($signature);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . $restPath,
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
                "authorization: MSP " . $this->username . ":" . $encodedsignature,
                "cache-control: no-cache",
                "Connection: Keep-Alive",
  "Keep-Alive: 300",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);


        if (!isset($response['details'])) {
            return array("status" => "100", "message" =>
                    "Invalid card details or network Error, try again");
        }

        if ($service == "AQA") {
            $yes = json_decode('[{"code":"RCP_ACSSE36","invoicePeriods":[1,12],"price":4200,"name":"DStv Access + HD/ExtraView","description":" "},{"code":"RCP_COFAME36","invoicePeriods":[1,12],"price":6200,"name":"DStv Family + HD/ExtraView","description":" "},{"code":"RCP_COMPE36","invoicePeriods":[1,12],"price":9000,"name":"DStv Compact + HD/ExtraView","description":" "},{"code":"RCP_COMPLE36","invoicePeriods":[1,12],"price":12850,"name":"DStv Compact Plus + HD/ExtraView","description":" "},{"code":"RCP_PRWE36","invoicePeriods":[1,12],"price":18000,"name":"DStv Premium + HD/ExtraView","description":" "},{"code":"RCP_PRWASIE36","invoicePeriods":[1,12],"price":18730,"name":"DStv Premium Asia + HD/ExtraView","description":" "},{"code":"RCP_ASIAE36","invoicePeriods":[1,12],"price":7250,"name":"Asian Bouqet + HD/ExtraView","description":" "},{"code":"RCP_XTRA","invoicePeriods":[1,12],"price":2200,"name":"HDPVR Access/ExtraView","description":" "}]', true);
            $response['details']['items'] = array_merge_recursive($response['details']['items'],
                $yes);
        }

        //udate
        $setting_date = date("Y-m-d H:i:s");
        self::db_query("UPDATE settings SET setting_value = ?,setting_date = ? WHERE setting_key = ? LIMIT 1",
            array(
            json_encode($response['details']),
            $setting_date,
            $service));


        return array("status" => "200", "message" => $response['details']);








    }



    public function complete_transaction($parameter)
    {

        $tid = $parameter['tid'];


        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        
        if (!isset($parameter['serial'])) {
            return array("status" => "100", "message" => "Unauthorised Transaction");
        }
        
              
        $channel = 1;
        if (isset($parameter['channel'])) {
            $channel = trim(urldecode($parameter['channel']));
        }
        
        $tid = urldecode($parameter['tid']);


        $row = self::db_query("SELECT rechargeproid, rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
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

        if ($rechargepro_status_code == 1) {
$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];


            
            $response = json_decode($result, true);
            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response['details']));
        }


        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        if ($row[0]['amount'] < 1) {
            return array("status" => "100", "message" =>
                    "Payment Not successful please contact support with TID $cartid 2");
        }


        if ($row[0]['amount'] < 1) {
            return array("status" => "100", "message" =>
                    "Payment Not successful please contact support with TID $cartid 2");
        }


        if (in_array($service, array(
            "AWA"))) {
            return self::buy_startimes($parameter);
        }




        if ($row[0]['rechargepro_status'] != "PAID") {

            if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }


            $row = self::db_query("SELECT cordinator_percentage,percentage,bill_formular,bill_rechargeprofull_percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",
                array($service));
            $cordinator_percentage = $row[0]['cordinator_percentage'];
            $percentage = $row[0]['percentage'];
            $bill_formular = $row[0]['bill_formular'];
            $bill_rechargeprofull_percentage = $row[0]['bill_rechargeprofull_percentage'];


            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT ac_ballance,profit_bal,rechargeproid,profile_creator, rechargepro_cordinator, rechargeprorole,service_charge,is_service_charge FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
                 
                 
             $myservice_charge = 0;           
            if($rechargeprorole < 4){
            if($is_service_charge == 1){
             $myservice_charge = $service_charge;  
            }
            
             }            
            
            if($channel != 1){
              $ac_ballance = $profit_bal;  
            }
            
            
             //if serial set device_type, serial and ip
             //include auth
             include "auth.php";
             $auth = new auth("POST");
             $parameter['rechargeproid'] = $rechargeproid;
             $validation = $auth->validation($parameter);
             if ($validation == false) {
                return array("status" => "100", "message" => "Unauthorised Transaction");
            }




        $tfee = 0;
        if ($rechargeprorole > 3) {
            $tfee = $this->transaction_fee;
        }

            $deduct = 1;
            if (empty($ac_ballance) || ($amount+$tfee) > $ac_ballance) {
                $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $ogaballance = $row[0]['ac_ballance'];
                $ogaautofeed = $row[0]['auto_feed_cahier_account'];
                if ($ogaautofeed == 1 && $rechargeprorole < 4) {

                    if (($amount+$tfee) > $ogaballance) {
                        return array("status" => "100", "message" => "Insufficient Fund");
                    }


                    $deduct = 0;

                    $newballance = $ogaballance - ($amount+$tfee);
                    self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $profile_creator));

                } else {
                    return array("status" => "100", "message" => "Insufficient Balance");
                }

            }


            $newballance = $ac_ballance - ($amount+$tfee);



            /////////////////////////////
            if ($deduct == 1) {
                if($channel != 1){
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }else{
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }
            }
  

            self::db_query("UPDATE rechargepro_transaction_log SET service_charge=?, cordinator_id =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=?,rechargepro_service_charge=? WHERE transactionid = ? LIMIT 1",
                array(
                $myservice_charge,
                $rechargepro_cordinator,
                "PAID",
                $profile_creator,
                $rechargeproid,
                2,$tfee,
                $tid));


            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }






        include "promo1.php";



 
 
$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];




$requestBody = '{
"details": {
"productsCodes": [
"' . $thirdPartyCode . '"
],
"customerNumber": ' . $address . ',
"customerName": "' . $name . '",
"invoicePeriod":1,
"amount": ' . $amount . '
},
"id": ' . $transaction_date . $tid . ',
"paymentCollectorId": "CDL",
"paymentMethod": "PREPAID",
"serviceId": "' . $service . '"
}';




        if (strpos($thirdPartyCode, 'RCP_') !== false) {

            $PartyCode = explode("_", $thirdPartyCode);
            $tmpthirdPartyCode = $PartyCode[1];

            $requestBody = '{
"details": {
"productsCodes": [
"' . $tmpthirdPartyCode . '","HDPVRE36"
],
"customerNumber": ' . $address . ',
"customerName": "' . $name . '",
"invoicePeriod":1,
"amount": ' . $amount . '
},
"id": ' . $transaction_date . $tid . ',
"paymentCollectorId": "CDL",
"paymentMethod": "PREPAID",
"serviceId": "' . $service . '"
}';
        }


        if ($thirdPartyCode == 'RCP_XTRA') {

            $requestBody = '{
"details": {
"productsCodes": [
"HDPVRE36"
],
"customerNumber": ' . $address . ',
"customerName": "' . $name . '",
"invoicePeriod":1,
"amount": ' . $amount . '
},
"id": ' . $transaction_date . $tid . ',
"paymentCollectorId": "CDL",
"paymentMethod": "PREPAID",
"serviceId": "' . $service . '"
}';
        }


        $httpMethod = "POST";
        $restPath = "/rest/consumer/v2/exchange";
        $date = gmdate('D, d M Y H:i:s T');


        $hashedRequestBody = base64_encode(hash('sha256', utf8_encode($requestBody), true));

        $signedData = $httpMethod . "\n" . $hashedRequestBody . "\n" . $date . "\n" . $restPath;

        $signature = hash_hmac('sha1', $signedData, $this->token, true);

        $encodedsignature = base64_encode($signature);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . $restPath,
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
                "authorization: MSP " . $this->username . ":" . $encodedsignature,
                "cache-control: no-cache",
                "Connection: Keep-Alive",
  "Keep-Alive: 300",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);
        
        $response['service_charge'] = "N100";
        $response['Total_amount'] = "N".($amount+100);
        
        
        
if (isset($response['code'])) {
    
    if (in_array($response['code'], array("EXC00113", "EXC00112","EXC00102","EXC00115"))){
            include "refund.php";
            $refund = new refund("POST");
            $myrefund = $refund->refund_now($parameter);
            if ($myrefund == "200") {
                
            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => array("T Status" => "Successful", "comment" =>
                                "Please Check your transaction Log")));
                                
            } else {
                return array("status" => "100", "message" => "Transaction Reversed");
            }
            
            }
    }



        

        if (!isset($response['details'])) {
            
                        
            if($this->proccess_count == 0){
                $this->proccess_count = 1;
                return self::tv($parameter);
            }
            //            include "refund.php";
            // $refund = new refund("POST");
            // $myrefund = $refund->refund_now($parameter);
            // return array("status" => "100", "message" =>"Transaction Reversed");
            return array("status" => "100", "message" =>
                    "Pending Transaction");
        }







        if ($response['details']['status'] == "ACCEPTED") {

            $response['Card Number'] = $accountnumber;
            $available_bounquet = self::available_bounquet(array("service" => $service));
            if (isset($available_bounquet['message']['items'])) {
                for ($i = 0; $i < count($available_bounquet['message']['items']); $i++) {
                    if ($available_bounquet['message']['items'][$i]['code'] == $thirdPartyCode) {
                        $response['Bouquet'] = $available_bounquet['message']['items'][$i]['name'];
                    }
                }
            }
            $response['Amount Paid'] = $amount;
            $result = json_encode($response);

            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));


            $status = $response['details']['status'];
            $statuscode = "0";
            $statusreference = $response['details']['exchangeReference'];

           if(!isset($parameter['sms'])){
            self::curlit($phone, "Thank you your subscription is activated");
}

            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $result,
                $tid));
                
                 self::que_rechargepropay_mail($tid, $email, "success");


            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response['details']));
        } else {
            $status = $response['details']['errorMessage'];
            $statuscode = $response['details']['errorCode'];
            $statusreference = $response['details']['exchangeReference'];

            self::que_rechargepropay_mail($tid, $email, $response['details']);

            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                $tid));

            include "refund.php";
            $refund = new refund("POST");
            $myrefund = $refund->refund_now($parameter);
            if ($myrefund == "200") {
                return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => array("T Status" => "Successful", "comment" =>
                                "Please Check your transaction Log")));
            } else {
                return array("status" => "100", "message" => "Transaction Reversed");
            }
            // return array("status" => "100", "message" => array(
            //      "status" => "Failed",
            //      "TransactionID" => $tid,
            //     "details" => $response['details']));
        }



    }
    
    
    
    public function buy_startimes($parameter){
        
        
        $tid = $parameter['tid'];


        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        
        if (!isset($parameter['serial'])) {
            return array("status" => "100", "message" => "Unauthorised Transaction");
        }
        
                
        $channel = 1;
        if (isset($parameter['channel'])) {
            $channel = trim(urldecode($parameter['channel']));
        }
        $tid = urldecode($parameter['tid']);


        $row = self::db_query("SELECT rechargeproid, rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
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
        $tdate = $row[0]['transaction_date'];

        if ($rechargepro_status_code == 1) {
            $response = json_decode($result, true);
            return array("status" => "200", "message" => array(
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response['details']));
        }


        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        if ($row[0]['amount'] < 1) {
            return array("status" => "100", "message" =>
                    "Payment Not successful please contact support with TID $cartid 2");
        }


        if ($row[0]['amount'] < 1) {
            return array("status" => "100", "message" =>
                    "Payment Not successful please contact support with TID $cartid 2");
        }



        if ($row[0]['rechargepro_status'] != "PAID") {

            if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }


            $row = self::db_query("SELECT cordinator_percentage,percentage,bill_formular,bill_rechargeprofull_percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",
                array($service));
            $cordinator_percentage = $row[0]['cordinator_percentage'];
            $percentage = $row[0]['percentage'];
            $bill_formular = $row[0]['bill_formular'];
            $bill_rechargeprofull_percentage = $row[0]['bill_rechargeprofull_percentage'];


            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT ac_ballance,profit_bal,rechargeproid,profile_creator, rechargepro_cordinator, rechargeprorole,service_charge,is_service_charge FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
                 
                 
             $myservice_charge = 0;           
            if($rechargeprorole < 4){
            if($is_service_charge == 1){
             $myservice_charge = $service_charge;  
            }
            
             }            
            
            if($channel != 1){
              $ac_ballance = $profit_bal;  
            }
            
            
             //if serial set device_type, serial and ip
             //include auth
             include "auth.php";
             $auth = new auth("POST");
             $parameter['rechargeproid'] = $rechargeproid;
             $validation = $auth->validation($parameter);
             if ($validation == false) {
                return array("status" => "100", "message" => "Unauthorised Transaction");
            }




        $tfee = 0;
        if ($rechargeprorole > 3) {
            $tfee = $this->transaction_fee;
        }

            $deduct = 1;
            if (empty($ac_ballance) || ($amount+$tfee) > $ac_ballance) {
                $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $ogaballance = $row[0]['ac_ballance'];
                $ogaautofeed = $row[0]['auto_feed_cahier_account'];
                if ($ogaautofeed == 1 && $rechargeprorole < 4) {

                    if (($amount+$tfee) > $ogaballance) {
                        return array("status" => "100", "message" => "Insufficient Fund");
                    }


                    $deduct = 0;

                    $newballance = $ogaballance - ($amount+$tfee);
                    self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $profile_creator));

                } else {
                    return array("status" => "100", "message" => "Insufficient Balance");
                }

            }


            $newballance = $ac_ballance - ($amount+$tfee);



            /////////////////////////////
            if ($deduct == 1) {
                if($channel != 1){
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }else{
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }
            }
  

            self::db_query("UPDATE rechargepro_transaction_log SET service_charge=?, cordinator_id =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=?,rechargepro_service_charge=? WHERE transactionid = ? LIMIT 1",
                array(
                $myservice_charge,
                $rechargepro_cordinator,
                "PAID",
                $profile_creator,
                $rechargeproid,
                2,$tfee,
                $tid));


            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }

















        include "promo1.php";





$vedorres = $transaction_date . $tid;




$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8080",
  CURLOPT_URL => "http://62.173.36.18:8080/stariboss-haiwai_proxy/electronicPaymentService",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.haiwai.sms.star.com" xmlns:haiw="http://haiwai.model.sms.star.com">
<soapenv:Header>
  <CALLCENTER_USERNAME xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_USERNAME>
  <CALLCENTER_PASSWORD xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_PASSWORD>
 </soapenv:Header>
   <soapenv:Body>
      <ser:customerPay2>
         <ser:in0>
            <haiw:customerCode></haiw:customerCode>
            <haiw:customerName>'.$name.'</haiw:customerName>
            <haiw:customerTel>08172397885</haiw:customerTel>
            <haiw:deviceType></haiw:deviceType>
            <haiw:email></haiw:email>
            <haiw:fee>'.$amount.'</haiw:fee>
            <haiw:payerID>9600</haiw:payerID>
            <haiw:payerPwd>0c7eb6021ef8f4dba0a63a2fc03fdf2f</haiw:payerPwd>
            <haiw:receiptCode></haiw:receiptCode>
            <haiw:smartCardCode>'.$accountnumber.'</haiw:smartCardCode>
            <haiw:transactionNo>'.$vedorres.'</haiw:transactionNo>
            <haiw:transferTime>'.$tdate.'</haiw:transferTime>
         </ser:in0>
      </ser:customerPay2>
   </soapenv:Body>
</soapenv:Envelope>',
  CURLOPT_HTTPHEADER => array(
   "Connection: Keep-Alive",
    "Keep-Alive: 300",
    "cache-control: no-cache"
  ),
));

$result = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);



$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];


if(in_array($httpcode,array("404","503"))){
            include "refund.php";
            $refund = new refund("POST");
            $myrefund = $refund->refund_now($parameter);
            if ($myrefund == "200") {
            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => array("T Status" => "Successful", "comment" =>
                                "Please Check your transaction Log")));
                                
            } else {
                return array("status" => "100", "message" => "Transaction Reversed");
            } 
}


$result = str_replace(array("soap:",":soap","ser:","con:"),array("","","",""),$result);
$result = preg_replace("/\p{Han}+/u", '', $result);
$xml = simplexml_load_string($result);
$json = json_encode($xml,true);
$response = json_decode($json,true);


if(!isset($response['Body']['customerPay2Response']['out']['returnCode'])){
    
                if($this->proccess_count == 0){
                $this->proccess_count = 1;
                return self::buy_startimes($parameter);
                }
    
            return array("status" => "100", "message" => "An error occured please contact support");
};


        //{"transactionNumber":9965,"details":{"":"6453258859733574","errorMessage":null,"":null,"utilityName":"Eskom","status":"ACCEPTED"}}

        if ($response['Body']['customerPay2Response']['out']['returnCode'] == "0") {

            $response['Card Number'] = $accountnumber;
            $available_bounquet = self::available_bounquet(array("service" => $service));
            if (isset($available_bounquet['message']['items'])) {
                for ($i = 0; $i < count($available_bounquet['message']['items']); $i++) {
                    if ($available_bounquet['message']['items'][$i]['code'] == $thirdPartyCode) {
                        $response['Bouquet'] = $available_bounquet['message']['items'][$i]['name'];
                    }
                }
            }
            $response['Amount Paid'] = $amount;
            //olisa
$response['service_charge'] = "N100";
$response['Total_amount'] = "N".($amount+100);
            $result = json_encode($response);

           // self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",array($amount, $service));


            $status = "00";
            $statuscode = "0";
            $statusreference = "00";


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $result,
                $tid));
                
                
                if(!isset($parameter['sms'])){
            self::que_rechargepropay_mail($tid, $email, "success");
            self::curlit($phone, "Thank you your subscription is activated\r\nInvoice Number:".$rechargeproid."_".$tid."\r\nvisit rechargepro.com.ng, For print out");

}

            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        } else{
            $status = $response['ResultCode'];
            $statuscode = $response['ResultCode'];
            $statusreference = $response['ResultCode'];

          
            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                $tid));

            include "refund.php";
            $refund = new refund("POST");
            $myrefund = $refund->refund_now($parameter);
            if ($myrefund == "200") {
                return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => array("T Status" => "Successful", "comment" =>
                                "Please Check your transaction Log")));
            } else {
                return array("status" => "100", "message" => "Transaction Reversed");
            }
            // return array("status" => "100", "message" => array(
            //      "status" => "Failed",
            //      "TransactionID" => $tid,
            //     "details" => $response['details']));
        }


        
    }




}
//{"items":[{"code":"ACSSE36","invoicePeriods":"[1,12]","price":2000,"name":"Access","description":" "},{"code":"ACSSE37","invoicePeriods":"[1,12]","price":7400,"name":"Access + Asia","description":" "},{"code":"ACSSE38","invoicePeriods":"[1,12]","price":4200,"name":"Access + HD\/ExtraView","description":" "},{"code":"ASIAE36","invoicePeriods":"[1,12]","price":5400,"name":"Asia Standalone","description":" "},{"code":"ASIAE37","invoicePeriods":"[1,12]","price":7600,"name":"Asian + HD\/ExtraView","description":" "},{"code":"COFAME36","invoicePeriods":"[1,12]","price":4000,"name":"Family","description":" "},{"code":"COFAME37","invoicePeriods":"[1,12]","price":9400,"name":"Family + Asia","description":" "},{"code":"COFAME38","invoicePeriods":"[1,12]","price":6200,"name":"Family + HD\/ExtraView","description":" "},{"code":"COMPE36","invoicePeriods":"[1,12]","price":6800,"name":"Compact","description":" "},{"code":"COMPE37","invoicePeriods":"[1,12]","price":9000,"name":"Compact + HD\/ExtraView","description":" "},{"code":"COMPE38","invoicePeriods":"[1,12]","price":8270,"name":"Compact + French Touch","description":" "},{"code":"COMPE39","invoicePeriods":"[1,12]","price":10470,"name":"Compact + French Touch + HD\/ExtraView","description":" "},{"code":"COMPLE36","invoicePeriods":"[1,12]","price":10650,"name":"Compact Plus","description":" "},{"code":"COMPLE37","invoicePeriods":"[1,12]","price":10650,"name":"Compact Plus","description":" "},{"code":"COMPLE38","invoicePeriods":"[1,12]","price":16050,"name":"Compact Plus + Asia","description":" "},{"code":"COMPLE39","invoicePeriods":"[1,12]","price":12850,"name":"Compact Plus + HD\/ExtraView","description":" "},{"code":"COMPLE40","invoicePeriods":"[1,12]","price":12850,"name":"Compact Plus + HD\/ExtraView","description":" "},{"code":"PRWE36","invoicePeriods":"[1,12]","price":15800,"name":"Premium","description":" "},{"code":"PRWE37","invoicePeriods":"[1,12]","price":18000,"name":"Premium + HD\/ExtraView","description":" "},{"code":"PRWE38","invoicePeriods":"[1,12]","price":17270,"name":"Premium + French Touch","description":" "},{"code":"PRWE39","invoicePeriods":"[1,12]","price":19470,"name":"Premium + French Touch + HD\/ExtraView","description":" "},{"code":"PRWASIE36","invoicePeriods":"[1,12]","price":17700,"name":"Premium Asia","description":" "},{"code":"PRWFRNSE36","invoicePeriods":"[1,12]","price":22200,"name":"Premium French Bonus","description":" "},{"code":"PRWFRNSE37","invoicePeriods":"[1,12]","price":24400,"name":"Premium French Bonus + HD\/Extraview","description":" "},{"code":"PRWASIE37","invoicePeriods":"[1,12]","price":19900,"name":"Premium Asia + HD\/ExtraView","description":" "},{"code":"ASIADDE36","invoicePeriods":"[1,12]","price":5400,"name":"Asia Add-on","description":" "},{"code":"FRN11E36","invoicePeriods":"[1,12]","price":6360,"name":"French Plus","description":" "},{"code":"FRN7E36","invoicePeriods":"[1,12]","price":1470,"name":"French Touch","description":" "},{"code":"FRN11W7","invoicePeriods":"[1,12]","price":3180,"name":"French 11","description":" "},{"code":"FRN15E36","invoicePeriods":"[1,12]","price":2200,"name":"HDPVR Access\/Extraview","description":" "},{"code":"BO","invoicePeriods":"[1,12]","price":400,"name":"Box Office","description":" "}]}
?>