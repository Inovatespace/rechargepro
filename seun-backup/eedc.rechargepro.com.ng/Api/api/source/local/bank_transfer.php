<?php
class bank_transfer extends Api
{
    //KEDCO 815.30
    public function __construct($method)
    {
        $this->proccess_count = 1;
                
        $this->transactionfee = 52;
        $this->wave_fee = 45;
        
        $this->pub_key = "FLWPUBK-d9774ccef433a08174cc2597b7f0119c-X";
        $this->private_key = "FLWSECK-efba7abe0decca4441c236caf91d9c76-X";
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
            return array("status" => "100", "message" =>
                    "Minimum Transfer Allowed, 100.00");
        }
        
        if($amount > 20000){
          $this->transactionfee = 100;  
        }

        if ($amount > 50000) {
            return array("status" => "100", "message" =>
                    "Maximum Transfer Allowed, 50,000.00");
        }


        $banklist = self::bank_list($parameter);
        if (!isset($banklist[$bankcode])){
            return array("status" => "100", "message" => "Invalid Bank");
        }


        #LASER
        $rechargeproid = "0";
        if (isset($parameter['private_key'])){
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid,transfer_activation FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $transfer_activation = $row[0]['transfer_activation'];
            if ($transfer_activation == 0) {
                return array("status" => "100", "message" =>
                        "Account not Eligible, Please contact your account officer or Support Department");
            }
            //check auth
        }
        



$jsonpost = '{
  "recipientaccount": "'.$account.'",
  "destbankcode": "'.$bankcode.'",
  "PBFPubKey": "'.$this->pub_key.'"
}';


        $acauth = self::wave_post($jsonpost,"flwv3-pug/getpaidx/api/resolve_account");
        if (!isset($acauth['status'])){
            return array("status" => "100", "message" => "Network error try again");
        }

        if ($acauth['status'] != "success"){
            return array("status" => "100", "message" => "Invalid Account Number");
        }

        $name = $acauth['data']['data']['accountname'];
        
                if(empty($name)){
            return array("status" => "100", "message" => "Invalid Account Number"); 
        }

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (name,rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,thirdPartycode,address) VALUES (?,?,?,?,?,?,?,?,?)",
            array(
            $name,
            $rechargeproid,
            $ip,
            "BANK TRANSFER",
            "BANK TRANSFER",
            $account,
            $amount,
            $bankcode,
            $narration));
            
            $totalamount = $amount+$this->transactionfee;


        return array("status" => "200", "message" => array(
                "ac" => $account,
                "name" => $name,
                "amount" => $amount,
                "totalamount"=>$totalamount,
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

        $row = self::db_query("SELECT rechargeproid,rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $rechargeproid = $row[0]['rechargeproid'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['rechargepro_subservice'];
        $account = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $result = $row[0]['rechargepro_print'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));



if($amount > 20000){
  $this->transactionfee = 100;  
}

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
            $row = self::db_query("SELECT profit_bal,ac_ballance, rechargeproid, profile_creator, rechargepro_cordinator, rechargeprorole, transfer_activation FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            $transfer_activation = $row[0]['transfer_activation'];
            if ($transfer_activation == 0) {
                return array("status" => "100", "message" =>
                        "Account not Eligible, Please contact your account officer or Support Department");
            }


           
           if($channel != 1){
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


            $deductamount = $amount + $this->transactionfee;

            if ($ac_ballance < $deductamount) {
                return array("status" => "100", "message" => "Insufficient Fund");
            }
            
            
            
            
            
            
             
            $newballance = $ac_ballance - $deductamount;
            

                if($channel != 1){
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }else{
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }

            $rechargeproprofit = $this->transactionfee - $this->wave_fee;

            self::db_query("UPDATE rechargepro_transaction_log SET cordinator_id =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=?,rechargeproprofit =? WHERE transactionid = ? LIMIT 1",
                array(
                $rechargepro_cordinator,
                "PAID",
                $profile_creator,
                $rechargeproid,
                2,
                $rechargeproprofit,
                $tid));
        }
        
        
  $ref = $transaction_date . "_" . $tid;      
$jsonpost = '{"account_bank":"'.$thirdPartyCode.'","account_number":"'.$account.'","amount":"'.$amount.'","seckey":"'.$this->private_key.'","narration":"'.$address.'","currency":"NGN","reference":"'.$ref.'","beneficiary_name":"'.$name.'"}';

     
   
     $wavepost = self::wave_post($jsonpost, "v2/gpx/transfers/create");
     
     
     
     
             
        if (!isset($wavepost['status'])) {
        
        if($this->proccess_count == 0){
        $this->proccess_count = 1;
        return self::complete_transaction($parameter);
        }
        
        return array("status" => "100", "message" =>
        "An error occured please contact support with TID $tid");
        }
     
     
     
    
    $myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
    $myac_ballance = $myrow[0]['ac_ballance'];
    $myprofit_bal = $myrow[0]['profit_bal'];
                    
                    
        if (isset($wavepost['status'])){

            if ($wavepost['status'] == "success"){
                $status = $wavepost['message'];
                $statuscode = "0";
                $statusreference = $ref;
                
                
              if($amount > 20000){
              $this->transactionfee = 100;  
              }


                $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                    '","Account Name":"' . $name . '","Narration":"' . $address .
                    '","Reference Number":"' . $statusreference . '","Transfer Amount":"' . $amount .
                    '","Transaction fee":"' . $this->transactionfee .
                    '","Total Amount":"' . ($amount+$this->transactionfee) .
                    '","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';


                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    1,
                    $result,
                    $tid));

                self::que_rechargepropay_mail($tid, $email, "success");
                
              
$response = self::array_flatten(json_decode($result,true));

if(isset($response['details'])){
$temarray = $response['details'];
}else{
$temarray = $response;
}

if(!empty($temarray)){
        if(count($temarray) > 0){
        foreach (self::myarray() as $a) {
           
            if (array_key_exists($a, $temarray)) {
                unset($temarray[$a]);
            }
            }
        }}

                return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => $temarray));
            } else {
                
                if ($wavepost['status'] == "error") {
  

                  $ref = $transaction_date . "_" . $tid;
                  
                  $wavepost = self::wave_post("","v2/gpx/transfers?seckey=".$this->private_key."&reference=".$ref,"GET");
                  
                
                   if ($wavepost['status'] == "success"){
                   $statusreference = $ref;
                   $result = '{"details":{"Product":"BANK TRANSFER","Account Number":"' . $account .
                    '","Account Name":"' . $name . '","Narration":"' . $address .
                    '","Reference Number":"' . $statusreference . '","Transfer Amount":"' . $amount .'","Transaction fee":"' . $this->transactionfee .'","Total Amount":"' . ($amount+$this->transactionfee) .'","responseMessage":"Successful Transaction","status":"ACCEPTED","statusCode":"0","responseCode":"0"}}';
                    

                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    1,
                    $result,
                    $tid));
                    
              self::que_rechargepropay_mail($tid, $email, "success");


$response = self::array_flatten(json_decode($result,true));

if(isset($response['details'])){
$temarray = $response['details'];
}else{
$temarray = $response;
}

if(!empty($temarray)){
        if(count($temarray) > 0){
        foreach (self::myarray() as $a) {
           
            if (array_key_exists($a, $temarray)) {
                unset($temarray[$a]);
            }
            }
        }}

                return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                        "status" => "Accepted",
                        "TransactionID" => $tid,
                        "details" => $temarray)); 
                    
                             
                   }else{
                    
                    
                $status = $wavepost['status'];
                $statuscode = $wavepost['data'];
                $statusreference = $wavepost['message'];


                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    $tid));


               // self::que_rechargepro_mail($tid, $email, $wavepost);

                return array("status" => "100", "message" => array(
                        "status" => "Failed",
                        "TransactionID" => $tid,
                        "details" => $wavepost));
                    
                   }
                   
                   
                    }else{
                
            
            

                $status = $wavepost['status'];
                $statuscode = $wavepost['data'];
                $statusreference = $wavepost['message'];


                self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
                    array(
                    $status,
                    $statuscode,
                    $statusreference,
                    $tid));


               // self::que_rechargepro_mail($tid, $email, $wavepost);

                return array("status" => "100", "message" => array(
                        "status" => "Failed",
                        "TransactionID" => $tid,
                        "details" => $wavepost));
}

            }

        } else {
            
            return array("status" => "100", "message" => array(
                    "status" => "Failed",
                    "TransactionID" => $tid,
                    "details" => "Unresolved Error"));
        }
    }


    function auth_wave()
    {

        $nowdate = date("Y-m-d H:i:s");
        $row = self::db_query("SELECT setting_value FROM settings WHERE setting_key = 'money_wave_auth' AND setting_date >= ?",
            array($nowdate));
        $setting_value = $row[0]['setting_value'];

        if (!empty($setting_value)) {
            return $setting_value;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://live.moneywaveapi.co/v1/merchant/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>
                "apiKey=FLWPUBK-d9774ccef433a08174cc2597b7f0119c-X&secret=FLWSECK-efba7abe0decca4441c236caf91d9c76-X",
            CURLOPT_HTTPHEADER => array("cache-control: no-cache",
                    "content-type: application/x-www-form-urlencoded"),
            ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $j = json_decode($response, true);


        $date = date("Y-m-d H:i:s", strtotime("+1 hours", strtotime(date("Y-m-d H:i:s"))));
        self::db_query("UPDATE settings SET setting_value =?, setting_date = ? WHERE setting_key = ? LIMIT 1",
            array(
            $j['token'],
            $date,
            "money_wave_auth"));

        return $j['token'];
    }


    function wave_post($fields, $url,$type="POST")
    {
        $p = "";
        if($type == "GET"){$p = "";}
       // $auth = self::auth_wave();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ravepay.co/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                //"authorization: " . $auth,
                "cache-control: no-cache",
                "content-type: application/json",
                //"postman-token: ead715f2-d759-857a-610c-bb52d029ef83"
                )
            ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $j = json_decode($response, true);
        return $j;
    }


}
?>