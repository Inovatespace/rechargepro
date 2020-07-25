<?php
class spectranet extends Api
{
    //201804191335
    //201804191330
    //KEDCO
    //54150432331
    //2018-08-03 12:03:52
    
    
    public function __construct($method)
    {

       $this->baseUrl = self::config('brixurl');
        $this->username = self::config('brixusername');
        $this->token = self::config('brixtoken');


        $this->transaction_fee = 100;
        $this->proccess_count = 0;
                
    }



    public function auth_transaction($parameter)
    {

                if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Amount is required");
        }
        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        if (!isset($parameter['bundle'])) {
            return array("status" => "100", "message" => "Bundle is Missing");
        }
        
               if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }

            


 $bundle = urldecode(trim($parameter['bundle']));
        $amount = self::cleandigit(urldecode($parameter['amount']));
        $phone = urldecode(trim($parameter['mobile']));
        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }
        $service = urldecode($parameter['service']);
        $accountnumber = $phone;

        if ($amount == 0 || $amount == "" || empty($amount)) {
            return array("status" => "100", "message" => "Invalid Amount");
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



  
            
       $name = $accountnumber;
        
            $thirdParty = "";
            $business = "";
            $thirdParty = "";
            $unique = "";
            $address = "";
            $district = "";
        


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
            if($rechargeprorole < 4){
            if($is_service_charge == 1){
             $totalmount = $amount+$service_charge; 
             $myservice_charge = $service_charge;  
            }
            
             }
             
             
             //invalid key
            if (empty($rechargeproid)) {
              if($parameter['private_key'] != "web"){  
                return array("status" => "100", "message" => "Invalid Key");
                }
                 $rechargeproid = "0"; 
                 $myservice_charge = "100";
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
            $bundle,
            $address,
            $name,
            $unique));
            
            
            
            



        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount"=>$amount,
                "totalamount"=>$totalmount,
                "tfee"=>$tfee,
                "address" => $address,
                "unique" => $unique,
                "thirdParty" => $bundle,
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
$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];


            
            $response = json_decode($result, true);
            if (!isset($response['details'])) {
                $response['details'] = $response;
            }
            
            if(isset($response['details']['pin'])){
            $response['Token'] = $response['details']['pin'];
            }
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
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
            $profit_bal = $row[0]['profit_bal'];
            $service_charge = $row[0]['service_charge'];
            $is_service_charge = $row[0]['is_service_charge'];
                 
                 
             $myservice_charge = 0;           
            if($rechargeprorole < 4){
            if($is_service_charge == 1){
             $myservice_charge = $service_charge;  
            }
            
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




        $tfee = 0;
        if ($rechargeprorole > 3) {
            $tfee = $this->transaction_fee;
        }

            $deduct = 1;
            if (empty($ac_ballance) || ($amount+$tfee) > $ac_ballance) {
                $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $ogaballance = $row[0]['ac_ballance'];
                $ogaautofeed = $row[0]['auto_feed_cahier_account'];
                if ($ogaautofeed == 1 && $rechargeprorole < 4) {

                    if (($amount+$tfee) > $ogaballance) {
                        return array("status" => "100", "message" => "Insufficient Fund");
                    }


                    $deduct = 0;

                    $newballance = $ogaballance - ($amount+$tfee);
                    self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $profile_creator));

                } else {
                    return array("status" => "100", "message" => "Insufficient Balance");
                }

            }


            $newballance = $ac_ballance - ($amount+$tfee);



            /////////////////////////////
            if ($deduct == 1) {
                if($channel != 1){
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }else{
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }
            }
  

            self::db_query("UPDATE rechargepro_transaction_log SET service_charge=?, cordinator_id =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,payment_method=?,rechargepro_service_charge=? WHERE transactionid = ? LIMIT 1",
                array(
                $myservice_charge,
                $rechargepro_cordinator,
                "PAID",
                $profile_creator,
                $rechargeproid,
                2,$tfee,
                $tid));


            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }

        
        
$statusreference = $transaction_date. $tid;

  
 
$requestBody = '{"meter":"'.$accountnumber.'","customer_reference":"'.$statusreference.'"}';




 $response = self::mobifin_post("api/billpay/internet/BPI-NGCA-BGA/".$thirdPartyCode, $requestBody, true);   

$response = json_decode($response,true);






$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];



         if (isset($response['client_apiresponse'])) {
        $response = self::json_clean_decode($response['client_apiresponse'],true);
        if(!isset($response['reference'])){
          $response['reference'] = $accountnumber;  
        }
        }
        
        
        
        if (!isset($response['status'])) {
                
          include "refund.php";
        $refund = new refund("POST");
       $myrefund = $refund->refund_now($parameter);
       if($myrefund == "200"){
return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
"status" => "Accepted",
"TransactionID" => $tid,
"details" =>array("T Status"=>"Successful","comment"=>"Please Check your transaction Log")));
}else{
return array("status" => "100", "message" =>"Transaction Reversed");
}
        }
        
        
        
        if ($response['code'] == "RECHARGE_FAILED") {
            include "refund.php";
        $refund = new refund("POST");
       $myrefund = $refund->refund_now($parameter);
       if($myrefund == "200"){
return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
"status" => "Accepted",
"TransactionID" => $tid,
"details" =>array("T Status"=>"Successful","comment"=>"Please Check your transaction Log")));
}else{
return array("status" => "100", "message" =>"Transaction Reversed");
}
        }
        
        

        $response['Phone'] = $accountnumber;
        //olisa
$response['service_charge'] = "N100";
$response['Total_amount'] = "N".($amount+100);
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';

        if ($response['status'] == "200" || $response['status'] == "201" || $response['status'] == "429") {

            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));

            $status = $response['message'];
            $statuscode = "0";
            $statusreference = $response['reference'];

            
            //self::que_rechargepropay_sms($tid);
            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $result,
                $tid));
                
                if(!isset($parameter['sms'])){
                            if($response['pin_based'] == true){
            $message = "Token:".$response['pins'][0]['pin']."\r\nAmount:$amount \r\nInvoice Number:".$rechargeproid."_".$tid."\r\nvisit rechargepro.com.ng, For print out";
            self::curlit($phone, $message);
            }else{
             self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:".$rechargeproid."_".$tid."\r\nvisit rechargepro.com.ng, For print out");   
            }}
                
                self::que_rechargepropay_mail($tid, $email, "success");

            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => self::array_flatten($response)));
        } else if($response['status'] == "500") {
            
            return self::verify_mobifin($parameter);
            
            } else {
                
                
                
     if (isset($response['client_apiresponse'])) {

        $response = self::json_clean_decode($response['client_apiresponse'],true);
        

        $response['Phone'] = $accountnumber;
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';

        if ($response['status'] == "500") {
          include "refund.php";
        $refund = new refund("POST");
       $myrefund = $refund->refund_now($parameter);
       if($myrefund == "200"){
return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
"status" => "Accepted",
"TransactionID" => $tid,
"details" =>array("T Status"=>"Successful","comment"=>"Please Check your transaction Log")));
}else{
return array("status" => "100", "message" =>"Transaction Reversed");
}
           
           }
        
        }
        
     
                

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


            
            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                $tid));
                  
            include "refund.php";
        $refund = new refund("POST");
        $myrefund = $refund->refund_now($parameter);
        
if($myrefund == "200"){
return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
"status" => "Accepted",
"TransactionID" => $tid,
"details" =>array("T Status"=>"Successful","comment"=>"Please Check your transaction Log")));
}else{
return array("status" => "100", "message" =>"Transaction Reversed");
}

            //return array("status" => "100", "message" => array("status" => "Failed","TransactionID" => $tid,"details" => $response));
        }

        


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

 
$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];






        $response = self::mobifin_post("topup/log/byref/" . $transaction_date.$tid, "", false);
        

         if(!isset($response['boy'])){
            include "refund.php";
            $refund = new refund("POST");
            $myrefund = $refund->refund_now($parameter);
            return array("status" => "100", "message" =>"Transaction Reversed");
            }
                
      
         if (!isset($response['client_apiresponse'])) {
            
                
            //include "refund.php";
        //$refund = new refund("POST");
        //$myrefund = $refund->refund_now($parameter);
        //return array("status" => "100", "message" =>"Transaction Reversed");
            return array("status" => "100", "message" =>
                   "Pending Transaction");
        }
        
        
        //$response = array();
        //$response = self::json_clean_decode($response['client_apiresponse']);
        
        $response = self::json_clean_decode($response['client_apiresponse'],true);
        
       

        $response['Phone'] = $accountnumber;
        //olisa
$response['service_charge'] = "N100";
$response['Total_amount'] = "N".($amount+100);
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';

        if ($response['status'] == "200" || $response['status'] == "201") {

            self::db_query("UPDATE rechargepro_services SET wallet = wallet+? WHERE services_key = ? LIMIT 1",
                array($amount, $service));

            $status = $response['message'];
            $statuscode = "0";
            $statusreference = $response['reference'];

            
           // self::que_rechargepropay_sms($tid);
            self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                array(
                $status,
                $statuscode,
                $statusreference,
                1,
                $result,
                $tid));
                
                if(!isset($parameter['sms'])){
                            if($response['pin_based'] == true){
            $message = "Token:".$response['pins'][0]['pin']."\r\nAmount:$amount \r\nInvoice Number:".$rechargeproid."_".$tid."\r\nvisit rechargepro.com.ng, For print out";
            self::curlit($phone, $message);
            }else{
             self::curlit($phone, "Thank you, Payment is successful \r\nInvoice Number:".$rechargeproid."_".$tid."\r\nvisit rechargepro.com.ng, For print out");   
            }}
                
                self::que_rechargepropay_mail($tid, $email, "success");

            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => self::array_flatten($response)));
        } else{
            
            
        

        if ($response['status'] == "500") {
          include "refund.php";
        $refund = new refund("POST");
       $myrefund = $refund->refund_now($parameter);
       if($myrefund == "200"){
return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
"status" => "Accepted",
"TransactionID" => $tid,
"details" =>array("T Status"=>"Successful","comment"=>"Please Check your transaction Log")));
}else{
return array("status" => "100", "message" =>"Transaction Reversed");
}
           
           }
        
        
        
        
        
        

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

         //   include "refund.php";
       // $refund = new refund("POST");
        //$myrefund = $refund->refund_now($parameter);
        //if($myrefund == "200"){
//return array("status" => "200", "message" => array(
//"status" => "Accepted",
//"TransactionID" => $tid,
//"details" =>array("T Status"=>"Successful","comment"=>"Please Check your transaction Log")));
//}else{
//return array("status" => "100", "message" =>"Transaction Reversed");
//}
            return array("status" => "100", "message" => "Pending Transactionb");
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
                    
                    return array("boy"=>"boy");
                }

        return $response;
    }


    
    
    


}
?>