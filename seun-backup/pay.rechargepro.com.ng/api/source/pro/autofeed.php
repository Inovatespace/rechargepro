<?php
class autofeed extends Api
{
    //KEDCO
    public function __construct($method)
    {

    }

    public function check_bal($parameter)
    {
        

        $ac_ballance = $parameter['ac_ballance'];
        $profile_creator = $parameter['profile_creator'];
        $quickpayid = $parameter['quickpayid'];
        $mainacbal = $parameter['mainacbal'];
        $processamount = $parameter['processamount'];
        $quickpayrole = $parameter['quickpayrole'];
        
         //include "autofeed.php";
         ////////////////////////////// AUTO FEED START
         
if (empty($ac_ballance) || ($processamount) > $ac_ballance) {
    $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account,feed_cahier_account_amount,profile_agent FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
        array($profile_creator));
    $ogaballance = $row[0]['ac_ballance'];
    $ogaautofeed = $row[0]['auto_feed_cahier_account'];
    $ogaprofile_agent = $row[0]['profile_agent'];
    $feed_cahier_account_amount = $row[0]['feed_cahier_account_amount'];
    if ($ogaautofeed == 1 && $quickpayrole < 4) {
        
        

        if ($processamount > $ogaballance) {
            return 0;
        }




        if ($ogaballance < $feed_cahier_account_amount) {
            return 0;
        }


        if ($processamount > $feed_cahier_account_amount) {
            return 0;
        }
        
        
        //register topup
        $myip = self::getRealIpAddr();
        self::db_query("INSERT INTO quickpay_transaction_log (cordinator_id,agent_id,quickpayid,transaction_reference,quickpay_subservice,quickpay_service,quickpay_status_code,ip,amount,quickpay_status,quickpay_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($profile_creator,$profile_creator,$profile_creator,"TRANSFER","TRANSFER","AUTO TOPUP","1",$myip,$feed_cahier_account_amount,"PAID",'{"details":{"TRANSFER":"'.$feed_cahier_account_amount.'","TRANSACTION STATUS","DONE"}}',$quickpayid)); 
        
        self::db_query("INSERT INTO quickpay_transaction_log (cordinator_id,agent_id,quickpayid,transaction_reference,quickpay_subservice,quickpay_service,quickpay_status_code,ip,amount,quickpay_status,quickpay_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($profile_creator,$profile_creator,$quickpayid,"TOPUP","TOPUP","TOPUP","1",$myip,$feed_cahier_account_amount,"PAID",'{"details":{"TOPUP":"'.$feed_cahier_account_amount.'","TRANSACTION STATUS","DONE"}}',$profile_creator)); 
  


        $newballance = $ogaballance - $feed_cahier_account_amount;
        
        self::db_query("UPDATE quickpay_account SET ac_ballance =? WHERE quickpayid = ? LIMIT 1",
            array($newballance, $profile_creator));


        $agentballance = $mainacbal + ($feed_cahier_account_amount - $processamount);
        self::db_query("UPDATE quickpay_account SET ac_ballance =? WHERE quickpayid = ? LIMIT 1",
            array($agentballance, $quickpayid));
            
            return 1;
            
    } else {
        return 0;
    }

}else{
    return 2;
    
}

}


}
 ////////////////////////////////// AUTO FEED END
?>