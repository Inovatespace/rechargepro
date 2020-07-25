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
        $row = self::db_query("SELECT services_key,service_name FROM quickpay_services WHERE services_category = ?  AND status = '1' ORDER BY service_name ASC",
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


        if (in_array($service, array("AED"))) {
            include "kallack.php";
            $kallack = new kallack("POST");
            return $kallack->auth_transaction($parameter);
        }


        if (in_array($service, array("JOD", "JOP"))) {
            include "jedc.php";
            $jedc = new jedc("POST");
            return $jedc->auth_transaction($parameter);
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


               $data = array("private_key"=>self::config("rechargekey"),"token"=>self::config("rechargetoken"),"service"=>$service,"mobile"=>$phone,"amount"=>$amount,"accountnumber"=>$accountnumber,"bankcode"=>"","bundle"=>"");
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
            $phone,
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


        if (in_array($service, array("AED"))) {
            include "kallack.php";
            $kallack = new kallack("POST");
            return $kallack->complete_transaction($parameter);
        }



        if (in_array($service, array("JOD", "JOP"))) {
            include "jedc.php";
            $jedc = new jedc("POST");
            return $jedc->complete_transaction($parameter);
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
                return self::complete_transaction($parameter);
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
            
            
         
            
            if (isset($temarray['tokenUnit'])) {
                $units = $temarray['tokenUnit'];
            } 
            
            if (isset($temarray['token'])) {
                $token = $temarray['token'];
            } 
            
            if (isset($temarray['credittoken'])) {
                $token = $temarray['credittoken'];
            }
            
            if (isset($temarray['Token'])) {
                $token = $temarray['Token'];
            }
            
            if (isset($temarray['creditToken'])) {
                $token = $temarray['creditToken'];
            }
            
            if (isset($temarray['units'])) {
                $units = $temarray['units'];
            }
            if (isset($temarray['tokenunit'])) {
                $units = $temarray['tokenunit'];
            }
            
            
                     if (isset($token)) {
                    $message = "Token:" . $token . "\r\nAmount:$amount \r\nUnits:" . $units .
                        "\r\nInvoice Number:" . $quickpayid . "_" . $tid . "\r\nvisit quickpay.com.ng, For print out";
                    self::curlit($phone, $message);
                } else {
                    self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $quickpayid .
                        "_" . $tid . "\r\nvisit quickpay.com.ng, For print out");
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