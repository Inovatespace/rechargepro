<?php

include "../../../../engine.autoloader.php";
$logger = $engine->log_me();



$allowedExts = array("zip");
if (!empty($_FILES['ImageFile']['name']))
{
    $filename = $_FILES['ImageFile']['name'];
    $temp = explode(".", $_FILES['ImageFile']['name']);
    $extension = end($temp);
    if (in_array($extension, $allowedExts))
    {
        //$logger->log($_FILES["ImageFile"]["error"]);
        if ($_FILES["ImageFile"]["error"] < 1)
        {
            move_uploaded_file($_FILES["ImageFile"]["tmp_name"], "../../../../tmp/" . $_FILES["ImageFile"]["name"]);
        }
    }

} else
{
    $logger->log("File Empty");
}

function unzip_theme($ziplocation, $themedirectory)
{
    $zip = new ZipArchive;
    $continue = 0;
    if ($zip->open($ziplocation))
    {
        for ($i = 0; $i < $zip->numFiles; $i++)
        {
            $zip->getNameIndex($i);
            $continue = 1;
        }
    }

    if ($continue == 1)
    {
        $res = $zip->open($ziplocation);
        if ($res === true)
        {
            $zip->extractTo($themedirectory);
            $zip->close();
            //update db
        }

    }

    @unlink($ziplocation);

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
                //  echo $newdir . "<br />";
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

$ziplocation = "../../../../tmp/$filename";
$themefolder = "../../../../theme/";

if (file_exists($ziplocation))
{
    unzip_theme($ziplocation, $themefolder);
}




?>