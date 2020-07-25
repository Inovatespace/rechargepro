<?php
class NotificationResponse extends Api
{

  public function __construct($method)
    {
       $this->key = "FYNZDHVBKFUMCWRELJS8U2HHYBYB85385S527725";
    }



    public function complete_transaction($parameter)
    {

        $dataPOST = trim(file_get_contents('php://input'));

        if (!$dataPOST) {
            return "";
        }

        $xmlData = simplexml_load_string($dataPOST);


        if (!isset($xmlData->Param[4]->Value)) {
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
        $SplitType = $xmlData->SplitType;
        $DestinationBankCode = $xmlData->DestinationBankCode;
        $Narration = $xmlData->Narration;
        $PaymentReference = $xmlData->PaymentReference;
        $TransactionInitiatedDate = $xmlData->TransactionInitiatedDate;
        $TransactionApprovalDate = $xmlData->TransactionApprovalDate;
        $TransactionApprovalDate = $xmlData->TransactionApprovalDate;
        
        $tid = $xmlData->Param[4]->Value;
        $amount = $xmlData->Param[1]->Value;
        
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
                $response['ResponseMessage'] = "Successful";
                
                $response[] = array("Key"=>"mount","Value"=>$amount);
                $response[] = array("Key"=>"Service_Charge","Value"=>100);
                $response[] = array("Key"=>"Totalamount","Value"=>$amount + 100);
                
                
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
                $response['ResponseMessage'] = "Successful";
                
                $response[] = array("Key"=>"mount","Value"=>$amount);
                $response[] = array("Key"=>"Service_Charge","Value"=>100);
                $response[] = array("Key"=>"Totalamount","Value"=>$amount + 100);
                
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
                $response['ResponseMessage'] = "Successful";
                
                $response[] = array("Key"=>"mount","Value"=>$amount);
                $response[] = array("Key"=>"Service_Charge","Value"=>100);
                $response[] = array("Key"=>"Totalamount","Value"=>$amount + 100);
            }
            
            return $response;
        }
        
        
        
        



        return array("status" => "100", "message" => "Invalid Request5");

    }


}



?>