<?php 
$engine = new engine();
$pp = "index";
if(isset($_REQUEST['pp'])){$pp = $_REQUEST['pp'];}
$returnurl = str_replace('&', '%', $pp);
$returnurl = str_replace('#', '/', $pp);
if($engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/index'>"; exit;};
?>



<div id="ac" style="display:none; text-align: center; vertical-align: middle;">
<div class="profilebg" style="padding: 10px; margin-top: -15px">
<div style="margin-bottom: 5px;">Hang tight. Your submission is being reviewed and will be approved in a jiffy!</div>
<div onclick="window.location.reload();" style="background-color:#F47E1F; padding:3px 0px; margin:3px; width:100%; cursor:pointer; text-align: center; color:white;" class="shadow">Add Download</div> 
<a href="downloads"><div style="background-color:#F47E1F; padding:3px 0px; margin:3px; width:100%; cursor:pointer; text-align: center; color:white;" class="shadow">Browse Downloads</div> </a>
</div>
</div>







<style type="text/css">
#content_body{background: url(theme/classic/images/innerbg.png) center repeat;}
</style>
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
       $('#status').html('Invalid login details').show();
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
    $('#status').html("Invalid Login Details").show(); 
	break;

	case "ac":
    window.location.href = "activateaccount";
	break;

	case "block":
    $('#status').html("Your account has been blocked, please contact the administrator").show(); 
	break;
    
    case "auth":
    $('#status').html("This operation is not authorised, Please enter authorisation code from an authorised device, to authrised web login").show(); 
    $(".loginholder").hide();
    $(".authholder").show();
	break;

	case "block":
    $('#status').html("Your account has been blocked, please contact the administrator").show(); 
	break;
    
    case "ok":
    window.location.href = res[1];
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
       $('#status').html('Invalid Code').show();
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
    $('#status').html("Invalid Code").show(); 
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



<div style="line-height: 25px;">

<div class="sitewidth"  style="overflow:hidden; margin-right: auto; margin-left: auto; margin-bottom:30px;">

<div class="nextcolor welcome" style="text-align: center; font-size:25px; margin-bottom: 10px; margin-top:10px; font-weight:bold;">Welcome!</div>
<div id="regf" style="text-align: center;  margin-bottom: 20px; padding-bottom:10px; border-bottom: solid 1px #CCCCCC;">User login Form</div>


<div id="statusb" class="nInformation" style="display: none;"></div>

<div id="status" class="nWarning" style="<?php if(!isset($_SESSION['error'])){echo "display: none;";}?>"><?php if(isset($_SESSION['error'])){echo $_SESSION['error']; unset($_SESSION['error']);}?></div>

<div class="loginholder" style="padding:0px 2.5%; overflow: hidden;">
<div style="border: solid 1px #EEEEEE; padding:3%; background-color: white;">
<div style="font-size: 20px; margin-bottom:5px;">Sign In</div>
<div style=" margin-bottom:5px;">Sign in below to continue</div>
<div>Email Address</div>
<div style="margin-bottom: 5px;"><input id="username" class="input" type="text" style="padding:5px; width: 98%;" /></div>
<div>Password</div>
<div style="margin-bottom: 5px;"><input id="password" class="input" type="password" style="padding:5px; width: 98%;" /></div>
<div style="margin-bottom: 10px;"><a href="forgetpassword">Forget your password?</a> </div>

<input type="hidden" value="<?php echo $returnurl;?>" id="returnurl" />

<span id="loading"></span>

<button class="mainbg loginbutton" style="border:none; font-size:110%; color:white; padding:10px 20px; margin-bottom: 10px; width:99%; cursor: pointer;" type="submit">LOGIN TO CONTINUE</button>


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


