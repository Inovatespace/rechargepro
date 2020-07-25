<?php
if (!isset($_SESSION)) {
    #$some_name = session_name("some_name");
    #session_set_cookie_params(0, '/', '.some_domain.com');f
    #session_start();
    session_start();
}


define('AUTH_DOCROOT', dirname(dirname(dirname(__file__))) . '/');
define('MAIN_ROOT', realpath(dirname(__file__)) . '/');


//require_once AUTH_DOCROOT . '/engine/PHPMailer/class.phpmailer.php';
require_once MAIN_ROOT . "/Logger.php";
require_once MAIN_ROOT . "/password.php";
require_once MAIN_ROOT . "/curl.php";
class engine extends curl
{

    public $current_menu = array();

    public function __construct()
    {
        $Logger = new Logger(array('path' => AUTH_DOCROOT . '/log/'));
        $Logger->enable_exception();

        if (self::config('log_error')) {
            $Logger->enable_error();
            $Logger->enable_display_error(self::config('display_error'));
            $Logger->enable_fatal();
            $Logger->enable_method_file(true);
        } else {
            $Logger->enable_display_error(self::config('display_error'));
        }

    }
    
    function array_change_value_case($input)
{
$narray = array();
if (!is_array($input))
{
return $narray;
}
foreach ($input as $key => $value)
{
if (is_array($value))
{
$tkey = strtolower($key); 
$narray[$tkey] = self::array_change_value_case($value);
 continue;
}
$tkey = strtolower($key);
$narray[$tkey] = strtolower($value);
}
return $narray;
}

function url_origin($use_forwarded_host = false )
{
    $ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
    $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $_SERVER['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : ( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $_SERVER['SERVER_NAME'] . $port;
    //return $protocol . '://' . $host .$_SERVER['REQUEST_URI'];
    return $_SERVER['REQUEST_URI'];
}



    function notification($rechargeproid_main, $message, $type = 0)
    {

        $vowels = array("<br />", "<br/>");
        $message = str_replace($vowels, "", $message);

        self::db_query("INSERT INTO messages (rechargeproid,message,message_type) VALUES (?,?,?)",
            array(
            $rechargeproid_main,
            $message,
            $type));
    }


    function toMoney($val, $symbol = 'N', $r = 2)
    {

        $n = $val;
        $sign = ($n < 0) ? '-' : '';
        $i = number_format(abs($n), $r);

        return $symbol . $sign . $i;


    }

    public function cleandigit($c)
    {
        return preg_replace("/[^0-9][.]/", "", $c);
    }


    function return_value($value)
    {
        if (!is_array($value)) {
            return ucwords($value);
        } else {
            foreach ($value as $valuea => $valueb) {
                return ", $valuea : $valueb";
            }
        }

    }

    function return_name($key, $value)
    {
        if (!is_array($key)) {

            $key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);
            if (!empty($value)) {
                return '<div  style="overflow: hidden;">
    <div style="float: left; font-weight:bold;">' . ucwords($key) . ': </div>
    <div style="float: left;">' . self::return_value($value) . '</div>
    </div>';
            }

        } else {

            foreach ($keya as $keyb => $valueb) {
                $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
                return self::return_name($keyb, $valueb);
            }
        }

    }


    public function que_rechargepropay_mail($tid, $email, $status)
    {
        //validate email
        if ($status == "success") {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "ok";
            }
            

            $row = self::db_query("SELECT rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,rechargepro_service,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
                array($tid));
            $thirdPartyCode = $row[0]['thirdPartycode'];
            $name = $row[0]['name'];
            $address = $row[0]['address'];
            $district = $row[0]['business_district'];
            $unique = $row[0]['phcn_unique'];
            $service = $row[0]['rechargepro_subservice'];
            $accountnumber = $row[0]['account_meter'];
            $phone = $row[0]['phone'];
            $rechargepro_service = $row[0]['rechargepro_service'];
            $amount = $row[0]['amount'];
            $rechargepro_status_code = $row[0]['rechargepro_status_code'];
            $rechargepro_print = $row[0]['rechargepro_print'];

            $tosend = "";

            $response = json_decode($rechargepro_print, true);
            
            $response = self::array_flatten($response);

           // if (isset($response['details'])) {

                $tosend = "";
                foreach ($response as $key => $value) {

                    if (!is_array($key)) {

                        $key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);

                        $tosend .= self::return_name($key, $value);

                    } else {
                        foreach ($key as $keya => $valuea) {

                            if (!is_array($keya)) {
                                $keya = preg_replace('/([A-Z])(?<!^)/', ' $1', $keya);
                                $tosend .= self::return_name($keya, $valuea);
                            } else {

                                foreach ($keya as $keyb => $valueb) {
                                    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
                                    $tosend .= self::return_name($keyb, $valueb);
                                }

                            }


                        }
                    }

                }
          //  }


            if (isset($response['VendorReferenceeeeeeeeeeeeeeeeeeeeeee'])) {
                $tosend = "";
                foreach ($response as $key => $value) {

                    if (!is_array($key)) {

                        $key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);

                        $tosend .= self::return_name($key, $value);

                    } else {
                        foreach ($key as $keya => $valuea) {

                            if (!is_array($keya)) {
                                $keya = preg_replace('/([A-Z])(?<!^)/', ' $1', $keya);
                                $tosend .= self::return_name($keya, $valuea);
                            } else {

                                foreach ($keya as $keyb => $valueb) {
                                    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
                                    $tosend .= self::return_name($keyb, $valueb);
                                }

                            }


                        }
                    }

                }
            }


            if (!empty($tosend)) {
                return self::send_mail("noreply@rechargepropay.com", $email, "rechargepropay $service Purchase",
                    $tosend);
            } else {
                return "ok";
            }

        }

    }


    public function curlit($from, $message, $sender="RechargePro")
    {

    $sms_array = array (
                'sender'    => "RechargePro",
                'to' => $from,
                'message'   => $message,
                'type'  => '0',          //This can be set as desired. 0 = Plain text ie the normal SMS
                'routing' => '5',         //This can be set as desired. 3 = Deliver message to DND phone numbers via the corporate route
                'token' => "EJuGhexNgRuhhofzRiguTVaMF4AbNr2uT2kNrT4AxAIo4OQtyonNvDmHtu82wolfAKXCymG5hgbWJMRYVwFQoHmTDRrVQSjZz3Z7"
                );    
        
   $params = http_build_query($sms_array);
    $ch = curl_init(); 
    
    curl_setopt($ch, CURLOPT_URL,"https://smartsmssolutions.com/api/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);   
 
    $output=curl_exec($ch);
 
    curl_close($ch);
    return $output;      
    }


    private function check_empty($string)
    {

        $array = self::myarray();
        if (!is_array($string)) {
            $string = trim(strtolower($string));
            if (!in_array($string, $array) && !empty($string) && strlen($string) > 1) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!empty($string) && count($string) > 0) {
                return true;
            } else {
                return false;
            }

        }
    }

    private function return_valueb($value)
    {
        if (!is_array($value)) {
            return ucwords(trim($value));
        } else {
            foreach ($value as $valuea => $valueb) {
                return array(trim($valuea) => trim($valueb));
            }
        }

    }

    private function return_nameb($key, $value)
    {

        if (!is_array($key)) {

            $key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);
            if (!empty($value)) {

                if (!is_array($value)) {
                    $val = self::return_valueb($value);
                    $check = 0;


                    if (self::check_empty($key)) {
                        $check = $check + 1;
                    }

                    if (self::check_empty($val)) {
                        $check = $check + 1;
                    }

                    if ($check == 2) {
                        return array(ucwords(trim($key)) => trim($val));
                    }
                } else {

                    foreach ($value as $keyb => $valueb) {
                        $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
                        return self::return_nameb($keyb, $valueb);
                    }

                }

            }

        } else {

            foreach ($keya as $keyb => $valueb) {
                $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
                return self::return_nameb($keyb, $valueb);
            }
        }

    }
    public function move_to_top(&$array, $key)
    {
        $temp = array($key => $array[$key]);
        unset($array[$key]);
        $array = $temp + $array;
    }

    function myarray()
    {
        $array = array(
            "VendorReference",
            "Reference",
            "ResponseTime",
            "UtilityAmtVatExcl",
            "FreeUnits",
            "ReceiptNumber",
            "RefundUnits",
            "RefundAmount",
            "ServiceChargeVatExcl",
            "IsRequery",
            "VendorName",
            "VendorOperatorName",
            "VendorTerminalId",
            "TerminalId",
            "SupplyGroupCode",
            "KeyRevisionNumber",
            "TariffIndex",
            "AlgorithmTechnology",
            "TokenTechnology",
            "VatRegNumber",
            "Message",
            "VatInvoiceNumber",
            "ResponseCode",
            "ResponseMessage",
            "exchange reference",
            "done",
            "0.0",
            "null",
            "false",
            "true",
            "nigeria",
            "response  code",
            "response  message",
            "Vendor Reference",
            "reference",
            "transactionnumber",
            "customercarereferenceid",
            "",
            "",
            "Vendor Name",
            "Vendor Operator Name",
            "",
            " ",
            "responsecode",
            "demo",
            "demo1",
            "Vendor Terminal Id",
            "Terminal Id",
            "",
            "status",
            "message",
            "paid_amount",
            "paid_currency",
            "topup_currency",
            "target",
            "product_id",
            "time",
            "country",
            "completed_in",
            "customer_reference",
            "api_transactionid",
            "pin_based",
            "AuditNo",
            "Description",
            "ProviderResponse",
            "RequestId",
            "ConfirmationCode",
            "uniqueReference",
            "exchangeReference",
            "transactionNumber",
            "MerchantReference",
            "PayUVasReference",
            "VasProvider",
            "VasProviderReference",
            "CustomFields",
            "Customfield",
            "incidentDeduction",
            "@attributes",
            "integratedBusinessCenter",
            "Key",
            "responsemessage",
            "meterNumber",
            "paymentAmount",
            "avtualTokenValue",
            "bal",
            "pft",
            "response_desc",
            "Game",
            "Partner Name",
            "payment_ref",
            "sales_date",
            "License",
            "ENQ",
            "FN",
            "DYA",
            "TransactionID",
            "retailer_id","Vat","Tariff","TariffRate","LastPurchase","Account Type","Website","Date","Address","DebtDescription","SupplyGroupCode","KeyRevisionNumber","TariffIndex","AlgorithmTechnology","TokenTechnology","VatRegNumber","VatInvoiceNumber","Message");

        return $array;
    }
    
    
    public function array_flatten($d){
          if(!is_array($d)){
            return false;
          }
          
          $result = array();
          foreach($d as $k => $v){
            if(is_array($v)){
                $result = array_merge($result,self::array_flatten($v));
            }else{
               $result[$k] = $v; 
            }
          }
            
            return $result;
        }
        
        
    public function que_rechargepropay_sms($tid)
    {
        if (!isset($tid)) {
            return "ok";
        }

        $row = self::db_query("SELECT rechargepro_status,transactionid,rechargepro_subservice,account_meter,phone,rechargepro_service,amount,transactionid,business_district,thirdPartycode,address,name,phcn_unique,rechargepro_status_code,rechargepro_print FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $name = $row[0]['name'];
        $address = $row[0]['address'];
        $district = $row[0]['business_district'];
        $unique = $row[0]['phcn_unique'];
        $service = $row[0]['rechargepro_subservice'];
        $accountnumber = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $rechargepro_service = $row[0]['rechargepro_service'];
        $amount = $row[0]['amount'];
        $rechargepro_status_code = $row[0]['rechargepro_status_code'];
        $rechargepro_print = $row[0]['rechargepro_print'];


        if (empty($phone)) {
            return "ok";
        }

        if (strlen($phone) < 11 || strlen($phone) > 11) {
            return "ok";
        }


        $array = array(
            "purchasedunits",
            "exchange reference",
            "done",
            "utilitydetail",
            "responsemessage",
            "vatregnumber",
            "customerdetail",
            "receiptnumber",
            "freeunits",
            "refundunits",
            "responsetime",
            "utilityamtvatexcl",
            "refundamount",
            "servicechargevatexcl",
            "debtdescription",
            "vat",
            "debtamount",
            "isrequery",
            "meterdetail",
            "0.0",
            "0",
            "null",
            "false",
            "true",
            "nigeria",
            "response  code",
            "response  message",
            "vendor  reference",
            "reference",
            "supply  group  code",
            "vendor  terminal  id",
            "terminal  id",
            "is  requery",
            "transactionnumber",
            "customercarereferenceid",
            "",
            "",
            "vendor name",
            "vendor operator name",
            "",
            " ",
            "responsecode",
            "vendorname",
            "demo",
            "vendoroperatorname",
            "demo1",
            "vendorterminalid",
            "terminalid",
            "",
            "vendorreference",
            "status",
            "message",
            "paid_amount",
            "paid_currency",
            "topup_currency",
            "target",
            "product_id",
            "time",
            "country",
            "completed_in",
            "customer_reference",
            "api_transactionid",
            "pin_based");


        $response = json_decode($rechargepro_print, true);
        if (is_array($response)) {
            $response = array_change_key_case($response, CASE_LOWER);
            //$response["Transaction Status"] = "Successful";


            if (isset($response['details'])) {
                foreach ($response['details'] as $key => $value) {
                    $response[$key] = $value;
                    unset($response['details']);
                }
            }


            if (isset($response['token'])) {
                self::move_to_top($response, 'token');
            }


            foreach ($array as $a) {
                if (array_key_exists($a, $response)) {
                    unset($response[$a]);
                }
            }

            $arrayreturn = array();


            if (count($response) > 0) {
                foreach ($response as $key => $value) {

                    if (!is_array($key)) {

                        $key = preg_replace('/([A-Z])(?<!^)/', ' $1', $key);

                        if (self::check_empty(self::return_nameb($key, $value))) {
                            $arrayreturn = array_merge_recursive($arrayreturn, self::return_nameb($key, $value));
                        }

                    } else {
                        foreach ($key as $keya => $valuea) {

                            if (!is_array($keya)) {
                                $keya = preg_replace('/([A-Z])(?<!^)/', ' $1', $keya);
                                if (self::check_empty(self::return_nameb($keya, $valuea))) {
                                    $arrayreturn = array_merge_recursive($arrayreturn, self::return_nameb($keya, $valuea));
                                }
                            } else {

                                foreach ($keya as $keyb => $valueb) {
                                    $keyb = preg_replace('/([A-Z])(?<!^)/', ' $1', $keyb);
                                    if (self::check_empty(self::return_nameb($keyb, $valueb))) {
                                        $arrayreturn = array_merge_recursive($arrayreturn, self::return_nameb($keyb, $valueb));
                                    }
                                }

                            }

                        }
                    }

                }
            }


            $toreturn = "";
            foreach ($arrayreturn as $key => $value) {
                if (!is_array($key) && !is_array($value)) {
                    $toreturn .= $key . ":" . $value . "\r\n";
                }

            }

            $message = substr($toreturn, 0, 150);
            return self::curlit($phone, $message);

        }
    }


    public function send_mail($from, $to, $subject, $message,$attachment=array())
    {
        
        
        
    define("EMAILQUEUE_DIR", AUTH_DOCROOT . '/engine/emailque/'); // Setup where emailqueue resides

    include_once EMAILQUEUE_DIR."config/application.config.inc.php"; // Includes emailqueue configuration
    include_once EMAILQUEUE_DIR."config/db.config.inc.php"; // Includes database connection configuration
    include_once EMAILQUEUE_DIR."scripts/emailqueue_inject.class.php"; // Includes emailqueue_inject class
    
    $emailqueue_inject = new emailqueue_inject(DB_HOST, DB_UID, DB_PWD, DB_DATABASE); // Creates an emailqueue_inject object. Needs the database connection information.
    
    $result = $emailqueue_inject->inject( // Injects an email to the queue
        null, // foreign_id_a
        null, // foreign_id_b
        null, // priority
        true, // is_inmediate
        null, // date_queued
        true, // is_html
        $from[0], // from
        $from[1], // from_name
        $to, // to
        $from[0], // replyto
        $from[1], // replyto_name
        $subject, // subject
        $message, // content
        false, // content_non_html: Optional, the content to show when the user is viewing the email with a client not capable of HTML (quite rare nowadays). If set to false, the given content HTML (above) will be automatically converted to a non-HTML version.
        false, // list_unsubscribe_url: Optional, URL where users can unsubscribe from the newletter (if it's a newletter). Use it to improve mailbox placement.
        // An optional array of files to be attached. Each file must be in turn a hash array specifying at least the "path" key.
        // Nice idea: Always attach an VCF contact card so users can simply click on it to add you as a contact, thus causing the email client to always consider your messages as no-spam.
        $attachment,
           // array(
            //    "path" => AUTH_DOCROOT."/frontend/gfx/img/logo_small.png", // Required. PHP must have permissions enough to read this file.
              //  "fileName" => "logo_small.png", // Optional. Emailqueue will extract the filename from the path if not specified.
               // "encoding" => "base64", // Optional. Defaults to "base64"
              //  "type" => "image/png" // Optional. Emailqueue will try to determine the type
            //),
            //array(
            //    "path" => __DIR__."/frontend/gfx/img/item.gif"
           // )
        true // is_embed_images: Whether to convert any images found on the given HMTL to attachments, so the email is completely self-contained, containing all the images it needs to be rendered correctly. Might cause some email clients to always show the images on a message instead of giving the user the option to download them. Beware: Activating this option will make your emails a lot bigger, increasing the bandwidth usage dramatically. It's been found that embedding images this way ranks high in at least some SPAM detection engines, maybe because the increased email footprint. Make your research and perform tests.
    );
    
    return $result;

    }


    public function event_log($id, $type, $who, $ip, $event)
    {
        self::db_query("INSERT activity_log (rechargeproid,activity_type,who,event,ip) VALUES (?,?,?,?,?)",
            array(
            $id,
            $type,
            $who,
            $event,
            $ip));
    }

    public function curPageURL()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) == "on") {
            $pageURL .= "s";
        }

        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public function getlanguage()
    {

    }

    public function icon_picture($id)
    {

        if (empty($id)) {
            return "/theme/" . self::config('theme') . "/images/icon.png";
        }

        $file = $id . ".jpg";

        $img = "/theme/" . self::config('theme') . "/images/icon.png";
        if (file_exists(AUTH_DOCROOT . "theme/" . self::config('theme') . "/icons/" . $file)) {
            return "/theme/" . self::config('theme') . "/icons/" . $file;
        }
        if (is_dir(AUTH_DOCROOT . "theme/" . self::config('theme') . "/icons/" . $file)) {
            return "/theme/" . self::config('theme') . "/images/icon.png";
            ;
        }

        return $img;
    }
    
    
    public function picture($id)
    {

        if (empty($id)) {
            return "/theme/" . self::config('theme') . "/images/small_default.png";
        }

        $file = $id . ".jpg";

        $img = "/theme/" . self::config('theme') . "/images/small_default.png";
        if (file_exists(AUTH_DOCROOT . "avater/" . $file)) {
            return "/avater/" . $file;
        }
        if (is_dir(AUTH_DOCROOT . "avater/" . $file)) {
            return "/theme/" . self::config('theme') . "/images/small_default.png";
            ;
        }

        return $img;
    }

    public function get_session($session)
    {
        if (!isset($_SESSION[$session])) {
            return false;
        }

        return $_SESSION[$session];
    }


    public function put_session($name, $session)
    {
        $_SESSION[$name] = $session;
    }

    public function destroy_session($session)
    {
        unset($_SESSION[$session]);
    }


    public function log_me()
    {
        $Logger = new Logger(array('path' => AUTH_DOCROOT . '/log/'));
        $Logger->enable_exception();

        if (self::config('log_error')) {
            $Logger->enable_error();
            $Logger->enable_display_error(self::config('display_error'));
            $Logger->enable_fatal();
            $Logger->enable_method_file(true);
        } else {
            $Logger->enable_display_error(self::config('display_error'));
        }

        return $Logger;
    }

    public function numbers_only($_input)
    {
        return preg_replace('/[^0-9.]*/', '', $_input);
    }
    public function byteconvert($bytes)
    {
        if ($bytes < 1) {
            return "0 B";
        }

        $symbol = array(
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB');
        $exp = floor(log($bytes) / log(1024));
        return sprintf('%.2f ' . $symbol[$exp], ($bytes / pow(1024, floor($exp))));
    }
    public function RandomString($type, $length = 10)
    {

        switch ($type) {
            case "1":
                $characters = '0123456789';
                break;

            case "2":
                $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
                break;

            case "3":
                $characters = 'abcdefghjklmnpqrstuvwxyz';
                break;

            case "4":
                $characters = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
                break;

            case "5":
                $characters = '0123456789abcdefghjklmnpqrstuvwxyz';
                break;
        }

        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    private function footer()
    {


        if (file_exists(self::config("theme_folder") . self::config("theme") .
            "/footer.php")) {
            $file = self::config("theme_folder") . self::config("theme") . "/footer.php";


            $opts = array('http' => array('method' => "GET", 'header' =>
                        "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));

            ob_start();
            include ($file);
            $output = ob_get_contents();
            ob_end_clean();

            return $output;
        } else {
            return "";
        }
    }


    private function body($call = 1)
    {
        $request = "";
        if (isset($_REQUEST['u'])) {
            $request = self::safe_html($_REQUEST['u']);
        } else {
            $request = "home";
        }

        $file = self::config("theme_folder") . self::config("theme") . "/pages/" . $request .
            ".php";

        if ($call == 2) {
            $file = self::config("theme_folder") . self::config("theme") . "/small/" . $request .
                ".php";
        }



            if (file_exists(self::config("theme_folder") . self::config("theme") . "/pages/" .
                $request . ".php")) {
                $file = self::config("theme_folder") . self::config("theme") . "/pages/" . $request .
                    ".php";


            } else {
                
            

                $row = self::db_query("SELECT username FROM rechargepro_account WHERE username = ? LIMIT 1",
                    array($request));
                if (empty($row[0]['username'])) {
                    $file = "engine/errorpages/404.php";
                } else {
                    $file = self::config("theme_folder") . self::config("theme") .
                        "/pages/profile.php";
                }

            }

       

        $opts = array('http' => array('method' => "GET", 'header' =>
                    "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));

        ob_start();
        include ($file);
        $output = ob_get_contents();
        ob_end_clean();

        return $output;


    }


    function site_menu()
    {


        $menukeys = array();
        $menukeys[0] = array(
            "pluginkey" => "index",
            "name" => "Home",
            "icon" => "fa-gamepad");
        $menukeys[1] = array(
            "pluginkey" => "utility",
            "name" => "Electricity",
            "icon" => "fa-gears");
        $menukeys[2] = array(
            "pluginkey" => "airtime",
            "name" => "Airtime/Data",
            "icon" => "fa-gears");
        $menukeys[3] = array(
            "pluginkey" => "tv",
            "name" => "TV",
            "icon" => "fa-gears");
        $menukeys[4] = array(
            "pluginkey" => "lottery",
            "name" => "lottery",
            "icon" => "fa-gears");
        $menukeys[5] = array(
            "pluginkey" => "bills",
            "name" => "Pay Bills",
            "icon" => "money-bill-alt");
        //      $menukeys[6] = array(
        //          "pluginkey" => "bulk",
        //            "name" => "Bulk Service",
        //            "icon" => "fa-gears");


        $menu = "";
        if (count($menukeys) > 0) {

            $menu .= '
    <ul class="glossymenu">';
            $u = "index";
            if (isset($_REQUEST['u'])) {
                $u = self::safe_html($_REQUEST['u']);
            }


            for ($i = 0; $i < count($menukeys); $i++) {
                $icon = $menukeys[$i]['icon'];

                if ($u == $menukeys[$i]['pluginkey']) {
                    $this->current_menu = $menukeys[$i];
                    $menu .= '<li class="current"><a style="cursor: pointer;" href="' . $menukeys[$i]['pluginkey'] .
                        '" ><b>' . $menukeys[$i]['name'] . '</b></a></li>';


                } else {
                    $menu .= '<li ><a style="cursor: pointer;" href="' . $menukeys[$i]['pluginkey'] .
                        '"><b>' . $menukeys[$i]['name'] . '</b></a></li>';
                }
            }
            $menu .= '</ul>  
';
        }
        return $menu;


    }

    function theme_parameter($index = "index", $call = 1)
    {
        //$opts = array('http' => array('method' => "GET", 'header' => "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));
        //ob_start();
        //include (AUTH_DOCROOT . "/" . self::config("theme_folder") . self::config("theme") . "/pages/sidebar/right.php");
        //$right = ob_get_contents();
        //ob_end_clean();

        $breadcrumb = "";
        if (isset($_REQUEST['p'])) {

            $breadcrumb = self::safe_html($_REQUEST['p']);
            if ($_REQUEST['p'] == "index" || empty($_REQUEST['p'])) {
                $breadcrumb = self::safe_html($_REQUEST['u']);
            }

        } else {
            if (!isset($_REQUEST['u']) || empty($breadcrumb)) {
                $breadcrumb = "dashboard";
            } else {
                $breadcrumb = self::safe_html($_REQUEST['u']);
            }
        }


        if (!isset($this->current_menu['name'])) {
            $this->current_menu['name'] = "";
            $this->current_menu['icon'] = "";
        }

        $ballance = "0";
        $profit_bal = 0;
        $namelim = 20;
        
        

        $account = '<span class="whitelink"><span style=" margin-top:20px;"><a href="/signin">Login</a> | <a href="/register">Sign Up</span></a></span>';

        if (self::get_session("rechargeproid")) {
            $row = self::db_query("SELECT ac_ballance,profit_bal,profile_creator FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array(self::get_session("rechargeproid")));
            $ballance = $row[0]['ac_ballance'];
            $profit_bal = $row[0]['profit_bal'];
            
         if(in_array($row[0]['profile_creator'],array("115"))){
            $rowb = self::db_query("SELECT ac_ballance,profit_bal,profile_creator FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
            array($row[0]['profile_creator']));
            $ballance = $rowb[0]['ac_ballance'];
            $profit_bal = $rowb[0]['profit_bal'];  
        }

            $account = '<span class="whitelink"><span style=" margin-top:20px;"><a href="/setting"><span>' . substr(self::get_session("name"), 0, $namelim) . '</span> | </a><a href="/logout"><span style="color:red;" class="fas fa-sign-out-alt"></span> <span style="color:red;" id="logout">Logout</span></a></span> ';
        }


        return array(
            "{ACCOUNT}" => $account,
            "{BALANCE}"=>$ballance,
            "{PROFIT}"=>$profit_bal,
            "{BREAD_CRUMB}" => $breadcrumb,
            "{BODY}" => self::body($call),
            "{FOOTER}" => self::footer(),
            "{MAIN_MENU}" => self::site_menu(),
            "{PLUGIN_NAME}" => $this->current_menu['name'],
            "{PLUGIN_IMAGE_CLASS}" => $this->current_menu['icon'],
            "{SITE_LOCATION}" => self::config("theme_folder") . self::config("theme"));


    }

    function account_menu($page)
    {

        $ac = "";
        $acl = "";
        if (self::get_session("rechargeprorole")) {

            $acl = '<li id="transactionlog" style="cursor: pointer;" >
<a href="transactionlog">
<span class="fas fa-table" style="font-size: 220%;"></span>
<div>Transaction Log</div>
</a></li> 


<li id="setting" style="cursor: pointer;" >
<a href="setting">
<span class="fas fa-cogs" style="font-size: 220%;"></span>
<div>Setting</div>
</a></li>';

            if (self::get_session("rechargeprorole") < 3) {
                $ac = '<li id="account" style="cursor: pointer;" >
<a href="account">
<span class="fas fa-user-circle" style="font-size: 220%;"></span>
<div>Account</div>
</a></li> ';
            }


        }


        $breadcrumb = "";
        if (isset($_REQUEST['p'])) {

            $breadcrumb = self::safe_html($_REQUEST['p']);
            if ($_REQUEST['p'] == "index" || empty($_REQUEST['p'])) {
                $breadcrumb = self::safe_html($_REQUEST['u']);
            }

        } else {
            if (!isset($_REQUEST['u']) || empty($breadcrumb)) {
                $breadcrumb = "dashboard";
            } else {
                $breadcrumb = self::safe_html($_REQUEST['u']);
            }
        }


        return '
<div id="menub" class="whitelink" style="overflow:hidden;">
<style type="text/css">
#menub ul{margin:0px; padding:0px;list-style: none; list-style-type: none;}
#menub li {float:left; text-align:center; padding:0px 15px;}
#menub li div{padding-bottom:5px;}
#menub li a:hover {color:#F94925;}
#menub li:hover { background:url(theme/classic/images/menu.png) bottom no-repeat; background-size: 100% 4px; color:#F94925;}
#menub .activen { background:url(theme/classic/images/menu.png) bottom no-repeat; background-size: 100% 4px;}
</style>

<ul>








' . $ac . '

' . $acl . '

<li style="cursor: pointer;" id="documentation"><a href="documentation">
<span class="fas fa-file-alt" style="font-size: 220%;"></span>
<div>API Documentation</div>
</a></li>
</ul>


<div class="whitelink" style="float: right; text-align:center; padding:0px 15px;">
<div style="cursor:pointer; float: left; border: solid 1px white; padding:5px; margin-right:5px;" name="theme/classic/pages/profile/deposit.php?width=400" class="radious5 tunnel"><span class="fas fa-cogs" style="font-size: 120%;"></span> Deposit</div>
<div style="cursor:pointer; float: left; border: solid 1px white; padding:5px;" name="theme/classic/pages/profile/transfer.php?width=450" class="radious5 tunnel"><span class="fas fa-cogs" style="font-size: 120%;"></span> Transfer</div>
</div>


<script type="text/javascript">
jQuery(document).ready(function($){
jQuery(".activen").removeClass();
jQuery("#' . $breadcrumb . '").addClass("activen");
jQuery(".breadcrumb").html("' . $breadcrumb . '");
})
</script>
</div>';





    }

    function limit_text($message, $start, $length, $max = 1000)
    {
        return substr($message, $start, $length);
    }

    public static function language($key, $default = null)
    {
        static $config;

        if ($config === null) {
            $config = include AUTH_DOCROOT . 'config/config.php';

        }

        return (isset($config[$key])) ? $config[$key] : $default;
    }

    public static function config($key, $default = null)
    {
        static $config;


        if ($config === null) {
            $config = include AUTH_DOCROOT . 'config/config.php';

        }

        return (isset($config[$key])) ? $config[$key] : $default;
    }


    public function getRealIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
            {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        //to check ip is pass from proxy
            {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }


    public function safe_html($dirty_html)
    {
        //$config = HTMLPurifier_Config::createDefault();
        //$purifier = new HTMLPurifier($config);
        //$clean_html = $purifier->purify($dirty_html);
        $clean_html = htmlentities($dirty_html);
        return $clean_html;
    }


    public function accessProtected($obj, $prop)
    {
        $reflection = new ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }


    public static function db2()
    {

        // Singleton PDO instance
        static $pdo;

        if ($pdo !== null)
            return $pdo;

        // Connect to database
        // Note about UTF-8: http://www.php.net/manual/en/pdo.construct.php#96325
        $pdo = new PDO(self::config('database_dsn2'), self::config('database_user2'),
            self::config('database_pass2'), array(PDO::MYSQL_ATTR_INIT_COMMAND =>
                "SET NAMES utf8", PDO::MYSQL_ATTR_LOCAL_INFILE => true));
        // http://php.net/manual/en/pdo.error-handling.php
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);

        //$pdo->exec("SET CHARACTER SET utf8");

        return $pdo;
    }
    public static function db()
    {

        // Singleton PDO instance
        static $pdo;

        if ($pdo !== null)
            return $pdo;

        // Connect to database
        // Note about UTF-8: http://www.php.net/manual/en/pdo.construct.php#96325
        $pdo = new PDO(self::config('database_dsn'), self::config('database_user'), self::
            config('database_pass'), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        // http://php.net/manual/en/pdo.error-handling.php
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
        //$pdo->exec("SET CHARACTER SET utf8");

        return $pdo;
    }


    function file_curl_post_json($data, $url, $include = false)
    {
        $data_string = json_encode($data);
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'cURL Request',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json', 'Content-Length: ' .
                    strlen($data_string))));
        // Send the request & save response to $resp
        $result = curl_exec($curl);


        // Close request to clear up some resources
        curl_close($curl);
        return $result;
        return $responseData = json_decode($result, true);
    }


    function file_get_b($data, $url, $include = false)
    {
        $options = array('http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                ), "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                ));
        $context = stream_context_create($options);
        $result = file_get_contents($url, $include, $context);


        return $responseData = json_decode($result, true);
    }
    
    


    function file_get_c($data, $url, $include = false)
    {
        $options = array('http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                ), "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                ));
        $context = stream_context_create($options);
        $result = file_get_contents($url, $include, $context);


        return $result;
    }

    function file_curl_post($data, $url, $include = false)
    {

        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT =>
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data));
        // Send the request & save response to $resp
        $result = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        return $responseData = json_decode($result, true);
    }


    public function db_query2($querry, $array, $bool = false) //query/array/count
    {
        $CONN = self::db2();
        $result = $CONN->prepare($querry);
        $result->execute($array);

        //DROP
        //case insensitive

        if ($bool == true) {
            return $result->rowCount();
        }

        if (preg_match("/INSERT INTO/", $querry, $matches)) {
            return $CONN->lastInsertId();
        }

        if (preg_match("/SELECT/", $querry, $matches)) {
            return self::db_select($result, $CONN);
        }


        return $result;
    }

    public function db_query($querry, $array, $bool = false) //query/array/count
    {
        $CONN = self::db();
        $result = $CONN->prepare($querry);
        $result->execute($array);

        if ($bool == true) {
            return $result->rowCount();
        }

        if (preg_match("/INSERT INTO/", $querry, $matches)) {
            return $CONN->lastInsertId();
        }

        if (preg_match("/SELECT/", $querry, $matches)) {
            return self::db_select($result, $CONN);
        }

        return $result;
    }


    private function db_select($result, $CONN)
    {
        $arrayrow = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $tmparray = array();
            foreach ($row as $key => $value) {
                if (self::config("filter_sql")) {
                    $tmparray[$key] = $value;
                } else {
                    $tmparray[$key] = $value;
                }
            }
            $arrayrow[] = $tmparray;
        }

        if (count($arrayrow) == 0) {
            //set array as empty
            for ($i = 0; $i < $result->columnCount(); $i++) {
                $columename = $result->getColumnMeta($i);
                $newarrayrow[0][$columename['name']] = null;
            }
            // $newarrayrow = array();
            return $newarrayrow;
        }
        return $arrayrow;
    }

    public function myfunction($products, $field, $value)
    {
        foreach ($products as $key => $product) {
            if ($product[$field] === $value)
                return $key;
        }
        return false;
    }

    public function array_count($array)
    {
        if (!isset($array[0])) {
            return 0;
        }
        $empty = array_filter($array[0]);
        if (empty($empty)) {
            return 0;
        }


        return count($array);
    }

    public function getExtension($str)
    {
        $boss = explode(".", strtolower($str));
        return end($boss);
    }


}




?>