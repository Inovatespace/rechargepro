<?php
include_once "../../../../engine.autoloader.php";
$themelocation = $engine->config("theme_folder");

$theme = $_REQUEST['name'];
$sitewidth = "100%";
$allow_widgetstate = "false";
$icon_size = "2.0";

$xml = "../../../../".$themelocation.$theme."/parameter.xml";
if (file_exists($xml)) {
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($xml);

    $x = $xmlDoc->documentElement;
    foreach ($x->childNodes as $item) {
        if (strlen($item->nodeValue) > 1) {
            if ($item->tagName == "width") {
                $sitewidth = trim($item->nodeValue);
            }

            if ($item->tagName == "dashbord_setting") {
                $allow_widgetstate = trim($item->nodeValue);
            }
            
              if ($item->tagName == "icon_size") {
                $icon_size = trim($item->nodeValue);
            }
          

        }
    }
}

$sitetheme = "
'theme'=>'$theme',
";

$site_width = "
'site_width'=>'width:".$sitewidth."', 
";

$widgetstate = "
'user_widget_state_change'=>$allow_widgetstate,
";


$icon_size = "
'icon_size'=>$icon_size,
";

$configarray = array('ICON_SIZE CONFIGURATION'=>$icon_size,'THEME CONFIGURATION'=>$sitetheme,'SITEWIDTH CONFIGURATION'=>$site_width,'USERWIDGET CONFIGURATION'=>$widgetstate,'ENDING CONFIGURATION'=>";");

configeditor($configarray);
function configeditor($configarray)
{

    $file = "../../../../config/config.php";


    $fh = fopen($file, 'r');
    $data = fread($fh, (filesize($file)+1000));
    fflush($fh);
    fclose($fh);



foreach($configarray AS $keyword => $config){
    $pattern = "/\/\* $keyword START \*\/(\s*)(.*?)(\s*)\/\* $keyword END \*\//is";
    $replacement = "/* $keyword START */".$config."/* $keyword END */";
    $data = preg_replace($pattern, $replacement, $data);
}
    
    
    

    if (is_writable($file)) {
        if (!$handle = fopen($file, 'w')) {
            echo "Cannot open file ($file)";
            exit;
        }

        if (fwrite($handle, $data) === false) {
            echo "Cannot write to file ($file)";
            exit;
        }
fflush($handle);
        fclose($handle);

    }
    else {
        echo "The file $file is not writable. Please CHMOD config.php to 777.";
        exit;
    }


}

?>