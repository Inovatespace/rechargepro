<?php
class notification extends Api
{

    //
    //orders
    //Thankyou
    //check out radio buttton address
    //acount auto add address

    public function __construct($method)
    {
        $this->webroot = "https://nextcashandcarry.com.ng";
        //$this->webroot = "http://192.168.1.4/next";
        //Api::Api_Method("sync", "GET", $method);
        //Api::Api_Method("login", "POST", $method);
        //$this->Logger = new Logger(array('path' => AUTH_DOCROOT . '/log'));
    }

    public function login($parameter)
    {
        if (!isset($parameter['username']) || !isset($parameter['password'])) {
            return "bad@Invalid Login Details";
        }
        
        $username = $parameter['username'];
        $password = sha1(md5($parameter['password']) . self::config("user_key"));

        $rowa = self::db_query("SELECT adminid, name,active,mobile,username FROM admin WHERE (mobile = ? OR username = ?)AND password = ? LIMIT 1",
            array($username, $username, $password));
        $row = $rowa[0];


        if (empty($row['adminid'])) {
            return "bad@Invalid login";
            exit;
        } else {

            if ($row['active'] == "0") {
                echo "bad@Inactive Account";
                exit;
            }
            // $useragent = "Android Tranzit";
            //self::dblog($row['adminid'], "login", $row['name'], self::getRealIpAddr(),
            // $useragent); 

            return "ok@" . $row['adminid']."@".$row['name'];

        }
    }


    public function alert($parameter) ////////////
    {
        if(empty($parameter['userid'])){
         return "ok@0@0@0@0@0@0";
        }

        $userid = $parameter['userid'];
        
        
        //check position
        $rowa = self::db_query("SELECT val5 FROM admin WHERE adminid = ? LIMIT 1", array($userid));
        $switch = $rowa[0]['val5'];
        switch ($switch){
        	case "Customer Care Officer":
            $pending = self::db_query2("SELECT id FROM item_orders_id WHERE status = ?", array(1), true);
            $review = self::db_query2("SELECT id FROM item_orders_id WHERE (status = ?)", array(6), true);
            return "ok@$pending@0@$review@0@0@0";
        	break;
        
        	case "Sales Officer":
            $approved = self::db_query2("SELECT id FROM item_orders_id WHERE status = ?", array(2), true);
            return "ok@0@$approved@0@0@0@0";
        	break;
        
        	case "Store Attendant":
            $warehouse = self::db_query2("SELECT id FROM item_orders_id WHERE status = ?", array(9), true);
            return "ok@0@0@0@$warehouse@0@0";
        	break;
                
        	case "Head, Sales Officer":
            $warehouse = self::db_query2("SELECT id FROM item_orders_id WHERE status = ?", array(9), true);
            $tranzit = self::db_query2("SELECT id FROM item_orders_id WHERE status = ? AND shippingstaff = '0'", array(3), true);
            return "ok@0@0@0@$warehouse@$tranzit@0";
        	break;
            
            case "Project Manager":
            case "Head, IT":
            case "Managing Director":
            $pending = self::db_query2("SELECT id FROM item_orders_id WHERE status = ?", array(1), true);
            $approved = self::db_query2("SELECT id FROM item_orders_id WHERE status = ?", array(2), true);
            $review = self::db_query2("SELECT id FROM item_orders_id WHERE (status = ?)", array(6), true);
            $warehouse = self::db_query2("SELECT id FROM item_orders_id WHERE status = ?", array(9), true);
            $tranzit = self::db_query2("SELECT id FROM item_orders_id WHERE status = ? AND shippingstaff = '0'", array(3), true);
            return "ok@$pending@$approved@$review@$warehouse@$tranzit@0";
        	break;  
            
        	default :
            return "ok@0@0@0@0@0@0";
        }
        
       // order/review- Customer Care Officer
       // approved - Sales Officer
       // pickup - Store Attendant
       // tranzit/pickup - Head, Sales Officer
      


//    order/approved/review/pickup/tranzit
        return "ok@0@0@0@0@0@0";
    }


}
?>