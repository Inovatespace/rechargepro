<?php
$engine = new engine();
if($engine->get_session("userid")){ echo "<meta http-equiv='refresh' content='0;url=index'>"; exit;};
?>
<script type="text/javascript">
function call_page(Id){
    window.location.href = "/index#"+Id;
}
</script>
<style type="text/css">
#content_body{background: url(theme/classic/images/innerbg.png) center repeat;}
</style>
<script type="text/javascript">
$(document).ready(function() {
	// validate login form on keyup and submit
	$(".loginbutton").click(function (){   

       var username = $("#username").val();
       
       $('#status').html("").hide(); 
       
       if(username == ""){
       $('#status').html('Email entered is not found on our record').show();
       $('#loading').html(""); return false; }
       
       
       $("#loading").html('<img src="theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "secure/forgetpassword.php",
                        data: 'username='+username,
                        cache: false,
                        success: function(html){
                        $("#loading").html("");
                        $('#status').html("").hide(); 
                        
                        
                         if(html == "ok"){
                           $('#status2').show();
                            }else{
                          $('#status').html(html).show();      
                            }
                       return false; 
                        }
                        });
       
       
     return false;  

	});    
    
    

    
	$(".cbutton").click(function (){   

       var password = $("#password1").val();
       var password2 = $("#password2").val();
       var id = $("#id").val();
       $('#status').html("").hide(); 
       
       if(password == "" || password2 == ""){
       $('#status').html('All fields are compulsory').show();
       $('#loading').hide(); return false; }
       
       if(password != password2){
       $('#status').html('Password field do not match').show();
       $('#loading').hide(); return false; }
       
       
       $("#loading").html('<img src="theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "secure/forgetpassword.php",
                        data: 'password='+password+'&password2='+password2+'&id='+id,
                        cache: false,
                        success: function(html){
                        $("#loading").html("");
                        $('#status').html("").hide(); 
                        
                        
                         if(html == "ok"){
                          window.location.href = "register";  
                            }else{
                          $('#status').html(html).show();      
                            }
                       return false; 
                        }
                        });
     return false;  

	}); 
    
   })
    
</script>


<div style="line-height: 25px; margin-top: 150px;">

<div class="sitewidth" style="overflow:hidden; margin-right: auto; margin-left: auto; margin-bottom:30px;">
<div class="nextcolor welcome" style="text-align: center; font-size:25px; margin-bottom: 10px; margin-top:10px; font-weight:bold; border-bottom: solid 1px #CCCCCC;; padding-bottom:10px;">Password Recovery</div>


<div id="status" class="nWarning" style="display: none;"></div>
<div id="status2" class="nInformation" style="display: none; text-align: center; margin-bottom: 20px; margin-top:20px;">Thank you, please check your email for further instruction on how to retrieve your password</div>
</div>





<?php
if(!isset($_REQUEST['code'])){
?>
<div class="sitewidth" style="overflow:hidden; margin-right: auto; margin-left: auto; margin-bottom:30px;">
<div style="padding:0px 2.5%;">
<div style="padding:3%; background-color: white;">
<div style="font-size: 20px; margin-bottom:5px;">Forgot Password</div>
<div style=" margin-bottom:15px;">Enter information below to continue</div>
<div>Enter Email</div>
<div style="margin-bottom: 10px;"><input id="username" class="input" type="text" style="padding:3%; width: 94%;" /></div>

<span id="loading"></span>
<button class="loginbutton mainbg" style="border:none; font-size:110%; color:white; padding:10px 20px; margin-bottom: 10px; width:100%; cursor: pointer;" type="submit"><i class="fa fa-lock fa-fw"></i> RECOVER PASSWORD</button>
</div>
</div>
</div>
<?php
	}else{
$date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));	   
$code = htmlentities($_REQUEST['code']);
$rowa = $engine->db_query("SELECT id FROM temp_code WHERE code = ? AND date >= ? LIMIT 1",array($code,$date));
$id = $rowa[0]['id']; 
if(empty($id)){
?>
<div style="overflow:hidden; margin-right: auto; margin-left: auto; width:800px; margin-bottom:30px;">
<div id="status" class="nWarning" style="">This link has either expired or does not exist</div>
</div>
<?php
	}else{
?>
<div style="overflow:hidden; margin-right: auto; margin-left: auto; width:500px; margin-bottom:30px;">
<div style="padding:0px 2.5%;">
<div style="border: solid 1px #EEEEEE; padding:3%; background-color: white;">
<div style="font-size: 20px; margin-bottom:5px;">Password Change</div>
<div style=" margin-bottom:15px;">Enter information below to continue</div>
<div>Enter New Password</div>
<div style="margin-bottom: 10px;"><input id="password1" class="password" type="password" style="padding:3%; width: 94%;" /></div>
<div>Verify Password</div>
<div style="margin-bottom: 10px;"><input id="password2" class="password" type="password" style="padding:3%; width: 94%;" /></div>
<input type="hidden" id="id" value="<?php echo $id;?>" />
<span id="loading"></span>
<button class="cbutton mainbg" style="border:none; font-size:110%; color:white; padding:10px 20px; margin-bottom: 10px; width:100%; cursor: pointer;" type="submit">SAVE PASSWORD</button>
</div>
</div>
</div>
<?php
}
	}
?>







</div>





