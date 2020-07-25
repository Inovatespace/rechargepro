<?php
include "../../engine.autoloader.php";

$email = $_REQUEST['email'];
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

  
}
?>


<div style="margin-top:-15px; height:285px; background: url(/theme/classic/images/letterbg.png) no-repeat; color:black;">
<div style="padding: 20px;">
<div style="margin-bottom: 10px; font-size:200%; color:#0F73C9;" >Thank you</div>


<div style="text-align: center;">
Our e-mail subscribers get inside scope on new products, promotions and lots more!
</div>
</div>

<div style="background-color: black; color:white; padding:20px; margin:5px;">
<div>I WANT TO RECEIVE UPDATE ON</div>
<div style="margin-top: 10px; overflow:hidden;">
<input type="checkbox" checked="checked" id="c1b" name="ccb"/>
<label for="c1b"><span></span>Promotions</label>  &nbsp; 

<input type="checkbox" checked="checked" id="c12b" name="cc2b"/>
<label for="c12b"><span></span>News</label></div>
</div>



</div>