<?php
class plentyauto extends Api
{



    public function r_n($tmparraygent)
    {
        $int = rand(1, 40);
        if(!in_array($int,$tmparraygent)){
           return $int; 
        }
        
        return self::r_n($tmparraygent);
        
    }

    public function cleanData($c)
    {
        return preg_replace("/[^0-9]/", "", $c);
    }
    
public function check_game($parameter){
    
                    $primary = $parameter['primary'];
                    $secondary = $parameter['secondary'];
                    $tertiary = $parameter['tertiary'];
                    $amount = $parameter['amount'];
                    
                    
                      if (strlen($primary) < 11 || strlen($primary) > 11) {
            return array("status" => "100", "message" => "Invalid Mobile Number");
        }
        
        $amount = $secondary*50;
        
                    
            return array("status"=>200,"message"=>array("name"=>$primary,"amount"=>$amount));
   
}
    
    
    public function play_game($parameter){
        
        $amount = $parameter['amount'];
        $primary = $parameter['primary'];
        $ref = $parameter['ref'];
        
        $noticket = floor($amount/50);
   
        $imei = "VERTIS_API_NEW";
        $sqlbet = "";
        $betarray = array();
        $bet_type = 0;
        $game_id = 1;
        $ip = self::getRealIpAddr();
   
            if (empty($noticket)) {
                return array("status" => "100", "message" => "Invalid number of ticket");
            }

            if ($noticket < 1) {
                return array("status" => "100", "message" => "Invalid number of ticket");
            }

            if ($noticket > 10) {
                return array("status" => "100", "message" => "Maximum of ten tickets allowed");
            }


            for ($i = 1; $i < ($noticket + 1); $i++) {

                $tmparraygent = array();
                for ($ii = 0; $ii < 6; $ii++) {
                    $tmparraygent[] = self::r_n($tmparraygent);
                }

                $sqlbet .= '"bet' . $i . '": "' . implode(",", $tmparraygent) . "\",";
                $betarray["bet$i"] = implode(",", $tmparraygent);
            }
            
            
        //"2063TQ07","2017T100",
        $dataarray = array(
            "retailer_id" => "201W100", //terminal ID MAX 7
            "super_agent_id" => "2017T100",
            "sub_agent_id" => "",
            "imei" => $imei,
            "timestamp" => date("Y-m-d H:i:s"),
            "game_id" => "1",
            "player_phone" => $primary,
            "bet_type" => "0",
            "number_of_draws" => "1",
            "payment_type" => "2",
            "payment_reference" => $ref,
            "number_of_bets" => $noticket,
            "amount" => $amount,
            "partner" => "SPL",
            "bet_list" => $betarray);


        $payload = array("buy_ticket_request" => $dataarray);
        
        
        $return = array();
        $mob1 = substr($primary, 0, 4);
        $mob2 = substr($primary, -3, 11);
        $return['game'] = "PLENTYMILLIONS";
        $return['partner'] = "SPL";
        $return['date'] = date("Y-m-d");
        $return['mobile'] = $mob1 . "####" . $mob2;
        $return['amount'] = $amount;
        $return['bet_list'] = $betarray;
        $return['license'] = "Licensed by the National Lottery Regulatory Commission";


        //return array("status" => "100", "message" =>implode("@",$betarray));

        $responseData = "";
        $url = "http://67.215.10.77/spl/starturl.php";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        //return array("status" => "100", "message" => $response);
        
        $responseData = json_decode($response,true);
        
        
   

        if (isset($responseData['buy_ticket_response'])) {
            $responseData = $responseData['buy_ticket_response'];

            if ($responseData["response_code"] == 00) {
              
 return array("status" => "200", "message" => $responseData);

            } else
                if ($responseData["response_code"] == 55) {

                 
 return array("status" => "200", "message" => $responseData);
                

                } else {

                    return array("status" => "100", "message" => $responseData["response_desc"]);
                }

        } else {
            return array("status" => "100", "message" => "An Error Occured");
        }
 

    }



}
?>