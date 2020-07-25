<?php
include "../../../../engine.autoloader.php";
$rechargeproid = $engine->get_session("rechargeproid");
$id = $_REQUEST["id"];


$row = $engine->db_query("SELECT SUM(amount) AS rp FROM rechargepro_transaction_log WHERE rechargepro_subservice = 'REWARD' AND account_meter = ? AND rechargeproid = ?", array($id,$rechargeproid));

if(empty($row[0]['rp'])){$row[0]['rp'] = 0;}
echo "&#8358;".$engine->safe_html($row[0]['rp']);
?>