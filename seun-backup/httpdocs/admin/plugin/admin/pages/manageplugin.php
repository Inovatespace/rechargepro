<?php
require "../../../engine.autoloader.php";


$row = $engine->db_query("SELECT admin_plugin.permission FROM admin_plugin WHERE pluginid = ? AND adminid = ? LIMIT 1",array($_REQUEST['u'],$_REQUEST['id']));
$fromdb = $row[0]['permission'];
$fromdb = explode(",",$fromdb);
$temparray = array();
foreach($fromdb AS $newarray){
 if(!empty($newarray)){
  $explode = explode("=",$newarray);
  $temparray[$explode[0]] = $explode[1];  
 }  
}

$arraykey = array_keys($temparray);
$arrayvalue = array_values($temparray);
?>

<div class="barmenu" style="padding:5px; margin: -15px -5px 0px -5px; text-align:left;">Plug-In Manager {<?php echo $_REQUEST['n'];?>}</div>
<div class="profilebg shadow" style="font-weight:bold; text-align:left; margin: 5px; padding:5px; overflow:hidden;">
<div class="whitelink" style="float: left; width:150px;">Name</div>
<div style="float: right; margin-right:5px; width:80px;">Admin</div>
<div style="float: right; margin-right:5px; width:80px;">Visibility</div>
<div style="float: right; margin-right:5px; width:80px;">Download</div>
</div>
<form method="post" action="plugin/admin/pages/pro/pluginpermissionpro.php">
<input name="adminid" type="hidden" value="<?php echo $_REQUEST['id'];?>" />
<input name="pluginid" type="hidden" value="<?php echo $_REQUEST['u'];?>" />
<?php
$xml="../../" . $_REQUEST['c'] . "/menu.xml";
 if(file_exists($xml)){
$xmlDoc = new DOMDocument();
$xmlDoc->load($xml);
$x = $xmlDoc->documentElement;
foreach ($x->childNodes AS $item)
  {
    if(strlen($item->nodeValue) > 1){
        
       $checkc = "";
       $checkb = "";
       $checka = "";
     if(in_array($item->tagName,$arraykey)){
        $checka = 'checked="checked"';
     if(isset($temparray[$item->tagName])){ 
        if($temparray[$item->tagName] == "1"){ 
     $checkb = 'checked="checked"'; 
       }
    if($temparray[$item->tagName] == "2"){ 
     $checkb = 'checked="checked"'; 
     $checkc = 'checked="checked"'; 
       }   
       
     }
    }     
        
 echo '<div class="shadow" style="text-align:left; margin: 5px; padding:5px; overflow:hidden;">
<div class="whitelink" style="float: left; width:150px;">'.$item->nodeValue.'</div>
<div style="float: right; margin-right:5px; width:80px;"><input id="a'.$item->tagName.'" name="admin[]" type="checkbox" value="'.$item->tagName.'" '.$checkb.' /><label for="a'.$item->tagName.'"><span></span></label></div>
<div style="float: right; margin-right:5px; width:80px;"><input id="b'.$item->tagName.'" name="visibility[]" type="checkbox" value="'.$item->tagName.'" '.$checka.' /><label for="b'.$item->tagName.'"><span></span></label></div>
<div style="float: right; margin-right:5px; width:80px;"><input id="c'.$item->tagName.'" name="download[]" type="checkbox" value="'.$item->tagName.'" '.$checkc.' /><label for="c'.$item->tagName.'"><span></span></label></div>
</div>'; 
 }   
  }
}//visibility first name=0, for all
//the replace name=0 with current admin 
?>
<div style="overflow: hidden; z-index:5; position:relative;">
<div class="activemenu" style="margin-left:50px; float: left; width:10px; height:20px; margin-bottom:-5px;"></div>
<div class="activemenu" style="margin-right:50px; float: right; width:10px; height:20px; margin-bottom:-5px;"></div>
</div>
<div style="z-index: 2; position:relative;">
<input class="activemenu shadow" style="border: none; padding:3px 0px; width:99%;" type="submit" value="Save" />
</div>
</form>