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
'website_root'=>'https://newmcbpay.com/',
'author'=>'Seun Makinde',
'app_name'=>'RECHARGE PRO',       
'admin_email'=>'seuntech@yahoo.com',
'admin_mobile'=>'08183874966',
'admin_website'=>'www.seuntech.com',
/* WEBSITE CONFIGURATION END */


/* DATABASE CONFIGURATION START */
'user_key'=>'agG_i@6&',
'database_dsn' => 'mysql:dbname=mcbpay; host=localhost;',
'database_user' => 'root',
'database_pass' => '',
/* DATABASE CONFIGURATION END */

/* DATABASE2 CONFIGURATION START */
'database_dsn2' => 'mysql:dbname=mcbpay_admin; host=localhost;',
'database_user2' => 'root',
'database_pass2' => '',
/* DATABASE2 CONFIGURATION END */


/* ICON_SIZE CONFIGURATION START */
'icon_size'=>2.0,
/* ICON_SIZE CONFIGURATION END */

/* RAVE PUBLIC_KEY CONFIGURATION START */
'rave_public_key'=>'FLWPUBK-b435b3754240acbc409b1cd14e730fb7-X',
'rave_secrete_key'=>'FLWSECK-36b56939db2f62a7e037f85ba8ef0637-X',
/* RAVE PUBLIC_KEY CONFIGURATION END */


/* BRIX CONFIGURATION START */
'brixurl'=>'https://test.platform.baxibox.com/app',
'brixusername'=>'brinqAfrica',
'brixtoken'=> base64_decode('eDBw6axRdbKMNWgh5VvjIhTGD1YNQu/S/8vhyadfvzhhltFYSDdgq0JbP3J/NGtxjxpKtjjxPS3pruLDbJknEw=='),
/* BRIX CONFIGURATION END */






/* Kallak CONFIGURATION START */
'kusername'=>'9c7f52087165_demo',
'kpassword'=>'n5q1FaU8xz2x4vLd2u8yW2$',
'kauthurl'=> 'https://api.kvg.com.ng/auth/demo',
'kverifyurl'=> 'https://api.kvg.com.ng/demo/energy/aedc/prepaid/meter/',
'kbuyurl'=> 'https://api.kvg.com.ng/demo/energy/aedc/prepaid/vend/',
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