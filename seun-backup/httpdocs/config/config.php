<?php
if(!defined('MENUAUTH_DOCROOT') && !defined('AUTH_DOCROOT')  && !defined('RAUTH_DOCROOT')   && !defined('AUTH_DOCROOTB')){
 exit('No direct script access');
 }


return array(
'version'=>1.0,
'max_request_hits' => '500000000',
'max_request_time' => '500000000',
'enable_block_ip' => false, #edit ip.ini to add ip to block



'theme_folder' => 'theme/',
'body_holder'=>'content_body',


/* THEME CONFIGURATION START */'theme'=>'classic',/* THEME CONFIGURATION END */


 /* SITEWIDTH CONFIGURATION START */
'site_width'=>'width:100%', 
/* SITEWIDTH CONFIGURATION END */   

 #do not edit below manualy unless you know what you are doing
 /* WEBSITE CONFIGURATION START */
'website_root'=>'https://rechargepro.com.ng/',
'author'=>'Seun Makinde',
'app_name'=>'RECHARGE PRO',       
'admin_email'=>'seuntech@yahoo.com',
'admin_mobile'=>'08183874966',
'admin_website'=>'www.seuntech.com',
/* WEBSITE CONFIGURATION END */


/* DATABASE CONFIGURATION START */
'user_key'=>'agG_i@6&',
'database_dsn' => 'mysql:dbname=recharge; host=localhost;',
'database_user' => 'recharge',
'database_pass' => '1@sgFT$',
/* DATABASE CONFIGURATION END */

/* DATABASE2 CONFIGURATION START */
'database_dsn2' => 'mysql:dbname=recharge_admin; host=localhost;',
'database_user2' => 'recharge',
'database_pass2' => '1@sgFT$',
/* DATABASE2 CONFIGURATION END */


/* ICON_SIZE CONFIGURATION START */
'icon_size'=>2.0,
/* ICON_SIZE CONFIGURATION END */

/* RAVE PUBLIC_KEY CONFIGURATION START */
'rave_public_key'=>'FLWPUBK-d9774ccef433a08174cc2597b7f0119c-X',
'rave_secrete_key'=>'FLWSECK-efba7abe0decca4441c236caf91d9c76-X',
/* RAVE PUBLIC_KEY CONFIGURATION END */


/* BRIX CONFIGURATION START */
'brixurl'=>'https://baxi.capricorndigi.com/app',
'brixusername'=>'vertis',
'brixtoken'=> base64_decode('zcFyHpA06K2dch6439QmkOHVlmsc074dbrEtOKJVLQGOEgLw1EQnGVgCvYS3b+j1z96gv48gQZSed4AQ4Xjk1g=='),
/* BRIX CONFIGURATION END */


/* Kallak CONFIGURATION START */
'kusername'=>'ccd07d214200_live',
'kpassword'=>'n5q1FaU8xz2x4vLd2edfg$',
'kauthurl'=> 'https://api.kvg.com.ng/auth/live',
'kverifyurl'=> 'https://api.kvg.com.ng/live/energy/aedc/prepaid/meter/',
'kbuyurl'=> 'https://api.kvg.com.ng/live/energy/aedc/prepaid/vend/',
/* Kallak CONFIGURATION END */



/* MOBIFINE CONFIGURATION START */
'mobiusername'=>'info@vertistechnologies.com',
'mobipassword'=>'Vertis@Tech888',
/* MOBIFINE CONFIGURATION END */



#For development purpose
/* LOG CONFIGURATION START */
'log_error' => true, // TRUE or FALSE
'display_error' => false, // TRUE or FALSE
/* LOG CONFIGURATION END */

'allow_ipchange' => true,
 /* LICENCE CONFIGURATION START */
'licence'=>'VB36754N4TMN4503683N43RT6732VRY57234G65756H75234Y234234FY2J2U3523JH43274234G23423V4237423U423542H34275342U4275432YU423423234',
 /* LICENCE CONFIGURATION END */

)
 /* ENDING CONFIGURATION START */;/* ENDING CONFIGURATION END */