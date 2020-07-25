<?php

//Amount
//TYPE
//REF
//TID - rechargepro
//LINK # if not
//DATE
//NARATION
//ACCOUNT NUMBER


include "../engine.autoloader.php";
require "PhpImap/__autoload.php";
$mailbox = new PhpImap\Mailbox("{rechargepro.com.ng:995/pop3/ssl/novalidate-cert}INBOX","servern@rechargepro.com.ng","Xhange@123",null);
$mailsIds = $mailbox->searchMailBox('ALL');

libxml_use_internal_errors(true);

function remove_double($ro){
    $ro = str_replace("&nbsp;","",$ro);
    $ro = html_entity_decode($ro);    
    $ro = preg_replace('/\s+/', ' ',$ro);
    $ro = trim($ro);
    return $ro;
}



$count = 0;
foreach($mailsIds AS $mailId){
    $count++;
    
$mail = $mailbox->getMail($mailId,true);
$emailbody = $mail->textPlain;
if(strlen($emailbody) < 5){$emailbody =  $mail->textHtml;}

$emailbody = utf8_decode(utf8_decode($emailbody));
//$emailbody = strip_tags($emailbody);

$emailbody = str_replace(array("<h1>","</h1>","<h3>","</h3>","<strike>","</strike>"),array("","","","","",""),$emailbody);



$doc = new DOMDocument();
$doc->loadHTML('<?xml encoding="utf-8"?>' . $emailbody);
$rows =$doc->getElementsByTagName('tr');
$tds= $doc->getElementsByTagName('td');


$mobile ="";
$accountnumber="";
$date="";$description="";
$amount=0;
$tmpacount = "";
$countit = 0;
foreach ($tds as $td) {
    
$countit++;

if($countit == 2){
    $accountnumber = remove_double($td->nodeValue);
}

if($countit == 4){
    $tmpacount = strtoupper(remove_double($td->nodeValue));
    $res = preg_replace("/[^0-9.]/", "",remove_double($td->nodeValue));
    $amount = substr($res, 0, strpos($res, "."));
}

if($countit == 6){
    $description = remove_double($td->nodeValue);
}

if($countit == 8){
    $res = preg_replace("/[^A-Za-z0-9 :-]/", "",remove_double($td->nodeValue));
    $res = str_replace(array("-"," ","-",":"),array("@","@","@","@"),$res);
    $res = trim($res);
    $explode = explode("@",$res);
    $res = $explode[0]."-".$explode[1]."-".$explode[2]." ".$explode[3].":".$explode[4];
    $date = date('Y-m-d H:i', strtotime('+0 days', strtotime($res)));
}

}


preg_match_all("/[\s]*(0)\w{10,}/si",$description, $match);
if(isset($match[0][0])){$mobile = remove_double($match[0][0]);};

$transactiontype = "";
if (strpos($tmpacount, 'CR') !== false) {
    $transactiontype = "CREDIT";
}

if (strpos($tmpacount, 'DR') !== false) {
    $transactiontype = "DEBIT";
}


//


$status =0;
if(strtoupper($transactiontype) == "CREDIT"){
      
        
    if(strlen($mobile) == 11){
        
        
        
       
      // print_r("<br />".$accountnumber."_".$amount."_".$description."_".$date."_".$transactiontype."_".$mobile); 
       
        $row = $engine->db_query2("SELECT rechargeproid, ac_ballance,loan_ballance,rechargepro_cordinator,profile_agent,profile_creator FROM rechargepro_account WHERE mobile = ? LIMIT 1",array($mobile));
        $ac_ballance = $row[0]['ac_ballance'];
        $rechargeproid = $row[0]['rechargeproid'];
        $user_profile_agent = $row[0]['profile_agent'];
        $rechargepro_cordinator = $row[0]['rechargepro_cordinator'];
        $profile_creator = $row[0]['profile_creator'];
        $loan_ballance = $row[0]['loan_ballance'];
        
        
        
        if(!empty($rechargeproid)){
            

         
         
        $start = date('Y-m-d H:i', strtotime('-30 minutes', strtotime($date)));
        $end = date('Y-m-d H:i', strtotime('+30 minutes', strtotime($date)));
         
       $row = $engine->db_query2("SELECT transactionid FROM rechargepro_transaction_log WHERE rechargepro_subservice = ? AND rechargeproid =? AND amount =? AND transaction_date BETWEEN ? AND ? LIMIT 1",array("CREDIT",$rechargeproid,$amount,$start,$end));
 
 
    if(empty($row[0]['transactionid'])){
        
        
        $newamount = $amount;
                if($loan_ballance > 0){
                
                if($loan_ballance > $amount){
                    $loan_ballance = $loan_ballance - $amount;
                    $newamount = 0;
                    
                    }else{
                    $loan_ballance = $loan_ballance - $amount;
                    $newamount = $loan_ballance;    
                    }
                    
$engine->db_query2("UPDATE rechargepro_account SET loan_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($loan_ballance,$rechargeproid));
 
                }
                
                
     $engine->db_query2("INSERT INTO rechargepro_transaction_log (transaction_date,cordinator_id,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,amount,rechargepro_status,rechargepro_print,account_meter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",array($date,$rechargepro_cordinator,$user_profile_agent,$rechargeproid,"AUTO PAY","CREDIT","AUTO PAY","1",$newamount,"PAID",'{"details":{"AUTO PAY":"'.$newamount.'","TRANSACTION STATUS","DONE","amount":"'.$newamount.'","AccountNumber":"'.$accountnumber.'", "Pro Mobile":"'.$mobile.'"}}',$accountnumber)); 
            
        $newbal = $ac_ballance + $newamount;
        $engine->db_query2("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($newbal,$rechargeproid));
        $status = 1;
       } 
        
        }
    }
}



$engine->db_query("INSERT INTO bank_alert (transaction_type,refid,amount,naration,acnumber,date,status) VALUES (?,?,?,?,?,?,?)",array($transactiontype,$mobile,$amount,$description,$accountnumber,$date,$status));

$mailbox->deleteMail($mailId);

if($count >= 10){break;}

}

$mailbox->expungeDeletedMails();
?>

