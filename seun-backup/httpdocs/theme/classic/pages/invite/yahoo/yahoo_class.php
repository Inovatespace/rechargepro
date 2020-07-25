<?php
	class yahoo_class
    {
        protected static $oauthConsumerKey ="dj0yJmk9MU9yeklNNlFab2ZIJnM9Y29uc3VtZXJzZWNyZXQmc3Y9MCZ4PTA4";
        protected static $OauthConsumerSecret ="0199a4b1fedbe7e9c5bc54720b01b3e60b908fd5";
        protected static $oauthDomain="https://rechargepro.com.ng/theme/classic/pages/invite/yahoo/yahoo.php";

        public function __construct(){
            //Check Session is Start Or not 
            if (session_status() == PHP_SESSION_NONE) {
                        session_start();
            }

        }

       /**
         * Authentication user And Access Refresh and access token
         *
         * @author <Pawan Kumar>
         * @return type boolean
         **/
        function getAuthorization($code)
       {
            $url = "https://api.login.yahoo.com/oauth2/get_token";

            $data="grant_type=authorization_code&redirect_uri=".self::$oauthDomain."&code=".$code;
            $auth =  base64_encode(self::$oauthConsumerKey.":".self::$OauthConsumerSecret);  

            $headers = array(
                 'Authorization: Basic '.$auth,
                 'Content-Type: application/x-www-form-urlencoded'
            );

            try{
                $resultSet =self::makeRequest($url,$data,$headers);
                if($resultSet->access_token){
                    $this->setAccessToken($resultSet->access_token);
                    $this->setRefreshToken($resultSet->refresh_token);
                    $this->setGuidToken($resultSet->xoauth_yahoo_guid);
                    return true;
                }
            }catch(Exception $ex){
                throw($ex);
            }

       }
        /**
         * Get All Contacts list From Yahoo API using Auth Access Token And oAuth Guid Token
         *
         * @author <Pawan Kumar>
         * @return type Object
         **/
        public function getUserContactsDetails()
        {
            /** Refresh Access Token is Expired **/
            $this->generateAccessToken();

            $guid  =$this->getGuidToken(); 
            $token =$this->getAccessToken();

            $contactUrl="https://social.yahooapis.com/v1/user/$guid/contacts?format=json";

            $opts = array(
                      'http'=>array(
                        'method'=>"GET",
                        'header'=>"Authorization: Bearer $token" 
                      )
                    );

            $context = stream_context_create($opts);
            $file = file_get_contents($contactUrl, false, $context);

            $output =json_decode($file);
            return $output;
        }

        /**
         * Get New Access Token using Refresh Token
         *
         * @author <Pawan Kumar>
         * @return type boolean
         **/
        protected function generateAccessToken()
        {

            $url = "https://api.login.yahoo.com/oauth2/get_token";

            $refreshToken = $this->getRefreshToken();
            $data="grant_type=refresh_token&redirect_uri=".self::$oauthDomain."&refresh_token=".$refreshToken;

            $auth =  base64_encode(self::$oauthConsumerKey.":".self::$OauthConsumerSecret);  
            $headers = array(
                 'Authorization: Basic '.$auth,
                 'Content-Type: application/x-www-form-urlencoded'
            );

            try{

                $resultSet =self::makeRequest($url,$data,$headers);

                if($resultSet->access_token){
                    $this->setAccessToken($resultSet->access_token);
                    return true;
                }else{
                    return false;
                }
            }catch(Exception $ex){
                throw($ex);
            }

        }

        /**
         * Build a login url using oAuth Consumber Key And Redirect Domain
         *
         * @author Pawan Kumar
         * @return type String
         **/
        public static function getLoginUrl()
        {
           $loginUrl = "https://api.login.yahoo.com/oauth2/request_auth";
           $buildUrl =$loginUrl."?client_id=".self::$oauthConsumerKey."&redirect_uri=".self::$oauthDomain."&response_type=code&language=en-us"; 
           return $buildUrl;
        }

        /**
         * Make  a Remote Post Request using MakeRequest Function
         *
         * @param Url String
         * @param $postData String Send Post Data With Request
         * @param headers Array Contain Auth basic information
         * @author Pawan Kumar
         * @return type Object
         **/

        public static function makeRequest($url,$postData,$headers){

            try{

                if (empty($url))throw new Exception("Url is Not Format."); 
                if (empty($postData))throw new Exception("Post Parameters is Not Defined");

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$postData);
                curl_setopt($ch, CURLOPT_URL,$url);

                $result = curl_exec($ch);
                $output =json_decode($result);

                return $output;

            }catch(\Exception $ex){
                throw($ex);
            }

        }

        /**
         * @param RefreshToken to set String Token Into Session
         */
        public function setRefreshToken($token)
        { 
          $_SESSION['refresh_token']=$token;
        }

        /**
         * @return String Refresh Token From Session
         */
        public function getRefreshToken()
        { 
            return $_SESSION['refresh_token'];
        }

        /**
         * @param AccessToken to set String Token into Session
         */
        public function setAccessToken($token)
        { 
            $_SESSION['access_token']=$token;
        }

        /**
         * @return String Access Token From Session
         */
        public function getAccessToken()
        {
            return $_SESSION['access_token'];
        }

        /**
         * @param GuidToken to set String Token into Session
         */
        public function setGuidToken($token)
        {
            $_SESSION['xoauth_yahoo_guid']=$token;
        }
        /**
         * @return String Guid Token from Session
         */
        public function getGuidToken()
        {
            return $_SESSION['xoauth_yahoo_guid'];
        }

    }


    // Initialize Session If Session is Not Start
    if(!$_SESSION){
    session_start();
    }

    if(isset($_GET['code'])){
        $code = $_GET['code'];
        if(!empty($code)){
            // create a instance of yahoo contacts
            $obj = new yahoo_class();
            //Successfully Authorization Process
            $obj->getAuthorization($code); 
            Header("Location:https://rechargepro.com.ng/yahoo");die;
        }
    }else{
        if(isset($_SESSION['access_token'])){

            // create a instance of yahoo contacts
            $obj = new yahoo_class();

            //After Authorization Get User Contacts Email
            $res =  $obj->getUserContactsDetails(); 
            
            $ct = $res->contacts->contact;
            $contactcount =  count($ct);
            for($i=0;$i<$contactcount;$i++){
                
           $name = $ct[$i]->fields[0]->value->givenName;
           $email = $ct[$i]->fields[1]->value; 
           
           if(empty($name)){$name = $email;}
           
           
if (filter_var($email, FILTER_VALIDATE_EMAIL)){
           $row = $engine->db_query("SELECT toemail FROM myinvite WHERE toemail = ?  LIMIT 1", array($email));
           if(empty($row[0]['toemail'])){
            $engine->db_query("INSERT INTO myinvite (invite_sourse,sentemail,sentname,toemail,toname) VALUES (?,?,?,?,?)", array("yahoo",$engine->get_session("rechargeproemail"),$engine->get_session("name"),$email,$name));
           }
           
           }
           
           
            }
           
            
            unset($_SESSION['access_token']);
            
            
            echo "<meta http-equiv='refresh' content='0;url=https://rechargepro.com.ng/invite'>"; exit;
        }else{
            $url = yahoo_class::getLoginUrl();
            ?>
            <link href="/theme/classic/pages/invite/yahoo/css/style.css" rel="stylesheet" type="text/css"/>
        <div id="main" style="text-align: center;">
            <h1>Import Yahoo Contacts</h1>
            <div id="login">
                <h2>Yahoo Sign-in</h2>
                <hr/>
                <a id="signin" href="<?php echo $url; ?>"><img src="/theme/classic/pages/invite/yahoo/images/sign-in-with-yahoo.png"/></a>
            </div>
        </div>
            <?php
           // echo "<center><strong><a href='$url'>Login With Yahoo Mail !</a></strong></center>";
        }

    }
?>