<?php $engine = new engine();?>
<br /><br />
<strong style="font-size: 30px;">Parameter</strong><br />
<strong>Category:</strong> 1,2,3;<br />
<strong>eBook</strong> 1<br />
<strong>Message</strong> 2<br />
<strong>Music</strong> 3<br />
<strong>All</strong> 0<br /><br />


<br /><br /><br />


There are two ways to extend this application<br />
<strong>Method 1:</strong> Access the methods directly in your code (Reponses is in array)<br />
<strong>Method 2:</strong> API (response is in 4 types)<br /><br />

<strong>Method 1</strong><br />
Include "<strong>engine.autoloader.php</strong>" in your code, the file is located at the root directory<br />
call $engine->method();<br />
example: $engine-> check_student_result(array("pin"=>" 1234","reg_number"=>" NOU164047531","year"=>"2017","term"=>"1"));<br />
The example above shows how to view student result for list of available method visit the <a href="help&p=api">API section</a><br /><br />

<strong>Method 2</strong><br />
Call the method like you would for a normal link. <br />
You can access the method with GET or POST<br /><br />
Example<br />
https://shuzia.com/api/v1/ios/downloads.json<br />
<strong>POST</strong><br />
A sample Post Script hass been provided below.<br />
to see the post parameter visite <a href="help&p=api">API section</a>
<br />
<strong>GET</strong><br />
https://shuzia.com/api/v1/ios/downloads/page/parameter/per_page/parameter/search/parameter/category/parameter.json<br /><br />
NOTE: the extension on the link.  The extension determines the response<br />
Available response (.json, .txt, .xml, . printr)<br />
A Sample Script is found below<br /><br />




<strong>Configuration</strong><br />
The system is compatible with the following database (MYSQL, SQLITE, MSSQL, ORACLE, ACCESS etc.)<br />
The PDO settings will have to be adjusted manually for switch in database.<br />
The configuration setting can be found in the config folder.<br />
The config file also contains other setting, that include API refresh rate, site information, theme etc. <br />
Additional configuration can be added<br /><br />





<pre>
function post($data, $url)
    {
        $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
        $compression = "gzip";
        $proxy = '';
        $cookies = true;
        $cookie_file = 'cookies.txt';
        
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($process, CURLOPT_HEADER, 1);
        //curl_setopt($process, CURLOPT_INTERFACE, "74.208.110.191");
        curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
        if ($cookies == true)
            curl_setopt($process, CURLOPT_COOKIEFILE, $cookie_file);
        if ($cookies == true)
            curl_setopt($process, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($process, CURLOPT_ENCODING, $compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if ($proxy)
            curl_setopt($process, CURLOPT_PROXY, $proxy);


        $type = gettype($data);
        if ($type == "array") {
            $data = array_string($data);
        }
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($process, CURLOPT_POST, 1);
        $return = curl_exec($process);
        curl_close($process);
        return $return;
    }
    
    
        function array_string($postData)
    {
        $topost = "";
        $count = count($postData);
        $keys = array_keys($postData);
        $value = array_values($postData);
        for ($i = 0; $i < $count; $i++) {
            $topost .= "&$keys[$i]=" . urlencode($value[$i]);
        }
        return $topost;
    }
    

 $postData = array("regnumber"=>"NOU164047531","key"=>"4586974HF8","term"=>"1","year"=>"2017","subject" =>"Biology","grade" =>"89","position" =>"5","remark" =>"good");
 $data = post($postData,'http://localhost/test/api/v1/result/upload_student_result.json');
print_r($data);</pre>