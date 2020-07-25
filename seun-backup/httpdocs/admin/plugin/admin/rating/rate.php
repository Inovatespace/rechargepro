<?php

if (!isset($_SESSION))
{
    session_start();
}
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
//@setrawcookie( "vote", "", $inTwoMonths, "/", false, NULL, 1 );

define('AUTH_RATEROOT', realpath(dirname(__file__)) . '/');
//define('SEMEC_ROOT', dirname(dirname(dirname(__file__))) . '/');

//if (!defined('MAIN_ROOT')) {
//include_once dirname(dirname(dirname(__FILE__))). '/resources.php';	
//}
include_once  dirname(dirname(dirname(dirname(__FILE__)))). '/resource.php';

class rate extends resources
{

    public function __construct()
    {

        print self::dependables();
        if (isset($_REQUEST['name']) && isset($_REQUEST['type']))
        {
            print self::vote();
        }

    }

    function dependables()
    {
        $result = '
        <script type="text/javascript" src="plugin/admin/rating/rate.js"></script>
        <link rel="stylesheet" type="text/css" href="plugin/admin/rating/poll.css"/>
        
        ';
        return $result;
    }


    public function vote()
    {
       
       $CONNRATE = self::sql_db();
       

        $voteid = $_REQUEST['name'];
        $type = $_REQUEST['type'];
        
        $ratename = $_REQUEST['ratename'];
        $rateid = $_REQUEST['rateid'];
        
        if ($type == "yes" || $type == "no")
        {
            $result = $CONNRATE->prepare("SELECT userid FROM rateuser WHERE userid =? AND id=? LIMIT 1");
            $result->execute(array(self::get_session("userid"),$voteid));
            $count = $result->rowCount();
            if ($count == 0)
            {
  $CONNAPP = self::sql_db();              
if ($ratename == "W") {
$result2 = $CONNAPP->prepare("UPDATE widget SET $type = $type + ? WHERE id = ? LIMIT 1");
$result2->execute(array('1',$rateid));
	} else {
$result2 = $CONNAPP->prepare("UPDATE app_list SET $type = $type + ? WHERE appkey = ? LIMIT 1");
$result2->execute(array('1',$rateid));
}
	



                $result3 = $CONNRATE->prepare("INSERT INTO rateuser (userid,id) VALUE (?,?)");
                $result3->execute(array(self::get_session("userid"),$voteid));
            }# else
#            {
#                return $count.'<div class="downshadow" style="z-index:3; text-align:center; border:solid 1px #CCCCCC; width:120px; overflow:hidden; padding:5px; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; color:black; background-color:white; position:absolute;"> <div style="float:left; width:110px; overflow:hidden;">You can not rate a product more than once</div> <div style="float:right; width:10px;"><img src="images/icon/xlose.png" width="8" height="8" /></div></div>';
#            }


        }


    }

    public function convert($number)
    {

        if ($number < 999)
        {
            $number = $number;
        } elseif ($number < 999999)
        {
            $number = round($number / 1000, 1) . 'K';
        } elseif ($number < 999999999)
        {
            $number = round($number / 1000000, 1) . 'M';

        } elseif ($number > 1000000000)
        {
            $number = round($number / 1000000000, 1) . 'B';
        }

        return $number;

    }


    public function showrate($pollid, $theme = "1plus", $yes =0, $no = 0, $show = "1", $narate, $ratetype)
    {
        $CONNRATE = self::sql_db();
        
        $pollid = trim($pollid);
      

            $disabled = '';
            $value = 'a';
            $color1 = '#68B100';
            $color2 = '#B02712';


        $parameter = array(
            $yes,
            $no,
            $pollid,
            $disabled,
            $value,
            $color1,
            $color2,
            $narate);
        $calltheme = "theme" . $theme;
        return self::$calltheme($parameter,$show,$ratetype);

    }







    public function themestar($parameter,$show,$ratetype)
    {
        if ($parameter[0] - $parameter[1] > 84)
        {
            $width = 84;
        } else
        {
            $width = $parameter[0] - $parameter[1];
        }
        
        if ($width < 1) {$width = 1;}
        
        if ($show == "1") {$theshow = '<input type="hidden" id="rateid" name="rateid"" value="'.$parameter[7].'" /><input type="hidden" id="ratename" value="'.$ratetype.'" /><input class="step step'.$parameter[2].'" ' . $parameter[3] . ' theid="'.$parameter[7].'" id="Yes'.$parameter[2].'" title="yes" name="' . $parameter[2] .
            '" style="float: left; margin-right:5px;" value="Yes" type="image" src="plugin/admin/rating/images/r' .
            $parameter[4] . '1.png" />  
<input class="step step'.$parameter[2].'" ' . $parameter[3] . ' theid="'.$parameter[7].'"  id="No'.$parameter[2].'" title="no" name="' . $parameter[2] .
            '" style="float: left; margin-right:5px;" type="image" src="plugin/admin/rating/images/l' .
            $parameter[4] . '1.png" value="No" /> 
<div id="increment'.$parameter[2].'" style="float:left; overflow:hidden;">' .
            self::convert($parameter[0] - $parameter[1]) . '</div>
<div style="margin-left:5px; float: left;">Points</div>';}else{$theshow = "";}

        $param = '
                <div id="spinner'.$parameter[2].'"></div>
        <div id="background'.$parameter[2].'" style="height:20px; position: relative; float:left; background:url(plugin/admin/rating/images/star2.png) no-repeat 0 0; width:' .
            $width . 'px;">  
<div style="margin-bottom:5px; height:17px; overflow:hidden; width:139px; background:url(plugin/admin/rating/images/star1.png) no-repeat 0 0;"></div>
</div>
<div style="clear:both; overflow: hidden;">
'.$theshow.'
</div>';
        return $param;

    }





}


$rate = new rate();
?>

