<?php
class eko extends Api
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


        $url = "https://eko.phcnpins.com/API/vproxy.asmx?op=FetchCust";
        $post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchCust xmlns="http://IKEDC_API/vproxy/">
      <MeterNo>' . $accountnumber . '</MeterNo>
      <hashstring>' . hash('sha512', $accountnumber . "EK0134") . '</hashstring>
      <api_key>46374a1d-2b9d-4ede-a7f3-731367d345cf</api_key>
    </FetchCust>
  </soap:Body>
</soap:Envelope>';


        $header = array(
            "Content-type:text/xml;charset=\"utf-8\"",
            "Accept:application/xml",
            "Cache-Control:no-cache",
            "Pragma:no-cache",
            //	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
            "Content-length:" . strlen($post_string));

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
        // curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);


        $result = str_replace(array("soap:", ":soap"), array("", ""), $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        $array = self::array_change_value_case($array);


        if ($code != 200) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }


        if (!isset($array['body'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }

        if (!isset($array['body']['fetchcustresponse'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again2");
        }

        if (!isset($array['body']['fetchcustresponse']['fetchcustresult'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again3");
        }

        $response = $array['body']['fetchcustresponse']['fetchcustresult'];


        //00_accountno: 026742255101, accounttype: 2, address: N/A, Balance: , ContactNo: , MeterNo: 026742255101, MinAmount: , Name: ALH KAREEM WAHAB

        $exp = explode("_", $response);


        if (trim($exp[0]) != "00") {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }

        $exp = explode(",", $response);


        if (!isset($exp[1])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }

        $ex = explode(":", $exp[0]);
        $re = trim($ex[1]);
        if (empty($re)) {
            return array("status" => "100", "message" => $response);
        }


        $district = "";

        $name = "";
        if (isset($exp[7])) {
            $ex = explode(":", $exp[7]);
            $name = $ex[1];
        }

        $thirdParty = "";
        $business = "";

        $thirdParty = "";
        if (isset($exp[1])) {
            $ex = explode(":", $exp[1]);
            $thirdParty = $ex[1];
        }

        $unique = "";

        $address = "";
        if (isset($exp[2])) {
            $ex = explode(":", $exp[2]);
            $address = $ex[1];
        }


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


        $statusreference = $transaction_date . $tid;


        $ispre = 0;
        if ($service == "EKP") {
            $ispre = 0;
        }

        $url = "https://eko.phcnpins.com/API/vproxy.asmx?op=PostTransaction_New";
        $post_string = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <PostTransaction_New xmlns="http://IKEDC_API/vproxy/">
      <AccountNo>' . $accountnumber . '</AccountNo>
      <amount>' . $amount . '</amount>
      <hashstring>' . hash('sha512', $accountnumber . "EK0134") . '</hashstring>
      <api_key>46374a1d-2b9d-4ede-a7f3-731367d345cf</api_key>
      <emailaddress>anderson@gmail.com</emailaddress>
      <isprepaid>'.$ispre.'</isprepaid>
      <txnref>' . $statusreference . '</txnref>
      <responseType>json</responseType>
    </PostTransaction_New>
  </soap:Body>
</soap:Envelope>';


        file_put_contents("ddd.txt", $post_string);


        $header = array(
            "Content-type:text/xml;charset=\"utf-8\"",
            "Accept:application/xml",
            "Cache-Control:no-cache",
            "Pragma:no-cache",
            //	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
            "Content-length:" . strlen($post_string));

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
        // curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);


file_put_contents("pureeko.php",$result);

        if ($code != 200) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }


        $result = str_replace(array("soap:", ":soap"), array("", ""), $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        $array = self::array_change_value_case($array);


        if (!isset($array['body'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }


        if (!isset($array['body']['posttransaction_newresponse'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again2");
        }


        if (!isset($array['body']['posttransaction_newresponse']['posttransaction_newresult'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again3");
        }


        $mainresponse = $array['body']['posttransaction_newresponse']['posttransaction_newresult'];
        $response = json_decode($mainresponse, true);


        if (isset($response[0])) {
            $response = $response[0];
            $mainresponse = json_encode($response[0]);
        }


        //[{"responsecode":"00","responsemessage":"Successful|CreditToken:71695923784348208589|TranId:201901071245131352|MeterNo:45022194174|Rate:0.00|Value:0.0. Thank you for using the service. Enugu Electric."}]200


        //[{"responsecode":"00","responsemessage":"Successful|CreditToken:71695923784348208589|TranId:201901071245131352|MeterNo:45022194174|Rate:0.00|Value:0.0. Thank you for using the service. Enugu Electric."}]200


        $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];
        $namyname = $myrow[0]['name'];


        if (isset($response['responsecode'])) {

            if (in_array($response['responsecode'], array("01"))) {
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


        if (!isset($response['responsecode'])) {

            if (!empty($mainresponse)) {
                $rps = explode("_", $mainresponse);
                if (isset($rps[0])) {
                    if (in_array($rps[0], array(
                        "04",
                        "05",
                        "01"))) {
                        return array("status" => "100", "message" => "An error occured");
                    }
                }
            }

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


        if ($response['responsecode'] == "00") {

            //

            $status = $response['responsecode'];
            $statuscode = "0";
            $statusreference = $response['responsemessage'];
            $response['returned_message'] = $response['responsemessage'];

            $natoken = "0";
            $rp = explode("|", $response['responsemessage']);
            if (isset($rp[1])) {
                $token = explode(":", $rp[1]);
                if (strtolower($token[0]) == "credittoken") {
                    $natoken = "1";
                    $response['token'] = $token[1];
                }
            }

            if (isset($rp[5])) {
                $tokenunit = explode(":", $rp[5]);
                if (strtolower($tokenunit[0]) == "value") {
                    $response['tokenunit'] = substr($tokenunit[1], 0, strpos($tokenunit[1], '.',
                        strpos($tokenunit[1], '.') + 1));
                }
            }

            if (!isset($parameter['sms'])) {
                if ($natoken == "1") {
                    $message = "Token:" . $response['token'] . "\r\nAmount:$amount \r\nUnits:" . $response['tokenunit'] .
                        "\r\nInvoice Number:" . $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                    self::curlit($phone, $message);
                } else {
                    self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                        "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
                }
            }


            if (isset($response['token'])) {
                $response['Token'] = $response['token'];
            }

            //olisa
            $response['Agent_name']=$namyname;
            $response['service_charge'] = "N100";
            $response['Total_amount'] = "N" . ($amount + 100);
            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));


            self::que_rechargepropay_mail($tid, $email, "success");

            $response = self::array_flatten($response);

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
            if ($response['responsecode'] == "04") {


                if ($response['responsemessage'] == "invalid input") {

                    return array("status" => "100", "message" => $response['responsemessage']);

                }


                //Transaction Already Exists
                $response = self::verify_transaction($statusreference);

                if ($response['status'] == 100) {
                    return $response;
                }

                $response = $response['message'];
                if (isset($response[0])) {
                    $response = $response[0];
                    $mainresponse = json_encode($response[0]);
                }

                if (isset($response['responsecode'])) {

                    if (in_array($response['responsecode'], array("01"))) {
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


                if ($response['responsecode'] == "00") {

                    //

                    $status = $response['responsecode'];
                    $statuscode = "0";
                    $statusreference = $response['responsemessage'];

                    $response['returned_message'] = $response['responsemessage'];
                    $natoken = "0";
                    $rp = explode("|", $response['responsemessage']);
                    if (isset($rp[1])) {
                        $token = explode(":", $rp[1]);
                        if (strtolower($token[0]) == "credittoken") {
                            $natoken = "1";
                            $response['token'] = $token[1];
                        }
                    }

                    if (isset($rp[5])) {
                        $tokenunit = explode(":", $rp[5]);
                        if (strtolower($tokenunit[0]) == "value") {
                            $response['tokenunit'] = substr($tokenunit[1], 0, strpos($tokenunit[1], '.',
                                strpos($tokenunit[1], '.') + 1));
                        }
                    }

                    if (!isset($parameter['sms'])) {
                        if ($natoken == "1") {
                            $message = "Token:" . $response['token'] . "\r\nAmount:$amount \r\nUnits:" . $response['tokenunit'] .
                                "\r\nInvoice Number:" . $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                            self::curlit($phone, $message);
                        } else {
                            self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                                "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
                        }
                    }


                    if (isset($response['token'])) {
                        $response['Token'] = $response['token'];
                    }

                    //olisa
                    $response['Agent_name']=$namyname;
                    $response['service_charge'] = "N100";
                    $response['Total_amount'] = "N" . ($amount + 100);
                    self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                        array(
                        $status,
                        $statuscode,
                        $statusreference,
                        1,
                        json_encode($response),
                        $tid));


                    self::que_rechargepropay_mail($tid, $email, "success");

                    $response = self::array_flatten($response);
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
                } else {
                    $status = $response['responsemessage'];
                    $statuscode = $response['responsecode'];
                    $statusreference = $response['responsemessage'];

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
                $status = $response['responsemessage'];
                $statuscode = $response['responsecode'];
                $statusreference = $response['responsemessage'];

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


    function verify_transaction($tref)
    {

        $url = "https://eko.phcnpins.com/API/vproxy.asmx?op=FetchTxnByRef";
        $post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>' . $tref . '</TxnRef>
      <hashstring>' . md5($tref . "EK0134") . '</hashstring>
      <api_key>46374a1d-2b9d-4ede-a7f3-731367d345cf</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';


        $header = array(
            "Content-type:text/xml;charset=\"utf-8\"",
            "Accept:application/xml",
            "Cache-Control:no-cache",
            "Pragma:no-cache",
            //	"SOAPAction:http://fets.phcnpins.com/API/vproxy.asmx?WSDL",
            "Content-length:" . strlen($post_string));

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        // curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);


        //file_put_contents("dd.xml",$result);

        $result = str_replace(array("soap:", ":soap"), array("", ""), $result);
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        $array = json_decode($json, true);
        $array = self::array_change_value_case($array);


        if (!isset($array['body'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }


        if (!isset($array['body']['fetchtxnbyrefresponse'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again2");
        }


        if (!isset($array['body']['fetchtxnbyrefresponse']['fetchtxnbyrefresult'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again3");
        }


        return array("status" => "200", "message" => json_decode($array['body']['fetchtxnbyrefresponse']['fetchtxnbyrefresult'], true));


    }


}
?>