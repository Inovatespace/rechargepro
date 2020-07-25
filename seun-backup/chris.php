<?php
	include "engine.autoloader.php";
    
    $row = $engine->db_query("SELECT SUM(ac_ballance) AS ac, SUM(profit_bal) AS pf FROM rechargepro_account WHERE profile_creator = '115'",array());
    $bal = $row[0]['ac'];
    $pf = $row[0]['pf'];
    
    
    $engine->db_query("UPDATE rechargepro_account SET ac_ballance ='0', profit_bal = '0' WHERE profile_creator = '115'",array());
    
    
    
    
    $row = $engine->db_query("SELECT ac_ballance, profit_bal FROM rechargepro_account WHERE rechargeproid = '115'",array());
    $ac_ballance = $row[0]['ac_ballance']+$row[0]['profit_bal'];
    $acbal = $bal+$pf+$ac_ballance;
 $engine->db_query("UPDATE rechargepro_account SET ac_ballance =?, profit_bal='0' WHERE rechargeproid = '115'",array($acbal));
 
 echo $acbal;

?>