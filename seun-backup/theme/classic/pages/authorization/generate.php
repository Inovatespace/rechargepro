<?php 
include "../../../../engine.autoloader.php";

$rechargeproemail = $engine->get_session("rechargeproemail"); 
$code = $engine->RandomString(4,5);

$engine->db_query("DELETE FROM temp_code WHERE email = ? AND status = '0'",array($rechargeproemail)); 


$engine->db_query("INSERT INTO temp_code (email,code) VALUES (?,?)",array($rechargeproemail,$code));





?>


<div class="whitemenu" style="padding: 10px; margin-top:-15px;">
<div style="margin-bottom: 5px;">AUTH CODE</div>
<div class="nInformation" style="font-size: 150%;"><?php echo $code;?></div>
</div>


