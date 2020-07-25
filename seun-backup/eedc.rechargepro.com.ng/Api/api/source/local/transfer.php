<?php

class transfer extends Api
{

    //KEDCO

    public function __construct($method)
    {

    }


    public function auth_transfer($parameter)
    {

        if (!isset($parameter['account'])) {
            return array("status" => "100", "message" => "Invalid Account");
        }


        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        $account = urldecode($parameter['account']);
        $amount = self::cleandigit(urldecode($parameter['amount']));


        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        $row = self::db_query("SELECT name, mobile, email, rechargeproid FROM rechargepro_account WHERE mobile = ? || email = ? LIMIT 1",
            array($account, $account));
        $name = $row[0]['name'];
        $mobile = $row[0]['mobile'];
        $email = $row[0]['email'];
        $rechargeproid_main = $row[0]['rechargeproid'];


        if (empty($name)) {
            return array("status" => "100", "message" => "Invalid Account");
        }

        #LASER
        $rechargeproid = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
        }


        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email) VALUES (?,?,?,?,?,?,?,?)",
            array(
            $rechargeproid,
            $ip,
            "TRANSFER",
            "TRANSFER",
            $rechargeproid_main,
            $amount,
            $mobile,
            $email));


        return array("status" => "200", "message" => array(
                "ac" => $account,
                "totalamount"=>$amount,
                "tfee"=>0,
                "name" => $name,
                "amount" => $amount,
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
        $rechargeproid_main = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $result = $row[0]['rechargepro_print'];
        $transaction_date = date('Y-m-d', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

        if ($rechargepro_status_code == 1) {
$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];


            $response = json_decode($result, true);
            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
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
            $row = self::db_query("SELECT ac_ballance,profit_bal, rechargeproid, profile_creator, rechargepro_cordinator, rechargeprorole FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            
            if($channel != 1){
              $ac_ballance = $profit_bal;  
            }

            if ($ac_ballance < $amount) {
                return array("status" => "100", "message" => "Insufficient Fund");
            }

            self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                array(
                $rechargeproid,
                $rechargeproid_main,
                $amount,
                "TRANSFER"));


            $newballance = $ac_ballance - $amount;

            
                
                  if($channel != 1){
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }else{
               self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                array($newballance, $rechargeproid));
                    }

            self::db_query("UPDATE rechargepro_transaction_log SET cordinator_id =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=? WHERE transactionid = ? LIMIT 1",
                array(
                $rechargepro_cordinator,
                "PAID",
                $profile_creator,
                $rechargeproid,
                2,
                $tid));

        }


        $row = self::db_query("SELECT ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid_main));
        $blanance = $row[0]['ac_ballance'];
        $blanance = $blanance + $amount;
        self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
            array($blanance, $rechargeproid_main));
            
            
            
            
$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];




        $status = "SUCCESS";
        $statuscode = "0";
        $statusreference = date("mdHis") . self::RandomString(4, 4) . "_" . $tid;


        $result = '{"details":{"Product":"TRANSFER","Reference Number":"' . $statusreference .
            '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';


        self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
            array(
            $status,
            $statuscode,
            $statusreference,
            1,
            $result,
            $tid));


        $message = "Hey $name,<br />
$amount was transfered to your wallet.<br />
Thank you,<br />
rechargeproPay";
        self::notification($rechargeproid_main, $message, 1);

        //self::update_reward($rechargeproid,$amount,"TRANSFER");
        self::update_reward($rechargeproid_main, $amount, "TOPUP");

        self::que_rechargepropay_mail($tid, $email, "success");


        return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                "status" => "Accepted",
                "TransactionID" => $tid,
                "details" => $result));


    }


    public function auth_topup($parameter)
    {


        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Please Login First");
        }

        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        $amount = self::cleandigit(urldecode($parameter['amount']));


        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
        }


        #LASER
        $rechargeproid = "";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
        }


        if (empty($rechargeproid)) {
            return array("status" => "100", "message" => "Please Login First");
        }


        $row = self::db_query("SELECT name, mobile, email, rechargeproid FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $name = $row[0]['name'];
        $mobile = $row[0]['mobile'];
        $email = $row[0]['email'];
        $rechargeproid_main = $row[0]['rechargeproid'];

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email) VALUES (?,?,?,?,?,?,?,?)",
            array(
            $rechargeproid,
            $ip,
            "TOPUP",
            "TOPUP",
            $rechargeproid_main,
            $amount,
            $mobile,
            $email));


        return array("status" => "200", "message" => array(
                "ac" => $name,
                "name" => $name,
                "amount" => $amount,
                "tid" => $insertid));
    }


    public function pay_topup($parameter)
    {


        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        $tid = $parameter['tid'];

        $tid = urldecode($parameter['tid']);


        $row = self::db_query("SELECT rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['rechargepro_subservice'];
        $rechargeproid_main = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $result = $row[0]['rechargepro_print'];
        $transaction_date = date('Y-m-d', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

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


        $row = self::db_query("SELECT ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid_main));
        $blanance = $row[0]['ac_ballance'];
        $blanance = $blanance + $amount;


        $tmpbal = ($amount * 1.5) / 100;
        $blanance = $blanance - $tmpbal;

        self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
            array($blanance, $rechargeproid_main));


        $status = "SUCCESS";
        $statuscode = "0";
        $statusreference = date("mdHis") . self::RandomString(4, 4) . "_" . $tid;


        $result = '{"details":{"Product":"TOPUP","Reference Number":"' . $statusreference .
            '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';


        self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
            array(
            $status,
            $statuscode,
            $statusreference,
            1,
            $result,
            $tid));


        $message = "Hey $name,<br />
$amount was transfered to your wallet.<br />
Thank you,<br />
rechargeproPay";
        self::notification($rechargeproid_main, $message, 1);
        self::update_reward($rechargeproid_main, $amount, "TOPUP");
        self::que_rechargepropay_mail($tid, $email, "success");


        return array("status" => "200", "message" => array(
                "status" => "Accepted",
                "TransactionID" => $tid,
                "details" => $result));


    }


    public function update_reward($rechargeproid, $amount, $what)
    {

        self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,rechargepro_service,rechargepro_subservice,amount,transaction_reference,rechargepro_status_code,rechargepro_status,rechargepro_print) VALUES (?,?,?,?,?,?,?,?)",
            array(
            $rechargeproid,
            $what,
            $what,
            $amount,
            $what,
            "1",
            "PAID",
            '{"details":{"' . $what . '":"' . $amount . '","TRANSACTION STATUS","DONE"}}'));

    }


}
?>