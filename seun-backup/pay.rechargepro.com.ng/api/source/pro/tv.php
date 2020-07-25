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
        $row = self::db_query("SELECT services_key,service_name FROM quickpay_services WHERE services_category = ?  AND status = '1' ORDER BY id",
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


        if (isset($available_bounquet['message'])) {

            $amount = "";
            foreach ($available_bounquet['message'] AS $key => $val) {
                if ($key == $code) {
                    $val = explode(" ",$val);
                    $amount = trim($val[0]);
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
            return array("status" => "100", "message" => "Invalid Amountk");
        }

        if (strlen($mobile) > 11 || strlen($mobile) < 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }

        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
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
        
         

        $minimumsales_amount = 100;
        if ($minimumsales_amount > $amount) {
            return array("status" => "100", "message" => "Minimum Amount Allowed: $minimumsales_amount");
        }

        if ($amount > $maximumsales_amount) {
            return array("status" => "100", "message" => "Maximum Amount Allowed: $maximumsales_amount");
        }

        if (empty($quickpay_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }


               $data = array("private_key"=>self::config("rechargekey"),"token"=>self::config("rechargetoken"),"service"=>$service,"mobile"=>$mobile,"amount"=>$amount,"accountnumber"=>$accountnumber,"bankcode"=>"","bundle"=>"");
       $rechargepost =  self::file_get_b($data, self::config("rechargeurl")."initiate_transaction.json");
       
        
        
        if(!isset($rechargepost['status'])){
return array("status" => "100", "message" =>"Network Error Try again");
}

        if($rechargepost['status'] == "100"){
return array("status" => "100", "message" =>$rechargepost['message']);
}

$rechargetid =  $rechargepost['message']['tid'];
$name =  $rechargepost['message']['name'];        



        $number = "";
        #LASER
        $quickpayid = "0";
        $quickpayrole = 4;
        $totalmount = $amount;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT quickpayid,quickpayrole,service_charge,is_service_charge FROM quickpay_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $quickpayid = $row[0]['quickpayid'];
            $quickpayrole = $row[0]['quickpayrole'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];

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

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO quickpay_transaction_log (thirdPartycode,service_charge,quickpayid,ip,quickpay_service,quickpay_subservice,account_meter,amount,phone,email,address,name,phcn_unique) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $rechargetid,
            $myservice_charge,
            $quickpayid,
            $ip,
            $quickpay_service,
            $service,
            $accountnumber,
            $amount,
            $mobile,
            $email,
            $number,
            $name,
            $code));

        $tfee = 0;
        if ($quickpayrole > 3) {
            $tfee = $this->transaction_fee;
        }

        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount" => $amount,
                "totalamount" => $totalmount,
                "tfee" => $tfee,
                "number" => $accountnumber,
                "tid" => $insertid));
    }



    public function available_bounquet($parameter)
    {

        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        $service = $parameter['service'];

       


        //chek
        $date = date("Y-m-d");
        $row = self::db_query("SELECT setting_value FROM settings WHERE setting_key = ?  AND setting_date > ? LIMIT 1",
            array($service, $date)); //
        $setting_value = $row[0]['setting_value'];




        if (!empty($setting_value)) {

            $j = json_decode($setting_value, true);
            return array("status" => "200", "message" => $j);
        }


       $data = array("private_key"=>self::config("rechargekey"),"token"=>self::config("rechargetoken"),"service"=>$service);
       $rechargepost =  self::file_get_b($data, self::config("rechargeurl")."tv_banquet_list.json");
       
       if(empty($rechargepost)){
        return array();
       }
       
       
      if(count($rechargepost) < 3){
        return array();
       }
      
        //udate
        $setting_date = date("Y-m-d H:i:s");
        self::db_query("UPDATE settings SET setting_value = ?,setting_date = ? WHERE setting_key = ? LIMIT 1",
            array(
            json_encode($rechargepost),
            $setting_date,
            $service));


        return array("status" => "200", "message" => $rechargepost);


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


}
//{"items":[{"code":"ACSSE36","invoicePeriods":"[1,12]","price":2000,"name":"Access","description":" "},{"code":"ACSSE37","invoicePeriods":"[1,12]","price":7400,"name":"Access + Asia","description":" "},{"code":"ACSSE38","invoicePeriods":"[1,12]","price":4200,"name":"Access + HD\/ExtraView","description":" "},{"code":"ASIAE36","invoicePeriods":"[1,12]","price":5400,"name":"Asia Standalone","description":" "},{"code":"ASIAE37","invoicePeriods":"[1,12]","price":7600,"name":"Asian + HD\/ExtraView","description":" "},{"code":"COFAME36","invoicePeriods":"[1,12]","price":4000,"name":"Family","description":" "},{"code":"COFAME37","invoicePeriods":"[1,12]","price":9400,"name":"Family + Asia","description":" "},{"code":"COFAME38","invoicePeriods":"[1,12]","price":6200,"name":"Family + HD\/ExtraView","description":" "},{"code":"COMPE36","invoicePeriods":"[1,12]","price":6800,"name":"Compact","description":" "},{"code":"COMPE37","invoicePeriods":"[1,12]","price":9000,"name":"Compact + HD\/ExtraView","description":" "},{"code":"COMPE38","invoicePeriods":"[1,12]","price":8270,"name":"Compact + French Touch","description":" "},{"code":"COMPE39","invoicePeriods":"[1,12]","price":10470,"name":"Compact + French Touch + HD\/ExtraView","description":" "},{"code":"COMPLE36","invoicePeriods":"[1,12]","price":10650,"name":"Compact Plus","description":" "},{"code":"COMPLE37","invoicePeriods":"[1,12]","price":10650,"name":"Compact Plus","description":" "},{"code":"COMPLE38","invoicePeriods":"[1,12]","price":16050,"name":"Compact Plus + Asia","description":" "},{"code":"COMPLE39","invoicePeriods":"[1,12]","price":12850,"name":"Compact Plus + HD\/ExtraView","description":" "},{"code":"COMPLE40","invoicePeriods":"[1,12]","price":12850,"name":"Compact Plus + HD\/ExtraView","description":" "},{"code":"PRWE36","invoicePeriods":"[1,12]","price":15800,"name":"Premium","description":" "},{"code":"PRWE37","invoicePeriods":"[1,12]","price":18000,"name":"Premium + HD\/ExtraView","description":" "},{"code":"PRWE38","invoicePeriods":"[1,12]","price":17270,"name":"Premium + French Touch","description":" "},{"code":"PRWE39","invoicePeriods":"[1,12]","price":19470,"name":"Premium + French Touch + HD\/ExtraView","description":" "},{"code":"PRWASIE36","invoicePeriods":"[1,12]","price":17700,"name":"Premium Asia","description":" "},{"code":"PRWFRNSE36","invoicePeriods":"[1,12]","price":22200,"name":"Premium French Bonus","description":" "},{"code":"PRWFRNSE37","invoicePeriods":"[1,12]","price":24400,"name":"Premium French Bonus + HD\/Extraview","description":" "},{"code":"PRWASIE37","invoicePeriods":"[1,12]","price":19900,"name":"Premium Asia + HD\/ExtraView","description":" "},{"code":"ASIADDE36","invoicePeriods":"[1,12]","price":5400,"name":"Asia Add-on","description":" "},{"code":"FRN11E36","invoicePeriods":"[1,12]","price":6360,"name":"French Plus","description":" "},{"code":"FRN7E36","invoicePeriods":"[1,12]","price":1470,"name":"French Touch","description":" "},{"code":"FRN11W7","invoicePeriods":"[1,12]","price":3180,"name":"French 11","description":" "},{"code":"FRN15E36","invoicePeriods":"[1,12]","price":2200,"name":"HDPVR Access\/Extraview","description":" "},{"code":"BO","invoicePeriods":"[1,12]","price":400,"name":"Box Office","description":" "}]}

?>