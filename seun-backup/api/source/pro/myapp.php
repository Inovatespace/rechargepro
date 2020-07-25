<?php
class myapp extends Api
{

    public function bills_list($parameter)
    {
        $cat = $parameter['cat'];
        $row = self::db_query("SELECT services_key,service_name FROM rechargepro_services WHERE services_category = ? AND service_subcategory =?  AND status = '1' ORDER BY service_name ASC",
            array(7, $cat));
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = $row[$dbc]['service_name'];
        }
        return $return;
    }

    public function bills_cat_list($parameter)
    {
        $row = self::db_query("SELECT subcategory_id,name FROM rechargepro_subcategory ORDER BY name ASC",
            array());
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['subcategory_id']] = $row[$dbc]['name'];
        }
        return $return;
    }

    public function airtime_list($parameter)
    {
        include "airtime_data.php";
        $airtime_data = new airtime_data("POST");
        return $airtime_data->network_list($parameter);
    }


    public function data_list($parameter)
    {
        include "airtime_data.php";
        $airtime_data = new airtime_data("POST");
        return $airtime_data->data_network_list($parameter);
    }

    public function bundle_list($parameter)
    {
        include "airtime_data.php";
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

            $return[$items[$dbc]['code']] = $nae . "@" . $items[$dbc]['price'];
        }
        return $return;
    }


    public function tv_list($parameter)
    {
        include "tv.php";
        $tv = new tv("POST");
        return $tv->network_list($parameter);
    }


    public function saveprofile($parameter)
    {

        if (!isset($parameter['private_key'])) {
            return array("unauthorised Access");
        }


        $bankname = $parameter['bankname'];
        $acname = $parameter['acname'];
        $acnumber = $parameter['acnumber'];
        $mobile = $parameter['mobile'];
        $email = $parameter['email'];
        $name = $parameter['name'];
        $id = $parameter['id'];


        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ? AND rechargeproid = ? AND status = '0' LIMIT 1",
            array($parameter['private_key'], $id));
        $rechargeproid = $row[0]['rechargeproid'];


        $row = self::db_query("SELECT transfer_activation FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $transfer_activation = $row[0]['transfer_activation'];
        if ($transfer_activation == 1) {
            return array("Action Not Permited");
        }


        if (empty($rechargeproid)) {
            return array("It looks Like you are logged in on another device, please Logout and login");
        }

        self::db_query("UPDATE rechargepro_account SET name =?, bank_name=?, bank_ac_name= ?, bank_ac_number= ? WHERE rechargeproid = ? LIMIT 1",
            array(
            $name,
            $bankname,
            $acname,
            $acnumber,
            $rechargeproid));


        return array("Profile Saved");

    }


    public function savepassword($parameter)
    {

        if (!isset($parameter['private_key'])) {
            return array("unauthorised Access");
        }


        if (!isset($parameter['oldpassword'])) {
            return array("Old Password not found on record");
        }

        if (empty($parameter['oldpassword'])) {
            return array("Old Password not found on record");
        }

        $password = $parameter['oldpassword'];
        $newpassword = $parameter['newpassword'];

        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ? AND rechargeproid = ? AND status = '0' LIMIT 1",
            array($parameter['private_key'], $parameter['id']));
        $rechargeproid = $row[0]['rechargeproid'];

        if (empty($rechargeproid)) {
            return array("It looks Like you are logged in on another device, please Logout and login");
        }


        $rowa = self::db_query("SELECT rechargeproid,password FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($rechargeproid));
        $row = $rowa[0];


        // $hasher = new PasswordHash(8, false);
        if (!self::CheckPassword($password, $row['password'])) {
            return array("Old Password not found on record");
        }


        if (self::CheckPassword($newpassword, $row['password'])) {
            return array("Password cannot be similar to old password");
        }


        //$password = sha1(md5($newpassword) . self::config("user_key"));
        $password = self::shuzia_HashPassword($newpassword, self::RandomString(4, 20));
        self::db_query("UPDATE rechargepro_account SET password =? WHERE rechargeproid = ? LIMIT 1",
            array($password, $row['rechargeproid']));

        return array("Password Saved");
    }


    public function banquet_list($parameter)
    {
        include "tv.php";
        $tv = new tv("POST");
        $bq = $tv->available_bounquet(array("service" => $parameter['service']));
        $bg = $bq['message'];


        if (!isset($bg['items'])) {
            return array();
        }
        
            $onehundred = 0;    
            /// FOR NEXT
            if(isset($parameter["private_key"])){
        $key = urldecode(trim($parameter["private_key"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key)); //AND status = '0'
        $rechargeproid = $row[0]['rechargeproid'];
                $row = self::db_query("SELECT profile_agent FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $profile_agent = $row[0]['profile_agent'];
        if(in_array($profile_agent,array(115)) || in_array($rechargeproid,array(115))){
          $onehundred = 100;  
        }
            }
            /// FOR NEXT END

        $return = array();
        for ($i = 0; $i < count($bg['items']); $i++) {
            $return[$bg['items'][$i]['code']] = ($bg['items'][$i]['price'] +$onehundred). " " . $bg['items'][$i]['name'];
        }

        return $return;
    }


    public function lottery_list($parameter)
    {
        include "lottery.php";
        $lottery = new lottery("POST");
        return $lottery->lottery_list($parameter);
    }


    public function utility_list($parameter)
    {
        include "electricity.php";
        $electricity = new electricity("POST");
        return $electricity->utility_list($parameter);
    }

    public function __construct($method)
    {
        //Api::Api_Method("user_key", "POST", $method); // this tell the API to only allow POST method
    }

    public function auth($parameter)
    {

        if (!isset($parameter['code']) || !isset($parameter['username'])) {
            return "bad";
        }

        $auth = $parameter['code'];
        $email = $parameter['username'];
        $serial = $parameter['serial'];
        $type = $parameter['type'];
        $devicename = $parameter['devicename'];

        $row = self::db_query("SELECT email FROM rechargepro_account WHERE email = ? || mobile = ? LIMIT 1",
            array($email, $email));
        $email = $row[0]['email'];


        $row = self::db_query("SELECT id FROM temp_code WHERE email = ? AND code = ? AND status = '0' LIMIT 1",
            array($email, $auth));
        if (empty($row[0]['id'])) {
            return "bad";
        }


        $devicecount = self::db_query("SELECT id FROM rechargepro_access WHERE email = ?",
            array($email), true);
        if ($devicecount > 2) {
            return "goo";
        }


        $threemonth = date("Y-m-d H:i:s", strtotime('-90 days', strtotime(date("Y-m-d H:i:s"))));
        self::db_query("DELETE FROM temp_code WHERE date <= ? LIMIT 1", array($threemonth));

        self::db_query("UPDATE temp_code SET STATUS = '1' WHERE id = ? LIMIT 1", array($row[0]['id']));

        self::db_query("INSERT INTO rechargepro_access (email,mac,name,device_type) VALUES (?,?,?,?)",
            array(
            $email,
            $serial,
            $devicename,
            $type));

        return "ok";
    }


    public function get_auth($parameter)
    {

        if (!isset($parameter['serial']) || !isset($parameter['email'])) {
            return "An error Occured contact support";
        }

        $email = $parameter['email'];
        $serial = $parameter['serial'];
        $devicename = $parameter['devicename'];


        $code = self::RandomString(4, 5);

        self::db_query("DELETE FROM temp_code WHERE email = ? AND status = '0'", array($email));


        self::db_query("INSERT INTO temp_code (email,code,serial,device_name) VALUES (?,?,?,?)",
            array(
            $email,
            $code,
            $serial,
            $devicename));


        return "AUTHENTICATION CODE: " . $code;

    }


    public function login($parameter)
    {


        if (!isset($parameter['username']) || !isset($parameter['password'])) {
            return "bad";
        }


        $username = $parameter['username'];
        $password = $parameter['password'];
        $serial = $parameter['serial'];
        $type = $parameter['type'];
        $devicename = $parameter['devicename'];
        $yes = 0;

        if ($serial == "web") {
            return "auth";
        }

        if ($type == "0") {
            return "auth";
        }

        //$money = $serial . "_" . $type . "_" . $devicename . "_" . $username . "_" . $password;
        //self::db_query("INSERT INTO rechargepro_account (companyaddress) VALUES (?)", array($money));
        //$hasher = new PasswordHash(8, false);
        $latestpassword = sha1(md5($password) . self::config("user_key"));


        $rowa = self::db_query("SELECT rechargeproid,name,password,rechargeprorole,public_secret,email,ac_ballance,sms_activation,transfer_activation,bypass,profit_bal FROM rechargepro_account WHERE (mobile = ? OR email = ?) AND active = '1' LIMIT 1",
            array($username, $username));
        $row = $rowa[0];
        $bypass = $row['bypass'];
        $profit_bal = $row['profit_bal'];
        $email = $row['email'];


        if ($latestpassword == $row['password']) {
            $newpassword = self::shuzia_HashPassword($latestpassword, self::RandomString(4,
                20));
            $row['password'] = $newpassword;
        }


        if (!self::CheckPassword($password, $row['password'])) {

            //check if mbc_access
            $accesspacount = 0;
            $rowm = self::db_query("SELECT password FROM rechargepro_access WHERE email = ? AND mac = ?",
                array($username, $serial));
            for ($dbc = 0; $dbc < self::array_count($rowm); $dbc++) {
                $accesspassword = $rowm[$dbc]['password'];
                if (self::CheckPassword($password, $accesspassword)) {
                    $accesspacount++;
                }
            }

            if ($accesspacount == 0) {
                return "bad";
            }
        }


        if (empty($row['rechargeproid'])) {
            return "bad";
        } else {


            //check if device has free access
            $deviceisfree = 0;
            if ($bypass > 0) {
                if ($type == $bypass) {
                    $deviceisfree = 1;
                }
            }


            if ($deviceisfree == 4233333) { //0
                //check if it is weballowed or first
                $regdevice = 0;
                $isallowed = 0;
                $rowb = self::db_query("SELECT device_type,id,email,mac,name FROM rechargepro_access WHERE email = ?",
                    array($email));
                for ($dbc = 0; $dbc < self::array_count($rowb); $dbc++) {
                    $device_type = $rowb[$dbc]['device_type'];
                    $id = $rowb[$dbc]['id'];
                    //$email = $rowb[$dbc]['email'];
                    $mac = $rowb[$dbc]['mac'];
                    $name = $rowb[$dbc]['name'];
                    if ($device_type == $type) {

                        if ($mac == $serial) {
                            $isallowed = 1;
                        }

                    }
                    $regdevice++;
                }

                if ($regdevice > 0) {

                    if ($isallowed == 0) {
                        return "auth";
                    }

                } else {
                    self::db_query("INSERT INTO rechargepro_access (email,mac,name,device_type) VALUES (?,?,?,?)",
                        array(
                        $email,
                        $serial,
                        $devicename,
                        $type));
                }


            } else {

                $rowb = self::db_query("SELECT id FROM rechargepro_access WHERE email = ? AND mac = ?",
                    array($email, $serial));
                if (empty($rowb[0]['id'])) {
                    self::db_query("INSERT INTO rechargepro_access (email,mac,name,device_type) VALUES (?,?,?,?)",
                        array(
                        $email,
                        $serial,
                        $devicename,
                        $type));
                }

            }


            self::db_query("UPDATE appauth SET status = '1' WHERE rechargeproid = ? AND accesstype = ?",
                array($row['rechargeproid'], $type));
            $code = self::RandomString(4, 30);

            $ip = self::getRealIpAddr();
            self::db_query("INSERT INTO appauth (rechargeproid,email,name,authkey,accesstype,ip) VALUES (?,?,?,?,?,?)",
                array(
                $row['rechargeproid'],
                $row['email'],
                $row['name'],
                $code,
                $type,
                $ip));

            return $row['rechargeproid'] . "@" . $row['name'] . "@" . $row['rechargeprorole'] .
                "@" . $code . "@" . $row['ac_ballance'] . "@" . $row['transfer_activation'] .
                "@" . $row['sms_activation'] . "@" . $profit_bal . "@" . $email;
        }

    }

    public function register($parameter)
    {

        if (!isset($parameter["name"]) || !isset($parameter["email"]) || !isset($parameter["mobile"]) ||
            !isset($parameter["password"])) {
            return "All fields are compulsory";
        }


        $name = $parameter["name"];
        $email = $parameter["email"];
        $mobile = $parameter["mobile"];
        $serial = $parameter['serial'];
        $type = $parameter['type'];
        $referer = $parameter['referer'];
        $devicename = $parameter['devicename'];

        $username = preg_replace('/\s+/', '', $email);
        //$hasher = new PasswordHash(8, false);
        // $password = sha1(md5($parameter["password"]) . self::config("user_key"));
        $password = self::shuzia_HashPassword($parameter["password"], self::
            RandomString(4, 20));


        if (!isset($name) || !isset($email) || !isset($username) || !isset($password) ||
            !isset($mobile)) {
            return "All fields are compulsory";
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid Email";
        }


        $public_key = self::RandomString(4, 40);
        $public_secret = self::RandomString(4, 30);
        $active = 1;
        $email = $email;
        $mobile = $mobile;
        $profile_agent = 1;


        $rechargeprorole = 4;
        $profile_rol = $rechargeprorole + 1;


        if (strlen($mobile) < 11) {
            return "Invalid Mobile";
        }

        if (strlen($mobile) > 11) {
            return "Invalid Mobile";
        }

        if (empty($mobile)) {
            return "Invalid Mobile";
        }


        $row = self::db_query("SELECT email,mobile FROM rechargepro_account WHERE email = ? || mobile = ? LIMIT 1",
            array($email, $mobile));
        if (!empty($row[0]['email'])) {
            return "Email Exist";
        }

        if (!empty($row[0]['mobile'])) {
            return "Mobile Exist";
        }


        $creator = "0";
        if (!empty($referer)) {
            $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE email = ? || mobile = ? LIMIT 1",
                array($referer, $referer));

            if (!empty($row[0]['rechargeproid'])) {
                $creator = $row[0]['rechargeproid'];
            }
        }


        $rechargeproid = self::db_query("INSERT INTO rechargepro_account (name,username,password,public_key,public_secret,active,email,mobile,profile_creator,rechargeprorole,profile_agent) VALUES (?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $name,
            $username,
            $password,
            $public_key,
            $public_secret,
            $active,
            $email,
            $mobile,
            $creator,
            4,
            0));

        $code = self::RandomString(4, 30);


        $ip = self::getRealIpAddr();
        self::db_query("INSERT INTO appauth (rechargeproid,email,name,authkey,accesstype,ip) VALUES (?,?,?,?,?,?)",
            array(
            $rechargeproid,
            $email,
            $name,
            $code,
            $type,
            $ip));


        self::db_query("INSERT INTO rechargepro_access (email,mac,name,device_type) VALUES (?,?,?,?)",
            array(
            $email,
            $serial,
            $devicename,
            $type));

        return $rechargeproid . "@" . $name . "@4@" . $code . "@0@0@0@0@" . $email;
    }


    public function myapp_request($parameter)
    {
        if (!isset($parameter["service_request"])) {
            return array("status" => "100", "message" => "Invalid Request2");
        }

        if (!isset($parameter["private_key"])) {
            return array("status" => "100", "message" => "Invalid Private Key");
        }


        //passsssssssssssssssssssssssssss


        $key = urldecode(trim($parameter["private_key"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key)); //AND status = '0'
        $rechargeproid = $row[0]['rechargeproid'];


        $row = self::db_query("SELECT public_secret,email,name FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $private_key = $row[0]['public_secret'];


        if ($parameter["service_request"] == "get_auth") {
            $parameter["email"] = $row[0]['email'];
            $parameter["private_key"] = $private_key;
            return self::get_auth($parameter);
        }


        if ($parameter["service_request"] == "account") {
            $parameter["private_key"] = $private_key;
            return self::ac($parameter);
        }

        if ($parameter["service_request"] == "transaction_log") {
            $parameter["private_key"] = $private_key;
            return self::transaction_log($parameter);
        }


        if ($parameter["service_request"] == "history") {
            $parameter["private_key"] = $private_key;
            return self::history($parameter);
        }


        if ($parameter["service_request"] == "agent") {
            $parameter["profilecreator"] = $rechargeproid;
            $parameter["private_key"] = $private_key;
            return self::agent_list($parameter);
        }


        if ($parameter["service_request"] == "daily_sales") {
            $parameter["profilecreator"] = $rechargeproid;
            $parameter["private_key"] = $private_key;
            return self::daily_sales($parameter);
        }


        if ($parameter["service_request"] == "reply_support") {
            $parameter["email"] = $row[0]['email'];
            $parameter["name"] = $row[0]['name'];
            $parameter["private_key"] = $private_key;
            return self::reply_support($parameter);
        }


        if ($parameter["service_request"] == "support") {
            $parameter["email"] = $row[0]['email'];
            $parameter["private_key"] = $private_key;
            return self::support($parameter);
        }


        if ($parameter["service_request"] == "support_details") {
            $parameter["email"] = $row[0]['email'];
            $parameter["private_key"] = $private_key;
            return self::support_details($parameter);
        }


        if ($parameter["service_request"] == "wallet") {
            $parameter["private_key"] = $private_key;
            return self::ac_ballance($parameter);
        }

        if ($parameter["service_request"] == "my_ballance") {
            $parameter["private_key"] = $private_key;
            return self::my_ballance($parameter);
        }
        
        



        if ($parameter["service_request"] == "repeat") {
            
            if (!isset($parameter["tid"])) {
                return array("status" => "100", "message" =>"Invalid Transaction");
            }
            
$tid = self::db_query("INSERT INTO rechargepro_transaction_log (cordinator_id,agent_id,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,service_charge,phone,email,payment_method) SELECT cordinator_id,agent_id,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,service_charge,phone,email,payment_method FROM rechargepro_transaction_log WHERE transactionid = ?",array($parameter["tid"]));


$row = self::db_query("SELECT account_meter,name,address,amount,rechargepro_subservice,rechargepro_service FROM rechargepro_transaction_log WHERE transactionid = ?",array($parameter["tid"]));
$name = $row[0]['name'];
$account_meter = $row[0]['account_meter'];
$amount = $row[0]['amount'];
$rechargepro_subservice = $row[0]['rechargepro_subservice'];
$rechargepro_service = $row[0]['rechargepro_service'];


$row = self::db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ?",array($rechargepro_subservice));
$services_category = $row[0]['services_category'];

if(empty($name)){$name = $account_meter;}

        $return['id'] = $tid;
        $return['ac'] = $name;
        $return['details'] = $account_meter;
        $return['amount'] = $amount;
        $return['service'] = $rechargepro_service;
        $return['cat'] = $services_category;
       
        
        $return['totalamount'] = $amount;
        $return['tfee'] = 0;


        return array("status" => "200", "message" => $return);

        }


        if ($parameter["service_request"] == "authpower") {
            $parameter["private_key"] = $private_key;
            include "electricity.php";
            $electricity = new electricity("POST");
            return $electricity->auth_transaction($parameter);
        }

        if ($parameter["service_request"] == "buypower") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active on another device with the same os,Logout and Login");
            }
            include "electricity.php";
            $electricity = new electricity("POST");
            return $electricity->complete_transaction($parameter);
        }

        if ($parameter["service_request"] == "authairtime") {
            $parameter["private_key"] = $private_key;
            include "airtime_data.php";
            $airtime_data = new airtime_data("POST");
            return $airtime_data->auth_airtime($parameter);
        }

        if ($parameter["service_request"] == "buyairtime") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "airtime_data.php";
            $airtime_data = new airtime_data("POST");
            return $airtime_data->complete_transaction($parameter);
        }


        if ($parameter["service_request"] == "authdata") {
            $parameter["private_key"] = $private_key;
            include "airtime_data.php";
            $airtime_data = new airtime_data("POST");
            return $airtime_data->auth_data($parameter);
        }


        if ($parameter["service_request"] == "authtv") {
            $parameter["private_key"] = $private_key;
            include "tv.php";
            $tv = new tv("POST");
            return $tv->auth_transaction($parameter);
        }

        if ($parameter["service_request"] == "buytv") {
            $parameter["private_key"] = $private_key;
            include "tv.php";
            $tv = new tv("POST");
            return $tv->complete_transaction($parameter);
        }

        if ($parameter["service_request"] == "authlottery") {
            $parameter["private_key"] = $private_key;
            $parameter["service"] = "2563";
            include "lottery.php";
            $lottery = new lottery("POST");
            return $lottery->auth_lottery($parameter);
        }

        if ($parameter["service_request"] == "buylottery") {
            $parameter["private_key"] = $private_key;
            include "lottery.php";
            $lottery = new lottery("POST");
            return $lottery->buy_lottery($parameter);
        }

        if ($parameter["service_request"] == "authbills") {
            $parameter["private_key"] = $private_key;
            include "bills.php";
            $bills = new bills("POST");
            return $bills->auth_transaction($parameter);
        }

        if ($parameter["service_request"] == "buybills") {
            $parameter["private_key"] = $private_key;
            include "bills.php";
            $bills = new bills("POST");
            return $bills->complete_transaction($parameter);
        }

        if ($parameter["service_request"] == "billswithcode") {
            $parameter["private_key"] = $private_key;
            include "bills.php";
            $bills = new bills("POST");
            return $bills->with_code($parameter);
        }

        if ($parameter["service_request"] == "cardpayment") {
            $parameter["private_key"] = $private_key;
            return self::cardpayment($parameter);
        }

        if ($parameter["service_request"] == "try") {
            $parameter["private_key"] = $private_key;
            $parameter["tid"] = $parameter["id"];
            return self::try_again($parameter);
        }

        if ($parameter["service_request"] == "print") {
            $parameter["private_key"] = $private_key;
            return self::print_ticket($parameter);
        }


        if ($parameter["service_request"] == "authwallettransfer") {
            $parameter["private_key"] = $private_key;
            include "transfer.php";
            $transfer = new transfer("POST");
            return $transfer->auth_transfer($parameter);
        }

        if ($parameter["service_request"] == "wallettransfer") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "transfer.php";
            $transfer = new transfer("POST");
            return $transfer->complete_transaction($parameter);
        }


        if ($parameter["service_request"] == "agentlocation") {
            $parameter["private_key"] = $private_key;
            return self::agent_location($parameter);
        }


        if ($parameter["service_request"] == "topup") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            return self::topup($parameter);
        }


        if ($parameter["service_request"] == "group") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "contact_group.php";
            $contact_group = new contact_group("POST");
            return $contact_group->group_list($parameter);
        }


        if ($parameter["service_request"] == "contact") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "contact_group.php";
            $contact_group = new contact_group("POST");
            return $contact_group->contact_list($parameter);
        }


        if ($parameter["service_request"] == "creatgroup") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "contact_group.php";
            $contact_group = new contact_group("POST");
            return $contact_group->new_group($parameter);
        }


        if ($parameter["service_request"] == "newcontact") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "contact_group.php";
            $contact_group = new contact_group("POST");
            return $contact_group->new_newmobilecontact($parameter);
        }


        if ($parameter["service_request"] == "smscode") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }

            include "sms.php";
            $sms = new sms("POST");
            return $sms->sms_code($parameter);
        }


        if ($parameter["service_request"] == "smsactivate") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }

            include "sms.php";
            $sms = new sms("POST");
            return $sms->sms_activate($parameter);
        }


        if ($parameter["service_request"] == "sendsms") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "sms.php";
            $sms = new sms("POST");
            return $sms->process_sms($parameter);
        }


        if ($parameter["service_request"] == "buybulkairtime") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bulkairtime.php";
            $bulkairtime = new bulkairtime("POST");
            return $bulkairtime->buy_airtime($parameter);
        }


        if ($parameter["service_request"] == "bulkpayaccount") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bulkpay.php";
            $bulkpay = new bulkpay("POST");
            return $bulkpay->account_list($parameter);
        }


        if ($parameter["service_request"] == "permission") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bulkpay.php";
            $bulkpay = new bulkpay("POST");
            return $bulkpay->permission_list($parameter);
        }

        if ($parameter["service_request"] == "bulkpay_transaction") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bulkpay.php";
            $bulkpay = new bulkpay("POST");
            return $bulkpay->transfer_list($parameter);
        }


        if ($parameter["service_request"] == "bulkpay_approve") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bulkpay.php";
            $bulkpay = new bulkpay("POST");
            return $bulkpay->approve_pay($parameter);
        }


        if ($parameter["service_request"] == "bulkpay_cancel") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bulkpay.php";
            $bulkpay = new bulkpay("POST");
            return $bulkpay->cancel_pay($parameter);
        }


        if ($parameter["service_request"] == "authwithdrawal") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bank_withdrawal.php";
            $bank_withdrawal = new bank_withdrawal("POST");
            return $bank_withdrawal->auth_withdrawal($parameter);
        }

        if ($parameter["service_request"] == "confirmwithdrawal") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bank_withdrawal.php";
            $bank_withdrawal = new bank_withdrawal("POST");
            return $bank_withdrawal->confirm_withdrawal($parameter);
        }


        if ($parameter["service_request"] == "validateotp") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bank_withdrawal.php";
            $bank_withdrawal = new bank_withdrawal("POST");
            return $bank_withdrawal->validate_otp($parameter);
        }


        if ($parameter["service_request"] == "authtransfer") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bank_transfer.php";
            $bank_transfer = new bank_transfer("POST");
            return $bank_transfer->auth_transfer($parameter);
        }


        if ($parameter["service_request"] == "banktransfer") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "bank_transfer.php";
            $bank_transfer = new bank_transfer("POST");
            return $bank_transfer->complete_transaction($parameter);
        }


        if ($parameter["service_request"] == "notification") {
            $parameter["private_key"] = $private_key;
            if (empty($private_key)) {
                return array("status" => "100", "message" =>
                        "Your account is active in another device with the same os,Logout and Login");
            }
            include "notification.php";
            $notification = new notification("POST");
            return $notification->system_message($parameter);
        }

        return array("status" => "100", "message" => "Invalid Request3");
    }


    public function topup($parameter)
    {


        if (!isset($parameter["ref"])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }

        if (empty($parameter["ref"])) {
            return array("status" => "100", "message" => "Invalid Transaction");
        }


        $rechargeproref = $parameter['ref'];
        $private_key = $parameter['private_key'];


        $payload = array(
            'flwref' => $rechargeproref,
            'SECKEY' => self::config("rave_secrete_key"),
            //'SECKEY' => "FLWSECK-d53a97bdda8d802c5a95ca3ec4b3f123-X",
            'normalize' => '1');

        $data_string = json_encode($payload);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
        //curl_setopt($ch, CURLOPT_URL, 'https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)));

        $result = curl_exec($ch);
        $response = json_decode($result, true);


        if (!isset($response["status"])) {
            return array("status" => "100", "message" => 'Network Error');
        }

        if (!isset($response["data"]["chargecode"])) {
            return array("status" => "100", "message" => 'Service Error1');
        }

        if ($response["data"]["chargecode"] == "00") {


            if (isset($response["data"]["tx"])) {
                $response["data"]["charged_amount"] = $response["data"]["tx"]["charged_amount"];
                $response["data"]["flwRef"] = $response["data"]["tx"]["flwRef"];
            }


            if (isset($response["data"]["flwref"])) {
                $response["data"]["flwRef"] = $response["data"]["flwref"];
            }


            //["embedtoken"]

            if (isset($response["data"]["flwRef"])) {

                if (isset($response["data"]["chargedamount"])) {
                    $response["data"]["charged_amount"] = $response["data"]["chargedamount"];
                }

                if (isset($response["data"]["card"]["card_tokens"]["embedtoken"])) {
                    $response["data"]["card"]["card_tokens"]["0"]["embedtoken"] = $response["data"]["card"]["card_tokens"]["embedtoken"];
                }


                $thepercentage = ceil(($response["data"]["charged_amount"] * 1.5)) / 100;
                if ($response["data"]["appfee"] > $thepercentage) {
                    $thepercentage = ceil(($response["data"]["charged_amount"] * 3.5)) / 100;

                    return array("status" => "100", "message" => 'Only Local Card Allowed');
                }


                $amount_to_charge = $response["data"]["charged_amount"] - $thepercentage;


                $row = self::db_query("SELECT rechargeproid,ac_ballance,profit_bal,name FROM rechargepro_account WHERE public_secret = ? LIMIT 1",
                    array($private_key));
                $rechargeproid = $row[0]['rechargeproid'];
                $ac_ballance = $row[0]['ac_ballance'];
                $profit_bal = $row[0]['profit_bal'];
                $namyname = $myrow[0]['name'];

                if (empty($rechargeproid)) {
                    return array("status" => "100", "message" =>
                            "Your payment is successful, but an error occured please contact support");
                }


                //check if ref has been used from transactionlog
                $row = self::db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE bank_ref = ? LIMIT 1",
                    array($rechargeproref));
                $transactionid = $row[0]['transactionid'];


                if (!empty($transactionid)) {
                    return array("status" => "200", "message" => array("bal" => $ac_ballance, "pft" =>
                                $profit_bal));
                }


                $ip = self::getRealIpAddr();
                self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,rechargepro_service,rechargepro_subservice,amount,payment_method,rechargepro_status,rechargepro_status_code,bank_ref,ip) VALUES (?,?,?,?,?,?,?,?,?)",
                    array(
                    $rechargeproid,
                    "topup",
                    "topup",
                    $amount_to_charge,
                    1,
                    "PAID",
                    1,
                    $rechargeproref,
                    $ip));

                $newbalance = $ac_ballance + $amount_to_charge;
                $row = self::db_query("UPDATE rechargepro_account SET ac_ballance =?  WHERE rechargeproid = ? LIMIT 1",
                    array($newbalance, $rechargeproid));

                return array("status" => "200", "message" => array("bal" => $newbalance, "pft" =>
                            $profit_bal));

            } else {
                return array("status" => "100", "message" =>
                        "1 Unknown Error Occured, Please Contact support with ref $rechargeproref");
            }

        } else {
            return array("status" => "100", "message" =>
                    "1 Unknown Error Occured, Please Contact support with ref $rechargeproref");
        }


    }

    public function agent_location($parameter)
    {

        $idarray = array();

        $per_page = 13;
        $search = "";

        $page = 1;
        if (isset($parameter['page'])) {
            $page = htmlentities($parameter['page']);
        }

        $start = ($page - 1) * $per_page;

        if (isset($parameter["search"])) {
            $search = $parameter["search"];
        }

        if (!isset($parameter["state"])) {
            return array("status" => "100", "message" => "Invalid State Selection");
        }

        $state = $parameter["state"];

        $totalpost = self::db_query("(SELECT rechargeproid  FROM rechargepro_account WHERE companyaddress LIKE ? AND companystate = ? AND active = '1' )",
            array("%$search%", $state), true);

        $content = array();
        $row = self::db_query("(SELECT name, companyaddress, companystate, companylga, mobile FROM rechargepro_account WHERE	companyaddress LIKE ? AND companystate = ? AND active = '1' LIMIT $start, $per_page)",
            array("%$search%", $state));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {

            $name = $row[$dbc]['name'];
            $companyaddress = $row[$dbc]['companyaddress'];
            $companystate = $row[$dbc]['companystate'];
            $companylga = $row[$dbc]['companylga'];
            $mobile = $row[$dbc]['mobile'];

            $content[] = $name . "#" . $mobile . "#" . $companystate . "#" . $companylga .
                "#" . $companyaddress;
        }

        $content = implode("@", $content);

        return array($totalpost . "#@" . ceil($totalpost / $per_page) . "#@" . $content);
    }


    public function cardpayment($parameter)
    {


        if (!isset($parameter["private_key"])) {
            return array("status" => "100", "message" => "Invalid Private Key");
        }

        $key = urldecode(trim($parameter["private_key"]));
        $row = self::db_query("SELECT rechargeproid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key)); //AND status = '0'
        $rechargeproid = $row[0]['rechargeproid'];


        $row = self::db_query("SELECT public_secret,email,name FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($rechargeproid));
        $private_key = $row[0]['public_secret'];

        if (empty($private_key)) {
            return array("status" => "100", "message" => "Invalid Key");
        }

        $parameter["private_key"] = $private_key;

        if (!isset($parameter["ref"])) {
            return array("status" => "100", "message" => "Invalid Transaction1");
        }

        if (empty($parameter["ref"])) {
            return array("status" => "100", "message" => "Invalid Transaction2");
        }

        if (!isset($parameter["tid"])) {
            return array("status" => "100", "message" => "Invalid Transaction3");
        }

        if (!isset($parameter["serial"])) {
            $parameter["serial"] = "web";
        }


        $rechargeproref = $parameter['ref'];
        $tid = htmlentities($parameter['tid']);
        $private_key = $parameter['private_key'];


        $payload = array(
            'flwref' => $rechargeproref,
            'SECKEY' => self::config("rave_secrete_key"), //secret key from pay button generated on rave dashboard
            'normalize' => '1');

        $data_string = json_encode($payload);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,
            'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify');
        // curl_setopt($ch, CURLOPT_URL, 'https://ravesandboxapi.flutterwave.com/flwv3-pug/getpaidx/api/v2/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                'Content-Length: ' . strlen($data_string)));

        $result = curl_exec($ch);
        $response = json_decode($result, true);

        // self::db_query("INSERT INTO rechargepro_transaction_log (rechargepro_print) VALUES (?)",array($result));

        //$response = self::file_get_b($payload,
        // "https://api.ravepay.co/flwv3-pug/getpaidx/api/verify");
        //FLW-MOCK-644652585dddde3e31c1035df6eab9c3

        //print_r($response); exit;

        //check the status is success


        if (!isset($response["status"])) {
            return array("status" => "100", "message" => 'Network Error');
        }

        if (!isset($response["data"]["chargecode"])) {
            return array("status" => "100", "message" => 'Service Error1');
        }

        if ($response["data"]["chargecode"] == "00") {


            $amountcharged = $response["data"]["chargedamount"];
            $rechargeproresponse = "Completed";


            $row = self::db_query("SELECT rechargeproid, amount, rechargepro_subservice, rechargepro_status_code FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
                array($tid));
            $amount_to_charge = $row[0]['amount'];
            $rechargepro_subservice = $row[0]['rechargepro_subservice'];
            $rechargepro_status_code = $row[0]['rechargepro_status_code'];
            $brechargeproid = $row[0]['rechargeproid'];

            $row = self::db_query("SELECT services_category,cordinator_percentage,percentage,bill_formular,bill_rechargeprofull_percentage FROM rechargepro_services WHERE services_key = ? LIMIT 1",
                array($rechargepro_subservice));
            $services_category = $row[0]['services_category'];
            $cordinator_percentage = $row[0]['cordinator_percentage'];
            $percentage = $row[0]['percentage'];
            $bill_formular = $row[0]['bill_formular'];
            $bill_rechargeprofull_percentage = $row[0]['bill_rechargeprofull_percentage'];


            $totalpercentage = $percentage + $bill_rechargeprofull_percentage + $cordinator_percentage;
            if ($bill_formular == 0) {
                $tfee = ($amount_to_charge * $totalpercentage) / 100;
            } else {
                $tfee = $totalpercentage;
            }

            $amountremaining = $amountcharged - $amount_to_charge;


            $bp = 0;
            if (($amount_to_charge - $tfee) < $amountremaining) {
                $amount_to_charge = $amountremaining;
                $bp = 0;
            }


            $thepercentage = ceil(($amountcharged * 1.5) / 100);
            if ($response["data"]["appfee"] > $thepercentage) {
                $thepercentage = ceil(($amountcharged * 3.5) / 100);


                //LOCAL CARD
                self::db_query("UPDATE rechargepro_transaction_log SET amount =?, rechargepro_status = ?, payment_method = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $amount_to_charge,
                    "PAID",
                    1,
                    $tid));
                return array("status" => "100", "message" => 'Only Local Card Allowed');
            }


            $row = self::db_query("SELECT rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($brechargeproid));
            $rechargeprorole = $row[0]['rechargeprorole'];


            if ($rechargepro_status_code == 1) {
                switch ($services_category) {
                    case 1:
                        include "electricity.php";
                        $electricity = new electricity("POST");
                        return $electricity->complete_transaction($parameter);
                        break;

                    case 2:
                    case 3:
                    case 4:
                        include "airtime_data.php";
                        $airtime_data = new airtime_data("POST");
                        return $airtime_data->complete_transaction($parameter);
                        break;

                    case 5:
                        include "tv.php";
                        $tv = new tv("POST");
                        return $tv->complete_transaction($parameter);
                        break;

                    case 7:
                        include "bills.php";
                        $bills = new bills("POST");
                        return $bills->complete_transaction($parameter);
                        break;

                    default:
                        return array("status" => "100", "message" => "bad@@Invalid Selection8");
                }

            }


            if (empty($rechargepro_subservice)) {
                return array("status" => "100", "message" =>
                        "Invalid Service, Contact support with Transaction ID $tid");
            }


            $row = self::db_query("SELECT rechargeproid, profile_creator FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
                array($private_key));
            $rechargeproid = $row[0]['rechargeproid'];
            $profile_creator = $row[0]['profile_creator'];
            if (empty($rechargeproid)) {
                return array("status" => "100", "message" => "Invalid Key");
            }


            self::db_query("UPDATE rechargepro_transaction_log SET amount =?, rechargepro_status = ?,agent_id=?,rechargeproid=?,bank_ref=?,bank_response=?, payment_method = ? WHERE transactionid = ? LIMIT 1",
                array(
                $amount_to_charge,
                "PAID",
                $profile_creator,
                $rechargeproid,
                $rechargeproref,
                $rechargeproresponse,
                1,
                $tid));


            self::db_query("UPDATE rechargepro_transaction_log SET refererprofit =?, agentprofit =?, cordprofit =?, rechargeproprofit = ? WHERE transactionid = ? LIMIT 1",
                array(
                0,
                0,
                0,
                $bp,
                $tid));
            //////////////////////////////////////////////////////////


            switch ($services_category) {
                case 1:
                    include "electricity.php";
                    $electricity = new electricity("POST");
                    return $electricity->complete_transaction($parameter);
                    break;

                case 2:
                case 3:
                case 4:
                    include "airtime_data.php";
                    $airtime_data = new airtime_data("POST");
                    return $airtime_data->complete_transaction($parameter);
                    break;

                case 5:
                    include "tv.php";
                    $tv = new tv("POST");
                    return $tv->complete_transaction($parameter);
                    break;

                case 7:
                    include "bills.php";
                    $bills = new bills("POST");
                    return $bills->complete_transaction($parameter);
                    break;

                default:
                    return array("status" => "100", "message" => "Invalid Selection8");
            }


        } else {
            return array("status" => "100", "message" =>
                    "Unknown Error Occured, Please Contact support with TID $tid");
        }

    }


    public function my_ballance($parameter)
    {
        if (!isset($parameter['private_key'])) {
            return "0";
        }

        $row = self::db_query("SELECT ac_ballance,profit_bal,name FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
            array($parameter['private_key']));
        return $row[0]['ac_ballance'] . "@" . $row[0]['profit_bal'];
    }


    public function ac_ballance($parameter)
    {
        if (!isset($parameter['private_key'])) {
            return "0";
        }

        $row = self::db_query("SELECT ac_ballance,profit_bal, rechargeproid,profile_creator FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
            array($parameter['private_key']));
        $bal = "MAIN::N" . $row[0]['ac_ballance'] . " - PROFIT::N" . $row[0]['profit_bal'];
        
        if(in_array($row[0]['profile_creator'],array("115"))){
            $rowb = self::db_query("SELECT ac_ballance,profit_bal,profile_creator FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($row[0]['profile_creator']));
            if($rowb[0]['ac_ballance'] > 50000){$rowb[0]['ac_ballance'] = 50000;}
         $bal = "MAIN::N" . $rowb[0]['ac_ballance'] . " - PROFIT::N-";   
        }

        $now = date("Y-m-d");
        $later = date("Y-m-d 23:23:23");


        $badservice = array(
            "BANK WITHDRAWAL",
            "Credit",
            "loan Credit",
            "PROFIT",
            "REWARD",
            "TOPUP",
            "Debit",
            "loan Debit",
            "WITHDRAW",
            "TRANSFER",
            "REFUND");
        $implode = "'" . implode("','", $badservice) . "'";

        $row = self::db_query("SELECT SUM(amount) AS amt, SUM(service_charge) sc FROM rechargepro_transaction_log WHERE rechargeproid = ? AND transaction_date BETWEEN ? AND ? AND 	rechargepro_status = 'PAID' AND rechargepro_subservice NOT IN ($implode)",
            array(
            $row[0]['rechargeproid'],
            $now,
            $later));
        $sales = ($row[0]['amt'] + $row[0]['sc']);

        return $bal . " {TODAY SALES : $sales}";
    }


    public function transaction_log($parameter)
    {
        // AND rechargepro_service != 'PROFIT'

        $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
            array($parameter['private_key']));
        $rechargeproid = $row[0]['rechargeproid'];


        $search = "";
        if (isset($parameter['search'])) {
            $search = htmlentities($parameter['search']);
        }

        if (!empty($search)) {

            $row = self::db_query("SELECT rechargepro_service AS REF, amount AS AMOUNT, phone AS MOBILE, rechargepro_print AS DETAILS, transaction_date AS DATE, rechargepro_status_code AS sync, transactionid AS id FROM rechargepro_transaction_log WHERE rechargeproid  = ? AND rechargepro_status = 'PAID' AND account_meter LIKE ?  ORDER BY transactionid DESC LIMIT 100",
                array($rechargeproid, "%$search%"));

        } else {

            $row = self::db_query("SELECT rechargepro_service AS REF, amount AS AMOUNT, phone AS MOBILE, rechargepro_print AS DETAILS, transaction_date AS DATE, rechargepro_status_code AS sync, transactionid AS id FROM rechargepro_transaction_log WHERE rechargeproid  = ? AND rechargepro_status = 'PAID' ORDER BY transactionid DESC LIMIT 100",
                array($rechargeproid));

        }

        $return = array();
        $return[] = array(
            "REF" => "",
            "AMOUNT" => "",
            "MOBILE" => "",
            "T-ID" => "",
            "DATE" => "",
            "sync" => "1",
            "id" => "0");
        $return[] = array(
            "REF" => "",
            "AMOUNT" => "",
            "MOBILE" => "",
            "T-ID" => "",
            "DATE" => "",
            "sync" => "1",
            "id" => "0");
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {

            if (empty($row[$dbc]['REF'])) {
                $row[$dbc]['REF'] = "-";
            }
            if (empty($row[$dbc]['MOBILE'])) {
                $row[$dbc]['MOBILE'] = "-";
            }
            if (empty($row[$dbc]['DETAILS'])) {
                $row[$dbc]['DETAILS'] = "-";
            }


            $return[] = array(
                "REF" => $row[$dbc]['REF'],
                "AMOUNT" => $row[$dbc]['AMOUNT'],
                "MOBILE" => $row[$dbc]['MOBILE'],
                "T-ID" => $row[$dbc]['id'],
                "DATE" => $row[$dbc]['DATE'],
                "sync" => $row[$dbc]['sync'],
                "id" => $row[$dbc]['id']);
        }
        return $return;

    }


    public function ac($parameter)
    {
        if (!isset($parameter['private_key'])) {
            return array("status" => "100", "message" => "Invalid Private Key");
        }

        $row = self::db_query("SELECT name,mobile,email,ac_ballance,bank_name,bank_ac_name,bank_ac_number FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
            array($parameter['private_key']));
        return $row[0];
    }


    public function category_list($parameter)
    {
        $row = self::db_query("SELECT subcategory_id, name FROM rechargepro_subcategory",
            array());
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['subcategory_id']] = $row[$dbc]['name'];
        }
        return $return;
    }


    public function marchant_list($parameter)
    {
        if (!isset($parameter['id'])) {
            return "0";
        }

        $row = self::db_query("SELECT services_key, service_name, bill_primary_field,bill_secondary_field,bill_tertiary_field FROM rechargepro_services WHERE service_subcategory = ? AND status ='1'",
            array($parameter['id']));
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = array(
                $row[$dbc]['service_name'],
                $row[$dbc]['bill_primary_field'],
                $row[$dbc]['bill_secondary_field'],
                $row[$dbc]['bill_tertiary_field']);
        }
        return $return;
    }

    public function sport_list($parameter)
    {


        $row = self::db_query("SELECT services_key, service_name, bill_primary_field,bill_secondary_field,bill_tertiary_field FROM rechargepro_services WHERE services_category = '41' AND status ='1'",
            array());
        $return = array();
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $return[$row[$dbc]['services_key']] = array(
                $row[$dbc]['service_name'],
                $row[$dbc]['bill_primary_field'],
                $row[$dbc]['bill_secondary_field'],
                $row[$dbc]['bill_tertiary_field']);
        }
        return $return;
    }

    private function history_id($key)
    {
        $row = self::db_query("SELECT id FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($key));
        return $row[0]['id'];
    }


    function merchant($id)
    {
        $row = self::db_query("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
            array($id));
        return $row[0]['name'];
    }


    public function history($parameter)
    {

        $idarray = array();
        $arrayuser = array();

        $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",
            array($parameter['private_key']));
        $rechargeproid = $row[0]['rechargeproid'];


        $per_page = 13;
        $search = "";

        $page = 1;
        if (isset($parameter['page'])) {
            $page = htmlentities($parameter['page']);
        }

        $start = ($page - 1) * $per_page;


        if (isset($parameter['search'])) {
            if (!empty($parameter['search'])) {
                $search = htmlentities($parameter['search']);
            }

        }

        if (!empty($search)) {

            $totalpost = self::db_query("(SELECT transactionid  FROM rechargepro_transaction_log WHERE rechargeproid  = ? AND rechargepro_status = 'PAID' AND account_meter LIKE ?)",
                array($rechargeproid, "%$search%"), true);

            $content = array();
            $row = self::db_query("(SELECT rechargeproid,transaction_reference, amount, phone, rechargepro_service,transaction_date,rechargepro_status,transactionid,rechargepro_subservice, rechargepro_status_code, account_meter,agentprofit FROM rechargepro_transaction_log WHERE	rechargeproid  = ? AND rechargepro_status = 'PAID' AND account_meter LIKE ?  ORDER BY transaction_date DESC LIMIT $start, $per_page)",
                array($rechargeproid, "%$search%"));

        } else {
            $totalpost = self::db_query("(SELECT transactionid  FROM rechargepro_transaction_log WHERE rechargeproid  = ? AND rechargepro_status = 'PAID' )",
                array($rechargeproid), true);

            $content = array();
            $row = self::db_query("(SELECT rechargeproid,transaction_reference, amount, phone, rechargepro_service,transaction_date,rechargepro_status,transactionid,rechargepro_subservice, rechargepro_status_code, account_meter,agentprofit FROM rechargepro_transaction_log WHERE	rechargeproid  = ? AND rechargepro_status = 'PAID'  ORDER BY transaction_date DESC LIMIT $start, $per_page)",
                array($rechargeproid));
        }

        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {


            $transactionid = $row[$dbc]['transactionid'];
            $transaction_reference = $row[$dbc]['transaction_reference'];
            $transaction_date = date("d M - H:iA", strtotime("+0 minutes", strtotime($row[$dbc]['transaction_date'])));

            $phone = $row[$dbc]['phone'];
            $rechargepro_service = $row[$dbc]['rechargepro_service'];
            $amount = $row[$dbc]['amount'];
            $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];
            $status = $row[$dbc]['rechargepro_status_code'];
            $account_meter = $row[$dbc]['account_meter'];
            $profile_creator = $row[$dbc]['rechargeproid'];
            $agentprofit = $row[$dbc]['agentprofit'];


            switch ($rechargepro_subservice) {
                case "REWARD":
                    if (!array_key_exists($account_meter, $arrayuser)) {
                        $arrayuser[$account_meter] = self::merchant($account_meter);
                    }
                    $sst = "Reward from " . $arrayuser[$account_meter];
                    break;

                case "TRANSFER":
                    if (!array_key_exists($account_meter, $arrayuser)) {
                        $arrayuser[$account_meter] = self::merchant($account_meter);
                    }
                    $sst = "Transfer to " . $arrayuser[$account_meter];
                    break;

                case "TOPUP":
                    if (!array_key_exists($account_meter, $arrayuser)) {
                        $arrayuser[$account_meter] = self::merchant($account_meter);
                    }
                    $sst = "Top up From " . $arrayuser[$account_meter];
                    break;

                case "WITHDRAW":
                    if (!array_key_exists($account_meter, $arrayuser)) {
                        $arrayuser[$account_meter] = self::merchant($account_meter);
                    }
                    $sst = "Withdrawal by " . $arrayuser[$account_meter];
                    if ($rechargeproid == $profile_creator) {
                        $sst = "Withdrawal from " . $arrayuser[$account_meter];
                    }
                    break;

                default:
                    $sst = $account_meter;
            }


            if ($status == "0") {
                $sst = "Touch for instant Refund/Retry";
                $phone = "";
            }

            if (empty($sst)) {
                $sst = "-";
            }

            if (!array_key_exists($rechargepro_subservice, $idarray)) {
                $idarray[$rechargepro_subservice] = self::history_id($rechargepro_subservice);
            }

            if (empty($account_meter)) {
                $account_meter = "-";
            }
            

            $content[] = $transactionid . "#" . $rechargepro_service . "#" . $phone . "#" .
                $amount . "#" . $transaction_date . "#" . $rechargepro_subservice . "#" . $idarray[$rechargepro_subservice] .
                "#" . $status . "#" . $sst."#".$agentprofit; //
        }

        $content = implode("*", $content); //@

        return $totalpost . "#@" . ceil($totalpost / $per_page) . "#@" . $content;
    }


    public function support($parameter)
    {

        $per_page = 13;
        $search = "";

        $page = 1;
        if (isset($parameter['page'])) {
            $page = htmlentities($parameter['page']);
        }

        $start = ($page - 1) * $per_page;

        $email = $parameter['email'];
        $totalpost = self::db_query("SELECT id  FROM contact_tickets WHERE email = ?",
            array($email), true);

        $content = array();
        $row = self::db_query("SELECT * FROM contact_tickets WHERE email = ? ORDER BY status,lastupdate,locked DESC LIMIT $start, $per_page",
            array($email));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {

            $trackid = $row[$dbc]['trackid'];
            $subject = preg_replace("/[^A-Za-z0-9]/", "", $row[$dbc]['subject']);
            $locked = $row[$dbc]['locked'];
            $status = $row[$dbc]['status'];
            $id = $row[$dbc]['id'];
            $lastupdate = date("d M - H:iA", strtotime("+0 minutes", strtotime($row[$dbc]['lastupdate'])));
            $is_attachment = $row[$dbc]['is_attachment'];


            $content[] = $trackid . "#" . $subject . "#" . $locked . "#" . $status . "#" . $id .
                "#" . $lastupdate . "#" . $is_attachment;
        }

        $content = implode("@", $content);
        return array($totalpost . "#@" . ceil($totalpost / $per_page) . "#@0#@" . $content);
    }


    public function reply_support($parameter)
    {

        $email = $parameter["email"];
        $postid = $parameter['id'];
        $name = $parameter["name"];
        $comment = $parameter['comment'];


        self::db_query("INSERT INTO contact_replies (replyto,name,message,staffemail) VALUES (?,?,?,?)",
            array(
            $postid,
            $name,
            $comment,
            $email));

        $cdate = date("Y-m-d H:i:s");
        self::db_query("UPDATE contact_tickets SET lastupdate = ?, status = ? WHERE id = ? LIMIT 1",
            array(
            $cdate,
            1,
            $postid));
    }

    public function support_details($parameter)
    {


        $email = $parameter["email"];
        $postid = $parameter['id'];

        $row = self::db_query("SELECT attachment1,attachment2,attachment3,trackid,locked, id, name, dt, ip, message, subject, category FROM contact_tickets WHERE id = ? AND email = ?",
            array($postid, $email));
        $thesubject = $row[0]['subject'];
        $themessage = $row[0]['message'];
        $thetrackid = $row[0]['trackid'];
        $theid = $row[0]['id'];
        $category = $row[0]['category'];
        $thename = $row[0]['name'];
        $thedate = date("d M - H:iA", strtotime("+0 minutes", strtotime($row[0]['dt'])));
        $thelocked = $row[0]['locked'];

        $attachment1 = $row[0]['attachment1'];
        $attachment2 = $row[0]['attachment2'];
        $attachment3 = $row[0]['attachment3'];

        switch ($category) {
            case "1":
                $category = "Technical Support";
                break;

            case "2":
                $category = "Account Crediting";
                break;

            case "3":
                $category = "General Enquiry";
                break;

            default:
                $category = "Technical Support";
        }


        $attach = 0;
        if (!empty($attachment1)) {
            $attach++;
        }
        if (!empty($attachment2)) {
            $attach++;
        }
        if (!empty($attachment3)) {
            $attach++;
        }


        if (empty($theid)) {
            return array("status" => "100", "message" => "Invalid Support ID");
        }


        $return = preg_replace("/[^A-Za-z0-9]/", "", $thesubject) . "@" . $thedate . "@" .
            $category . "@" . $attach . "@" . preg_replace("/[^A-Za-z0-9]/", "", $themessage);


        self::db_query("UPDATE contact_tickets SET status = '1' WHERE id = ? LIMIT 1",
            array($theid));

        $color = array();
        $row = self::db_query("SELECT name, message, dt FROM contact_replies WHERE replyto = ?",
            array($theid));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $name = $row[$dbc]['name'];
            $mes = $row[$dbc]['message'];
            $date = $row[$dbc]['dt'];
            $color[] = $name . "#" . $mes . "#" . date("d M - H:iA", strtotime("+0 minutes",
                strtotime($date)));
            ;
        }

        if (count($color) > 0) {
            $return .= "##" . implode("@#", $color);
        }


        return array("status" => "200", "message" => $return);
    }


    public function agent_list($parameter)
    {

        $per_page = 13;
        $search = "";

        $page = 1;
        if (isset($parameter['page'])) {
            $page = htmlentities($parameter['page']);
        }

        $start = ($page - 1) * $per_page;


        $profilecreator = $parameter['profilecreator'];

        $totalpost = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE profile_agent = ? ",
            array($profilecreator), true);

        $content = array();
        $row = self::db_query("SELECT profit_bal,rechargeproid,name,username,password,public_key,public_secret,active,email,created_date,mobile,ac_ballance,last_payout,rechargeprorole,profile_creator,profile_agent FROM rechargepro_account WHERE profile_creator = ? LIMIT $start, $per_page",
            array($profilecreator));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {

            $rechargeproid = $row[$dbc]['rechargeproid'];
            $name = $row[$dbc]['name'];
            $username = $row[$dbc]['username'];
            $public_key = $row[$dbc]['public_key'];
            $public_secret = $row[$dbc]['public_secret']; //
            $email = $row[$dbc]['email'];
            $created_date = $row[$dbc]['created_date'];
            $mobile = $row[$dbc]['mobile'];
            $ac_ballance = $row[$dbc]['ac_ballance'];
            $last_payout = $row[$dbc]['last_payout'];
            $ac_rechargeprorole = $row[$dbc]['rechargeprorole'];
            $main_profile_creator = $row[$dbc]['profile_creator'];
            $profit_bal = $row[$dbc]['profit_bal']; //is account live or test
            $active = $row[$dbc]['active']; //active account
            //$profile_process_transaction = $row[$dbc]['profile_process_transaction']; //who bear t cost
            $profile_agent = $row[$dbc]['profile_agent'];


            $content[] = $rechargeproid . "#" . $name . "#" . $mobile . "#" . $ac_ballance .
                "#" . $profit_bal . "#" . $ac_rechargeprorole;
        }

        $content = implode("@", $content);
        return array($totalpost . "#@" . ceil($totalpost / $per_page) . "#@0#@" . $content);
    }


    public function daily_sales($parameter)
    {
        $cardlist = array();
        $profile_creator = $parameter['profilecreator'];
        $today = date("Y-m-d", strtotime("+0 day", strtotime($parameter['date'])));
        ;
        $later = date("Y-m-d 23:55:55", strtotime("+0 day", strtotime($today)));
        ;

        $row = self::db_query("SELECT service_charge, rechargeproid, amount, transaction_date, rechargepro_subservice FROM rechargepro_transaction_log WHERE transaction_date BETWEEN ? AND ? AND rechargepro_status = ?  AND refund = '0' AND  (cordinator_id = ? ||  rechargeproid = ?) ORDER BY transaction_date ASC",
            array(
            $today,
            $later,
            "PAID",
            $profile_creator,
            $profile_creator));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $rechargeproid = $row[$dbc]['rechargeproid'];
            $service_charge = $row[$dbc]['service_charge'];
            $tcount = 1;
            $paid_amount = $row[$dbc]['amount'];
            $last_date_time = date("H:i:s A", strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
            $rechargepro_subservice = $row[$dbc]['rechargepro_subservice'];

            if (!array_key_exists($rechargeproid, $cardlist)) {
                $first_date_time = date("H:i:s A", strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
                $cardlist[$rechargeproid][$rechargepro_subservice] = array(
                    $paid_amount,
                    $tcount,
                    $first_date_time,
                    $last_date_time,
                    $service_charge);
            } else {

                if (isset($cardlist[$rechargeproid][$rechargepro_subservice])) {
                    $nowamount = $paid_amount + $cardlist[$rechargeproid][$rechargepro_subservice][0] +
                        $service_charge;
                    $nowcount = $tcount + $cardlist[$rechargeproid][$rechargepro_subservice][1];
                    $first_date_time = $cardlist[$rechargeproid][$rechargepro_subservice][2];
                    $cardlist[$rechargeproid][$rechargepro_subservice] = array(
                        $nowamount,
                        $nowcount,
                        $first_date_time,
                        $last_date_time,
                        $service_charge);
                } else {
                    $first_date_time = date("H:i:s A", strtotime("+0 day", strtotime($row[$dbc]['transaction_date'])));
                    $cardlist[$rechargeproid][$rechargepro_subservice] = array(
                        $paid_amount,
                        $tcount,
                        $first_date_time,
                        $last_date_time,
                        $service_charge);
                }
            }

        }


        $content = array();

        $badservice = array(
            "BANK WITHDRAWAL",
            "Credit",
            "loan Credit",
            "PROFIT",
            "REWARD",
            "TOPUP",
            "Debit",
            "loan Debit",
            "WITHDRAW"); //,"TRANSFER"
        foreach ($cardlist as $key => $value) {

            $firsttransaction = "-";
            $lasttransaction = "-";
            $amount = 0;
            $tcount = 0;
            $topup = 0;
            $transfer = 0;
            $widrawl = 0;
            $reward = 0;
            $expected = 0;
            $debit = 0;
            $servicechargee = 0;

            $mycount = 0;
            foreach ($value as $service => $valuearray) {

                if (!in_array($service, $badservice)) {

                    $mycount++;
                    if ($mycount == 1) {
                        $firsttransaction = $valuearray[2];
                    }
                    $lasttransaction = $valuearray[3];
                    ;

                    $amount = $amount + $valuearray[0];
                    $servicechargee = $servicechargee + $valuearray[4];
                    $tcount++;
                }

                if (in_array($service, array("PROFIT", "REWARD"))) {
                    $reward = $reward + $valuearray[0] + $valuearray[4];
                }

                if (in_array($service, array(
                    "Credit",
                    "TOPUP",
                    "loan Credit"))) {
                    $topup = $topup + $valuearray[0] + $valuearray[4];
                }

                if (in_array($service, array("Debit", "loan Debit"))) {
                    $debit = $debit + $valuearray[0] + $valuearray[4];
                }

                //if($service == "TRANSFER"){$transfer = $transfer+$valuearray[0];}
                if ($service == "WITHDRAW") {
                    $widrawl = $widrawl + $valuearray[0] + $valuearray[4];
                }

            }

            $name = self::merchant($key);

            $content[] = $rechargeproid . "#" . $name . "#" . $amount . "#" . $servicechargee .
                "#" . ($amount + $servicechargee);

        }

        $content = implode("@", $content);
        return $content;
    }


    function clean_myapp($st)
    {
        return preg_replace("/[^A-Za-z0-9 -=@;]/", "", $st);
    }


    public function downloads($parameter)
    {
        $per_page = $parameter['per_page'];
        $search = "";
        $category = "";
        $subcatigory = "";
        $tag = "0";

        $page = 1;
        if (isset($parameter['page'])) {
            $page = htmlentities($parameter['page']);
        }


        if (!empty($parameter['search'])) {
            $search = $parameter['search'];
        }


        //switch category
        $start = ($page - 1) * $per_page;


        $post = array();


        $rowcount = self::db_query("SELECT id FROM rechargepro_services WHERE (services_key LIKE ? OR service_name LIKE ?)  AND status = '1' AND (services_category = '7' OR services_category = '41') LIMIT $start, $per_page",
            array("%$search%", "%$search%"), true);

        if ($rowcount > 0) {
            $row = self::db_query("SELECT id,services_key,service_name,services_category,bill_primary_field,bill_secondary_field,bill_tertiary_field FROM rechargepro_services WHERE (services_key LIKE ? OR service_name LIKE ?)  AND status = '1' AND (services_category = '7' OR services_category = '41') LIMIT $start, $per_page",
                array("%$search%", "%$search%"));
            for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {

                $id = $row[$dbc]['id'];
                $services_key = $row[$dbc]['services_key'];
                $service_name = $row[$dbc]['service_name'];
                $services_category = $row[$dbc]['services_category'];
                $bill_primary_field = $row[$dbc]['bill_primary_field'];
                $bill_secondary_field = $row[$dbc]['bill_secondary_field'];
                $bill_tertiary_field = $row[$dbc]['bill_tertiary_field'];


                $post[] = $services_key . "#" . $service_name . "#" . $services_category . "#" .
                    $id . "#" . $bill_primary_field . "#" . self::clean_myapp($bill_secondary_field) .
                    "#" . self::clean_myapp($bill_tertiary_field) . "n";
            }


            if ($rowcount < $per_page) {
                $totalpages = 1;
            } else {
                $totalpages = ceil($rowcount / $per_page);
            }


            $content = implode("_", $post);
            return array($rowcount . "*" . $totalpages . "#@" . $content);

        } else {
            return array("0*0#@");
        }
    }


    public function print_ticket($parameter)
    {

        if (!isset($parameter["id"])) {
            return array("status" => "100", "message" => "Invalid ID");
        }

        $row = self::db_query("SELECT transactionid,rechargepro_service,rechargepro_subservice,account_meter,business_district,thirdPartycode,address,name,phcn_unique,amount,phone,email,payment_method,transaction_status,transaction_code,transaction_reference,rechargepro_status,rechargepro_status_code,rechargepro_print,transaction_date FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($parameter['id']));

        if (empty($row[0]['transactionid'])) {
            return array("status" => "100", "message" => "Invalid ID");
        }


        $transactionid = $row[0]['transactionid'];
        $rechargepro_service = $row[0]['rechargepro_service'];
        $rechargepro_subservice = $row[0]['rechargepro_subservice'];
        $account_meter = $row[0]['account_meter'];
        $business_district = $row[0]['business_district'];
        $thirdPartycode = $row[0]['thirdPartycode'];
        $address = $row[0]['address'];
        $name = $row[0]['name'];
        $phcn_unique = $row[0]['phcn_unique'];
        $amount = $row[0]['amount'];
        $phone = $row[0]['phone'];
        $email = $row[0]['email'];
        $payment_method = $row[0]['payment_method'];
        $transaction_status = $row[0]['transaction_status'];
        $transaction_code = $row[0]['transaction_code'];
        $transaction_reference = $row[0]['transaction_reference'];
        $rechargepro_status = $row[0]['rechargepro_status'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $rechargepro_print = $row[0]['rechargepro_print'];
        $transaction_date = $row[0]['transaction_date'];


        $response = json_decode($rechargepro_print, true);
        $response = self::array_flatten($response);

        // $response = json_decode($rechargepro_print, true);
        //$response = array_change_key_case($response , CASE_LOWER);


        if ($rechargepro_subservice == "AED") {
            $response["Account Type"] = "Prepaid";
        }

        //$response["Transaction Status"] = "Successful";
        $response["Transaction Date"] = $transaction_date;

        if (isset($response['details'])) {
            if (is_array($response['details'])) {
                foreach ($response['details'] as $key => $value) {
                    $response[$key] = $value;
                    unset($response['details']);
                }
            }
        }


        if (isset($response['details']['token'])) {
            $response['Token'] = $response['details']['token'];
        }


        $arrayreturn = array();
        foreach ($response as $key => $value) {
            if (!is_array($value)) {
                $arrayreturn[$key] = $value;
            } else {
                foreach ($value as $keya => $valuea) {
                    $arrayreturn[$keya] = $valuea;
                }
            }
        }


        $response = $arrayreturn;


        $temarray = array_values($response);
        foreach (self::myarray() as $a) {
            if (array_key_exists($a, $response)) {
                unset($response[$a]);
            }

            if (in_array($a, $temarray)) {
                unset($response[$a]);
            }
        }


        if (isset($response['Address'])) {
            self::move_to_top($response, 'Address');
        }

        if (isset($response['Name'])) {
            self::move_to_top($response, 'Name');
        }

        if (isset($response['MeterNumber'])) {
            self::move_to_top($response, 'MeterNumber');
        }

        if (isset($response['Token'])) {
            self::move_to_top($response, 'Token');
        }

        if (isset($response['token'])) {
            self::move_to_top($response, 'token');
        }
        // print_r($response);
        // $arrayreturn["Transaction Status"] = "Successful";
        //$arrayreturn["Transaction Date"] = $transaction_date;

        return array("status" => "200", "message" => array('details' => $response));

    }


    public function try_again_new($parameter)
    {


        if (!isset($parameter["tid"])) {
            return array("status" => "100", "message" => "Invalid ID");
        }

        $parameter['serial'] = "web";

        $row = self::db_query("SELECT rechargepro_subservice FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($parameter['tid']));
        $rechargeproservice = $row[0]['rechargepro_subservice'];

        if (empty($rechargeproservice)) {
            return array("status" => "100", "message" => "Invalid ID");
        }

        $row = self::db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($rechargeproservice));


        if ($row[0]['services_category'] == "1") {
            include "electricity.php";
            $electricity = new electricity("POST");
            return $electricity->complete_transaction($parameter);
        }

     
        if ($row[0]['services_category'] == "2" || $row[0]['services_category'] == "3") {
            include "airtime_data.php";
            $airtime_data = new airtime_data("POST");
            return $airtime_data->complete_transaction($parameter);
        }

        if ($row[0]['services_category'] == "5") {
            include "tv.php";
            $tv = new tv("POST");
            return $tv->complete_transaction($parameter);
        }

        if ($row[0]['services_category'] == "6") {
            include "lottery.php";
            $lottery = new lottery("POST");
            return $lottery->complete_transaction($parameter);
        }

        if ($row[0]['services_category'] == "7") {
            include "bills.php";
            $bills = new bills("POST");
            return $bills->complete_transaction($parameter);
        }


        if ($rechargeproservice == "BANK WITHDRAWAL") {
            include "bank_withdrawal.php";
            $bank_withdrawal = new bank_withdrawal("POST");
            return $bank_withdrawal->complete_transaction($parameter);
        }


        if ($rechargeproservice == "BANK TRANSFER") {
            include "bank_transfer.php";
            $bank_transfer = new bank_transfer("POST");
            return $bank_transfer->complete_transaction($parameter);
        }

        return array("status" => "100", "message" => "Invalid Request4");

    }


    public function try_again($parameter)
    {


        if (!isset($parameter["tid"])) {
            return array("status" => "100", "message" => "Invalid ID");
        }


        if (!isset($parameter['serial'])) {
            $parameter['serial'] = "web";
        }

        $row = self::db_query("SELECT rechargepro_subservice FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($parameter['tid']));
        $rechargeproservice = $row[0]['rechargepro_subservice'];

        if (empty($rechargeproservice)) {
            return array("status" => "100", "message" => "Invalid ID");
        }

        $row = self::db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($rechargeproservice));


        if ($row[0]['services_category'] == "1") {
            include "electricity.php";
            $electricity = new electricity("POST");
            return $electricity->complete_transaction($parameter);
        }
        
                if ($row[0]['services_category'] == "3") {
            include "airtime_data.php";
            $airtime_data = new airtime_data("POST");
            return $airtime_data->complete_transaction($parameter);
        }                

        if ($row[0]['services_category'] == "2") {
            include "airtime_data.php";
            $airtime_data = new airtime_data("POST");
            return $airtime_data->complete_transaction($parameter);
        }

        if ($row[0]['services_category'] == "5") {
            include "tv.php";
            $tv = new tv("POST");
            return $tv->complete_transaction($parameter);
        }

        if ($row[0]['services_category'] == "6") {
            include "lottery.php";
            $lottery = new lottery("POST");
            return $lottery->complete_transaction($parameter);
        }

        if ($row[0]['services_category'] == "7") {
            include "bills.php";
            $bills = new bills("POST");
            return $bills->complete_transaction($parameter);
        }


        if ($rechargeproservice == "BANK WITHDRAWAL") {
            include "bank_withdrawal.php";
            $bank_withdrawal = new bank_withdrawal("POST");
            return $bank_withdrawal->complete_transaction($parameter);
        }


        if ($rechargeproservice == "BANK TRANSFER") {
            include "bank_transfer.php";
            $bank_transfer = new bank_transfer("POST");
            return $bank_transfer->complete_transaction($parameter);
        }


        if ($rechargeproservice == "TOPUP") {
            $tid = $parameter['tid'];
            $row = self::db_query("SELECT amount,rechargeproid FROM rechargepro_transaction_log WHERE transactionid = ? AND 	rechargepro_status = 'PAID' LIMIT 1",
                array($tid));
            $amount_to_charge = $row[0]['amount'];
            $whoto_rechargeproid = $row[0]['rechargeproid'];

            if (!empty($whoto_rechargeproid)) {
                $result = '{"details":{"CREDIT":"' . $amount_to_charge .
                    '","TRANSACTION STATUS","DONE"}}';
                self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_status_code = '1', rechargepro_print = ? WHERE transactionid = ? LIMIT 1",
                    array($result, $tid));

                self::db_query("UPDATE rechargepro_account SET ac_ballance = ac_ballance + ? WHERE rechargeproid = ? LIMIT 1",
                    array($amount_to_charge, $whoto_rechargeproid));
            }

        }

        return array("status" => "100", "message" => "Invalid Request5");

    }


    public function myapp_pos($parameter)
    {


        // if(isset($parameter['service'])){
        //     $parameter['service'] = trim($parameter['service']);
        //      return "bad@".$parameter['service'];
        //  }


        //return "bad*".$parameter['service_request'];


        $array = self::myapp_request($parameter);


        if ($array["status"] == "100") {

            return "bad*" . $array["message"];

        } else {

            $array = $array["message"];

            ////////////////////
            $c = array();
            if (isset($array['TransactionID'])) {

                $p = self::print_ticket(array("id" => $array['TransactionID']));
                $p = $p['message'];

                $c = array();
                $row = self::db_query("SELECT rechargepro_subservice,rechargepro_service FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
                    array($array['TransactionID']));
                $rechargepro_service = $row[0]['rechargepro_service'];
                $rechargepro_subservice = $row[0]['rechargepro_subservice'];

                $c[] = $rechargepro_subservice;
                $c[] = $rechargepro_service;

                $cb = $p;
                if (isset($p['details'])) {
                    $cb = $p['details'];
                }

                foreach ($cb as $a => $b) {
                    $c[] = $a . " : " . $b;
                }
            }


            /////////////////////////////
            if (isset($array['tid'])) {
                $c = array($array['tid']);
                foreach ($array as $a => $b) {
                    $c[] = $b;
                }
            }


            ////////////////////////////
            if (!isset($array['tid']) && !isset($array['TransactionID'])) {

                $c = array();
                if (isset($parameter['id'])) {
                    $row = self::db_query("SELECT rechargepro_subservice,rechargepro_service FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
                        array($parameter['id']));
                    $rechargepro_service = $row[0]['rechargepro_service'];
                    $rechargepro_subservice = $row[0]['rechargepro_subservice'];

                    $c[] = $rechargepro_subservice;
                    $c[] = $rechargepro_service;
                }

                $cb = $array;
                if (isset($array['details'])) {
                    $cb = $array['details'];
                }

                foreach ($cb as $a => $b) {
                    $c[] = $a . " : " . $b;
                }

            }


            return "ok*" . implode("@", $c);
        }


    }


}



?>