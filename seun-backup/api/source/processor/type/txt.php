<?php

 header("Generator: RECHARGEPRO API");
 header('HTTP/1.0 200" " ok');
 header("Content-Type: text/plain; charset=UTF-8");

 $result = '';

 echo encode($encode, $result);


 function encode($value, &$result)
 {

    // Get variable type
    $type = gettype($value);

    switch ($type)
    {
       case "boolean";
          $result .= $value ? "true":
          "false \n";
          break;
       case "integer":
          $result .= $value . "\n";
          break;
       case "double":
          $result .= $value . "\n";
          break;
       case "string":
          $result .= $value . "\n";
          break;
       case "array":
          if (count($value) == 0)
             return;
          foreach ($value as $arr_key => $arr_val)
          {
             if (is_array($arr_val))
             {
                foreach ($arr_val as $n)
                {
                   if (is_array($n))
                   {
                      foreach ($n as $s => $t)
                      {
                         encode($arr_key . '::' . $s.'::'.$t, $result);
                      }

                   } else
                   {
                      encode($arr_key . '::' . $n, $result);
                   }

                }

             } else
             {
                encode($arr_key . '::' . $arr_val, $result);
             }

          }
          break;
       case "object":
          $properties = get_object_vars($value);
          foreach ($properties as $obj_key => $obj_val)
          {
             encode($obj_key . '::' . $obj_val . "\n", $result);
          }
          break;
       default:

          break;
    }

    return $result;

 }

?>
