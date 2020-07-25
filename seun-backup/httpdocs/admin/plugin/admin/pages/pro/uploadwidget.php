<?php
include "../../../../engine.autoloader.php";

$allowedExts = array("zip");
if(!empty($_FILES['ImageFile']['name'])){
$filename = $_FILES['ImageFile']['name'];
$temp = explode(".", $_FILES['ImageFile']['name']);
$extension = end($temp);
if (in_array($extension, $allowedExts))
  {
  if ($_FILES["ImageFile"]["error"] < 1)
    {
        move_uploaded_file($_FILES["ImageFile"]["tmp_name"],"../../../../tmp/" . $_FILES["ImageFile"]["name"]);
    }
  }
  
}else{
    echo "fffff";
}

function unzip_widget($ziplocation, $widgetdirectory)
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
            $zip->extractTo($widgetdirectory);
            $zip->close();
            //update db
        }

    }

   @unlink($ziplocation);

}


$ziplocation = "../../../../tmp/$filename";
$widgetfolder = "../../../../widget/";
$widgetid = 0;
$insert = 0;
$name = "";
$widgetkey = "";
$website = "";
$version = "";
$about = "";

if (file_exists($ziplocation))
{
    unzip_widget($ziplocation, $widgetfolder);
}


$xml = $widgetfolder . "/widget.xml";
if (file_exists($xml))
{
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($xml);
    $x = $xmlDoc->documentElement;
    foreach ($x->childNodes as $item)
    {
        if (strlen($item->nodeValue) > 1)
        {

            if ($item->tagName == "widgetkey")
            {
                ${$item->tagName} = $item->nodeValue;
                $row = $engine->db_query("SELECT widgetid FROM widget WHERE widgetkey = ? LIMIT 1",array($item->nodeValue));
                if (!empty($row[0]['widgetid']))
                {
                    $widgetid = $row[0]['widgetid'];
                }else{
                   $insert = 1; 
                }
                
                
            } else
            {
                ${$item->tagName} = $item->nodeValue; 
                if ($widgetid != 0)
                {
                $engine->db_query("UPDATE  widget SET $item->tagName = ? WHERE widgetid = ? LIMIT 1",array($item->nodeValue,$widgetid));
                }
            }

        }
    }


if($insert == 1){ 
$engine->db_query("INSERT INTO widget (name,widgetkey,website,version,about) VALUES(?,?,?,?,?) ",array($name,$widgetkey,$website,$version,$about));
}

    @unlink($xml);
}

?>