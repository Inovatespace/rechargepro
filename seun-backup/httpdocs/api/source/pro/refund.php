<?php
class refund extends Api
{
    //KEDCO
    function fix_phone($mobile)
    {
        $mobile = "234" . substr($mobile, 1);
        return $mobile;
    }

    function refund_now($parameter)
    {

        //sleep(1);
        $tid = htmlentities($parameter['tid']);

        $row = self::db_query("SELECT rechargepro_print,transaction_date,account_meter,phone,rechargepro_service,cordinator_id,agent_id,rechargeproid,rechargepro_subservice,amount,thirdPartycode,refererprofit,agentprofit,cordprofit,rechargeproprofit,rechargepro_service_charge FROM rechargepro_transaction_log WHERE transactionid = ? AND rechargepro_status_code = ? AND refund = '0' AND bank_ref ='' LIMIT 1",
            array($tid, "0"));
        $amount_to_charge = $row[0]['amount'];
        $rechargepro_subservice = $row[0]['rechargepro_subservice'];
        $rechargepro_service = $row[0]['rechargepro_service'];
        $rechargepro_cordinator = $row[0]['cordinator_id'];
        $agent_id = $row[0]['agent_id'];
        $rechargeproid = $row[0]['rechargeproid'];
        $account_meter = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        $rechargepro_print = $row[0]['rechargepro_print'];

        $refererprofit = $row[0]['refererprofit'];
        $agentprofit = $row[0]['agentprofit'];
        $cordprofit = $row[0]['cordprofit'];
        $rechargepro_print = $row[0]['rechargepro_print'];
        $rechargepro_service_charge = $row[0]['rechargepro_service_charge'];


        if (empty($rechargeproid)) {
            exit;
        }


        $run = 0;
        if (in_array($rechargepro_service, array(
            "ADD",
            "JOD",
            "JOP"))) {
            $run = 1;
        }


        if (in_array($rechargepro_subservice, array(
            "BOA",
            "BOB"))) {
            $run = 1;
            $vref = $transaction_date . $tid;
            $return = self::verify_vertibra($vref, $rechargepro_subservice);
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }

        if (in_array($rechargepro_subservice, array(
            "2351",
            "ACC",
            "2352",
            "AEC",
            "ANB",
            "ANA",
            "WEC",
            "BGA",
            "2353",
            "ALC","2354","ADC"))) {
            $run = 1;
            $return = self::verify_mobifin(array("tid" => $tid));
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }

        if (in_array($rechargepro_subservice, array("2354", "ADC"))) {
            //$run = 1;
            //$return = self::vendMtn($tid, self::fix_phone($account_meter),$amount_to_charge,$thirdPartyCode);
            //if($return == "1"){
            return "200";
            // }
        }


        if (in_array($rechargepro_subservice, array("eeeeeeeeeeee"))) {
            $run = 1;
            $return = self::confirm_payu(array("tid" => $tid));
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }
        
        
          if (in_array($rechargepro_subservice, array("AEP","AED"))) {
            $run = 1;
            $vref = $transaction_date . $tid;
            $return = self::search_paga($vref, $tid,$service);
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }

        if (in_array($rechargepro_subservice, array("AED","AEE","AEF"))) {
            $run = 1;
            $vref = $transaction_date . $tid;
            $return = self::search_power_aedc($vref, $tid);
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }
        
        
                if (in_array($rechargepro_subservice, array("AEP"))) {
            $run = 1;
            $vref = $transaction_date . $tid;
            $return = self::search_power_aedc_post($vref, $tid);
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }
        
                if (in_array($rechargepro_subservice, array("BIA","BIB"))) {
            $run = 1;
            $return = self::confirm_vtpass(array("tid" => $tid));
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }


        if (in_array($rechargepro_subservice, array(
            "AQA",
            "AQC",
            "AEP","EPP", "EKP","IKP", "IPP","IBB", "IBP","AVC","AVB"))) {
            $run = 1;
            $return = self::confirm_cap(array("tid" => $tid));
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }


        if ($run == 3444440) {
            $run = 1;
            $statusreference = $transaction_date . $tid;
            $return = self::post_switch($statusreference, $tid);
            if ($return == "1") {
                return "200";
            }

            if ($return == "3") {
                return "300";
            }
        }


        //set refund = 1
        self::db_query("UPDATE rechargepro_transaction_log SET refund=?  WHERE transactionid = ?",
            array("1", $tid));


        //if(empty($percentage)){exit;}
        if (!empty($account_meter)) {

            $row = self::db_query("SELECT rechargeprorole, ac_ballance,profit_bal, profile_creator , name, email,merge_ac FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($rechargeproid));
            $rechargeprorole = $row[0]['rechargeprorole'];
            $myballance = $row[0]['ac_ballance'];
            $myprofitbal = $row[0]['profit_bal'];
            $profile_creator = $row[0]['profile_creator'];
            $merge_ac = $row[0]['merge_ac'];
            $name = $row[0]['name'];
            $email = $row[0]['email'];

            $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $rechargeproid .
                "_" . $tid;
            self::db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",
                array(
                "0",
                "REFUND",
                $what));

            if ($rechargeprorole <= 3) {


                self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                    array(
                    $rechargeproid,
                    $rechargeproid,
                    $agentprofit,
                    "-REWARD"));

                if ($merge_ac == 0) {
                    $newballance = $myballance + $amount_to_charge + $rechargepro_service_charge;
                    $newprofit = $myprofitbal - $agentprofit;
                    self::db_query("UPDATE rechargepro_account SET ac_ballance = ?, profit_bal=? WHERE rechargeproid = ? LIMIT 1",
                        array(
                        $newballance,
                        $newprofit,
                        $rechargeproid));
                } else {
                    $newballance = ($myballance + $amount_to_charge + $rechargepro_service_charge) -
                        $agentprofit;
                    self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $rechargeproid));
                }


                if ($rechargepro_cordinator > 0 || $rechargeprorole == 1) {

                    if ($rechargeprorole == 1) {
                        $rechargepro_cordinator = $rechargeproid;
                    }

                    self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $rechargepro_cordinator,
                        $rechargeproid,
                        $cordprofit,
                        "-COR_REWARD"));


                    $row = self::db_query("SELECT profit_bal,ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($rechargepro_cordinator));


                    if ($merge_ac == 0) {
                        $cordinator_ballance = $row[0]['profit_bal'];
                        $cornewballance = $cordinator_ballance - $cordprofit;
                        self::db_query("UPDATE rechargepro_account SET profit_bal=? WHERE rechargeproid = ? LIMIT 1",
                            array($cornewballance, $rechargepro_cordinator));
                    } else {
                        $cordinator_ballance = $row[0]['ac_ballance'];
                        $cornewballance = $cordinator_ballance - $cordprofit;
                        self::db_query("UPDATE rechargepro_account SET ac_ballance=? WHERE rechargeproid = ? LIMIT 1",
                            array($cornewballance, $rechargepro_cordinator));
                    }
                }
                //  }

            }


            if ($rechargeprorole > 3) {
                if ($profile_creator > 0 && $refererprofit > 0) {

                    $row = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($profile_creator));


                    self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $profile_creator,
                        $rechargeproid,
                        $refererprofit,
                        "-USER_REWARD"));


                    if ($merge_ac == 0) {
                        $creator_ballance = $row[0]['profit_bal'];
                        $creator_ballance = $creator_ballance - $refererprofit;
                        self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                            array($creator_ballance, $profile_creator));
                    } else {
                        $creator_ballance = $row[0]['ac_ballance'];
                        $creator_ballance = $creator_ballance - $refererprofit;
                        self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                            array($creator_ballance, $profile_creator));
                    }

                }

                //add 100 naira

                $newballance = $myballance + $amount_to_charge + $rechargepro_service_charge;
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));

            }


            //  if ($bill_formular == 1){
            //  $fullf = $cordinator_percentage+$percentage+$bill_rechargeprofull_percentage+$service_provider_percentage;
            //  $row = self::db_query("SELECT ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($rechargeproid));
            //   $myballance = $row[0]['ac_ballance'];
            //
            //   $myballance = $myballance + $fullf;
            //    self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($myballance, $rechargeproid));
            //       }


        }


        self::db_query("INSERT INTO rechargepro_refund (rechargeproid,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,transactionid,rechargepro_status_code,rechargepro_status) VALUES (?,?,?,?,?,?,?,?,?)",
            array(
            $rechargeproid,
            $rechargepro_service,
            $rechargepro_subservice,
            $account_meter,
            $amount_to_charge,
            $phone,
            $tid,
            "1",
            "PAID"));


        if ($rechargepro_subservice == "BANK TRANSFER") {

            $row = self::db_query("SELECT rechargeprorole, ac_ballance, profile_creator , name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($rechargeproid));
            $rechargeprorole = $row[0]['rechargeprorole'];
            $myballance = $row[0]['ac_ballance'];
            $profile_creator = $row[0]['profile_creator'];
            $name = $row[0]['name'];


            $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $rechargeproid .
                "_" . $tid;
            self::db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",
                array(
                "0",
                "REFUND",
                $what));


            $tfee = 40;
            if ($amount_to_charge > 150000) {
                $tfee = 55;
            }
            $myballance = $myballance + $amount_to_charge + $tfee;
            self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                array($myballance, $rechargeproid));
        }


        if ($tid) {

            $newprint = '{"details":{"REFUND_DATE":"' . date("Y-m-d H:i:s") .
                '","TRANSACTION STATUS","DONE"}}';

            self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_service = ?, rechargepro_subservice =?, rechargepro_status_code=?, rechargepro_status=?, rechargepro_print = ?  WHERE transactionid = ?",
                array(
                "REFUND($rechargepro_service)",
                "REFUND",
                "1",
                "PAID",
                $newprint,
                $tid));


            self::db_query("DELETE FROM rechargepro_transaction_log  WHERE ip = ?", array($tid));

            //self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,rechargepro_service,rechargepro_subservice,amount,transaction_reference,rechargepro_status_code,rechargepro_status,rechargepro_print) VALUES (?,?,?,?,?,?,?,?)",array($rechargeproid,"REFUND","REFUND",$amount_to_charge,"REFUND","1","PAID",'{"details":{"REFUND":"'.$amount_to_charge.'","TRANSACTION STATUS","DONE"}}'));
        }


        $message = "Hey,<br />
$amount_to_charge has been refunded to your wallet, for uncompleted $rechargepro_subservice transaction!<br />
Thank you,<br />
RechargePro";
        self::notification($rechargeproid, $message, 1);

        self::send_mail('noreply@rechargepro.com.ng', $email, "RechargePro Refund", $message);

        return "100";
    }


    function search_paga($vref, $tid,$service)
    {


        
        $requestBody = '{
"referenceNumber":"' . $vref . '"}';

        //MY001
        $baseUrl = "https://mypaga.com/paga-webservices/business-rest/secured/getOperationStatus";


        $httpMethod = "POST";



        
        $hashed = hash("sha512", $vref . "f4c9f47fc23d46a38eb7361e0fe7bfb5fc384cd39798426cb5d9c5907880cc213f1a9195a6394db4acbd882bff4c15df3287956f87aa49cca6f54584636b24c7");
        //{"responseCode":-1,"message":"Failure","referenceNumber":"22341115632","merchantTransactionReference":null,"transactionId":null,"currency":null,"exchangeRate":null,"fee":null}
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_POSTFIELDS => $requestBody,
            //CURLOPT_USERPWD => $username.":" .$password,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "principal:8A82C8F9-22AF-4E77-95E1-B57458CC39EE",
                "credentials:tB3+f7xK=WpWzvH",
                "hash:$hashed",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json"),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $return = json_decode($result, true);

//file_put_contents("prrr.php",$result);
        //{"responseCode":0,"message":"Transaction completed successfully","referenceNumber":"22341115A","transactionId":"F978H","fee":42.0,"transactionStatus":"SUCCESSFUL"}


        if (isset($return['responseCode'])) {

            if (in_array($return['responseCode'], array("0","1"))) {
                
                        $status = "-";
                $statuscode = "0";
                $statusreference = "-";
                $units = "-";

$response = array();
            if ($service == "AED") {
                $response['Token'] = $token;
                $response['details']['Token'] = $token;

                $response['Units'] = $units;
                $response['details']['Units'] = $units;

                $response['pin'] = $token;

                
                $response = '{"VendorReference":"' . $statusreference . '","Reference":"-","MeterNumber":"-","Amount":"-","ResponseTime":"' . date("Y-m-d H:i:s A") .
                    '","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":"' . $token .
                    '","FreeUnits":0,"ReceiptNumber":"-","PurchasedUnits":"' . $units .
                    '","DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"-","Address":"-","Tariff":"-","TariffRate":"-","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":"' .
                    $token . '","service_charge":"N100","Total_amount":"N-"}';
                $response = json_decode($response, true);
                $response["Account Type"] = "Prepaid";
            } else {
                $status = "-";
                $statuscode = "0";
                $statusreference = "-";


                
                $response = '{"transactionNumber":"' . $statusreference .
                    '","details":{"customerAddress":"-","costOfUnits":null,"ac":null,"meterNumber":null,"tariffIndex":null,"vat":"0","costOfUnit":null,"units":null,"accountNumber":"-","debtPayment":"-","supplyGroupCode":null,"customerName":"-","responseCode":"0","creditToken":null,"exchangeReference":"' . $statusreference .
                    '","receipt":"' . $statusreference .
                    '","responseMessage":"successful","fixedCharge":"0","status":"ACCEPTED","tariffInstance":"-"},"service_charge":"N100","Total_amount":"N-"}';
                $response = json_decode($response, true);
                $response["Account Type"] = "Postpaid";
            }
                
                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    1,
                    json_encode($response),
                    $tid)); 
                return "1";

            } else
                if (in_array($return['responseCode'], array(
                    "-1"))) {
                    return "2";//
                } else {
                    return "3";
                }

        }
        
        
        if(isset($return['errorMessage'])){
            return "2";//
        }

        return "3";
    }
    
    
    function confirm_vtpass($parameter)
    {

        $tid = $parameter['tid'];


        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));


 $parameterref = $transaction_date . $tid;
                $url = "https://vtpass.com/api/query";
        $post_string = array(
  'request_id' => $parameterref // unique for every transaction
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
        curl_setopt($soap_do, CURLOPT_USERPWD,"nebolisa@vertistechnologiesltd.com:Change@123");
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        //curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        // curl_setopt($soap_do, CURLOPT_USERPWD, $this->username.":".$this->password);
        $result = curl_exec($soap_do);
        $httpcode = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);
        $array = json_decode($result, true);

        if (in_array($httpcode, array("404", "503"))) {
            return "2";
        }


        //print_r($array);

        if (!isset($array['code'])) {

    
                return "1";
            

        }
            
            if(in_array($array['content']['response_code'],array("016"))){
                
                 return "2";
                }

         if ($array['content']['response_code'] == "000") {
                    
                    $response = array();

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

            return "1";
        }

                

          

        return "3";

    }

    function confirm_payu($parameter)
    {

        $tid = $parameter['tid'];


        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));


        $parameterref = $transaction_date . $tid;


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mcapi-server.herokuapp.com/transactions/single/$parameterref",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "vertis:nVfQeKTn4c",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Connection: Keep-Alive",
                "Keep-Alive: 300",
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"),
            ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response, true);


        if (in_array($httpcode, array(
            "404",
            "500",
            "505",
            "503"))) {
            return "2";
        }


        if (!isset($response[0])) {

            return "3";
        }


        $response = $response[0];


        if ($response['status'] == "-1" || $response['status'] == "1") {
$response['Agent_name']=$namyname;
            $response['Card Number'] = $accountnumber;
            $response['Amount Paid'] = $amount;
            $result = json_encode($response);

            //self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",array($amount, $service));

            $status = "00";
            $statuscode = "0";
            $statusreference = "00";

            self::que_rechargepropay_mail($tid, $email, "success");
            self::curlit($phone, "Thank you your subscription is activated");


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $result,
                $tid));


            return "1";
        } else {

            return "2";


        }


        return "3";

    }

    function confirm_cap($parameter)
    {

        $tid = $parameter['tid'];


        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));


        $baseUrl = self::config('brixurl');
        $username = self::config('brixusername');
        $token = self::config('brixtoken');


$statusreference = $transaction_date . $tid;
        $requestBody = '{"id": ' . $transaction_date . $tid . '}';


        $httpMethod = "POST";
        $restPath = "/rest/consumer/v2/exchange/query";
        $date = gmdate('D, d M Y H:i:s T');


        $hashedRequestBody = base64_encode(hash('sha256', utf8_encode($requestBody), true));

        $signedData = $httpMethod . "\n" . $hashedRequestBody . "\n" . $date . "\n" . $restPath;

        $signature = hash_hmac('sha1', $signedData, $token, true);

        $encodedsignature = base64_encode($signature);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl . $restPath,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_POSTFIELDS => $requestBody,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "authorization: MSP " . $username . ":" . $encodedsignature,
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        $return = json_decode($result, true);

        if (in_array($httpcode, array("404", "503"))) {
            return "2";
        }


        //print_r($return);

        if (isset($return['code'])) {

            if (in_array($return['code'], array(
                "EXC00113",
                "EXC00112",
                "EXC00102",
                "EXC00115"))) {
                return "2";
            }

        } else {

            if (isset($return['details']['status'])) {

                if (in_array($return['details']['status'], array("REJECTED"))) {
                    return "2";
                } else {


                    if (in_array($return['details']['status'], array("ACCEPTED"))) {


                        $status = $return['details']['status'];
                        $statuscode = "0";
                        $venstatusreference = $return['details']['exchangeReference'];


                        $return['service_charge'] = "N100";
                        $return['Total_amount'] = "N" . ($amount + 100);
                        $return['Amount Paid'] = $amount;
                        
                        
                        
                        
                        

            $token = "";
            if (isset($return['details']['token'])) {
                $token = $return['details']['token'];
            }

            if (isset($return['details']['standardTokenValue'])) {
                $token = $return['details']['standardTokenValue'];
            }
            
            if (isset($return['details']['externalReference'])) {
                $token = $return['details']['externalReference'];
            }


            if (isset($return['details']['creditToken'])) {
                $token = $return['details']['creditToken'];
            }

            $units = "";
            if (isset($return['details']['tokenUnit'])) {
                $units = $return['details']['tokenUnit'];
            } //
            
            if (isset($return['details']['power'])) {
                $units = $return['details']['power'];
            } //
            
            if (isset($return['details']['units'])) {
                $units = $return['details']['units'];
            }
            if (isset($return['details']['standardTokenUnits'])) {
                $units = $return['details']['standardTokenUnits'];
            }
            if (isset($return['details']['amountOfPower'])) {
                $units = $return['details']['amountOfPower'];
            }
                        
                        
                        if (!empty($token)) {
                        $totalpay = $amount+100;
                $response = $return;      
                        
                $response = '{"VendorReference":"'.$statusreference.'","Reference":"'.$venstatusreference.'","MeterNumber":"'.$accountnumber.'","Amount":'.$totalpay.',"ResponseTime":"'.date("Y-m-d H:i:s A").'","UtilityAmtVatExcl":"-","Vat":"-","TerminalId":null,"Token":"'.$token.'","FreeUnits":0,"ReceiptNumber":"7'.$venstatusreference.'","PurchasedUnits":'.$units.',"DebtDescription":null,"DebtAmount":0,"RefundUnits":0,"RefundAmount":0,"ServiceChargeVatExcl":0,"IsRequery":"NO","VendorName":"rechargepro","VendorOperatorName":"RECHARGEPRO","VendorTerminalId":"RECHARGEPRO_1","MeterDetail":{"SupplyGroupCode":null,"KeyRevisionNumber":null,"TariffIndex":null,"AlgorithmTechnology":null,"TokenTechnology":null},"UtilityDetail":{"Name":null,"VatRegNumber":null,"Message":null},"CustomerDetail":{"Name":"'.$name.'","Address":"'.$address.'","Tariff":"Prepaid Residential 1PH 3PH","TariffRate":"192 KWH @ 24.3","VatInvoiceNumber":null,"LastPurchase":"-"},"ResponseCode":100,"ResponseMessage":"SUCCESSFUL","pin":"'.$token.'","service_charge":"N100","Total_amount":"N'.$totalpay.'"}';
                $return = json_decode($response,true);
                        }
                        
                        
                        $resultb = json_encode($return);
                        self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                            array(
                            $status,
                            $statuscode,
                            $venstatusreference,
                            1,
                            $resultb,
                            $tid));

                        return "1";
                    }

                }

            }
        }

        return "3";

    }

    function search_power_aedc($vref, $tid)
    {


        $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "AEDC_key"));


        $token = "bearer " . $rmk[0]['setting_value'];


        //01011150565667


        // $vref = $transaction_date . $tid;
        $Url = "https://api.kvg.com.ng/live/energy/aedc/prepaid/requery/vref/$vref";
        $baseUrl = $Url;


        $httpMethod = "GET";
        $date = gmdate('D, d M Y H:i:s T');


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "AUTHORIZATION:$token",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);

        $return = $response;


        if (isset($return['ResponseCode'])) {

            if (in_array($return['ResponseCode'], array("100","102","116","124"))) {
                $status = $response['ResponseMessage'];
                $statuscode = "0";
                $statusreference = $response['VendorReference'];
                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    1,
                    $result,
                    $tid)); 
                return "1";

            } else
                if (in_array($return['ResponseCode'], array(
                    "126","112","115","104","527,","527","531","250","251","252","253","254","255"))) {
                    return "2";//
                } else {
                    return "3";
                }

        }

        return "3";
    }
    
    
    function search_power_aedc_post($vref, $tid)
    {


        $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "AEDC_key"));


        $token = "bearer " . $rmk[0]['setting_value'];


        //01011150565667


        // $vref = $transaction_date . $tid;
        $Url = "https://api.kvg.com.ng/live/energy/aedc/postpaid/payment/$vref";
        $baseUrl = $Url;


        $httpMethod = "GET";
        $date = gmdate('D, d M Y H:i:s T');


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpMethod,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json, application/*+json",
                "accept-encoding: gzip,deflate",
                "AUTHORIZATION:$token",
                "cache-control: no-cache",
                "connection: Keep-Alive",
                "content-type: application/json",
                "x-msp-date:" . $date),
            ));
        $result = curl_exec($curl);
        $err = curl_error($curl);
        $response = json_decode($result, true);

        $return = $response;

if($tid == "45232"){
   file_put_contents("post3.php",$result); 
}
        if (isset($return['code'])) {

            if (in_array($return['code'], array("100","102","116","124"))) {
                $status = $response['message'];
                $statuscode = "0";
                $statusreference = $response['vref'];
                
                
                            $totalamount = $amount+100;
            $response = '{"transactionNumber":"'. $response['vref'].'","details":{"customerAddress":"'. $response['customer_address'].'","costOfUnits":null,"ac":null,"meterNumber":null,"tariffIndex":null,"vat":"0","costOfUnit":null,"units":null,"accountNumber":"'. $response['customer_no'].'","debtPayment":"-","supplyGroupCode":null,"customerName":"'. $response['customer_name'].'","responseCode":"'. $response['code'].'","creditToken":null,"exchangeReference":"'. $response['reference'].'","receipt":"'. $response['reference'].'","responseMessage":"'. $response['message'].'","fixedCharge":"0","status":"ACCEPTED","tariffInstance":"-"},"service_charge":"N100","Total_amount":"N'.$totalamount.'"}';

$response = json_decode($response,true);
$response["Transaction Date"] = date("Y-m-d H:i:s");

         
         
         
                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    1,
                    json_encode($response),
                    $tid));
                return "1";

            } else
                if (in_array($return['code'], array(
                    "126","112","115","104","527,","527","531","250","251","252","253","254","255"))) {
                    return "2";//2
                } else {
                    return "3";//3
                }

        }

        return "3";
    }


    function vendMtn($sequence, $destMsisdn, $amount = 0, $tariffTypeId = 1)
    {
        //echo $sequence." -- ".$destMsisdn." -- ".$amount."<br/>"; //die();
        //1 or 9

        // return array("status" => "100", "message" => $destMsisdn."_".$amount);

        $username = "BRINQ-AFRICA_!46";
        $password = "dyap_yuwy!hyd56";
        $origMsisdn = "2348137266424";
        $url = "https://BRINQ-AFRICA_!46:dyap_yuwy!hyd56@41.220.77.137:443/axis2/services/BRINQ-AFRICAService";

        $post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://hostif.vtm.prism.co.za/xsd"><soapenv:Header/><soapenv:Body><vend xmlns="http://hostif.vtm.prism.co.za/xsd"><sequence>' .
            $sequence . '</sequence><origMsisdn>' . $origMsisdn .
            '</origMsisdn><destMsisdn>' . $destMsisdn . '</destMsisdn><amount>' . $amount .
            '</amount><tariffTypeId>' . $tariffTypeId .
            '</tariffTypeId></vend></soapenv:Body></soapenv:Envelope>';


        $header = array(
            "Content-type:text/xml;charset=\"utf-8\"",
            "Accept:application/xml",
            "Cache-Control:no-cache",
            "Pragma:no-cache",
            "SOAPAction:https://BRINQ-AFRICA_!46:dyap_yuwy!hyd56@41.220.77.137:443/axis2/services/BRINQ-AFRICAService",
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
        curl_setopt($soap_do, CURLOPT_USERPWD, $username . ":" . $password);
        $result = curl_exec($soap_do);
        $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
        $err = curl_error($soap_do);
        curl_close($soap_do);
        //print_r($result, true); //die();
        //$json = json_encode($result);
        //file_put_contents("myxmlfile1.xml", $result);
        //file_put_contents("myxmlfile2.xml", $json);
        //soapenv: :soapenv

        $eml = array();
        $result = str_replace(array("soapenv:", ":soapenv"), array("", ""), $result);
        $xml = simplexml_load_string($result);
        if ($xml === false) {
            self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_print = ? WHERE transactionid =?",
                array($result, $sequence));
            return "1";
        } else {


            $statusid = (string )$xml->Body->vendResponse->statusId;
            if (empty($statusid)) {
                $statusid = (string )$xml->Body->vendResponse->responseCode;
            }

            if (in_array($statusid, array(
                "0",
                "2",
                "5",
                "7",
                "10",
                "12",
                "21",
                "203",
                "106"))) {


                $eml['status'] = 200;
                $eml['message']['statusId'] = (string )$xml->Body->vendResponse->statusId;
                $eml['message']['txRefId'] = (string )$xml->Body->vendResponse->txRefId;
                $eml['message']['seqtxRefId'] = (string )$xml->Body->vendResponse->seqtxRefId;
                $eml['message']['responseMessage'] = (string )$xml->Body->vendResponse->
                    responseMessage;

                $status = $response['status'];
                $statuscode = "0";
                $statusreference = $response['message']['txRefId'];


                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $eml['status'],
                    "0",
                    $eml['message']['seqtxRefId'],
                    1,
                    json_encode($eml['message']),
                    $sequence));

                return "1";
            } else {

                self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_print = ? WHERE transactionid =?",
                    array($result, $sequence));

                return "1";
            }


        }
        //		return $eml;
    }


    function verify_vertibra($tref, $service)
    {
        //"EPP","EKP","IKP","IPP","IBB","IBP","BOA","BOB"
        if (in_array($service, array("EPP", "EKP"))) {
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
        }


        if (in_array($service, array("IKP", "IPP"))) {
            $url = "https://www.iepins.com.ng/API/vproxy.asmx?op=FetchTxnByRef";
            $post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>' . $tref . '</TxnRef>
      <hashstring>' . md5($tref . "IE5273") . '</hashstring>
      <api_key>E146A2C4460B6511AEA043565D605C6A</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
        }


        if (in_array($service, array("IBB", "IBP"))) {
            $url = "https://www.iepins.com.ng/API/vproxy.asmx?op=FetchTxnByRef";
            $post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://IBEDC_API/vproxy/">
      <TxnRef>' . $tref . '</TxnRef>
      <hashstring>' . md5($tref . "IB0024") . '</hashstring>
      <api_key>0510770c-d3c7-4a27-8452-f55545c12f1b</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
        }


        if (in_array($service, array("BOA", "BOB"))) {

            $url = "http://eedcstaging.phcnpins.com/api/vproxy.asmx?op=FetchTxnByRef";
            $post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>' . $tref . '</TxnRef>
      <hashstring>' . md5($tref . "EE0174") . '</hashstring>
      <api_key>579B5A722C993135F3E0F906AB84FBBE</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
        }


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


        if ($code == "404" || $code == "500") {
            return "2";
        }


        if (!isset($array['body'])) {
            return "3";
        }


        if (!isset($array['body']['fetchtxnbyrefresponse'])) {
            return "3";
        }


        if (!isset($array['body']['fetchtxnbyrefresponse']['fetchtxnbyrefresult'])) {
            return "3";
        }


        $response = json_decode($array['body']['fetchtxnbyrefresponse']['fetchtxnbyrefresult'], true);


        if (isset($response[0])) {
            $response = $response[0];
            $mainresponse = json_encode($response[0]);
        }


        if (isset($response['responsecode'])) {
            if (in_array($response['responsecode'], array(
                "04",
                "05",
                "01"))) {
                return "2";
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


            if ($natoken == "1") {
                $message = "Token:" . $response['token'] . "\r\nAmount:$amount \r\nUnits:" . $response['tokenunit'] .
                    "\r\nInvoice Number:" . $rechargeproid . "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out";
                self::curlit($phone, $message);
            } else {
                self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:" . $rechargeproid .
                    "_" . $tid . "\r\nvisit rechargepro.com.ng, For print out");
            }


            if (isset($response['token'])) {
                $response['Token'] = $response['token'];
            }
$response['Agent_name']=$namyname;
            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                json_encode($response),
                $tid));


            self::que_rechargepropay_mail($tid, $email, "success");


            return "1";
        }

    }


    ///////////////////////////////
    public function verify_mobifin($parameter)
    {

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

        $response = self::mobifin_post("topup/log/byref/RECH" . $transaction_date . $tid, "", false);


        if (!is_array($response)) {
            if ($response == "404") {
                return "2";
            }
        }


        if (isset($response['code'])) {
            if ($response['code'] == "RECHARGE_FAILED" || $response['code'] ==
                "MSISDN_INVALID") {
                $status = "";
                $statuscode = "";

                if (isset($response['message'])) {
                    $status = $response['message'];
                }

                if (isset($response['status'])) {
                    $statuscode = $response['status'];
                }

                $statusreference = "";
                if (isset($response['code'])) {
                    $statusreference = $response['code'];
                }


                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    $result,
                    $tid));

                return "0";
            }
        }


        if (!isset($response['status'])) {
            return "3";
        }

        if (in_array($response['status'], array(
            "400",
            "402",
            "405",
            "401",
            "408",
            "427"))) {
            return "2";
        }


        if ($response['code'] == "RECHARGE_FAILED" || $response['code'] ==
            "MSISDN_INVALID") {
            return "2";
        }


        if (!isset($response['client_apiresponse'])) {
            return "3";
        }

        //$response = array();
        //$response = self::json_clean_decode($response['client_apiresponse']);

        $response = self::json_clean_decode($response['client_apiresponse'], true);

        //503
        if (!isset($response['reference'])) {
            $response['reference'] = $accountnumber;
        }

        $response['Phone'] = $accountnumber;
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';

        if ($response['status'] == "208") {
            return "3";
        }

        if ($response['status'] == "200" || $response['status'] == "201" || $response['status'] ==
            "429") {

            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));

            $status = $response['message'];
            $statuscode = "0";
            $statusreference = $response['reference'];


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $result,
                $tid));

            return "1";
        } else {

            $status = "";
            $statuscode = "";

            if (isset($response['message'])) {
                $status = $response['message'];
            }

            if (isset($response['status'])) {
                $statuscode = $response['status'];
            }

            $statusreference = "";
            if (isset($response['code'])) {
                $statusreference = $response['code'];
            }


            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                $result,
                $tid));

            return "0";
        }

    }


    function mobifin_post($path, $data_string, $post = true)
    {
        $auth = "1";

        if ($path != "auth") {
            $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
            $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
                array($nowdate, "mobifin"));
            if (!empty($rmk[0]['setting_value'])) {

                $auth = $rmk[0]['setting_value'];

            } else {
                $access = self::mobifin_auth();
                if ($access['status'] == "100") {
                    return $access;
                } else {
                    $auth = $access['message'];
                }
            }
        }


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://clients.primeairtime.com/api/' . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "accept: application/json, application/*+json",
            "accept-encoding: gzip,deflate",
            "Authorization: Bearer $auth",
            "cache-control: no-cache",
            "Connection: Keep-Alive",
            "Keep-Alive: 300",
            "content-type: application/json",
            ));

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response = json_decode($result, true);

        if (in_array($httpcode, array("404", "503"))) {
            return "404";
        }

        return $response;
    }

    function mobifin_auth()
    {
        $data_string = '{
 "username" : "' . self::config('mobiusername') . '",
 "password": "' . self::config('mobipassword') . '"
}';
        $access = self::mobifin_post("auth", $data_string, true);

        if (isset($access['token'])) {
            $date = date("Y-m-d H:i:s", strtotime("+0 day", strtotime($access['expires'])));
            self::db_query("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
                array(
                $access['token'],
                $date,
                "mobifin"));
            return array("status" => "200", "message" => $access['token']);
        } else {
            return array("status" => "100", "message" => "Network Error");
        }

    }

    function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0)
    {
        // search and remove comments like /* */ and //
        $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#",
            '', $json);
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return json_decode($json, $assoc, $depth, $options);
        } elseif (version_compare(phpversion(), '5.3.0', '>=')) {
            return json_decode($json, $assoc, $depth);
        } else {
            return json_decode($json, $assoc);
        }
    }

}
?>