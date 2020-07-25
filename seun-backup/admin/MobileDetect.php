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
 
class mobiledetector
{
    #some device does not set user agent you can set default action for this device by setting the user agent
    private $useragent = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0";

    #you can skip this arrays for the main time 
    #If you want to be more specific about a device all you need to do is add it to the array list from the top
    #Example 
    #'iphone4' => 'iPhone OS 4_0'
    #Or
    #'iphone' => '(iPhone OS 4_0|iPhone OS 3_0)'
        
    private $devices = array(
        "blackberry"    => "blackberry",
        "android"       => "android",
    	"ipad"			=> "ipad",
        "opera"         => "(opera mini|opera mobi)",
        "iphone"        => "(iphone|ipod)",
        "palm"          => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino|webos)",
        "windows"       => "(iemobile|smartphone|windows phone|ppc)",
        "generic"       => "(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)"
         );
         
    public function device( $device_name, $key )
    {

        #This makes sure that a user agent is set to avoid error
        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) )
        {
            $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        } else
        {
            $_SERVER['HTTP_USER_AGENT'] = $this->useragent;
        }

        #I use preg_match to see if the name exists in the user agent
        #This method works 99% of the time
        #The remaining 1% is when the user fakes the user agent
        $preg = preg_match( "/".strtolower( $key )."/i", $_SERVER['HTTP_USER_AGENT'] );

        #most mobile device always have mobile on their user agent. 
        #to avoid getting two result we pick only one if the result is both generic and another device.
        #that is why you must add any new list at the top of the array
        if ( $device_name == 'generic' && $preg == true )
        {
            return false;
        } else
        {

            if ( $preg )
            {
                return true;
            }

        }

    }


    public function mobile()
    {
        
        #Split the array into some thing we can work with
        foreach ( $this->devices as $device_name => $key )
        {
            #if the result is positive show the device as in the case of generic above
            if ( self::device( $device_name, $key ) )
            {
                return $device_name;
            }


        }
        return "others";
    }


}

?>