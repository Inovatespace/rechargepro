<?php

include_once "../../../../engine.autoloader.php";


$pluginfolder = "../../../../plugin/";
$id = $_REQUEST['id'];

$row = $engine->db_query("SELECT pluginkey FROM plugin WHERE pluginid = ? LIMIT 1",array($id));
$pluginkey = $row[0]['pluginkey'];


$xml = $pluginfolder . $pluginkey . "/uninstall.xml";
if (file_exists($xml))
{
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($xml);
    $x = $xmlDoc->documentElement;
    foreach ($x->childNodes as $item)
    {
        if (strlen($item->nodeValue) > 1)
        {

            $theswitch = trim($item->tagName);
            switch ($theswitch)
            {

                case "api":
                    unlink("../../../../api/source/$item->nodeValue");
                    break;


                default:
                    $thetable = trim($item->nodeValue);
                    $row = $engine->db_query("DROP TABLE $thetable",array());
            }

        }
    }

}

function nadir($thefile, $sentarray)
{

    $location = $thefile . "/";
    $invalidfile = array(".", "..");


    $dir = scandir($location);
    foreach ($dir as $thefileb)
    {
        if (!in_array($thefileb, $invalidfile))
        {
            if (is_dir($location . $thefileb))
            {
                $newdir = $location . $thefileb;
                // echo $newdir . "<br />";
                $sentarray[] = $newdir;
                $sentarray = nadir($newdir, $sentarray);

            } else
            {
                @unlink($location . $thefileb);
            }

        }
    }

    return $sentarray;
}



if (empty($pluginkey) || $pluginkey == "/" || $pluginkey == ".")
{
    exit;
}


$location = $pluginfolder . $pluginkey . "/";
$sentarray = array($location);
$invalidfile = array(".", "..");
$dir = scandir($location);
foreach ($dir as $thefile)
{
    if (!in_array($thefile, $invalidfile))
    {
        if (is_dir($location . $thefile))
        {
            $newdir = $location . $thefile;
            $sentarray[] = $newdir;
            //echo $newdir . "<br />";
            $sentarray = nadir($newdir, $sentarray);


        } else
        {
            @unlink($location . $thefile);
        }
    }

}


rsort($sentarray);
foreach ($sentarray as $theltod)
{
    $todel = $theltod;
    if (file_exists($todel))
    {
        rmdir($todel);
    }
}

if (file_exists($location))
{
    rmdir($location);
}


if (!file_exists($location))
{
$engine->db_query("DELETE FROM plugin WHERE pluginid = ? LIMIT 1",array($id));
$engine->db_query("DELETE FROM admin_plugin WHERE pluginid = ?",array($id));
}
?>