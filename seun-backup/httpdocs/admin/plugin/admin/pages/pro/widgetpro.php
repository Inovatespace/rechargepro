<?php
include_once "../../../../engine.autoloader.php";


$adminid = $_REQUEST['adminid'];

$widget = array();
if(isset($_REQUEST['widget'])){
$widget = $_REQUEST['widget'];
}

$adminwidgetarray = array();
$row = $engine->db_query("SELECT widgetid FROM admin_widget WHERE adminid = ?",array($adminid));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$adminwidgetarray[] = $row[0]['widgetid'];    
}

$arraydifference = array_diff($adminwidgetarray,$widget);


for($i=0;$i<count($widget);$i++){
   
$countit = $engine->db_query("SELECT id FROM admin_widget WHERE adminid = ? AND widgetid = ? LIMIT 1",array($adminid,$widget[$i]),true);

if($countit < 1){
$engine->db_query("INSERT INTO admin_widget (widgetid,adminid) VALUES (?,?)",array($widget[$i],$adminid)); 
}
}

foreach($arraydifference AS $todelete){
$engine->db_query("DELETE FROM admin_widget WHERE adminid = ? AND widgetid = ? LIMIT 1",array($adminid,$todelete));     
}

echo "<meta http-equiv='refresh' content='0;url=../../../../admin&p=index&i=$adminid'>"; exit;
?>