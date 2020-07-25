<?php
 session_start();
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
 define('MAIN_DOCROOT', realpath(dirname(__file__)) . '/');
 define('SOURCE_DOCROOT', realpath(dirname(__file__)) . '/source/');
 include_once SOURCE_DOCROOT . "processor/Api.php";

 $ty = new Api();
 $first_parameter = $ty->Request();
 $second_parameter = $ty->cleanurl();

 $countpos = strpos($first_parameter['3'], ".");
 if ($countpos > 1)
 {
    $function = substr($first_parameter['3'], 0, $countpos);
 } else
 {
    $function = $first_parameter['3'];
 }
 $class = $first_parameter['2'];

 $service = new $class($function);
 $encode = $service->$function($second_parameter);
 


 switch (strtolower($second_parameter['api_type']))
 {
    case "xml":
       require_once SOURCE_DOCROOT . "processor/type/xml.php";
       break;

    case "txt":
       require_once SOURCE_DOCROOT . "processor/type/txt.php";
       break;

    case "printr":
       require_once SOURCE_DOCROOT . "processor/type/printr.php";
       break;

    case "json":
       require_once SOURCE_DOCROOT . "processor/type/json.php";
       break;

    default:
       echo "nothing";
 }
?>