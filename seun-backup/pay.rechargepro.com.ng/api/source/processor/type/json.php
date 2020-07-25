<?php
 header("Generator: QuickPay API");
 header('HTTP/1.0 200" " ok');
 header("Content-Type: application/json; charset=UTF-8");

 $result = '';

 echo encode($encode);


 function encode($encode)
 {
    if (isset($second_parameter['cb']) && !empty($second_parameter['cb']))
    {
       return $second_parameter['cb'] . '(' . json_encode($encode) . ')';
    }
    return json_encode($encode);


 }
