<?php
include "../../../engine.autoloader.php";


if(!empty($_REQUEST['id']) && !empty($_REQUEST['comment'])){
$engine->db_query2("INSERT INTO contact_replies (replyto,name,message,staffemail) VALUES (?,?,?,?)",array($_REQUEST['id'],$engine->get_session("name"),$_REQUEST['comment'],$engine->get_session("email")));


$email = urldecode($_REQUEST['email']);
$name = urldecode($_REQUEST['name']);
$trackid = urldecode($_REQUEST['trackid']);

$message = "<hr/> Dear $name, <br /><br />You have a new response to your ticket.<br /><br />Follow the link below to view your ticket<br /><br /> <a href='https://rechargepro.com.ng/support#$trackid'>https://rechargepro.com.ng/support#$trackid</a> <br /><br />    ".$_REQUEST['comment']."   <br /><br />Thank you<br />RechargePro Team";
$engine->send_mail(array("support@rechargepro.com.ng","RechargePro"), $email, "RE: $trackid RechargePro your ticket have a reply", $message);


  

$cdate = date("Y-m-d H:i:s");
$engine->db_query2("UPDATE contact_tickets SET lastupdate = ?, status = ?, admin_status =? WHERE id = ? LIMIT 1",array($cdate,0,$_REQUEST['open'],$_REQUEST['id']));
}


if(!empty($_REQUEST['id']) && !empty($_REQUEST['memo'])){
    
$engine->db_query2("INSERT INTO contact_replies (replyto,name,message,staffemail,reply_type) VALUES (?,?,?,?,?)",array($_REQUEST['id'],$engine->get_session("name"),$_REQUEST['memo'],$engine->get_session("email"),2));

}
?>

