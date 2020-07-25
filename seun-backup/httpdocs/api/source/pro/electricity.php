<?php
class electricity extends Api
{
    //201804191335
    //201804191330
    //KEDCO
    //54150432331
    //2018-08-03 12:03:52


    public function __construct($method)
    {
        //Api::Api_Method("user_key", "POST", $method); // this tell the API to only allow POST method
        $this->baseUrl = self::config('brixurl');
        $this->username = self::config('brixusername');
        $this->token = self::config('brixtoken');


        $this->transaction_fee = 100;
        $this->proccess_count = 0;

    }

    ///////////////////  EXTERNAL

    public function utility_list($parameter)
    {
        $row = self::db_query("SELECT services_key,service_name FROM rechargepro_services WHERE services_category = ?  AND status = '1' ORDER BY service_name ASC",
            array(1));
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = $row[$dbc]['service_name'];
        }
        return $return;
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

        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid Key");
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


        /// FOR NEXT
        if (isset($parameter["private_key"])) {
            $key = urldecode(trim($parameter["private_key"]));
            $row = self::db_query("SELECT profile_agent,rechargeproid FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($key));
            $profile_agent = $row[0]['profile_agent'];
            $rechargeproid = $row[0]['rechargeproid'];
            if (in_array($profile_agent, array(115)) || in_array($rechargeproid, array(115))) {
                $onehundred = 100;
                $amount = $amount - 100;
                $parameter['amount'] = $amount;
            }
        }


        if (in_array($service, array("AED", "AEP"))) { //

            if (substr($accountnumber, 0, 4) === "0101") {
                $accountnumber = "02" . substr($accountnumber, 4);
            }

            include "paga.php";
            $paga = new paga("POST");
            return $paga->auth_transaction($parameter);
        }

        if (in_array($service, array("AED", "AEP"))){ //

            if (substr($accountnumber, 0, 4) === "0101") {
                $accountnumber = "02" . substr($accountnumber, 4);
            }

            include "kallack.php";
            $kallack = new kallack("POST");
            return $kallack->auth_transaction($parameter);
        }


        if (in_array($service, array("BOA", "BOB"))) {
            include "eedc.php";
            $eedc = new eedc("POST");
            return $eedc->auth_transaction($parameter);
        }


        if (in_array($service, array("BIA", "BIB"))) {
            include "phedc.php";
            $phedc = new phedc("POST");
            return $phedc->auth_transaction($parameter);
        }


        if (in_array($service, array("EPP", "EKP"))) {
            include "eko.php";
            $eko = new eko("POST");
            return $eko->auth_transaction($parameter);
        }

        if (in_array($service, array("IKP", "IPP"))) {
            include "ikeja.php";
            $ikeja = new ikeja("POST");
            return $ikeja->auth_transaction($parameter);
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

        if ($amount < 10) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        if (in_array($service, array("BIA", "BIB"))) {
            if (strlen($accountnumber) > 14) {
                $accountnumber = substr($accountnumber, 2);
            }
        }


        $requestBody = '{
    "details": {
        "customerReference": "' . $accountnumber . '"
    },
    "serviceId": "' . $service . '"
}';


        if ($service == "BOA") {
            $requestBody = '{
        "details": {
            "customerReference": "' . $accountnumber . '",
            "requestType": "VALIDATE_METER_NUMBER"
        },
        "serviceId": "BOA"
}
';
        }

        if ($service == "BOB") {
            $requestBody = '{
        "details": {
            "customerReference": "' . $accountnumber . '",
            "requestType": "VALIDATE_ACCOUNT_NUMBER"
        },
        "serviceId": "BOB"
}';
        }


        if ($service == "AED") {
            $requestBody = '{
        "details": {
            "customerReference": "' . $accountnumber . '",
            "customerReferenceType": "STS_PREPAID"
        },
        "serviceId": "BABA"
}
';
        }


        if ($service == "AEP") {
            $requestBody = '{
        "details": {
            "customerReference": "' . $accountnumber . '",
            "customerReferenceType": "POSTPAID"
        },
        "serviceId": "BABB"
}
';
        }


        if ($service == "EPP") {
            $requestBody = '{
        "details": {
                "customerReference": "' . $accountnumber . '"
        },
        "serviceId": "AVA"
}';
        }
        if ($service == "EKP") {
            $requestBody = '{
        "details": {
                "meterNumber": "' . $accountnumber . '"
        },
        "serviceId": "BAA"
}';
        }


        if ($service == "IKP") {
            $requestBody = '{
        "details": {
                "meterNumber": "' . $accountnumber . '"
        },
        "serviceId": "APB"
}';
        }
        if ($service == "IPP") {
            $requestBody = '{
        "details": {
                "customerNumber": "' . $accountnumber . '"
        },
        "serviceId": "APA"
}';
        }


        if ($service == "IBB") {
            $requestBody = '{
    "details": {
        "customerReference": "' . $accountnumber . '"
    },
    "serviceId": "AUB"
}';
        }

        if ($service == "IBP") {
            $requestBody = '{
        "details": {
            "customerReference": "' . $accountnumber . '"
        },
        "serviceId": "AUA"
}';
        }


        if ($service == "AVB") {
            $requestBody = '{
    "details": {
        "customerReference": "' . $accountnumber . '"
    },
    "serviceId": "AVB"
}';
        }

        if ($service == "AVC") {
            $requestBody = '{
    "details": {
        "customerReference": "' . $accountnumber . '"
    },
    "serviceId": "AVC"
}';
        }


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


        //file_put_contents("ff.php", $result);
        // return array("status" => "100", "message" =>$result);


        if (!isset($response['details'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again");
        }


        //
        //phoneNumber
        //
        $district = "";

        if (isset($response['details']['district'])) {
            $district = $response['details']['district'];
        }

        if (isset($response['details']['customerDistrict'])) {
            $district = $response['details']['customerDistrict'];
        }

        if (isset($response['details']['firstName'])) {
            $response['details']['customerName'] = $response['details']['firstName'] . " " .
                $response['details']['lastName'];
        }


        $address = "";
        if (isset($response['details']['address'])) {
            $address = $response['details']['address'];
        }
        if (isset($response['details']['customerAddress'])) {
            $address = $response['details']['customerAddress'];
        }

        if (isset($response['details']['accountNumber'])) {
            $response['details']['uniqueReference'] = $response['details']['accountNumber'];
        }

        if (isset($response['details']['name'])) {
            $response['details']['customerName'] = $response['details']['name'];
        }


        if (empty($response['details']['customerName'])) {
            return array("status" => "100", "message" => "Invalid Account");
        }


        $name = $response['details']['customerName'];

        $thirdParty = "";
        $business = "";
        $thirdParty = "";
        $unique = "";

        if (isset($response['details']['customerReference'])) {
            $thirdParty = $response['details']['customerReference'];
        }

        if (isset($response['details']['thirdPartyCode'])) {
            $thirdParty = $response['details']['thirdPartyCode'];
        }


        if (isset($response['details']['customerDtNumber'])) {
            $thirdParty = $response['details']['customerDtNumber'];
        }


        if (isset($response['details']['uniqueReference'])) {
            $unique = $response['details']['uniqueReference'];
        }

        if (isset($response['details']['uniqueCode'])) {
            $unique = $response['details']['uniqueCode'];
        }

        if (isset($response['details']['customerAccountType'])) {
            $unique = $response['details']['customerAccountType'];
        }


        $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
        $address = preg_replace("/[^A-Za-z0-9 ]/", '', $address);


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = "";
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
        if (isset($response['outstandingAmount'])) {
            $outstanding = $response['outstandingAmount'];
        }

        $MinVendAmount = 500;
        if (isset($response['minimumAmount'])) {
            $MinVendAmount = $response['minimumAmount'];
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


        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        if ($rechargepro_status_code == 1) {
            $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($rechargeproid));
            $myac_ballance = $myrow[0]['ac_ballance'];
            $myprofit_bal = $myrow[0]['profit_bal'];
            $namyname = $myrow[0]['name'];


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


        if (in_array($service, array("AED", "AEP"))) {
            include "paga.php";
            $paga = new paga("POST");
            return $paga->complete_transaction($parameter);
        }

        if (in_array($service, array(
            "AED",
            "AEP",
            "AEF",
            "AEE"))) { //,"AEP"
            include "kallack.php";
            $kallack = new kallack("POST");
            return $kallack->complete_transaction($parameter);
        }


        if (in_array($service, array("BOA", "BOB"))) {
            include "eedc.php";
            $eedc = new eedc("POST");
            return $eedc->complete_transaction($parameter);
        }

        if (in_array($service, array("EPP", "EKP"))) {
            include "eko.php";
            $eko = new eko("POST");
            return $eko->complete_transaction($parameter);
        }

        if (in_array($service, array("IKP", "IPP"))) {
            include "ikeja.php";
            $ikeja = new ikeja("POST");
            return $ikeja->complete_transaction($parameter);
        }


        if (in_array($service, array("IBB", "IBP"))) {
            // include "ibadan.php";
            //$ibadan = new ibadan("POST");
            //return $ibadan->complete_transaction($parameter);
        }


        if (in_array($service, array("BIA", "BIB"))) {
            include "phedc.php";
            $phedc = new phedc("POST");
            return $phedc->complete_transaction($parameter);
        }


        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $rechargeproid));
                } else {
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
                2,
                $tfee,
                $tid));


            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }


        ////////////////////////////////////////1868800.00
        $seun = self::db_query("SELECT id, print FROM seun WHERE status !='1' AND amount =? AND meter = ? LIMIT 1",
            array($amount, $accountnumber));
        $seunprint = $seun[0]['print'];
        if (!empty($seunprint)) {
            $status = "SUCCESSFUL";
            $statuscode = "0";
            $statusreference = rand(00000000, 99999999);


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $seunprint,
                $tid));


            self::db_query("UPDATE seun SET status = ?, newtid = ? WHERE id = ? LIMIT 1",
                array(
                1,
                $tid,
                $seun[0]['id']));

            if (!isset($parameter['sms'])) {
                self::que_rechargepropay_mail($tid, $email, "success");
                //self::curlit($tid);
            }


            return array("status" => "200", "message" => array(
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $seunprint));
        }
        ////////////////////////////////////////


        $statusreference = $transaction_date . $tid;


        $requestBody = '{"details": {"customerName": "' . $name . '","customerPhone": "' .
            $phone . '","customerReference": "' . $accountnumber . '","uniqueReference": "' .
            $unique . '","amount": ' . $amount . '},"id":"' . $statusreference .
            '","paymentCollectorId":"CDL","paymentMethod":"PREPAID","serviceId":"' . $service .
            '"}';


        if (in_array($service, array("BOA", "BOB"))) {
            $requestBody = '{"details": {
            "accountNumber": "' . $unique . '",
            "amount": ' . $amount . ',
            "customerAddress": "' . $address . '",
            "customerDistrict": "' . $district . '",
            "customerName": "' . $name . '",
            "meterNumber": "' . $accountnumber . '",
            "customerPhoneNumber": "' . $phone . '"
        },
        "id":"' . $statusreference . '",
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "' . $service . '"
}';
        }

        if (in_array($service, array("AEP"))) {
            $requestBody = '{"details": {
            "customerReference": "' . $accountnumber . '",
            "customerReferenceType": "POSTPAID",
            "amount": ' . $amount . ',
            "uniqueCode": "' . $unique . '"
        },
        "id":' . $statusreference . ',
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "BABB"
}';
        }


        if (in_array($service, array("AED"))) {
            $requestBody = '{"details": {
            "customerReference": "' . $accountnumber . '",
            "customerReferenceType": "STS_PREPAID",
            "amount": ' . $amount . ',
            "uniqueCode": "' . $unique . '"
        },
        "id":' . $statusreference . ',
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "BABA"
}';
        }


        ///////***************************************///////////////


        if ($service == "EPP") {
            $requestBody = '{
        "details": {
                "customerReference": "' . $accountnumber . '",
                "amount": ' . $amount . '
        },
        "id": ' . $statusreference . ',
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "AVA"
}';
        }

        if ($service == "EKP") {
            $requestBody = '{
        "details": {
                "customerAddress": "' . $address . '",
                "customerDistrict": "' . $district . '",
                "customerName": "' . $name . '",
                "meterNumber": "' . $accountnumber . '",
                "amount": ' . $amount . '
        },
        "id": ' . $statusreference . ',
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "BAA"
}';
        }


        if ($service == "IKP") {
            $requestBody = '{
        "details": {
                "meterNumber": "' . $accountnumber . '",
                "amount": ' . $amount . ',
                "phoneNumber": "08183874966",
                "email": "seuntech2k@yahoo.com",
                "customerName": "' . $name . '",
                "customerAddress": "' . $address . '",
                "customerDtNumber": "' . $thirdPartyCode . '",
                "customerAccountType":"' . $unique . '",
                "contactType": "TENANT"
        },
        "id": ' . $statusreference . ',
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "APB"
}';
        }

        if ($service == "IPP") {
            $requestBody = '{
        "details": {
                "accountNumber": "' . $accountnumber . '",
                "amount": ' . $amount . ',
                "phoneNumber": "08183874966",
                "email": "seuntech2k@yahoo.com",
                "customerName": "' . $name . '",
                "customerAddress": "' . $address . '",
                "customerDtNumber": "' . $thirdPartyCode . '",
                "customerAccountType":"' . $unique . '",
                "contactType": "LANDLORD"
        },
        "id": ' . $statusreference . ',
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "APA"
}';
        }


        if ($service == "IBB") {
            $requestBody = '{
    "details": {
        "customerName": "' . $name . '",
        "customerReference": "' . $accountnumber . '",
        "customerType": "PREPAID",
        "thirdPartyCode": "' . $thirdPartyCode . '",
        "amount": ' . $amount . '
    },
    "id": ' . $statusreference . ',
    "paymentCollectorId": "CDL",
    "paymentMethod": "PREPAID",
    "serviceId": "AUB"
}';
        }

        if ($service == "IBP") {
            $requestBody = '{
        "details": {
            "customerReference": "' . $accountnumber . '",
            "customerType": "POSTPAID",
            "customerName": "' . $name . '",
            "thirdPartyCode": "' . $thirdPartyCode . '",
            "amount": ' . $amount . '
        },
        "id": ' . $statusreference . ',
        "paymentCollectorId": "CDL",
        "paymentMethod": "PREPAID",
        "serviceId": "AUA"
}';
        }


        if ($service == "AVB") {
            $requestBody = '{
    "details": {
        "customerReference": "' . $accountnumber . '",
        "amount": ' . $amount . '
    },
    "id": ' . $statusreference . ',
    "paymentCollectorId": "CDL",
    "paymentMethod": "PREPAID",
    "serviceId": "AVB"
}';
        }

        if ($service == "AVC") {
            $requestBody = '{
    "details": {
        "customerReference": "' . $accountnumber . '",
        "amount": ' . $amount . '
    },
    "id": ' . $statusreference . ',
    "paymentCollectorId": "CDL",
    "paymentMethod": "PREPAID",
    "serviceId": "AVC"
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
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response = json_decode($result, true);


        //file_put_contents("dd.php", $result.$code);


        $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];
        $namyname = $myrow[0]['name'];


        if (isset($response['code'])) {

            if (in_array($response['code'], array(
                "EXC00113",
                "EXC00112",
                "EXC00102"))) {
                include "refund.php";
                $refund = new refund("POST");
                $myrefund = $refund->refund_now($parameter);
                if ($myrefund == "200") {

                    return array("status" => "200", "message" => array(
                            "bal" => $myac_ballance,
                            "pft" => $myprofit_bal,
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
        }


        if (!isset($response['details'])) {


            if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::complete_transaction($parameter);
            }
            //            include "refund.php";
            // $refund = new refund("POST");
            // $myrefund = $refund->refund_now($parameter);
            // return array("status" => "100", "message" =>"Transaction Reversed");
            return array("status" => "100", "message" =>
                    "An error occured please contact support with TID $tid");
        }


        if ($response['details']['status'] == "ACCEPTED") {

            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));


            $status = $response['details']['status'];
            $statuscode = "0";
            $venstatusreference = $response['details']['exchangeReference'];


            $token = "";
            if (isset($response['details']['token'])) {
                $token = $response['details']['token'];
            }

            if (isset($response['details']['standardTokenValue'])) {
                $token = $response['details']['standardTokenValue'];
            }

            if (isset($response['details']['externalReference'])) {
                $token = $response['details']['externalReference'];
            }


            if (isset($response['details']['creditToken'])) {
                $token = $response['details']['creditToken'];
            }

            $units = "";
            if (isset($response['details']['tokenUnit'])) {
                $units = $response['details']['tokenUnit'];
            } //

            if (isset($response['details']['power'])) {
                $units = $response['details']['power'];
            } //

            if (isset($response['details']['units'])) {
                $units = $response['details']['units'];
            }
            if (isset($response['details']['standardTokenUnits'])) {
                $units = $response['details']['standardTokenUnits'];
            }
            if (isset($response['details']['amountOfPower'])) {
                $units = $response['details']['amountOfPower'];
            }

            if (!isset($parameter['sms'])) {
                if (!empty($token)) {

                    $message = "Token:" . $token . "\r\nAmount:$amount \r\nUnits:" . $units . "\r\nInvoice Number:" .
                        $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                    self::curlit($phone, $message);


                } else {
                    self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                        "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
                }
            }


            if (!empty($token)) {
                $response['Token'] = $token;
                $response['details']['Token'] = $token;

                $response['Units'] = $units;
                $response['details']['Units'] = $units;

                $totalpay = $amount + 100;
                $response = '{"VendorReference":"' . $statusreference . '","Reference":"' . $venstatusreference .
                    '","MeterNumber":"' . $accountnumber . '","Amount":' . $totalpay .
                    ',"ResponseTime":"' . date("Y-m-d H:i:s A") .
                    '","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":"' . $token .
                    '","FreeUnits":0,"ReceiptNumber":"7' . $venstatusreference .
                    '","PurchasedUnits":' . $units .
                    ',"DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"' .
                    $name . '","Address":"' . $address .
                    '","Tariff":"-","TariffRate":"-","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":"' .
                    $token . '","service_charge":"N100","Total_amount":"N' . $totalpay . '"}';
                $response = json_decode($response, true);
            }
            //olisa
            $response['Agent_name'] = $namyname;
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $venstatusreference,
                1,
                json_encode($response),
                $tid));


            self::que_rechargepropay_mail($tid, $email, "success");

            $response = self::array_flatten($response);


            if (isset($response['details'])) {
                $temarray = $response['details'];
            } else {
                $temarray = $response;
            }

            if (!empty($temarray)) {
                if (count($temarray) > 0) {
                    foreach (self::myarray() as $a) {

                        if (array_key_exists($a, $temarray)) {
                            unset($temarray[$a]);
                        }
                    }
                }
            }

            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $temarray));
        } else {
            if (!isset($response['details']['errorMessage'])) {
                $response['details']['errorMessage'] = "null";
            }
            if (!isset($response['details']['errorCode'])) {
                $response['details']['errorCode'] = "null";
            }
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
                return array("status" => "200", "message" => array(
                        "bal" => $myac_ballance,
                        "pft" => $myprofit_bal,
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
                // return array("status" => "100", "message" => array(
                //      "status" => "Failed",
                //      "TransactionID" => $tid,
                //     "details" => $response['details']));
        }


    }


}
?>