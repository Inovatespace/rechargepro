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
        if ($_FILES["ImageFile"]["error"] < 1)
        {
            move_uploaded_file($_FILES["ImageFile"]["tmp_name"], "../../../../tmp/" . $_FILES["ImageFile"]["name"]);
        }
    }

} else
{
    $logger->log("File Empty");
}

function unzip_plugin($ziplocation, $plugindirectory)
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
            $zip->extractTo($plugindirectory);
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
$pluginfolder = "../../../../plugin/";
$pluginid = 0;
$insert = 0;
$name = "";
$pluginkey = "";
$website = "";
$version = "";
$about = "";

if (file_exists($ziplocation))
{
    unzip_plugin($ziplocation, $pluginfolder);
}


$xml = $pluginfolder . "/install.xml";
if (file_exists($xml))
{
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($xml);
    $x = $xmlDoc->documentElement;
    foreach ($x->childNodes as $item)
    {
        if (strlen($item->nodeValue) > 1)
        {

            if ($item->tagName == "intern" && $engine->config("version") < $item->nodeValue)
            {
                $logger->log("Plugin Not compatible with current version Please upgrade First");
                exit;
            }
            $theswitch = trim($item->tagName);
            switch ($theswitch)
            {
                case "pluginkey":
                    ${$item->tagName} = $item->nodeValue;
                    $row = $engine->db_query("SELECT pluginid FROM plugin WHERE pluginkey = ? LIMIT 1",array($item->nodeValue));
                    if (!empty($row[0]['pluginid']))
                    {
                        $pluginid = $row[0]['pluginid'];
                    } else
                    {
                        $insert = 1;
                    }

                    break;

                case "api":
                    $apiFile = $pluginfolder . "install/" . $item->nodeValue;
                    copy($apiFile, "../../../../api/source/$item->nodeValue");

                    break;

                case "sql":
                    $sqlFile = $pluginfolder . "install/" . $item->nodeValue;
                    $newImport = new sqlImport($sqlFile);
                    $newImport->import();
                    break;

                default:
                    ${$item->tagName} = $item->nodeValue;
                    if ($pluginid != 0 && $item->tagName != "sql" && $item->tagName != "api")
                    {
                        $engine->db_query("UPDATE  plugin SET $item->tagName = ? WHERE pluginid = ? LIMIT 1",array($item->nodeValue, $pluginid));
                    }
            }

        }
    }


    if ($insert == 1)
    {
        $engine->db_query("INSERT INTO plugin (name,pluginkey,website,version,about) VALUES(?,?,?,?,?) ",array(
            $name,
            $pluginkey,
            $website,
            $version,
            $about));
    }

    @unlink($xml);


    if (file_exists($pluginfolder . 'install/'))
    {

        $location = $pluginfolder . "install/";
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
                    // echo $newdir . "<br />";
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

    }

}

?>