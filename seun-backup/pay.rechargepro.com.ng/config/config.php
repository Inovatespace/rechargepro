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
'website_root'=>'https://jedc.com/',
'author'=>'Seun Makinde',
'app_name'=>'QuickPay',       
'admin_email'=>'seuntech@yahoo.com',
'admin_mobile'=>'08183874966',
'admin_website'=>'www.seuntech.com',
/* WEBSITE CONFIGURATION END */


/* DATABASE CONFIGURATION START */
'user_key'=>'agG_i@6&',
'database_dsn' => 'mysql:dbname=dipo; host=localhost;',
'database_user' => 'dipo',
'database_pass' => 'dipo123',
/* DATABASE CONFIGURATION END */

/* DATABASE2 CONFIGURATION START */
'database_dsn2' => 'mysql:dbname=dipo_admin; host=localhost;',
'database_user2' => 'root',
'database_pass2' => '',
/* DATABASE2 CONFIGURATION END */


/* ICON_SIZE CONFIGURATION START */
'icon_size'=>2.0,
/* ICON_SIZE CONFIGURATION END */

/* RAVE PUBLIC_KEY CONFIGURATION START */
'rave_public_key'=>'FLWPUBK-95e3499371cafa47a8c1d8cf7f8de7c6-X',
'rave_secrete_key'=>'FLWSECK-46c8efd4a98fd132345cceab8f4d2767-X',
/* RAVE PUBLIC_KEY CONFIGURATION END */


/* BRIX CONFIGURATION START */
'rechargeurl'=>'https://rechargepro.com.ng/api/public/transaction/',
'rechargekey'=>'1234QWER5678TYUI',
'rechargetoken'=> '1234:QWER:5678:TYUI',
/* BRIX CONFIGURATION END */



/* Kallak CONFIGURATION START */
'kusername'=>'369eaca64c4d_live',
'kpassword'=>'AiA256z0WJ245ERicb99EA1@k',
'kauthurl'=> 'https://api.kvg.com.ng/auth/live',
'kverifyurl'=> 'https://api.kvg.com.ng/live/energy/aedc/prepaid/meter/',
'kbuyurl'=> 'https://api.kvg.com.ng/live/energy/aedc/prepaid/vend/',
/* Kallak CONFIGURATION END */


/* MOBIFINE CONFIGURATION START */
'mobiusername'=>'inewuser@quickpay',
'mobipassword'=>'Quickpay@34588',
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