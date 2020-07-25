<?php
include_once "../../../../engine.autoloader.php";

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

$id = $_REQUEST['id'];

$row = $engine->db_query("SELECT widgetkey FROM widget WHERE widgetid = ? LIMIT 1",array($id));
$widgetkey = $row[0]['widgetkey'];

$engine->db_query("DELETE FROM widget WHERE widgetid = ? LIMIT 1",array($id)); 
$engine->db_query("DELETE FROM admin_widget WHERE widgetid = ?",array($id));



$widgetfolder = '../../../../widget/'; // get all file names
$location = $widgetfolder . $widgetkey . "/";
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
?>