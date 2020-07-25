<?php
class verification extends Api
{

  public function __construct($method)
    {
       $this->key = "FYNZDHVBKFUMCWRELJS8U2HHYBYB85385S527725";
    }


    public function initiate_transaction($parameter)
    {

        $dataPOST = trim(file_get_contents('php://input'));

        if (!$dataPOST) {
            return "";
        }

        $xmlData = simplexml_load_string($dataPOST);


        if (!isset($xmlData->Amount)) {
            return array("status" => "100", "message" => "Invalid Service");
        }
        if (!isset($xmlData->Amount)) {
            return array("status" => "100", "message" => "Invalid mobile");
        }
        if (!isset($xmlData->ProductID)) {
            return array("status" => "100", "message" => "Invalid ProductID");
        }

        if (!isset($xmlData->ClientAccount)) {
            return array("status" => "100", "message" => "Invalid accountnumber");
        }


        $SourceBankCode = $xmlData->SourceBankCode;
        $SourceBankName = $xmlData->SourceBankName;
        $InstitutionCode = $xmlData->InstitutionCode;
        $ChannelCode = $xmlData->ChannelCode;
        $Step = $xmlData->Step;
        $StepCount = $xmlData->StepCount;
        $CustomerName = $xmlData->CustomerName;
        $CustomerAccountNumber = $xmlData->CustomerAccountNumber;
        $BillerID = $xmlData->BillerID;
        $BillerName = $xmlData->BillerName;
        $ProductID = $xmlData->ProductID;
        $ProductName = $xmlData->ProductName;
        $Amount = $xmlData->Amount;
        $ClientAccount = $xmlData->ClientAccount;
        $ClientMobile = $xmlData->ClientMobile;

        $ep = explode(".", $Amount);
        $Amount = $ep[0];


        $parameter['private_key'] = $this->key;
        $parameter['service'] = $ProductID;
        $parameter['amount'] = $Amount;
        $parameter['mobile'] = $ClientMobile;
        $parameter['accountnumber'] = $ClientAccount;


        if (in_array($parameter['service'], array(
            "AED",
            "AEP",
            "BIA",
            "BIB",
            "EPP",
            "EKP",
            "IKP",
            "IPP",
            "BOA",
            "BOB"))) {
            include "source/local/electricity.php";
            $electricity = new electricity("POST");
            $return = $electricity->auth_transaction($parameter);


            $response = array();
            $response['BillerID'] = $BillerID;
            if ($return['status'] == 100) {
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = $return['message'];
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['name'] = $return['message']['name'];
                $response['amount'] = $return['message']['amount'];
                $response['totalamount'] = $return['message']['amount'] + 100;
                $response['tid'] = $return['message']['tid'];
                $response['ResponseMessage'] = "Successful";
            }

            return $response;

        }

        if (in_array($parameter['service'], array(
            "AQA",
            "AQC",
            "AWA"))) {
            //code from amount

            include "source/local/tv.php";
            $tv = new tv("POST");

            $available_bounquet = $tv->available_bounquet(array("service" => $parameter['service']));
            if ($available_bounquet['status'] == 100) {
                return array("status" => "100", "message" => "Invalid Response Contact Support");
            }

            $banquet = array();
            foreach ($available_bounquet['message']['items'] as $key => $value) {
                $banquet[$value['code']] = $value['price'];
            }

            $amount_array = array_values($banquet);
            if (!in_array($Amount, $amount_array)) {
                return array("status" => "100", "message" => "Invalid Amount");
            }


            $parameter['code'] = array_search($Amount, $banquet);

            $return = $tv->auth_transaction($parameter);

            $response = array();
            $response['BillerID'] = $BillerID;
            if ($return['status'] == 100) {
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = $return['message'];
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['name'] = $return['message']['name'];
                $response['amount'] = $return['message']['amount'];
                $response['totalamount'] = $return['message']['amount'] + 100;
                $response['tid'] = $return['message']['tid'];
                $response['ResponseMessage'] = "Successful";
            }

            return $response;
        }


        if ($parameter['service'] == "TOP") {

            $statuscode = "00";
            $row = self::db_query("SELECT rechargeproid, email, name FROM rechargepro_account WHERE mobile = ? LIMIT 1",
                array($parameter['accountnumber']));
            $rechargeproid = $row[0]['rechargeproid'];
            $email = $row[0]['email'];
            $CustomerName = $row[0]['name'];


            $response = array();
            $response['BillerID'] = $BillerID;
            if (empty($rechargeproid)) {
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = "Invalid Phone Number";
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['name'] = $CustomerName;
                $response['amount'] = $parameter['amount'];
                $response['totalamount'] = $parameter['amount'] + 100;
                $response['tid'] = $parameter['accountnumber'];
                $response['ResponseMessage'] = "Successful";
            }

            return $response;
        }


        return array("status" => "100", "message" => "Invalid Request4");

    }


    public function complete_transaction($parameter)
    {

        $dataPOST = trim(file_get_contents('php://input'));

        if (!$dataPOST) {
            return "";
        }

        $xmlData = simplexml_load_string($dataPOST);


        if (!isset($xmlData->tid)) {
            return array("status" => "100", "message" => "Invalid Transaction ID");
        }

        $SourceBankCode = $xmlData->SourceBankCode;
        $SessionID = $xmlData->SessionID;
        $ChannelCode = $xmlData->ChannelCode;
        $TotalAmount = $xmlData->TotalAmount;
        $TransactionFeeBearer = $xmlData->TransactionFeeBearer;
        $CustomerName = $xmlData->CustomerName;
        $CustomerAccountNumber = $xmlData->CustomerAccountNumber;
        $BillerID = $xmlData->BillerID;
        $BillerName = $xmlData->BillerName;
        $ProductID = $xmlData->ProductID;
        $ProductName = $xmlData->ProductName;
        $amount = $xmlData->Amount;
        $SplitType = $xmlData->SplitType;
        $DestinationBankCode = $xmlData->DestinationBankCode;
        $Narration = $xmlData->Narration;
        $PaymentReference = $xmlData->PaymentReference;
        $TransactionInitiatedDate = $xmlData->TransactionInitiatedDate;
        $TransactionApprovalDate = $xmlData->TransactionApprovalDate;
        $TransactionApprovalDate = $xmlData->TransactionApprovalDate;
        $tid = $xmlData->tid;
        
        $ep = explode(".", $amount);
        $amount = $ep[0];


        $parameter['private_key'] = $this->key;
        $parameter['serial'] = "web";
        $parameter['tid'] = $tid;
        
        
        
        
        
        
            if (strlen($tid) == 11) {
            $row = self::db_query("SELECT rechargeproid, ac_ballance, profile_creator FROM rechargepro_account WHERE mobile = ? LIMIT 1",array($tid));
            $rechargeproid = $row[0]['rechargeproid'];
            $ac_ballance = $row[0]['ac_ballance'];
            $profile_creator = $row[0]['profile_creator'];
           
            
            $response = array();
            $response['BillerID'] = $BillerID;
            if(empty($rechargeproid)){
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = "A error has occured please call, 08183874966 for immediate solution";
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['Tmount'] = $amount;
                $response['Service_Charge'] = 100;
                $response['Totalamount'] = $amount + 100;
                $response['ResponseMessage'] = "Successful";
                
                
             $myip = self::getRealIpAddr();
                
             $rowcount = self::db_query("SELECT transactionid FROM rechargepro_transaction_log WHERE transaction_reference = ? AND rechargeproid = ? LIMIT 1",array($SessionID,$rechargeproid),true);
                          
             if($rowcount == 0){       
             $ac_ballance = $ac_ballance + $amount;
             self::db_query("UPDATE rechargepro_account SET ac_ballance = ? WHERE rechargeproid = ? LIMIT 1",array($ac_ballance,$rechargeproid)); 
             }

             
             self::db_query("INSERT INTO rechargepro_transaction_log (rechargepro_status,account_meter,agent_id,rechargeproid,transaction_reference,rechargepro_subservice,rechargepro_service,rechargepro_status_code,ip,amount,rechargepro_print) VALUES (?,?,?,?,?,?,?,?,?,?,?)",array("PAID","E-BILLS",$profile_creator,$rechargeproid,$SessionID,"Credit","E-BILLS","1",$myip,$amount,'{"details":'.json_encode($response).'}')); 
            }
            
            return $response;
        }
        
        


        $row = self::db_query("SELECT rechargepro_subservice FROM rechargepro_transaction_log WHERE transactionid = ? LIMIT 1",
            array($tid));
        $rechargeproservice = $row[0]['rechargepro_subservice'];

        if (empty($rechargeproservice)) {
            return array("status" => "100", "message" => "Invalid Transaction ID");
        }

        $row = self::db_query("SELECT services_category FROM rechargepro_services WHERE services_key = ? LIMIT 1",
            array($rechargeproservice));


        if ($row[0]['services_category'] == "1") {
           // include "source/local/electricity.php";
            //$electricity = new electricity("POST");
            //$return = $electricity->complete_transaction($parameter);
           $return["status"]=="200";
            $response = array();
            $response['BillerID'] = $BillerID;
            if ($return['status'] == 100) {
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = "A error has occured please call, 08183874966 for immidiate solution";
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['Amount'] = $amount;
                $response['Service_Charge'] = 100;
                $response['Totalamount'] = $amount + 100;
                $response['ResponseMessage'] = "Successful";
            }
            
            return $response;
        }


        if ($row[0]['services_category'] == "5") {
           // include "source/local/tv.php";
            //$tv = new tv("POST");
            //$return = $tv->complete_transaction($parameter);
             $return["status"]=="200";
            $response = array();
            $response['BillerID'] = $BillerID;
            if ($return['status'] == 100) {
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = "A error has occured please call, 08183874966 for immediate solution";
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['Tmount'] = $amount;
                $response['Service_Charge'] = 100;
                $response['Totalamount'] = $amount + 100;
                $response['ResponseMessage'] = "Successful";
            }
            
            return $response;
        }
        
        
        
        



        return array("status" => "100", "message" => "Invalid Request5");

    }


}



?>