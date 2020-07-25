<?php
class glo extends Api
{

    public function __construct($method)
    {
        $this->proccess_count = 0;
    }


    public function vend_airtime($parameter)
    {


        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Amount is Missing");
        }

        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
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


        $accountnumber = $phone;


        //check service


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

        if (empty($quickpay_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        if ($amount < 1) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        #LASER
        $quickpayid = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT quickpayid FROM quickpay_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $quickpayid = $row[0]['quickpayid'];

            //invalid key
            if (empty($quickpayid)) {
                if ($parameter['private_key'] != "web") {
                    return array("status" => "100", "message" => "Invalid Key");
                }else{
                  $quickpayid = "0";  
                }
            }
        }
        
        
        $data = array("private_key"=>self::config("rechargekey"),"token"=>self::config("rechargetoken"),"service"=>$service,"mobile"=>$phone,"amount"=>$amount,"accountnumber"=>$phone,"bankcode"=>"","bundle"=>"");
       $rechargepost =  self::file_get_b($data, self::config("rechargeurl")."initiate_transaction.json");
       
        
        
        if(!isset($rechargepost['status'])){
return array("status" => "100", "message" =>"Network Error Try again");
}

        if($rechargepost['status'] == "100"){
return array("status" => "100", "message" =>$rechargepost['message']);
}

$rechargetid =  $rechargepost['message']['tid'];
        

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO quickpay_transaction_log (thirdPartycode,quickpayid,ip,quickpay_service,quickpay_subservice,account_meter,amount,phone,email) VALUES (?,?,?,?,?,?,?,?,?)",
            array(
            $rechargetid,
            $quickpayid,
            $ip,
            $quickpay_service,
            $service,
            $accountnumber,
            $amount,
            $phone,
            $email));


        return array("status" => "200", "message" => array(
                "name" => $accountnumber,
                "amount" => $amount,
                "totalamount" => $amount,
                "details" => $accountnumber,
                "tfee" => 0,
                "amount" => $amount,
                "tid" => $insertid));

    }

    public function vend_data($parameter)
    {

        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Amount is required");
        }
        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        if (!isset($parameter['bundle'])) {
            return array("status" => "100", "message" => "Bundle is Missing");
        }


        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid Key");
        }
        $bundle = urldecode(trim($parameter['bundle']));
        $amount = urldecode(trim($parameter['amount']));
        $phone = urldecode(trim($parameter['mobile']));
        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }
        $service = urldecode($parameter['service']);
        $accountnumber = $phone;


        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM quickpay_services WHERE services_key = ? LIMIT 1",
            array($service));
        $quickpay_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status'];

        if ($status == 0) {
            return array("status" => "100", "message" =>
                    "This service is curently Not Active");
        }

        if ($minimumsales_amount > $amount) {
            return array("status" => "100", "message" => "Minimum Amount Alloweddddd: $minimumsales_amount {$amount}");
        }

        if ($amount > $maximumsales_amount) {
            return array("status" => "100", "message" => "Maximum Amount Allowed: $maximumsales_amount");
        }

        if (empty($quickpay_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        if ($amount < 10) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        #LASER
        $quickpayid = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT quickpayid FROM quickpay_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $quickpayid = $row[0]['quickpayid'];

            //invalid key
            if (empty($quickpayid)) {
                if ($parameter['private_key'] != "web") {
                    return array("status" => "100", "message" => "Invalid Key");
                } else{
                  $quickpayid = "0";  
                }
            }
        }
        
        
        $data = array("private_key"=>self::config("rechargekey"),"token"=>self::config("rechargetoken"),"service"=>$service,"mobile"=>$phone,"amount"=>$amount,"accountnumber"=>$phone,"bankcode"=>"","bundle"=>"");
       $rechargepost =  self::file_get_b($data, self::config("rechargeurl")."initiate_transaction.json");
       
               
        if(!isset($rechargepost['status'])){
return array("status" => "100", "message" =>"Network Error Try again");
}

        if($rechargepost['status'] == "100"){
return array("status" => "100", "message" =>$rechargepost['message']);
}

$rechargetid =  $rechargepost['message']['tid'];
        

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO quickpay_transaction_log (thirdPartycode,quickpayid,ip,business_district,quickpay_service,quickpay_subservice,account_meter,amount,phone,email) VALUES (?,?,?,?,?,?,?,?,?,?)",
            array(
            $rechargetid,
            $quickpayid,
            $ip,
            $bundle,
            $quickpay_service,
            $service,
            $accountnumber,
            $amount,
            $phone,
            $email));


        return array("status" => "200", "message" => array(
                "name" => $accountnumber . " " . $bundle,
                "amount" => $amount,
                "totalamount" => $amount,
                "details" => $accountnumber,
                "amount" => $amount,
                "tfee" => 0,
                "tid" => $insertid));
    }

    public function fix_phone($mobile)
    {
        $mobile = "234" . substr($mobile, 1);
        return $mobile;
    }

    public function buy_glo($parameter)
    {

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

        if ($quickpay_status_code == 1) {
            $myrow = self::db_query("SELECT ac_ballance,profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                array($quickpayid));
            $myac_ballance = $myrow[0]['ac_ballance'];
            $myprofit_bal = $myrow[0]['profit_bal'];


            $response = json_decode($result, true);
            
            
            $response = self::array_flatten($response['details']);

            $temarray = $response;
            foreach (self::myarray() as $a) {
                if (array_key_exists($a, $temarray)) {
                    unset($temarray[$a]);
                }
            }
            
            
            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $temarray));
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
            $row = self::db_query("SELECT ac_ballance,profit_bal, quickpayid, profile_creator, quickpay_cordinator, quickpayrole FROM quickpay_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $mainacbal = $ac_ballance;
            $quickpayid = $row[0]['quickpayid'];
            $profile_creator = $row[0]['profile_creator'];
            $quickpayrole = $row[0]['quickpayrole'];
            $quickpay_cordinator = $row[0]['quickpay_cordinator'];
            $profit_bal = $row[0]['profit_bal'];


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
            $parameter['processamount'] = $amount;
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


            $newballance = $ac_ballance - $amount;

            if ($deduct == 1) {
                if ($channel != 1) {
                    self::db_query("UPDATE quickpay_account SET profit_bal = ? WHERE quickpayid = ? LIMIT 1",
                        array($newballance, $quickpayid));
                } else {
                    self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                        array($newballance, $quickpayid));
                }
            }


            self::db_query("UPDATE quickpay_transaction_log SET cordinator_id =?, quickpay_status = ?,agent_id=?,quickpayid=?,payment_method=? WHERE transactionid = ? LIMIT 1",
                array(
                $quickpay_cordinator,
                "PAID",
                $profile_creator,
                $quickpayid,
                2,
                $tid));

            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }


        $row = self::db_query("SELECT service_resuest FROM quickpay_services WHERE services_key = ? LIMIT 1",
            array($service));
        $service_resuest = $row[0]['service_resuest'];


        include "promo1.php";
        
        
        
        $data = array("private_key"=>self::config("rechargekey"),"token"=>self::config("rechargetoken"),"tid"=>$thirdPartyCode);
       $response =  self::file_get_b($data, self::config("rechargeurl")."complete_transaction.json");
       
       


        $myrow = self::db_query("SELECT ac_ballance,profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
            array($quickpayid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];


        if (!isset($response['status'])) {

            if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::buy_glo($parameter);
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


        if ($response['status'] == "200") {

            self::db_query("UPDATE quickpay_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));


            $status = $response['status'];
            $statuscode = "0";
            $statusreference = "COMPLETED";

            $response['message']['amount'] = $amount;
            $response['message']['Phone'] = $accountnumber;

            //self::que_quickpay_mail($tid, $email, "success");
            //self::que_quickpay_sms($tid);
            self::db_query("UPDATE quickpay_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,quickpay_status_code =?, quickpay_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response['message']),
                $tid));
                
                
            $response = self::array_flatten($response['message']);

            $temarray = $response;
            foreach (self::myarray() as $a) {
                if (array_key_exists($a, $temarray)) {
                    unset($temarray[$a]);
                }
            }


            return array("status" => "200", "message" => array(
                    "bal" => $myac_ballance,
                    "pft" => $myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $temarray));

        } else
            if ($response['status'] == "300") {
          
             return array("status" => "300", "message" => "Transaction Pending");
                                        
            } else {

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

    public function sell($sequence)
    {
        return self::vendglo($sequence['s'], $sequence['n'], 20);
    }


    function vendglo($sequence, $destMsisdn, $amount = 0, $tariffTypeId = 1, $dataplan =
        "")
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
            <clientReference>' . $sequence . '</clientReference>
            <clientRequestTimeout>500</clientRequestTimeout>
            <!--Optional:-->
            <initiatorPrincipalId>
               <!--reseller id for parent:-->
               <id>DIST23480153224085</id>
               <!--Optional:-->
               <type>RESELLERUSER</type>
               <!--Optional:-->
               <userId>VERTISWEB</userId>
            </initiatorPrincipalId>
            <!--password for parent:-->
            <password>Olisa@vty</password>
         </context>
         <!--Optional:-->
         <senderPrincipalId>
            <!--reseleer id for parent:-->
            <id>DIST23480153224085</id>
            <!--Optional:-->
            <type>RESELLERUSER</type>
            <!--user for the reseller:-->
            <userId>VERTISWEB</userId>
         </senderPrincipalId>
         <!--Optional:-->
         <topupPrincipalId>
            <!--user to be topup:-->
            <id>' . $destMsisdn . '</id>
            <!--Optional:-->
            <type>SUBSCRIBERMSISDN</type>
            <!--Optional:-->
            <userId>VERTISWEB</userId>
         </topupPrincipalId>
         <!--Optional:-->
         <senderAccountSpecifier>
            <!--reselleer id for parent:-->
            <accountId>DIST23480153224085</accountId>
            <!--Optional:-->
            <accountTypeId>RESELLER</accountTypeId>
         </senderAccountSpecifier>
         <!--Optional:-->
         <topupAccountSpecifier>
            <!--user to be toped up:-->
            <accountId>' . $destMsisdn . '</accountId>
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
            <value>' . $amount . '</value>
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
          <clientComment>DIST23480153224085</clientComment>
          <!--Optional:-->
          <clientId>ERS</clientId>
          <!--Optional:-->
          <prepareOnly>false</prepareOnly>
          <!--Optional:-->
          <clientReference>' . $sequence . '</clientReference>
          <clientRequestTimeout>500</clientRequestTimeout>
          <!--Optional:-->
          <initiatorPrincipalId>
             <!--Optional:-->
             <id>DIST23480153224085</id>
             <!--Optional:-->
             <type>RESELLERUSER</type>
             <!--Optional:-->
             <userId>VERTISWEB</userId>
          </initiatorPrincipalId>
          <!--Optional:-->
          <password>Olisa@vty</password>
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
          <id>DIST23480153224085</id>
          <!--Optional:-->
          <type>RESELLERUSER</type>
          <!--Optional:-->
          <userId>VERTISWEB</userId>
       </senderPrincipalId>
       <!--Optional:-->
       <topupPrincipalId>
          <!--Optional:-->
          <id>' . $destMsisdn . '</id>
          <!--Optional:-->
          <type>SUBSCRIBERMSISDN</type>
          <!--Optional:-->
          <userId>VERTISWEB</userId>
       </topupPrincipalId>
       <!--Optional:-->
       <senderAccountSpecifier>
          <!--Optional:-->
          <accountId>DIST23480153224085</accountId>
          <!--Optional:-->
          <accountTypeId>RESELLER</accountTypeId>
       </senderAccountSpecifier>
       <!--Optional:-->
       <topupAccountSpecifier>
          <!--Optional:-->
          <accountId>' . $destMsisdn . '</accountId>
          <!--Optional:-->
          <accountTypeId>DATA_BUNDLE</accountTypeId>
       </topupAccountSpecifier>
       <!--Optional:-->
       <productId>' . $dataplan . '</productId>
       <!--Optional:-->
       <amount>
          <!--Optional:-->
          <currency>NGN</currency>
          <!--Optional:-->
          <value>' . $amount . '</value>
       </amount>
    </ext:requestTopup>
 </soapenv:Body>
</soapenv:Envelope>';


        $post_string = $airtime;
        if ($tariffTypeId == "9") {
            $post_string = $data;
        }
        $url = "http://41.203.65.10:8913/topupservice/service?wsdl";


        $header = array(
            "Content-type:text/xml;charset=\"utf-8\"",
            "Accept:application/xml",
            "Cache-Control:no-cache",
            "Connection: Keep-Alive",
            "Keep-Alive: 300",
            "Pragma:no-cache");
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 30);
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
        //curl_close($soap_do);


        //file_put_contents("ddd.txt",$result.$code."seun");


        if (in_array($code, array("404", "503"))) {
            $eml['status'] = 100;
            $eml['message'] = "Network Error Try Again";
            return $eml;
        }


        $response = str_replace(array("&lt;", "&"), array("<", "and"), $result);

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


        $eml = array();

        if ($all_data === false) {
            $eml['status'] = 300;
            $eml['message'] = "glo Error";
            self::db_query("INSERT INTO quickpay_transaction_log (quickpay_print) VALUES (?)",
                array($response));
        } else {


            $statusid = $all_data['resultCode'];


            if (in_array(trim($all_data['resultCode']), array(
                "0",
                "2016",
                "1",
                "2",
                "3",
                "4",
                "48",
                "90",
                "43",
                "57",
                "62",
                "93",
                "105"))) {

                //$rand_try = rand(0,2);

                if (trim($all_data['resultCode']) == "2016") {
                    $all_data['resultCode'] = "2016";
                    $all_data['ersReference'] = $sequence;
                } else {
                    //if($rand_try == 1){<><>
                    self::db_query("UPDATE settings SET setting_value = ? WHERE setting_key = 'glo_bal' ",
                        array($all_data['senderPrincipal']['accounts']['account']['balance']['value']));
                    // }

                }

                $eml['status'] = 200;
                $eml['message']['statusId'] = $all_data['resultCode'];
                $eml['message']['txRefId'] = $all_data['ersReference'];
            } else {

                $eml['status'] = 100;
                $eml['message'] = "Error :: $statusid " . self::glo_status($statusid);
            }


        }
        return $eml;
    }


    function glo_status($code)
    {

        switch ($code) {
            case "0":
                $response = "SUCCESS";
                break;
            case "1":
                $response = "PENDING_APPROVAL";
                break;
            case "2":
                $response = "REPORT_NOT_READY";
                break;
            case "3":
                $response = "VOUCHER_DELIVERED";
                break;
            case "4":
                $response = "VOUCHER_SOLD";
                break;
            case "10":
                $response = "REJECTED_BUSINESS_LOGIC";
                break;
            case "11":
                $response = "REJECTED_AMOUNT";
                break;
            case "12":
                $response = "REJECTED_PAYMENT";
                break;
            case "13":
                $response = "REJECTED_TOPUP";
                break;
            case "20":
                $response = "AUTHENTICATION_FAILED";
                break;
            case "21":
                $response = "ACCESS_DENIED";
                break;
            case "22":
                $response = "INVALID_NEW_PASSWORD";
                break;
            case "23":
                $response = "INVALID_ERS_REFERENCE";
                break;
            case "29":
                $response = "INVALID_INITIATOR_PRINCIPAL_ID";
                break;
            case "30":
                $response = "INVALID_RECEIVER_PRINCIPAL_ID";
                break;
            case "31":
                $response = "INVALID_SENDER_PRINCIPAL_ID";
                break;
            case "32":
                $response = "INVALID_TOPUP_PRINCIPAL_ID";
                break;
            case "33":
                $response = "INVALID_INITIATOR_PRINCIPAL_STATE";
                break;
            case "34":
                $response = "INVALID_RECEIVER_PRINCIPAL_STATE";
                break;
            case "35":
                $response = "INVALID_SENDER_PRINCIPAL_STATE";
                break;
            case "36":
                $response = "INVALID_TOPUP_PRINCIPAL_STATE";
                break;
            case "37":
                $response = "INITIATOR_PRINCIPAL_NOT_FOUND";
                break;
            case "38":
                $response = "RECEIVER_PRINCIPAL_NOT_FOUND";
                break;
            case "39":
                $response = "SENDER_PRINCIPAL_NOT_FOUND";
                break;
            case "40":
                $response = "TOPUP_PRINCIPAL_NOT_FOUND";
                break;
            case "41":
                $response = "INVALID_PRODUCT";
                break;
            case "42":
                $response = "INVALID_RECEIVER_ACCOUNT_TYPE";
                break;
            case "43":
                $response = "INVALID_SENDER_ACCOUNT_TYPE";
                break;
            case "44":
                $response = "INVALID_TOPUP_ACCOUNT_TYPE";
                break;
            case "45":
                $response = "RECEIVER_ACCOUNT_NOT_FOUND";
                break;
            case "46":
                $response = "SENDER_ACCOUNT_NOT_FOUND";
                break;
            case "47":
                $response = "TOPUP_ACCOUNT_NOT_FOUND";
                break;
            case "48":
                $response = "PAYMENT_IN_PROGRESS";
                break;
            case "49":
                $response = "INVALID_INVOICE_DATA";
                break;
            case "50":
                $response = "CANNOT_CANCEL_PAID_INVOICE";
                break;
            case "51":
                $response = "CANNOT_CANCEL_INVOICE_IN_PROGRESS";
                break;
            case "52":
                $response = "INVALID_CUSTOMER";
                break;
            case "53":
                $response = "INVALID_SEQR_ID";
                break;
            case "54":
                $response = "INVALID_INVOICE_REFERENCE";
                break;
            case "55":
                $response = "PAYMENT_ALREADY_CANCELLED";
                break;
            case "56":
                $response = "REGISTRATION_NOT_POSSIBLE";
                break;
            case "57":
                $response = "REGISTRATION_PRINCIPAL_ALREADY_EXISTS";
                break;
            case "60":
                $response = "AUTHORIZATION_EXPIRED";
                break;
            case "61":
                $response = "AUTHORIZATION_CANCELLED";
                break;
            case "62":
                $response = "AUTHORIZATION_IN_PROGRESS";
                break;
            case "63":
                $response = "INVALID_AUTHORIZATION_REFERENCE";
                break;
            case "90":
                $response = "SYSTEM_ERROR";
                break;
            case "91":
                $response = "UNSUPPORTED_OPERATION";
                break;
            case "92":
                $response = "LICENSE_REJECTION";
                break;
            case "93":
                $response = "SYSTEM_BUSY";
                break;
            case "94":
                $response = "SERVICE_UNAVAILABLE";
                break;
            case "95":
                $response = "INVOICE_ALREADY_CANCELED";
                break;
            case "96":
                $response = "INVOICE_STATE_NOT_RESERVED";
                break;
            case "97":
                $response = "PRODUCT_OUT_OF_STOCK";
                break;
            case "98":
                $response = "TRANSACTION_ALREADY_REVERSED";
                break;
            case "100":
                $response = "SUCCESS_CANCELLED";
                break;
            case "101":
                $response = "RECEIVER_ACCOUNT_DOES_NOT_ALLOW_REFUND";
                break;
            case "102":
                $response = "LIMIT_EXCEED";
                break;
            case "103":
                $response = "ACCOUNT_NOT_CREATED";
                break;
            case "104":
                $response = "INSUFFICIENT_SENDER_CREDIT";
                break;
            case "105":
                $response = "ACCOUNTSYSTEM_TIMEOUT";
                break;
            case "106":
                $response = "ACCOUNTSYSTEM_UNAVAILABLE";
                break;
            case "107":
                $response = "TRANSACTION_NOT_PENDING";
                break;
            case "108":
                $response = "COMMITTOPU_WITHOUT_PREPARETOPUP";
                break;
            case "109":
                $response = "CANCELTOPU_WITHOUT_PREPARETOPUP";
                break;
            case "901":
                $response = "INVALID_CUSTOMER_PRINCIPAL";
                break;
            case "530":
                $response = "SENDER_NOT_FOUND";
                break;
            case "931":
                $response = "TRANSACTION_REVERSAL_ALREADY_REQUESTED";
                break;
            case "933":
                $response = "TRANSACTION_NOT_FOUND";
                break;
            case "2016":
                $response = "TRANSACTION _ALREADY_COMPLETED";
                break;
            default:
                $response = "Known Error";
                break;
        }

        return $response;


    }


}
?>