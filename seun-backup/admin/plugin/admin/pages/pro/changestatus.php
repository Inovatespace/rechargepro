<?php
include_once "../../../../engine.autoloader.php";




$id = $_REQUEST['id'];
$value = $_REQUEST['value'];

$engine->db_query("UPDATE admin SET active = ? WHERE username = ? LIMIT 1",array($value,$id));      


        if (!$engine->config("local_authentication")) {
            $postData = array(
                "username" => $id,
                "value" => $value,
                "server_id" => $engine->config('server_id'));
            $return = $engine->file_get($postData, $engine->config('authentication_server') .
                'api/core/admin/change_status.json');
            $return = json_decode($return, true);
        }
?>