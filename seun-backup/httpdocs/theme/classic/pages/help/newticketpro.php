<?php
include "../../../../engine.autoloader.php";


if(!empty($_REQUEST['subject']) && !empty($_REQUEST['message'])){

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
            
            
            
            
            
        if(count($_FILES['file']['name']) > 0){
            
            $file = array();
            
        //Loop through each file
        for($i=0; $i<count($_FILES['file']['name']); $i++){
          //Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];

            //Make sure we have a filepath
            if($tmpFilePath != ""){
                
            //docx doc pdf xls xlsx csv txt jpg jpeg png 
                
            $file_info = new finfo(FILEINFO_MIME);
            $mime_type = $file_info->buffer(file_get_contents($tmpFilePath));
            if(in_array($mime_type,array("image/png; charset=binary","image/jpg; charset=binary","image/jpeg; charset=binary","image/jpg","image/jpeg","image/png","application/vnd.ms-excel","application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/pdf","text/csv","application/msword","application/vnd.openxmlformats-officedocument.wordprocessingml.document"))){    
          
}
                //save the filename
                $shortname = $insertid."_".$_FILES['file']['name'][$i];
              
                //save the url and the file
                $filePath = "../../../../ticket/" . $shortname;

                //Upload the file into the temp dir
                if(move_uploaded_file($tmpFilePath, $filePath)) {
               
                 $engine->db_query("INSERT INTO contact_attachment (postid,attachment) VALUES (?,?)",array($insertid,$shortname));
                $engine->db_query("UPDATE contact_tickets SET is_attachment = '1' WHERE id = ?",array($insertid));
}
                }
              }
        
    }

echo "Thank You message sent"; exit;
}else{
echo "All fields are compulsory"; exit;
}
