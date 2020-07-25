<?php
//val4 position
//val5 department


$row = $engine->db_query("SELECT val4, val5 FROM admin WHERE adminid = ? LIMIT 1",array($_SESSION['adminid']));
$_SESSION['department'] = $row[0]['val4'];
$_SESSION['staf_position'] = $row[0]['val5'];
?>