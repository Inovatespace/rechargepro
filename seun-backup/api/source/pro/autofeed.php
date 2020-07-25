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
        $rechargeproid = $parameter['rechargeproid'];
        $mainacbal = $parameter['mainacbal'];
        $processamount = $parameter['processamount'];
        $rechargeprorole = $parameter['rechargeprorole'];
        $tid = $parameter['tid'];
        
        
 
 
 
// if($profile_creator == "115"){
//    $row = self::db_query("SELECT SUM(ac_ballance) AS ac, SUM(profit_bal) AS pf FROM rechargepro_account WHERE profile_creator = '115'",array());
//    $bal = $row[0]['ac'];
//    $pf = $row[0]['pf'];
//    
//    
//    self::db_query("UPDATE rechargepro_account SET ac_ballance ='0', profit_bal = '0' WHERE profile_creator = '115'",array());
//    
//    
//    
//    
//    $row = self::db_query("SELECT ac_ballance, profit_bal FROM rechargepro_account WHERE rechargeproid = '115'",array());
//    $ac_ballance = $row[0]['ac_ballance']+$row[0]['profit_bal'];
//    $acbal = $bal+$pf+$ac_ballance;
//    
//    self::db_query("UPDATE rechargepro_account SET ac_ballance =?, profit_bal='0' WHERE rechargeproid = '115'",array($acbal));
// }
        
   
        //{"ac_ballance":"4824022.55","auto_feed_cahier_account":"1","feed_cahier_account_amount":"50000","profile_agent":"0"}
         //include "autofeed.php";
         ////////////////////////////// AUTO FEED START
         
if (empty($ac_ballance) || ($processamount) > $ac_ballance) {
    $row = self::db_query("SELECT ac_ballance,auto_feed_cahier_account,feed_cahier_account_amount,profile_agent FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
        array($profile_creator));
    $ogaballance = $row[0]['ac_ballance'];
    $ogaautofeed = $row[0]['auto_feed_cahier_account'];
    $ogaprofile_agent = $row[0]['profile_agent'];
    $feed_cahier_account_amount = $row[0]['feed_cahier_account_amount'];
    
    
    
           

        
        
        
    if ($ogaautofeed == 1 && $rechargeprorole < 4) {
        
        if ($processamount > $ogaballance) {
            return "bad";
        }

        if ($ogaballance < $feed_cahier_account_amount) {
            return "bad";
        }


        if ($processamount > $feed_cahier_account_amount && !in_array($rechargeproid,array(172))) {
            return "bad";
        }
        
        $newballance = $ogaballance - $processamount;
        
        self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($newballance, $profile_creator));
            ////set co bal on trans
      self::db_query("UPDATE rechargepro_transaction_log SET bal2=? WHERE transactionid = ? LIMIT 1", array($ogaballance,$tid));
      
              if($profile_creator == "115"){
            $what = "SPECIAL_" . $ogaautofeed . "_" . $rechargeproid ."_".$feed_cahier_account_amount."_".$ogaballance ."_" . $tid;
            self::db_query("INSERT INTO log_log (rechargeproid,what,details) VALUES (?,?,?)",
                array(
                "115",
                "CAUGHT",
                $what));
        }
        
      return "good";
            
    } else {
        return "bad";
    }

}else{
    return "allow";
    
}

}


}
 ////////////////////////////////// AUTO FEED END
?>