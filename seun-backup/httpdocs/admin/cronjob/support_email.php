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
$mailbox = new PhpImap\Mailbox("{rechargepro.com.ng:995/pop3/ssl/novalidate-cert}INBOX",
    "support@rechargepro.com.ng", "Xhange@123", "../tmp/");
$mailsIds = $mailbox->searchMailBox('ALL');

libxml_use_internal_errors(true);

function remove_double($ro)
{
    $ro = str_replace("&nbsp;", "", $ro);
    $ro = html_entity_decode($ro);
    $ro = preg_replace('/\s+/', ' ', $ro);
    $ro = trim($ro);
    return $ro;
}

    $blacklist = array();
    $row = $engine->db_query2("SELECT email FROM contact_blacklist",array());
    for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $blacklist[] = $row[$dbc]['email'];
    }


$count = 0;
foreach ($mailsIds as $mailId) {
    $count++;

    $mail = $mailbox->getMail($mailId, true);
    
    $fromname = $mail->fromName;
    $fromemail = $mail->fromAddress;
    $fromsubject = $mail->subject;
    
    
    if(!in_array($fromemail,$blacklist)){
  
    $emailbody = $mail->textPlain;
    if (strlen($emailbody) < 5) {
        $emailbody = $mail->textHtml;
    }


    $emailbody = str_replace("<hr/>", "@#*@#@", $emailbody);
    $emailbody = utf8_decode(utf8_decode($emailbody));
    //$emailbody = strip_tags($emailbody);


    $reference = 0;
    preg_match_all("/[\s]*[0-9]{4,4}[A-Z]{4,4}[0-9]{6,6}/si", $emailbody, $match);
    if (isset($match[0][0])) {
        $reference = remove_double($match[0][0]);
    }
    ;


    if ($reference != 0) {
        $message = explode("@#*@#@", $emailbody);

        $row = $engine->db_query2("SELECT id, name, email FROM contact_tickets WHERE trackid = ? LIMIT 1",
            array($reference));
        $id = $row[0]['id'];
        $name = $row[0]['name'];
        $email = $row[0]['email'];

        if (!empty($id) && !empty($message[0])) {
            $engine->db_query2("INSERT INTO contact_replies (replyto,name,message,staffemail) VALUES (?,?,?,?)",
                array(
                $id,
                $name,
                $message[0],
                $email));
            $cdate = date("Y-m-d H:i:s");
            $engine->db_query2("UPDATE contact_tickets SET lastupdate = ?, status = ?, admin_status ='0' WHERE id = ? LIMIT 1",
                array(
                $cdate,
                1,
                $id));
                
                
                
    $attachmentlist = array();
    $row = $engine->db_query2("SELECT attachment FROM contact_attachment",array());
    for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $attachmentlist[] = $row[$dbc]['attachment'];
    }
                
                
                
                
            foreach ($mail->getAttachments() as $files) {
            $file_path = $files->filePath;
            $file_name = basename($file_path);
            $file_path = "../tmp/".$file_name;
            


            //Make sure we have a filepath
            $shortname = $id . "_".$files->name;//
            if(!in_array($shortname,$attachmentlist)){
            if (!empty($file_name)) {
                
               

                //docx doc pdf xls xlsx csv txt jpg jpeg png

                $file_info = new finfo(FILEINFO_MIME);
                $mime_type = $file_info->buffer(file_get_contents($file_path));
                if (in_array($mime_type, array(
                "image/png; charset=binary",
                "image/jpg; charset=binary",
                "image/jpeg; charset=binary",
                    "image/jpg",
                    "image/jpeg",
                    "image/png",
                    "application/vnd.ms-excel",
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    "application/pdf",
                    "text/csv",
                    "application/msword",
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"))) {
                        
                      
                    
                                        //save the url and the file
                    $filePath = "../../ticket/" . $shortname;
                    
                    if (copy($file_path, $filePath)) {
                        $engine->db_query2("INSERT INTO contact_attachment (postid,attachment) VALUES (?,?)",array($id,$shortname));
                        $engine->db_query("UPDATE contact_tickets SET is_attachment = '1' WHERE id = ?",array($id));
                    }
                }

            }
            }

        }
                
                
                
                
                
                
                
                
                
                
                
                
                
        } else {
            $reference = 0;
        }

    }


    if ($reference == 0) {


        $message = explode("@#*@#@", $emailbody);

        $datetime = date("Y-m-d H:i:s");
        $lastupdate = date("Y-m-d H:i:s");
        $trackid = $engine->RandomString(1, 4) . $engine->RandomString(2, 4) . date("His");
        
        
          
  $senderip = "000.000.000.000";
if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/',$mail->headersRaw, $ip_match)) {
   $senderip = $ip_match[0];
}

        $insertid = $engine->db_query2("INSERT INTO contact_tickets (trackid,name,email,category,priority,subject,message,dt,lastupdate,ip) VALUES (?,?,?,?,?,?,?,?,?,?)",
            array(
            $trackid,
            urldecode($fromname),
            urldecode($fromemail),
            urldecode("3"),
            urldecode("1"),
            urldecode($fromsubject),
            urldecode($message[0]),
            $datetime,
            $lastupdate,
            $senderip));


        $message = "<hr/> Dear $fromname, <br /><br />Your Ticket has been logged you can Track your ticket by following the link below<br /><br /> <a href='https://rechargepro.com.ng/support#$trackid'>https://rechargepro.com.ng/support#$trackid</a> <br /><br />    " .
            $message[0] . "   <br /><br />Thank you<br />RechargePro Team";
        $engine->send_mail(array("support@rechargepro.com.ng", "RechargePro"), $fromemail,
            "RE: $trackid RechargePro Ticket Opened", $message);


        foreach ($mail->getAttachments() as $files) {
            $file_path = $files->filePath;
            $file_name = basename($file_path);
            $file_path = "../tmp/".$file_name;
            


            //Make sure we have a filepath
            if (!empty($file_name)) {
                
               

                //docx doc pdf xls xlsx csv txt jpg jpeg png

                $file_info = new finfo(FILEINFO_MIME);
                $mime_type = $file_info->buffer(file_get_contents($file_path));
                if (in_array($mime_type, array(
                "image/png; charset=binary",
                "image/jpg; charset=binary",
                "image/jpeg; charset=binary",
                    "image/jpg",
                    "image/jpeg",
                    "image/png",
                    "application/vnd.ms-excel",
                    "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                    "application/pdf",
                    "text/csv",
                    "application/msword",
                    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"))) {
                        
                      
                    $shortname = $insertid . "_".$files->name;//
                                        //save the url and the file
                    $filePath = "../../ticket/" . $shortname;
                    
                    if (copy($file_path, $filePath)) {
                        $engine->db_query2("INSERT INTO contact_attachment (postid,attachment) VALUES (?,?)",array($insertid,$shortname));
                        $engine->db_query2("UPDATE contact_tickets SET is_attachment = '1' WHERE id = ?",array($insertid));
                    }
                }

            }

        }
        
        }


}
        $mailbox->deleteMail($mailId);

        if ($count >= 10) {
            break;
        }

    }

    $mailbox->expungeDeletedMails();

?>

