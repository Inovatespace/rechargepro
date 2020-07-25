<?php
class auth extends Api
{
    //KEDCO
    public function __construct($method)
    {

    }

    public function validation($parameter)
    {
        $rechargeproid = urldecode($parameter['rechargeproid']);
        $serial = urldecode($parameter['serial']);
        $tid = urldecode($parameter['tid']);


        if ($rechargeproid != 0) {
            $row = self::db_query("SELECT email FROM rechargepro_account WHERE rechargeproid = ? AND active = '1' LIMIT 1",
                array($rechargeproid));
            if (empty($row[0]['email'])) {
                return false;
            }

            $row = self::db_query("SELECT id,name,device_type FROM rechargepro_access WHERE mac = ? AND email=? LIMIT 1",
                array($serial, $row[0]['email']));
            $macname = $row[0]['name'];
            $device_type = $row[0]['device_type'];
            if (empty($macname)) {
                return false;
            }
            $ip = self::getRealIpAddr();
            self::db_query("UPDATE rechargepro_transaction_log SET device_type =?, device_mac =?, device_name =?, ip =? WHERE transactionid = ? LIMIT 1",
                array(
                $device_type,
                $serial,
                $macname,
                $ip,
                $tid));

        } else {
            if ($serial != "web") {
                return false;
            }

            if (!isset($parameter['private_key'])) {
                return false;
            }
        }

        return true;
    }


}
?>