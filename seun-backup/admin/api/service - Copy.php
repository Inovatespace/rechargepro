<?php
include "../engine.autoloader.php";

$version = $_REQUEST['u'];
define("CLASS_MODEL_DIR","source/" . $version."/");
function class_autoload($class)
{
    $class = str_replace('_', '', strtolower($class . '.php'));
    if (file_exists(CLASS_MODEL_DIR. $class)) {
        include_once (CLASS_MODEL_DIR. $class);
    }
}

spl_autoload_register('class_autoload');


class Api{}

$class_array = scandir(CLASS_MODEL_DIR);
$classarray = array();
foreach ($class_array as $class) {

if($engine->getExtension($class) == "php"){
    $explode = explode(".", $class);
    $classarray[] = $explode[0];
}
}


$class = $classarray[2]; //the dynamic
$classmethod = get_class_methods($class);
$method = $classmethod[1];// the dynamic
$methodkey = array_search($method,$classmethod);
$nextkey = $methodkey+1;
if(!key_exists($nextkey,$classmethod)){
  $nextkey = $methodkey;  
}

$methodcontent = file_get_contents(CLASS_MODEL_DIR.$class.".php");
$methodcontent = htmlentities($methodcontent);
$explode = explode("$method(",$methodcontent);
$methodcontent = $explode[1];
$explode = explode("$method[$nextkey](",$methodcontent);
$methodcontent = $explode[0];
$methodcontent = str_ireplace('"',"'",$methodcontent);


$matches = array();
preg_match_all("/parameter[[]['][A-Z a-z 0-9]{0,}/",$methodcontent,$matches);

$parameters = array();
foreach($matches[0] AS $value){
    $explode = explode("parameter['",$value);
    $parameters[] = trim($explode[1]);
}

$parameters = array_unique($parameters);


print_r($parameters);

?>