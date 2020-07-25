<?php
include ('../engine.autoloader.php');


    
    
    
  if(isset($_REQUEST['password'])){
    $password = $_REQUEST['password'];
    $password2 = $_REQUEST['password2'];
    $id = $_REQUEST['id'];
    
    if($password != $password2){
      echo "Password field do not match"; exit;  
    }
    
    $date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
    $rowa = $engine->db_query("SELECT email,code FROM temp_code WHERE id = ? AND date >= ? AND status = '0' LIMIT 1",array($id,$date));
    $mainid = $rowa[0]['email'];
    $code = $rowa[0]['code'];;
    
    if(empty($mainid)){
     echo "The code entered has been used"; exit;     
    }
    

    
    $password = $engine->shuzia_HashPassword($password,$engine->RandomString(4,20));
    //$password = sha1(md5($password) . $engine->config("user_key"));
    $engine->db_query("UPDATE rechargepro_account SET password = ? WHERE email = ? LIMIT 1",array($password,$mainid));
    
    $engine->db_query("UPDATE temp_code SET status = '1' WHERE code = ? LIMIT 1",array($code));
    
    echo "ok";
    exit;
  }  
    
    
    
    
    
    
    
    
    
    
    
    
    



if (empty($_REQUEST['username'])) {
    echo "All Fields are compulsory";
    exit;
}

$username = $_REQUEST['username'];


$rowa = $engine->db_query("SELECT email,mobile FROM rechargepro_account WHERE email = ? AND active = ? LIMIT 1",array($username,1));
$row = $rowa[0];
$email = $row['email']; 


if (empty($row['email'])){
    echo "Email entered is not found on our record";
    exit;
}



$code = date("dymhHis").$engine->RandomString("4",6).$engine->RandomString("1",6).$engine->RandomString("4",6);

$message = '<!--[if (mso 16)]>
    <style type="text/css">
    a {text-decoration: none;}
    </style>
    <![endif]--> 
  <!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--> 
  <!--[if !mso]><!-- --> 
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i" rel="stylesheet"> 
  <!--<![endif]--> 
  <style type="text/css">
@media only screen and (max-width:600px) {p, ul li, ol li, a { font-size:16px!important; line-height:150%!important } h1 { font-size:32px!important; text-align:center; line-height:120%!important } h2 { font-size:26px!important; text-align:center; line-height:120%!important } h3 { font-size:20px!important; text-align:center; line-height:120%!important } h1 a { font-size:32px!important } h2 a { font-size:26px!important } h3 a { font-size:20px!important } .es-menu td a { font-size:16px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:16px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:16px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } .es-button { font-size:16px!important; display:inline-block!important; border-width:15px 30px 15px 30px!important } .es-btn-fw { border-width:10px 0px!important; text-align:center!important } .es-adaptive table, .es-btn-fw, .es-btn-fw-brdr, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0px!important } .es-m-p0r { padding-right:0px!important } .es-m-p0l { padding-left:0px!important } .es-m-p0t { padding-top:0px!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } .es-desk-hidden { display:table-row!important; width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } .es-desk-menu-hidden { display:table-cell!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } }
#outlook a {
	padding:0;
}
.ExternalClass {
	width:100%;
}
.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
	line-height:100%;
}
.es-button {
	mso-style-priority:100!important;
	text-decoration:none!important;
}
a[x-apple-data-detectors] {
	color:inherit!important;
	text-decoration:none!important;
	font-size:inherit!important;
	font-family:inherit!important;
	font-weight:inherit!important;
	line-height:inherit!important;
}
.es-desk-hidden {
	display:none;
	float:left;
	overflow:hidden;
	width:0;
	max-height:0;
	line-height:0;
	mso-hide:all;
}
</style> 

 <div style="width:100%;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0;"> 
  <div class="es-wrapper-color" style="background-color:#EEEEEE;"> 
  
  
   <!--[if gte mso 9]>
			<v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
				<v:fill type="tile" color="#eeeeee"></v:fill>
			</v:background>
		<![endif]--> 
   <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;"> 
     <tr style="border-collapse:collapse;"> 
      <td valign="top" style="padding:0;Margin:0;"> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
         <tr style="border-collapse:collapse;"> 
         </tr> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;"> 
           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;" width="600" cellspacing="0" cellpadding="0" align="center"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" style="Margin:0;padding-left:10px;padding-right:10px;padding-top:15px;padding-bottom:15px;"> </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
         <tr style="border-collapse:collapse;"> 
         </tr> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;"> 
           <table class="es-header-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#044767;" width="600" cellspacing="0" cellpadding="0" bgcolor="#044767" align="center"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" style="Margin:0;padding-top:35px;padding-bottom:35px;padding-left:35px;padding-right:35px;"> 
               <!--[if mso]><table width="530" cellpadding="0" cellspacing="0"><tr><td width="340" valign="top"><![endif]--> 
               <table class="es-left" cellspacing="0" cellpadding="0" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td class="es-m-p0r es-m-p20b" width="340" valign="top" align="center" style="padding:0;Margin:0;"> 
                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                     <tr style="border-collapse:collapse;"> 
                      <td class="es-m-txt-c" align="left" style="padding:0;Margin:0;"> <h1 style="Margin:0;line-height:36px;mso-line-height-rule:exactly;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;font-size:36px;font-style:normal;font-weight:bold;color:#FFFFFF;">RechargePro</h1></td> 
                     </tr> 
                   </table> </td> 
                 </tr> 
               </table> 
               <!--[if mso]></td><td width="20"></td><td width="170" valign="top"><![endif]--> 
               <table cellspacing="0" cellpadding="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                 <tr class="es-hidden" style="border-collapse:collapse;"> 
                  <td class="es-m-p20b" width="170" align="left" style="padding:0;Margin:0;"> 
                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:5px;"> 
                       <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td style="padding:0;Margin:0px;border-bottom:1px solid #044767;background:rgba(0, 0, 0, 0) none repeat scroll 0% 0%;height:1px;width:100%;margin:0px;"></td> 
                         </tr> 
                       </table> </td> 
                     </tr> 
                     <tr style="border-collapse:collapse;"> 
                      <td style="padding:0;Margin:0;"> 
                       <table cellspacing="0" cellpadding="0" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr style="border-collapse:collapse;"> 
                          <td align="left" style="padding:0;Margin:0;"> 
                           <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                             <tr style="border-collapse:collapse;"> 
                              <td align="right" style="padding:0;Margin:0;"></td> 
                             </tr> 
                           </table> </td> 
                          <td valign="top" align="left" style="padding:0;Margin:0;padding-left:10px;"> </td> 
                         </tr> 
                       </table> </td> 
                     </tr> 
                   </table> </td> 
                 </tr> 
               </table> 
               <!--[if mso]></td></tr></table><![endif]--> </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;"> 
           <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" style="padding:0;Margin:0;padding-left:35px;padding-right:35px;padding-top:40px;"> 
               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td width="530" valign="top" align="center" style="padding:0;Margin:0;"> 
                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                   </table> </td> 
                 </tr> 
               </table> </td> 
             </tr> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" style="Margin:0;padding-top:30px;padding-bottom:35px;padding-left:35px;padding-right:35px;"> 
              
<div style="color: #002366; font-size:25px; margin-bottom:20px;">Forgot Your rechargepro Password</div>
<div style="margin-bottom:10px;">Oops! We got the word that you forgot your password. No problem, these things happen.
To reset your rechargepro password for the account with the email , click the button below and you can choose a new password.</div>

<div style="text-align: center;">
<a href="https://rechargepro.com.ng/forgetpassword&code='.$code.'"><button style="background-color:#002366; border:none; color:white; padding:10px 20px; margin-bottom: 10px; width:70%; cursor: pointer;" type="submit"> RECOVER PASSWORD</button></a></div>



              
          </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;"> 
           <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" style="padding:0;Margin:0;padding-top:15px;padding-left:35px;padding-right:35px;"> 
               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td width="530" valign="top" align="center" style="padding:0;Margin:0;"> 
                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                     
                   </table> </td> 
                 </tr> 
               </table> </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table> 
       <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;"> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;"> 
           <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#1B9BA3;border-bottom:10px solid #48AFB5;" width="600" cellspacing="0" cellpadding="0" bgcolor="#1b9ba3" align="center"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" style="padding:0;Margin:0;"> 
               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td width="600" valign="top" align="center" style="padding:0;Margin:0;"> 
                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                     <tr style="border-collapse:collapse;"> 
                      <td style="padding:0;Margin:0;"> 
                       <table class="es-menu" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                         <tr class="links-images-top" style="border-collapse:collapse; color:white;"> 
                          <td style="Margin:0;padding-left:5px;padding-right:5px;border:0;" id="esd-menu-id-0" bgcolor="transparent" align="center"><div>Need some more assistance? Just visit our <a href="https://rechargepro.com.ng/support">rechargepro support page</a>.</div></td> 
                          
                         </tr> 
                       </table> </td> 
                     </tr> 
                   </table> </td> 
                 </tr> 
               </table> </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table> 
       <table class="es-footer" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top;"> 
         <tr style="border-collapse:collapse;"> 
          <td align="center" style="padding:0;Margin:0;"> 
           <table class="es-footer-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;border-top:10px solid #48AFB5;" width="600" cellspacing="0" cellpadding="0" align="center"> 
             <tr style="border-collapse:collapse;"> 
              <td align="left" style="Margin:0;padding-top:35px;padding-left:35px;padding-right:35px;padding-bottom:40px;"> 
               <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                 <tr style="border-collapse:collapse;"> 
                  <td width="530" valign="top" align="center" style="padding:0;Margin:0;"> 
                   <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;"> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="center" style="padding:0;Margin:0;padding-bottom:15px;"></td> 
                     </tr> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="center" style="padding:0;Margin:0;"> <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;line-height:21px;color:#333333;"><strong>4 jerome udoji, Asokoro, Abuja </strong></p> <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:\'open sans\', \'helvetica neue\', helvetica, arial, sans-serif;line-height:21px;color:#333333;"><strong>08181770906<br />support@rechargepro.com.ng</strong></p> </td> 
                     </tr> 
                   
                   </table> </td> 
                 </tr> 
               </table> </td> 
             </tr> 
           </table> </td> 
         </tr> 
       </table> </td> 
     </tr> 
   </table> 
  </div>  
 </div>';


$engine->db_query("INSERT INTO temp_code (email,code) VALUES (?,?)",array($email,$code));


$engine->send_mail(array('noreply@rechargepro.com.ng', 'Rechargepro!'),$email, "Password Recovery", $message,$attachment=array());





echo "ok"; exit;

?>