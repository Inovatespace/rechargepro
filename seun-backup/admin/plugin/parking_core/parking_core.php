<?php
//if (!isset($_SESSION))
//{
//    session_start();
//}
//
//define('AUTH_DOCROOTB', dirname(dirname(dirname(__file__))) . '/');
//define('MAIN_ROOTB', realpath(dirname(__file__)) . '/');
//require_once AUTH_DOCROOTB . "engine/class/Logger.php";
//
//class parking_core extends engine
//{
//
//    public function __construct()
//    {
//        $Logger = new Logger(array('path' => AUTH_DOCROOTB . '/log/'));
//        $Logger->enable_exception();
//
//        if (self::config('log_error'))
//        {
//            $Logger->enable_error();
//            $Logger->enable_display_error(self::config('display_error'));
//            $Logger->enable_fatal();
//            $Logger->enable_method_file(true);
//        } else
//        {
//            $Logger->enable_display_error(self::config('display_error'));
//        }
//    }
//    
// public function toMoney($val,$symbol='N',$r=2)
//{
//
//$length = 0;
//    $n = $val; 
//    $c = is_float($n) ? 1 : number_format($n,$r);
//    $d = '.';
//    $t = ',';
//    $sign = ($n < 0) ? '-' : '';
//    $i = $n=number_format(abs($n),$r); 
//    $j = (($j = $i.$length) > 3) ? $j % 3 : 0; 
//
//   return  $symbol.$sign .($j ? substr($i,0, $j) + $t : '').preg_replace('/(\d{3})(?=\d)/',"$1" + $t,substr($i,$j)) ;
//
//} 
//    
//    public function sendmail($fromm,$fromname,$too,$cc,$reply,$subject,$message,$ecat){  
//        self::db_query("INSERT INTO temp_email (fromm,fromname,too,cc,reply,subject,message,ecat) VALUES (?,?,?,?,?,?,?,?)",array($fromm,$fromname,$too,$cc,$reply,$subject,$message,$ecat));
//        
//        
//    }
//    
//    
//
//    public function authenticateme($what)
//    {
//
//        $explode = explode("-", $what);
//
//        if (!isset($_SESSION["tab$explode[0]"]))
//        {
//            echo "<meta http-equiv='refresh' content='0;url=index.php'>";
//            exit;
//        }
//
//
//        $session = $_SESSION["tab$explode[0]"];
//        $session = explode("@", $session);
//
//        if (empty($session[0]))
//        {
//            echo "<div class='nWarning'>Permission Have not been granted to view Content</div>";
//            exit;
//        }
//
//
//        if ($explode[1] == "")
//        {
//            $explode[1] = $session[0];
//        }
//
//
//        if (!in_array($explode[1], $session))
//        {
//            echo "<meta http-equiv='refresh' content='0;url=index.php'>";
//            exit;
//        }
//
//        if ($explode[1] == "")
//        {
//            $getm = $session[0];
//        }
//
//
//        return $explode[1];
//
//    }
//
//    /**
//     * FTP START
//     */
//    public function spl_ftp($ftp_user_name)
//    {
//        $ftp_server = "127.0.0.1";
//        //$ftp_user_name="headit";
//        $ftp_user_pass = "";
//
//        // set up basic connection
//        $conn_id = ftp_connect($ftp_server);
//        // login with username and password
//        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
//        return $conn_id;
//    }
//
//    public function display_currentdir($conn_id)
//    {
//        return ftp_pwd($conn_id);
//    }
//
//    public function makeftp_dir($conn_id, $dir_name)
//    {
//        ftp_chdir($conn_id, $dir_name);
//    }
//
//
//
//    function blockedaccount()
//    {
//        if (isset($_SESSION['disabled']) && $_SESSION['disabled'] == 1)
//        {
//            echo "<meta http-equiv='refresh' content='0;url=../index?p=5'>";
//            exit;
//        }
//    }
//
//    function session($session)
//    {
//        return $_SESSION[$session];
//    }
//
//
//    function limit_text($message, $start, $length, $max = 1000)
//    {
//        return substr($message, $start, $length);
//    }
//
//    public static function config($key, $default = null)
//    {
//        static $config;
//
//        if ($config === null)
//        {
//            $config = include MAIN_ROOTB . 'config/config.php';
//
//        }
//
//        return (isset($config[$key])) ? $config[$key] : $default;
//    }
//
//    public function getRealIpAddr()
//    {
//        if (!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
//        {
//            $ip = $_SERVER['HTTP_CLIENT_IP'];
//        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
//        //to check ip is pass from proxy
//        {
//            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//        } else
//        {
//            $ip = $_SERVER['REMOTE_ADDR'];
//        }
//        return $ip;
//    }
//
//    public function byteconvert($bytes)
//    {
//        if ($bytes < 1)
//        {
//            return "0 B";
//        }
//
//        $symbol = array(
//            'B',
//            'KB',
//            'MB',
//            'GB',
//            'TB',
//            'PB',
//            'EB',
//            'ZB',
//            'YB');
//        $exp = floor(log($bytes) / log(1024));
//        return sprintf('%.2f ' . $symbol[$exp], ($bytes / pow(1024, floor($exp))));
//    }
//
//    public function getExtension($str)
//    {
//        $boss = explode(".", strtolower($str));
//        return end($boss);
//    }
//
//
//    public function country()
//    {
//        include MAIN_ROOTB . "country.php";
//        return;
//    }
//
//
//
//    public function street()
//    {
//        include MAIN_ROOTB . "street.php";
//        return $array;
//    }
//
//    public function getdistrict($street)
//    {
//        include MAIN_ROOTB . "district.php";
//        return $district;
//    }
//
//    public function staf_position()
//    {
//        include MAIN_ROOTB . "position.php";
//        return $position;
//    }
//
//    public function department($position, $call)
//    {
//        include MAIN_ROOTB . "department.php";
//        if ($call == 0)
//        {
//            return $answer;
//        }
//        if ($call == 1)
//        {
//            return $code;
//        }
//
//        if ($call == 2)
//        {
//            return array(
//                "Finance and Admin",
//                "Public Relations",
//                "Business Development",
//                "Geographical Information System",
//                "Legal",
//                "General Manager",
//                "Human Resources",
//                "Managing Director",
//                "Operations",
//                "Chief Operating Officer",
//                "IT");
//        }
//
//    }
//
//    function streetcordinate_popularplace($street)
//    {
//        include MAIN_ROOTB . "streetcordinate.php";
//        return $result;
//    }
//
//    function street_code($code,$stat = false)
//    {
//        
//        include MAIN_ROOTB . "streetcode.php";
//        if($stat == true){
//          return $streetcode;  
//        }
//        
//        
//        
//        if (in_array($code, $streetcode))
//        {
//            $key = array_search($code, $streetcode);
//            return $key;
//        } else
//        {
//            return $code;
//        }
//    }
//    
//    
//
//    function palocation($agent_username, $pos_date_time)
//    {
//         $row =  self::db_query("SELECT street FROM sales_report_it WHERE date BETWEEN ? AND ? AND staffid = ? LIMIT 1",array());
//        $startdate = substr($pos_date_time, 0, 10);
//        $enddate = $today = date("Y-m-d", strtotime("+1 day", strtotime($startdate)));
//
//        $res2->execute(array(
//            $startdate,
//            $enddate,
//            $agent_username));
//        $row2 = $res2->fetch(PDO::FETCH_ASSOC);
//        $street = substr($row[0]['street'], 0, 22);
//        ;
//        return $street;
//    }
//
//
//    function paname($username, $limit = 0)
//    {
//                 $row =  self::db_query("SELECT name FROM members WHERE staffid = ? LIMIT 1",array($username));
//        if ($limit > 0)
//        {
//            $name = substr($row[0]['name'], 0, $limit);
//        } else
//        {
//            $name = $row[0]['name'];
//        }
//        return $name;
//    }
//    
//    
//        function paymentmode($id)
//    {
//        switch ($id){
//    	case "0": $what = "Cash";
//    	break;
//        
//        case "1": $what = "A Card";
//    	break;
//    
//    	case "2": $what = "ATM Card";
//    	break;
//    
//    	case "3": $what = "ATM Machine";
//    	break;
//    
//    	default : $what = "-";
//        }
//        
//        return $what;
//    }
//
//
//}
//
//$parking_core = new parking_core();

?>