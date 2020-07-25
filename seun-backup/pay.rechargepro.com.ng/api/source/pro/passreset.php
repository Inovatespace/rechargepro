<?php
/**
 * 
 */
class passreset extends Api
{


    function __construct()
    {
        # code...
    }


    public static function initiate_pass_reset($parameter)
    {


        if (!isset($parameter["email"])) {
            return array("status" => "100", "message" => "No record found");
        }

        $email = $parameter["email"];

        $row = self::db_query("SELECT quickpayid,email FROM quickpay_account WHERE email = ? OR mobile = ?",
            array($email, $email));
        $row = $row[0];
        $email = $row["email"];

        if (!empty($row['quickpayid'])) {
            $token = self::RandomString(4, 4);

            self::db_query("UPDATE quickpay_account SET temp_code = ? WHERE quickpayid = ?",
                array($token, $row['quickpayid']));

            $msg = "Hi" . "\r\n" .
                " A request to change your password has been received. Enter the token below to reset your password." .
                "\r\n"; //
            $msg .= "Token: " . $token . "\r\n";
            $msg .= "QuickPay";


            self::send_mail(array("noreply@quickpay.com.ng", "QuickPay"), $email,
                "TOKEN RESET", $msg);

            return array("status" => "200", "message" => "success");


        } else {
            return array("status" => "100", "message" => "No record found");
        }
    }

    public static function confirm_pass_reset($parameter)
    {
        $email = $parameter['email'];
        $token = $parameter['token'];
        $newpassword = $parameter['newpassword'];
        $verifypassword = $parameter['verifynewpassword'];


        if ($newpassword != $verifypassword) {
            return array("status" => "100", "message" => "Password do not match");
        }

        $row = self::db_query("SELECT quickpayid,email FROM quickpay_account WHERE 	(email = ? OR mobile = ?) AND temp_code = ? LIMIT 1",
            array(
            $email,
            $email,
            $token));
        $row = $row[0];
        $quickpayid = $row["quickpayid"];

        if (empty($row['quickpayid'])) {
            return array("status" => "100", "message" => "Invalid Code");
        }

        $password = sha1(md5($newpassword) . self::config("user_key"));

        $row = self::db_query("UPDATE quickpay_account SET password = ?, temp_code='1f4' WHERE quickpayid= ? LIMIT 1",
            array($password, $quickpayid));

        return array("status" => "200", "message" => "successful");

    }


    public static function initiate_pin_reset($parameter)
    {

        if (!isset($parameter["email"])) {
            return array("status" => "100", "message" => "No record found");
        }

        $email = $parameter["email"];

        $row = self::db_query("SELECT quickpayid,email FROM quickpay_account WHERE email = ? OR mobile = ?",
            array($email, $email));
        $row = $row[0];
        $email = $row["email"];

        if (!empty($row['quickpayid'])) {
            $token = self::RandomString(4, 4);

            self::db_query("UPDATE quickpay_account SET temp_code = ? WHERE quickpayid = ?",
                array($token, $row['quickpayid']));

            $msg = "Hi" . "\r\n" .
                " A request to change your PIN has been received. Enter the token below to reset your PIN." .
                "\r\n"; //
            $msg .= "Token: " . $token . "\r\n";
            $msg .= "QuickPay";

            self::send_mail(array("noreply@quickpay.com.ng", "QuickPay"), $email,
                "TOKEN RESET", $msg);

            return array("status" => "200", "message" => "success");


        } else {
            return array("status" => "100", "message" => "No record found");
        }
    }


    public static function confirm_pin_reset($parameter)
    {
        $email = $parameter['email'];
        $token = $parameter['token'];


        $row = self::db_query("SELECT quickpayid,email FROM quickpay_account WHERE 	(email = ? OR mobile = ?) AND temp_code = ? LIMIT 1",
            array(
            $email,
            $email,
            $token));
        $row = $row[0];
        $quickpayid = $row["quickpayid"];


        if (empty($row['quickpayid'])) {
            return array("status" => "100", "message" => "Invalid Code");
        }


        $row = self::db_query("UPDATE quickpay_account SET temp_code='1f4' WHERE quickpayid= ? LIMIT 1",
            array($quickpayid));

        return array("status" => "200", "message" => array("message" => "successful",
                    "hash" => md5($email)));
    }


}
?>