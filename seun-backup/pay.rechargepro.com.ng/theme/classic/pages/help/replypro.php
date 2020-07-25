<?php
include "../../../../engine.autoloader.php";


if(!empty($_REQUEST['id']) && !empty($_REQUEST['comment'])){
$engine->db_query("INSERT INTO contact_replies (replyto,name,message,staffemail) VALUES (?,?,?,?)",array($_REQUEST['id'],$engine->get_session("name"),$_REQUEST['comment'],$engine->get_session("quickpayemail")));

$cdate = date("Y-m-d H:i:s");
$engine->db_query("UPDATE contact_tickets SET lastupdate = ?, status = ?, admin_status ='0' WHERE id = ? LIMIT 1",array($cdate,1,$_REQUEST['id']));
}
?>

