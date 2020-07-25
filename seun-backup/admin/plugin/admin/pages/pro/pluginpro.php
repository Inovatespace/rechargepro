<?php
include_once "../../../../engine.autoloader.php";



$adminid = $_REQUEST['adminid'];

$plugin = array();
if(isset($_REQUEST['plugin'])){
$plugin = $_REQUEST['plugin'];
}

$fillpermission = 0;

//check user role; if superadmin/admin
$row = $engine->db_query("SELECT role FROM admin WHERE adminid = ? LIMIT 1",array($adminid));
if($row[0]['role'] == "superadmin" || $row[0]['role'] == "admin"){
$fillpermission = 1;   
}


$adminpluginarray = array();
$row = $engine->db_query("SELECT pluginid FROM admin_plugin WHERE adminid = ?",array($adminid));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$adminpluginarray[] = $row[$dbc]['pluginid'];    
}

$arraydifference = array_diff($adminpluginarray,$plugin);


for($i=0;$i<count($plugin);$i++){
$permission = ""; 
$countit = $engine->db_query("SELECT id FROM admin_plugin WHERE adminid = ? AND pluginid = ? LIMIT 1",array($adminid,$plugin[$i]),true);
if($countit < 1){
    
if($fillpermission == 1){
$row = $engine->db_query("SELECT pluginkey FROM plugin WHERE pluginid = ? LIMIT 1",array($plugin[$i]));
$key = $row[0]['pluginkey'];

  $xml="../../../" . $key . "/menu.xml";
 if(file_exists($xml)){
$xmlDoc = new DOMDocument();
$xmlDoc->load($xml);
$x = $xmlDoc->documentElement;
foreach ($x->childNodes AS $item)
  {
    if(strlen($item->nodeValue) > 1){
 $permission .= $item->tagName."=1,"; 
 }   
  }
}
}    
    
    
$engine->db_query("INSERT INTO admin_plugin (pluginid,adminid,permission) VALUES (?,?,?)",array($plugin[$i],$adminid,$permission)); 
}
}

foreach($arraydifference AS $todelete){
$engine->db_query("DELETE FROM admin_plugin WHERE adminid = ? AND pluginid = ? LIMIT 1",array($adminid,$todelete));    
}

echo "<meta http-equiv='refresh' content='0;url=../../../../admin&p=index&i=$adminid'>"; exit;
?>