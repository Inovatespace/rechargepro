<?php
if (isset($service)) {


    $rowpromo1 = self::db_query("SELECT bill_formular,promo1,services_category FROM quickpay_services WHERE services_key = ? LIMIT 1",
        array($service));
    $bill_formular = $rowpromo1[0]['bill_formular'];
    $promo1 = $rowpromo1[0]['promo1'];
    $promo1category = $rowpromo1[0]['services_category'];
    $promo1 = 0.5;
    if (in_array($promo1category, array(1, 2)) && $promo1 > 0) {


        if ($quickpayid > 0) {
            $rowquickpayrole = self::db_query("SELECT quickpayrole FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                array($quickpayid));
            $promo1role = $rowquickpayrole[0]['quickpayrole'];
        } else {
            $promo1role = 4;
        }


        if ($promo1role > 3) {

            $mypercent = $promo1;
            if ($bill_formular == 0) {
                $mypercent = (($amount * $promo1) / 100);
            }

            // $amount = $amount + $mypercent;

        }


    }

}
?>