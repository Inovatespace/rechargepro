<?php 

require "../../../../../engine.autoloader.php";

                            $accesstoken = '';
                            $client_id = '580166836059-bgmb5rb1eguu1c9vh80dk746g7gnveo7.apps.googleusercontent.com';
                            $client_secret = '2Bu0HA5M0R9hlljYBA4L9QXB';
                            $redirect_uri = 'https://rechargepro.com.ng/theme/classic/pages/invite/gmail/callback.php';
                            $simple_api_key = 'AIzaSyB_IOM7pfIAc3OEFZZBC5LpoACPPgwJ_hg';
                            $max_results = 1000;
                            $auth_code = $_GET["code"];

                            function curl_file_get_contents($url) {
                                $curl = curl_init();
                                $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

                                curl_setopt($curl, CURLOPT_URL, $url);   //The URL to fetch. This can also be set when initializing a session with curl_init().
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);    //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
                                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);   //The number of seconds to wait while trying to connect.    

                                curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
                                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);  //To follow any "Location: " header that the server sends as part of the HTTP header.
                                curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
                                curl_setopt($curl, CURLOPT_TIMEOUT, 10);   //The maximum number of seconds to allow cURL functions to execute.
                                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //To stop cURL from verifying the peer's certificate.
                                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

                                $contents = curl_exec($curl);
                                curl_close($curl);
                                return $contents;
                            }

                            $fields = array(
                                'code' => urlencode($auth_code),
                                'client_id' => urlencode($client_id),
                                'client_secret' => urlencode($client_secret),
                                'redirect_uri' => urlencode($redirect_uri),
                                'grant_type' => urlencode('authorization_code')
                            );
                            $post = '';
                            foreach ($fields as $key => $value) {
                                $post .= $key . '=' . $value . '&';
                            }
                            $post = rtrim($post, '&');

                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
                            curl_setopt($curl, CURLOPT_POST, 5);
                            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
                            $result = curl_exec($curl);

                            curl_close($curl);

                            $response = json_decode($result);
                            if (isset($response->access_token)) {
                                $accesstoken = $response->access_token;
                                $_SESSION['access_token'] = $response->access_token;
                            }


                            if (isset($_GET['code'])) {


                                $accesstoken = $_SESSION['access_token'];
                            }

                         






$url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results=' . $max_results . '&oauth_token=' . $accesstoken;
                            $xmlresponse = curl_file_get_contents($url);
                            
                            
                            
                           // print_r($xmlresponse);  exit;

                            if ((strlen(stristr($xmlresponse, 'Authorization required')) > 0) && (strlen(stristr($xmlresponse, 'Error ')) > 0)) {
                                echo "<h2>OOPS !! Something went wrong. Please try reloading the page.</h2>";
                                exit();
                            }

                            //echo " <a href ='http://127.0.0.1/gmail_contact/callback.php?downloadcsv=1&code=4/eK2ugUwI_qiV1kE3fDa_92geg7s1DusDsN9BHzGrrTE# '><img src='images/excelimg.jpg' alt=''id ='downcsv'/></a>";
                            // echo "<h3>Email Addresses:</h3>";
                            $xml = new SimpleXMLElement($xmlresponse);
                            $xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005/Atom');

                            $result = $xml->xpath('//gd:email');


foreach ($result as $title) {
    
    
    
    //print_r($title);
    
           $name = $title->attributes()->displayName;
           $email = $title->attributes()->address;
           
           if(empty($name)){$name = $email;}
           
           
if (filter_var($email, FILTER_VALIDATE_EMAIL)){
           $row = $engine->db_query("SELECT toemail FROM myinvite WHERE toemail = ?  LIMIT 1", array($email));
           if(empty($row[0]['toemail'])){
            $engine->db_query("INSERT INTO myinvite (invite_sourse,sentemail,sentname,toemail,toname) VALUES (?,?,?,?,?)", array("google",$engine->get_session("rechargeproemail"),$engine->get_session("name"),$email,$name));
           }
           
           }
                                    }
                                    
                                    
                                    
                                    unset($_SESSION['access_token']);
            echo "<meta http-equiv='refresh' content='0;url=https://rechargepro.com.ng/invite'>"; exit;
                                    ?>
