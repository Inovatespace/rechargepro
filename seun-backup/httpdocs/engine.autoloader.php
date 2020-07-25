<?php      
define("APPLICATION_MODEL_DIR",dirname(__FILE__).'/engine/class/');

    function application_autoload($class){
	    $class = str_replace('_', '', strtolower($class.'.php'));
	    if(file_exists(APPLICATION_MODEL_DIR.$class)){
	        include_once(APPLICATION_MODEL_DIR.$class);
	    }
	}
    
    spl_autoload_register('application_autoload');
    $engine = new engine();
 ?>