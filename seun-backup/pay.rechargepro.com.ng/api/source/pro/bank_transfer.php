<?php
class bank_transfer extends Api
{
    //KEDCO 815.30
    public function __construct($method)
    {
        $this->proccess_count = 1;

        $this->transactionfee = 35;
        $this->wave_fee = 25;

    
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
$row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status,percentage FROM quickpay_services WHERE services_key = ? LIMIT 1",array("FUN"));
        $quickpay_service = $row[0]['service_name'];
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

        if (empty($quickpay_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        


        $banklist = self::bank_list($parameter);
        if (!isset($banklist[$bankcode])) {
            return array("status" => "100", "message" => "Invalid Bank");
        }


        #LASER
        $quickpayid = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT quickpayid,transfer_activation FROM quickpay_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $quickpayid = $row[0]['quickpayid'];
            $transfer_activation = $row[0]['transfer_activation'];
            if ($transfer_activation == 0) {
                return array("status" => "100", "message" =>
                        "Account not Eligible, Please contact your account officer or Support Department");
            }
            //check auth
            
            
         $rowb = self::db_query("SELECT percentage FROM quickpay_services_agent WHERE services_key = ? AND quickpayid = ? LIMIT 1",
            array("FUN", $quickpayid));
        if (!empty($rowb[0]['percentage'])){
            $percentage = $rowb[0]['percentage'];
            $this->transactionfee = $percentage;
            ///$bill_quickpayfull_percentage = $rowb[0]['bill_quickpayfull_percentage'];
        }
        
        }
        
        
                
        $data = array("private_key"=>self::config("rechargekey"),"token"=>self::config("rechargetoken"),"service"=>"BANKTRANSFER","mobile"=>"08152354744","amount"=>$amount,"accountnumber"=>$account,"bankcode"=>$bankcode,"bundle"=>"");
       $rechargepost =  self::file_get_b($data, self::config("rechargeurl")."initiate_transaction.json");
       
        
        

        
        if(!isset($rechargepost['status'])){
return array("status" => "100", "message" =>"Network Error Try again");
}

        if($rechargepost['status'] == "100"){
return array("status" => "100", "message" =>$rechargepost['message']);
}

$rechargetid =  $rechargepost['message']['tid'];
$name =  $rechargepost['message']['name'];        




        $narration = $narration;

        if (empty($name)) {
            return array("status" => "100", "message" => "Invalid Account Number");
        }


        #LASER
        $quickpayid = "0";
        $quickpayrole = 4;
        $totalamount = $amount + $this->transactionfee;
        $myservice_charge = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT quickpayid,quickpayrole,service_charge,is_service_charge FROM quickpay_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $quickpayid = $row[0]['quickpayid'];
            $quickpayrole = $row[0]['quickpayrole'];
            $is_service_charge = $row[0]['is_service_charge'];

            if ($amount > 5000) {
                $service_charge = ceil($amount / 5000) * 200;
            } else {
                $service_charge = 200;
            }


            if ($quickpayrole < 4) {

                if ($is_service_charge == 1) {
                    //$totalamount = $amount + $service_charge;
                    //$myservice_charge = $service_charge;
                    //$this->transactionfee = $service_charge;
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
        $insertid = self::db_query("INSERT INTO quickpay_transaction_log (thirdPartycode,service_charge,name,quickpayid,ip,quickpay_service,quickpay_subservice,account_meter,amount,phcn_unique,address) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $rechargetid,
            $myservice_charge,
            $name,
            $quickpayid,
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

        $row = self::db_query("SELECT quickpayid,quickpay_status,transactionid,quickpay_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,quickpay_status_code,quickpay_print,transaction_date FROM quickpay_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $quickpayid = $row[0]['quickpayid'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['quickpay_subservice'];
        $account = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $quickpay_status_code = $row[0]['quickpay_status_code'];
        $result = $row[0]['quickpay_print'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));


//get transaction fee
$rowv = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status,percentage FROM quickpay_services WHERE services_key = ? LIMIT 1",array("FUN"));
        $quickpay_service = $rowv[0]['service_name'];
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

        if (empty($quickpay_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        $this->transactionfee = $percentage;
        
        
                    $myrow = self::db_query("SELECT ac_ballance,profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                array($quickpayid));
            $myac_ballance = $myrow[0]['ac_ballance'];
            $myprofit_bal = $myrow[0]['profit_bal'];

        if ($quickpay_status_code == 1) {


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


        if ($row[0]['quickpay_status'] != "PAID") {

            if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }

            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT profit_bal,ac_ballance, quickpayid, profile_creator, quickpay_cordinator, quickpayrole, transfer_activation, is_service_charge FROM quickpay_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $mainacbal = $ac_ballance;
            $quickpayid = $row[0]['quickpayid'];
            $profile_creator = $row[0]['profile_creator'];
            $quickpayrole = $row[0]['quickpayrole'];
            $quickpay_cordinator = $row[0]['quickpay_cordinator'];
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
            if ($quickpayrole < 4) {
                if ($is_service_charge == 1) {
                    //$myservice_charge = $service_charge;
                }
            }


            if ($channel != 1) {
                $ac_ballance = $profit_bal;
            }
            
            
         $rowb = self::db_query("SELECT percentage FROM quickpay_services_agent WHERE services_key = ? AND quickpayid = ? LIMIT 1",
            array("FUN", $quickpayid));
        if (!empty($rowb[0]['percentage'])){
            $percentage = $rowb[0]['percentage'];
            $this->transactionfee = $percentage;
            ///$bill_quickpayfull_percentage = $rowb[0]['bill_quickpayfull_percentage'];
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


            $deductamount = $amount + $this->transactionfee;

            if ($ac_ballance < $deductamount) {
                return array("status" => "100", "message" => "Insufficient Fund");
            }


            $newballance = $ac_ballance - $deductamount;


            if ($channel != 1) {
                self::db_query("UPDATE quickpay_account SET profit_bal = ? WHERE quickpayid = ? LIMIT 1",
                    array($newballance, $quickpayid));
            } else {
                self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                    array($newballance, $quickpayid));
            }

            $quickpayprofit = $this->transactionfee - $this->wave_fee;

            self::db_query("UPDATE quickpay_transaction_log SET service_charge=?, cordinator_id =?, quickpay_status = ?,agent_id=?,quickpayid=?,payment_method=?,quickpayprofit =?, reb = ? WHERE transactionid = ? LIMIT 1",
                array(
                $myservice_charge,
                $quickpay_cordinator,
                "PAID",
                $profile_creator,
                $quickpayid,
                2,
                $quickpayprofit,
                $this->transactionfee,
                $tid));
        }

        
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
?>