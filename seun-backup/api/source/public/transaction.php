<?php
class transaction extends Api
{
    
    
    private function credential($k,$s){
        
        if($k != "1234QWER5678TYUI" || $s != "1234:QWER:5678:TYUI"){
            return false;
        }
        
        return true;
    }
    
    

    public function __construct($method)
    {

    }
    
        public function bills_cat_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
        $row = self::db_query("SELECT subcategory_id,name FROM rechargepro_subcategory ORDER BY name ASC",
            array());
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['subcategory_id']] = $row[$dbc]['name'];
        }
        return $return;
    }
    
        public function bills_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
        if (!isset($parameter['cat'])) {
            return array("status" => "100", "message" => "Invalid Category");
        }
        
        $cat = $parameter['cat'];
        $row = self::db_query("SELECT services_key,service_name FROM rechargepro_services WHERE services_category = ? AND service_subcategory =?  AND status = '1' ORDER BY service_name ASC",
            array(7, $cat));
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = $row[$dbc]['service_name'];
        }
        return $return;
    }


    public function airtime_list($parameter)
    {
       $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
       
        include "source/pro/airtime_data.php";
        $airtime_data = new airtime_data("POST");
        return $airtime_data->network_list($parameter);
    }


    public function bank_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
        include "source/pro/bank_transfer.php";
        $bank_transfer = new bank_transfer("POST");
        return $bank_transfer->bank_list($parameter);
    }
    
        public function data_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
        include "source/pro/airtime_data.php";
        $airtime_data = new airtime_data("POST");
        return $airtime_data->data_network_list($parameter);
    }

    public function data_bundle_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        include "source/pro/airtime_data.php";
        $airtime_data = new airtime_data("POST");
        $v = $airtime_data->available_bundle($parameter);
        if ($v['status'] == "100") {
            return array();
        }

        $items = $v['message']['bundles'];

        $return = array();
        for ($dbc = 0; $dbc < count($items); $dbc++) {

            $nae = $items[$dbc]['name'];

            if (empty($items[$dbc]['name'])) {
                $nae = $items[$dbc]['allowance'];
            }

            $return[$items[$dbc]['code']] = $nae . "_" . $items[$dbc]['price'];
        }
        return $return;
    }



    public function electricity_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
        
        include "source/pro/electricity.php";
        $electricity = new electricity("POST");
        return $electricity->utility_list($parameter);
    }


    public function tv_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
        include "source/pro/tv.php";
        $tv = new tv("POST");
        return $tv->network_list($parameter);
    }

    public function tv_banquet_list($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        include "source/pro/tv.php";
        $tv = new tv("POST");
        $bq = $tv->available_bounquet(array("service" => $parameter['service']));
        $bg = $bq['message'];


        if (!isset($bg['items'])) {
            return array();
        }

        $return = array();
        for ($i = 0; $i < count($bg['items']); $i++) {
            $return[$bg['items'][$i]['code']] = $bg['items'][$i]['price'] . " " . $bg['items'][$i]['name'];
        }

        return $return;
    }


    public function status($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid Key");
        }

        return array("status" => "200", "message" => array("balance" => "10000",
                    "profit" => "25"));
    }

    public function initiate_transaction($parameter)
    {
        //privatekey
        
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
        



        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }

        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Invalid amount");
        }

        if (!isset($parameter['accountnumber'])) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }


        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid Key");
        }

        if (!isset($parameter['token'])) {
            return array("status" => "100", "message" => "Invalid Token");
        }
        
        
        if(!self::credential($parameter['private_key'],$parameter['token'])){
          return array("status" => "100", "message" => "Invalid Auth");  
        }

        //make sure serial is registered as API

        $parameter['accountnumber'] = self::clean_transaction($parameter['accountnumber']);
        $parameter['amount'] = $parameter['amount'];
        $parameter['service'] = self::clean_transaction($parameter['service']);
        $parameter['mobile'] = self::clean_transaction($parameter['mobile']);
        $parameter['serial'] = self::clean_transaction($parameter['token']);
        $Amount = $parameter['amount'];


        //$key = urldecode(trim($parameter["privatekey"]));
        //$row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
          //  array($key));
       // $rechargeproid = $row[0]['rechargeproid'];

        //$row = self::db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",array($rechargeproid));
        //$parameter['private_key'] = $row[0]['public_secret'];


        //if (empty($row[0]['public_secret'])) {
        //    return array("status" => "100", "message" => "Invalid Key");
        //}


        if($parameter['private_key'] == "1234QWER5678TYUI"){
         $parameter['private_key']    = "UM8NHD38FBAZA70J8M1QY51NRVTS2UM7THT7SXED";
        }

        if (in_array($parameter['service'], array("BANKTRANSFER"))) {

            $parameter['account'] = $parameter['accountnumber'];
            $parameter['narration'] = "";

            $parameter['bankcode'];

            include "source/pro/bank_transfer.php";
            $bank_transfer = new bank_transfer("POST");
            $return = $bank_transfer->auth_transfer($parameter);


            if ($return['status'] == 100) {
                return array("status" => "100", "message" => $return['message']);
            } else {

                return array("status" => "200", "message" => array(
                        "name" => $return['message']['name'],
                        "amount" => $return['message']['amount'],
                        "totalamount" => $return['message']['totalamount'],
                        "tfee" => $return['message']['tfee'],
                        "tid" => $return['message']['tid']));


            }

        }


        if (in_array($parameter['service'], array(
            "ACC",
            "AEC",
            "ALC",
            "ADC",
            "ANB",
            "BGA"))) {
            //bundle
            include "source/pro/airtime_data.php";
            $airtime_data = new airtime_data("POST");
            $return = $airtime_data->auth_data($parameter);


            if ($return['status'] == 100) {
                return array("status" => "100", "message" => $return['message']);
            } else {
                return array("status" => "200", "message" => array(
                        "name" => $return['message']['name'],
                        "amount" => $return['message']['amount'],
                        "totalamount" => $return['message']['totalamount'],
                        "tfee" => $return['message']['tfee'],
                        "tid" => $return['message']['tid']));
            }

        }


        if (in_array($parameter['service'], array(
            "2351",
            "2352",
            "2353",
            "2354",
            "ANA"))) {
            include "source/pro/airtime_data.php";
            $airtime_data = new airtime_data("POST");
            $return = $airtime_data->auth_airtime($parameter);


            if ($return['status'] == 100) {
                return array("status" => "100", "message" => $return['message']);
            } else {
                return array("status" => "200", "message" => array(
                        "name" => $return['message']['name'],
                        "amount" => $return['message']['amount'],
                        "totalamount" => $return['message']['totalamount'],
                        "tfee" => $return['message']['tfee'],
                        "tid" => $return['message']['tid']));
            }

        }


        if (in_array($parameter['service'], array("AVC","AVB","IBB","IBP",
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
                return array("status" => "100", "message" => $return['message']);
            } else {
                
                                
            $outstanding = "";
            if(isset($response['outstanding'])){
                $outstanding = $response['outstanding'];
            }
            
            $MinVendAmount = 500;
            if(isset($response['minimum'])){
                $MinVendAmount = $response['minimum'];
            }
            
                return array("status" => "200", "message" => array(
                        "name" => $return['message']['name'],
                        "amount" => $return['message']['amount'],
                        "totalamount" => $return['message']['totalamount'],
                        "tfee" => $return['message']['tfee'],
                        "tid" => $return['message']['tid'],
                "outstanding"=>$outstanding,
                "minimum"=>$MinVendAmount));
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
                return array("status" => "100", "message" => "Invalid Response Contact Support");
            }

            $banquet = array();
            foreach ($available_bounquet['message']['items'] as $key => $value) {
                $banquet[$value['code']] = $value['price'];
            }

            $amount_array = array_values($banquet);
            if (!in_array($Amount, $amount_array)) {
                return array("status" => "100", "message" => "Invalid Amount");
            }


            $parameter['code'] = array_search($Amount, $banquet);

            $return = $tv->auth_transaction($parameter);

            if ($return['status'] == 100) {
                return array("status" => "100", "message" => $return['message']);
            } else {

                return array("status" => "200", "message" => array(
                        "name" => $return['message']['name'],
                        "amount" => $return['message']['amount'],
                        "totalamount" => $return['message']['totalamount'],
                        "tfee" => $return['message']['tfee'],
                        "tid" => $return['message']['tid']));

            }

        }


        return array("status" => "100", "message" => "Invalid Request4");

    }


    public function verify_transaction($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }
       
        return self::complete_transaction($parameter);
    }

    public function complete_transaction($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }

        // $parameter['tid'] = 3248; //$tid;

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction ID");
        }

        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid Key");
        }

        if (!isset($parameter['token'])) {
            return array("status" => "100", "message" => "Invalid Token");
        }
        
        
        
       if (isset($parameter['status'])) {
        if($parameter['status'] == "100"){
            $array = array("Failed Transaction","Invalid Account Number","Server Error");
            $rand = rand(0,2);
            return array("status" => "100", "message" => $array[$rand]);
            }
        }
        
        
        if (isset($parameter['status'])) {
        if($parameter['status'] == "300"){
            return array("status" => "300", "message" => "Pending Transaction");
            }
        }

        //make sure serial is registered as API


        $tid = self::clean_transaction($parameter['tid']);
        $parameter['tid'] = $tid;
        $parameter['serial'] = self::clean_transaction($parameter['token']);


        $row = self::db_query("SELECT rechargepro_subservice FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $rechargeproservice = $row[0]['rechargepro_subservice'];

        if (empty($rechargeproservice)) {
            return array("status" => "100", "message" => "Invalid Transaction ID");
        }

        $row = self::db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($rechargeproservice));




    $r = "";
        if ($rechargeproservice == "BANK TRANSFER") { return json_decode('{"status":"200","message":{"Product":"BANK TRANSFER","Account Number":"290880830","Account Name":"Seun makinde.","Narration":"081","Reference Number":"1905220010330228","Transfer Amount":"10000","Transaction fee":"25","Total Amount":"10025","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}',true);}


        if ($row[0]['services_category'] == "1") { return json_decode('{"status":"200","message":{"VendorReference":"201905217882","Reference":"T19141220604040ec6036cfc4afb9f40","MeterNumber":"07077485618","Amount":1000,"ResponseTime":"5\/21\/2019 10:06:05 PM","UtilityAmtVatExcl":47.62,"Vat":0,"TerminalId":null,"Token":"5332-8015-8731-1881-2456","FreeUnits":0,"ReceiptNumber":"7011201905211424348","PurchasedUnits":39.2,"DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"PRINCE EHIE VICTOR .  ","Address":"HOUSE 17 FLANBOYANT STREET, FHA, NYANYA FHA Und St. Vendors Center","Tariff":"Prepaid Residential 1PH 3PH","TariffRate":"952.38 KWH @ 5","VatInvoiceNumber":null,"LastPurchase":-18},"ResponseCode":"100","ResponseMessage":"SUCCESSFUL","service_charge":"N100","Total_amount":"N1100"}}',true);}


        if ($row[0]['services_category'] == "2" || $row[0]['services_category'] == "3") { return json_decode('{"status":"200","message":{"status": 200,"message": ["code : RECHARGECOMPLETE","topupamount : 78","operatorname : 9mobile","Phone : 08183874966"]}}',true);}


        if ($row[0]['services_category'] == "5") { return json_decode('{"status":"200","message":{"transactionNumber":27450985,"details":{"customerCareReferenceId":null,"exchangeReference":"46933566","errorMessage":"","errorCode":null,"errorId":null,"auditReferenceNumber":"6b8ae362-8064-4253-ac64-a57d5720a2f7","done":true,"status":"ACCEPTED"},"service_charge":"N100","Total_amount":"N2000","Card Number":"4601825540","Bouquet":"GOtv Plus","Amount Paid":"1900"}}',true);}

        return array("status" => "100", "message" => "Invalid Request5");

    }


    public function print_lasttransaction($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }

        $key = urldecode(trim($parameter["privatekey"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key));
        $rechargeproid = $row[0]['rechargeproid'];

        $row = self::db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $parameter['private_key'] = $row[0]['public_secret'];


        if (empty($row[0]['public_secret'])) {
            return array("status" => "100", "message" => "Invalid Key");
        }


        $row = self::db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE rechargeproid = ? AND 	rechargepro_status = 'PAID' ORDER BY transactionid DESC LIMIT 1",
            array($rechargeproid));

        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }
        $parameter['tid'] = $row[0]['transactionid'];
        return self::complete_transaction($parameter);

    }


    public function print_usingaccount($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }

        if (!isset($parameter['account'])) {
            return array("status" => "100", "message" => "Invalid Account");
        }


        $key = urldecode(trim($parameter["privatekey"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key));
        $rechargeproid = $row[0]['rechargeproid'];

        $row = self::db_query("SELECT public_secret FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $parameter['private_key'] = $row[0]['public_secret'];


        if (empty($row[0]['public_secret'])) {
            return array("status" => "100", "message" => "Invalid Key");
        }

        $account = $parameter['account'];


        $row = self::db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE account_meter = ? AND 	rechargepro_status = 'PAID' ORDER BY transactionid DESC LIMIT 1",
            array($account));
        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }
        $parameter['tid'] = $row[0]['transactionid'];
        return self::complete_transaction($parameter);
    }


    public function success_print($parameter)
    {
        $data_back = json_decode(file_get_contents('php://input'));
       if($data_back) {$parameter = json_decode(json_encode($data_back),true); }

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


        return array("status" => "200", "message" => $c);

    }


    public function clean_transaction($c)
    {
        return preg_replace("/[^a-zA-Z0-9\-]/", "", $c);
    }


}



?>