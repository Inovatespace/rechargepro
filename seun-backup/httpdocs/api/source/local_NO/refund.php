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

        $row = self::db_query("SELECT rechargepro_print,transaction_date,account_meter,phone,rechargepro_service,cordinator_id,agent_id,rechargeproid,rechargepro_subservice,amount,thirdPartycode,refererprofit,agentprofit,cordprofit,rechargeproprofit,rechargepro_service_charge FROM rechargepro_transaction_log WHERE transactionid = ? AND rechargepro_status_code = ? AND refund = '0' AND bank_ref ='' LIMIT 1",array($tid, "0"));
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
        
        $refererprofit = $row[0]['rechargepro_print'];
        $agentprofit = $row[0]['agentprofit'];
        $cordprofit = $row[0]['cordprofit'];
        $rechargepro_print = $row[0]['rechargepro_print'];
        $rechargepro_service_charge = $row[0]['rechargepro_service_charge'];
        
        
        
        
        

        if (empty($rechargeproid)){
            exit;
        }
        
        
        $run = 0;
        if(in_array($rechargepro_service,array("ADD","JOD","JOP"))){
          $run = 1;
        }  
        
        
        if(in_array($rechargepro_subservice,array("EPP","EKP","IKP","IPP","IBB","IBP","BOA","BOB"))){
        $run = 1;
        $vref = $transaction_date.$tid;
        $return = self::verify_vertibra($vref,$rechargepro_subservice);
        if($return == "1"){
           return "200";
          }
        }
        
          if(in_array($rechargepro_subservice,array("2351","ACC","2352","AEC","ANB","ANA","WEC","BGA","2353","ALC"))){
        $run = 1;
        $return = self::verify_mobifin(array("tid" => $tid));
        if($return == "1"){
           return "200";
          }
        }
        
        if(in_array($rechargepro_subservice,array("2354","ADC"))){
        $run = 1;
        //$return = self::vendMtn($tid, self::fix_phone($account_meter),$amount_to_charge,$thirdPartyCode);
        //if($return == "1"){
            return "200";
          //  }
        }
       
       
        if(in_array($rechargepro_subservice,array("eeeeeeeeeeee"))){
        $run = 1;
        $return = self::confirm_payu(array("tid" => $tid));
        if($return == "1"){
                 return "200";
            }
        }
 
        if(in_array($rechargepro_subservice,array("AED"))){
            $run = 1;
            $vref = $transaction_date.$tid;
            $return = self::search_power_aedc($vref,$tid);
            if($return == "1"){
                return "200";
            }
        }       
        
        
       if(in_array($rechargepro_subservice,array("BIA","BIB","AQA","AQC"))){
        $run = 1;
        $return = self::confirm_cap(array("tid" => $tid));
        if($return == "1"){
                 return "200";
            }
        }
        
        
        if($run == 3444440){
            $run = 1;
        $statusreference = $transaction_date . $tid;
        $return = self::post_switch($statusreference,$tid);
            if($return == "1"){
                return "200";
            }
        }
        
      
        
               
        
        //set refund = 1
self::db_query("UPDATE rechargepro_transaction_log SET refund=?  WHERE transactionid = ?",
                array(
                "1",
                $tid));
                
                
        //if(empty($percentage)){exit;}
if(!empty($account_meter)){

      $row = self::db_query("SELECT rechargeprorole, ac_ballance,profit_bal, profile_creator , name, email FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($rechargeproid));
        $rechargeprorole = $row[0]['rechargeprorole'];
        $myballance = $row[0]['ac_ballance'];
        $myprofitbal = $row[0]['profit_bal'];
        $profile_creator = $row[0]['profile_creator'];
        $name = $row[0]['name'];
        $email = $row[0]['email'];

        $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $rechargeproid."_".$tid;
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
                    $newballance = $myballance + $amount_to_charge + $rechargepro_service_charge;
                    $newprofit = $myprofitbal - $agentprofit;
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ?, profit_bal=? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance,$newprofit,$rechargeproid));


                if ($rechargepro_cordinator > 0 || $rechargeprorole == 1){
                    
                    if($rechargeprorole == 1){
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
                    $cordinator_ballance = $row[0]['profit_bal'];

                    $cornewballance = $cordinator_ballance - $cordprofit;
                    self::db_query("UPDATE rechargepro_account SET profit_bal=? WHERE rechargeproid = ? LIMIT 1",
                        array($cornewballance, $rechargepro_cordinator));
                }
          //  }

        }


        if ($rechargeprorole > 3) {
            if ($profile_creator > 0 && $refererprofit > 0) {

                $row = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $creator_ballance = $row[0]['profit_bal'];

                self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                    array(
                    $profile_creator,
                    $rechargeproid,
                    $refererprofit,
                    "-USER_REWARD"));


                $creator_ballance = $creator_ballance - $refererprofit;
                self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($creator_ballance, $profile_creator));

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

       
self::db_query("INSERT INTO rechargepro_refund (rechargeproid,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,transactionid,rechargepro_status_code,rechargepro_status) VALUES (?,?,?,?,?,?,?,?,?)",array($rechargeproid,$rechargepro_service,$rechargepro_subservice,$account_meter,$amount_to_charge,$phone,$tid,"1","PAID"));


        if ($rechargepro_subservice == "BANK TRANSFER"){
            
      $row = self::db_query("SELECT rechargeprorole, ac_ballance, profile_creator , name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($rechargeproid));
        $rechargeprorole = $row[0]['rechargeprorole'];
        $myballance = $row[0]['ac_ballance'];
        $profile_creator = $row[0]['profile_creator'];
        $name = $row[0]['name'];
        
        
        
        
        $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $rechargeproid."_".$tid;
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


        if ($tid){
            
           $newprint = '{"details":{"REFUND_DATE":"'.date("Y-m-d H:i:s").'","TRANSACTION STATUS","DONE"}}';
            
            self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_service = ?, rechargepro_subservice =?, rechargepro_status_code=?, rechargepro_status=?, rechargepro_print = ?  WHERE transactionid = ?",
                array(
                "REFUND($rechargepro_service)",
                "REFUND",
                "1",
                "PAID",
                $newprint,
                $tid));
                
                
                
            self::db_query("DELETE FROM rechargepro_transaction_log  WHERE ip = ?",array($tid));       

//self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,rechargepro_service,rechargepro_subservice,amount,transaction_reference,rechargepro_status_code,rechargepro_status,rechargepro_print) VALUES (?,?,?,?,?,?,?,?)",array($rechargeproid,"REFUND","REFUND",$amount_to_charge,"REFUND","1","PAID",'{"details":{"REFUND":"'.$amount_to_charge.'","TRANSACTION STATUS","DONE"}}'));
        }



        $message = "Hey,<br />
$amount_to_charge has been refunded to your wallet, for uncompleted $rechargepro_subservice transaction!<br />
Thank you,<br />
RechargePro";
      self::notification($rechargeproid, $message, 1);
        
     self::send_mail('noreply@rechargepro.com.ng',$email,"RechargePro Refund",$message);
        
         return "100";
    }
    
    
    

function post_switch($unique,$tid){
    
$switch_clientid = "IKIA1DB0E7FA90F910355F09DDAF9EC68ABC4C22BA52";
$switch_secrete = "idOcCImWru/FQj/b5+vBqiKZ1dIc5suUUt/zSkOozXk=";
$switch_treminalid = "3BQA0001";
$switch_transfer_prefix = "1681";
        
    
        
$date = new DateTime(null, new DateTimeZone("Africa/Lagos"));
$date = $date->getTimestamp();

//https://saturn.interswitchng.com/
$unique = $switch_transfer_prefix.$unique;
$once = $unique.rand(00000,99999);
$url = strtolower("https://saturn.interswitchng.com/api/v2/quickteller/transactions?requestreference=$unique");

$stringToBeSigned = "GET&".urlencode($url)."&".$date."&".$once."&".$switch_clientid."&".$switch_secrete;
$signature = base64_encode(sha1($stringToBeSigned, true));

//$postfield["TerminalId"] = $switch_treminalid;
//$postfield["requestReference"] = $unique;

$curl = curl_init();



curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  //CURLOPT_POSTFIELDS => json_encode($postfield),
  CURLOPT_HTTPHEADER => array(
    "authorization:InterswitchAuth ".base64_encode($switch_clientid),
    "cache-control:no-cache",
    "content-type:application/json",
    "nonce:$once",
    "signature:$signature",
    "signaturemethod:SHA1",
    "terminalid:".$switch_treminalid,
    "timestamp:$date"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

$return = json_decode($response, true);
 
 
//file_put_contents("1.txt",$return);

    if(isset($return['transactionResponseCode'])){
            
    if(in_array($return['transactionResponseCode'],array("90000","90010","90011","90016","900A0","70045","10001","90009","90005","90039","00","90091","90009"))){
        
        //,"90022"
        $status = "SUCCESS";
        $statuscode = "0";
        $statusreference = "COMPLETED";
        
    $row = self::db_query("SELECT rechargepro_service,account_meter,amount FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
        array($tid));
    $rechargepro_service = $row[0]['rechargepro_service'];
    $primary = $row[0]['account_meter'];
    $amount = $row[0]['amount'];
   
        $result = '{"details":{"Product":"' . $rechargepro_service . '","Reference Number":"' .
            $statusreference . '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"'.$return['transactionRef'].'","AmountPaid":"'.$amount.'","Client_Account":"'.$primary.'"}}';


        self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
            array(
            $status,
            $statuscode,
            $statusreference,
            1,
            $result,
            $tid));
            
    return  "1";
    }
    
    
    if(in_array($return['transactionResponseCode'],array("90006"))){//,"90091","90021"
        return "2";
        }
    
    }
    
    if(isset($return['errors'][0]['code'])){
        
        if($return['error']['code'] == "70038"){
    return "2";
    }
    }
    
    
    return "1";
    }
    
   

//

function confirm_payu ($parameter)
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





$parameterref = $transaction_date.$tid;


     
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://mcapi-server.herokuapp.com/transactions/single/$parameterref",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_USERPWD=> "vertis:nVfQeKTn4c",
  CURLOPT_HTTPAUTH=> CURLAUTH_BASIC,  
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
                    "Connection: Keep-Alive",
                "Keep-Alive: 300",
    "cache-control: no-cache",
    "content-type: application/x-www-form-urlencoded"
  ),
));

$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);
$response = json_decode($response,true);



if(in_array($httpcode,array("404","500","505","503"))){
     return "2";
}




if(!isset($response[0])){
    
   return "1";
}


$response = $response[0];
 

  
  
  
        if ($response['status'] == "-1" || $response['status'] == "1") {

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
        }else{
            
             return "2";   
            
            
        }            
            





    return "1";

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
        
        if(in_array($httpcode,array("404","503"))){
            return "2";
        }
        

//print_r($return);

    if (isset($return['code'])) {

        if (in_array($return['code'], array("EXC00113", "EXC00112","EXC00102","EXC00115"))) {
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
            $statusreference = $return['details']['exchangeReference'];



                    $return['service_charge'] = "N100";
                    $return['Total_amount'] = "N".($amount+100);
                    $return['Amount Paid'] = $amount;
                    $resultb = json_encode($return);
                     self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $resultb,
                $tid));
                
                    return "1";
                }

            }

        }
    }
    
    return "1";

}

function search_power_aedc($vref,$tid)
    {


  $nowdate = date("Y-m-d H:i:s", strtotime("+5 hours", strtotime(date("Y-m-d H:i:s"))));
        $rmk = self::db_query("SELECT setting_value, setting_date FROM settings WHERE setting_date > ? AND setting_key = ? LIMIT 1",
            array($nowdate, "AEDC_key"));


        $token = "bearer ".$rmk[0]['setting_value'];


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
        
        
        if(isset($return['ResponseCode'])){
    
    if(in_array($return['ResponseCode'],array("100"))){
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
    
    }else if(in_array($return['ResponseCode'],array("0","104","213","210"))){
        return "2";
    }else{
        return "1";  
    }
    
}

return"1";
    }
    
    
    
    
    
     
	function vendMtn($sequence, $destMsisdn, $amount = 0, $tariffTypeId = 1)
    {
		//echo $sequence." -- ".$destMsisdn." -- ".$amount."<br/>"; //die();
        //1 or 9
        
           // return array("status" => "100", "message" => $destMsisdn."_".$amount);
     
		$username="BRINQ-AFRICA_!46";
		$password="dyap_yuwy!hyd56";
		$origMsisdn="2348137266424";
		$url = "https://BRINQ-AFRICA_!46:dyap_yuwy!hyd56@41.220.77.137:443/axis2/services/BRINQ-AFRICAService";
                
		$post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://hostif.vtm.prism.co.za/xsd"><soapenv:Header/><soapenv:Body><vend xmlns="http://hostif.vtm.prism.co.za/xsd"><sequence>'.$sequence.'</sequence><origMsisdn>'.$origMsisdn.'</origMsisdn><destMsisdn>'.$destMsisdn.'</destMsisdn><amount>'.$amount.'</amount><tariffTypeId>'.$tariffTypeId.'</tariffTypeId></vend></soapenv:Body></soapenv:Envelope>';
        
                        
	
		$header = array(
		"Content-type:text/xml;charset=\"utf-8\"",
		"Accept:application/xml",
		"Cache-Control:no-cache",
		"Pragma:no-cache",
		"SOAPAction:https://BRINQ-AFRICA_!46:dyap_yuwy!hyd56@41.220.77.137:443/axis2/services/BRINQ-AFRICAService",
		"Content-length:".strlen($post_string));
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,            $url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		 curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
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
        $result = str_replace(array("soapenv:",":soapenv"),array("",""),$result);
         $xml = simplexml_load_string($result);
         if ($xml === false) {
          self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_print = ? WHERE transactionid =?",array($result,$sequence));
         return "1";  
         }else{
            
            
            $statusid = (string)$xml->Body->vendResponse->statusId;
            if(empty($statusid)){$statusid = (string)$xml->Body->vendResponse->responseCode;}
    
       if(in_array($statusid,array("0","2","5","7","10","12","21","203","106"))){
       
       
    
 
          $eml['status'] = 200;
          $eml['message']['statusId'] = (string)$xml->Body->vendResponse->statusId; 
          $eml['message']['txRefId'] = (string)$xml->Body->vendResponse->txRefId;
          $eml['message']['seqtxRefId'] = (string)$xml->Body->vendResponse->seqtxRefId;
          $eml['message']['responseMessage'] = (string)$xml->Body->vendResponse->responseMessage; 
          
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
                }else{
                    
          self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_print = ? WHERE transactionid =?",array($result,$sequence));    
         
         return "1";
                }
                
                
             

         }
//		return $eml;
    }
    
    
    
    
    function verify_vertibra($tref,$service){
        //"EPP","EKP","IKP","IPP","IBB","IBP","BOA","BOB"
        if(in_array($service,array("EPP","EKP"))){
  $url = "https://eko.phcnpins.com/API/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."EK0134").'</hashstring>
      <api_key>46374a1d-2b9d-4ede-a7f3-731367d345cf</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
}


        if(in_array($service,array("IKP","IPP"))){
  $url = "https://www.iepins.com.ng/API/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."IE5273").'</hashstring>
      <api_key>E146A2C4460B6511AEA043565D605C6A</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
}


        if(in_array($service,array("IBB","IBP"))){
  $url = "https://www.iepins.com.ng/API/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://IBEDC_API/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."IB0024").'</hashstring>
      <api_key>0510770c-d3c7-4a27-8452-f55545c12f1b</api_key>
    </FetchTxnByRef>
  </soap:Body>
</soap:Envelope>';
}


        if(in_array($service,array("BOA","BOB"))){
        
  $url = "http://eedcstaging.phcnpins.com/api/vproxy.asmx?op=FetchTxnByRef";  
$post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <FetchTxnByRef xmlns="http://localhost/eedc/vproxy/">
      <TxnRef>'.$tref.'</TxnRef>
      <hashstring>'.md5($tref."EE0174").'</hashstring>
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
		"Content-length:".strlen($post_string)
        );
        
		 $soap_do = curl_init();
		 curl_setopt($soap_do, CURLOPT_URL,$url );
		 curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		 curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
		 curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
		 curl_setopt($soap_do, CURLOPT_POST,           true );
		 curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string);
		 curl_setopt($soap_do, CURLOPT_HTTPHEADER,   $header);
		// curl_setopt($soap_do, CURLOPT_USERPWD, $username.":".$password);
		 $result = curl_exec($soap_do);
		 $code = curl_getinfo($soap_do, CURLINFO_HTTP_CODE);
		 $err = curl_error($soap_do);


//file_put_contents("dd.xml",$result);
 
            
            
         $result = str_replace(array("soap:",":soap"),array("",""),$result);
         $xml = simplexml_load_string($result);
         $json = json_encode($xml);
         $array = json_decode($json,TRUE);
         $array = self::array_change_value_case($array);
         
       
          if ($code == "404" || $code == "500"){
            return "2";
         }
   
         
         
         if (!isset($array['body'])) {
           return "1";
        }
        
        
               if (!isset($array['body']['fetchtxnbyrefresponse'])) {
            return "1";
        }
        
        
               if (!isset($array['body']['fetchtxnbyrefresponse']['fetchtxnbyrefresult'])) {
            return "1";
        }
        
        
        
      $response = json_decode($array['body']['fetchtxnbyrefresponse']['fetchtxnbyrefresult'],true);
         
         
          if(isset($response[0])){$response = $response[0]; $mainresponse = json_encode($response[0]);}
      
         
if (isset($response['responsecode'])) {
    if (in_array($response['responsecode'], array("04","05","01"))){
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
 $rp = explode("|",$response['responsemessage']);
 if(isset($rp[1])){
    $token = explode(":",$rp[1]);
    if(strtolower($token[0]) == "credittoken"){
                $natoken = "1";
        $response['token'] = $token[1];
    }
 }
 
 if(isset($rp[5])){
    $tokenunit = explode(":",$rp[5]);
    if(strtolower($tokenunit[0]) == "value"){
        $response['tokenunit'] = substr($tokenunit[1], 0, strpos($tokenunit[1], '.', strpos($tokenunit[1], '.')+1));
    }
 }
            

            if($natoken == "1"){
            $message = "Token:".$response['token']."\r\nAmount:$amount \r\nUnits:".$response['tokenunit']."\r\nInvoice Number:".$rechargeproid."_".$tid."\r\nvisit rechargepro.com.ng, For print out";
            self::curlit($phone, $message);
            }else{
             self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:".$rechargeproid."_".$tid."\r\nvisit rechargepro.com.ng, For print out");   
            }
            
            
            
            if(isset($response['token'])){
            $response['Token'] = $response['token'];
            }

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

        $response = self::mobifin_post("topup/log/byref/" . $transaction_date.$tid, "", false);
        
        
        
        if(!isset($response['status'])){
          return "1";  
        }
        
         if (in_array($response['status'],array("400","402","405","401","408"))) {
            return "2";
            }
        
         if ($response == "404"){
            return "2";
         }
         
      if ($response['code'] == "RECHARGE_FAILED"){
            return "2";
        }
        
        
         if (!isset($response['client_apiresponse'])){
            return "1";
        }
        
        //$response = array();
        //$response = self::json_clean_decode($response['client_apiresponse']);
        
        $response = self::json_clean_decode($response['client_apiresponse'],true);
        
       //503
        if(!isset($response['reference'])){
          $response['reference'] = $accountnumber;  
        }

        $response['Phone'] = $accountnumber;
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';
        
        if ($response['status'] == "208"){
            return "2";
        }

        if ($response['status'] == "200" || $response['status'] == "201" || $response['status'] == "429") {

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
        } else{

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
                $statusreference,$result,
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
        
        if(in_array($httpcode,array("404","503"))){
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
    
    function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    if(version_compare(phpversion(), '5.4.0', '>=')) { 
        return json_decode($json, $assoc, $depth, $options);
    }
    elseif(version_compare(phpversion(), '5.3.0', '>=')) { 
        return json_decode($json, $assoc, $depth);
    }
    else {
        return json_decode($json, $assoc);
    }
}

}
?>