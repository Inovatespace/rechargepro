<?php
class jedc extends Api
{
    //201804191335
    //201804191330
    //KEDCO


    public function __construct($method)
    {
        //Api::Api_Method("user_key", "POST", $method); // this tell the API to only allow POST method

        $this->jos_postpaid_username = "8a9f6683da_live";
        $this->jos_postpaid_password = 'sG7vU4tEuS5v_b';
        $this->jos_postpaid_url = "https://jedbills.com.ng/api/live/postpaid";
        //https://jos.lightup.com.ng/api/registry/postpaid
        $this->jos_postpaid_auth_url = "https://jedbills.com.ng/api/auth/token/live";


        $this->jos_prepaid_username = "4cf470c50cc945b_live";
        $this->jos_prepaid_password = "z6dN9k5mh3^s9h2A@d";
        $this->jos_prepaid_url = "https://api.adroitsuite.com.ng/core/auth/token/live";
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



        if (in_array($service, array("JOP"))) {
            return self::meter_info_jos_post_paid($parameter);
        }

        if (in_array($service, array("JOD"))) {
            return self::meter_info_jos_pre_paid($parameter);
        }


        return array("status" => "100", "message" => "Invalid Selection");

    }


 

    public function is_in_array($arraya, $arrayb)
    {
        $toreturn = false;
        foreach ($arraya as $value) {
            //if (in_array($value, $arrayb)) {
            if (isset($arrayb[$value])) {
                $toreturn .= $arrayb[$value] . " ";
            }
            //}
        }

        return $toreturn;
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


        if (in_array($service, array("JOP"))) {
            return self::buy_jos_post_paid($parameter);
        }


        if (in_array($service, array("JOD"))) {
            return self::buy_jos_pre_paid($parameter);
        }


        return array("status" => "100", "message" => "Invalid Selection");

    }


    ///////////////////////// JOS POST PAID

    function jos_postpaid_auth()
    {
        $data_string = array(
            "username" => $this->jos_postpaid_username,
            "password" => $this->jos_postpaid_password,
            "grant_type" => "password");
        $access = self::jos_disco_postpaid_post($this->jos_postpaid_auth_url, "POST", $data_string);

        if (isset($access['access_token'])) {
            $date = date("Y-m-d H:i:s", strtotime("+0 day", strtotime($access['expires_on'])));
            self::db_query("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
                array(
                $access['access_token'],
                $date,
                "jos_disco_postpaid"));
            return array("status" => "200", "message" => $access['access_token']);
        } else {
            return array("status" => "100", "message" => $access); //"Network Error"
        }

    }


    function jos_disco_postpaid_post($path, $type = "GET", $parameter = array())
    {

        $auth = "1";

        $header = array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded",
            "postman-token: b4665676-ee7a-b08d-7440-12fc44afb79d");

        if (strpos($path, 'auth/token') === false) {

            $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
            $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
                array($nowdate, "jos_disco_postpaid"));
            if (!empty($rmk[0]['setting_value'])) {

                $auth = $rmk[0]['setting_value'];

            } else {

                $access = self::jos_postpaid_auth();
                if ($access['status'] == "100") {
                    return $access;
                } else {
                    $auth = $access['message'];
                }
            }

            $header[] = "Authorization: Bearer $auth";
        }


        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => "$path",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => http_build_query($parameter),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $header,
            ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response, true);

        return $response;
    }


    function meter_info_jos_post_paid($parameter)
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



      $data_string = array(
            "customer_no" => $accountnumber);
        $response = self::jos_disco_postpaid_post("$this->jos_postpaid_url/customer",
            "POST", $data_string);
            
      

        if (!isset($response['code'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again");
        }


        if ($response['code'] != "100") {
            return array("status" => "100", "message" => $response["message"]);
        }

        $name = $response['name'];
        $address = $response['address'];
        $unique = "";
        $thirdParty = "";
        $district = "";
        $business = $response['state'];

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


    function buy_jos_post_paid($parameter)
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


        $vref = $transaction_date . $tid;
        $data_string = array(
            "txn_ref" => $vref,
            "amount" => $amount,
            "posted_on" => $transaction_date,
            "customer_no" => $accountnumber,
            "mobile" => $phone,
            "cheque" => "false",
            "agent_id" => "08036541599");
        $response = self::jos_disco_postpaid_post("$this->jos_postpaid_url/submit",
            "POST", $data_string);

        //Array ( [receipt_no] => 81591747190001 [customer_no] => 611170030400 [code] => err100 [message] => Successful [timestamp] => 6/8/2018 5:47:52 PM )


        if (!isset($response['code'])) {
            return array("status" => "100", "message" =>
                    "An error occured please contact support with TID $tid 3");
        }


        //{"transactionNumber":9965,"details":{"":"6453258859733574","errorMessage":null,"":null,"utilityName":"Eskom","status":"ACCEPTED"}}

        self::db_query("UPDATE brinq_transaction_log SET quickpay_status = ? WHERE transactionid = ? LIMIT 1",
            array("PAID", $tid));

        if ($response['code'] == "100" || $response['code'] == "108" || $response['code'] ==
            "107" || $response['code'] == "99") {


            $response['Customer Number'] = $accountnumber;
            $response['Name'] = $name;
            $response['Address'] = $address;
            $response['State'] = $district;
            $response['Amount Paid'] = $amount;
            $response["Account Type"] = "Postpaid";
            $response["Transaction Date"] = date("Y-m-d H:i:s");

            //self::db_query("UPDATE brinq_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
             //   array($amount, $service));

            $status = $response['message'];
            $statuscode = "0";
            $statusreference = $response['receipt_no'];


           // $josper = (($amount * $bill_brinqfull_percentage) / 100);


            self::db_query("UPDATE brinq_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,brinq_status_code =?, quickpay_print = ?  WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));

            self::curlit($phone, "JEDC POSTPAID\n Meter :" . $accountnumber . " \n Amount :" .
                $response["Amount Paid"] . " \n Paid");


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
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $temarray));
        } else {
                               $message = "Failed1";
                    if (isset($response['message'])) {
                        $message = $response['message'];
                    }
                    
                  $newprint = '{"details":{"REFUND_DATE":"' . date("Y-m-d H:i:s") .
                '","TRANSACTION STATUS","DONE"}}';

          //  self::db_query("UPDATE brinq_transaction_log SET transaction_status =?, quickpay_service = ?, quickpay_subservice =?, brinq_status_code=?, quickpay_status=?, quickpay_print = ?  WHERE transactionid = ?",
          //      array(
         //       $message,
           //     "REFUND($quickpay_service)",
             //   "REFUND",
             //   "1",
              //  "PAID",
               // $newprint,
               // $tid));

            return array("status" => "100", "message" => $message);
        }


    }


    // JOS PREPAID


    function jos_prepaid_auth()
    {
        $data_string = array("username" => $this->jos_prepaid_username, "password" => $this->
                jos_prepaid_password);
        $access = self::jos_disco_prepaid_post($this->jos_prepaid_url, "POST", $data_string);



        if (isset($access['token'])) {
            $date = date("Y-m-d H:i:s", strtotime("+0 day", strtotime($access['expires'])));
            self::db_query("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
                array(
                $access['token'],
                $date,
                "jos_disco_prepaid"));
            return array("status" => "200", "message" => $access['token']);
        } else {
            return array("status" => "100", "message" => "Network Error");
        }

    }


    function jos_disco_prepaid_post($path, $type = "GET", $parameter = array())
    {
        $auth = "1";

        $header = array(
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded",
            "postman-token: b4665676-ee7a-b08d-7440-12fc44afb79d");

        if (strpos($path, 'token') === false) {
            $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
            $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
                array($nowdate, "jos_disco_prepaid"));
            if (!empty($rmk[0]['setting_value'])) {
                $auth = $rmk[0]['setting_value'];
            } else {
                $access = self::jos_prepaid_auth();
                if ($access['status'] == "100") {
                    return $access;
                } else {
                    $auth = $access['message'];
                }
            }

            $header[] = "Authorization: Bearer $auth";
        }


        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => "$path",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => http_build_query($parameter),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $header,
            ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $response = json_decode($response, true);

        return $response;
    }


    function meter_info_jos_pre_paid($parameter)
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



        //Array ( [client] => Array ( [meter_name] => SUNDAY [meter_address] => FWATI M ANGWA DOKI,BUKURU,null [meter_number] => 04181818552 ) [code] => 100 [message] => Successful [time] => 6/11/2018 12:28:25 PM )
        $response = self::jos_disco_prepaid_post("https://api.adroitsuite.com.ng/core/energy/jos/prepaid/live/customer/$accountnumber");


        if (!isset($response['code'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again");
        }


        if ($response['code'] != "100") {
            return array("status" => "100", "message" => $response["message"]);
        }

        $name = $response['client']['meter_name'];
        $address = $response['client']['meter_address'];
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


    function buy_jos_pre_paid($parameter)
    {

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }


        $tid = urldecode($parameter['tid']);


        $row = self::db_query("SELECT quickpay_service,brinqid,quickpay_status,transactionid,quickpay_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,brinq_status_code,brinq_status_code,quickpay_print,transaction_date FROM brinq_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $name = $row[0]['name'];
        $brinqid = $row[0]['brinqid'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['quickpay_subservice'];
        $accountnumber = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $quickpay_service = $row[0]['quickpay_service'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $brinq_status_code = $row[0]['brinq_status_code'];
        $result = $row[0]['quickpay_print'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        $postdate = $row[0]['transaction_date'];
        if ($brinq_status_code == 1) {
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


        self::db_query("UPDATE brinq_transaction_log SET quickpay_status = ? WHERE transactionid = ? LIMIT 1",
            array("PAID", $tid));


        $row = self::db_query("SELECT service_resuest, bill_brinqfull_percentage FROM brinq_services WHERE services_key = ? LIMIT 1",
            array($service));
        $service_resuest = $row[0]['service_resuest'];
        $bill_brinqfull_percentage = $row[0]['bill_brinqfull_percentage'];


        $vref = $transaction_date . $tid;
        $data_string = array(
            "amount" => $amount,
            "vref" => $vref,
            "meter" => $accountnumber,
            "wallet_id" => "DBA0EC0E2A65802D07762CDBA",
            "mobile" => $phone,
            "agent_id" => "QPY_8036541599");//$parameter['agentmobile']);
        $response = self::jos_disco_prepaid_post("https://api.adroitsuite.com.ng/core/energy/jos/prepaid/live/vend",
            "POST", $data_string);


        if (!isset($response['code'])) {
            return array("status" => "100", "message" =>
                    "An error occured please contact support with TID $tid 3");
        }


        //Array ( [transaction_id] => 181581035594 [meter_class] => SINGLE PHASE RESIDENTIAL (R2) [charge] => Array ( [debt_paid] => 0.2 [debt_tax] => 0 [fixed_amount] => 0 [fixed_tax] => 0 [debt_remaining] => 70 [debt_desc] => [fixed_desc] => ) [token] => Array ( [tariff] => 0.1 KWh @ 29.81 N/KWh: : : [amount] => 0.77 [tax] => 0.3 [units] => 0.1 [pin] => 1972-2721-2608-2574-9205 ) [code] => 100 [message] => OK [time] => 6/11/2018 12:24:31 PM )


        if ($response['code'] == "100") {

            $response['Customer Number'] = $accountnumber;
            $response['Name'] = $name;
            $response['Address'] = $address;
            $response['Amount Paid'] = $amount;
            $response["Account Type"] = "Prepaid";
            $response["Transaction Date"] = date("Y-m-d H:i:s");


            // self::db_query("UPDATE brinq_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",array($amount, $service));

            $status = $response['message'];
            $statuscode = "0";
            $statusreference = $response['transaction_id'];

            //$josper = (($amount * $bill_brinqfull_percentage) / 100);


            self::db_query("UPDATE brinq_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,brinq_status_code =?, quickpay_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));


            self::curlit($phone, "JEDC TOKEN\n Token :" . $response["token"]["pin"] . " \n Amount :" .
                $response["Amount Paid"] . " \n units :" . $response["token"]["units"]);


            $response = self::array_flatten($response);

            if (isset($response['pin'])) {
                $response['Token'] = $response['pin'];
                unset($response['pin']);
            }


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
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $temarray));

        } else {


            if ($response['code'] == "701") {


                $response = self::jos_disco_prepaid_post("https://api.adroitsuite.com.ng/core/energy/jos/prepaid/live/transaction/$vref");


                //self::db_query("UPDATE brinq_transaction_log SET quickpay_print = ? WHERE transactionid = ? LIMIT 1",array(json_encode($response),$tid));


                if ($response['code'] == "100") {

                    $response['Customer Number'] = $accountnumber;
                    $response['Name'] = $name;
                    $response['Address'] = $address;
                    $response['Amount Paid'] = $amount;
                    $response["Account Type"] = "Prepaid";
                    $response["Transaction Date"] = date("Y-m-d H:i:s");


                    // self::db_query("UPDATE brinq_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",array($amount, $service));

                    $status = $response['message'];
                    $statuscode = "0";
                    $statusreference = $response['transaction_id'];

                    //$josper = (($amount * $bill_brinqfull_percentage) / 100);


                    self::db_query("UPDATE brinq_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,brinq_status_code =?, quickpay_print = ? WHERE transactionid = ? LIMIT 1",
                        array(
                        $status,
                        $statuscode,
                        $statusreference,
                        1,
                        json_encode($response),
                        $tid));


                    $response = self::array_flatten($response);

                    if (isset($response['pin'])) {
                        $response['Token'] = $response['pin'];
                        unset($response['pin']);
                    }


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
                            "status" => "Accepted",
                            "TransactionID" => $tid,
                            "details" => $temarray));

                } else {


                    $message = "Failed1";
                    if (isset($response['message'])) {
                        $message = $response['message'];
                    }
                    
                  $newprint = '{"details":{"REFUND_DATE":"' . date("Y-m-d H:i:s") .
                '","TRANSACTION STATUS","DONE"}}';

//            self::db_query("UPDATE brinq_transaction_log SET transaction_status =?, quickpay_service = ?, quickpay_subservice =?, brinq_status_code=?, quickpay_status=?, quickpay_print = ?  WHERE transactionid = ?",
//                array(
//                $message,
//                "REFUND($quickpay_service)",
//                "REFUND",
//                "1",
//                "PAID",
//                $newprint,
//                $tid));
                
                
                    return array("status" => "100", "message" => $message);


                }

            } else {


                          $message = "Failed1";
                    if (isset($response['message'])) {
                        $message = $response['message'];
                    }
                    
                  $newprint = '{"details":{"REFUND_DATE":"' . date("Y-m-d H:i:s") .
                '","TRANSACTION STATUS","DONE"}}';

 //           self::db_query("UPDATE brinq_transaction_log SET transaction_status =?, quickpay_service = ?, quickpay_subservice =?, brinq_status_code=?, quickpay_status=?, quickpay_print = ?  WHERE transactionid = ?",
//                array(
//                $message,
//                "REFUND($quickpay_service)",
//                "REFUND",
//                "1",
//                "PAID",
//                $newprint,
//                $tid));
                
                return array("status" => "100", "message" => $message);
            }
        }


    }


}
?>