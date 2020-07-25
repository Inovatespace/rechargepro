<?php

class bills extends Api
{

    //KEDCO

    public function __construct($method)
    {
        $this->transaction_fee = 100;
        $this->switch_clientid = "IKIA1DB0E7FA90F910355F09DDAF9EC68ABC4C22BA52";
        $this->switch_secrete = "idOcCImWru/FQj/b5+vBqiKZ1dIc5suUUt/zSkOozXk=";
        $this->switch_treminalid = "3BQA0001";
        $this->switch_transfer_prefix = "1681";
    }


    function secondary($bill_secondary_field){
        
         $secondary_type = "";
         
        if (strpos($bill_secondary_field, '@') !== false) {
    $ex = explode("@",$bill_secondary_field);
    $bill_secondary_field_title = $ex[0];
 
    if($ex[1] == "select"){
$fieldcount++;
$secondary_type["title"] = $bill_secondary_field_title;



    if (strpos($ex[2], '=') !== false) {
    $ex = explode(";",$ex[2]);
    for($i = 0; $i < count($ex); $i++){
    $exb = explode("=",$ex[$i]);
        $secondary_type[$exb[0]] =$exb[1];
    }
    }
  
    }else{
        $fieldcount++;
       $secondary_type = $bill_secondary_field_title; 
    }
    

    }
    
    return $secondary_type;
    
    }
    
    
    function tertiary($bill_tertiary_field){
        $tertiary_type = "";
        
    if (strpos($bill_tertiary_field, '@') !== false) {
    $ex = explode("@",$bill_tertiary_field);
    $bill_tertiary_field_title = $ex[0];
    
    if($ex[1] == "select"){
    $fieldcount++;
    $tertiary_type["title"] = $bill_tertiary_field_title;
        
    if (strpos($ex[2], '=') !== false) {
    $ex = explode(";",$ex[2]);
    for($i = 0; $i < count($ex); $i++){
    $exb = explode("=",$ex[$i]);
$tertiary_type[$exb[0]] = $exb[1];
    }
    }
    
    }else{
        $fieldcount++;
        $tertiary_type = $bill_tertiary_field_title;
    }
    
    }
    
    return $tertiary_type;
    }
    

    
    public function bill_list($parameter)
    {
        $row = self::db_query("SELECT services_key, service_name, bill_primary_field,bill_secondary_field,bill_tertiary_field FROM rechargepro_services WHERE services_category = '41' AND status ='1'",
            array());
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
               $return[$row[$dbc]['services_key']] = array(
               'name'=>$row[$dbc]['service_name'],
               'primary'=>$row[$dbc]['bill_primary_field'],
               'secondary'=>self::secondary($row[$dbc]['bill_secondary_field']),
               'tertiary'=>self::tertiary($row[$dbc]['bill_tertiary_field'])
               );
        }
        return $return;
    }
    
    
        public function single_bill_list($parameter)
    {
        
       if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        
        $service = $parameter['service'];
        
        $row = self::db_query("SELECT services_key, service_name, bill_primary_field,bill_secondary_field,bill_tertiary_field FROM rechargepro_services WHERE services_key =? AND status ='1'",
            array($service));
        $return = array();
        
               $return[$row[0]['services_key']] = array(
              'name' => $row[0]['service_name'],
               'primary' => $row[0]['bill_primary_field'],
               'secondary' => self::secondary($row[0]['bill_secondary_field']),
               'tertiary' => self::tertiary($row[0]['bill_tertiary_field']),
               );
        
        return $return;
    }
    
    
    
    

    public function with_code($parameter)
    {

        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid private_key");
        }

        if (!isset($parameter['code'])) {
            return array("status" => "100", "message" => "Invalid Biller Code");
        }


        $row = self::db_query("SELECT bill_primary_field,bill_secondary_field,bill_tertiary_field,service_name,bill_return_url,bill_verify_url,bill_need_phone,bill_need_amount FROM rechargepro_services WHERE services_key = ? AND services_category = ? AND status = '1' LIMIT 1",
            array($parameter['code'], 7));

        $bill_primary_field = $row[0]['bill_primary_field'];
        $bill_secondary_field = $row[0]['bill_secondary_field'];
        $bill_tertiary_field = $row[0]['bill_tertiary_field'];

        $rechargepro_service = $row[0]['service_name'];
        $bill_return_url = $row[0]['bill_return_url'];
        $bill_verify_url = $row[0]['bill_verify_url'];
        
        $bill_need_phone = $row[0]['bill_need_phone'];
        $bill_need_amount = $row[0]['bill_need_amount'];

//file_put_contents("ff.php",$parameter['code']);

        if (empty($rechargepro_service)) {
            return array("status" => "100", "message" => "Invalid Merchant Code");
        }


        return array(
            "status" => "200",
            "name" => $rechargepro_service,
            "bill_primary_field" => $bill_primary_field,
            "bill_secondary_field" => $bill_secondary_field,
            "bill_tertiary_field" => $bill_tertiary_field,"bill_need_phone"=>$bill_need_phone,"bill_need_amount"=>$bill_need_amount);


    }


    public function auth_transaction($parameter)
    {

/**
 *         if (!isset($parameter['mobile'])) {
 *             return array("status" => "100", "message" => "Invalid mobile");
 *         }
 *         
 *         if (!isset($parameter['amount'])) {
 *             return array("status" => "100", "message" => "Invalid amount");
 *         }
 */

        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
        }

        if (!isset($parameter['primary'])) {
            return array("status" => "100", "message" => "Enter Compulsory Fields");
        }

        if (!isset($parameter['tertiary'])) {
            return array("status" => "100", "message" => "Enter Compulsory Fields");
        }

        if (!isset($parameter['secondary'])) {
            return array("status" => "100", "message" => "Enter Compulsory Fields");
        }



        $service = urldecode($parameter['service']);
        $primary = urldecode($parameter['primary']);
        $secondary = urldecode($parameter['secondary']);
        $tertiary = urldecode($parameter['tertiary']);
        
        
/**
 *         $mobile = urldecode(trim($parameter['mobile']));
 *         $amount = self::cleandigit(urldecode($parameter['amount']));

 *         if ($amount == 0 || $amount == "" || empty($amount)) {
 *             return array("status" => "100", "message" => "Invalid Amount");
 *         }

 *         if (strlen($mobile) > 11 || strlen($mobile) < 11) {
 *             return array("status" => "100", "message" => "Invalid Mobile Number");
 *         }
 */

        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }


        $row = self::db_query("SELECT service_name,bill_return_url,bill_verify_url,minimumsales_amount,maximumsales_amount,status,hasamount,service_resuest,bill_need_phone,bill_need_amount,bill_need_service_charge FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $rechargepro_service = $row[0]['service_name'];
        $bill_return_url = $row[0]['bill_return_url'];
        $bill_verify_url = $row[0]['bill_verify_url'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status'];
        $hasamount = $row[0]['hasamount'];
        $service_resuest = $row[0]['service_resuest'];
        $bill_need_service_charge = $row[0]['bill_need_service_charge'];
        
        
        $bill_need_phone = $row[0]['bill_need_phone'];
        $bill_need_amount = $row[0]['bill_need_amount'];

        if ($status == 0){
            return array("status" => "100", "message" =>
                    "This service is curently Not Active".$service);
        }
        
        $amount = 0;
        
        
        if($hasamount == 1){
         $amount = $primary;  
        }
        
        
        if($hasamount == 2){
            if(!empty($service_resuest)){
             $_resuest = json_decode($service_resuest,true);
             if(isset($_resuest[$secondary])){
               $amount = $_resuest[$secondary];  
             }else{
                return array("status" => "100", "message" => "Biller Error, Please contact support");
             }
              
            }else{
           $amount = $secondary;    
          }
        }
           
      
        if($hasamount == 3){
            if(!empty($service_resuest)){
             $_resuest = json_decode($service_resuest,true);
             if(isset($_resuest[$tertiary])){
               $amount = $_resuest[$tertiary];  
             }else{
                return array("status" => "100", "message" => "Biller Error, Please contact support");
             }
              
            }else{
           $amount = $tertiary;  
          }
        }
        
        
        if(isset($parameter['amount'])){
            $amount = $parameter['amount'];
        }
        
        $mobile = $primary;
        if(isset($parameter['mobile'])){
        $mobile = $parameter['mobile'];
                    if (strlen($mobile) > 11 || strlen($mobile) < 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }
        }
        
          
          if($amount > 0){

        if ($minimumsales_amount > $amount) {
            return array("status" => "100", "message" => "Minimum Amount Allowed: $minimumsales_amount");
        }

        if ($amount > $maximumsales_amount) {
            return array("status" => "100", "message" => "Maximum Amount Allowed: $maximumsales_amount");
        }
        
        }

        if (empty($rechargepro_service)) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        
        
        
          $name = $primary;

        if (strlen($bill_verify_url) > 5) {
            if (filter_var($bill_verify_url, FILTER_VALIDATE_URL)) {
                
                
                
                $postfield = array(
                    "status" => "Querry",
                    "primary" => $primary,
                    "secondary" => $secondary,
                    "tertiary" => $tertiary,
                    "amount" => $amount);
                    

                    $response = self::file_get_b($postfield,$bill_verify_url);


        } else {
            $response = include_once "../billers/".$bill_verify_url;
        }
                
        
        if(!isset($response['status'])){
         return array("status" => "100", "message" => "Error Processing Trasaction");   
        }
        
        if($response['status'] == "100"){
         return array("status" => "100", "message" => $response['message']);   
        }
        
        $amount = $response['message']['amount'];
        $name = $response['message']['name'];
                //amount //name //status = 0 or 1
            
        }
        
        //if post return name, set name
      

        #LASER
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
        if ($bill_need_service_charge == 1) {
            $tfee = $myservice_charge;
        }
        
        $totalmount = $amount+$tfee;
        

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (service_charge,rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email,business_district,thirdPartycode,name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $tfee,
            $rechargeproid,
            $ip,
            $rechargepro_service,
            $service,
            $primary,
            $amount,
            $mobile,
            $email,
            $secondary,
            $tertiary,
            $name));
            
    
        return array("status" => "200", "message" => array(
                "name" => $name,
                "amount"=>$amount,
                "totalamount"=>$totalmount,
                "tfee"=>$tfee,
                "ac" => $primary,
                "primary" => $primary,
                "secondary" => $secondary,
                "tertiary" => $tertiary,
                "tid" => $insertid));
    }


    public function complete_transaction($parameter)
    {
        $tid = $parameter['tid'];

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        $tid = urldecode($parameter['tid']);


        $row = self::db_query("SELECT rechargeproid,service_charge,rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $rechargeproid = $row[0]['rechargeproid'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['rechargepro_subservice'];
        $primary = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $result = $row[0]['rechargepro_print'];
        $service_charge = $row[0]['service_charge'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

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

        if ($row[0]['rechargepro_status'] != "PAID") {

            if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }


            $row = self::db_query("SELECT service_name,cordinator_percentage,percentage,bill_return_url,bill_verify_url,bill_formular,bill_rechargeprofull_percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",
                array($service));
            $percentage = $row[0]['percentage'];
            $bill_return_url = $row[0]['bill_return_url'];
            $bill_verify_url = $row[0]['bill_verify_url'];
            $bill_formular = $row[0]['bill_formular'];
            $cordinator_percentage = $row[0]['cordinator_percentage'];
            $service_name = $row[0]['service_name'];
            $bill_rechargeprofull_percentage = $row[0]['bill_rechargeprofull_percentage'];


            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT ac_ballance, rechargeproid, profile_creator, rechargepro_cordinator, rechargeprorole FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $ac_ballance = $row[0]['ac_ballance'];
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            $rechargeprorole = $row[0]['rechargeprorole'];
            $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];

            $deduct = 1;
            if (empty($ac_ballance) || (($amount+$this->transaction_fee) > $ac_ballance)) {
                $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $ogaballance = $row[0]['ac_ballance'];
                $ogaautofeed = $row[0]['auto_feed_cahier_account'];
                if ($ogaautofeed == 1 && $rechargeprorole < 4) {

                    if (($amount+$this->transaction_fee) > $ogaballance) {
                        return array("status" => "100", "message" => "Insufficient Fund");
                    }


                    $deduct = 0;

                    $newballance = $ogaballance - ($amount+$this->transaction_fee);
                    self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $profile_creator));

                } else {
                    return array("status" => "100", "message" => "Insufficient Balance");
                }

            }


            $newballance = $ac_ballance - ($amount+$this->transaction_fee);


            if ($deduct == 1) {
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

            //PER HERE
            include "percentage.php";
            $percentage = new percentage("POST");
            $percentage->calculate_per($parameter);
        }




$row = self::db_query("SELECT service_resuest,bill_return_url FROM rechargepro_services WHERE services_key = ? LIMIT 1",array($service));
$key = $row[0]['service_resuest'];
$url = $row[0]['bill_return_url'];


$statusreference = $transaction_date. "_" . $tid;


             $postfield = array(
                    "status" => "complete",
                    "primary" => $primary,
                    "secondary" => $district,
                    "tertiary" => $thirdPartyCode,
                    "amount" => $amount,"ref"=>$statusreference,"key"=>$key);
    
    
if (filter_var($url, FILTER_VALIDATE_URL)) {
    $responseData = self::file_get_b($postfield, $url);
} else {
    $responseData = include_once "../billers/".$url;
}


   
   
   
$myrow = self::db_query("SELECT ac_ballance,profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid));
$myac_ballance = $myrow[0]['ac_ballance'];
$myprofit_bal = $myrow[0]['profit_bal'];

   
   
    
    
if (!isset($responseData['status'])) {
    
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
    
    
    if ($responseData['status'] == "100") {
        
/**
 *                   include "refund.php";
 *         $refund = new refund("POST");
 *        $myrefund = $refund->refund_now($parameter);
 *        if($myrefund == "200"){
 * return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
 * "status" => "Accepted",
 * "TransactionID" => $tid,
 * "details" =>array("T Status"=>"Successful","comment"=>"Please Check your transaction Log")));
 * }else{
 * return array("status" => "100", "message" =>"Transaction Reversed");
 * }
 */

 return array("status" => "100", "message" =>$responseData['message']);    
        
        }
        
        
        if ($responseData['status'] == "300") {

return array("status" => "100", "message" =>$responseData['message']);

            }


if ($responseData['status'] == "200") {
    
    
     //result
    //
    //
    

        $status = "SUCCESS";
        $statuscode = "0";
        
        $responseData = $responseData['message'];
        
        
        if(isset($responseData['Token'])){
            if(!empty($responseData['Token'])){
            $responseData['pin'] = $responseData['Token'];
            }}
            
            $responseData['AmountPaid'] = $amount;
            $responseData['service_charge'] = $service_charge;
            $responseData['Total_amount'] = $amount+$service_charge;
            
            
            $result = json_encode($responseData);


        self::db_query("UPDATE rechargepro_transaction_log SET transaction_status =?,transaction_code =?,transaction_reference =?,rechargepro_status_code =?, rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
            array(
            $status,
            $statuscode,
            $statusreference,
            1,
            $result,
            $tid));


//send sms
         

        self::que_rechargepropay_mail($tid, $email, "success");
        //self::que_rechargepropay_sms($tid);
        
        
$responseData = self::array_flatten($responseData);


if(isset($responseData['details'])){
$temarray = $responseData['details'];
}else{
$temarray = $responseData;
}

if(!empty($temarray)){
        if(count($temarray) > 0){
        foreach (self::myarray() as $a) {
           
            if (array_key_exists($a, $temarray)) {
                unset($temarray[$a]);
            }
            }
        }}
        
       // return array("status" => "100", "message" => json_encode($temarray));

         return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $temarray));

    
    }


    }
    
    
    


}
?>