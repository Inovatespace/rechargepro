<?php
class transaction extends Api
{

    public function __construct($method)
    {

    }


    public function initiate_transaction($parameter)
    {
        //privatekey

        if (!isset($parameter['service'])) {
            return "bad@Invalid Service";
        }
        if (!isset($parameter['mobile'])) {
            return "bad@Invalid mobile";
        }

        if (!isset($parameter['amount'])) {
            return "bad@Invalid amount";
        }

        if (!isset($parameter['accountnumber'])) {
            return "bad@Invalid accountnumber";
        }

        $parameter['accountnumber'] = self::clean_transaction($parameter['accountnumber']);
        $parameter['amount'] = self::clean_transaction($parameter['amount']);
        $parameter['service'] = self::clean_transaction($parameter['service']);
        $parameter['mobile'] = self::clean_transaction($parameter['mobile']);
        $parameter['serial'] = self::clean_transaction($parameter['serial']);


        $key = urldecode(trim($parameter["privatekey"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key));
        $rechargeproid = $row[0]['rechargeproid'];

        $row = self::db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $parameter['private_key'] = $row[0]['public_secret'];


        if (empty($row[0]['public_secret'])) {
            return "bad@Invalid Key";
        }


        if (in_array($parameter['service'], array("BANKTRANSFER"))) {

            $parameter['account'] = $parameter['accountnumber'];
            $parameter['narration'] = "";

            $parameter['bankcode'];

            include "source/pro/bank_transfer.php";
            $bank_transfer = new bank_transfer("POST");
            $return = $bank_transfer->auth_transfer($parameter);


            if ($return['status'] == 100) {
                return "bad@" . $return['message'];
            } else {
                return "ok@" . $return['message']['name'] . "@" . $return['message']['amount'] .
                    "@" . $return['message']['tid'];
            }

        }


        if (in_array($parameter['service'], array(
            "2351",
            "2352",
            "2353",
            "2354"))) {
            include "source/pro/airtime_data.php";
            $airtime_data = new airtime_data("POST");
            $return = $airtime_data->auth_airtime($parameter);


            if ($return['status'] == 100) {
                return "bad@" . $return['message'];
            } else {
                return "ok@" . $return['message']['name'] . "@" . $return['message']['amount'] .
                    "@" . $return['message']['tid'];
            }

        }


        if (in_array($parameter['service'], array(
            "AED",
            "AEP",
            "BIA",
            "BIB",
            "EPP",
            "EKP",
            "IKP",
            "IPP",
            "BOA",
            "BOB"))) {
            include "source/pro/electricity.php";
            $electricity = new electricity("POST");
            $return = $electricity->auth_transaction($parameter);


            if ($return['status'] == 100) {
                return "bad@" . $return['message'];
            } else {
                return "ok@" . $return['message']['name'] . "@" . $return['message']['amount'] .
                    "@" . $return['message']['tid'];
            }


        }

        if (in_array($parameter['service'], array(
            "AQA",
            "AQC",
            "AWA"))) {
            //code from amount

            include "source/pro/tv.php";
            $tv = new tv("POST");

            $available_bounquet = $tv->available_bounquet(array("service" => $parameter['service']));
            if ($available_bounquet['status'] == 100) {
                return "bad@Invalid Response Contact Support";
            }

            $banquet = array();
            foreach ($available_bounquet['message']['items'] as $key => $value) {
                $banquet[$value['code']] = $value['price'];
            }

            $amount_array = array_values($banquet);
            if (!in_array($Amount, $amount_array)) {
                return "bad@Invalid Amount";
            }


            $parameter['code'] = array_search($Amount, $banquet);

            $return = $tv->auth_transaction($parameter);

            if ($return['status'] == 100) {
                return "bad@" . $return['message'];
            } else {

                return "ok@" . $return['message']['name'] . "@" . $return['message']['amount'] .
                    "@" . $return['message']['tid'];

            }

        }


        return "bad@Invalid Request4";

    }


    public function complete_transaction($parameter)
    {

        // $parameter['tid'] = 3248; //$tid;

        if (!isset($parameter['tid'])) {
            return "bad@Invalid Transaction ID";
        }


        $key = urldecode(trim($parameter["privatekey"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key));
        $rechargeproid = $row[0]['rechargeproid'];

        $row = self::db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $parameter['private_key'] = $row[0]['public_secret'];


        if (empty($row[0]['public_secret'])) {
            return "bad@Invalid Key";
        }


        $tid = self::clean_transaction($parameter['tid']);
        $parameter['tid'] = $tid;
        $parameter['serial'] = self::clean_transaction($parameter['serial']);


        $row = self::db_query("SELECT rechargepro_subservice FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $rechargeproservice = $row[0]['rechargepro_subservice'];

        if (empty($rechargeproservice)) {
            return "bad@Invalid Transaction ID";
        }

        $row = self::db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($rechargeproservice));


        if ($rechargeproservice == "BANK TRANSFER") {
            include "source/pro/bank_transfer.php";
            $bank_transfer = new bank_transfer("POST");
            $return = $bank_transfer->complete_transaction($parameter);
            if (in_array($return['status'], array(100, 300))) {
                return "bad@" . $return["message"];
            } else {
                return self::success_print($parameter);
            }

        }


        if ($row[0]['services_category'] == "1") {
            include "source/pro/electricity.php";
            $electricity = new electricity("POST");
            $return = $electricity->complete_transaction($parameter);
            if (in_array($return['status'], array(100, 300))) {
                return "bad@" . $return["message"];
            } else {
                return self::success_print($parameter);
            }

        }


        if ($row[0]['services_category'] == "2") {
            include "source/pro/airtime_data.php";
            $airtime_data = new airtime_data("POST");
            $return = $airtime_data->complete_transaction($parameter);
            if (in_array($return['status'], array(100, 300))) {
                return "bad@" . $return["message"];
            } else {
                return self::success_print($parameter);
            }

        }


        if ($row[0]['services_category'] == "5") {
            include "source/pro/tv.php";
            $tv = new tv("POST");
            $return = $tv->complete_transaction($parameter);
            $response = array();
            $response['BillerID'] = $BillerID;
            if (in_array($return['status'], array(100, 300))) {
                return "bad@" . $return["message"];
            } else {
                return self::success_print($parameter);
            }

        }

        return "bad@Invalid Request5";

    }


    public function print_lasttransaction($parameter)
    {

        $key = urldecode(trim($parameter["privatekey"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key));
        $rechargeproid = $row[0]['rechargeproid'];

        $row = self::db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $parameter['private_key'] = $row[0]['public_secret'];


        if (empty($row[0]['public_secret'])) {
            return "bad@Invalid Key";
        }


        $row = self::db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE rechargeproid = ? AND 	rechargepro_status = 'PAID' ORDER BY transactionid DESC LIMIT 1",
            array($rechargeproid));

        if (empty($row[0]['transactionid'])) {
            return "bad@Invalid Transaction";
        }
        $parameter['tid'] = $row[0]['transactionid'];
        return self::complete_transaction($parameter);

    }


    public function print_usingaccount($parameter)
    {

        if (!isset($parameter['account'])) {
            return "bad@Invalid Account";
        }
        
        
        
        $key = urldecode(trim($parameter["privatekey"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key));
        $rechargeproid = $row[0]['rechargeproid'];

        $row = self::db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $parameter['private_key'] = $row[0]['public_secret'];


        if (empty($row[0]['public_secret'])) {
            return "bad@Invalid Key";
        }

        $account = $parameter['account'];


        $row = self::db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE account_meter = ? AND 	rechargepro_status = 'PAID' ORDER BY transactionid DESC LIMIT 1",
            array($account));
        if (empty($row[0]['transactionid'])) {
            return "bad@Invalid Transaction";
        }
        $parameter['tid'] = $row[0]['transactionid'];
        return self::complete_transaction($parameter);
    }


    public function success_print($parameter)
    {

        $tid = $parameter['tid'];

        $row = self::db_query("SELECT rechargepro_print,rechargeproid,transactionid,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $rechargepro_print = $row[0]['rechargepro_print'];
        $transaction_date = $row[0]['transaction_date'];
        $rechargeproid = $row[0]['rechargeproid'];


        $response = json_decode($rechargepro_print, true);

        if (isset($response['details'])) {
            $temarray = $response['details'];
        } else {
            $temarray = $response;
        }


        $response = self::array_flatten($temarray);


        if (!empty($response)) {
            if (count($response) > 0) {
                foreach (self::myarray() as $a) {

                    if (array_key_exists($a, $response)) {
                        unset($response[$a]);
                    }

                }
            }
        }

        $c = array();
        foreach ($response as $a => $b) {
            $c[] = self::clean_transaction($a) . " : " . self::clean_transaction($b);
        }

        $receiptid = $rechargeproid . "_" . $tid;

        $tokin = "";
        if (isset($response['Token'])) {
            $tokin = $response['Token'];
        }
        if (isset($response['token'])) {
            $tokin = $response['token'];
        }
        if (isset($response['Pin'])) {
            $tokin = $response['Pin'];
        }
        if (isset($response['pin'])) {
            $tokin = $response['pin'];
        }

        return "ok@" . $receiptid . "@" . $transaction_date . "@" . implode("*", $c) .
            "@" . $tokin;
    }


    public function clean_transaction($c)
    {
        return preg_replace("/[^a-zA-Z0-9\-]/", "", $c);
    }


}



?>