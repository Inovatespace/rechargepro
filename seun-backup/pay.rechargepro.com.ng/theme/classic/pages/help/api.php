<?php
include "../../../../engine.autoloader.php";




if(!empty($_REQUEST['subject']) && !empty($_REQUEST['message'])){
    
    
    

        $key = urldecode(trim($_REQUEST["private_key"]));
        $row = $engine->db_query("SELECT quickpayid FROM appauth WHERE authkey = ?  LIMIT 1",
            array($key)); //AND status = '0'
        $quickpayid = $row[0]['quickpayid'];


        $row = $engine->db_query("SELECT public_secret,email, name FROM quickpay_account WHERE quickpayid = ? AND active = '1' LIMIT 1",
            array($quickpayid));
        $private_key = 
        
        
    
    
    
    $_REQUEST['name'] = $row[0]['name'];
    $_REQUEST['email'] = $row[0]['email'];
    $_REQUEST['priority'] = 3;
    

        $datetime = date("Y-m-d H:i:s");
        $lastupdate = date("Y-m-d H:i:s");
        $trackid = $engine->RandomString(1,4).$engine->RandomString(2,4).date("His");

        $insertid = $engine->db_query("INSERT INTO contact_tickets (trackid,name,email,category,priority,subject,message,dt,lastupdate,ip) VALUES (?,?,?,?,?,?,?,?,?,?)",
            array(
            $trackid,
            urldecode($_REQUEST['name']),
            urldecode($_REQUEST['email']),
            urldecode($_REQUEST['category']),
            urldecode($_REQUEST['priority']),
            urldecode($_REQUEST['subject']),
            urldecode($_REQUEST['message']),
            $datetime,
            $lastupdate,
            $engine->getRealIpAddr()));
            
            
            
            
        if($_FILES['file']['name']){
            $alphabet = "a";
            $file = array();
 

          //Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'];
            $path_info = pathinfo($_FILES['file']['name']);

            //Make sure we have a filepath
            if($tmpFilePath != ""){
                
            //docx doc pdf xls xlsx csv txt jpg jpeg png 
                
            $file_info = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $file_info->buffer(file_get_contents($tmpFilePath));
            
            
            if(in_array($mime_type,array("image/jpeg; charset=binary","image/jpg","image/jpeg","image/png","application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/pdf","text/csv","application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document"))){    
          
          
            
  
                //save the filename
                $shortname = $insertid."_$alphabet.".$path_info['extension'];
               $file[] = $shortname;

                //save the url and the file
                $filePath = "../../../../ticket/" . $shortname;

                //Upload the file into the temp dir
                if(move_uploaded_file($tmpFilePath, $filePath)) {
                   
                $engine->db_query("UPDATE contact_tickets SET attachment1=? , is_attachment = '1' WHERE id = ?",array($shortname,$insertid));

                    }
                }
              }
        
    }

echo "Thank You message sent"; exit;
}else{
echo "All fields are compulsory"; exit;
}
