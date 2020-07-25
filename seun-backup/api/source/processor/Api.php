<?php
/**
 * @author seun makinde williams (seuntech)
 * @copyright 2011
 * @version 1.0
 * @tutorial for help or more script visit www.1plusalltutor.com
 * @link http://www.1plusalltutor.com
 * @license * This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 */

 define('SERVER_DOCROOT', realpath(dirname(dirname(dirname(dirname(__file__))))) . '/');
 require_once SERVER_DOCROOT . "/engine.autoloader.php";

 class Api extends engine
 {


    public static function block_ip()
    {
       
       if (self::api_config('enable_block_ip'))
       {
          $config = parse_ini_file(MAIN_DOCROOT . "ip.ini", true);
          $blocked = self::api_config('block');

            $blocked = explode(',', $config[$key]);
          if (in_array($_SERVER['REMOTE_ADDR'], $blocked))
          {
             throw new Logger("",Logger::BLOCKED);
          }
       }
    }
    
    public static function api_config($key, $default = null)
    {
       static $api_config;

       if ($api_config === null)
       {
          $api_config = parse_ini_file(MAIN_DOCROOT . "ip.ini", true);
       }

       return (isset($api_config[$key])) ? $api_config[$key] : $default;
    }
    
    

    public function RequestLimit()
    {
       if (!isset($_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]))
       {
          $_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['hits'] = 0;
          $_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['first'] = time();
       } else
       {
          if ((time() - $_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['first']) >=
             self::api_config('max_request_time'))
          {
             // Reset vars
             $_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['hits'] = 0;
             $_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['first'] = time();
          }
       }

       // Set current time in session
       $_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['last'] = time();
       $_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['hits']++;

       // Check if max requests reached
       if ($_SESSION['requestlimit'][$_SERVER['REMOTE_ADDR']]['hits'] > self::api_config('max_request_hits'))
       {
          throw new Logger("",Logger::TO_MANY_REQUESTS);
       }

    }


    public function Request()
    {

       $pageURL = 'http';
       if (isset($_SERVER["HTTPS"]) == "on")
       {
          $pageURL .= "s";
       }
       $pageURL .= "://";
       if ($_SERVER["SERVER_PORT"] != "80")
       {
          $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
       } else
       {
          $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
       }

       if (!isset($_GET['url_param']))
       {
          echo "some thing is missing";
          exit();
       }
       
       //$api_url = self::api_config('url');
       //$this->endpoint_url = $api_url;
       //$this->endpoint_path = str_replace($pageURL, "", $api_url);


       //$_SERVER["REQUEST_URI"] = str_replace($api_url, "", $pageURL . $_SERVER["REQUEST_URI"]);
       $apiPos = strpos($pageURL, "api")+3;
      $_SERVER["REQUEST_URI"] =  substr($pageURL, $apiPos, 500);
       $_SERVER["REQUEST_URI"] = str_replace(array("%2f", "%2F", "%252f", "%252F"),
          "--slash--", $_SERVER["REQUEST_URI"]);
       $extension_pos = strrpos($_SERVER["REQUEST_URI"], ".");
       $_SERVER["REQUEST_URI"] = substr($_SERVER["REQUEST_URI"], 0, $extension_pos);
       $this->url_data = explode("/", trim("0" . $_SERVER["REQUEST_URI"], "/"));
       
if($this->url_data['1'] == "local"){$this->url_data['1'] = "pro";}

       if (!file_exists(SOURCE_DOCROOT . $this->url_data['1']))
       {
          throw new Logger("",Logger::VERSION_NOT_FOUND, $this->url_data['1']);
       }

       if (!file_exists(SOURCE_DOCROOT . $this->url_data['1'] . "/" . $this->url_data['2'] .
          ".php"))
       {
          throw new Logger("",Logger::SERVICE_NOT_FOUND, $this->url_data['2']);
       }

       require_once SOURCE_DOCROOT . $this->url_data['1'] . "/" . $this->url_data['2'] .
          ".php";

       $countpos = strpos($this->url_data['3'], ".");
       if ($countpos > 1)
       {
          $function = substr($this->url_data['3'], 0, $countpos);
       } else
       {
          $function = $this->url_data['3'];
       }


       if (!method_exists($this->url_data['2'], $function))
       {
          throw new Logger("",Logger::FUNCTION_NOT_FOUND, $function);
       }


       #print_r($this->url_data); exit;
       return $this->url_data;
    }


    function Api_Method($function, $method, $call)
    {

       if ($call == $function && $_SERVER["REQUEST_METHOD"] != $method)
       {
          throw new Logger("",Logger::REQUEST_METHOD_DISABLED, $_SERVER["REQUEST_METHOD"]);
       }

    }


    function cleanurl()
    {
       $this->params = $_FILES;

       $counter = 0;
       foreach ($this->params as $key => $value)
       {
          $this->params[$key] = isset($this->url_data[$counter]) ? $this->url_data[$counter] :
             "";
          $counter++;
       }

       $url_data_num = count($this->url_data);
       for ($i = $counter; $i < $url_data_num; $i += 2)
       {
          $this->params[$this->url_data[$i]] = isset($this->url_data[($i + 1)]) ? $this->
             url_data[($i + 1)] : "";
       }

       $this->params = array_merge($this->params, $_GET);

       $this->params = array_merge($this->params, $_POST);
       return $this->params;

    }



 


 }



?>