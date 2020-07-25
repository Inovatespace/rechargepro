<?php
class percentage extends Api
{

    function calculate_per($parameter)
    {

        if (!isset($parameter['tid'])) {
            exit;
        }

        $itd = $parameter['tid'];
        $row = self::db_query("SELECT quickpay_service_charge,quickpay_status,quickpay_subservice,account_meter,phone,email,amount,quickpayid,transactionid,payment_method,quickpay_status_code,quickpay_print FROM quickpay_transaction_log WHERE transactionid = ? AND quickpay_status_code = '0' LIMIT 1",
            array($itd));
        $transactionid = $row[0]['transactionid'];
        $service = $row[0]['quickpay_subservice'];
        $amount = $row[0]['amount'];
        $quickpayid = $row[0]['quickpayid'];
        $payment_method = $row[0]['payment_method'];
        $quickpay_service_charge = $row[0]['quickpay_service_charge'];


        $rowb = self::db_query("SELECT services_category,cordinator_percentage,percentage,bill_formular,bill_quickpayfull_percentage,promo1 FROM quickpay_services WHERE services_key = ? LIMIT 1",
            array($service));
        $services_category = $rowb[0]['services_category'];
        $promo1 = $rowb[0]['promo1'];
        $cordinator_percentage = $rowb[0]['cordinator_percentage'];
        $percentage = $rowb[0]['percentage'];
        $bill_formular = $rowb[0]['bill_formular'];
        $bill_quickpayfull_percentage = $rowb[0]['bill_quickpayfull_percentage'];


        //**when  creating profile copy percentage
        //**when specifing percemtage affect all under
        // get your percentage


        //check for fee here


        $rowb = self::db_query("SELECT bill_quickpayfull_percentage,quickpayid,cordinator_percentage,percentage,bill_formular FROM quickpay_services_agent WHERE services_key = ? AND quickpayid = ? LIMIT 1",
            array($service, $quickpayid));
        if (!empty($rowb[0]['quickpayid'])) {
            $cordinator_percentage = $rowb[0]['cordinator_percentage'];
            $percentage = $rowb[0]['percentage'];
            $bill_formular = $rowb[0]['bill_formular'];
            ///  $bill_quickpayfull_percentage = $rowb[0]['bill_quickpayfull_percentage'];
        }


        //check for fixedcharge
        $fixedfee = 0;
        $rowb = self::db_query("SELECT fixedfee FROM quickpay_services_fixed WHERE services_key = ? AND quickpayid = ? LIMIT 1",
            array($service, $quickpayid));
        if (!empty($rowb[0]['fixedfee'])) {
            $fixedfee = $rowb[0]['fixedfee'];
        }


        if ($quickpayid == 0) {
            $percent = ($bill_quickpayfull_percentage) - (($amount * 1.5) / 100);
            if ($bill_formular == 0) {
                $percent = (($amount * ($bill_quickpayfull_percentage)) / 100) - (($amount *
                    1.5) / 100);
            }

            $bp = $percent;
            if (!in_array($services_category, array(
                2,
                3,
                4))) {
                $bp = $percent + 100;
            }


            //add user percentage and reduce quickpay
            $promopercet = 0;
            if (in_array($services_category, array(1, 2)) && $promo1 > 0) {


                if ($quickpayid > 0) {
                    $rowquickpayrole = self::db_query("SELECT quickpayrole FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                        array($quickpayid));
                    $promo1role = $rowquickpayrole[0]['quickpayrole'];
                } else {
                    $promo1role = 4;
                }


                if ($promo1role > 3) {

                    $promopercet = $promo1;
                    if ($bill_formular == 0) {
                        $promopercet = (($amount * $promo1) / 100);
                    }
                }

                $bp = $bp - $promopercet;

            }


            self::db_query("UPDATE quickpay_transaction_log SET refererprofit =?,agentprofit =?,cordprofit =?,quickpayprofit = ?, promo1 =? WHERE transactionid = ? LIMIT 1",
                array(
                0,
                0,
                0,
                $bp,
                $promopercet,
                $transactionid));

        } else {
            //////////////////////
            if ($payment_method == "1") {
                $percent = ($bill_quickpayfull_percentage) - (($amount * 1.5) / 100);
                if ($bill_formular == 0) {
                    $percent = (($amount * ($bill_quickpayfull_percentage)) / 100) - (($amount *
                        1.5) / 100);
                }

                $bp = $percent;
                if (!in_array($services_category, array(
                    2,
                    3,
                    4))) {
                    $bp = $percent + 100;
                }

                //add user percentage and reduce quickpay
                $promopercet = 0;
                if (in_array($services_category, array(1, 2)) && $promo1 > 0) {


                    if ($quickpayid > 0) {
                        $rowquickpayrole = self::db_query("SELECT quickpayrole FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                            array($quickpayid));
                        $promo1role = $rowquickpayrole[0]['quickpayrole'];
                    } else {
                        $promo1role = 4;
                    }


                    if ($promo1role > 3) {

                        $promopercet = $promo1;
                        if ($bill_formular == 0) {
                            $promopercet = (($amount * $promo1) / 100);
                        }
                    }

                    $bp = $bp - $promopercet;

                }
                self::db_query("UPDATE quickpay_transaction_log SET refererprofit =?,agentprofit =?,cordprofit =?,quickpayprofit = ?, promo1 = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    0,
                    0,
                    0,
                    $bp,
                    $promopercet,
                    $transactionid));
            }
            /////////////////////////////


            //////////////////////////////
            if ($payment_method == "2") {
                $rowb = self::db_query("SELECT ac_ballance, quickpayid, profile_creator, quickpay_cordinator, quickpayrole FROM quickpay_account WHERE quickpayid = ? AND active = '1' LIMIT 1",
                    array($quickpayid));
                $ac_ballance = $rowb[0]['ac_ballance'];
                $quickpayid = $rowb[0]['quickpayid'];
                $profile_creator = $rowb[0]['profile_creator'];
                $quickpayrole = $rowb[0]['quickpayrole'];
                $quickpay_cordinator = $rowb[0]['quickpay_cordinator'];

                $agentprofit = 0;
                $referearprofit = 0;
                $corprofit = 0;
                $quickpaypercentage = 0;


                if ($quickpayrole > 3) {
                    //check for refere
                    if ($profile_creator > 0) {
                        //give 0.2%
                        $special_percent = ($amount * 0.5) / 100;
                        if ($bill_formular == 1) {
                            $special_percent = ($specialamount * 5) / 100;
                        }
                        $referearprofit = $special_percent;
                        //UPDATE REFEREE + PAYOUT

                        if ($referearprofit > 0) {

                            self::db_query("INSERT INTO payout_log (quickpayid,officer,amount,ptype) VALUES (?,?,?,?)",
                                array(
                                $profile_creator,
                                $quickpayid,
                                $referearprofit,
                                "USER_REWARD"));

                            //self::update_reward($itd,$profile_creator,$referearprofit);
                            self::update_reward($itd, $quickpayid, $profile_creator, $referearprofit,
                                "REWARD");

                            $row = self::db_query("SELECT ac_ballance FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                                array($profile_creator));
                            $creator_ballance = $row[0]['ac_ballance'];

                            $creator_ballance = $creator_ballance + $referearprofit;
                            self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                                array($creator_ballance, $profile_creator));
                        }
                    }

                }


                ///////////////////////////
                if ($quickpay_cordinator > 0 || $quickpayrole == 1) {
                    //cordinator
                    if ($quickpayrole == 1) {
                        $quickpay_cordinator = $quickpayid;
                    }

                    $corprofit = $cordinator_percentage;
                    if ($bill_formular == 0) {
                        $corprofit = ($amount * $cordinator_percentage) / 100;
                    }

                    //UPDATE CORDINATOR + PAYOUT
                    self::db_query("INSERT INTO payout_log (quickpayid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $quickpay_cordinator,
                        $quickpayid,
                        $corprofit,
                        "COR_REWARD"));

                    self::update_reward($itd, $quickpayid, $quickpay_cordinator, $corprofit,
                        "REWARD");

                    $row = self::db_query("SELECT ac_ballance FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                        array($quickpay_cordinator));
                    $cordinator_ballance = $row[0]['ac_ballance'];

                    $cornewballance = $cordinator_ballance + $corprofit;
                    self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                        array($cornewballance, $quickpay_cordinator));
                }


                ///////////////////////////////////////////
                if ($quickpayrole < 4) {
                    //agent
                    $agentprofit = $percentage;
                    if ($bill_formular == 0) {
                        $agentprofit = ($amount * $percentage) / 100;
                    }

                    ///////////////////////////////////////////
                    //UPDATE AGENT + PAYOUT
                    self::db_query("INSERT INTO payout_log (quickpayid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $quickpayid,
                        $quickpayid,
                        $agentprofit,
                        "REWARD"));

                    // self::update_reward($itd,$quickpayid,$agentprofit);

                    $row = self::db_query("SELECT ac_ballance FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                        array($quickpayid));
                    $ownerballance = $row[0]['ac_ballance'];


                    ///set service charge
                    $newquickpay_service_charge = $quickpay_service_charge + $fixedfee;
                    self::db_query("UPDATE quickpay_transaction_log SET quickpay_service_charge =? WHERE transactionid = ? LIMIT 1",
                        array($newquickpay_service_charge, $itd));


                    $newballance = ($ownerballance - $fixedfee) + $agentprofit;
                    self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                        array($newballance, $quickpayid));

                    if (!in_array($services_category, array(
                        2,
                        3,
                        4))) {
                        $agentprofit = $agentprofit; // + 100;
                    }
                }


                ////////////////////////////////////////////
                //quickpaypercentage

                if ($quickpayrole < 4) {


                    if ($corprofit > 0) {

                        $quickpaypercentage = $bill_quickpayfull_percentage - $percentage - $cordinator_percentage;
                        if ($bill_formular == 0) {
                            $quickpaypercentage = ($amount * ($bill_quickpayfull_percentage - $percentage -
                                $cordinator_percentage)) / 100;
                        }

                    } else {


                        $quickpaypercentage = $bill_quickpayfull_percentage - $percentage;
                        if ($bill_formular == 0) {
                            $quickpaypercentage = ($amount * ($bill_quickpayfull_percentage - $percentage)) /
                                100;
                        }


                    }


                } else {


                    $percent = $bill_quickpayfull_percentage;
                    if ($bill_formular == 0) {
                        $percent = (($amount * $bill_quickpayfull_percentage) / 100);
                    }

                    $quickpaypercentage = $percent;
                    if (!in_array($services_category, array(
                        2,
                        3,
                        4))) {
                        $quickpaypercentage = $percent + 100;
                    }

                    if ($referearprofit > 0) {
                        $quickpaypercentage = $quickpaypercentage - $referearprofit;
                    }

                }


                // if role > 3 , //add user percentage and reduce quickpay
                //add user percentage and reduce quickpay
                $promopercet = 0;
                if (in_array($services_category, array(1, 2)) && $promo1 > 0 && $quickpayrole >
                    3) {


                    if ($quickpayid > 0) {
                        $rowquickpayrole = self::db_query("SELECT quickpayrole FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                            array($quickpayid));
                        $promo1role = $rowquickpayrole[0]['quickpayrole'];
                    } else {
                        $promo1role = 4;
                    }


                    if ($promo1role > 3) {

                        $promopercet = $promo1;
                        if ($bill_formular == 0) {
                            $promopercet = (($amount * $promo1) / 100);
                        }
                    }

                    $quickpaypercentage = $quickpaypercentage - $promopercet;

                }


                //GENERAL PERCENTAGE CALCULATOR
                self::db_query("UPDATE quickpay_transaction_log SET refererprofit =?,agentprofit =?,cordprofit =?,quickpayprofit = ?, promo1 = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $referearprofit,
                    $agentprofit,
                    $corprofit,
                    $quickpaypercentage,
                    $promopercet,
                    $transactionid));
            }

        }


    }


    public function update_reward($itd, $quickpayid, $quickpay_cordinator, $amount,
        $wht = "PROFIT")
    {

        if ($amount > 0) {
            self::db_query("INSERT INTO quickpay_transaction_log (quickpayid,account_meter,quickpay_service,quickpay_subservice,amount,transaction_reference,quickpay_status_code,quickpay_status,quickpay_print,ip) VALUES (?,?,?,?,?,?,?,?,?,?)",
                array(
                $quickpay_cordinator,
                $quickpayid,
                $wht,
                $wht,
                $amount,
                $wht,
                "1",
                "PAID",
                '{"details":{"' . $wht . '":"' . $amount . '","TRANSACTION STATUS","DONE"}}',
                $itd));
        }
    }


}
?>