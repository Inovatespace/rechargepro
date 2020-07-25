<?php
include ('../engine.autoloader.php');
require ("../engine/phprotector/PhProtector.php");


function parseUrl($url) {
    $r  = "^(?:(?P<scheme>\w+)://)?";
    $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
    $r .= "(?P<host>(?:(?P<subdomain>[\w\.]+)\.)?" . "(?P<domain>\w+\.(?P<extension>\w+)))";
    $r .= "(?::(?P<port>\d+))?";
    $r .= "(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
    $r .= "(?:\?(?P<arg>[\w=&]+))?";
    $r .= "(?:#(?P<anchor>\w+))?";
    $r = "!$r!";                                                // Delimiters
    
    preg_match ( $r, $url, $out );
    
    if(!isset($out['domain'])){
        $out['domain'] = parse_url("$url", PHP_URL_HOST);
    }
    
    return $out;
}

/* TESTING environment (show all PHP errors!) */
$prot = new PhProtector("../log/log.xml", true);
if ($prot->isMalicious()) {
    header("location: ../engine/errorpages/index.html"); //if an atack is found, it will be redirected to this page :)
    die();
}


if (empty($_REQUEST['username']) || empty($_REQUEST['password'])) {
    $_SESSION['login_error'] = "bad";
    echo "bad";
    exit;
}

$username = $_REQUEST['username'];
$password = sha1(md5($_REQUEST['password']) . $engine->config("user_key"));

if ($engine->config("local_authentication")) {
    $rowa = $engine->db_query("SELECT adminid,username,email,name,mobile,role,state,theme,plainpassword FROM admin WHERE (username = ? || email = ? || mobile = ?) AND password = ?  AND active = ? LIMIT 1",
        array(
        $username,
        $username,
        $username,
        $password,
        1));
    $row = $rowa[0];
} else {


    $postData = array(
        "username" => $username,
        "password" => $_REQUEST['password'],
        "server_id" => $engine->config('server_id'));
    $return = $engine->post($postData, $engine->config('authentication_server') .
        'api/core/admin/admin_login.json');
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


    $rowb = $engine->db_query("SELECT adminid FROM admin WHERE (username = ? || email = ? || mobile = ?) LIMIT 1",
        array(
        $username,
        $username,
        $username));
    if (empty($rowb[0]['adminid'])) {
        $lastinsertid = $engine->db_query("INSERT INTO admin (username,email,name,address,mobile,dob,country,state,lga,role,sex,active,reg_date,theme,val1,val2,val3,val4,val5) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            array(
            $return[0]['username'],
            $return[0]['email'],
            $return[0]['name'],
            $return[0]['address'],
            $return[0]['mobile'],
            $return[0]['dob'],
            $return[0]['country'],
            $return[0]['state'],
            $return[0]['lga'],
            $return[0]['role'],
            $return[0]['sex'],
            $return[0]['active'],
            $return[0]['reg_date'],
            $return[0]['theme'],
            $return[0]['val1'],
            $return[0]['val2'],
            $return[0]['val3'],
            $return[0]['val4'],
            $return[0]['val5']));

        $row['adminid'] = $lastinsertid;
    } else {
        $row['adminid'] = $rowb[0]['adminid'];

        $engine->db_query("UPDATE admin SET email = ?, name = ?, address = ?, mobile = ?, dob = ?, country = ?, state = ?, lga = ?, role = ?, sex = ?, active = ?, reg_date = ?, theme = ?, val1 = ?, val2 = ?, val3 = ?, val4 = ?, val5=? WHERE adminid = ? AND username = ? LIMIT 1",
            array(
            $return[0]['email'],
            $return[0]['name'],
            $return[0]['address'],
            $return[0]['mobile'],
            $return[0]['dob'],
            $return[0]['country'],
            $return[0]['state'],
            $return[0]['lga'],
            $return[0]['role'],
            $return[0]['sex'],
            $return[0]['active'],
            $return[0]['reg_date'],
            $return[0]['theme'],
            $return[0]['val1'],
            $return[0]['val2'],
            $return[0]['val3'],
            $return[0]['val4'],
            $return[0]['val5'],
            $row['adminid'],$return[0]['username'],));


        $saveto = "../avater/" . $username . ".jpg";
        if (!file_exists($saveto)) {
            $nowtime = strtotime(date("Y-m-d H:i:s", strtotime("-20 day", strtotime(date("Y-m-d H:i:s")))));
            $filetime = strtotime(date("Y-m-d H:i:s", filemtime($saveto)));
            if ($nowtime > $filetime) {
                $data = $engine->get($engine->config('authentication_server') . "/avater/" . $username .
                    ".jpg");
                $fp = fopen($saveto, 'w+');
                fwrite($fp, $data);
                fclose($fp);

                $saveto = "../avater/small_" . $username . ".jpg";
                $data = $engine->get($engine->config('authentication_server') . "/avater/small_" . $username .
                    ".jpg");
                $fp = fopen($saveto, 'w+');
                fwrite($fp, $data);
                fclose($fp);
            }
        }
    }
    
    
    $rootweb = parseUrl($engine->config("website_root"));
    $cookiedomain = $rootweb['domain'];
    
         $Cookie = new Cookie();
         $inTwoMonths = 60 * 60 * 24 * 60 + time();
         $Cookie->save("header", $return[0]['server'], $inTwoMonths, "/", $cookiedomain, false);
    //if userid of name not exist here, creat it
}


if (empty($row['username'])) {
    $_SESSION['login_error'] = "bad";
    echo "bad";
    exit;
} else {
    $ss = new securesession();
    $ss->check_browser = true;
    $ss->check_ip_blocks = 2;
    $ss->secure_word = 'IYO_';
    $ss->regenerate_id = true;
    $ss->Open();

    $_SESSION['adminid'] = $row['adminid'];
    $_SESSION['adminme'] = $row['username'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['mobile'] = $row['mobile'];
    $_SESSION['email'] = $row['email'];
    $_SESSION['power'] = $row['role'];
    $_SESSION['state'] = $row['state'];
    $_SESSION['theme'] = $row['theme'];


    $rowemail = $engine->db_query("SELECT email, host,protocal FROM email_account WHERE acountid = ? LIMIT 1",
        array($row['adminid']));
    if (!empty($rowemail[0]['email'])) {
        $_SESSION['email_host'] = $rowemail[0]['email'] . "#" . $rowemail[0]['host'] .
            "#" . $row['plainpassword'] . "#" . $rowemail[0]['protocal'];
    }


    #This makes sure that a user agent is set to avoid error
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $useragent = " [" . $_SERVER['HTTP_USER_AGENT'] . "]";
    } else {
        $useragent = " [No user agent set]";
    }

    $engine->dblog($row['adminid'], "login", $row['username'], $engine->
        getRealIpAddr(), $useragent);

    if (file_exists('plugin/login.php')) {
        include ('plugin/login.php');
    }

}

$returnurl = "index";
if (isset($_REQUEST['returnurl'])) {
    $returnurl = str_replace('%', '&', $_REQUEST['returnurl']);
}
echo $returnurl;
exit;

?>