<?php
class autofeed extends Api
{
    //KEDCO
    public function __construct($method)
    {

    }

    public function validation($parameter)
    {
         //include "autofeed.php";
         ////////////////////////////// AUTO FEED START
         $deduct = 1;
if (empty($ac_ballance) || ($amount + $tfee) > $ac_ballance) {
    $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account,feed_cahier_account_amount,profile_agent FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
        array($profile_creator));
    $ogaballance = $row[0]['ac_ballance'];
    $ogaautofeed = $row[0]['auto_feed_cahier_account'];
    $ogaprofile_agent = $row[0]['profile_agent'];
    $feed_cahier_account_amount = $row[0]['feed_cahier_account_amount'];
    if ($ogaautofeed == 1 && $rechargeprorole < 4) {
        
        $processamount = ($amount + $tfee);

        if ($processamount > $ogaballance) {
            return array("status" => "100", "message" => "Insufficient Fund");
        }




        if ($ogaballance < $feed_cahier_account_amount) {
            return array("status" => "100", "message" => "Insufficient Fund");
        }


        if ($processamount > $feed_cahier_account_amount) {
            return array("status" => "100", "message" => "Insufficient Fund");
        }
        
        
        //register topup
        $myip = self::getRealIpAddr();
        self::db_query("INSERT INTO rechargepro_transaction_log (cordinator_id,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_status,rechargepro_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($profile_creator,$profile_creator,$profile_creator,"TRANSFER","TRANSFER","AUTO TOPUP","1",$myip,$feed_cahier_account_amount,"PAID",'{"details":{"TRANSFER":"'.$feed_cahier_account_amount.'","TRANSACTION STATUS","DONE"}}',$rechargeproid)); 
        
        self::db_query("INSERT INTO rechargepro_transaction_log (cordinator_id,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_status,rechargepro_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($profile_creator,$profile_creator,$rechargeproid,"TOPUP","TOPUP","TOPUP","1",$myip,$feed_cahier_account_amount,"PAID",'{"details":{"TOPUP":"'.$feed_cahier_account_amount.'","TRANSACTION STATUS","DONE"}}',$profile_creator)); 
  


        $deduct = 0;

        $newballance = $ogaballance - $feed_cahier_account_amount;
        
        self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
            array($newballance, $profile_creator));


        $agentballance = $mainacbal + ($feed_cahier_account_amount - $processamount);
        self::db_query("UPDATE rechargepro_account SET ac_ballance =? WHERE rechargeproid = ? LIMIT 1",
            array($agentballance, $rechargeproid));
            
    } else {
        return array("status" => "100", "message" => "Insufficient Balance");
    }

}
 ////////////////////////////////// AUTO FEED END
?>