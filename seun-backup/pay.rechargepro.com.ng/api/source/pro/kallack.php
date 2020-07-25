<?php
class kallack extends Api
{
    //201804191335
    //201804191330
    //KEDCO
    //54150432331
    //2018-08-03 12:03:52


    public function __construct($method)
    {
        $this->transaction_fee = 100;
        $this->proccess_count = 0;
    }


    //AEDCCCCCCCCCCCCCCCCCCCCCCCCC
    public function aunthenticate_kalac()
    {
        $username = self::config("kusername");
        $password = self::config("kpassword");
        $url = self::config("kauthurl");


        $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
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


    public function auth_transaction($parameter)
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


        $amount = self::cleandigit(urldecode($parameter['amount']));
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

        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM quickpay_services WHERE services_key = ? LIMIT 1",
            array($service));
        $quickpay_service = $row[0]['service_name'];
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


        if (empty($quickpay_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        if ($amount < 500) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        //$meter = "0101150565667";
        $baseUrl = self::config("kverifyurl") . $accountnumber;


        $setting = self::aunthenticate_kalac();
        if ($setting['status'] == "100") {
            return array("status" => "100", "message" =>
                    "An error occured please contact the administrator");
        }

        $token = "bearer " . trim($setting['message']);


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
            //CURLOPT_POSTFIELDS => $requestBody,
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

        $name = $response['CustomerDetail']['Name'];
        $address = $response['CustomerDetail']['Address'];
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


        if ($response['MinVendAmount'] > $amount) {
            return array("status" => "100", "message" => "Minimum Amount required " . $response['MinVendAmount']);
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = "";
        }

        #LASER
        #LASER
        $quickpayid = "0";
        $quickpayrole = 4;
        $totalmount = $amount;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT profile_agent,quickpayid,quickpayrole,service_charge,is_service_charge FROM quickpay_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $quickpayid = $row[0]['quickpayid'];
            $quickpayrole = $row[0]['quickpayrole'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
            $profile_agent = $row[0]['profile_agent'];
            
          
            /// FOR NEXT
        if(in_array($profile_agent,array(115)) || in_array($quickpayid,array(115))){
          $amount = $amount - 100; 
         if ($response['MinVendAmount'] > $amount) {
            return array("status" => "100", "message" => "Minimum Amount required " . $response['MinVendAmount']);
        } 
        }/// FOR NEXT END
            
            
            if ($quickpayrole < 4) {
                if ($is_service_charge == 1) {
                    $totalmount = $amount + $service_charge;
                    $myservice_charge = $service_charge;
                }

            }


            //invalid key
            if (empty($quickpayid)) {
                if ($parameter['private_key'] != "web") {
                    return array("status" => "100", "message" => "Invalid Key");
                }
                $quickpayid = "0";
            }

        }


        $tfee = 0;
        if ($quickpayrole > 3) {
            $tfee = $this->transaction_fee;
        }


        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO quickpay_transaction_log (service_charge,quickpayid,ip,quickpay_service,quickpay_subservice,account_meter,amount,phone,email,business_district,thirdPartycode,address,name,phcn_unique) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $myservice_charge,
            $quickpayid,
            $ip,
            $quickpay_service,
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


        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount" => $amount,
                "totalamount" => $totalmount,
                "tfee" => $tfee,
                "address" => $address,
                "unique" => $unique,
                "thirdParty" => $thirdParty,
                "business" => $district,
                "tid" => $insertid));
    }


    public function meter_info_aedc_post($parameter)
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


        $amount = self::cleandigit(urldecode($parameter['amount']));
        $phone = urldecode(trim($parameter['mobile']));
        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }
        $service = urldecode($parameter['service']);
        $accountnumber = urldecode($parameter['accountnumber']);


        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        if (strlen($phone) > 11 || strlen($phone) < 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }


        $row = self::db_query("SELECT service_name FROM quickpay_services WHERE services_key = ? LIMIT 1",
            array($service));
        $quickpay_service = $row[0]['service_name'];

        if (empty($quickpay_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        if ($amount < 500) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        //$meter = "0101150565667";
        $baseUrl = "https://api.kvg.com.ng/live/energy/aedc/postpaid/customer";
        ; //45700271799


        $setting = self::aunthenticate_kalac();
        if ($setting['status'] == "100") {
            return array("status" => "100", "message" =>
                    "An error occured please contact the administrator");
        }

        $token = "bearer " . trim($setting['message']);

        $requestBody = '{"customer_no":"' . $accountnumber . '"}';


        $httpMethod = "POST";
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


        return array("status" => "100", "message" => $result);

        if (!isset($response['code'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again");
        }


        if ($response['code'] != "100") {
            return array("status" => "100", "message" => $response["message"]);
        }


        $name = $response['name'];
        $address = $response['address'];
        $unique = $response['customer_no'];
        $thirdParty = $response['outstanding'];
        $business = "";

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


        if ($response['MinVendAmount'] > $amount) {
            return array("status" => "100", "message" => "Minimum Amount required " . $response['MinVendAmount']);
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = "";
        }

        #LASER
        $quickpayid = "0";
        $quickpayrole = 4;
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT quickpayid,quickpayrole FROM quickpay_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $quickpayid = $row[0]['quickpayid'];
            $quickpayrole = $row[0]['quickpayrole'];
        }

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO quickpay_transaction_log (quickpayid,ip,quickpay_service,quickpay_subservice,account_meter,amount,phone,email,business_district,thirdPartycode,address,name,phcn_unique) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $quickpayid,
            $ip,
            $quickpay_service,
            $service,
            $accountnumber,
            $amount,
            $phone,
            $email,
            $business,
            $thirdParty,
            $address,
            $name,
            $unique));

        $tfee = 0;
        if ($quickpayrole > 3) {
            $tfee = 100;
        }
        return array("status" => "200", "message" => array(
                "name" => $name,
                "address" => $address,
                "unique" => $unique,
                "tfee" => $tfee,
                "thirdParty" => $thirdParty,
                "business" => $business,
                "tid" => $insertid));
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


        $row = self::db_query("SELECT quickpayid, quickpay_status,transactionid,quickpay_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,quickpay_status_code,quickpay_print,transaction_date FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $quickpayid = $row[0]['quickpayid'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['quickpay_subservice'];
        $accountnumber = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $quickpay_status_code = $row[0]['quickpay_status_code'];
        $result = $row[0]['quickpay_print'];
        $primary = $row[0]['account_meter'];


        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        if ($quickpay_status_code == 1) {
            $myrow = self::db_query("SELECT ac_ballance,profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                array($quickpayid));
            $myac_ballance = $myrow[0]['ac_ballance'];
            $myprofit_bal = $myrow[0]['profit_bal'];


            $response = json_decode($result, true);
            if (!isset($response['details'])) {
                $response['details'] = $response;
            }

            if (isset($response['details']['token'])) {
                $response['Token'] = $response['details']['token'];
            }
            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
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


        if ($row[0]['quickpay_status'] != "PAID") {

            if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }


            $row = self::db_query("SELECT cordinator_percentage,percentage,bill_formular,bill_quickpayfull_percentage FROM quickpay_services WHERE services_key = ? LIMIT 1",
                array($service));
            $cordinator_percentage = $row[0]['cordinator_percentage'];
            $percentage = $row[0]['percentage'];
            $bill_formular = $row[0]['bill_formular'];
            $bill_quickpayfull_percentage = $row[0]['bill_quickpayfull_percentage'];


            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT ac_ballance,profit_bal,quickpayid,profile_creator, quickpay_cordinator, quickpayrole,service_charge,is_service_charge FROM quickpay_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $mainacbal = $ac_ballance;
            $quickpayid = $row[0]['quickpayid'];
            $profile_creator = $row[0]['profile_creator'];
            $quickpayrole = $row[0]['quickpayrole'];
            $quickpay_cordinator = $row[0]['quickpay_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];


            $myservice_charge = 0;
            if ($quickpayrole < 4) {
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
            $parameter['quickpayid'] = $quickpayid;
            $validation = $auth->validation($parameter);
            if ($validation == false) {
                return array("status" => "100", "message" => "Unauthorised Transaction");
            }


            $tfee = 0;
            if ($quickpayrole > 3) {
                $tfee = $this->transaction_fee;
            }

          ////////////////////////////////// AUTO FEED START
          //include "";
            $deduct = 1;
            include "autofeed.php";
            $autofeed = new autofeed("POST");
            $parameter['quickpayid'] = $quickpayid;
            $parameter['ac_ballance'] = $ac_ballance;
            $parameter['profile_creator'] = $profile_creator;
            $parameter['quickpayid'] = $quickpayid;
            $parameter['mainacbal'] = $mainacbal;
            $parameter['quickpayrole'] = $quickpayrole;
            $parameter['processamount'] = ($amount + $tfee);
            $autofeedvalidation = $autofeed->check_bal($parameter);
            if ($autofeedvalidation == 0) {
                return array("status" => "100", "message" => "Insufficient Fund");
            } else
                if ($autofeedvalidation == 1) {
                    $deduct = 0;
                } else {
                    $deduct = 1;
                }
 ////////////////////////////////// AUTO FEED END


            $newballance = $ac_ballance - ($amount + $tfee);


            /////////////////////////////
            if ($deduct == 1) {
                if ($channel != 1) {
                    self::db_query("UPDATE quickpay_account SET profit_bal = ? WHERE quickpayid = ? LIMIT 1",
                        array($newballance, $quickpayid));
                } else {
                    self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                        array($newballance, $quickpayid));
                }
            }


            self::db_query("UPDATE quickpay_transaction_log SET service_charge=?, cordinator_id =?, quickpay_status = ?,agent_id=?,quickpayid=?,payment_method=?,quickpay_service_charge=? WHERE transactionid = ? LIMIT 1",
                array(
                $myservice_charge,
                $quickpay_cordinator,
                "PAID",
                $profile_creator,
                $quickpayid,
                2,
                $tfee,
                $tid));


            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }


        $setting = self::aunthenticate_kalac();
        if ($setting['status'] == "100") {
            return array("status" => "100", "message" =>
                    "An error occured please contact the administrator");
        }

        $token = "bearer " . $setting['message'];


        $vref = $transaction_date . $tid;
        $Url = "";
        $baseUrl = self::config("kbuyurl") . $Url;

        $postfield = array(
            "meter" => $accountnumber,
            "amount" => $amount,
            "hash_id" => "8379ED34B811251AAFFD1F557",
            "vref" => $vref,
            "mobile" => $phone);


        $httpMethod = "POST";
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
            CURLOPT_POSTFIELDS => json_encode($postfield),
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


        //self::db_query("INSERT INTO quickpay_transaction_log (transaction_status) VALUES (?)",array($result));


        if (!isset($response['ResponseCode'])) {

            if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::complete_transaction($parameter);
            }
            // include "refund.php";
            // $refund = new refund("POST");
            //$myrefund = $refund->refund_now($parameter);
            // return array("status" => "100", "message" =>"Transaction Reversed");
            return array("status" => "300", "message" => "Pending Transaction");
        }


        //{"transactionNumber":9965,"details":{"":"6453258859733574","errorMessage":null,"":null,"utilityName":"Eskom","status":"ACCEPTED"}}

        if ($response['ResponseCode'] == "100") {

            self::db_query("UPDATE quickpay_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));

            $status = $response['ResponseMessage'];
            $statuscode = "0";
            $statusreference = $response['VendorReference'];


            //olisa
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);


            self::db_query("UPDATE quickpay_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,quickpay_status_code =?, quickpay_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));


            self::que_quickpaypay_mail($tid, $email, "success");


            $message = "Token:" . $response['Token'] . "\r\nAmount:$amount \r\nUnits:" . $response['PurchasedUnits'] .
                "\r\nInvoice Number:" . $quickpayid . "_" . $tid . "\r\nvisit quickpay.com.ng, For print out";
            self::curlit($phone, $message);


            //AEDC ARRAYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY

            $response["Account Type"] = "Prepaid";


            //$response["Transaction Status"] = "Successful";
            $response["Transaction Date"] = date("Y-m-d H:i:s");

            if (isset($response['details'])) {
                foreach ($response['details'] as $key => $value) {
                    $response[$key] = $value;
                    unset($response['details']);
                }
            }


            $arrayreturn = array();
            foreach ($response as $key => $value) {
                if (!is_array($value)) {
                    $arrayreturn[$key] = $value;
                } else {
                    foreach ($value as $keya => $valuea) {
                        $arrayreturn[$keya] = $valuea;
                    }
                }
            }


            $response = $arrayreturn;


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
        } else {
            $status = $response['ResponseMessage'];
            $statuscode = $response['ResponseCode'];
            $statusreference = "";

            if ($response['ResponseCode'] == "116") {

                return self::search_power_aedc(array(
                    "tid" => $tid,
                    "vref" => $vref,
                    "phone" => $phone,
                    "quickpayid" => $quickpayid));

            } else {

                self::db_query("UPDATE quickpay_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
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

    }

    //

    public function search_power_aedc($parameter)
    {

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        $phone = $parameter['phone'];
        $quickpayid = $parameter['quickpayid'];


        $tid = urldecode($parameter['tid']);


        $setting = self::aunthenticate_kalac();
        if ($setting['status'] == "100") {
            return array("status" => "100", "message" =>
                    "An error occured please contact the administrator");
        }

        $token = "bearer " . $setting['message'];


        //01011150565667


        $vref = $parameter['vref'];
        $Url = "https://api.kvg.com.ng/live/energy/aedc/prepaid/v2/requery/vref/$vref";
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


        if (!isset($response['ResponseCode'])) {
            return array("status" => "100", "message" =>
                    "An error occured please contact support with TID $tid 3");
        }


        //{"transactionNumber":9965,"details":{"":"6453258859733574","errorMessage":null,"":null,"utilityName":"Eskom","status":"ACCEPTED"}}

        if ($response['ResponseCode'] == "100") {

            //self::db_query("UPDATE quickpay_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",array($amount, $service));

            $status = $response['ResponseMessage'];
            $statuscode = "0";
            $statusreference = $response['VendorReference'];


            //olisa
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);


            self::db_query("UPDATE quickpay_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,quickpay_status_code =?, quickpay_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));

            $message = "Token:" . $response['Token'] . "\r\nAmount:$amount \r\nUnits:" . $response['PurchasedUnits'] .
                "\r\nInvoice Number:" . $quickpayid . "_" . $tid . "\r\nvisit quickpay.com.ng, For print out";
            self::curlit($phone, $message);


            //AEDC ARRAYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY

            $response["Account Type"] = "Prepaid";


            //$response["Transaction Status"] = "Successful";
            $response["Transaction Date"] = date("Y-m-d H:i:s");

            if (isset($response['details'])) {
                foreach ($response['details'] as $key => $value) {
                    $response[$key] = $value;
                    unset($response['details']);
                }
            }


            $arrayreturn = array();
            foreach ($response as $key => $value) {
                if (!is_array($value)) {
                    $arrayreturn[$key] = $value;
                } else {
                    foreach ($value as $keya => $valuea) {
                        $arrayreturn[$keya] = $valuea;
                    }
                }
            }


            $response = $arrayreturn;


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
        } else {
            $status = $response['ResponseMessage'];
            $statuscode = $response['ResponseCode'];
            $statusreference = "";


            self::db_query("UPDATE quickpay_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                $tid));


            //self::que_quickpaypay_mail($tid, $email, $response);

            return array("status" => "100", "message" => array(
                    "status" => "Failed",
                    "TransactionID" => $tid,
                    "details" => $response));
        }


    }


}
?>