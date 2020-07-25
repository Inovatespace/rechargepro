<?php

class notification extends Api
{

        //KEDCO
        public function __construct($method)
        {
    
        }

        public function system_message($parameter){
        
            if (!isset($parameter['private_key'])) {
                return array("status" => "100", "message" => "Invalid Key");
            }

        
            $mid = 0;
            if (isset($parameter['mid'])) {
                $mid = $parameter['mid'];
            }
            
            //return array("status" => "100", "message" => $mid."@".$parameter['private_key']); 
        
        $private_key = $parameter['private_key'];
        $row = self::db_query("SELECT rechargeproid FROM rechargepro_account WHERE public_secret = ? AND active = '1' LIMIT 1",array($private_key));
        $rechargeproid = $row[0]['rechargeproid'];
                
            if (empty($rechargeproid)) {
                return array("status" => "100", "message" => "Invalid Key");
            }
        
        
        $row = self::db_query("SELECT id,message,message_type FROM messages WHERE id > ? AND rechargeproid = '0' ORDER BY id DESC LIMIT 1",array($mid));
        $message = $row[0]['message'];
        $id = $row[0]['id'];
        $message_type = $row[0]['message_type'];
        
        
        $row = self::db_query("SELECT id,message,message_type FROM messages WHERE id > ? AND rechargeproid = ?  ORDER BY id DESC LIMIT 1",array($mid,$rechargeproid ));
        $messageb = $row[0]['message'];
        $idb = $row[0]['id'];
        $message_typeb = $row[0]['message_type'];
        
        
        if(empty($id)){$id = 0;}
        if(empty($idb)){$idb = 0;}
        
        $newid = $idb;
        if($id > $idb){$newid = $id;}
        
        
        $retutn = $newid."@";
        if(!empty($message)){$retutn .= $message."@".$message_type;}
        
        if(!empty($messageb) && !empty($message)){$retutn .= "@".$messageb."@".$message_typeb;}
        
        if(!empty($messageb) && empty($message)){$retutn .= $messageb."@".$message_typeb;}

   return array("status" => "200", "message" => $retutn); 
      
    }
    
    
    
    
    
}
?>