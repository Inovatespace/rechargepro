<?php
if (isset($service)) {


    $rowpromo1 = self::db_query("SELECT bill_formular,promo1,services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",array($service));
    $bill_formular = $rowpromo1[0]['bill_formular'];
    $promo1 = $rowpromo1[0]['promo1'];
    $promo1category = $rowpromo1[0]['services_category'];
    $promo1 = 0.5;
    if (in_array($promo1category, array(1, 2)) && $promo1 > 0) {


        if ($rechargeproid > 0) {
            $rowrechargeprorole = self::db_query("SELECT rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                array($rechargeproid));
            $promo1role = $rowrechargeprorole[0]['rechargeprorole'];
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