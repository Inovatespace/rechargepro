<?php
ini_set('max_execution_time', 5000);
$now = 0;

	$ftp_server = "localhost";
	$ftp_user_name = "test";
	$ftp_user_pass = "test";


   ob_end_flush();
   $remote_file = 'backup.zip';
   $local_file = 'backup.zip';

   $fp = fopen($local_file, 'r');
   $conn_id = ftp_connect($ftp_server);
   $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
   $ret = ftp_nb_fput($conn_id, $remote_file, $fp, FTP_BINARY);
   while ($ret == FTP_MOREDATA) {
       // Establish a new connection to FTP server
       if(!isset($conn_id2)) {
           $conn_id2 = ftp_connect($ftp_server);
           $login_result2 = ftp_login($conn_id2, $ftp_user_name, $ftp_user_pass);
       }
      
       // Retreive size of uploaded file.
       if(isset($conn_id2)) {
           clearstatcache(); // <- this must be included!!
           $remote_file_size = ftp_size($conn_id2, $remote_file);
       }

       // Calculate upload progress
       $local_file_size  = filesize($local_file);
       if (isset($remote_file_size) && $remote_file_size > 0 ){
           $i = ($remote_file_size/$local_file_size)*100;
					 
                     $thei = ceil(trim($i));
                     if($now != $thei){
                        
                        $fpa = fopen( 'progress.txt', 'w' );
                        fputs( $fpa, trim("$now"));
                        fclose( $fpa );
                     
                     }
                     $now = $thei;
           //printf("%d%% uploaded", $i);
           flush();
			
       } 
       $ret = ftp_nb_continue($conn_id);
			 
   }


	
   if ($ret != FTP_FINISHED) {
   $fpa = fopen( 'progress.txt', 'w' );
   fputs( $fpa, "error" );
   fclose( $fpa );
   }
   else {
   $fpa = fopen( 'progress.txt', 'w' );
   fputs( $fpa, "done" );
   fclose( $fpa );
   }
   fclose($fp);
?>