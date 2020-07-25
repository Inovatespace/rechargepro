<?php
class bank_transfer extends Api
{
    //KEDCO 815.30
    public function __construct($method)
    {
        $this->proccess_count = 1;

        $this->transactionfee = 35;
        $this->wave_fee = 15;

        $this->apiUser = "v3Rt15Tec8N01ogL3s";
        $this->apiPass = "Ct4D83jRnU1WLQ3y";
        $this->DES = "GQ5MBX0FV83OAH6SE91IP27Z";

    }

    public function bank_list($parameter)
    {
        $return = array();
        $row = self::db_query("SELECT setting_value FROM settings WHERE setting_key = 'bank_code'",
            array());
        $bankcodes = $row[0]['setting_value'];
        $bankcodes = json_decode($bankcodes, true);

        foreach ($bankcodes as $key => $val) {
            $return[$key] = $val;
        }

        asort($return);

        return $return;
    }


    public function auth_transfer($parameter)
    {


        if (!isset($parameter['account'])) {
            return array("status" => "100", "message" => "Invalid Account");
        }


        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        if (!isset($parameter['narration'])) {
            return array("status" => "100", "message" => "Narration is Compulsory");
        }

        if (!isset($parameter['bankcode'])) {
            return array("status" => "100", "message" => "Invalid Bank");
        }

        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid Private Key");
        }


        $account = urldecode($parameter['account']);
        $amount = self::cleandigit(urldecode($parameter['amount']));
        $narration = self::cleandigit(urldecode($parameter['narration']));
        $bankcode = self::cleandigit(urldecode($parameter['bankcode']));


        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        if ($amount < 50) {
            return array("status" => "100", "message" => "Minimum Transfer Allowed, 100.00");
        }


//get transaction fee
$row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status,percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",array("FUN"));
        $rechargepro_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status'];
        $percentage = $row[0]['percentage'];
        $this->transactionfee = $percentage;

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

        


        $banklist = self::bank_list($parameter);
        if (!isset($banklist[$bankcode])) {
            return array("status" => "100", "message" => "Invalid Bank");
        }


        #LASER
        $rechargeproid = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid,transfer_activation FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $transfer_activation = $row[0]['transfer_activation'];
            if ($transfer_activation == 0) {
                return array("status" => "100", "message" =>
                        "Account not Eligible, Please contact your account officer or Support Department");
            }
            //check auth
            
            
         $rowb = self::db_query("SELECT percentage FROM rechargepro_services_agent WHERE services_key = ? AND rechargeproid = ? LIMIT 1",
            array("FUN", $rechargeproid));
        if (!empty($rowb[0]['percentage'])){
            $percentage = $rowb[0]['percentage'];
            $this->transactionfee = $percentage;
            ///$bill_rechargeprofull_percentage = $rowb[0]['bill_rechargeprofull_percentage'];
        }
        
        }


        $data = '<ValidationRequest><DestinationCode>' . $bankcode .
            '</DestinationCode><AccountNumber>' . $account .
            '</AccountNumber></ValidationRequest>';
        $ciphertext = openssl_encrypt($data, "des-ede3", $this->DES);
        $hash = hash('sha512', $ciphertext . $this->apiPass);


        $post_string = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:teas="http://teasy.com">
 <soap:Header/>
 <soap:Body>
 <teas:ne>
 <teas:apiUser>' . $this->apiUser . '</teas:apiUser>
<teas:request>' . $ciphertext . '</teas:request>
<teas:hash>' . $hash . '</teas:hash>
 </teas:ne>
 </soap:Body>
</soap:Envelope>';

        $header = array(
            "Connection: Keep-Alive",
            "Keep-Alive: 300",
            "Content-type:text/xml;charset=\"utf-8\"",
            "Accept:application/xml",
            "Cache-Control:no-cache",
            "Pragma:no-cache",
            "SOAPAction:https://teasypay.ng/axis2/services/NIPProxy2",
            "Content-length:" . strlen($post_string));
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL,
            "https://teasypay.ng/axis2/services/NIPProxy2?wsdl");
            curl_setopt($soap_do, CURLOPT_NOSIGNAL, 1);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($soap_do, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);
        // curl_close($soap_do);
        
       

        if ($code != 200) {
            return array("status" => "100", "message" => "Network error try again1");
        }


        $result = str_replace(array(
            "soapenv:",
            ":soapenv",
            "ax213:",
            ":ax213",
            "ax212:",
            ":ax212",
            "ns:",
            ":ns",
            "xsi:",
            ":xsi",
            "ax25:",
            ":ax25"), array(
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            ""), $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        if (!isset($array["Body"])) {
            return array("status" => "100", "message" => "Network error try again2");
        }

        if (!isset($array["Body"]["neResponse"]["return"])) {
            return array("status" => "100", "message" => "Network error try again3");
        }


        if (!isset($array["Body"]["neResponse"]["return"]["data"])) {
            return array("status" => "100", "message" => "Network error try again4");
        }


        $resonsecode = $array["Body"]["neResponse"]["return"]["code"];
        $data = $array["Body"]["neResponse"]["return"]["data"];


        if (is_array($data)) {
            return array("status" => "100", "message" =>
                    "Bank Service not available, try again");
        }

        $decrypted = openssl_decrypt($data, "des-ede3", $this->DES);


$decrypted = str_replace(array(
            "<?xml version='1.0' encoding='UTF-8'?>",
            '<?xml version="1.0" encoding="UTF-8" ?>'), array(
            "",
            "",
            ), $decrypted);
            
          //  file_put_contents("test.php",$decrypted);
        $xml = simplexml_load_string($decrypted);
        $json = json_encode($xml);
         
        $array = json_decode($json, true);


        $responsecode = $array['ResponseCode'];

        if ($responsecode != "00") {
            return array("status" => "100", "message" => "Invalid Account Number");
        }


        if (is_array($array['KYCLevel'])) {
            if (empty($array['KYCLevel'])) {
                $array['KYCLevel'] = "";
            }

            if (isset($array['KYCLevel'][0])) {
                $array['KYCLevel'] = $array['KYCLevel'][0];
            }
        }

        $business_district = $array['KYCLevel'] . "_" . $array['SessionID'];
        $channelcoe = $array['ChannelCode'];
        $name = $array['AccountName'];
        $phcn_unique = $array['BankVerificationNumber'];


if(is_array($phcn_unique)){
   // file_put_contents("ddd.txt",json_encode($phcn_unique));
    $phcn_unique = implode("",$phcn_unique);
}


        $narration = $narration;

        if (empty($name)) {
            return array("status" => "100", "message" => "Invalid Account Number");
        }


        #LASER
        $rechargeproid = "0";
        $rechargeprorole = 4;
        $totalamount = $amount + $this->transactionfee;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid,rechargeprorole,service_charge,is_service_charge FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $is_service_charge = $row[0]['is_service_charge'];

            if ($amount > 5000) {
                $service_charge = ceil($amount / 5000) * 200;
            } else {
                $service_charge = 200;
            }


            if ($rechargeprorole < 4) {

                if ($is_service_charge == 1) {
                    //$totalamount = $amount + $service_charge;
                    //$myservice_charge = $service_charge;
                    //$this->transactionfee = $service_charge;
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
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (business_district,phcn_unique,service_charge,name,rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,thirdPartycode,address) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $business_district,
            $phcn_unique,
            $myservice_charge,
            $name,
            $rechargeproid,
            $ip,
            "BANK TRANSFER",
            "BANK TRANSFER",
            $account,
            $amount,
            $bankcode,
            $narration));


        return array("status" => "200", "message" => array(
                "ac" => $account,
                "name" => $name,
                "amount" => $amount,
                "totalamount" => $totalamount,
                "tfee" => $this->transactionfee,
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

        $tid = $parameter['tid'];

        $tid = urldecode($parameter['tid']);

        $row = self::db_query("SELECT rechargeproid,rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $rechargeproid = $row[0]['rechargeproid'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['rechargepro_subservice'];
        $account = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $result = $row[0]['rechargepro_print'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        
        
          $banklist = self::bank_list($parameter);
        if (!isset($banklist[$thirdPartyCode])) {
            return array("status" => "100", "message" => "Invalid Bank");
        }
        
         $bankname = $banklist[$thirdPartyCode];



//get transaction fee
$rowv = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status,percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",array("FUN"));
        $rechargepro_service = $rowv[0]['service_name'];
        $minimumsales_amount = $rowv[0]['minimumsales_amount'];
        $maximumsales_amount = $rowv[0]['maximumsales_amount'];
        $status = $rowv[0]['status'];
        $percentage = $rowv[0]['percentage'];

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

        $this->transactionfee = $percentage;
        
        
                    $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($rechargeproid));
            $myac_ballance = $myrow[0]['ac_ballance'];
            $myprofit_bal = $myrow[0]['profit_bal'];
            $namyname = $myrow[0]['name'];

        if ($rechargepro_status_code == 1) {


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


        if ($row[0]['rechargepro_status'] != "PAID") {

            if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }

            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT profit_bal,ac_ballance, rechargeproid, profile_creator, rechargepro_cordinator, rechargeprorole, transfer_activation, is_service_charge FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $mainacbal = $ac_ballance;
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            $transfer_activation = $row[0]['transfer_activation'];
            $is_service_charge = $row[0]['is_service_charge'];

            if ($transfer_activation == 0) {
                return array("status" => "100", "message" =>
                        "Account not Eligible, Please contact your account officer or Support Department");
            }


            if ($amount > 5000) {
                $service_charge = ceil($amount / 5000) * 200;
            } else {
                $service_charge = 200;
            }


            $myservice_charge = 0;
            if ($rechargeprorole < 4) {
                if ($is_service_charge == 1) {
                    //$myservice_charge = $service_charge;
                }
            }


            if ($channel != 1) {
                $ac_ballance = $profit_bal;
            }
            
            
         $rowb = self::db_query("SELECT percentage FROM rechargepro_services_agent WHERE services_key = ? AND rechargeproid = ? LIMIT 1",
            array("FUN", $rechargeproid));
        if (!empty($rowb[0]['percentage'])){
            $percentage = $rowb[0]['percentage'];
            $this->transactionfee = $percentage;
            ///$bill_rechargeprofull_percentage = $rowb[0]['bill_rechargeprofull_percentage'];
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


            $deductamount = $amount + $this->transactionfee;

            if ($ac_ballance < $deductamount) {
                return array("status" => "100", "message" => "Insufficient Fund");
            }


            $newballance = $ac_ballance - $deductamount;


            if ($channel != 1) {
                self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
            } else {
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
            }
            
            self::db_query("UPDATE rechargepro_transaction_log SET bal1=? WHERE transactionid = ? LIMIT 1", array($ac_ballance,$tid));

            $rechargeproprofit = $this->transactionfee - $this->wave_fee;

            self::db_query("UPDATE rechargepro_transaction_log SET service_charge=?, cordinator_id =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=?,rechargeproprofit =?, reb = ? WHERE transactionid = ? LIMIT 1",
                array(
                $myservice_charge,
                $rechargepro_cordinator,
                "PAID",
                $profile_creator,
                $rechargeproid,
                2,
                $rechargeproprofit,
                $this->transactionfee,
                $tid));
        }


            $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                '","Account Name":"' . $name . '","Narration":"' . $address .
                '","Reference Number":"CONTACT ADMIN","Transfer Amount":"' . $amount .
                '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0","Bank":"'.$bankname.'","Agent_name":"'.$namyname.'"}}';


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                "COMPLETED",
                "1",
                "MANUAL",
                1,
                $result,
                $tid));
                
                if($account == "3093153839"){// 3500 - sent 30 september = calli sent 29th
                return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => json_decode($result, true)));  
                }
                
                

        $ep = explode("_", $district);


        $data = '<FTRequest>
<NameEnquiryReference>' . $ep[1] . '</NameEnquiryReference>
<SourceAccountNumber>2348183874966</SourceAccountNumber>
<SourceAccountPIN>3885</SourceAccountPIN>
<SourceAgentShortCode>74966</SourceAgentShortCode>
<SourceVerificationCode></SourceVerificationCode>
<TargetAccountName>' . $name . '</TargetAccountName>
<TargetAccountNumber>' . $account . '</TargetAccountNumber>
<TargetVerificationCode>' . $unique . '</TargetVerificationCode>
<TargetKYCLevel>' . $ep[0] . '</TargetKYCLevel>
<DestinationCode>' . $thirdPartyCode . '</DestinationCode>
<Narration>' . $address . '</Narration>
<Amount>' . $amount . '</Amount>
</FTRequest>';
        $ciphertext = openssl_encrypt($data, "des-ede3", $this->DES);
        $hash = hash('sha512', $ciphertext . $this->apiPass);


        $post_string = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:teas="http://teasy.com">
 <soap:Header/>
 <soap:Body>
 <teas:ft>
 <teas:apiUser>' . $this->apiUser . '</teas:apiUser>
<teas:request>' . $ciphertext . '</teas:request>
<teas:hash>' . $hash . '</teas:hash>
 </teas:ft>
 </soap:Body>
</soap:Envelope>';

        $header = array(
            "Connection: Keep-Alive",
            "Keep-Alive: 300",
            "Content-type:text/xml;charset=\"utf-8\"",
            "Accept:application/xml",
            "Cache-Control:no-cache",
            "Pragma:no-cache",
            "SOAPAction:https://teasypay.ng/axis2/services/NIPProxy2",
            "Content-length:" . strlen($post_string));
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL,
            "https://teasypay.ng/axis2/services/NIPProxy2?wsdl");
        curl_setopt($soap_do, CURLOPT_NOSIGNAL, 1);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);
        // curl_close($soap_do);

        if ($code != 200) {

            if ($this->proccess_count == 0) {
                //$this->proccess_count = 1;
                //return self::complete_transaction($parameter);
            }

            $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                '","Account Name":"' . $name . '","Narration":"' . $address .
                '","Reference Number":"MANUAL","Transfer Amount":"' . $amount .
                '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0","Bank":"'.$bankname.'","Agent_name":"'.$namyname.'"}}';


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                "COMPLETED",
                "1",
                "MANUAL",
                1,
                $result,
                $tid));

            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => json_decode($result, true)));
        }


        $result = str_replace(array(
            "soapenv:",
            ":soapenv",
            "ax212:",
            ":ax212",
            "ax213:",
            ":ax213",
            "ns:",
            ":ns",
            "xsi:",
            ":xsi",
            "ax25:",
            ":ax25"), array(
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            "",
            ""), $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json, true);


        if (!isset($array["Body"])) {
            return array("status" => "100", "message" => "Network error try again");
        }

        if (!isset($array["Body"]["ftResponse"]["return"])) {
            return array("status" => "100", "message" => "Network error try again");
        }


        if (!isset($array["Body"]["ftResponse"]["return"]["data"])) {
            return array("status" => "100", "message" => "Network error try again");
        }


        $resonsecode = $array["Body"]["ftResponse"]["return"]["code"];
        $data = $array["Body"]["ftResponse"]["return"]["data"];


        if (is_array($data)) {
            $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                '","Account Name":"' . $name . '","Narration":"' . $address .
                '","Reference Number":"MANUAL","Transfer Amount":"' . $amount .
                '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0","Bank":"'.$bankname.'","Agent_name":"'.$namyname.'"}}';


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                "COMPLETED",
                "1",
                "MANUAL",
                1,
                $result,
                $tid));
            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => json_decode($result, true)));
            //return array("status" => "100", "message" => "Bank Service not available, try again");
        }


        $decrypted = openssl_decrypt($data, "des-ede3", $this->DES);
        $decrypted = str_replace(array(
            "<?xml version='1.0' encoding='UTF-8'?>",
            '<?xml version="1.0" encoding="UTF-8" ?>'), array(
            "",
            "",
            ), $decrypted);

        $xml = simplexml_load_string($decrypted);
        $json = json_encode($xml);
        $array = json_decode($json, true);


        $responsecode = $array['ResponseCode'];

        if (in_array($responsecode, array(
            "06",
            "05",
            "09",
            "34",
            "96",
            "97",
            "63",
            "68"))) {
            $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                '","Account Name":"' . $name . '","Narration":"' . $address .
                '","Reference Number":"MANUAL","Transfer Amount":"' . $amount .
                '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                "COMPLETED",
                "1",
                "MANUAL",
                1,
                $result,
                $tid));
            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => json_decode($result, true)));
            //return array("status" => "300", "message" => "Transaction Pending");
        }


        if (in_array($responsecode, array(
            "03",
            "07",
            "08",
            "12",
            "13",
            "14",
            "15",
            "16",
            "17",
            "18",
            "21",
            "25",
            "30",
            "35",
            "51",
            "57",
            "58",
            "61",
            "65",
            "91",
            "92"))) {


            $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                '","Account Name":"' . $name . '","Narration":"' . $address .
                '","Reference Number":"MANUAL","Transfer Amount":"' . $amount .
                '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                "COMPLETED",
                "1",
                "MANUAL",
                1,
                $result,
                $tid));

            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => json_decode($result, true)));
            //return array("status" => "100", "message" => "Transaction Reversed");
        }


        if (in_array($responsecode, array(
            "26",
            "00",
            "94"))) {

            $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($rechargeproid));
            $myac_ballance = $myrow[0]['ac_ballance'];
            $myprofit_bal = $myrow[0]['profit_bal'];
            $namyname = $myrow[0]['name'];


            $status = "ACCEPTED";
            $statuscode = "0";
            $statusreference = $array['PaymentReference'];


            if ($amount > 30000) {
               // $this->transactionfee = 40;
            }


            $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                '","Account Name":"' . $name . '","Narration":"' . $address .
                '","Reference Number":"' . $statusreference . '","Transfer Amount":"' . $amount .
                '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $result,
                $tid));

            self::que_rechargepropay_mail($tid, $email, "success");


            $response = self::array_flatten(json_decode($result, true));

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

            $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                '","Account Name":"' . $name . '","Narration":"' . $address .
                '","Reference Number":"MANUAL","Transfer Amount":"' . $amount .
                '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                "COMPLETED",
                "1",
                "MANUAL",
                1,
                $result,
                $tid));
            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => json_decode($result, true)));
            // return array("status" => "300", "message" => "Transaction Pending");
        }


    }


}
?>