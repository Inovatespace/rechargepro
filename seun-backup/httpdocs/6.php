<?php
include "engine.autoloader.php";
        
    ////////////////////////////////////////1868800.00
    
                
    $row = $engine->db_query("SELECT transactionid,rechargeproid,agentprofit,service_charge,rechargepro_service_charge FROM rechargepro_transaction_log WHERE (rechargepro_subservice = 'AEP'  OR rechargepro_subservice = 'AED') AND rechargepro_status_code = '1' AND refund = '0' AND agentprofit > '0' AND transaction_date > '2020-01-08' LIMIT 800",array());
        for ($dbc = 0; $dbc < $engine->array_count($row); $dbc++) {
            
            $tid = $row[$dbc]['transactionid'];
            $rechargeproid = $row[$dbc]['rechargeproid'];
            $agentprofit = $row[$dbc]['agentprofit'];        
         
         $engine->db_query("UPDATE rechargepro_transaction_log SET agentprofit =?,rechargeproprofit = ? WHERE transactionid = ? LIMIT 1",
                array(
                0,
                0,
                $tid));
                
    
$seun =  $engine->db_query("SELECT ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1", array($rechargeproid)); 
$ballance = $seun[0]['ac_ballance'];
$ac_ballance = $ballance-$agentprofit;
                
    $engine->db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",array($ac_ballance,$rechargeproid));
        
        echo $tid."<br />";
        }

?>