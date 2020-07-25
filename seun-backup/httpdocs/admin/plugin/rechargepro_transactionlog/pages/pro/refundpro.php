<?php
include "../../../../../engine.autoloader.php";
$tid = htmlentities($_REQUEST['tid']);


class Api
{
    
    public function db_query($querry, $array, $bool = false) //query/array/count
    {
       $engine = new engine();
       $row = $engine->db_query($querry, $array, $bool);
       return $row;
    }
    
    
    function notification($rechargeproid_main, $message, $type = 0)
    {
       $engine = new engine();
       $row = $engine->notification($rechargeproid_main, $message, $type);
       return $row;
    }

    function config($key, $default = null)
    {
       $engine = new engine();
       $row = $engine->config($key, $default);
       return $row;
    }
    
}
    

 include "../../../../../api/source/pro/refund.php";
 $refund = new refund("POST");
 $myrefund = $refund->refund_now(array("tid"=>$tid));
        
?>