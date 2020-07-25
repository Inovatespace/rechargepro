<?php
class Cookie
{

    public function __construct()
    {
    }

    public function exists($name)
    {
        if(isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }else{
        return false;
        }
    }


    public function save($name, $value = null, $expire = null, $path = null, $domain = null,
        $secure = null, $httponly = null)
    {
        return setrawcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }


    public function delete($name)
    {
        //setcookie($name, false, time() - 60*100000);
        //setrawcookie($name, "", 0, "/", "", false);
        setrawcookie($name, "", time() - 60*100000, "/", ".splvending.com", null, null);
        //unset($_COOKIE[$name]);
        //setcookie($name, '', time() - 3600);
        //setrawcookie($name, "", time() - 3600);
    }
}
?>
