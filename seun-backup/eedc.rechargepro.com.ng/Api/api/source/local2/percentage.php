<?php
class percentage extends Api
{

    function calculate_per($parameter)
    {

        if (!isset($parameter['tid'])) {
            exit;
        }

        $itd = $parameter['tid'];
        $row = self::db_query("SELECT rechargepro_service_charge,rechargepro_status,rechargepro_subservice,account_meter,phone,email,amount,rechargeproid,transactionid,payment_method,rechargepro_status_code,rechargepro_print FROM rechargepro_transaction_log WHERE transactionid = ? AND rechargepro_status_code = '0' LIMIT 1",
            array($itd));
        $transactionid = $row[0]['transactionid'];
        $service = $row[0]['rechargepro_subservice'];
        $amount = $row[0]['amount'];
        $rechargeproid = $row[0]['rechargeproid'];
        $payment_method = $row[0]['payment_method'];
        $rechargepro_service_charge = $row[0]['rechargepro_service_charge'];


        $rowb = self::db_query("SELECT services_category,cordinator_percentage,percentage,bill_formular,bill_rechargeprofull_percentage,promo1 FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($service));
        $services_category = $rowb[0]['services_category'];
        $promo1 = $rowb[0]['promo1'];
        $cordinator_percentage = $rowb[0]['cordinator_percentage'];
        $percentage = $rowb[0]['percentage'];
        $bill_formular = $rowb[0]['bill_formular'];
        $bill_rechargeprofull_percentage = $rowb[0]['bill_rechargeprofull_percentage'];
        
        
        //**when  creating profile copy percentage
        //**when specifing percemtage affect all under
        // get your percentage
        
        
        //check for fee here
        
        
        $rowb = self::db_query("SELECT bill_rechargeprofull_percentage,rechargeproid,cordinator_percentage,percentage,bill_formular FROM rechargepro_services_agent WHERE services_key = ? AND rechargeproid = ? LIMIT 1",
            array($service,$rechargeproid));
        if(!empty($rowb[0]['rechargeproid'])){
        $cordinator_percentage = $rowb[0]['cordinator_percentage'];
        $percentage = $rowb[0]['percentage'];
        $bill_formular = $rowb[0]['bill_formular'];
      ///  $bill_rechargeprofull_percentage = $rowb[0]['bill_rechargeprofull_percentage'];
        }
        
        
        //check for fixedcharge
        $fixedfee = 0;
        $rowb = self::db_query("SELECT fixedfee FROM rechargepro_services_fixed WHERE services_key = ? AND rechargeproid = ? LIMIT 1",
            array($service,$rechargeproid));
        if(!empty($rowb[0]['fixedfee'])){
        $fixedfee = $rowb[0]['fixedfee'];
        }
        


        if ($rechargeproid == 0) {
            $percent = ($bill_rechargeprofull_percentage) - (($amount *
                1.5) / 100);
            if ($bill_formular == 0) {
                $percent = (($amount * ($bill_rechargeprofull_percentage)) /
                    100) - (($amount * 1.5) / 100);
            }

            $bp = $percent;
            if (!in_array($services_category, array(
                2,
                3,
                4))) {
                $bp = $percent + 100;
            }
            
            
            //add user percentage and reduce rechargepro
            $promopercet = 0;
                if (in_array($services_category, array(1, 2)) && $promo1 > 0) {
        
        
                if ($rechargeproid > 0) {
                    $rowrechargeprorole = self::db_query("SELECT rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($rechargeproid));
                    $promo1role = $rowrechargeprorole[0]['rechargeprorole'];
                } else {
                    $promo1role = 4;
                }
        
        
                if ($promo1role > 3) {
        
                    $promopercet = $promo1;
                    if ($bill_formular == 0) {
                        $promopercet = (($amount * $promo1) / 100);
                    }
                }
        
        $bp = $bp-$promopercet;
        
            }


            self::db_query("UPDATE rechargepro_transaction_log SET refererprofit =?,agentprofit =?,cordprofit =?,rechargeproprofit = ?, promo1 =? WHERE transactionid = ? LIMIT 1",
                array(
                0,
                0,
                0,
                $bp,$promopercet,
                $transactionid));

        } else {
            //////////////////////
            if ($payment_method == "1") {
                $percent = ($bill_rechargeprofull_percentage) - (($amount *
                    1.5) / 100);
                if ($bill_formular == 0) {
                    $percent = (($amount * ($bill_rechargeprofull_percentage)) /
                        100) - (($amount * 1.5) / 100);
                }

                $bp = $percent;
                if (!in_array($services_category, array(
                    2,
                    3,
                    4))) {
                    $bp = $percent + 100;
                }
                
                //add user percentage and reduce rechargepro
                 $promopercet = 0;
                if (in_array($services_category, array(1, 2)) && $promo1 > 0) {
        
        
                if ($rechargeproid > 0) {
                    $rowrechargeprorole = self::db_query("SELECT rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($rechargeproid));
                    $promo1role = $rowrechargeprorole[0]['rechargeprorole'];
                } else {
                    $promo1role = 4;
                }
        
        
                if ($promo1role > 3) {
        
                    $promopercet = $promo1;
                    if ($bill_formular == 0) {
                        $promopercet = (($amount * $promo1) / 100);
                    }
                }
        
        $bp = $bp-$promopercet;
        
            }
                self::db_query("UPDATE rechargepro_transaction_log SET refererprofit =?,agentprofit =?,cordprofit =?,rechargeproprofit = ?, promo1 = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    0,
                    0,
                    0,
                    $bp,$promopercet,
                    $transactionid));
            }
            /////////////////////////////


            //////////////////////////////
            if ($payment_method == "2") {
                $rowb = self::db_query("SELECT ac_ballance, rechargeproid, profile_creator, rechargepro_cordinator, rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
                    array($rechargeproid));
                $ac_ballance = $rowb[0]['ac_ballance'];
                $rechargeproid = $rowb[0]['rechargeproid'];
                $profile_creator = $rowb[0]['profile_creator'];
                $rechargeprorole = $rowb[0]['rechargeprorole'];
                $rechargepro_cordinator = $rowb[0]['rechargepro_cordinator'];

                $agentprofit = 0;
                $referearprofit = 0;
                $corprofit = 0;
                $rechargepropercentage = 0;


                if ($rechargeprorole > 3) {
                    //check for refere
                    if ($profile_creator > 0) {
                        //give 0.2%
                        $special_percent = ($amount * 0.5) / 100;
                        if ($bill_formular == 1) {
                            $special_percent = ($specialamount * 5) / 100;
                        }
                        $referearprofit = $special_percent;
                        //UPDATE REFEREE + PAYOUT
                        
                        if($referearprofit > 0){

                        self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                            array(
                            $profile_creator,
                            $rechargeproid,
                            $referearprofit,
                            "USER_REWARD"));
                            
                        //self::update_reward($itd,$profile_creator,$referearprofit);
                        self::update_reward($itd,$rechargeproid,$profile_creator,$referearprofit,"REWARD");

                        $row = self::db_query("SELECT profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                            array($profile_creator));
                        $creator_ballance = $row[0]['profit_bal'];

                        $creator_ballance = $creator_ballance + $referearprofit;
                        self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                            array($creator_ballance, $profile_creator));
                    }
                    }

                }


                ///////////////////////////
                if ($rechargepro_cordinator > 0 || $rechargeprorole == 1) {
                    //cordinator
                    if ($rechargeprorole == 1) {
                        $rechargepro_cordinator = $rechargeproid;
                    }

                    $corprofit = $cordinator_percentage;
                    if ($bill_formular == 0) {
                        $corprofit = ($amount * $cordinator_percentage) / 100;
                    }

                    //UPDATE CORDINATOR + PAYOUT
                    self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $rechargepro_cordinator,
                        $rechargeproid,
                        $corprofit,
                        "COR_REWARD"));

                        self::update_reward($itd,$rechargeproid,$rechargepro_cordinator,$corprofit,"REWARD");

                    $row = self::db_query("SELECT profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($rechargepro_cordinator));
                    $cordinator_ballance = $row[0]['profit_bal'];

                    $cornewballance = $cordinator_ballance + $corprofit;
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                        array($cornewballance, $rechargepro_cordinator));
                }


                ///////////////////////////////////////////
                if ($rechargeprorole < 4) {
                    //agent
                    $agentprofit = $percentage;
                    if ($bill_formular == 0) {
                       $agentprofit = ($amount * $percentage) / 100;
                    }

                    ///////////////////////////////////////////
                    //UPDATE AGENT + PAYOUT
                    self::db_query("INSERT INTO payout_log (rechargeproid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $rechargeproid,
                        $rechargeproid,
                        $agentprofit,
                        "REWARD"));
                        
                       // self::update_reward($itd,$rechargeproid,$agentprofit);

                    $row = self::db_query("SELECT profit_bal FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($rechargeproid));
                    $ownerballance = $row[0]['profit_bal'];
                    
                    
                    ///set service charge
                    $newrechargepro_service_charge = $rechargepro_service_charge + $fixedfee;
                    self::db_query("UPDATE rechargepro_transaction_log SET rechargepro_service_charge =? WHERE transactionid = ? LIMIT 1",
            array($newrechargepro_service_charge,$itd));
                    
                    
                    

                    $newballance = ($ownerballance-$fixedfee) + $agentprofit;
                    self::db_query("UPDATE rechargepro_account SET profit_bal = ? WHERE rechargeproid = ? LIMIT 1",
                        array($newballance, $rechargeproid));
                        
                   if (!in_array($services_category, array(
                        2,
                        3,
                        4))) {
                        $agentprofit = $agentprofit;// + 100;
                    }
                }
                
                
                
                ////////////////////////////////////////////
                //rechargepropercentage

                if ($rechargeprorole < 4) {


                    if ($corprofit > 0) {

                        $rechargepropercentage = $bill_rechargeprofull_percentage-$percentage - $cordinator_percentage;
                        if ($bill_formular == 0) {
                            $rechargepropercentage = ($amount * ($bill_rechargeprofull_percentage-$percentage - $cordinator_percentage)) / 100;
                        }

                    } else {


                        $rechargepropercentage = $bill_rechargeprofull_percentage - $percentage;
                        if ($bill_formular == 0) {
                            $rechargepropercentage = ($amount * ($bill_rechargeprofull_percentage - $percentage)) /
                                100;
                        }
                        

                    }


                } else {


                    $percent = $bill_rechargeprofull_percentage;
                    if ($bill_formular == 0) {
                        $percent = (($amount * $bill_rechargeprofull_percentage) /
                            100);
                    }

                    $rechargepropercentage = $percent;
                    if (!in_array($services_category, array(
                        2,
                        3,
                        4))) {
                        $rechargepropercentage = $percent + 100;
                    }

                    if ($referearprofit > 0) {
                        $rechargepropercentage = $rechargepropercentage - $referearprofit;
                    }

                }


// if role > 3 , //add user percentage and reduce rechargepro
                //add user percentage and reduce rechargepro
                 $promopercet = 0;
                if (in_array($services_category, array(1, 2)) && $promo1 > 0 && $rechargeprorole > 3) {
        
        
                if ($rechargeproid > 0) {
                    $rowrechargeprorole = self::db_query("SELECT rechargeprorole FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",
                        array($rechargeproid));
                    $promo1role = $rowrechargeprorole[0]['rechargeprorole'];
                } else {
                    $promo1role = 4;
                }
        
        
                if ($promo1role > 3) {
        
                    $promopercet = $promo1;
                    if ($bill_formular == 0) {
                        $promopercet = (($amount * $promo1) / 100);
                    }
                }
        
        $rechargepropercentage = $rechargepropercentage-$promopercet;
        
            }
            
            
                //GENERAL PERCENTAGE CALCULATOR
                self::db_query("UPDATE rechargepro_transaction_log SET refererprofit =?,agentprofit =?,cordprofit =?,rechargeproprofit = ?, promo1 = ? WHERE transactionid = ? LIMIT 1",
                    array(
                    $referearprofit,
                    $agentprofit,
                    $corprofit,
                    $rechargepropercentage,$promopercet,
                    $transactionid));
            }

        }


    }
    
    
    public function update_reward($itd,$rechargeproid,$rechargepro_cordinator,$amount,$wht="PROFIT"){
        
                if($amount > 0){
        self::db_query("INSERT INTO rechargepro_transaction_log (rechargeproid,account_meter,rechargepro_service,rechargepro_subservice,amount,transaction_reference,rechargepro_status_code,rechargepro_status,rechargepro_print,ip) VALUES (?,?,?,?,?,?,?,?,?,?)",
                        array(
                        $rechargepro_cordinator,
                        $rechargeproid,
                        $wht,
                        $wht,
                        $amount,$wht,"1","PAID",'{"details":{"'.$wht.'":"'.$amount.'","TRANSACTION STATUS","DONE"}}',$itd));
                        }
    }


}
?>