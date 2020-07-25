<?php 
$engine = new engine();
$pp = "index";
if(isset($_REQUEST['pp'])){$pp = $_REQUEST['pp'];}
$returnurl = str_replace('&', '%', $pp);
$returnurl = str_replace('#', '/', $pp);
if($engine->get_session("recharge4id")){ echo "<meta http-equiv='refresh' content='0;url=/clientarea'>"; exit;};
?>






<script type="text/javascript">

var username = "";
$(document).ready(function() {

	// validate login form on keyup and submit
	$(".loginbutton").click(function (){   

       username = $("#username").val();
       var password = $("#password").val();
       var returnurl = $("#returnurl").val();
       
       $('#status').html("").hide(); 
       
       if(username == "" || password == ""){
       $('#status').html('{%INVALID_LOGIN_DETAILS%}').show();
       $('#loading').html(""); return false; }
       
       
       $("#loading").html('<img src="/theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "/secure/login",
                        data: 'username='+username+'&password='+password+"&returnurl="+encodeURIComponent(returnurl),
                        cache: false,
                        success: function(html){
$('#loading').html("");                 
var res = html.split("*");
                  
switch (res[0]){ 
	case "bad":
    $('#status').html("{%INVALID_LOGIN_DETAILS%}").show(); 
	break;

	case "ac":
    window.location.href = "activateaccount";
	break;

	case "block":
    $('#status').html("{%YOUR_ACCOUNT_BLOCKED%}").show(); 
	break;
    
    case "auth":
    $('#status').html("{%OPERATION_NOT_AUTH_DEVICE%}").show(); 
    $(".loginholder").hide();
    $(".authholder").show();
	break;

	case "block":
    $('#status').html("{%YOUR_ACCOUNT_BLOCKED%}").show(); 
	break;
    
    case "ok":
    window.location.href = "/clientarea";//res[1];
    //$('#status').html(html).show(); 
	break;
    
	default :     $('#status').html(html).show(); 
}  
                       
                       return false; 
                        }
                        });
       
       
     return false;  

	});    
    
    
    $(".authbutton").click(function (){   

       var auth = $("#auth").val();
       
       $('#status').html("").hide(); 
       
       if(auth == ""){
       $('#status').html('{%INVALID_CODE%}').show();
       $('#loading').html(""); return false; }
       
       
       $("#loadingb").html('<img src="/theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "/secure/auth.php",
                        data: 'auth='+auth+"&username="+username,
                        cache: false,
                        success: function(html){
$('#loadingb').html("");                 
var res = html.split("*");
                  
switch (res[0]){ 
	case "bad":
    $('#status').html("{%INVALID_CODE%}").show(); 
	break;
    
    case "goo":
    $('#status').html("Maximum device allowed").show(); 
	break;
    
    case "ok":
    $('#status').html("").hide(); 
    $('#statusb').html("Authorised, please login <span clas='fas fa-check-circle' style='color:#0BB20B;'></span>").show();
    $(".loginholder").show();
    $(".authholder").hide();
	break;
    
	default :     $('#status').html(html).show(); 
}  
                       
                       return false; 
                        }
                        });
       
       
     return false;  

	});  
    

    $('#mobile').keypress(function(e) {
        if($('#mobile').val().length > 10){return false;}
            var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9]/);
            if (verified) {e.preventDefault();}
    });
    
    

    
   })
    
</script>


<div style="line-height: 25px; margin: 150px 0px;">

<div class="sitewidth"  style="overflow:hidden; margin-right: auto; margin-left: auto; margin-bottom:30px;">

<style type="text/css">
body {background:url(/theme/classic/images/bg2.png) white  top no-repeat; background-size: cover; background-attachment:fixed;}
.leftmargin{margin-left: 50%;}
@media (max-width: 900px) {
    .leftmargin{margin-left: 40%;}
    body {background:url(/theme/classic/images/bg2.png) white -100px top no-repeat; background-size: cover; background-attachment:fixed;}
}
@media (max-width: 800px) {
    .leftmargin{margin-left: 40%;}
    body {background:url(/theme/classic/images/bg2.png) white -400px top no-repeat; background-size: cover; background-attachment:fixed;}
}
@media (max-width: 770px) {
    .leftmargin{margin-left: 30%;}
    body {background:url(/theme/classic/images/bg2.png) white -500px top no-repeat; background-size: cover; background-attachment:fixed;}
}
@media (max-width: 650px) {
    .leftmargin{margin-left: 0%;}
    body {background:none white -500px top no-repeat; background-size: cover; background-attachment:fixed;}
}

@media (max-width: 500px) {
    .leftmargin{margin-left: 0%;}
    body {background:none white -500px top no-repeat; background-size: cover; background-attachment:fixed;}
}
</style>
<div class="leftmargin">

<div class="" style="padding-left:4%; padding-bottom:10px; font-size:25px; margin-bottom: 10px; font-weight:bold; border-bottom: solid 1px #CCCCCC;">{%CLIENT_ACCESS%}</div>

<div id="statusb" class="nInformation" style="display: none;"></div>

<div id="status" class="nWarning" style="<?php if(!isset($_SESSION['error'])){echo "display: none;";}?>"><?php if(isset($_SESSION['error'])){echo $_SESSION['error']; unset($_SESSION['error']);}?></div>

<div class="loginholder" style="padding:0px 2.5%; overflow: hidden;">
<div style="padding:3%; background-color: white;">
<div>{%EMAIL_ADDRESS%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="username" class="input" type="text" style="padding:10px 1%; width: 100%;" /><span class="focus-border"><i></i></span></div>
<div>{%PASSWORD%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="password" class="input" type="password" style="padding:10px 1%; width: 98%;" /><span class="focus-border"><i></i></span></div>
<div style="margin-bottom: 10px;"><a href="forgetpassword">{%FORGOT_PASSWORD%}</a> </div>

<input type="hidden" value="<?php echo $returnurl;?>" id="returnurl" />

<span id="loading"></span>


<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<div class="container-contact100-form-btn">
<div class="wrap-contact100-form-btn">
<div class="contact100-form-bgbtn"></div>
<button class="contact100-form-btn  loginbutton" id="mysub">
<span> {%LOGIN_CONTINUE%} <i class="fa fa-long-arrow-right m-l-7" aria-hidden="true"></i>
</span>
</button>
</div>
</div>
</div>

</div>
</div>




<div class="authholder" style="padding:0px 2.5%; overflow: hidden; display:none;">
<div style="border: solid 1px #EEEEEE; padding:3%; background-color: white;">
<div style="font-size: 20px; margin-bottom:5px;">Authorisation</div>
<div style=" margin-bottom:5px;">Enter code below to continue</div>
<div>AUTHORISATION CODE</div>
<div style="margin-bottom: 5px;"><input id="auth" class="input" type="text" style="padding:10px 5px; width: 98%;" /></div>

<span id="loadingb"></span>

<button class="mainbg authbutton" style="border:none; font-size:110%; color:white; padding:10px 20px; margin-bottom: 10px; width:99%; cursor: pointer;" type="submit">GET PERMISSION</button>


</div>
</div>

</div>


</div>
</div>


