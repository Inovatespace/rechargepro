<?php
class phedc extends Api
{

    //201804191335
    //201804191330
    //KEDCO
    //54150432331
    //2018-08-03 12:03:52


    public function __construct($method)
    {
    $this->username = "nebolisa@vertistechnologiesltd.com"; //"sandbox@vtpass.com";//
    $this->password = "Change@123"; //"sandbox";//password
    $this->host = 'https://vtpass.com/api';//'https://sandbox.vtpass.com/api';//;
    //'1111111111111'

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
        
        
        if (in_array($service, array("BIA", "BIB"))) {
            if (strlen($accountnumber) > 14) {
                $accountnumber = substr($accountnumber, 2);
            }
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

        if ($amount < 10) {
            return array("status" => "100", "message" => "Invalid Amount");
        }
        
        
        $servicetype = "postpaid";
       if ($service == "BIA") {
           $servicetype = "prepaid";
        }
        
        



        $url = $this->host."/merchant-verify";
        $post_string = array(
  'serviceID'=> "portharcourt-electric", //integer
  'billersCode' => $accountnumber,
  'variation_code'=>$servicetype // unique for every transaction
    );

        $header = array(
                "accept: application/json, application/*+json",
                "cache-control: no-cache",
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "content-type: application/json");

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_USERPWD,$this->username.":" .$this->password);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        //curl_setopt($soap_do, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
       // //curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        // curl_setopt($soap_do, CURLOPT_USERPWD, $this->username.":".$this->password);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);
        
    file_put_contents("seun.php",$result);

        if ($code != 200){
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }


        $array = json_decode($result, true);
      

        if (!isset($array['code'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1b");
        }


        if (!isset($array['content'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again2");
        }


        if (!isset($array['content']['Customer_Name'])) {
            return array("status" => "100", "message" =>
                    "Wrong meter number or unable to reach PHED system at the moment. Please try Again");
        }


        $response = $array['content'];




        //
        //phoneNumber
        //
        $district = $response['Customer_Phone'];
        $name = $response['Customer_Name'];

        $thirdParty = "";
        $business = "";
        $thirdParty = "";
        $unique = $response['MeterNumber'];
        $address = $response['Address'];


        $name = preg_replace("/[^A-Za-z0-9 ]/", '', $name);
        $address = preg_replace("/[^A-Za-z0-9 ]/", '', $address);


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




 $servicetype = "postpaid";
       if ($service == "BIA") {
           $servicetype = "prepaid";
        }


        $url = $this->host."/pay";
        $post_string = array(
  'serviceID'=> "portharcourt-electric",
  'billersCode' => $accountnumber,
  'type'=>$servicetype,
    'variation_code'=>$servicetype,
  'amount' =>  $amount, 
  'phone' => "08183874966", 
  'request_id' => $statusreference
    );


        $header = array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "cache-control: no-cache",
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "content-type: application/json");

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 60);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_USERPWD,$this->username.":" .$this->password);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        //curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        // curl_setopt($soap_do, CURLOPT_USERPWD, $this->username.":".$this->password);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);
        
  // file_put_contents("ophedcc.php",$result);

        if ($code != 200){
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }


        $array = json_decode($result, true);
      
      
        if (!isset($array['code'])) {
            
               if ($this->proccess_count == 0) {
                $this->proccess_count = 1;
                return self::complete_transaction($parameter);
            }
            
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }



        $myrow = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $myac_ballance = $myrow[0]['ac_ballance'];
        $myprofit_bal = $myrow[0]['profit_bal'];
        $namyname = $myrow[0]['name'];


        if(in_array($array['code'],array("016"))){

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
        
        

        $response = array();


        if (in_array($array['code'],array("000","001"))) {
            
            
             if (in_array($array['code'],array("pending"))) {return array("status" => "300", "message" => "Transaction Pending"); }
             if (in_array($array['response_code'],array("pending"))) {return array("status" => "300", "message" => "Transaction Pending"); }
           

            $status = $array['code'];
            $statuscode = "0";
            $statusreference = $array['response_description'];
            $venstatusreference = $array['requestId'];


            $token = "";
            if (isset($array['token'])) {
                    $token = $array['token'];
            }

                $units = "";
            if (isset($array['token'])) {
                   $units =  $array['units'];
            }

       
                $response['Agent_name']=$namyname;
            if (!empty($token)) {
                $response['Token'] = $token;
                $response['details']['Token'] = $token;
                
                $response['Units'] = $units;
                $response['details']['Units'] = $units;
                
                
                
                $totalpay = $amount+100;
                $response = '{"VendorReference":"'.$statusreference.'","Reference":"'.$venstatusreference.'","MeterNumber":"'.$accountnumber.'","Amount":'.$totalpay.',"ResponseTime":"'.date("Y-m-d H:i:s A").'","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":"'.$token.'","FreeUnits":0,"ReceiptNumber":"7'.$venstatusreference.'","PurchasedUnits":"'.$units.'","DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"'.$name.'","Address":"'.$address.'","Tariff":"Prepaid Residential 1PH 3PH","TariffRate":"192 KWH @ 24.3","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":"'.$token.'","service_charge":"N100","Total_amount":"N'.$totalpay.'"}';
                $response = json_decode($response,true);
                
            }else{
           
          
            $totalamount = $amount+100;
            $response = '{"transactionNumber":"'. $statusreference.'","details":{"customerAddress":"'. $address.'","costOfUnits":null,"ac":null,"meterNumber":null,"tariffIndex":null,"vat":"0","costOfUnit":null,"units":null,"accountNumber":"'. $accountnumber.'","debtPayment":"-","supplyGroupCode":null,"customerName":"'. $name.'","responseCode":"'. $statuscode.'","creditToken":null,"exchangeReference":"'. $venstatusreference.'","receipt":"'. $venstatusreference.'","responseMessage":"'. $statusreference.'","fixedCharge":"0","status":"ACCEPTED","tariffInstance":"-"},"service_charge":"N100","Total_amount":"N'.$totalamount.'"}';

$response = json_decode($response,true);
$response["Transaction Date"] = date("Y-m-d H:i:s");
            }


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));
                
                
                
            if (!isset($parameter['sms'])) {
                if (!empty($token)) {
                    $message = "Token:" . $token . "\r\nAmount:$amount \r\nUnits:" . $units .
                        "\r\nInvoice Number:" . $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                    self::curlit($phone, $message);
                } else {
                    self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                        "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
                }
            }


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
            if (in_array($array['code'],array("014","019","001"))) {


                //Transaction Already Exists
                $response = self::verify_transaction($statusreference);
                
                // file_put_contents("vphedc2.php",json_encode($response));
                 
                 
                $array = $response['message'];
                
                

                //{"code":"001","response_code":"000","response_description":"TRANSACTION QUERY","content":{"requestId":"REC123562","response_code":"016","response_description":"TRANSACTION FAILED","amount":"500.00","created_date":{"date":"2019-08-08 12:19:21.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":""}}
                
           if(!isset($array['code'])){

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
        
                if(in_array($array['content']['response_code'],array("016"))){

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
 
 
 //{"code":"001","response_code":"000","response_description":"TRANSACTION QUERY","content":{"requestId":"2019081835422","response_code":"000","response_description":"TRANSACTION SUCCESSFUL","amount":"10000.00","created_date":{"date":"2019-08-18 15:20:43.000000","timezone_type":3,"timezone":"Africa\/Lagos"},"purchased_code":"Token : 71124975731879219849","meterNumber":"62141398750","customerName":null,"address":null,"token":"71124975731879219849","tokenAmount":"10000","tokenValue":"10000","businessCenter":null,"exchangeReference":"180820191347550","units":"220.6"}}


                if ($array['content']['response_code'] == "000") {
                    
                    $response = array();

                           //{"code":"001","response_code":"000","response_description":"TRANSACTION QUERY","content":{"requestId":"REC1235620","response_code":"000","response_description":"TRANSACTION SUCCESSFUL","amount":"500.00","purchased_code":"Token : 57880316552890870667","meterNumber":"1111111111111","customerName":null,"customerNumber":"825279658801","address":null,"token":"57880316552890870667","tokenAmount":"500","tokenValue":"500","businessCenter":null,"receiptNumber":"1807201963743","units":"15.8","tariff":"30.23000","energyAmount":null,"energyVAT":null}}
                           
                           //{"code":"001","response_code":"000","response_description":"TRANSACTION QUERY","content":{"requestId":"2019081835422","response_code":"000","response_description":"TRANSACTION SUCCESSFUL","amount":"10000.00","purchased_code":"Token : 71124975731879219849","meterNumber":"62141398750","customerName":null,"address":null,"token":"71124975731879219849","tokenAmount":"10000","tokenValue":"10000","businessCenter":null,"exchangeReference":"180820191347550","units":"220.6"}}
                
                //Array ( [status] => 200 [message] => Array ( [code] => 001 [response_code] => 000 [response_description] => TRANSACTION QUERY [content] => Array ( [requestId] => 2019081835422 [response_code] => 000 [response_description] => TRANSACTION SUCCESSFUL [amount] => 10000.00 [created_date] => Array ( [date] => 2019-08-18 15:20:43.000000 [timezone_type] => 3 [timezone] => Africa/Lagos ) [purchased_code] => Token : 71124975731879219849 [meterNumber] => 62141398750 [customerName] => [address] => [token] => 71124975731879219849 [tokenAmount] => 10000 [tokenValue] => 10000 [businessCenter] => [exchangeReference] => 180820191347550 [units] => 220.6 ) ) )

            $status = $array['content']['response_code'];
            $statuscode = "0";
            $statusreference = $array['content']['response_description'];
            $venstatusreference = $array['content']['requestId'];


            $token = "";
            if (isset($array['content']['token'])) {
                    $token = $array['content']['token'];
            }

                $units = "";
            if (isset($array['token'])) {
                   $units =  $array['content']['units'];
            }

       $response['Agent_name']=$namyname;

            if (!empty($token)) {
                $response['Token'] = $token;
                $response['details']['Token'] = $token;
                
                $response['Units'] = $units;
                $response['details']['Units'] = $units;
                
                $totalpay = $amount+100;
                $response = '{"VendorReference":"'.$statusreference.'","Reference":"'.$venstatusreference.'","MeterNumber":"'.$accountnumber.'","Amount":'.$totalpay.',"ResponseTime":"'.date("Y-m-d H:i:s A").'","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":"'.$token.'","FreeUnits":0,"ReceiptNumber":"7'.$venstatusreference.'","PurchasedUnits":"'.$units.'","DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"'.$name.'","Address":"'.$address.'","Tariff":"Prepaid Residential 1PH 3PH","TariffRate":"192 KWH @ 24.3","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":"'.$token.'","service_charge":"N100","Total_amount":"N'.$totalpay.'"}';
                $response = json_decode($response,true);
                
                
            }else{
           
          
            $totalamount = $amount+100;
            $response = '{"transactionNumber":"'. $statusreference.'","details":{"customerAddress":"'. $address.'","costOfUnits":null,"ac":null,"meterNumber":null,"tariffIndex":null,"vat":"0","costOfUnit":null,"units":null,"accountNumber":"'. $accountnumber.'","debtPayment":"-","supplyGroupCode":null,"customerName":"'. $name.'","responseCode":"'. $statuscode.'","creditToken":null,"exchangeReference":"'. $venstatusreference.'","receipt":"'. $venstatusreference.'","responseMessage":"'. $statusreference.'","fixedCharge":"0","status":"ACCEPTED","tariffInstance":"-"},"service_charge":"N100","Total_amount":"N'.$totalamount.'"}';

$response = json_decode($response,true);
$response["Transaction Date"] = date("Y-m-d H:i:s");

            }


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));
                
                
                
            if (!isset($parameter['sms'])) {
                if (!empty($token)) {
                    $message = "Token:" . $token . "\r\nAmount:$amount \r\nUnits:" . $units .
                        "\r\nInvoice Number:" . $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                    self::curlit($phone, $message);
                } else {
                    self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                        "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
                }
            }


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
          $status = $array['content']['response_code'];
            $statuscode = "0";
            $statusreference = $array['content']['response_description'];
            $venstatusreference = "";
     

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
                
            $status = $array['code'];
            $statuscode = "0";
            $statusreference = $array['response_description'];
            $venstatusreference = "";
     

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


    }


    function verify_transaction($tref)
    {



        $url = $this->host."/query";
        $post_string = array(
  'request_id' => $tref // unique for every transaction
    );


        $header = array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "cache-control: no-cache",
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "content-type: application/json");

        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 30);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_USERPWD,$this->username.":" .$this->password);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        //curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        // curl_setopt($soap_do, CURLOPT_USERPWD, $this->username.":".$this->password);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);

  // file_put_contents("vphedc.php",$result);

        if ($code != 200){
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }


        $array = json_decode($result, true);
      

        if (!isset($array['code'])) {
            return array("status" => "100", "message" =>
                    "Invalid meter details or network Error, try again1");
        }



        return array("status" => "200", "message" => $array);


    }


}
?>