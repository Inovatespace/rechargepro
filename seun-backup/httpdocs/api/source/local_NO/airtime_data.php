<?php
class airtime_data extends Api
{
    //36680.68
    //KEDCO
    //39330.68
    
    //503 invalid smdn
    function product_id($number, $service)
    {

        $smscost = array(
            "0701" => "1",
            "0703" => "5",
            //"0705" => "6",
            "0706" => "5",
            "0708" => "1",
            "0802" => "1",
            "0803" => "5",
            //"0805" => "6",
            "0806" => "5",
           // "0807" => "6",
            "0808" => "1",
            "0809" => "2",
            "0810" => "5",
            //"0811" => "6",
            "0812" => "1",
            "0813" => "5",
            "0814" => "5",
            //"0815" => "6",
            "0816" => "5",
            "0817" => "2",
            "0818" => "2",
            "0909" => "2",
            "0908" => "2",
            "0902" => "1",
            "0903" => "5",
            //"0905" => "6",
            "0906" => "5",
            "0907" => "1");

        $first3 = substr($number, 0, 4);
        //$first3 = "0".substr($first3, 3);

        $service = "0";
        if (array_key_exists($first3, $smscost)) {
            $service = $smscost[$first3];
        }

        switch ($service) {
            case "1":
                $productid = "2351";
                break;

            case "2":
                $productid = "2352";
                break;

            case "5":
                $productid = "2353";
                break;

            case "6":
                $productid = "2354";
                break;

            default:
                $productid = $service;
        }

        return $productid;
    }


    public function __construct($method)
    {
        $this->transaction_fee = 100;
        $this->proccess_count = 0;        
    }


    public function network_list($parameter)
    {
        $row = self::db_query("SELECT services_key,service_name FROM rechargepro_services WHERE services_category = ?  AND status = '1' ORDER BY id",
            array(2));
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = $row[$dbc]['service_name'];
        }
        return $return;
    }

    public function data_network_list($parameter)
    {
        $row = self::db_query("SELECT services_key,service_name FROM rechargepro_services WHERE services_category = ?  AND status = '1' ORDER BY id",
            array(3));
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = $row[$dbc]['service_name'];
        }
        return $return;
    }
    
    


    public function auth_airtime($parameter)
    {

        if (!isset($parameter['amount'])) {
            return array("status" => "100", "message" => "Amount is Missing");
        }

        if (!isset($parameter['mobile'])) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($parameter['service'])) {
            return array("status" => "100", "message" => "Invalid Service");
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


        $accountnumber = $phone;


        //check service
        if ($amount == 0 || $amount == "" || empty($amount)){
            return array("status" => "100", "message" => "Invalid Amount");
        }
        
        
        
                
        if($service == "2354"){
        include "glo.php";
        $glo = new glo("POST");
        return $glo->vend_airtime($parameter);
        }
        
        
        if($service == "ANA"){
        include "smile.php";
        $smile = new smile("POST");
        return $smile->auth_airtime($parameter);
        }
        
       
        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $rechargepro_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status'];

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

        if ($amount < 1) {
            return array("status" => "100", "message" => "Invalid Amount");
        }

        #LASER
        $rechargeproid = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            
                         //invalid key
            if (empty($rechargeproid)) {
                if($parameter['private_key'] != "web"){  
                return array("status" => "100", "message" => "Invalid Key");
                }
                 $rechargeproid = "0"; 
            }
        }

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,ip,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email) VALUES (?,?,?,?,?,?,?,?)",
            array(
            $rechargeproid,
            $ip,
            $rechargepro_service,
            $service,
            $accountnumber,
            $amount,
            $phone,
            $email));


        return array("status" => "200", "message" => array(
                "name" => $accountnumber,
                "amount"=>$amount,
                "totalamount"=>$amount,
                "tfee"=>0,
                "details" => $accountnumber,
                "amount" => $amount,
                "tid" => $insertid));
    }

    public function auth_data($parameter)
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
        $amount = urldecode(trim($parameter['amount']));
        $phone = urldecode(trim($parameter['mobile']));
        $email = "";
        if (isset($parameter['email'])) {
            $email = urldecode($parameter['email']);
        }
        $service = urldecode($parameter['service']);
        $accountnumber = $phone;
        
        
      if($service == "ADC"){
        include "glo.php";
        $glo = new glo("POST");
        return $glo->vend_data($parameter);
        }
        
        
        if($service == "ANB"){
        include "smile.php";
        $smile = new smile("POST");
        return $smile->auth_data($parameter);
        }
        
        
        if($service == "BGA"){
        include "spectranet.php";
        $spectranet = new spectranet("POST");
        return $spectranet->auth_transaction($parameter);
        }


        $row = self::db_query("SELECT service_name,minimumsales_amount,maximumsales_amount,status FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $rechargepro_service = $row[0]['service_name'];
        $minimumsales_amount = $row[0]['minimumsales_amount'];
        $maximumsales_amount = $row[0]['maximumsales_amount'];
        $status = $row[0]['status'];

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


        #LASER
        $rechargeproid = "0";
        if (isset($parameter['private_key'])) {
            $private_key = $parameter['private_key'];
            $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            
             //invalid key
            if (empty($rechargeproid)) {
                if($parameter['private_key'] != "web"){  
                return array("status" => "100", "message" => "Invalid Key");
                }
                 $rechargeproid = "0"; 
            }
        }

        $ip = self::getRealIpAddr();
        $insertid = self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,ip,thirdPartycode,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,email) VALUES (?,?,?,?,?,?,?,?,?)",
            array(
            $rechargeproid,
            $ip,
            $bundle,
            $rechargepro_service,
            $service,
            $accountnumber,
            $amount,
            $phone,
            $email));


        return array("status" => "200", "message" => array(
                "name" => $accountnumber . " " . $bundle,
                "amount"=>$amount,
                "totalamount"=>$amount,
                "details" => $accountnumber,
                "tfee"=>0,
                "amount" => $amount,
                "tid" => $insertid));
        
    }

function formatBytes($size) {

if(strlen($size) == 2 || strlen($size) == 3){return $size."MB";}

if(strlen($size) == 4){
 $nozero = rtrim($size, "0");
 if(strlen($nozero) == 1){ return $nozero."GB";}
 
 $first = substr($nozero,0,1); 
 $second = substr($nozero,1); 
 return $first.".".$second."GB";
}


if(strlen($size) == 5){
 $nozero = rtrim($size, "0");
 if(strlen($nozero) == 1){ return $nozero."0GB";}
 
 $first = substr($nozero,0,2); 
 $second = substr($nozero,2); 
 return $first.".".$second."GB";
}

if(strlen($size) == 6){
 $nozero = rtrim($size, "0");
 if(strlen($nozero) == 1){ return $nozero."000GB";}
 if(strlen($nozero) == 2){ return $nozero."00GB";}
 
 $first = substr($nozero,0,3); 
 $second = substr($nozero,3); 
 return $first.".".$second."GB";
}

return $size;
} 


    public function available_bundle($parameter)
    {
        if(!isset($parameter['service'])){  return array("status" => "100", "message" => "Service code required");}

        $service = $parameter['service'];

        //chek
        $date = date("Y-m-d");
        $row = self::db_query("SELECT setting_value FROM settings WHERE setting_key = ? AND setting_date > ? LIMIT 1",
            array($service, $date));
        $setting_value = $row[0]['setting_value'];
        
        
     
if($service == "ADC"){
    //GLO
$setting_value = '{"bundles":[
{"isAvailable":true,"ercValue":"DATA-18","price":50,"name":null,"allowance":"27.5 MB 1 day","validity":"1 day"},
{"isAvailable":true,"ercValue":"DATA-21","price":100,"name":null,"allowance":"100MB 1 day","validity":"1 day"},
{"isAvailable":true,"ercValue":"DATA-28","price":200,"name":null,"allowance":"262MB 5 day","validity":"5 day"},
{"isAvailable":true,"ercValue":"DATA-27","price":500,"name":null,"allowance":"1GB 14 days","validity":"14 day"},
{"isAvailable":true,"ercValue":"DATA-2","price":1000,"name":null,"allowance":"2GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-25","price":2000,"name":null,"allowance":"4.5GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-19","price":2500,"name":null,"allowance":"7.2GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-23","price":3000,"name":null,"allowance":"8.75GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-12","price":4000,"name":null,"allowance":"12.5GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-5","price":5000,"name":null,"allowance":"15.6GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-4","price":8000,"name":null,"allowance":"25GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-10","price":10000,"name":null,"allowance":"32.5GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-11","price":15000,"name":null,"allowance":"52.5GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-20","price":18000,"name":null,"allowance":"62.5GB 30 days","validity":"30 days"},
{"isAvailable":true,"ercValue":"DATA-33","price":20000,"name":null,"allowance":"78.7GB 30 days","validity":"30 days"}
]}';
}




                
        if (!empty($setting_value)) {////////////////!!!!!!!!!!!!!!

            $startimeb = array();
            $j = json_decode($setting_value,true);
            
           
            
            for ($i = 0; $i < count($j["bundles"]); $i++) {

                $code = "";
                
                if (isset($j["bundles"][$i]["price"])) {
                    $code = $j["bundles"][$i]["price"];
                }
                
                
                if (isset($j["bundles"][$i]["ercValue"])) {
                    $code = $j["bundles"][$i]["ercValue"];
                }


                if (isset($j["bundles"][$i]["ersPlanId"])) {
                    $code = $j["bundles"][$i]["ersPlanId"];
                }


                if (isset($j["bundles"][$i]["amount"])) {
                    $j["bundles"][$i]["isAvailable"] = true;
                    $j["bundles"][$i]["price"] = $j["bundles"][$i]["amount"];
                    $j["bundles"][$i]["name"] = $j["bundles"][$i]["description"];
                    $j["bundles"][$i]["allowance"] = $j["bundles"][$i]["description"];
                    $j["bundles"][$i]["validity"] = $j["bundles"][$i]["description"];
                    $code = $j["bundles"][$i]["typeCode"];
                }


                $startimeb['bundles'][] = array(
                    "isAvailable" => $j["bundles"][$i]["isAvailable"],
                    "price" => $j["bundles"][$i]["price"],
                    "name" => $j["bundles"][$i]["name"],
                    "allowance" => $j["bundles"][$i]["allowance"],
                    "validity" => $j["bundles"][$i]["validity"],
                    "code" => $code);
            }
            return array("status" => "200", "message" => $startimeb);
        }
        
        
        
       if(in_array($service,array("ACC","AEC","ALC","BGA","ANB"))){
        
if($service == "ACC"){
$response = self::mobifin_post("datatopup/info/2349024667096", "", false);
}

if($service == "AEC"){
$response = self::mobifin_post("datatopup/info/2348183874966", "", false);
}
              
if($service == "ALC"){
$response = self::mobifin_post("datatopup/info/2347032251665", "", false);
}  

if($service == "ANB"){
$response = self::mobifin_post("billpay/internet/BPI-NGCA-ANB", "", false);
} 

if($service == "BGA"){
$response = self::mobifin_post("billpay/internet/BPI-NGCA-BGA", "", false);
}       
 


        if (!isset($response['products'])) {
            return array("status" => "100", "message" =>"An error occured please contact support with");
        }
        
        
        $rt = $response['products'];
        
        $s_value = array();
        
        if(in_array($service,array("BGA","ANB"))){
         for($i=0; $i<count($rt); $i++){
            $name = preg_replace("/[^A-Za-z0-9 ]/", '', $rt[$i]['name']);
           $s_value[] = '{"isAvailable":true,"ercValue":"'.$rt[$i]['code'].'","price":'.$rt[$i]['price'].',"name":"'.$name.'","allowance":"'.$rt[$i]['price'].'","validity":""}';
        }   
        }else{
        for($i=0; $i<count($rt); $i++){
           $s_value[] = '{"isAvailable":true,"ercValue":"'.$rt[$i]['product_id'].'","price":'.$rt[$i]['denomination'].',"name":null,"allowance":"'.self::formatBytes($rt[$i]['data_amount']).'","validity":"1 day"}';
        }
        }
        
        $im = implode(",",$s_value);
        
        $setting_value = '{"bundles":['.$im.']}';
        


        //udate
        $setting_date = date("Y-m-d H:i:s");
        self::db_query("UPDATE settings SET setting_value = ?,setting_date = ? WHERE setting_key = ? LIMIT 1",
            array(
            $setting_value,
            $setting_date,
            $service));

           $startimeb = array();
            $j = json_decode($setting_value);
            for ($i = 0; $i < count($j->{"bundles"}); $i++) {

                $code = "";
                
                if (isset($j->{"bundles"}[$i]->{"price"})) {
                    $code = $j->{"bundles"}[$i]->{"price"};
                }
                
                
                if (isset($j->{"bundles"}[$i]->{"ercValue"})) {
                    $code = $j->{"bundles"}[$i]->{"ercValue"};
                }


                if (isset($j->{"bundles"}[$i]->{"ersPlanId"})) {
                    $code = $j->{"bundles"}[$i]->{"ersPlanId"};
                }


                if (isset($j->{"bundles"}[$i]->{"amount"})) {
                    $j->{"bundles"}[$i]->{"isAvailable"} = true;
                    $j->{"bundles"}[$i]->{"price"} = $j->{"bundles"}[$i]->{"amount"};
                    $j->{"bundles"}[$i]->{"name"} = $j->{"bundles"}[$i]->{"description"};
                    $j->{"bundles"}[$i]->{"allowance"} = $j->{"bundles"}[$i]->{"description"};
                    $j->{"bundles"}[$i]->{"validity"} = $j->{"bundles"}[$i]->{"description"};
                    $code = $j->{"bundles"}[$i]->{"typeCode"};
                }


                $startimeb['bundles'][] = array(
                    "isAvailable" => $j->{"bundles"}[$i]->{"isAvailable"},
                    "price" => $j->{"bundles"}[$i]->{"price"},
                    "name" => $j->{"bundles"}[$i]->{"name"},
                    "allowance" => $j->{"bundles"}[$i]->{"allowance"},
                    "validity" => $j->{"bundles"}[$i]->{"validity"},
                    "code" => $code);
            }
            return array("status" => "200", "message" => $startimeb);
}

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
       
        
        $row = self::db_query("SELECT rechargeproid, rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,email,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
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
        $rechargepro_staus = $row[0]['rechargepro_status'];
        
        
        
         
         
         

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
    
        
        if(in_array($service, array("ADC","2354"))){
        include "glo.php";
        $glo = new glo("POST");
        return $glo->buy_glo($parameter);
        }
        
        
        if(in_array($service, array("ANB","ANA"))){
        include "smile.php";
        $smile = new smile("POST");
        return $smile->complete_transaction($parameter);
        }
        
        
        if(in_array($service, array("BGA"))){
        include "spectranet.php";
        $spectranet = new spectranet("POST");
        return $spectranet->complete_transaction($parameter);
        }
        

        if (in_array($service, array(
            "2351",
            "2352",
            "2353"))) {
            return self::buy_airtime_mobifin($parameter);
        }

        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        if ($amount < 1) {
            return array("status" => "100", "message" =>
                    "Payment Not successful please contact support with TID $cartid 2");
        }

        if ($rechargepro_staus != "PAID") {

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
            $row = self::db_query("SELECT ac_ballance,profit_bal,rechargeproid, profile_creator, rechargepro_cordinator, rechargeprorole FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
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
            
            
             //if serial set device_type, serial and ip
             //include auth
             include "auth.php";
             $auth = new auth("POST");
             $parameter['rechargeproid'] = $rechargeproid;
             $validation = $auth->validation($parameter);
             if ($validation == false) {
                return array("status" => "100", "message" => "Unauthorised Transaction");
            }


            $deduct = 1;
            if (empty($ac_ballance) || $amount > $ac_ballance) {
                $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $ogaballance = $row[0]['ac_ballance'];
                $ogaautofeed = $row[0]['auto_feed_cahier_account'];
                if ($ogaautofeed == 1 && $rechargeprorole < 4) {

                    if ($amount > $ogaballance) {
                        return array("status" => "100", "message" => "Insufficient Fund");
                    }


                    $deduct = 0;

                    $newballance = $ogaballance - $amount;
                    self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $profile_creator));

                } else {
                    return array("status" => "100", "message" => "Insufficient Balanceb");
                }

            }


            $newballance = $ac_ballance - $amount;

            if ($deduct == 1) {
                if($channel != 1){
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }else{
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }
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


        $row = self::db_query("SELECT service_resuest FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $service_resuest = $row[0]['service_resuest'];
        
include "promo1.php";



        $requestBody = '{
    "product_id": "'.$thirdPartyCode.'",
    "denomination" : "'.$amount.'",
    "send_sms" : false,
    "sms_text" : "",
    "customer_reference":"'.$transaction_date.$tid.'"
}';

$tosendac = substr($accountnumber, 1);
$response = self::mobifin_post("datatopup/exec/234".$tosendac, $requestBody, true);


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
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';
        
        if ($response['status'] == "208"){
          return array("status" => "100", "message" =>"Pending Transaction");  
        }

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
                
                self::que_rechargepropay_mail($tid, $email, "success");

            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
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

     ///////////////////////////////
    public function buy_airtime_mobifin($parameter)
    {

        if (!isset($parameter['tid'])) {
            return array("status" => "100", "message" => "Invalid Transaction");
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
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

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


            $row = self::db_query("SELECT cordinator_percentage,percentage,bill_formular,bill_rechargeprofull_percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",
                array($service));
            $cordinator_percentage = $row[0]['cordinator_percentage'];
            $percentage = $row[0]['percentage'];
            $bill_formular = $row[0]['bill_formular'];
            $bill_rechargeprofull_percentage = $row[0]['bill_rechargeprofull_percentage'];

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

             //if serial set device_type, serial and ip
             //include auth
             include "auth.php";
             $auth = new auth("POST");
             $parameter['rechargeproid'] = $rechargeproid;
             $validation = $auth->validation($parameter);
             if ($validation == false) {
                return array("status" => "100", "message" => "Unauthorised Transaction");
            }
            
             
            $deduct = 1;
            if (empty($ac_ballance) || $amount > $ac_ballance) {
                $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $ogaballance = $row[0]['ac_ballance'];
                $ogaautofeed = $row[0]['auto_feed_cahier_account'];
                if ($ogaautofeed == 1 && $rechargeprorole < 4) {

                    if ($amount > $ogaballance) {
                        return array("status" => "100", "message" => "Insufficient Fund");
                    }


                    $deduct = 0;

                    $newballance = $ogaballance - $amount;
                    self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $profile_creator));

                } else {
                    return array("status" => "100", "message" => "Insufficient Balance");
                }

            }


            $newballance = $ac_ballance - $amount;
            
             if ($deduct == 1) {
                if($channel != 1){
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }else{
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance, $rechargeproid));
                    }
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


        switch ($service) {
            case "2351":
                $productid = "MFIN-1-OR";
                break;

            case "2352":
                $productid = "MFIN-2-OR";
                break;

            case "2353":
                $productid = "MFIN-5-OR";
                break;

            case "2354":
                $productid = "MFIN-6-OR";
                break;

            default:
                return array("status" => "100", "message" => "Invalid Service");
        }

include "promo1.php";

        $requestBody = '{
                "product_id": "' . $productid . '",
                "denomination" : ' . $amount . ',
                "send_sms" : false,
                "sms_text" : "",
                "customer_reference":"'.$transaction_date.$tid.'"
            }';

        $tosendac = substr($accountnumber, 1);
        $response = self::mobifin_post("topup/exec/234" . $tosendac, $requestBody, true);
        //$response = json_decode($response, true);
        
        //file_put_contents("dd.php",json_encode($response));
        
        

        //return array("status" => "100", "message" =>$result);
        
  
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
        
                if (in_array($response['status'],array("400","402","405","401","408"))) {
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
        
        
                if ($response['status'] == "208"){
          return array("status" => "100", "message" =>"Pending Transaction");  
        }
        

        $response['Phone'] = $accountnumber;
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';

        if ($response['status'] == "200" || $response['status'] == "201" || $response['status'] == "429") {

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
                
                self::que_rechargepropay_mail($tid, $email, "success");

            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
        } else if($response['status'] == "500") {
            
            return self::verify_mobifin($parameter);
            
            }else if($response['status'] == "503"){


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
                
            }else{
                
                
                
                
                
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
            return array("status" => "100", "message" => "Pending Transaction");
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
        $result = json_encode($response);
        $result = '{"details":' . $result . '}';
        
        
                if ($response['status'] == "208"){
          return array("status" => "100", "message" =>"Pending Transaction");  
        }

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
                
                self::que_rechargepropay_mail($tid, $email, "success");

            return array("status" => "200", "message" => array("bal"=>$myac_ballance,"pft"=>$myprofit_bal,
                    "status" => "Accepted",
                    "TransactionID" => $tid,
                    "details" => $response));
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