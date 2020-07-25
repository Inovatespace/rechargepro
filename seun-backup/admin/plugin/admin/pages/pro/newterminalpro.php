<?php
include "../../../../engine.autoloader.php";



if(empty($_REQUEST['account'])){ echo "bad"; exit;}

$account = $_REQUEST['account'];
$password = $_REQUEST['password'];

$access = "";
foreach($_REQUEST AS $key => $value){

if($value != 1 && $key != "account" && $key != "password"){
 $access .= $key."=".$value.",";
}

}


$countstaff = $engine->db_query("SELECT staffid FROM members WHERE staffid = ? LIMIT 1",array($account),true);
if($countstaff == 0){echo "bad"; exit;}


$row = $engine->db_query("SELECT id FROM terminal_acces WHERE account_id = ? LIMIT 1",array($account));
$acid = $row[0]['id'];

$countaccess = strlen($access);
if($countaccess > 0){
$access = substr($access, 0, ($countaccess-1));

if(empty($acid)){
$engine->db_query("INSERT INTO terminal_acces (account_id,password,access) VALUES(?,?,?) ",array($account,$password,$access));
}else{
$engine->db_query("UPDATE terminal_acces SET password =?, access=? WHERE id = ? LIMIT 1",array($password,$access,$acid));
}

}else{
$engine->db_query("DELETE FROM terminal_acces WHERE id = ? LIMIT 1",array($acid));
}



echo "<meta http-equiv='refresh' content='0;url=../../../../admin&p=terminal'>"; exit;

?>