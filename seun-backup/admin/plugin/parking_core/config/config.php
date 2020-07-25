<?php

return array(
    'bundle_email' => 'olisa.igboka@safeparkingltd.com',

    'database_dsn' => 'mysql:dbname=najectdb; host=localhost;',
    'database_user' => 'root',
    'database_pass' => 'alltech',

'can_closediscount'=>array("admin","seuntech","olisa"),
    'givediscount' => array(
        'Managing Director' => '10',
        'seuntech' => '15',
        'Chief Operating Officer' => '15'),

        
/* LAST CLOSE DISCOUNT DATE START */
'close_discount_date' => '2015-04-09',
/* LAST CLOSE DISCOUNT DATE END */



    #For development purpose
    'log_error' => true, // TRUE or FALSE
    'display_error' => true, // TRUE or FALSE

  'videourl'=>'http://localhost/video',

    'public_holiday' => array(
        '2013-04-01',
        '2013-05-01',
        '2013-05-29',
        '2013-08-08',
        '2013-08-09',
        '2013-10-01',
        '2013-10-15',
        '2013-10-16'),

  
    'movestate' => array(
        'General Manager',
        'Head, IT',
        'Managing Director',
        'Human Resources Manager',
        'Office Assistant Manager',
        'Chief Operating Officer')
        //account finance gm headfinance headhr headopp headpd it legal md opps sec
        );
