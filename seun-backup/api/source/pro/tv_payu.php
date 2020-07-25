<?php
class tv extends Api
{
    //KEDCO
    public function __construct($method)
    {
        $this->payu_username = "vertis";
        $this->payu_password = "nVfQeKTn4c";
        $this->payu_banquet_url = "https://mcapi-server.herokuapp.com/bouquets";
        $this->payu_lookup_url = "https://mcapi-server.herokuapp.com/Vendor/Lookup";
        $this->payu_payment_url =
            "https://mcapi-server.herokuapp.com/Vendor/SinglePayment";
        $this->payu_verify_url =
            "https://mcapi-server.herokuapp.com/transactions/single/";

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


        if (in_array($service, array("AWA"))) {
            return self::auth_startimes($parameter);
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->payu_lookup_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->payu_username . ":" . $this->payu_password,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => 'xml=<PayUVasRequest Ver="1.0"><MerchantId>' . $this->
                payu_username . '</MerchantId><MerchantReference>' . date("H:i:s") . self::
                RandomString(4, 15) .
                '</MerchantReference><TransactionType>ACCOUNT_LOOKUP</TransactionType><VasId>MCA_ACCOUNT_SQ_NG</VasId><CountryCode>NG</CountryCode><CustomerId>' .
                $accountnumber . '</CustomerId></PayUVasRequest>',
            CURLOPT_HTTPHEADER => array(
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"),
            ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        //curl_close($curl);


        $xml = simplexml_load_string($response);
        $json = json_encode($xml, true);
        $array = json_decode($json, true);


        if (!isset($array['CustomFields'])) {
            return array("status" => "100", "message" =>
                    "Invalid card details or network Error, try again");
        }


        $customerid = "";
        $fname = "";
        $lastname = "";
        for ($i = 0; $i < count($array['CustomFields']['Customfield']); $i++) {

            if ($array['CustomFields']['Customfield'][$i]['@attributes']['Key'] ==
                "BASKET_ID") {
                $customerid = $array['CustomFields']['Customfield'][$i]['@attributes']['Value'];
            }
            if ($array['CustomFields']['Customfield'][$i]['@attributes']['Key'] ==
                "FIRSTNAME") {
                $fname = $array['CustomFields']['Customfield'][$i]['@attributes']['Value'];
            }
            if ($array['CustomFields']['Customfield'][$i]['@attributes']['Key'] == "SURNAME") {
                $lastname = $array['CustomFields']['Customfield'][$i]['@attributes']['Value'];
            }

        }


        $name = $fname . " " . $lastname;


        $number = $customerid;


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
                "amount" => $amount,
                "totalamount" => $totalmount,
                "tfee" => $tfee,
                "number" => $number,
                "tid" => $insertid));
    }


    private function auth_startimes($parameter)
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
        $accountnumber = explode("-", $accountnumber);
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
            CURLOPT_POSTFIELDS =>
                '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.haiwai.sms.star.com" xmlns:con="http://condition.haiwai.model.sms.star.com">
<soapenv:Header>
  <CALLCENTER_USERNAME xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_USERNAME>
  <CALLCENTER_PASSWORD xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_PASSWORD>
 </soapenv:Header>
   <soapenv:Body>
      <ser:querySubscriberInfo>
         <ser:in0>
            <con:payerID>9600</con:payerID>
            <con:payerPwd>0c7eb6021ef8f4dba0a63a2fc03fdf2f</con:payerPwd>
            <con:smartCardCode>' . $accountnumber . '</con:smartCardCode>
            <con:transactionNo>' . date("YmdHis") . rand(0000000, 9999999) .
                '</con:transactionNo>
         </ser:in0>
      </ser:querySubscriberInfo>
   </soapenv:Body>
</soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array(
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "cache-control: no-cache"),
            ));

        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);

        curl_close($curl);


        $result = str_replace(array(
            "soap:",
            ":soap",
            "ser:",
            "con:"), array(
            "",
            "",
            "",
            ""), $result);
        $result = preg_replace("/\p{Han}+/u", '', $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml, true);
        $response = json_decode($json, true);


        if (!isset($response['Body']['querySubscriberInfoResponse']['out']['customerName'])) {
            return array("status" => "100", "message" =>
                    "Invalid card details or network Error, try again");
        }

        $name = $response['Body']['querySubscriberInfoResponse']['out']['customerName'];

        $number = $accountnumber;


        if (empty($name)) {
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
                "amount" => $amount,
                "totalamount" => $totalmount,
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
        $row = self::db_query("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1",
            array($service)); // AND setting_date > ? , $date
        $setting_value = $row[0]['setting_value'];


        if (empty($setting_value)) {
            $row = self::db_query("SELECT service_resuest FROM rechargepro_services WHERE 	services_key = ? LIMIT 1",
                array($service));
            $setting_value = $row[0]['service_resuest'];
        }


        if (!empty($setting_value)) {

            $startimeb = array();
            $j = json_decode($setting_value, true);
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


        return array();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->payu_banquet_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "$this->payu_username:$this->payu_password",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"),
            ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        //curl_close($curl);


        $response = json_decode($response, true);


        if (count($response) < 2) {
            return array("status" => "100", "message" =>
                    "Invalid card details or network Error, try again");
        }


        $dsvt_bouquet = array();
        $gotv_bouquet = array();

        for ($i = 0; $i < count($response); $i++) {

            if (trim($response[$i]['bouquet_category']) == "DSTV") {
                $dsvt_bouquet[] = array(
                    "code" => $response[$i]['product_key'],
                    "invoicePeriods" => "[1,12]",
                    "price" => $response[$i]['amount'],
                    "name" => $response[$i]['bouquet_name'],
                    "description" => " ");
            }


            if (trim($response[$i]['bouquet_category']) == "GOTV") {
                $gotv_bouquet[] = array(
                    "code" => $response[$i]['product_key'],
                    "invoicePeriods" => "[1,12]",
                    "price" => $response[$i]['amount'],
                    "name" => $response[$i]['bouquet_name'],
                    "description" => " ");
            }

        }


        //udate
        $response['details']['items'] = $dsvt_bouquet;
        $setting_date = date("Y-m-d H:i:s");
        self::db_query("UPDATE settings SET setting_value = ?,setting_date = ? WHERE setting_key = 'AQA' LIMIT 1",
            array(json_encode($response['details']), $setting_date));


        //udate
        $response['details']['items'] = $gotv_bouquet;
        $setting_date = date("Y-m-d H:i:s");
        self::db_query("UPDATE settings SET setting_value = ?,setting_date = ? WHERE setting_key = 'AQC' LIMIT 1",
            array(json_encode($response['details']), $setting_date));


        if ($service == "AQA") {
            $response['details']['items'] = $dsvt_bouquet;
        }


        if ($service == "AQC") {
            $response['details']['items'] = $gotv_bouquet;
        }


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
            $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($rechargeproid));
            $myac_ballance = $myrow[0]['ac_ballance'];
            $myprofit_bal = $myrow[0]['profit_bal'];
            $namyname = $myrow[0]['name'];


            $response = json_decode($result, true);
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


        if ($row[0]['amount'] < 1) {
            return array("status" => "100", "message" =>
                    "Payment Not successful please contact support with TID $cartid 2");
        }


        if (in_array($service, array("AWA"))) {
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

            $deduct = 1;
            if (empty($ac_ballance) || ($amount + $tfee) > $ac_ballance) {
                $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $ogaballance = $row[0]['ac_ballance'];
                $ogaautofeed = $row[0]['auto_feed_cahier_account'];
                if ($ogaautofeed == 1 && $rechargeprorole < 4) {

                    if (($amount + $tfee) > $ogaballance) {
                        return array("status" => "100", "message" => "Insufficient Fund");
                    }


                    $deduct = 0;

                    $newballance = $ogaballance - ($amount + $tfee);
                    self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $profile_creator));

                } else {
                    return array("status" => "100", "message" => "Insufficient Balance");
                }

            }


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


        include "promo1.php";


        $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];
        $namyname = $myrow[0]['name'];


        $vedorres = $transaction_date . $tid;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->payu_payment_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->payu_username . ":" . $this->payu_password,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => 'xml=<PayUVasRequest Ver="1.0"><MerchantId>' . $this->
                payu_username . '</MerchantId><MerchantReference>' . $vedorres .
                '</MerchantReference><TransactionType>SINGLE</TransactionType><VasId>MCA_ACCOUNT_SQ_NG</VasId><CountryCode>NG</CountryCode><AmountInCents>' .
                $amount . '</AmountInCents><CustomerId>' . $accountnumber .
                '</CustomerId><CustomFields><Customfield Key="BasketId" Value="' . $address .
                '" /></CustomFields></PayUVasRequest>',
            CURLOPT_HTTPHEADER => array(
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"),
            ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);


        if (in_array($httpcode, array("404", "503"))) {
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


        curl_close($curl);


        $xml = simplexml_load_string($response);
        $json = json_encode($xml, true);
        $response = json_decode($json, true);


        if (isset($response['ResultCode'])) {

            if (in_array($response['ResultCode'], array("12453jh"))) {
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

        //return array("status" => "100", "message" =>$result.$err);

        if (!isset($response['ResultCode'])) {


            if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::tv($parameter);
            }

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


        //{"transactionNumber":9965,"details":{"":"6453258859733574","errorMessage":null,"":null,"utilityName":"Eskom","status":"ACCEPTED"}}

        if ($response['ResultCode'] == "00") {

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
            $response['Agent_name']=$namyname;
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);
            $result = json_encode($response);

            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));


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


            if (!isset($parameter['sms'])) {
                self::que_rechargepropay_mail($tid, $email, "success");
                self::curlit($phone, "Thank you your subscription is activated \r\nInvoice Number:" .
                    $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
            }
            $response = self::array_flatten($response);


            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        } else
            if ($response['ResultCode'] == "1062") {


                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $this->payu_verify_url . $vedorres,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_USERPWD => $this->payu_username . ":" . $this->payu_password,
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "Connection: Keep-Alive",
                        "Keep-Alive: 300",
                        "cache-control: no-cache",
                        "content-type: application/x-www-form-urlencoded"),
                    ));

                $response = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $err = curl_error($curl);
                curl_close($curl);
                $response = json_decode($response, true);


                if (in_array($httpcode, array(
                    "404",
                    "500",
                    "505",
                    "503"))) {

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


                if (!isset($response[0])) {

                    $response['Card Number'] = $accountnumber;
                    $response['Amount Paid'] = $amount;
                    //olisa
                    $response['Agent_name']=$namyname;
                    $response['service_charge'] = "N100";
                    $response['Total_amount'] = "N" . ($amount + 100);
                    $result = json_encode($response);

                    //self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",array($amount, $service));


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

                    if (!isset($parameter['sms'])) {
                        self::que_rechargepropay_mail($tid, $email, "success");
                        self::curlit($phone, "Thank you your subscription is activated\r\nInvoice Number:" .
                            $tid . "_" . $rechargeproid . "\r\nvisit rechargepro.com.ng, For print out");
                    }
                    $response = self::array_flatten($response);

                    return array("status" => "200", "message" => array(
                            "bal" => $myac_ballance,
                            "pft" => $myprofit_bal,
                            "status" => "Accepted",
                            "TransactionID" => $tid,
                            "details" => $response));
                }


                $response = $response[0];


                if ($response['status'] == "-1" || $response['status'] == "1") {

                    $response['Card Number'] = $accountnumber;
                    $response['Amount Paid'] = $amount;
                    //olisa
                    $response['Agent_name']=$namyname;
                    $response['service_charge'] = "N100";
                    $response['Total_amount'] = "N" . ($amount + 100);
                    $result = json_encode($response);

                    //self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",array($amount, $service));


                    $status = "00";
                    $statuscode = "0";
                    $statusreference = "00";

                    self::que_rechargepropay_mail($tid, $email, "success");
                    self::curlit($phone, "Thank you your subscription is activated");


                    self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                        array(
                        $status,
                        $statuscode,
                        $statusreference,
                        1,
                        $result,
                        $tid));


                    return array("status" => "200", "message" => array(
                            "bal" => $myac_ballance,
                            "pft" => $myprofit_bal,
                            "status" => "Accepted",
                            "TransactionID" => $tid,
                            "details" => $response));
                } else {

                    $status = $response['ResultCode'];
                    $statuscode = $response['ResultCode'];
                    $statusreference = $response['ResultCode'];

                    self::que_rechargepropay_mail($tid, $email, $response);

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


                }


            } else {
                $status = $response['ResultCode'];
                $statuscode = $response['ResultCode'];
                $statusreference = $response['ResultCode'];

                self::que_rechargepropay_mail($tid, $email, $response);

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


    public function buy_startimes($parameter)
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
            CURLOPT_POSTFIELDS =>
                '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.haiwai.sms.star.com" xmlns:haiw="http://haiwai.model.sms.star.com">
<soapenv:Header>
  <CALLCENTER_USERNAME xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_USERNAME>
  <CALLCENTER_PASSWORD xmlns="NAMESPACE_STARSMS">StarCallCenter</CALLCENTER_PASSWORD>
 </soapenv:Header>
   <soapenv:Body>
      <ser:customerPay2>
         <ser:in0>
            <haiw:customerCode></haiw:customerCode>
            <haiw:customerName>' . $name . '</haiw:customerName>
            <haiw:customerTel>08172397885</haiw:customerTel>
            <haiw:deviceType></haiw:deviceType>
            <haiw:email></haiw:email>
            <haiw:fee>' . $amount . '</haiw:fee>
            <haiw:payerID>9600</haiw:payerID>
            <haiw:payerPwd>0c7eb6021ef8f4dba0a63a2fc03fdf2f</haiw:payerPwd>
            <haiw:receiptCode></haiw:receiptCode>
            <haiw:smartCardCode>' . $accountnumber . '</haiw:smartCardCode>
            <haiw:transactionNo>' . $vedorres . '</haiw:transactionNo>
            <haiw:transferTime>' . $tdate . '</haiw:transferTime>
         </ser:in0>
      </ser:customerPay2>
   </soapenv:Body>
</soapenv:Envelope>',
            CURLOPT_HTTPHEADER => array(
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "cache-control: no-cache"),
            ));

        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);


        $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];
        $namyname = $myrow[0]['name'];


        if (in_array($httpcode, array("404", "503"))) {
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


        $result = str_replace(array(
            "soap:",
            ":soap",
            "ser:",
            "con:"), array(
            "",
            "",
            "",
            ""), $result);
        $result = preg_replace("/\p{Han}+/u", '', $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml, true);
        $response = json_decode($json, true);


        if (!isset($response['Body']['customerPay2Response']['out']['returnCode'])) {

            if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::buy_startimes($parameter);
            }

            return array("status" => "100", "message" =>
                    "An error occured please contact support");
        }
        ;


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
            $response['Agent_name']=$namyname;
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);
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


            self::que_rechargepropay_mail($tid, $email, "success");
            self::curlit($phone, "Thank you your subscription is activated\r\nInvoice Number:" .
                $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");


            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        } else {
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
//{"items":[{"code":"ACSSE36","invoicePeriods":"[1,12]","price":2000,"name":"Access","description":" "},{"code":"ACSSE37","invoicePeriods":"[1,12]","price":7400,"name":"Access + Asia","description":" "},{"code":"ACSSE38","invoicePeriods":"[1,12]","price":4200,"name":"Access + HD\/ExtraView","description":" "},{"code":"ASIAE36","invoicePeriods":"[1,12]","price":5400,"name":"Asia Standalone","description":" "},{"code":"ASIAE37","invoicePeriods":"[1,12]","price":7600,"name":"Asian + HD\/ExtraView","description":" "},{"code":"COFAME36","invoicePeriods":"[1,12]","price":4000,"name":"Family","description":" "},{"code":"COFAME37","invoicePeriods":"[1,12]","price":9400,"name":"Family + Asia","description":" "},{"code":"COFAME38","invoicePeriods":"[1,12]","price":6200,"name":"Family + HD\/ExtraView","description":" "},{"code":"COMPE36","invoicePeriods":"[1,12]","price":6800,"name":"Compact","description":" "},{"code":"COMPE37","invoicePeriods":"[1,12]","price":9000,"name":"Compact + HD\/ExtraView","description":" "},{"code":"COMPE38","invoicePeriods":"[1,12]","price":8270,"name":"Compact + French Touch","description":" "},{"code":"COMPE39","invoicePeriods":"[1,12]","price":10470,"name":"Compact + French Touch + HD\/ExtraView","description":" "},{"code":"COMPLE36","invoicePeriods":"[1,12]","price":10650,"name":"Compact Plus","description":" "},{"code":"COMPLE37","invoicePeriods":"[1,12]","price":10650,"name":"Compact Plus","description":" "},{"code":"COMPLE38","invoicePeriods":"[1,12]","price":16050,"name":"Compact Plus + Asia","description":" "},{"code":"COMPLE39","invoicePeriods":"[1,12]","price":12850,"name":"Compact Plus + HD\/ExtraView","description":" "},{"code":"COMPLE40","invoicePeriods":"[1,12]","price":12850,"name":"Compact Plus + HD\/ExtraView","description":" "},{"code":"PRWE36","invoicePeriods":"[1,12]","price":15800,"name":"Premium","description":" "},{"code":"PRWE37","invoicePeriods":"[1,12]","price":18000,"name":"Premium + HD\/ExtraView","description":" "},{"code":"PRWE38","invoicePeriods":"[1,12]","price":17270,"name":"Premium + French Touch","description":" "},{"code":"PRWE39","invoicePeriods":"[1,12]","price":19470,"name":"Premium + French Touch + HD\/ExtraView","description":" "},{"code":"PRWASIE36","invoicePeriods":"[1,12]","price":17700,"name":"Premium Asia","description":" "},{"code":"PRWFRNSE36","invoicePeriods":"[1,12]","price":22200,"name":"Premium French Bonus","description":" "},{"code":"PRWFRNSE37","invoicePeriods":"[1,12]","price":24400,"name":"Premium French Bonus + HD\/Extraview","description":" "},{"code":"PRWASIE37","invoicePeriods":"[1,12]","price":19900,"name":"Premium Asia + HD\/ExtraView","description":" "},{"code":"ASIADDE36","invoicePeriods":"[1,12]","price":5400,"name":"Asia Add-on","description":" "},{"code":"FRN11E36","invoicePeriods":"[1,12]","price":6360,"name":"French Plus","description":" "},{"code":"FRN7E36","invoicePeriods":"[1,12]","price":1470,"name":"French Touch","description":" "},{"code":"FRN11W7","invoicePeriods":"[1,12]","price":3180,"name":"French 11","description":" "},{"code":"FRN15E36","invoicePeriods":"[1,12]","price":2200,"name":"HDPVR Access\/Extraview","description":" "},{"code":"BO","invoicePeriods":"[1,12]","price":400,"name":"Box Office","description":" "}]}

?>