<?php
if(!defined('MENUAUTH_DOCROOT') && !defined('AUTH_DOCROOT')  && !defined('RAUTH_DOCROOT')   && !defined('AUTH_DOCROOTB')){
 exit('No direct script access');
 }


return array(
'version'=>1.0,
'max_request_hits' => '500000000',
'max_request_time' => '500000000',
'enable_block_ip' => false, #edit ip.ini to add ip to block
    

'enable_memcache' => false,
'theme_folder' => 'theme/',
'body_holder'=>'content_body',


/* THEME CONFIGURATION START */
'theme'=>'pro',
/* THEME CONFIGURATION END */

/* USERWIDGET CONFIGURATION START */
'user_widget_state_change'=>false,
/* USERWIDGET CONFIGURATION END */ 

/* USERIMAGE CONFIGURATION START */
'user_change_image'=>true,
/* USERIMAGE CONFIGURATION END */ 

/* USEREDIT CONFIGURATION START */
'user_edit_information'=>true,
/* USEREDIT CONFIGURATION END */ 
 
 /* SITEWIDTH CONFIGURATION START */
'site_width'=>'width:100%', 
/* SITEWIDTH CONFIGURATION END */   

 #do not edit below manualy unless you know what you are doing
 /* WEBSITE CONFIGURATION START */
'website_root'=>'http://localhost/rechargepro/admin',
'author'=>'Safe Parking Limited',
'app_name'=>'RECHARGE PRO ADMIN',       
'admin_email'=>'s.makinde@safeparkingltd.com',
'admin_mobile'=>'08026633096',
'admin_website'=>'www.paywise.com.ng',
/* WEBSITE CONFIGURATION END */

'motto'=>'Collectively Progressing',
'messaging_alert'=>true,


 /* DATABASE CONFIGURATION START */
'user_key'=>'asU7234',
'database_dsn' => 'mysql:dbname=recharge_admin; host=localhost;',
'database_user' => 'recharge',
'database_pass' => '1@sgFT$',
/* DATABASE CONFIGURATION END */


 /* DATABASE2 CONFIGURATION START */
'database_dsn2' => 'mysql:dbname=recharge; host=localhost;',
'database_user2' => 'recharge',
'database_pass2' => '1@sgFT$',
/* DATABASE2 CONFIGURATION END */



#For development purpose
 /* LOG CONFIGURATION START */
'log_error' => true, // TRUE or FALSE
'display_error' => true, // TRUE or FALSE
/* LOG CONFIGURATION END */

 /* LICENCE CONFIGURATION START */
'licence'=>'VB36754N4TMN4503683N43RT6732VRY57234G65756H75234Y234234FY2J2U3523JH43274234G23423V4237423U423542H34275342U4275432YU423423234',
 /* LICENCE CONFIGURATION END */
 
   /* DASHBOARD CONFIGURATION START */
'show_dashboard'=>true,
'dashboard_size'=>'1200px',
'dashboard_format'=>'2',//1==dashboard,2=blog
'allow_user_theme_sellection'=>false,
'allow_ipchange'=>true,
/* DASHBOARD CONFIGURATION END */ 
 
/* ICON_SIZE CONFIGURATION START */
'icon_size'=>2.0,
/* ICON_SIZE CONFIGURATION END */

'disable_chat'=>1,//1 yes, 0 no
'chat_url' => '/admin/stalk/',
    
    
'server_address'=>'http://auth.splvending.com',
'local_authentication'=>true,
'authentication_server'=>'http://auth.splvending.com/',
'server_id'=>'SPLDIAMOND',
'backup_address'=>'',

 
 'imagefolder'=>"items")
 /* ENDING CONFIGURATION START */;/* ENDING CONFIGURATION END */