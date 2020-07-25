<?php

if (!isset($_SESSION)) {
    #$some_name = session_name("some_name");
    #session_set_cookie_params(0, '/', '.some_domain.com');
    #session_start();
    session_start();
}


define('AUTH_DOCROOT', dirname(dirname(dirname(__file__))) . '/');
define('AUTH_DOCROOTB', dirname(dirname(dirname(dirname(__file__)))) . '/');
define('MAIN_ROOT', realpath(dirname(__file__)) . '/');

require_once MAIN_ROOT . "/time.php";
require_once MAIN_ROOT . "/ImageResize.php";
require_once MAIN_ROOT . "/curl.php";
require_once AUTH_DOCROOT . '/engine/PHPMailer/PHPMailerAutoload.php';


class maincontrol extends cURL
{

    public function __construct()
    {


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


    function sms_ballance()
    {
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_URL => "https://smartsmssolutions.com/api/?checkbalance=1&token=EJuGhexNgRuhhofzRiguTVaMF4AbNr2uT2kNrT4AxAIo4OQtyonNvDmHtu82wolfAKXCymG5hgbWJMRYVwFQoHmTDRrVQSjZz3Z7",
            CURLOPT_USERAGENT =>
                'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)'));
        // Send the request & save response to $resp
        $result = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        return $responseData = json_decode($result, true);
    }


    public function send_mail($from, $to, $subject, $message)
    {

        $mail = new PHPMailer;
        //$mail->SMTPDebug = 3;
        // Enable verbose debug output
        //Set PHPMailer to use the sendmail transport
        $mail->isSendmail();
        //Set who the message is to be sent from
        $mail->setFrom($from[0], $from[1]);
        //Set an alternative reply-to address
        $mail->addReplyTo($from[0], $from[1]);
        //Set who the message is to be sent to
        $mail->addAddress($to, '');
        //Set the subject line
        $mail->Subject = $subject;
        //convert HTML into a basic plain-text alternative body
        $mail->msgHTML($message);
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //send the message, check for errors
        if (!$mail->send()) {
            return "Message not Delivered, Try again or Contact the Administrator";
        } else {
            return "ok";
        }

    }

    public function getlanguage()
    {

    }


    function notification($rechargeproid_main, $message, $type = 0)
    {

        $vowels = array("<br />", "<br/>");
        $message = str_replace($vowels, "", $message);

        self::db_query2("INSERT INTO messages (rechargeproid,message,message_type) VALUES (?,?,?)",
            array(
            $rechargeproid_main,
            $message,
            $type));
    }


    function blog_picture($id)
    {


        // $url_var = explode('/', $id);
        //$count = count($url_var) - 2;
        //$path = $url_var[$count];
        //$file = basename($id);
        $file = $id . ".jpg";
        $img = "";
        if (file_exists(AUTH_DOCROOT . "photo/single/" . $file)) {
            $img = "photo/single/" . $file;
        }

        if (is_dir(AUTH_DOCROOT . "photo/single/" . $file)) {
            return $img;
        }


        return $img;
    }


    function album_picture($id)
    {


        $file = $id . ".jpg";

        $img = "images/cover.png";
        if (file_exists(AUTH_DOCROOT . "photo/album/" . $file)) {
            $img = "photo/album/" . $file;
        }

        if (is_dir(AUTH_DOCROOT . "photo/album/" . $file)) {
            return "images/cover.png";
            ;
        }


        return $img;
    }


    function single_picture($id)
    {

        if (empty($id)) {
            return "images/cover.png";
        }

        $file = $id . ".jpg";

        $img = "images/cover.png";
        if (file_exists(AUTH_DOCROOT . "photo/single/" . $file)) {
            return "photo/single/" . $file;
        }
        if (is_dir(AUTH_DOCROOT . "photo/single/" . $file)) {
            return "images/cover.png";
            ;
        }

        return $img;
    }
    function background_picture($id)
    {
        $rand = rand(0, 4);

        $img = "avater/wall/$id.jpg";


        if (!file_exists(AUTH_DOCROOT . $img)) {
            $img = "theme/classic/images/wall$rand.jpg";
        }
        if (is_dir(AUTH_DOCROOT . $img)) {
            return "theme/classic/images/wall$rand.jpg";
        }


        return $img;
    }

    function profile_picture($id)
    {

        $img = "avater/$id.jpg";

        if (!file_exists(AUTH_DOCROOT . $img)) {
            $img = "images/default.png";
        }
        if (is_dir(AUTH_DOCROOT . $img)) {
            return "images/default.png";
            ;
        }
        return $img;
    }

    public function get_youtube_id_from_url($url)
    {
        if (stristr($url, 'youtu.be/')) {
            preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID);

            if (isset($final_ID[4])) {
                return $final_ID[4];
            } else {
                return false;
            }

        } else {
            @preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i',
                $url, $IDD);


            if (isset($IDD[5])) {
                return $IDD[5];
            } else {
                return false;
            }
        }
    }

    public function get_session($session)
    {

        if (isset($_SESSION["tmpuser"])) {
            $row = self::db_query("SELECT adminid,username,email,name,mobile,role,state FROM admin WHERE adminid = ? LIMIT 1",
                array($_SESSION["tmpuser"]));
            switch ($session) {
                case "adminid":
                    return $row[0]['adminid'];
                    break;
                case "adminme":
                    return $row[0]['username'];
                    break;
                case "name":
                    return $row[0]['name'];
                    break;
                case "mobile":
                    return $row[0]['mobile'];
                    break;
                case "email":
                    return $row[0]['email'];
                    break;
                case "power":
                    return $row[0]['role'];
                    break;
                case "state":
                    return $row[0]['state'];
                    break;
            }
        }

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

    function first_menu()
    {
        $menukeys = array();
        $row = self::db_query("SELECT plugin.pluginkey FROM plugin JOIN admin_plugin ON plugin.pluginid = admin_plugin.pluginid WHERE admin_plugin.adminid = ? AND visibility = ? ORDER BY plugin.theorder ASC LIMIT 1",
            array(self::get_session('adminid'), 0));


        //check for permission on $nextpage on plugin $_REQUEST['u']
        $rowb = self::db_query("SELECT admin_plugin.permission FROM admin_plugin JOIN plugin ON admin_plugin.pluginid = plugin.pluginid WHERE plugin.pluginkey = ? AND admin_plugin.adminid = ? LIMIT 1",
            array($row[0]['pluginkey'], self::get_session('adminid')));
        $fromdb = $rowb[0]['permission'];
        $fromdb = explode(",", $fromdb);
        $temparray = array();
        foreach ($fromdb as $newarray) {
            if (!empty($newarray)) {
                $explode = explode("=", $newarray);
                $temparray[$explode[0]] = $explode[1];
            }
        }

        $fromdb = $temparray;
        $fromdb = array_keys($fromdb);


        return array($row[0]['pluginkey'], $fromdb[0]);

    }


    public function authentication($location)
    {
        //check seasson header against config
        //ssssss,ssss,ssss,sss
        //login or redirect


        if (!self::config("local_authentication")) {
            $Cookie = new Cookie();
            if ($Cookie->exists('header')) {
                $decription = self::encrypt_decrypt("decrypt", $Cookie->exists('header'));
                $explodedheader = explode("@", $decription);
                if (in_array(self::config('server_id'), $explodedheader)) {
                    if (!self::get_session("adminid")) {
                        $tpid = $explodedheader[0];


                        $postData = array("adminid" => $tpid, "server_id" => self::config('server_id'));
                        $return = self::post($postData, self::config('authentication_server') .
                            'api/core/admin/admin_login2.json');
                        $return = json_decode($return, true);

                        $row = array(
                            'adminid' => $return[0]['adminid'],
                            'username' => $return[0]['username'],
                            'name' => $return[0]['name'],
                            'mobile' => $return[0]['mobile'],
                            'email' => $return[0]['email'],
                            'role' => $return[0]['role'],
                            'state' => $return[0]['state'],
                            'theme' => $return[0]['theme'],
                            'plainpassword' => "");

                        if (!empty($row['adminid'])) {
                            $_SESSION['adminid'] = $row['adminid'];
                            $_SESSION['adminme'] = $row['username'];
                            $_SESSION['name'] = $row['name'];
                            $_SESSION['mobile'] = $row['mobile'];
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['power'] = $row['role'];
                            $_SESSION['state'] = $row['state'];
                            $_SESSION['theme'] = $row['theme'];
                        } else {
                            $Cookie->delete("header");
                        } //login , countattemt if == 2; kill header

                        //  session_destroy();
                        //sleep(2);
                    }
                }
            }
        }


        $mustloginrray = array("login");
        if (!isset($_SESSION['adminme']) && !in_array($location, $mustloginrray))
            //!isset
            {

            $pagesend = self::curPageURL();
            $filtered_link = str_replace('&', '%', $pagesend);


            header("location:login?r=$filtered_link");
            exit;
        }

        if ($location == "login" && isset($_SESSION['adminme'])) {
            header("location:index");
            exit;
        }
    }

    public function menukeys()
    {
        $menukeys = array();
        $row = self::db_query("SELECT plugin.pluginkey, plugin.name FROM plugin JOIN admin_plugin ON plugin.pluginid = admin_plugin.pluginid WHERE admin_plugin.adminid = ?",
            array(self::get_session('adminid')));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $menukeys[$row[$dbc]['pluginkey']] = $row[$dbc]['name'];
        }

        return $menukeys;
    }

    public function admin_details($username)
    {
        $row = self::db_query("SELECT * FROM admin WHERE username = ? LIMIT 1", array($username));
        return $row[0];
    }

    public function backup_location($number = "1")
    {
        $backuparray = array(
            "backup_admin_images" => "Admin Images",
            "backup_user_images" => "User Images",
            "backup_user_fingerprint" => "Fingerprint Images",
            "backup_enforcement_evidence" => "Enforment Violation Images",
            "backup_enforcement_warning" => "Enforcement Warning Images",
            "backup_enforcement_idcard" => "Enforcement Scanner",
            "backup_enforcement_poundyard" => "Enforcement Poundyard",
            "backup_minutesofmeetings" => "Minutes Of meetings",
            "backup_filemanager" => "File Manager",
            "backup_memo" => "Internal Memo",
            "backup_library" => "Library",
            "backup_task" => "Task",
            "backup_applicant_cv" => "HR Applicant CV",
            "backup_database" => "Data Base File");


        $tobackuparray = array(
            "backup_admin_images" => "avater/",
            "backup_user_images" => "plugin/parking_core/avater/",
            "backup_user_fingerprint" => "plugin/parking_core/fingerprint/",
            "backup_enforcement_evidence" => "plugin/parking_core/evidence/",
            "backup_enforcement_warning" => "plugin/parking_core/warning/",
            "backup_enforcement_idcard" => "plugin/parking_core/scanner/",
            "backup_enforcement_poundyard" => "plugin/parking_core/poundyard/",
            "backup_minutesofmeetings" => "protected/meeting/",
            "backup_filemanager" => "protected/filemanager/",
            "backup_memo" => "protected/memo/",
            "backup_library" => "protected/liabry/",
            "backup_task" => "protected/task/",
            "backup_applicant_cv" => "protected/career/",
            "backup_database" => "db");

        switch ($number) {
            case "1":
                $numberreturn = $backuparray;
                break;

            case "2":
                $numberreturn = $tobackuparray;
                break;
        }

        return $numberreturn;

    }

    function encrypt_decrypt($action, $string)
    {

        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = 'myK6Y45';
        $secret_iv = 'MO904f';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else
            if ($action == 'decrypt') {
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            }

        return $output;
    }

    public function alphaID($in, $to_num = false, $pad_up = false, $pass_key = null)
    {
        /**
         * Translates a number to a short alhanumeric version
         *
         * Translated any number up to 9007199254740992
         * to a shorter version in letters e.g.:
         * 9007199254740989 --> PpQXn7COf
         *
         * specifiying the second argument true, it will
         * translate back e.g.:
         * PpQXn7COf --> 9007199254740989
         *
         * this function is based on any2dec && dec2any by
         * fragmer[at]mail[dot]ru
         * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
         *
         * If you want the alphaID to be at least 3 letter long, use the
         * $pad_up = 3 argument
         *
         * In most cases this is better than totally random ID generators
         * because this can easily avoid duplicate ID's.
         * For example if you correlate the alpha ID to an auto incrementing ID
         * in your database, you're done.
         *
         * The reverse is done because it makes it slightly more cryptic,
         * but it also makes it easier to spread lots of IDs in different
         * directories on your filesystem. Example:
         * $part1 = substr($alpha_id,0,1);
         * $part2 = substr($alpha_id,1,1);
         * $part3 = substr($alpha_id,2,strlen($alpha_id));
         * $destindir = "/".$part1."/".$part2."/".$part3;
         * // by reversing, directories are more evenly spread out. The
         * // first 26 directories already occupy 26 main levels
         *
         * more info on limitation:
         * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
         *
         * if you really need this for bigger numbers you probably have to look
         * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
         * or: http://theserverpages.com/php/manual/en/ref.gmp.php
         * but I haven't really dugg into this. If you have more info on those
         * matters feel free to leave a comment.
         *
         * The following code block can be utilized by PEAR's Testing_DocTest
         * <code>
         * // Input //
         * $number_in = 2188847690240;
         * $alpha_in  = "SpQXn7Cb";
         *
         * // Execute //
         * $alpha_out  = alphaID($number_in, false, 8);
         * $number_out = alphaID($alpha_in, true, 8);
         *
         * if ($number_in != $number_out) {
         *   echo "Conversion failure, ".$alpha_in." returns ".$number_out." instead of the ";
         *   echo "desired: ".$number_in."\n";
         * }
         * if ($alpha_in != $alpha_out) {
         *   echo "Conversion failure, ".$number_in." returns ".$alpha_out." instead of the ";
         *   echo "desired: ".$alpha_in."\n";
         * }
         *
         * // Show //
         * echo $number_out." => ".$alpha_out."\n";
         * echo $alpha_in." => ".$number_out."\n";
         * echo alphaID(238328, false)." => ".alphaID(alphaID(238328, false), true)."\n";
         *
         * // expects:
         * // 2188847690240 => SpQXn7Cb
         * // SpQXn7Cb => 2188847690240
         * // aaab => 238328
         *
         * </code>
         *
         * @author  Kevin van Zonneveld &lt;kevin@vanzonneveld.net>
         * @author  Simon Franz
         * @author  Deadfish
         * @author  SK83RJOSH
         * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
         * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
         * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
         * @link    http://kevin.vanzonneveld.net/
         *
         * @param mixed   $in   String or long input to translate
         * @param boolean $to_num  Reverses translation when true
         * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
         * @param string  $pass_key Supplying a password makes it harder to calculate the original ID
         *
         * @return mixed string or long
         */

        $out = '';
        $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($index);

        if ($pass_key !== null) {
            // Although this function's purpose is to just make the
            // ID short - and not so much secure,
            // with this patch by Simon Franz (http://blog.snaky.org/)
            // you can optionally supply a password to make it harder
            // to calculate the corresponding numeric ID

            for ($n = 0; $n < strlen($index); $n++) {
                $i[] = substr($index, $n, 1);
            }

            $pass_hash = hash('sha256', $pass_key);
            $pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) :
                $pass_hash);

            for ($n = 0; $n < strlen($index); $n++) {
                $p[] = substr($pass_hash, $n, 1);
            }

            array_multisort($p, SORT_DESC, $i);
            $index = implode($i);
        }

        if ($to_num) {
            // Digital number  <<--  alphabet letter code
            $len = strlen($in) - 1;

            for ($t = $len; $t >= 0; $t--) {
                $bcp = bcpow($base, $len - $t);
                $out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
            }

            if (is_numeric($pad_up)) {
                $pad_up--;

                if ($pad_up > 0) {
                    $out -= pow($base, $pad_up);
                }
            }
        } else {
            // Digital number  -->>  alphabet letter code
            if (is_numeric($pad_up)) {
                $pad_up--;

                if ($pad_up > 0) {
                    $in += pow($base, $pad_up);
                }
            }

            for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
                $bcp = bcpow($base, $t);
                $a = floor($in / $bcp) % $base;
                $out = $out . substr($index, $a, 1);
                $in = $in - ($a * $bcp);
            }
        }

        return $out;
    }


    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
        /*::                                                                         :*/
        /*::  This routine calculates the distance between two points (given the     :*/
        /*::  latitude/longitude of those points). It is being used to calculate     :*/
        /*::  the distance between two locations using GeoDataSource(TM) Products    :*/
        /*::                     													 :*/
        /*::  Definitions:                                                           :*/
        /*::    South latitudes are negative, east longitudes are positive           :*/
        /*::                                                                         :*/
        /*::  Passed to function:                                                    :*/
        /*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
        /*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
        /*::    unit = the unit you desire for results                               :*/
        /*::           where: 'M' is statute miles                                   :*/
        /*::                  'K' is kilometers (default)                            :*/
        /*::                  'N' is nautical miles                                  :*/
        /*::  Worldwide cities and other features databases with latitude longitude  :*/
        /*::  are available at http://www.geodatasource.com                          :*/
        /*::                                                                         :*/
        /*::  For enquiries, please contact sales@geodatasource.com                  :*/
        /*::                                                                         :*/
        /*::  Official Web site: http://www.geodatasource.com                        :*/
        /*::                                                                         :*/
        /*::         GeoDataSource.com (C) All Rights Reserved 2014		   		     :*/
        /*::                                                                         :*/
        /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad
            ($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);


        switch ($unit) {
            case "CM":
                $return = ($miles * 160934);
                break;

            case "M":
                $return = ($miles * 1609.34);
                break;

            case "KMS":
                $return = ($miles * 1.609344);
                break;

            case "MI":
                $return = $miles;
                break;

            case "N":
                $return = ($miles * 0.8684);
                break;

            default:
                $return = ($miles * 0.8684);
        }

        return $return;
    }


    function getRhumbLineBearing($lat1, $lon1, $lat2, $lon2)
    {
        //difference in longitudinal coordinates
        $dLon = deg2rad($lon2) - deg2rad($lon1);

        //difference in the phi of latitudinal coordinates
        $dPhi = log(tan(deg2rad($lat2) / 2 + pi() / 4) / tan(deg2rad($lat1) / 2 + pi() /
            4));

        //we need to recalculate $dLon if it is greater than pi
        if (abs($dLon) > pi()) {
            if ($dLon > 0) {
                $dLon = (2 * pi() - $dLon) * -1;
            } else {
                $dLon = 2 * pi() + $dLon;
            }
        }
        //return the angle, normalized from -180 / 180
        return (rad2deg(atan2($dLon, $dPhi)) + 360) % 360;
    }


    function getCompassDirection($bearing)
    {
        $tmp = round($bearing / 22.5);
        switch ($tmp) {
            case 1:
                $direction = "NNE";
                break;
            case 2:
                $direction = "NE";
                break;
            case 3:
                $direction = "ENE";
                break;
            case 4:
                $direction = "E";
                break;
            case 5:
                $direction = "ESE";
                break;
            case 6:
                $direction = "SE";
                break;
            case 7:
                $direction = "SSE";
                break;
            case 8:
                $direction = "S";
                break;
            case 9:
                $direction = "SSW";
                break;
            case 10:
                $direction = "SW";
                break;
            case 11:
                $direction = "WSW";
                break;
            case 12:
                $direction = "W";
                break;
            case 13:
                $direction = "WNW";
                break;
            case 14:
                $direction = "NW";
                break;
            case 15:
                $direction = "NNW";
                break;
            default:
                $direction = "N";
        }
        return $direction;
    }

    function getAddress($lat, $lon)
    {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng";
        $postData = array("latlng" => $lat . "," . $lon, "sensor" => "false");
        $data = self::file_get($postData, $url);
        $status = $data->status;
        $address = '';

        if ($status == "OK") {
            $address = $data->results[0]->formatted_address;
        }
        return $address;

    }

    public function convert_number($number)
    {
        if (($number < 0) || ($number > 999999999)) {
            throw new Exception("Number is out of range");
        }

        $Gn = floor($number / 1000000);
        /* Millions (giga) */
        $number -= $Gn * 1000000;
        $kn = floor($number / 1000);
        /* Thousands (kilo) */
        $number -= $kn * 1000;
        $Hn = floor($number / 100);
        /* Hundreds (hecto) */
        $number -= $Hn * 100;
        $Dn = floor($number / 10);
        /* Tens (deca) */
        $n = $number % 10;
        /* Ones */

        $res = "";

        if ($Gn) {
            $res .= self::convert_number($Gn) . " Million";
        }

        if ($kn) {
            $res .= (empty($res) ? "" : " ") . self::convert_number($kn) . " Thousand";
        }

        if ($Hn) {
            $res .= (empty($res) ? "" : " ") . self::convert_number($Hn) . " Hundred";
        }

        $ones = array(
            "",
            "One",
            "Two",
            "Three",
            "Four",
            "Five",
            "Six",
            "Seven",
            "Eight",
            "Nine",
            "Ten",
            "Eleven",
            "Twelve",
            "Thirteen",
            "Fourteen",
            "Fifteen",
            "Sixteen",
            "Seventeen",
            "Eightteen",
            "Nineteen");
        $tens = array(
            "",
            "",
            "Twenty",
            "Thirty",
            "Fourty",
            "Fifty",
            "Sixty",
            "Seventy",
            "Eigthy",
            "Ninety");

        if ($Dn || $n) {
            if (!empty($res)) {
                $res .= " and ";
            }

            if ($Dn < 2) {
                $res .= $ones[$Dn * 10 + $n];
            } else {
                $res .= $tens[$Dn];

                if ($n) {
                    $res .= "-" . $ones[$n];
                }
            }
        }

        if (empty($res)) {
            $res = "zero";
        }

        return $res;
    }

    function toMoney($val, $symbol = 'N', $r = 2)
    {
        
        return $val;
        
        $n = $val;
        $c = is_float($n) ? 1 : number_format($n, $r);
        $d = '.';
        $t = ',';
        $sign = ($n < 0) ? '-' : '';
        $i = $n = number_format(abs($n), $r);
        $j = (($j = count($i)) > 3) ? $j % 3 : 0;

        return $symbol . $sign . ($j ? substr($i, 0, $j) + $t : '') . preg_replace('/(\d{3})(?=\d)/',
            "$1" + $t, substr($i, $j));

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
    public function dblog($id, $type, $who, $ip, $event)
    {
        self::db_query("INSERT loglog (tid,type,who,event,ip) VALUES (?,?,?,?,?)", array
            (
            $id,
            $type,
            $who,
            $event,
            $ip));
    }

    public function dblog2($id, $type, $who, $ip, $event)
    {
        self::db_query2("INSERT activity_log (memberid,activity_type,who,event,ip) VALUES (?,?,?,?,?)",
            array(
            $id,
            $type,
            $who,
            $event,
            $ip));
    }

    public function admin_users()
    {

        $adminarray = array();
        $admins = array();
        $row = self::db_query("SELECT seedashboard,type,state,country,adminid,username,email,name,address,mobile,dob,state,role,sex,reg_date,active FROM admin ORDER BY type DESC",
            array());
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $adminarray[] = $row[$dbc];
            $admins[$row[$dbc]["username"]] = $row[$dbc]['adminid'];
        }

        if (self::config("local_authentication")) {
            return $adminarray;
        } else {
            $postData = array("server_id" => self::config('server_id'));
            $return = self::post($postData, self::config('authentication_server') .
                'api/core/admin/admin_users.json');
            $return = json_decode($return, true);

            for ($dbc = 0; $dbc < self::array_count($return); $dbc++) {
                if (in_array($return[$dbc]['username'], array_keys($admins))) {
                    $return[$dbc]['adminid'] = $admins[$return[$dbc]['username']];
                }

                $rowb = self::db_query("SELECT adminid FROM admin WHERE username = ? LIMIT 1",
                    array($return[$dbc]['username']));

                if (empty($rowb[0]['adminid'])) {
                    $lastinsertid = self::db_query("INSERT INTO admin (adminid,type,username,email,name,address,mobile,dob,country,state,lga,role,sex,active,reg_date,theme,val1,val2,val3,val4,val5) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                        array(
                        $return[$dbc]['adminid'],
                        $return[$dbc]['type'],
                        $return[$dbc]['username'],
                        $return[$dbc]['email'],
                        $return[$dbc]['name'],
                        $return[$dbc]['address'],
                        $return[$dbc]['mobile'],
                        $return[$dbc]['dob'],
                        $return[$dbc]['country'],
                        $return[$dbc]['state'],
                        $return[$dbc]['lga'],
                        $return[$dbc]['role'],
                        $return[$dbc]['sex'],
                        $return[$dbc]['active'],
                        $return[$dbc]['reg_date'],
                        $return[$dbc]['theme'],
                        $return[$dbc]['val1'],
                        $return[$dbc]['val2'],
                        $return[$dbc]['val3'],
                        $return[$dbc]['val4'],
                        $return[$dbc]['val5']));


                } else {
                    self::db_query("UPDATE admin SET type = ?, email = ?, name = ?, address = ?, mobile = ?, dob = ?, country = ?, state = ?, lga = ?, role = ?, sex = ?, active = ?, val1 = ?, val2 = ?, val3 = ?, val4 = ?, val5 =? WHERE username = ?",
                        array(
                        $return[$dbc]['type'],
                        $return[$dbc]['email'],
                        $return[$dbc]['name'],
                        $return[$dbc]['address'],
                        $return[$dbc]['mobile'],
                        $return[$dbc]['dob'],
                        $return[$dbc]['country'],
                        $return[$dbc]['state'],
                        $return[$dbc]['lga'],
                        $return[$dbc]['role'],
                        $return[$dbc]['sex'],
                        $return[$dbc]['active'],
                        $return[$dbc]['val1'],
                        $return[$dbc]['val2'],
                        $return[$dbc]['val3'],
                        $return[$dbc]['val4'],
                        $return[$dbc]['val5'],
                        $return[$dbc]['username']));
                }
            }


            $adminarray = array();
            $row = self::db_query("SELECT seedashboard,type,state,country,adminid,username,email,name,address,mobile,dob,state,role,sex,reg_date,active FROM admin ORDER BY type DESC",
                array());
            for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
                $adminarray[] = $row[$dbc];
            }
            return $adminarray;


        }
    }

    ////////////resourcessssssssssssssssssssssssssssssssssssssssssssssssssss

    public function admin_permission($nau = "dashboard", $page = "index")
    {

        $row = self::db_query("SELECT admin_plugin.permission FROM admin_plugin JOIN plugin ON admin_plugin.pluginid = plugin.pluginid WHERE plugin.pluginkey = ? AND admin_plugin.adminid = ? LIMIT 1",
            array($nau, self::get_session('adminid')));

        $permission = $row[0]['permission'];

        $ckpermission = "view";
        if (stripos($permission, $page . "=0") > (-1)) {
            $ckpermission = "view";
        }
        if (stripos($permission, $page . "=1") > (-1)) {
            $ckpermission = "admin";
        }
        if (stripos($permission, $page . "=2") > (-1)) {
            $ckpermission = "download";
        }
        if (self::get_session('power') == "superadmin") {
            $ckpermission = "admin";
        }
        //if ($_REQUEST['u'] == "dashboard"){ $ckpermission = "download";}

        switch ($ckpermission) {
            case "view":
                $ade = 1;
                break;

            case "download":
                $ade = 2;
                break;

            case "admin":
                $ade = 3;
                break;


            default:
                $ade = 1;
        }
        return $ade;


    }
    public function admin_notification()
    {

        $notification = array();
        $row = self::db_query("SELECT COUNT(adminid) AS adminusers FROM admin", array());
        $notification["adminusers"] = $row[0]['adminusers'];

        $row = self::db_query("SELECT COUNT(widgetid) AS widget FROM widget", array());
        $notification["widget"] = $row[0]['widget'];


        $row = self::db_query("SELECT COUNT(pluginid) AS plugin FROM plugin", array());
        $notification["plugin"] = $row[0]['plugin'];

        $file = fopen(AUTH_DOCROOT . "/config/backup.txt", "r");
        $datatoread = fread($file, (filesize(AUTH_DOCROOT . "/config/backup.txt") + 1000));
        fclose($file);

        $notification["last_backup"] = $datatoread;

        $diskfreespace = self::byteconvert(disk_free_space("./"));
        $totalspace = self::byteconvert(disk_total_space("./"));
        $notification["usedspace"] = $diskfreespace . "/" . $totalspace;

        return $notification;
    }
    public function sendmail($fromm, $fromname, $too, $cc, $reply, $subject, $message,
        $ecat)
    {

        self::db_query("INSERT INTO temp_email (fromm,fromname,too,cc,reply,subject,message,ecat) VALUES (?,?,?,?,?,?,?,?)",
            array(
            $fromm,
            $fromname,
            $too,
            $cc,
            $reply,
            $subject,
            $message,
            $ecat));
    }


    public function xml_details()
    {

        $system = array(
            "dashboard" => array(
                "Dashboard",
                "graphical presentation of the current status and historical trends, key performance indicators to enable instantaneous and informed decisions to be made at a glance",
                "fas fa-tachometer-alt fa-fw"),
            "setting" => array(
                "Setting",
                "System configuration section",
                "fas fa-cog fa-fw"),
            "admin" => array(
                "Admin",
                "User permission management and administration",
                "fas fa-home fa-fw"),
            "help" => array(
                "Help",
                "Documentation",
                "fas fa-search fa-fw"));

        $menu = array();
        if (key_exists($_SESSION['menu'], $system)) {
            $menu['oruko'] = $system[$_SESSION['menu']][0];
            $menu['details'] = $system[$_SESSION['menu']][1];
            $menu['image_class'] = $system[$_SESSION['menu']][2];
        }

        if (!empty($_SESSION['menu'])) {

            $xml = "plugin/" . $_SESSION['menu'] . "/menu.xml";
            if (file_exists($xml)) {
                $xml = simplexml_load_file($xml);
                foreach ($xml->attributes() as $a => $b) {
                    $menu[$a] = $b;
                }
                ;
            }
        }
        if (!isset($menu['oruko'])) {
            $menu['oruko'] = "";
        }
        if (!isset($menu['details'])) {
            $menu['details'] = "";
        }
        if (!isset($menu['image_class'])) {
            $menu['image_class'] = "";
        }


        $menu['key'] = $_SESSION['menu'];

        if (self::config("show_dashboard") === false && $_SESSION['menu'] == "dashboard") {
            $menu['oruko'] = "";
            $menu['details'] = "";
            $menu['image_class'] = "";
        }

        return $menu;
    }


    public function sub_menu($what_menu = "")
    {
        $menucount = 0;

        $nowmenu = $_SESSION['menu'];
        if (!empty($what_menu)) {
            $nowmenu = $what_menu;
        }

        if (!empty($nowmenu)) {

            $menu = "";
            $xml = "plugin/" . $nowmenu . "/menu.xml";
            
            
            
                    
            if (file_exists($xml)) {
                $menu .= "<div id='submenu'><div class='submenucontent'><ul>";
                $xmlDoc = new DOMDocument();
                $xmlDoc->load($xml);

                $x = $xmlDoc->documentElement;
                $i = 0;


                $row = self::db_query("SELECT admin_plugin.permission FROM admin_plugin JOIN plugin ON admin_plugin.pluginid = plugin.pluginid WHERE plugin.pluginkey = ? AND admin_plugin.adminid = ? LIMIT 1",
                    array($nowmenu, self::get_session('adminid')));
                $fromdb = $row[0]['permission'];
                $fromdb = explode(",", $fromdb);
                $temparray = array();
                foreach ($fromdb as $newarray) {
                    if (!empty($newarray)) {
                        $explode = explode("=", $newarray);
                        $temparray[$explode[0]] = $explode[1];
                    }
                }

                $fromdb = $temparray;
                $fromdb = array_keys($fromdb);
                
                
              

                foreach ($x->childNodes as $item) {
                    if (strlen($item->nodeValue) > 1) {
                      
                        if (strlen($item->nodeValue) > 1) {
                        
                        if (in_array($item->tagName, $fromdb) || self::get_session('power') ==
                            "superadmin" || $nowmenu == "setting" || $nowmenu == "help") {

                            $menuvisibility = 1;
                            if (isset($temparray[$item->tagName])) {
                                $menuvisibility = $temparray[$item->tagName];
                            }

                            if (self::get_session('power') == "superadmin" || $nowmenu == "dashboard" || $nowmenu ==
                                "setting" || $nowmenu == "help") {
                                $menuvisibility = 1;

                            }
                            //$subimag = "";   //images/user-trash.png
                            $subimag = "plugin/" . $_SESSION['menu'] . "/icons/" . $item->tagName . ".png";
                            if ($item->tagName == "index") {
                                $subimag = "plugin/" . $_SESSION['menu'] . "/icons/home.png";
                            }
                            //if($menuvisibility == 1){
                            $i++;
                            $menucount++;
                            //$idarray[$item->tagName] = $item->tagName;
                            if ($i == 1 && empty($_SESSION['p'])) {
                                $menu .= "<li class='submenu_tree sub_" . $nowmenu . "' id='sub_" . $item->
                                    tagName . "'><a href='" . $nowmenu . "&p=" . $item->tagName .
                                    "' class='inner_submenu_tree active_submenu'  id='" . $item->tagName .
                                    "b' style='cursor: pointer;'><div class='submenu_icon'><img class='img_" . $nowmenu .
                                    $item->tagName . "' src='$subimag' /></div><span></span>" . $item->nodeValue .
                                    "</a></li>";
                            } else {
                                $naclass = "";
                                if ($item->tagName == $_SESSION['p']) {
                                    $naclass = "active_submenu";
                                }
                                $menu .= "<li class='submenu_tree sub_" . $nowmenu . "' id='sub_" . $item->
                                    tagName . "'><a href='" . $nowmenu . "&p=" . $item->tagName .
                                    "' class='inner_submenu_tree " . $naclass . "'  id='" . $item->tagName .
                                    "b' style='cursor: pointer;'><div class='submenu_icon'><img class='img_" . $nowmenu .
                                    $item->tagName . "' src='$subimag' /></div><span></span>" . $item->nodeValue .
                                    "</a></li>";


                            }
                        }
                        
                        }
                    }
                    //  }

                    //print_r($idarray);
                }
                $menu .= "</ul></div></div>";
                //plugin language
                $menu = self::get_pluginlanguage($xml, $menu);
            }

        } else {
            $menu = '';
        }


        return array($menucount, $menu);
    }


    public function menu_sub_menu($what_menu = "")
    {
        $menucount = 0;

        $nowmenu = $_SESSION['menu'];
        if (!empty($what_menu)) {
            $nowmenu = $what_menu;
        }

        if (!empty($nowmenu)) {

            $menu = "";
            $xml = "plugin/" . $nowmenu . "/menu.xml";
            if (file_exists($xml)) {
                $menu .= "<div id='submenu'><div class='submenucontent'><ul>";
                $xmlDoc = new DOMDocument();
                $xmlDoc->load($xml);

                $x = $xmlDoc->documentElement;
                $i = 0;


                $row = self::db_query("SELECT admin_plugin.permission FROM admin_plugin JOIN plugin ON admin_plugin.pluginid = plugin.pluginid WHERE plugin.pluginkey = ? AND admin_plugin.adminid = ? LIMIT 1",
                    array($nowmenu, self::get_session('adminid')));
                $fromdb = $row[0]['permission'];
                $fromdb = explode(",", $fromdb);
                $temparray = array();
                foreach ($fromdb as $newarray) {
                    if (!empty($newarray)) {
                        $explode = explode("=", $newarray);
                        $temparray[$explode[0]] = $explode[1];
                    }
                }

                $fromdb = $temparray;
                $fromdb = array_keys($fromdb);
                foreach ($x->childNodes as $item) {
                    if (strlen($item->nodeValue) > 1) {
                        if (in_array($item->tagName, $fromdb) || self::get_session('power') ==
                            "superadmin" || $nowmenu == "setting" || $nowmenu == "help") {

                            $menuvisibility = 1;
                            if (isset($temparray[$item->tagName])) {
                                $menuvisibility = $temparray[$item->tagName];
                            }

                            if (self::get_session('power') == "superadmin" || $nowmenu == "dashboard" || $nowmenu ==
                                "setting" || $nowmenu == "help") {
                                $menuvisibility = 1;

                            }
                            //$subimag = "";   //images/user-trash.png
                            //$subimag = "plugin/".$_SESSION['menu']."/icons/".$item->tagName.".png";
                            //if($menuvisibility == 1){
                            $i++;
                            $menucount++;
                            //$idarray[$item->tagName] = $item->tagName;
                            if ($i == 1 && empty($_SESSION['p'])) {
                                $menu .= "<li class='submenu_tree' id='subb_" . $item->tagName . "'><a href='" .
                                    $nowmenu . "&p=" . $item->tagName .
                                    "' class='inner_submenu_tree active_submenu'  id='" . $item->tagName .
                                    "b' style='cursor: pointer;'><span></span>" . $item->nodeValue . "</a></li>";
                            } else {
                                $naclass = "";
                                if ($item->tagName == $_SESSION['p']) {
                                    $naclass = "active_submenu";
                                }
                                $menu .= "<li class='submenu_tree' id='subb_" . $item->tagName . "'><a href='" .
                                    $nowmenu . "&p=" . $item->tagName . "' class='inner_submenu_tree " . $naclass .
                                    "'  id='" . $item->tagName . "b' style='cursor: pointer;'><span></span>" . $item->
                                    nodeValue . "</a></li>";


                            }
                        }
                    }
                    //  }

                    //print_r($idarray);
                }
                $menu .= "</ul></div></div>";
                //plugin language
                $menu = self::get_pluginlanguage($xml, $menu);
            }

        } else {
            $menu = '';
        }


        return array($menucount, $menu);
    }


    function top_menu()
    {


        $u = "";
        if (isset($_REQUEST['u'])) {
            $u = self::safe_html($_REQUEST['u']);
        }


        if ($u == "setting") {
            $s = "active_submenu";
        } else {
            $s = "";
        }
        if ($u == "admin") {
            $a = "active_submenu";
        } else {
            $a = "";
        }
        if ($u == "help") {
            $h = "active_submenu";
        } else {
            $h = "";
        }

        if ($u == "dashboard" || $u == "") {
            $d = "active_submenu";
        } else {
            $d = "";
        }


        if (isset($_SESSION["tmpuser"])) {
            return '<div id="topmenu">
<div class="topmenucontent">
<ul>
<li id="top_logout"><a style="cursor: pointer;" href="switch"><span></span>{LANMAIN_SWITCHUSER}</a></li>
</ul>  
</div>
</div>';
        }


        $admin = "";
        if (self::get_session('power') == "superadmin") {
            $admin = '<li id="top_admin"><a style="cursor: pointer;" href="admin"  class="' .
                $a . '"><span class="fas fa-home fa-fw"></span>{LANMAIN_ADMIN}</a></li>';
        }

        //$help = "";
        //if(isset($_REQUEST['p'])){if($_REQUEST['p'] == "help"){$help = "active_submenu";}}

        $menu = '<div id="topmenu">
  <div class="topmenucontent">
    <ul>
<li id="top_logout"><a style="cursor: pointer;" href="logout"><span class="fas fa-sign-out-alt fa-fw"></span>{LANMAIN_LOGOUT}</a></li>
<li id="top_help"><a style="cursor: pointer;" href="help"  class="' . $h .
            '"><span class="fas fa-search fa-fw"></span>{LANMAIN_HELP}</a></li>
' . $admin . '<li id="top_setting"><a style="cursor: pointer;" href="setting"  class="' .
            $s . '"><span class="fas fa-cog fa-fw"></span>{LANMAIN_SETTING}</a></li>';
        if (self::config("show_dashboard") === true) {
            $menu .= '<li id="top_dashboard"><a style="cursor: pointer;" href="dashboard" class="' .
                $d . '"><span class="fas fa-tachometer-alt fa-fw"></span>{LANMAIN_DASHBOARD}</a></li>';
        }
        $menu .= '</ul>  
 </div>
</div>';

        return $menu;
    }

    function site_notification()
    {
        $memocount = 0;
        $emailcount = 0;
        $taskcount = 0;


        //1 = no mail
        //2 = new mail
        //0 = invisible, non admin
        $mcount = 1;
        if ($memocount > 0) {
            $mcount = 2;
        }

        $ecount = 1;
        if ($emailcount > 0) {
            $ecount = 2;
        }

        $tcount = 1;
        if ($taskcount > 0) {
            $tcount = 2;
        }

        return array(
            $mcount,
            $tcount,
            $ecount,
            $memocount,
            $taskcount,
            $emailcount);
    }

    function site_language()
    {
        $languagearray = array(
            "en" => "English",
            "es" => "Spanish",
            "fr" => "French",
            "swc" => "Swahili");
        $menu = '<div id="langmenu" class="block">
  <div class="langmenucontent">
    <ul>';

        foreach ($languagearray as $key => $value) {

            $menu .= '<li id="lang_' . $key .
                '"><a style="cursor: pointer;" href="setlanguage?l=' . $key .
                '"><span><img src="' . self::config("theme_folder") . self::config("theme") .
                '/images/language/' . $key .
                '.png" width="20" height="20"  style="vertical-align: middle;" /></span>' . $value .
                '</a></li>';

        }
        $menu .= '</ul>  
 </div>
</div>';
        return $menu;
    }

    function auth_menu()
    {


        $u = "";
        if (isset($_REQUEST['u'])) {
            $u = self::safe_html($_REQUEST['u']);
        }


        if ($u == "setting") {
            $s = "active_submenu";
        } else {
            $s = "";
        }

        if ($u == "help") {
            $h = "active_submenu";
        } else {
            $h = "";
        }

        $admin = "";
        $menu = '<div id="topmenu">
  <div class="topmenucontent">
    <ul>
<li id="top_logout"><a style="cursor: pointer;" href="logout"><span class="fas fa-sign-out fa-fw"></span>{LANMAIN_LOGOUT}</a></li>
<li id="top_help"><a style="cursor: pointer;" href="?p=help"  class="' . $h .
            '"><span class="fas fa-search fa-fw"></span>{LANMAIN_HELP}</a></li>
' . $admin . '<li id="top_setting"><a style="cursor: pointer;" href="setting"  class="' .
            $s . '"><span class="fas fa-cog fa-fw"></span>{LANMAIN_SETTING}</a></li>';
        $menu .= '</ul>  
 </div>
</div>';

        return $menu;
    }


    function site_menu()
    {


        $menukeys = array();
        $row = self::db_query("SELECT plugin.pluginkey, plugin.name, plugin.icon FROM plugin JOIN admin_plugin ON plugin.pluginid = admin_plugin.pluginid WHERE admin_plugin.adminid = ? AND visibility = ? ORDER BY plugin.theorder ASC",
            array(self::get_session('adminid'), 0));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $menukeys[] = $row[$dbc];
        }

        $menu = "";
        if (count($menukeys) > 0) {

            $consize = self::config("icon_size");
            switch ($consize) {
                case "1.0":
                    $iconsize = "fab fas fa-lg";
                    break;

                case "2.0":
                    $iconsize = "fab fas fa-2x";
                    break;

                case "3.0":
                    $iconsize = "fab fas fa-3x";
                    break;

                case "4.0":
                    $iconsize = "fab fas fa-4x";
                    break;

                case "5.0":
                    $iconsize = "fab fas fa-5x";
                    break;

                default:
                    $iconsize = "fab fas fa-lg";
            }


            $menu .= '<div id="leftmenu" class="block">
  <div class="leftmenucontent">
    <ul>';
            $u = "";
            if (isset($_REQUEST['u'])) {
                $u = self::safe_html($_REQUEST['u']);
            }

            for ($i = 0; $i < count($menukeys); $i++) {
                $icon = $menukeys[$i]['icon'];

                if ($u == $menukeys[$i]['pluginkey']) {
                    $menu .= '<li id="left_' . $menukeys[$i]['pluginkey'] .
                        '" class="li_sitemenu"><span class="glow"></span><a style="cursor: pointer;" href="' .
                        $menukeys[$i]['pluginkey'] .
                        '" class="active_submenu sitemenu_ancor" ><span id="menuicon1_' . $menukeys[$i]['pluginkey'] .
                        '" class="' . $icon . " " . $iconsize .
                        ' menu_icon1" style="margin-right:5px;"></span><span class="menu_icon2"  id="menuicon2_' .
                        $menukeys[$i]['pluginkey'] . '">' . $menukeys[$i]['name'] . '</span></a></li>';

                    // $submenu = self::menu_sub_menu($menukeys[$i]['pluginkey']);
                    // $menu .= '<div id="leftmenu_' . $menukeys[$i]['pluginkey'] .'" class="leftmenu_submenu">' . $submenu[1] . '</div>';

                } else {
                    $menu .= '<li id="left_' . $menukeys[$i]['pluginkey'] .
                        '"><span class="glow"></span> <a style="cursor: pointer;" class="sitemenu_ancor" href="' .
                        $menukeys[$i]['pluginkey'] . '"><span id="menuicon1_' . $menukeys[$i]['pluginkey'] .
                        '" class="' . $icon . " " . $iconsize .
                        ' menu_icon1" style="margin-right:5px;"></span><span class="menu_icon2"  id="menuicon2_' .
                        $menukeys[$i]['pluginkey'] . '">' . $menukeys[$i]['name'] . '</span></a></li>';

                    //$submenu = self::menu_sub_menu($menukeys[$i]['pluginkey']);
                    //$menu .= '<div id="leftmenu_' . $menukeys[$i]['pluginkey'] .'" class="leftmenu_submenu">' . $submenu[1] . '</div>';
                }
            }
            $menu .= '</ul>  
 </div>
</div>';
        }
        return $menu;


    }
    function reverse_strrchr($haystack, $needle)
    {
        $pos = strrpos($haystack, $needle);
        if ($pos === false) {
            $subject = $haystack;
        } else {
            $subject = substr($haystack, 0, $pos + 1);
        }

        return preg_replace('~(.*)' . preg_quote("pages/", '~') . '~', '$1' . "", $subject,
            1);
    }


    function get_pluginlanguage($file, $output)
    {
        //start language
        $foldersearch = self::reverse_strrchr($file, '/') . "language/";

        //search for language folder if none ignoe language
        if (file_exists($foldersearch)) {
            //search for language
            if (file_exists($foldersearch . $_COOKIE['language'] . ".php")) {
                include $foldersearch . $_COOKIE['language'] . ".php";
            } else {
                if (file_exists($foldersearch . "en.php")) {
                    include $foldersearch . "en.php";
                } else {

                    $langfileto_include = null;
                    $langfiles = scandir($foldersearch, 1); // using SCANDIR_SORT_DESCENDING PHP 5.4+ ONLY!
                    foreach ($langfiles as $lanfile) {
                        if ($lanfile !== '.' && $lanfile !== '..') {
                            $langfileto_include = $foldersearch . $lanfile;
                        }
                    }

                    if ($langfileto_include != null) {
                        include $langfileto_include;
                    }
                }
            }
            //if found use else search for english else search folder for any language
            //if none egnore


        }


        if (isset($pluginlanguagearray)) {
            $languageparameterskey = array_keys($pluginlanguagearray);
            $languageparametersvalue = array_values($pluginlanguagearray);
            $output = str_replace($languageparameterskey, $languageparametersvalue, $output);
        }
        //end language
        return $output;
    }

    function body($bool = false)
    {

        if ($bool == true) {
            return "<HEAD><meta http-equiv='refresh' content='2;url='index'></HEAD>";
        }

        if (!isset($_SESSION['body'])) {
            return "<HEAD><meta http-equiv='refresh' content='2;url='index'></HEAD>";
        } else {

            $file = $_SESSION['body'];
            if (file_exists($file)) {
                $file = $file;
            } else {
                $file = "engine/errorpages/404.php";
            }

            $opts = array('http' => array('method' => "GET", 'header' =>
                        "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));


            ob_start();
            include ($file);
            $output = ob_get_contents();

            //plugin language
            $output = self::get_pluginlanguage($file, $output);

            ob_end_clean();
            file_put_contents('cache/' . self::get_session('adminme') . '_filename.html', $output);


            $context = stream_context_create($opts);
            $file = file_get_contents('cache/' . self::get_session('adminme') .
                '_filename.html', false, $context);

            return $file;
        }

    }

    function right_widget()
    {

        $widgetarray = array();
        $row = self::db_query("SELECT widget.widgetkey, admin_widget.id, admin_widget.widgettop, admin_widget.widgetleft FROM widget JOIN admin_widget ON widget.widgetid = admin_widget.widgetid WHERE admin_widget.adminid = ? AND widget.position = ? AND widget.widgetstatus = ? AND admin_widget.status = ? ORDER BY widget.widgetorder ASC",
            array(
            self::get_session('adminid'),
            2,
            1,
            1));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $widgetarray[] = $row[$dbc];
        }


        $return = "";
        for ($i = 0; $i < count($widgetarray); $i++) {

            $parameter = self::readparameter($widgetarray[$i]['widgetkey']);

            $file = "widget/" . $widgetarray[$i]['widgetkey'] . "/index.php";
            $h = '';
            if (isset($parameter['height'])) {
                $h = 'height:' . $parameter['height'] . ';';
            }

            $widgetcalss = 'rightwidgettitle';
            if (isset($parameter['header_class'])) {
                $widgetcalss = $parameter['header_class'];
            }

            $return .= '<div class="rightwidgetcontainer" >
<div class="' . $widgetcalss . '">' . $parameter['title'] . '</div>
<div class="rightwidgetbody" style="' . $h . '">';


            $opts = array('http' => array('method' => "GET", 'header' =>
                        "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));


            ob_start();
            include ($file);
            $output = ob_get_contents();
            ob_end_clean();
            file_put_contents('cache/' . self::get_session('adminme') . '_' . $widgetarray[$i]['widgetkey'] .
                '.html', $output);


            $context = stream_context_create($opts);
            $file = file_get_contents('cache/' . self::get_session('adminme') . '_' . $widgetarray[$i]['widgetkey'] .
                '.html', false, $context);

            $return .= $file;


            $return .= '</div></div>';

        }
        return $return;
    }

    function left_widget()
    {

        $widgetarray = array();
        $row = self::db_query("SELECT widget.widgetkey, admin_widget.id, admin_widget.widgettop, admin_widget.widgetleft FROM widget JOIN admin_widget ON widget.widgetid = admin_widget.widgetid WHERE admin_widget.adminid = ? AND widget.position = ? AND widget.widgetstatus = ? AND admin_widget.status = ? ORDER BY widget.widgetorder ASC",
            array(
            self::get_session('adminid'),
            1,
            1,
            1));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $widgetarray[] = $row[$dbc];
        }


        $return = "";
        for ($i = 0; $i < count($widgetarray); $i++) {

            $parameter = self::readparameter($widgetarray[$i]['widgetkey']);

            $file = "widget/" . $widgetarray[$i]['widgetkey'] . "/index.php";
            $h = '';
            if (isset($parameter['height'])) {
                $h = 'height:' . $parameter['height'] . ';';
            }

            $widgetcalss = 'leftwidgettitle';
            if (isset($parameter['header_class'])) {
                $widgetcalss = $parameter['header_class'];
            }

            $return .= '<div class="leftwidgetcontainer">
<div class="' . $widgetcalss . '">' . $parameter['title'] . '</div>
<div class="leftwidgetbody" style="' . $h . '">';


            $opts = array('http' => array('method' => "GET", 'header' =>
                        "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));


            ob_start();
            include ($file);
            $output = ob_get_contents();
            ob_end_clean();
            file_put_contents('cache/' . self::get_session('adminme') . '_' . $widgetarray[$i]['widgetkey'] .
                '.html', $output);


            $context = stream_context_create($opts);
            $file = file_get_contents('cache/' . self::get_session('adminme') . '_' . $widgetarray[$i]['widgetkey'] .
                '.html', false, $context);

            $return .= $file;


            $return .= '</div></div>';

        }
        return $return;
    }

    function middle_widget()
    {

        $widgetarray = array();
        $row = self::db_query("SELECT widget.widgetkey, admin_widget.id, admin_widget.widgettop, admin_widget.widgetleft FROM widget JOIN admin_widget ON widget.widgetid = admin_widget.widgetid WHERE admin_widget.adminid = ? AND widget.position = ? AND widget.widgetstatus = ? AND admin_widget.status = ? ORDER BY widget.widgetorder ASC",
            array(
            self::get_session('adminid'),
            0,
            1,
            1));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $widgetarray[] = $row[$dbc];
        }


        $return = "";
        for ($i = 0; $i < count($widgetarray); $i++) {

            $parameter = self::readparameter($widgetarray[$i]['widgetkey']);

            $file = "widget/" . $widgetarray[$i]['widgetkey'] . "/index.php";
            $h = '';
            if (isset($parameter['height'])) {
                $h = 'height:' . $parameter['height'] . ';';
            }

            $widgetcalss = 'leftwidgettitle';
            if (isset($parameter['header_class'])) {
                $widgetcalss = $parameter['header_class'];
            }

            $return .= '<div class="leftwidgetcontainer">
<div class="' . $widgetcalss . '">' . $parameter['title'] . '</div>
<div class="leftwidgetbody" style="' . $h . '">';


            $opts = array('http' => array('method' => "GET", 'header' =>
                        "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));


            ob_start();
            include ($file);
            $output = ob_get_contents();
            ob_end_clean();
            file_put_contents('cache/' . self::get_session('adminme') . '_' . $widgetarray[$i]['widgetkey'] .
                '.html', $output);


            $context = stream_context_create($opts);
            $file = file_get_contents('cache/' . self::get_session('adminme') . '_' . $widgetarray[$i]['widgetkey'] .
                '.html', false, $context);

            $return .= $file;


            $return .= '</div></div>';

        }
        return $return;
    }

    public function show_widget()
    {

        $menukeys = array();
        $row = self::db_query("SELECT  widget.name, widget.widgetkey, widget.position, admin_widget.status, admin_widget.id, admin_widget.widgettop, admin_widget.widgetleft FROM widget JOIN admin_widget ON widget.widgetid = admin_widget.widgetid WHERE admin_widget.adminid = ? AND widget.widgetstatus = ?",
            array(self::get_session('adminid'), 1));
        for ($dbc = 0; $dbc < self::array_count($row); $dbc++) {
            $menukeys[] = $row[$dbc];
        }
        return $menukeys;
    }


    function get_widgetlanguage($file, $output)
    {
        //start language
        $foldersearch = $file;

        //search for language folder if none ignoe language
        if (file_exists($foldersearch)) {
            //search for language
            if (file_exists($foldersearch . $_COOKIE['language'] . ".php")) {
                include $foldersearch . $_COOKIE['language'] . ".php";

            } else {
                if (file_exists($foldersearch . "en.php")) {
                    include $foldersearch . "en.php";
                } else {

                    $langfileto_include = null;
                    $langfiles = scandir($foldersearch, 1); // using SCANDIR_SORT_DESCENDING PHP 5.4+ ONLY!
                    foreach ($langfiles as $lanfile) {
                        if ($lanfile !== '.' && $lanfile !== '..') {
                            $langfileto_include = $foldersearch . $lanfile;
                        }
                    }

                    if ($langfileto_include != null) {
                        include $langfileto_include;
                    }
                }
            }
            //if found use else search for english else search folder for any language
            //if none egnore
        }


        if (isset($widgetlanguagearray)) {
            $languageparameterskey = array_keys($widgetlanguagearray);
            $languageparametersvalue = array_values($widgetlanguagearray);
            $output = str_replace($languageparameterskey, $languageparametersvalue, $output);
        }
        //end language
        return $output;
    }

    function auth_body($bool = false)
    {
        if ($bool == true) {
            return "<HEAD><meta http-equiv='refresh' content='2;url='index'></HEAD>";
        }

        if (!isset($_SESSION['body'])) {
            return "<HEAD><meta http-equiv='refresh' content='2;url='index'></HEAD>";
        } else {

            $file = $_SESSION['body'];
            if (file_exists($file)) {
                $file = $file;
            } else {
                $file = "engine/errorpages/404.php";
            }

            $opts = array('http' => array('method' => "GET", 'header' =>
                        "Accept-language: en\r\n" . "Cookie: foo=bar\r\n"));


            ob_start();
            include ($file);
            $output = ob_get_contents();

            //plugin language
            $output = self::get_pluginlanguage($file, $output);

            ob_end_clean();
            file_put_contents('cache/' . self::get_session('adminme') . '_filename.html', $output);


            $context = stream_context_create($opts);
            $file = file_get_contents('cache/' . self::get_session('adminme') .
                '_filename.html', false, $context);

            return $file;
        }

    }

    function body_small()
    {

        $widgetarray = self::show_widget();

        $return = '
<script type="text/javascript">
function callmywidget(widgetid){
   $("#content_body").append("<img class=\'appendedimg\' src=\'images/ajax-loader.gif\' />"); 
      $.ajax({
           type: "POST",
           url: "plugin/dashboard/pages/callwidget.php",
           data: "widgetid="+widgetid,
           success: function (msg){
           $("#tmpbody").html(msg).show();
           $(".appendedimg").remove();
           $("#content_body").hide();
           },
           error:function(mgd){
            $(".appendedimg").remove();
            alert("Connection Error");
           }
       });
    
}
</script>';

        $return .= '<div id="smallmenu" class="block">
        <div class="smallmenucontent">
        <ul>';
        for ($icount = 0; $icount < count($widgetarray); $icount++) {
            $return .= '<li id="small_' . $widgetarray[$icount]['widgetkey'] .
                '"><a onclick="callmywidget(\'' . $widgetarray[$icount]['widgetkey'] . '\')" style="cursor: pointer;"><span></span>' .
                $widgetarray[$icount]['name'] . '</a></li>';
        }
        $return .= '</ul>   
        </div>
        </div>';
        return $return;
    }


    function theme_parameter($index = "index")
    {
        $footer = "<div style='display:none; text-align:center;'>SPL Server V" . self::
            config("version") . " =&raquo; " . self::config("app_name") . "</div>
<div style='display:none; text-align:center;'><a href='http://www.RechargePro.com'>http://www.RechargePro.com</a></div>";

        if ($index == "index") {

            $mgbig = self::config("theme_folder") . self::config("theme") .
                "/images/default.png";
            $mgsmall = self::config("theme_folder") . self::config("theme") .
                "/images/small_default.png";
            if (file_exists("avater/" . self::get_session('adminme') . ".jpg")) {
                $mgbig = "avater/" . self::get_session('adminme') . ".jpg";
                $mgsmall = "avater/small_" . self::get_session('adminme') . ".jpg";
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


            $plugindetails = self::xml_details();
            $notification = self::site_notification();
            $submenu = self::sub_menu();
            return array(
                "{RIGHT_WIDGET}" => self::right_widget(),
                "{LEFT_WIDGET}" => self::left_widget(),
                "{BODY_HOLDER}" => self::config("body_holder"),
                "{PLUGIN_MENU}" => $submenu[1],
                "{BODY}" => self::body(),
                "{BODY_SMALL}" => self::body_small(),
                "{TOP_MENU}" => self::top_menu(),
                "{SITE_MENU}" => self::site_menu(),
                "{SITE_LOGO}" => self::config("theme_folder").self::config("theme")."/images/logo.png",
                "{SITE_LOCATION}" => self::config("theme_folder").self::config("theme"),
                "{SITE_NAME}" => self::config("app_name"),
                "{LOGIN_NAME}" => self::get_session('name'),
                "{AVATER_SMALL}" => $mgsmall,
                "{AVATER_BIG}" => $mgbig,
                "{FOOTER}" => $footer,
                "{LOGOUT}" => '<div id="topmenu"><div class="topmenucontent"><ul><li id="top_logout"><a style="cursor: pointer;" href="logout"><span></span>logout</a></li></ul></div></div>',
                "{BREAD_CRUMB}" => $breadcrumb,
                "{LANGUAGE}" => $_COOKIE['language'],
                "{LANGUAGE_FILE}" => self::site_language(),
                "{NOTIFICATION_MEMO}" => $notification[0],
                "{NOTIFICATION_MEMO_COUNT}" => $notification[3],
                "{NOTIFICATION_TASK}" => 1,
                "{NOTIFICATION_TASK_COUNT}" => 1,
                "{NOTIFICATION_EMAIL}" => 1,
                "{NOTIFICATION_EMAIL_COUNT}" => 1,
                "{PLUGIN_KEY}" => $plugindetails['key'],
                "{PLUGIN_KEY_MENU_COUNT}" => $submenu[0],
                "{PLUGIN_NAME}" => $plugindetails['oruko'],
                "{PLUGIN_DETAILS}" => $plugindetails['details'],
                "{PLUGIN_IMAGE_CLASS}" => $plugindetails['image_class'],
                );
        } else
            if ($index == "auth") {


                $mgbig = self::config("theme_folder") . self::config("theme") .
                    "/images/default.png";
                $mgsmall = self::config("theme_folder") . self::config("theme") .
                    "/images/small_default.png";
                if (file_exists("avater/" . self::get_session('adminme') . ".jpg")) {
                    $mgbig = "avater/" . self::get_session('adminme') . ".jpg";
                    $mgsmall = "avater/small_" . self::get_session('adminme') . ".jpg";
                }
                return array(
                    "{SITE_LOGO}" => self::config("theme_folder") . self::config("theme") .
                        "/images/logo.png",
                    "{SITE_LOCATION}" => self::config("theme_folder") . self::config("theme"),
                    "{SITE_NAME}" => self::config("app_name"),
                    "{LOGIN_NAME}" => self::get_session('name'),
                    "{AVATER_SMALL}" => $mgsmall,
                    "{AVATER_BIG}" => $mgbig,
                    "{FOOTER}" => $footer,
                    "{LOGOUT}" => '<div id="topmenu"><div class="topmenucontent"><ul><li id="top_logout"><a style="cursor: pointer;" href="logout"><span></span>logout</a></li></ul></div></div>',
                    "{LANGUAGE}" => $_COOKIE['language'],
                    "{LANGUAGE_FILE}" => self::site_language(),
                    "{TOP_MENU}" => self::auth_menu(),
                    "{BODY}" => self::auth_body());
            } else {
                $error = "";
                if (isset($_SESSION['login_error'])) {
                    $error = $_SESSION['login_error'];
                    unset($_SESSION['login_error']);
                }

                $r = "";
                if (isset($_REQUEST['r'])) {
                    $r = $_REQUEST['r'];
                }


                return array(
                    "{SITE_LOGO}" => self::config("theme_folder") . self::config("theme") .
                        "/images/logo.png",
                    "{SITE_LOCATION}" => self::config("theme_folder") . self::config("theme"),
                    "{SITE_NAME}" => self::config("app_name"),
                    "{LOGIN_USERNAME}" =>
                        '<input id="username" autocomplete="off" name="username" type="text" class="input" />',
                    "{LOGIN_PASSWORD}" =>
                        '<input id="password" autocomplete="off" name="password" type="password" class="input" />',
                    "{LOGIN_RETURNURL}" =>
                        '<input type="hidden" name="returnurl" id="returnurl" value="' . $r . '" />',


                    "{LOGIN_BUTTON}" => '<input name="login" id="login" class="login" type="submit" value="{LANMAIN_LOGIN}" />',
                    "{LOGIN_FORM_LOCATION}" => 'secure/login.php',
                    "{LOGIN_ERROR}" => $error,
                    "{MOTTO}" => self::config("motto"),

                    );
            }
    }

    public function readparameter($widget)
    {

        if (!empty($widget)) {


            $menu = "";
            $xml = "widget/$widget/parameter.xml";
            if (file_exists($xml)) {

                $xmlDoc = new DOMDocument();
                $xmlDoc->load($xml);
                $x = $xmlDoc->documentElement;
                $paramarray = array();
                foreach ($x->childNodes as $item) {

                    if (strlen($item->nodeValue) > 1) {
                        $paramarray[$item->tagName] = $item->nodeValue;
                    }
                }
                return $paramarray;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    function picture($username, $picdo = "small")
    {
        $number = rand(1, 9);

        $imga = "avater/$username.jpg";
        $img = "../avater/$username.jpg";

        if (!file_exists(AUTH_DOCROOTB . $imga)) {
            $img = "../theme/classic/images/avater/$number.png";
        }
        if (is_dir(AUTH_DOCROOTB . $imga)) {
            $img = "../theme/classic/images/avater/$number.png";
        }
        return $img;


    }

    function blockedaccount()
    {
        if (isset($_SESSION['disabled']) && $_SESSION['disabled'] == 1) {
            echo "<meta http-equiv='refresh' content='0;url=../index?p=5'>";
            exit;
        }
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

        if ($config["allow_user_theme_sellection"]) {
            if ($key == "theme" || $key == "icon_size") {
                $theme = (isset($config["theme"])) ? $config["theme"] : $default;
                $iconsize = (isset($config["icon_size"])) ? $config["icon_size"] : $default;
                $site_width = (isset($config["site_width"])) ? $config["site_width"] : $default;
                $user_widget_state_change = (isset($config["user_widget_state_change"])) ? $config["user_widget_state_change"] :
                    $default;


                if (self::get_session("theme")) {
                    $explodetheme = explode("/", self::get_session("theme"));
                    $theme = $explodetheme[0];
                    $iconsize = $explodetheme[1];
                    $site_width = $explodetheme[2];
                    $user_widget_state_change = $explodetheme[3];
                }


                if ($key == "theme") {
                    return $theme;
                }
                if ($key == "icon_size") {
                    return $iconsize;
                }
                if ($key == "site_width") {
                    return $site_width;
                }

                if ($key == "user_widget_state_change") {
                    return $user_widget_state_change;
                }


            }

        }

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

    public function getExtension($str)
    {
        $boss = explode(".", strtolower($str));
        return end($boss);
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
    public function country()
    {
        include MAIN_ROOT . "country.php";
        return;
    }

    public function state($country = "Nigeria", $width = "width:200px;", $height =
        "height:20px;")
    {
        //$country = strtolower($country);
        if (file_exists(realpath(dirname(__file__)) . "/country_state/$country.php")) {

            include (realpath(dirname(__file__)) . "/country_state/$country.php");
        }
        return $statearray;
    }


    public function lga($state = "Abuja", $width = "width:200px;", $height =
        "height:20px;")
    {
        //$state = strtolower($state);
        if (file_exists(realpath(dirname(__file__)) . "/lga/$state.php")) {

            include (realpath(dirname(__file__)) . "/lga/$state.php");
        } else {
            '<input  class="input" type="text" style="' . $width . ' ' . $height .
                '" id="state" name="lga"/>';
        }
        return;
    }


    public function outputcsv($fileName, $assocDataArray)
    {
        ob_clean();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $fileName);
        if (isset($assocDataArray['0'])) {
            $fp = fopen('php://output', 'w');
            fputcsv($fp, array_keys($assocDataArray['0']));
            foreach ($assocDataArray as $values) {
                fputcsv($fp, $values);
            }
            fclose($fp);
        }
        ob_flush();
    }

    public function safe_html($dirty_html)
    {
       // $config = HTMLPurifier_Config::createDefault();
       // $purifier = new HTMLPurifier($config);
       // $clean_html = $purifier->purify($dirty_html);
        return $dirty_html;//$clean_html;
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

    private function db_select($result, $CONN)
    {
        $arrayrow = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $tmparray = array();
            foreach ($row as $key => $value) {
                if (self::config("filter_sql")) {
                    $tmparray[$key] = self::safe_html($value);
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

    public function array_count($array)
    {
        $empty = array_filter($array[0]);
        if (empty($empty)) {
            return 0;
        }
        return count($array);
    }

    public function myfunction($products, $field, $value)
    {
        foreach ($products as $key => $product) {
            if ($product[$field] === $value)
                return $key;
        }
        return false;
    }

}


class engine extends maincontrol
{
    public function __construct()
    {
        self::log_me();
        new language();
    }
}


require_once MAIN_ROOT . "/classsqlimport.php";
require_once MAIN_ROOT . "/memcache.php";
require_once MAIN_ROOT . "/Logger.php";
require_once "LanguageDetect.php";


$engine = new engine();
?>