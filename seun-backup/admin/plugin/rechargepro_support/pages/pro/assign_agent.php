<?php
include "../../../../engine.autoloader.php";
$selection = $_REQUEST['selection'];
$id = $_REQUEST['agent'];


$row = $engine->db_query2("SELECT name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));
$agentname = $row[0]['name'];


$message = "Assigned to $agentname by ".$engine->get_session("name");

$exp = explode(",",$selection);

foreach($exp AS $st){
    
    //assigned to 
    //hidden comment
    //wait lock
    
    //reply_type 0 normal/1 comment hash/2 hidden green
$engine->db_query2("INSERT INTO contact_replies (replyto,name,message,staffemail,reply_type) VALUES (?,?,?,?,?)",array($st,$engine->get_session("name"),$message,$engine->get_session("email"),"1"));

$engine->db_query2("UPDATE contact_tickets SET admin_agent = ? WHERE id = ? LIMIT 1", array($id,$st));
}

?>
