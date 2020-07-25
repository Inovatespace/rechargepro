<?php
class myrefund extends Api
{
    //KEDCO
    function fix_phone($mobile)
    {
        $mobile = "234" . substr($mobile, 1);
        return $mobile;
    }

    function refund_now($parameter)
    {
        $tid = htmlentities($parameter['tid']);

        $row = self::db_query("SELECT quickpay_print,transaction_date,account_meter,phone,quickpay_service,cordinator_id,agent_id,quickpayid,quickpay_subservice,amount,thirdPartycode,refererprofit,agentprofit,cordprofit,quickpayprofit,quickpay_service_charge FROM quickpay_transaction_log WHERE transactionid = ? AND refund = '0' AND bank_ref ='' LIMIT 1",
            array($tid));
        $amount_to_charge = $row[0]['amount'];
        $quickpay_subservice = $row[0]['quickpay_subservice'];
        $quickpay_service = $row[0]['quickpay_service'];
        $quickpay_cordinator = $row[0]['cordinator_id'];
        $agent_id = $row[0]['agent_id'];
        $quickpayid = $row[0]['quickpayid'];
        $account_meter = $row[0]['account_meter'];
        $phone = $row[0]['phone'];
        $thirdPartyCode = $row[0]['thirdPartycode'];
        $transaction_date = date('Ymd', strtotime('+0 days', strtotime($row[0]['transaction_date'])));
        $quickpay_print = $row[0]['quickpay_print'];

        $refererprofit = $row[0]['quickpay_print'];
        $agentprofit = $row[0]['agentprofit'];
        $cordprofit = $row[0]['cordprofit'];
        $quickpay_print = $row[0]['quickpay_print'];
        $quickpay_service_charge = $row[0]['quickpay_service_charge'];
        $quickpayprofit = $row[0]['quickpayprofit'];

        if (empty($quickpayid)) {
            exit;
        }


        //set refund = 1
        self::db_query("UPDATE quickpay_transaction_log SET refund=?  WHERE transactionid = ?",
            array("1", $tid));


        if (!empty($account_meter) && $quickpay_subservice != "BANK TRANSFER") {

            $row = self::db_query("SELECT profit_bal,quickpayrole, ac_ballance, profile_creator , name, email FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                array($quickpayid));
            $quickpayrole = $row[0]['quickpayrole'];
            $myballance = $row[0]['ac_ballance'];
            $myprofitbal = $row[0]['profit_bal'];
            $profile_creator = $row[0]['profile_creator'];
            $name = $row[0]['name'];
            $email = $row[0]['email'];

            $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $quickpayid .
                "_" . $tid;
            self::db_query("INSERT INTO log_log (quickpayid,what,details) VALUES (?,?,?)",
                array(
                "0",
                "REFUND",
                $what));

            if ($quickpayrole <= 3) {


                $newballance = ($myballance + $amount_to_charge);
                self::db_query("INSERT INTO payout_log (quickpayid,officer,amount,ptype) VALUES (?,?,?,?)",
                    array(
                    $quickpayid,
                    $quickpayid,
                    $agentprofit,
                    "-REWARD"));

                $newballance = ($myballance + $amount_to_charge) + $quickpay_service_charge;
                $newprofit = $myprofitbal - $agentprofit;
                self::db_query("UPDATE quickpay_account SET ac_ballance = ?, profit_bal=? WHERE quickpayid = ? LIMIT 1",
                    array(
                    $newballance,
                    $newprofit,
                    $quickpayid));


                if ($quickpay_cordinator > 0 || $quickpayrole == 1) {

                    if ($quickpayrole == 1) {
                        $quickpay_cordinator = $quickpayid;
                    }

                    self::db_query("INSERT INTO payout_log (quickpayid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $quickpay_cordinator,
                        $quickpayid,
                        $cordprofit,
                        "-COR_REWARD"));


                    $row = self::db_query("SELECT profit_bal,ac_ballance FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                        array($quickpay_cordinator));
                    $cordinator_ballance = $row[0]['profit_bal'];

                    $cornewballance = $cordinator_ballance - $cordprofit;
                    self::db_query("UPDATE quickpay_account SET profit_bal = ? WHERE quickpayid = ? LIMIT 1",
                        array($cornewballance, $quickpay_cordinator));
                }
                //  }

            }


            if ($quickpayrole > 3) {
                if ($profile_creator > 0 && $refererprofit > 0) {

                    $row = self::db_query("SELECT profit_bal FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                        array($profile_creator));
                    $creator_ballance = $row[0]['profit_bal'];

                    self::db_query("INSERT INTO payout_log (quickpayid,officer,amount,ptype) VALUES (?,?,?,?)",
                        array(
                        $profile_creator,
                        $quickpayid,
                        $refererprofit,
                        "-USER_REWARD"));


                    $creator_ballance = $creator_ballance - $refererprofit;
                    self::db_query("UPDATE quickpay_account SET profit_bal = ? WHERE quickpayid = ? LIMIT 1",
                        array($creator_ballance, $profile_creator));

                }

                //+ 100 naira
                $newballance = $myballance + $amount_to_charge + $quickpay_service_charge;
                self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                    array($newballance, $quickpayid));

            }


            //      if ($bill_formular == 1){
            //     $fullf = $cordinator_percentage+$percentage+$bill_quickpayfull_percentage+$service_provider_percentage;
            //      $row = self::db_query("SELECT ac_ballance FROM quickpay_account WHERE quickpayid = ? LIMIT 1",array($quickpayid));
            //      $myballance = $row[0]['ac_ballance'];
            //
            //       $myballance = $myballance + $fullf;
            //       self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",array($myballance, $quickpayid));
            //           }


        }


        self::db_query("INSERT INTO quickpay_refund (quickpayid,quickpay_service,quickpay_subservice,account_meter,amount,phone,transactionid,quickpay_status_code,quickpay_status) VALUES (?,?,?,?,?,?,?,?,?)",
            array(
            $quickpayid,
            $quickpay_service,
            $quickpay_subservice,
            $account_meter,
            $amount_to_charge,
            $phone,
            $tid,
            "1",
            "PAID"));


        if ($quickpay_subservice == "BANK TRANSFER") {

            $row = self::db_query("SELECT quickpayrole, ac_ballance, profile_creator , name FROM quickpay_account WHERE quickpayid = ? LIMIT 1",
                array($quickpayid));
            $quickpayrole = $row[0]['quickpayrole'];
            $myballance = $row[0]['ac_ballance'];
            $profile_creator = $row[0]['profile_creator'];
            $name = $row[0]['name'];


            $what = "Admin_refund_" . $myballance . "_" . $amount_to_charge . "_" . $quickpayid .
                "_" . $tid;
            self::db_query("INSERT INTO log_log (quickpayid,what,details) VALUES (?,?,?)",
                array(
                "0",
                "REFUND",
                $what));


            $tfee = 15+$quickpayprofit;
            $himballance = $myballance + $amount_to_charge + $tfee;
            self::db_query("UPDATE quickpay_account SET ac_ballance = ? WHERE quickpayid = ? LIMIT 1",
                array($himballance, $quickpayid));
        }


        if ($tid) {

            $newprint = '{"details":{"REFUND_DATE":"' . date("Y-m-d H:i:s") .
                '","TRANSACTION STATUS","DONE"}}';

            self::db_query("UPDATE quickpay_transaction_log SET quickpay_service = ?, quickpay_subservice =?, quickpay_status_code=?, quickpay_status=?, quickpay_print = ?  WHERE transactionid = ?",
                array(
                "REFUND($quickpay_service)",
                "REFUND",
                "1",
                "PAID",
                $newprint,
                $tid));


            //self::db_query("DELETE FROM quickpay_transaction_log  WHERE ip = ?", array($tid));

            //self::db_query("INSERT INTO quickpay_transaction_log (quickpayid,quickpay_service,quickpay_subservice,amount,transaction_reference,quickpay_status_code,quickpay_status,quickpay_print) VALUES (?,?,?,?,?,?,?,?)",array($quickpayid,"REFUND","REFUND",$amount_to_charge,"REFUND","1","PAID",'{"details":{"REFUND":"'.$amount_to_charge.'","TRANSACTION STATUS","DONE"}}'));
        }


        $message = "Hey $name,<br />
$amount_to_charge has been refunded to your wallet, for uncompleted $quickpay_subservice transaction!<br />
Thank you,<br />
QuickPay";
        self::notification($quickpayid, $message, 1);

        //self::send_mail('noreply@quickpay',$email,"QuickPay Refund",$message);

        return "100";
    }


}
?>