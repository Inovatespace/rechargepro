<?php
class myrefund extends Api
{
    //KEDCO
         function fix_phone($mobile)
    {
       $mobile = "234" . substr($mobile, 1);  
       return $mobile;
    }

    function refund_now($parameter)
    {
        $tid = htmlentities($parameter['tid']);

        $row = self::db_query("SELECT rechargepro_print,transaction_date,account_meter,phone,rechargepro_service,cordinator_id,agent_id,rechargeproid,rechargepro_subservice,amount,thirdPartycode,refererprofit,agentprofit,cordprofit,rechargeproprofit,rechargepro_service_charge FROM rechargepro_transaction_log WHERE transactionid = ? AND refund = '0' AND bank_ref ='' LIMIT 1",array($tid));
        $amount_to_charge = $row[0]['amount'];
        $rechargepro_subservice = $row[0]['rechargepro_subservice'];
        $rechargepro_service = $row[0]['rechargepro_service'];
        $rechargepro_cordinator = $row[0]['cordinator_id'];
        $agent_id = $row[0]['agent_id'];
        $rechargeproid = $row[0]['rechargeproid'];
        $account_meter = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        $rechargepro_print = $row[0]['rechargepro_print'];
        
        $refererprofit = $row[0]['rechargepro_print'];
        $agentprofit = $row[0]['agentprofit'];
        $cordprofit = $row[0]['cordprofit'];
        $rechargepro_print = $row[0]['rechargepro_print'];
        $rechargepro_service_charge = $row[0]['rechargepro_service_charge'];
        
        if (empty($rechargeproid)){
            exit;
        }
        
        
               
        
        //set refund = 1
self::db_query("UPDATE rechargepro_transaction_log SET refund=?  WHERE transactionid = ?",
                array(
                "1",
                $tid));
                
     
if(!empty($account_meter) && $rechargepro_subservice != "BANK TRANSFER"){

      $row = self::db_query("SELECT profit_bal,rechargeprorole, ac_ballance, profile_creator , name, email FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($rechargeproid));
        $rechargeprorole = $row[0]['rechargeprorole'];
        $myballance = $row[0]['ac_ballance'];
        $myprofitbal = $row[0]['profit_bal'];
        $profile_creator = $row[0]['profile_creator'];
        $name = $row[0]['name'];
$email = $row[0]['email'];

        $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $rechargeproid."_".$tid;
        self::db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",
            array(
            "0",
            "REFUND",
            $what));

        if ($rechargeprorole <= 3) {
          

                $newballance = ($myballance + $amount_to_charge);
                self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                    array(
                    $rechargeproid,
                    $rechargeproid,
                    $agentprofit,
                    "-REWARD"));
                    
                    $newballance = ($myballance + $amount_to_charge) + $rechargepro_service_charge;
                    $newprofit = $myprofitbal - $agentprofit;
                self::db_query("UPDATE rechargepro_account SET ac_ballance = ?, profit_bal=? WHERE rechargeproid = ? LIMIT 1",
                    array($newballance,$newprofit,$rechargeproid));


                if ($rechargepro_cordinator > 0 || $rechargeprorole == 1){
                    
                    if($rechargeprorole == 1){
                      $rechargepro_cordinator = $rechargeproid;
                    }

                    self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $rechargepro_cordinator,
                        $rechargeproid,
                        $cordprofit,
                        "-COR_REWARD"));


                    $row = self::db_query("SELECT profit_bal,ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($rechargepro_cordinator));
                    $cordinator_ballance = $row[0]['profit_bal'];

                    $cornewballance = $cordinator_ballance - $cordprofit;
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                        array($cornewballance, $rechargepro_cordinator));
                }
          //  }

        }


        if ($rechargeprorole > 3) {
            if ($profile_creator > 0 && $refererprofit> 0) {

                $row = self::db_query("SELECT profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                    array($profile_creator));
                $creator_ballance = $row[0]['profit_bal'];

                self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                    array(
                    $profile_creator,
                    $rechargeproid,
                    $refererprofit,
                    "-USER_REWARD"));


                $creator_ballance = $creator_ballance - $refererprofit;
                self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                    array($creator_ballance, $profile_creator));

            }

//+ 100 naira
            $newballance = $myballance + $amount_to_charge+ $rechargepro_service_charge;
            self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                array($newballance, $rechargeproid));

        }
        
        
        
      
  //      if ($bill_formular == 1){
   //     $fullf = $cordinator_percentage+$percentage+$bill_rechargeprofull_percentage+$service_provider_percentage;
  //      $row = self::db_query("SELECT ac_ballance FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($rechargeproid));
  //      $myballance = $row[0]['ac_ballance'];
//
//       $myballance = $myballance + $fullf;
//       self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($myballance, $rechargeproid));
 //           }
            
            
        
        }

       
self::db_query("INSERT INTO rechargepro_refund (rechargeproid,rechargepro_service,rechargepro_subservice,account_meter,amount,phone,transactionid,rechargepro_status_code,rechargepro_status) VALUES (?,?,?,?,?,?,?,?,?)",array($rechargeproid,$rechargepro_service,$rechargepro_subservice,$account_meter,$amount_to_charge,$phone,$tid,"1","PAID"));


        if ($rechargepro_subservice == "BANK TRANSFER"){
            
      $row = self::db_query("SELECT rechargeprorole, ac_ballance, profile_creator , name FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($rechargeproid));
        $rechargeprorole = $row[0]['rechargeprorole'];
        $myballance = $row[0]['ac_ballance'];
        $profile_creator = $row[0]['profile_creator'];
        $name = $row[0]['name'];
        
        
        
        
        $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $rechargeproid."_".$tid;
        self::db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",
            array(
            "0",
            "REFUND",
            $what));
        
        
            $tfee = 52;
            if ($amount_to_charge > 20000) {
                $tfee = 100;
            }
            $myballance = $myballance + $amount_to_charge + $tfee;
            self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",
                array($myballance, $rechargeproid));
        }


        if ($tid){
            
            $newprint = '{"details":{"REFUND_DATE":"'.date("Y-m-d H:i:s").'","TRANSACTION STATUS","DONE"}}';
            
            self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_service = ?, rechargepro_subservice =?, rechargepro_status_code=?, rechargepro_status=?, rechargepro_print = ?  WHERE transactionid = ?",
                array(
                "REFUND($rechargepro_service)",
                "REFUND",
                "1",
                "PAID",
                $newprint,
                $tid));
                
                
                
            self::db_query("DELETE FROM rechargepro_transaction_log  WHERE ip = ?",array($tid));       

//self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,rechargepro_service,rechargepro_subservice,amount,transaction_reference,rechargepro_status_code,rechargepro_status,rechargepro_print) VALUES (?,?,?,?,?,?,?,?)",array($rechargeproid,"REFUND","REFUND",$amount_to_charge,"REFUND","1","PAID",'{"details":{"REFUND":"'.$amount_to_charge.'","TRANSACTION STATUS","DONE"}}'));
        }


        $message = "Hey $name,<br />
$amount_to_charge has been refunded to your wallet, for uncompleted $rechargepro_subservice transaction!<br />
Thank you,<br />
RechargePro";
        self::notification($rechargeproid, $message, 1);
        
     //self::send_mail('noreply@rechargepro',$email,"RechargePro Refund",$message);
        
         return "100";
    }
    
    
    
    

}
?>