<?php
include ('../engine.autoloader.php');
//require ("../engine/phprotector/PhProtector.php");

include ('auth/Facebook/autoload.php');


function grab_image($image_url, $image_file){
$image = json_decode(file_get_contents($image_url),true);
$image = file_get_contents($image['data']['url']);
file_put_contents($image_file,$image);                               // closing file handle
}

 $fb = new Facebook\Facebook([
  'app_id' => '323154064708485', // Replace {app-id} with your app id
  'app_secret' => 'c4a1e79701c5c882338f4f5dd5fec13e',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  $_SESSION['error'] = 'Graph returned an error: ' . $e->getMessage();
  header('Location: https://nextcashandcarry.com.ng/cart');
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  $_SESSION['error'] = 'Facebook SDK returned an error: ' . $e->getMessage();
  header('Location: https://nextcashandcarry.com.ng/cart');
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'From facebook Bad request';
  }
  exit;
}




try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=id,name,email,birthday,location,picture,gender,hometown', $accessToken->getValue());
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    
  $_SESSION['error'] = 'Graph returned an error: ' . $e->getMessage();
  header('Location: https://nextcashandcarry.com.ng/cart');
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  $_SESSION['error'] = 'Facebook SDK returned an error: ' . $e->getMessage();
  header('Location: https://nextcashandcarry.com.ng/cart');
  exit;
}

$user = $response->getGraphUser();

 $email = $user['email'];
 $name = $user['name'];
 $picture = $user['picture']['url'];
 $userid = $user['id'];
 $sex = $user['gender'];
 $rowa = $engine->db_query("SELECT memberid, email,name, active, activemobile FROM members WHERE email = ? LIMIT 1",array($email));
    $row = $rowa[0];
    $memberid = $row['memberid'];
if (empty($row['email'])){
    //register
    $title = "Mrs";
    if(strtolower($sex) == "male"){$title = "Mr"; $sex = "Male";}
    if(strtolower($sex) == "female"){$sex = "Female"; }

$memberid = $engine->db_query("INSERT INTO members (title,name,email,active,sex,facebook) VALUES (?,?,?,?,?,?)",array($title,$name,$email,1,$sex,$accessToken->getValue()));

grab_image($picture,"../avater/".$memberid.".jpg");

   // $ss = new securesession();
//    $ss->check_browser = true;
//    $ss->check_ip_blocks = 2;
//    $ss->secure_word = 'IYO_';
//    $ss->regenerate_id = true;
//    $ss->Open();
    
    $_SESSION['userid'] = $memberid;
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    
        #This makes sure that a user agent is set to avoid error
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $useragent = " [" . $_SERVER['HTTP_USER_AGENT'] . "]";
    } else {
        $useragent = " [No user agent set]";
    }

    $engine->dblog($memberid, "login", $name, $engine->getRealIpAddr(), $useragent);
    

        
    header('Location: https://nextcashandcarry.com.ng/activatemobile');
    exit;
} else {
    
//    $ss = new securesession();
//    $ss->check_browser = true;
//    $ss->check_ip_blocks = 2;
//    $ss->secure_word = 'IYO_';
//    $ss->regenerate_id = true;
//    $ss->Open();
    
    
    if($row['active'] == 1){
        
    $engine->db_query("UPDATE members SET facebook = ? WHERE memberid = ? LIMIT 1",array($accessToken->getValue(),$memberid));    
                                

    $_SESSION['userid'] = $row['memberid'];
    $_SESSION['name'] = $row['name'];
    $_SESSION['email'] = $row['email'];
    

    #This makes sure that a user agent is set to avoid error
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $useragent = " [" . $_SERVER['HTTP_USER_AGENT'] . "]";
    } else {
        $useragent = " [No user agent set]";
    }

    $engine->dblog($_SESSION['userid'], "login", $_SESSION['name'], $engine->getRealIpAddr(), $useragent);
    
    
     //   if (empty($row['activemobile'])) {
   //  header('Location: https://nextcashandcarry.com.ng/activatemobile'); exit;
      //  }
    
    
    
    header('Location: https://nextcashandcarry.com.ng/cart');
    exit;
    }else{
 $_SESSION['error'] = strtoupper('This Account has been banned by the administrator');
  header('Location: https://nextcashandcarry.com.ng/cart');
  exit;
    }
 }


  

?>