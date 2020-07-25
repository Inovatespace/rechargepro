<?php
class paga extends Api
{


    public function __construct($method)
    {
        $this->transaction_fee = 100;
        $this->proccess_count = 0;
        $this->token = "8A82C8F9-22AF-4E77-95E1-B57458CC39EE";
        $this->password = "tB3+f7xK=WpWzvH";
        $this->hmac = "f4c9f47fc23d46a38eb7361e0fe7bfb5fc384cd39798426cb5d9c5907880cc213f1a9195a6394db4acbd882bff4c15df3287956f87aa49cca6f54584636b24c7";
        $this->url = "https://mypaga.com";
        $this->merchantaccount = "13B5041B-7143-46B1-9A88-F355AD7EA1EC";


        //$this->token = "938312F0-8210-45B1-9777-922E35C3F2C3";
        //$this->password = "eS5#ShaREXjgUH%";
        //$this->hmac = "57ebfc8fddb240b8bbcd5aacc574a0a303c75d7f5af0464e975d3745ee8483f5ae00f6ccbd3c429a981f09fa080efd4aed752d856ec645d381e536b38a9de17a";
        //$this->url = "https://qa1.mypaga.com";
        //$this->merchantaccount = "A3878DC1-F07D-48E7-AA59-8276C3C26647";
    }
    
    //AEDCCCCCCCCCCCCCCCCCCCCCCCCC 16689
    public function aunthenticate_kalac()
    {
        $username = self::config("kusername");
        $password = self::config("kpassword");
        $url = self::config("kauthurl");


        $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "AEDC_key"));
        if (!empty($rmk[0]['setting_value'])){

            return array("status" => "200", "message" => $rmk[0]['setting_value']);

        } else {


            $payload = array("username" => $username, "password" => $password);


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


                self::db_query("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
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


public function auth_prepaid($parameter)
    {

        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        if (!isset($parameter['accountnumber'])) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }

        if (strlen($parameter['accountnumber']) < 3) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }


        $amount = self::cleanaedc(urldecode($parameter['amount']));
        $phone = urldecode(trim($parameter['mobile']));
        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }
        $service = urldecode($parameter['service']);
        $accountnumber = urldecode($parameter['accountnumber']);
        
        

        
        
        if(substr($accountnumber, 0, 4 ) === "0101"){
         $accountnumber = "02".substr($accountnumber, 4);   
        }


        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        if (strlen($phone) > 11 || strlen($phone) < 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }

        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $rechargepro_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status']; //

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

        if ($amount < 500) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        //$meter = "0101150565667";
        $baseUrl = "https://api.kvg.com.ng/live/energy/aedc/prepaid/preview";;


        $setting = self::aunthenticate_kalac();
        if ($setting['status'] == "100") {
            return array("status" => "100", "message" =>
                    "An error occured please contact the administrator");
        }

        $token = "bearer " . trim($setting['message']);


        $httpMethod = "POST";
        $date = gmdate('D, d M Y H:i:s T');
        
        $requestBody = '{
"customer_no":"'.$accountnumber.'",
"amount":"'.$amount.'"
}';


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
            CURLOPT_POSTFIELDS => $requestBody,
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



        if (!isset($response['ResponseCode'])) {
            return array("status" => "100", "message" => $result .
                    "Invalid meter details or network Error, try again");
        }


        if ($response['ResponseCode'] != "100") {
            return array("status" => "100", "message" => $response["ResponseMessage"]);
        }

        $name = $response['Name'];
        $address = "-";
        $unique = "";
        $thirdParty = $response['Topup']['Units'];
        $business = "";
        $district = "";

        if (empty($business)) {
            $business = "";
        }
        if (empty($thirdParty)) {
            $thirdParty = "";
        }
        if (empty($unique)) {
            $unique = "";
        }
        if (empty($address)) {
            $address = "";
        }
        if (empty($name)) {
            $name = "";
        }
        
        
        if ($thirdParty < 1) {
            return array("status" => "100", "message" => "Please settle your dept");
        }


        $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
        $address = preg_replace("/[^A-Za-z0-9 ]/", '', $address);





        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = "";
        }

        #LASER
        #LASER
        $rechargeproid = "0";
        $rechargeprorole = 4;
        $totalmount = $amount;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT profile_agent,rechargeproid,rechargeprorole,service_charge,is_service_charge FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
            $profile_agent = $row[0]['profile_agent'];
            
          
    
            
            
            if ($rechargeprorole < 4) {
                if ($is_service_charge == 1) {
                    $totalmount = $amount + $service_charge;
                    $myservice_charge = $service_charge;
                }

            }


            //invalid key
            if (empty($rechargeproid)) {
                if ($parameter['private_key'] != "web") {
                    return array("status" => "100", "message" => "Invalid Key");
                }
                $rechargeproid = "0";
            }

        }


        $tfee = 0;
        if ($rechargeprorole > 3) {
            $tfee = $this->transaction_fee;
        }


        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (service_charge,rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email,business_district,thirdPartycode,address,name,phcn_unique) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $myservice_charge,
            $rechargeproid,
            $ip,
            $rechargepro_service,
            $service,
            $accountnumber,
            $amount,
            $phone,
            $email,
            $district,
            $thirdParty,
            $address,
            $name,
            $unique));
            
            
            $outstanding = "";
            if(isset($response['outstanding'])){
                $outstanding = $response['outstanding'];
            }
            
            $MinVendAmount = 500;
            if(isset($response['MinVendAmount'])){
                $MinVendAmount = $response['MinVendAmount'];
            }


        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount" => $amount,
                "totalamount" => $totalmount,
                "tfee" => $tfee,
                "address" => $address,
                "unique" => $unique,
                "thirdParty" => $thirdParty,
                "business" => $district,
                "tid" => $insertid,
                "outstanding"=>$outstanding,
                "minimum"=>$MinVendAmount));
    }


    public function cleanaedc($c)
    {
        return preg_replace("/[^0-9.]/", "", $c);
    } //
    public function auth_transaction($parameter)
    {

        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Invalid Amount");
        }
        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        if (!isset($parameter['accountnumber'])) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }

        if (strlen($parameter['accountnumber']) < 3) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }


        $amount = self::cleanaedc(urldecode($parameter['amount']));
        $phone = urldecode(trim($parameter['mobile']));
        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }
        $service = urldecode($parameter['service']);
        $accountnumber = urldecode($parameter['accountnumber']);


        if (substr($accountnumber, 0, 4) === "0101") {
            $accountnumber = "02" . substr($accountnumber, 4);
        }


        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        if (strlen($phone) > 11 || strlen($phone) < 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }

        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $rechargepro_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status']; //

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

        if ($amount < 500) {
            return array("status" => "100", "message" => "Invalid Amount");
        }
        
        
      
      if (in_array($service, array("AED"))) { //

            if (substr($accountnumber, 0, 4) === "0101") {
                $accountnumber = "02" . substr($accountnumber, 4);
            }
            return self::auth_prepaid($parameter);
        }
        


        $meter = "MY003";
        if ($service == 'AEP') {
            $meter = "MY001";
        }


        $requestBody = '{"referenceNumber": "123456789",
"merchantAccount":"'.$this->merchantaccount.'",
"merchantReferenceNumber": "' . $accountnumber .
            '","merchantServiceProductCode":"' . $meter . '"}';

        $baseUrl = $this->url .
            "/paga-webservices/business-rest/secured/getMerchantAccountDetails";
        $httpMethod = "POST";

        $hashed = hash("sha512", "123456789" . $this->merchantaccount .
            $accountnumber . $meter . $this->hmac);
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
                "principal:$this->token",
                "credentials:$this->password",
                "hash:$hashed",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json"),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);


        if (!isset($response['responseCode'])) {
            return array("status" => "100", "message" => "Invalid meter details or network Error, try again");
        }


        if ($response['responseCode'] != "0") {
            return array("status" => "100", "message" => json_encode($response["message"]));
        }
        
        if(!isset($response['serviceAddress'])){$response['serviceAddress'] = "";}

        $name = $response['customerName'];
        $address = $response['serviceAddress'];
        $unique = "";
        $thirdParty = "";
        $business = "";
        $district = "";

        if (empty($business)) {
            $business = "";
        }
        if (empty($thirdParty)) {
            $thirdParty = "";
        }
        if (empty($unique)) {
            $unique = "";
        }
        if (empty($address)) {
            $address = "";
        }
        if (empty($name)) {
            $name = "";
        }


        $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
        $address = preg_replace("/[^A-Za-z0-9 ]/", '', $address);


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = "";
        }

        #LASER
        #LASER
        $rechargeproid = "0";
        $rechargeprorole = 4;
        $totalmount = $amount;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT profile_agent,rechargeproid,rechargeprorole,service_charge,is_service_charge FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
            $profile_agent = $row[0]['profile_agent'];


            if ($rechargeprorole < 4) {
                if ($is_service_charge == 1) {
                    $totalmount = $amount + $service_charge;
                    $myservice_charge = $service_charge;
                }

            }


            //invalid key
            if (empty($rechargeproid)) {
                if ($parameter['private_key'] != "web") {
                    return array("status" => "100", "message" => "Invalid Key");
                }
                $rechargeproid = "0";
            }

        }


        $tfee = 0;
        if ($rechargeprorole > 3) {
            $tfee = $this->transaction_fee;
        }


        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (service_charge,rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email,business_district,thirdPartycode,address,name,phcn_unique) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $myservice_charge,
            $rechargeproid,
            $ip,
            $rechargepro_service,
            $service,
            $accountnumber,
            $amount,
            $phone,
            $email,
            $district,
            $thirdParty,
            $address,
            $name,
            $unique));


        $outstanding = "";
        if (isset($response['outstanding'])) {
            $outstanding = $response['outstanding'];
        }

        $MinVendAmount = 500;
        if (isset($response['MinVendAmount'])) {
            $MinVendAmount = $response['MinVendAmount'];
        }


        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount" => $amount,
                "totalamount" => $totalmount,
                "tfee" => $tfee,
                "address" => $address,
                "unique" => $unique,
                "thirdParty" => $thirdParty,
                "business" => $district,
                "tid" => $insertid,
                "outstanding" => $outstanding,
                "minimum" => $MinVendAmount));
    }


    public function complete_transaction($parameter)
    {

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
        $primary = $row[0]['account_meter'];


        $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];
        $namyname = $myrow[0]['name'];

        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        if ($rechargepro_status_code == 1) {


            $response = json_decode($result, true);
            //if (!isset($response['details'])) {
            //    $response['details'] = $response;
            // }

            $response = self::array_flatten($response);


            $temarray = array_values($response);
            foreach (self::myarray() as $a) {
                if (array_key_exists($a, $response)) {
                    unset($response[$a]);
                }

                if (in_array($a, $temarray)) {
                    unset($response[$a]);
                }
            }


            if (isset($response['Address'])) {
                self::move_to_top($response, 'Address');
            }

            if (isset($response['Name'])) {
                self::move_to_top($response, 'Name');
            }

            if (isset($response['MeterNumber'])) {
                self::move_to_top($response, 'MeterNumber');
            }

            if (isset($response['Token'])) {
                self::move_to_top($response, 'Token');
            }

            if (isset($response['details']['token'])) {
                $response['Token'] = $response['details']['token'];
            }

            // $response = self::array_flatten($response);

            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        }


        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        if ($row[0]['amount'] < 1) {
            return array("status" => "100", "message" =>
                    "Payment Not successful please contact support with TID $cartid 2");
        }


/**
 *         ////////////////////////// TWICE
 *         $tmptid = 0;
 *         $tmprow = self::db_query("SELECT transaction_date,transactionid FROM rechargepro_transaction_log WHERE rechargepro_status = 'PAID' AND rechargepro_status_code = '0' AND amount = ? AND account_meter = ? AND rechargeproid = ? AND transactionid != ? LIMIT 1",
 *             array(
 *             $amount,
 *             $primary,
 *             $rechargeproid,
 *             $tid));
 *         if (!empty($tmprow[0]['transactionid'])) {
 *             $row[0]['rechargepro_status'] = "PAID";
 *             $tmptid = $tid;
 *             $transaction_date = date('Ymd', strtotime('+0 days', strtotime($tmprow[0]['transaction_date'])));
 *             $tid = $tmprow[0]['transactionid'];

 *             self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
 *                 array(
 *                 $rechargeproid,
 *                 $tmptid,
 *                 $tid,
 *                 "CLONE"));
 *             self::db_query("DELETE FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
 *                 array($tmptid));
 *         }
 *         ////////////////////////// TWICE
 */


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
            $mainacbal = $ac_ballance;
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];


            $myservice_charge = 0;
            if ($rechargeprorole < 4) {
                if ($is_service_charge == 1) {
                    $myservice_charge = $service_charge;
                }

            }

            if ($channel != 1) {
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

            ////////////////////////////////// AUTO FEED START
            //include "";
            $deduct = 1;
            include "autofeed.php";
            $autofeed = new autofeed("POST");
            $parameter['rechargeproid'] = $rechargeproid;
            $parameter['ac_ballance'] = $ac_ballance;
            $parameter['profile_creator'] = $profile_creator;
            $parameter['rechargeproid'] = $rechargeproid;
            $parameter['mainacbal'] = $mainacbal;
            $parameter['rechargeprorole'] = $rechargeprorole;
            $parameter['processamount'] = ($amount + $tfee);
            $autofeedvalidation = $autofeed->check_bal($parameter);
            if ($autofeedvalidation == "bad") {
                return array("status" => "100", "message" => "Insufficient Fund");
            } else
                if ($autofeedvalidation == "good") {
                    $deduct = 0;

                    $deduct = 1;
                }
                ////////////////////////////////// AUTO FEED END


                $newballance = $ac_ballance - ($amount + $tfee);


            /////////////////////////////
            if ($deduct == 1) {
                           if($profile_creator == "115"){
            $what = "SPECIAL_" . $profile_creator . "_" . $rechargeproid . "_" . json_encode($parameter) ."_" . $tid;
            self::db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",
                array(
                "115",
                "CAUGHT2",
                $what));
        }
        
                if ($channel != 1) {
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $rechargeproid));
                } else {
                    self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $rechargeproid));
                }
                //set bal on trans
                self::db_query("UPDATE rechargepro_transaction_log SET bal1=? WHERE transactionid = ? LIMIT 1", array($ac_ballance,$tid));
            }


            self::db_query("UPDATE rechargepro_transaction_log SET service_charge=?, cordinator_id =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=?,rechargepro_service_charge=? WHERE transactionid = ? LIMIT 1",
                array(
                $myservice_charge,
                $rechargepro_cordinator,
                "PAID",
                $profile_creator,
                $rechargeproid,
                2,
                $tfee,
                $tid));


            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }


        ////////////////////////////////////////1868800.00
          $seun =  self::db_query("SELECT id, print FROM seun WHERE status !='1' AND amount =? AND meter = ? LIMIT 1", array($amount,$accountnumber));
          $seunprint = $seun[0]['print'];
            if(!empty($seunprint)){
                    $status = "SUCCESSFUL";
                    $statuscode = "0";
                    $statusreference = rand(00000000,99999999);
        
        
                    self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                        array(
                        $status,
                        $statuscode,
                        $statusreference,
                        1,
                        $seunprint,
                        $tid));
        
        
                   self::db_query("UPDATE seun SET status = ?, newtid = ? WHERE id = ? LIMIT 1", array(1,$tid,$seun[0]['id']));
        
                    self::que_rechargepropay_mail($tid, $email, "success");
                    self::que_rechargepropay_sms($tid);
        
        
            return array("status" => "200", "message" => array(
                            "status" => "Accepted",
                            "TransactionID" => $tid,
                            "details" => $seunprint));}
        //////////////////////////////////////////


        $vref = $transaction_date . $tid;


        $meter = "MY003";
        if ($service == 'AEP') {
            $meter = "MY001";
        }


        $requestBody = '{
"referenceNumber":"' . $vref . '",
"amount":"' . $amount . '",
"currency":"NGN",
"merchantAccount":"'.$this->merchantaccount.'",
"merchantReferenceNumber":"' . $accountnumber . '",
"merchantService":[ 
"' . $meter . '"
],
"locale":"NG"}';


        //MY001
        $baseUrl = $this->url."/paga-webservices/business-rest/secured/merchantPayment";


        $httpMethod = "POST";

        $hashed = hash("sha512", $vref . $amount .
            $this->merchantaccount . $accountnumber . $this->hmac);
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
                "principal:$this->token",
                "credentials:$this->password",
                "hash:$hashed",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json"),
            ));
        $result = curl_exec($curl);
        $result = str_replace("Paga", '', $result);
        $err = curl_error($curl);
        $response = json_decode($result, true);


//if($accountnumber == "709308442"){
//file_put_contents("paeeeee.php",$result);
//}
               
                

        if (!isset($response['responseCode'])) {
            
            
            

            if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::complete_transaction($parameter);
            }
//return array("status" => "300", "message" => "Transaction Pending");//fake

            include "refund.php";
            $refund = new refund("POST");
            $myrefund = $refund->refund_now($parameter);
            if ($myrefund == "200") {
                return array("status" => "200", "message" => array(
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => array("T Status" => "Successful", "comment" =>
                                "Please Check your transaction Log")));
            } else
                if ($myrefund == "300") {
                    return array("status" => "300", "message" => "Transaction Pending");
                } else {
                    return array("status" => "100", "message" => "Transaction Reversed");
                }
                
        }


        if (in_array($response['responseCode'], array("-2"))) {
            return array("status" => "300", "message" => "Pending Transaction4");
        }


        self::db_query("UPDATE rechargepro_transaction_log SET business_district=? WHERE transactionid = ? LIMIT 1",
            array($response['responseCode'], $tid));

        if ($response['responseCode'] == "37016") {
//return array("status" => "300", "message" => "Transaction Pending");//fake
            return self::search_aedc(array("tid" => $tid));
        }


        //self::db_query("INSERT INTO rechargepro_transaction_log (transaction_status) VALUES (?)",array($result));
        //{"responseCode":0,"message":"You have successfully paid N 500.00 to for acct 13B5041B-7143-46B1-9A88-F355AD7EA1EC. Token: 28778971921865912270. Paga TxnID: F978H","referenceNumber":"22341115A","merchantTransactionReference":"28778971921865912270","transactionId":"F978H","currency":"NGN","exchangeRate":null,"fee":42.0}
        
        
       // {"responseCode":0,"message":"You have successfully paid N 500.00 to  for acct 13B5041B-7143-46B1-9A88-F355AD7EA1EC. Token: 17274409218677522163. Paga TxnID: 69QQG","referenceNumber":"20191204128866","merchantTransactionReference":"17274409218677522163","transactionId":"69QQG","currency":"NGN","exchangeRate":null,"fee":42.0}
       //{"VendorReference":"20191204128870","Reference":"20191204128870","MeterNumber":"07081777356","Amount":600,"ResponseTime":"2019-12-04 01:12:59 AM","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":" 45227269183719973596","FreeUnits":0,"ReceiptNumber":"20191204128870","PurchasedUnits":"-","DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"LT EGBO EGBO","Address":"","Tariff":"-","TariffRate":"-","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":" 45227269183719973596","service_charge":"N100","Total_amount":"N600","Account Type":"Prepaid"}
//{"responseCode":0,"message":"You have successfully paid N 500.00 to  for acct 13B5041B-7143-46B1-9A88-F355AD7EA1EC. Token: Receipt: 7002201912040804553. Paga TxnID: 20PY2","referenceNumber":"20191204128872","merchantTransactionReference":"Receipt: 7002201912040804553","transactionId":"20PY2","currency":"NGN","exchangeRate":null,"fee":42.0}       
       //128866 no token C07YF
       //3544-9849-8038-7900-6979 - 691
       
       
       
        //{"responseCode":37016,"message":"Duplicate Reference Number","referenceNumber":"22341115A","merchantTransactionReference":null,"transactionId":null,"currency":null,"exchangeRate":null,"fee":null}

        $units = $thirdPartyCode;
        if ($response['responseCode'] == "0") {
            
            
            //melove
$min = "500000";
$max = "20000000";
if(($min <= $amount) && ($amount <= $max)){
self::db_query("INSERT INTO refund_process (tid,date,amount,typep) VALUES (?,?,?,?)",array($tid,date("Y-m-d H:i:s"),$amount,"s"));  
}


            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));

            $status = $response['message'];
            $statuscode = "0";
            $statusreference = $response['referenceNumber'];


            $token = "";
            $pagamessage = $response['message'];
            $exp = explode("Token:", $pagamessage);
            if (strlen($exp[0]) > 6) {
                $exp = explode(".", $exp[1]);
                if (strlen($exp[0]) > 6) {
                    $token = $exp[0];
                }
            }
            
            if (strpos($pagamessage, "Token: Receipt:") !== false) {
                    $token = "";
                }
           
//{"responseCode":0,"message":"You have successfully paid N 500.00 to  for acct 13B5041B-7143-46B1-9A88-F355AD7EA1EC. Token: 72525069251549812640. Paga TxnID: C07YF","referenceNumber":"20191204128864","merchantTransactionReference":"72525069251549812640","transactionId":"C07YF","currency":"NGN","exchangeRate":null,"fee":42.0}

            //olisa
            $response['Agent_name'] = $namyname;
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);


            if (!isset($parameter['sms'])) {
                if ($service == "AED") {

                    $message = "Token:" . $token . "\r\nAmount:$amount \r\nUnits:" . $units . "\r\nInvoice Number:" .
                        $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                    self::curlit($phone, $message);


                } else {
                    self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                        "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
                }
            }


            if ($service == 'AED') {
              
                $response['Token'] = $token;
                $response['details']['Token'] = $token;

                $response['Units'] = $units;
                $response['details']['Units'] = $units;

                $response['pin'] = $token;

                $totalpay = $amount + 100;
                $response = '{"VendorReference":"' . $statusreference . '","Reference":"' . $statusreference .
                    '","MeterNumber":"' . $accountnumber . '","Amount":' . $amount .
                    ',"ResponseTime":"' . date("Y-m-d H:i:s A") .
                    '","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":"' . $token .
                    '","FreeUnits":0,"ReceiptNumber":"' . $statusreference .
                    '","PurchasedUnits":"' . $units .
                    '","DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"' .
                    $name . '","Address":"' . $address .
                    '","Tariff":"-","TariffRate":"-","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":"' .
                    $token . '","service_charge":"N100","Total_amount":"N' . $totalpay . '"}';
                $response = json_decode($response, true);
                $response["Account Type"] = "Prepaid";
                }
                
            if ($service == 'AEP') {
                $status = $response['message'];
                $statuscode = "0";
                $statusreference = $response['referenceNumber'];


                $totalamount = $amount + 100;
                $response = '{"transactionNumber":"' . $statusreference .
                    '","details":{"customerAddress":"' . $address .
                    '","Amount":' . $amount .',"costOfUnits":null,"ac":null,"meterNumber":null,"tariffIndex":null,"vat":"0","costOfUnit":null,"units":null,"accountNumber":"' .
                    $accountnumber . '","debtPayment":"-","supplyGroupCode":null,"customerName":"' .
                    $name . '","responseCode":"0","creditToken":null,"exchangeReference":"' . $statusreference .
                    '","receipt":"' . $statusreference .
                    '","responseMessage":"successful","fixedCharge":"0","status":"ACCEPTED","tariffInstance":"-"},"service_charge":"N100","Total_amount":"N' .
                    $totalamount . '"}';
                $response = json_decode($response, true);
                $response["Account Type"] = "Postpaid";
            }


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));


            self::que_rechargepropay_mail($tid, $email, "success");


            //AEDC ARRAYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY


            //$response["Transaction Status"] = "Successful";
            $response["Transaction Date"] = date("Y-m-d H:i:s");


            //if (!isset($response['details'])) {
            //    $response['details'] = $response;
            //}
            $response = self::array_flatten($response);


            $temarray = array_values($response);
            foreach (self::myarray() as $a) {
                if (array_key_exists($a, $response)) {
                    unset($response[$a]);
                }

                if (in_array($a, $temarray)) {
                    unset($response[$a]);
                }
            }


            if (isset($response['Address'])) {
                self::move_to_top($response, 'Address');
            }

            if (isset($response['Name'])) {
                self::move_to_top($response, 'Name');
            }

            if (isset($response['MeterNumber'])) {
                self::move_to_top($response, 'MeterNumber');
            }

            if (isset($response['Token'])) {
                self::move_to_top($response, 'Token');
            }
            //AEDC ARRAYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY


            return array("status" => "200", "message" => array(
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        }


        if ($response['responseCode'] == "-1") {
            $status = $response['message'];
            $statuscode = $response['responseCode'];
            $statusreference = "";

            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                $tid));
//return array("status" => "300", "message" => "Transaction Pending");//fake

            include "refund.php";
            $refund = new refund("POST");
            $myrefund = $refund->refund_now($parameter);
            if ($myrefund == "200") {
                return array("status" => "200", "message" => array(
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => array("T Status" => "Successful", "comment" =>
                                "Please Check your transaction Log")));
            } else
                if ($myrefund == "300") {
                    return array("status" => "300", "message" => "Transaction Pending3");
                } else {
                    return array("status" => "100", "message" => "Transaction Reversed");
                }
                //  return array("status" => "100", "message" => array(
                //            "status" => "Failed",
                //           "TransactionID" => $tid,
                //           "details" => $response));

        }else{
            return array("status" => "300", "message" => "Transaction Pending2");
        }

    }


    public function search_aedc($parameter)
    {

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
        $primary = $row[0]['account_meter'];


        $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];
        $namyname = $myrow[0]['name'];

        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        if ($rechargepro_status_code == 1) {


            $response = json_decode($result, true);
            //if (!isset($response['details'])) {
            //    $response['details'] = $response;
            // }

            $response = self::array_flatten($response);


            $temarray = array_values($response);
            foreach (self::myarray() as $a) {
                if (array_key_exists($a, $response)) {
                    unset($response[$a]);
                }

                if (in_array($a, $temarray)) {
                    unset($response[$a]);
                }
            }


            if (isset($response['Address'])) {
                self::move_to_top($response, 'Address');
            }

            if (isset($response['Name'])) {
                self::move_to_top($response, 'Name');
            }

            if (isset($response['MeterNumber'])) {
                self::move_to_top($response, 'MeterNumber');
            }

            if (isset($response['Token'])) {
                self::move_to_top($response, 'Token');
            }

            if (isset($response['details']['token'])) {
                $response['Token'] = $response['details']['token'];
            }

            // $response = self::array_flatten($response);

            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        }


        $vref = $transaction_date . $tid;


        $requestBody = '{
"referenceNumber":"' . $vref . '"}';

        //MY001
        $baseUrl = $this->url."/paga-webservices/business-rest/secured/getOperationStatus";


        $httpMethod = "POST";

        $hashed = hash("sha512", $vref . $this->hmac);
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
                "principal:$token",
                "credentials:$password",
                "hash:$hashed",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json"),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);


        //{"responseCode":0,"message":"Transaction completed successfully","referenceNumber":"22341115A","transactionId":"F978H","fee":42.0,"transactionStatus":"SUCCESSFUL"}


        if (!isset($response['responseCode'])) {

            if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::search_aedc($parameter);
            }
            // include "refund.php";
            // $refund = new refund("POST");
            //$myrefund = $refund->refund_now($parameter);
            // return array("status" => "100", "message" =>"Transaction Reversed");
            return array("status" => "300", "message" => "Pending Transaction");
        }


        if ($response['responseCode'] == "0") {

            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));

            $status = "0";
            $statuscode = "0";
            $statusreference = "";


            $token = "";


            //olisa
            $response['Agent_name'] = $namyname;
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);


            if (!isset($parameter['sms'])) {
                if ($service == "AED") {

                    $message = "Token:" . $token . "\r\nAmount:$amount \r\nUnits:" . $units . "\r\nInvoice Number:" .
                        $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                    self::curlit($phone, $message);


                } else {
                    self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                        "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
                }
            }


            if ($service == "AED") {
                
                $tks = self::token_unit($accountnumber,$transaction_date);
                
                if(isset($tks['token'])){
                    $token = $tks['token'];
                    $units = $tks['unit'];
                }
                
                $response['Token'] = $token;
                $response['details']['Token'] = $token;

                $response['Units'] = $units;
                $response['details']['Units'] = $units;

                $response['pin'] = $token;

                $totalpay = $amount + 100;
                $response = '{"VendorReference":"' . $statusreference . '","Reference":"' . $venstatusreference .
                    '","MeterNumber":"' . $accountnumber . '","Amount":' . $totalpay .
                    ',"ResponseTime":"' . date("Y-m-d H:i:s A") .
                    '","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":"' . $token .
                    '","FreeUnits":0,"ReceiptNumber":"7' . $venstatusreference .
                    '","PurchasedUnits":"' . $units .
                    '","DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"' .
                    $name . '","Address":"' . $address .
                    '","Tariff":"-","TariffRate":"-","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":"' .
                    $token . '","service_charge":"N100","Total_amount":"N' . $totalpay . '"}';
                $response = json_decode($response, true);
                $response["Account Type"] = "Prepaid";
            } else {
                $status = "-";
                $statuscode = "0";
                $statusreference = "-";


                $totalamount = $amount + 100;
                $response = '{"transactionNumber":"' . $statusreference .
                    '","details":{"customerAddress":"' . $address .
                    '","costOfUnits":null,"ac":null,"meterNumber":null,"tariffIndex":null,"vat":"0","costOfUnit":null,"units":null,"accountNumber":"' .
                    $accountnumber . '","debtPayment":"-","supplyGroupCode":null,"customerName":"' .
                    $name . '","responseCode":"0","creditToken":null,"exchangeReference":"' . $statusreference .
                    '","receipt":"' . $statusreference .
                    '","responseMessage":"successful","fixedCharge":"0","status":"ACCEPTED","tariffInstance":"-"},"service_charge":"N100","Total_amount":"N' .
                    $totalamount . '"}';
                $response = json_decode($response, true);
                $response["Account Type"] = "Postpaid";
            }


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));


            self::que_rechargepropay_mail($tid, $email, "success");


            //AEDC ARRAYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY


            //$response["Transaction Status"] = "Successful";
            $response["Transaction Date"] = date("Y-m-d H:i:s");


            //if (!isset($response['details'])) {
            //    $response['details'] = $response;
            //}
            $response = self::array_flatten($response);


            $temarray = array_values($response);
            foreach (self::myarray() as $a) {
                if (array_key_exists($a, $response)) {
                    unset($response[$a]);
                }

                if (in_array($a, $temarray)) {
                    unset($response[$a]);
                }
            }


            if (isset($response['Address'])) {
                self::move_to_top($response, 'Address');
            }

            if (isset($response['Name'])) {
                self::move_to_top($response, 'Name');
            }

            if (isset($response['MeterNumber'])) {
                self::move_to_top($response, 'MeterNumber');
            }

            if (isset($response['Token'])) {
                self::move_to_top($response, 'Token');
            }
            //AEDC ARRAYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY


            return array("status" => "200", "message" => array(
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        }


        if ($response['ResponseCode'] == "-1") {
            $status = "-";
            $statuscode = "-1";
            $statusreference = "";

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
                return array("status" => "200", "message" => array(
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => array("T Status" => "Successful", "comment" =>
                                "Please Check your transaction Log")));
            } else
                if ($myrefund == "300") {
                    return array("status" => "300", "message" => "Transaction Pending");
                } else {
                    return array("status" => "100", "message" => "Transaction Reversed");
                }
                //  return array("status" => "100", "message" => array(
                //            "status" => "Failed",
                //           "TransactionID" => $tid,
                //           "details" => $response));

        }


    }
    
    
    function token_unit($meter,$date){


    function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['descVendor'] === $id) {
           return $key;
       }
   }
   return null;
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "8443",
  CURLOPT_URL => "https://webportal.abujaelectricity.com:8443/webportal-aecms/rest/public/cms/prepaid/receipts/meterNumber/?dateFrom=$date&dateTo=$date&meterNumber=$meter",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_POSTFIELDS => "{\r\n\"customer_no\":\"07081777356\",\r\n\"amount\":\"500\"\r\n}       ",
  CURLOPT_HTTPHEADER => array(
    "Accept: */*",
    "Accept-Encoding: gzip, deflate",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    "Content-Length: 57",
    "Content-Type: application/json",
    "Host: webportal.abujaelectricity.com:8443",
    "Postman-Token: 611742ca-0ac7-4292-a1f4-227535f7ddc2,9f51c357-7028-42ba-96d8-46aa81b8232c",
    "User-Agent: PostmanRuntime/7.20.1",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

$d = json_decode($response,true);

$count = count($d);
if($count < 1){
    return array();
}


if($count > 1){
    $s = searchForId("PagaTech Prepaid",$d);
    }




$return = array("token"=>$d[0]['tokens'],"unit"=>$d[0]['units']);
return $return;
    }


}
?>