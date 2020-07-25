<?php
class login extends Api
{

    public function __construct($method)
    {

    }

    public function login($parameter)
    {


        if (!isset($parameter['username']) || !isset($parameter['password'])) {
            return "bad@Invalid Username";
        }


        $username = $parameter['username'];
        $password = $parameter['password'];
        $serial = self::clean_transaction($parameter['serial']);
        $type = "POS";
        $devicename = "POS";
        $yes = 0;


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
                return "bad@Invalid Username";
            }
        }


        if (empty($row['rechargeproid'])) {
            return "bad@Invalid Username";
        } else {


            //check if device has free access
            $deviceisfree = 0;
            if ($bypass > 0) {
                if ($type == $bypass) {
                    $deviceisfree = 1;
                }
            }


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

            return "ok@" . $row['name'] . "@" . $row['rechargeproid'] . "@" . date("Y-m-d H:i:s") .
                "@" . $code;
        }

    }
    
    
    
    public function clean_transaction($c)
    {
        return preg_replace("/[^a-zA-Z0-9\-]/", "", $c);
    }


}



?>