<?php 
include "../../../../../engine.autoloader.php";
$tid = htmlentities($_REQUEST['tid']);

  
        $row = $engine->db_query("SELECT transaction_date,account_meter,phone,rechargepro_service,cordinator_id,agent_id,rechargeproid,rechargepro_subservice,amount,thirdPartycode,business_district FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",array($tid));
        $amount_to_charge = $row[0]['amount'];
        $rechargepro_subservice = $row[0]['rechargepro_subservice'];
        $rechargepro_service = $row[0]['rechargepro_service'];
        $rechargepro_cordinator = $row[0]['cordinator_id'];
        $agent_id = $row[0]['agent_id'];
        $rechargeproid = $row[0]['rechargeproid'];
        $account_meter = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $district = $row[0]['business_district'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));

        if (empty($rechargeproid)){
            exit;
        }
        
        

        if (strpos($rechargepro_service, 'REFUND') !== false) {
            if(preg_match('#\((.*?)\)#',$rechargepro_service, $match)){
                
                $row = $engine->db_query("SELECT services_key FROM rechargepro_services WHERE service_name LIKE ? LIMIT 1",array("%$match[1]%"));
        $rechargepro_subservice = $row[0]['services_key'];
            };
        }

include "verify/tv.php";

?>