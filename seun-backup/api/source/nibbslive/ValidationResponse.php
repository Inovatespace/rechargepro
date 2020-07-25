<?php
class ValidationResponse extends Api
{

    public function __construct($method)
    {
        $this->key = "CK5YWVET0S0DPKVK3CKCHADHS1K6RQ2USG0LLWDT";
    }


    public function initiate_transaction($parameter)
    {

        $dataPOST = trim(file_get_contents('php://input'));

        if (!$dataPOST) {
            return "";
        }

        //file_put_contents("nibb.php", $dataPOST);

        $xmlData = simplexml_load_string($dataPOST);

        //return $xmlData->Param[1]->Value;

        if (!isset($xmlData->Param[2]->Value)) {
            return array(
                "BillerID" => "Unknowm",
                "NextStep" => 0,
                "ResponseCode" => "01",
                "ResponseMessage" => "Invalid Amount");
        }
        if (!isset($xmlData->Param[0]->Value)) {
            return array(
                "BillerID" => "Unknowm",
                "NextStep" => 0,
                "ResponseCode" => "01",
                "ResponseMessage" => "Invalid Mobile");
        }
        if (!isset($xmlData->Param[1]->Value)) {
            return array(
                "BillerID" => "Unknowm",
                "NextStep" => 0,
                "ResponseCode" => "01",
                "ResponseMessage" => "Invalid Productid");
        }

        if (!isset($xmlData->Param[2]->Value)) {
            return array(
                "BillerID" => "Unknowm",
                "NextStep" => 0,
                "ResponseCode" => "01",
                "ResponseMessage" => "Invalid accountnumber");
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
        //$ProductID = $xmlData->ProductID;
        $ProductID = $xmlData->Param[1]->Value;
        $ProductName = $xmlData->ProductName;
        $Amount = trim($xmlData->Param[2]->Value);
        $ClientAccount = $xmlData->Param[3]->Value;
        $ClientMobile = $xmlData->Param[0]->Value;

        $ep = explode(".", $Amount);
        $Amount = $ep[0];


        $parameter['private_key'] = $this->key;
        $parameter['service'] = trim($ProductID);
        $parameter['amount'] = trim($Amount);
        $parameter['mobile'] = trim($ClientMobile);
        $parameter['accountnumber'] = trim($ClientAccount);


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




            include "source/pro/electricity.php";
            $electricity = new electricity("POST");
            $return = $electricity->auth_transaction($parameter);


            $response = array();
            $response['BillerID'] = $BillerID;
            if ($return['status'] == "100") {
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = $return['message'];
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['ResponseMessage'] = "Successful";


                $response[] = array("Key" => "name", "Value" => $return['message']['name']);
                $response[] = array("Key" => "amount", "Value" => $return['message']['amount']);
                $response[] = array("Key" => "totalamount", "Value" => $return['message']['amount']);
                $response[] = array("Key" => "tid", "Value" => $return['message']['tid']);

            }

            return $response;

        }

        if (in_array($parameter['service'], array(
            "AQA",
            "AQC",
            "AWA"))) {
            //code from amount

            include "source/pro/tv.php";
            $tv = new tv("POST");

            $available_bounquet = $tv->available_bounquet(array("service" => $parameter['service']));
            if ($available_bounquet['status'] == 100) {

                return array(
                    "BillerID" => $BillerID,
                    "NextStep" => 0,
                    "ResponseCode" => "01",
                    "ResponseMessage" => "Invalid Response Contact Support");
            }

            $banquet = array();
            foreach ($available_bounquet['message']['items'] as $key => $value) {
                $banquet[$value['code']] = $value['price'];
            }

            $amount_array = array_values($banquet);
            if (!in_array($Amount, $amount_array)) {

                return array(
                    "BillerID" => $BillerID,
                    "NextStep" => 0,
                    "ResponseCode" => "01",
                    "ResponseMessage" => "Invalid Amount");
            }


            $parameter['code'] = array_search($Amount, $banquet);

            $return = $tv->auth_transaction($parameter);

            $response = array();
            $response['BillerID'] = $BillerID;
            if ($return['status'] == "100") {
                $response['NextStep'] = 0;
                $response['ResponseCode'] = "01";
                $response['ResponseMessage'] = $return['message'];
            } else {
                $response['NextStep'] = 1;
                $response['ResponseCode'] = "00";
                $response['ResponseMessage'] = "Successful";

                $response[] = array("Key" => "name", "Value" => $return['message']['name']);
                $response[] = array("Key" => "amount", "Value" => $return['message']['amount']);
                $response[] = array("Key" => "totalamount", "Value" => $return['message']['amount']);
                $response[] = array("Key" => "tid", "Value" => $return['message']['tid']);

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
                $response['ResponseMessage'] = "Successful";

                $response[] = array("Key" => "name", "Value" => $CustomerName);
                $response[] = array("Key" => "amount", "Value" => $parameter['amount']);
                $response[] = array("Key" => "totalamount", "Value" => $parameter['amount']);
                $response[] = array("Key" => "tid", "Value" => $parameter['accountnumber']);
            }

            return $response;
        }


        return array(
            "BillerID" => $BillerID,
            "NextStep" => 0,
            "ResponseCode" => "01",
            "ResponseMessage" => "Invalid Request");

    }


}



?>