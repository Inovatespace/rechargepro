<link href="{SITE_LOCATION}/css/frontpage.css" rel="stylesheet" type="text/css" />
<div>
<div style="margin-left: auto; margin-right: auto; width:330px; position:relative;">

<div id="status1" style="display:none; margin-left:-30px; width:400px; top:35px; position:absolute; z-index:3;">
<div id="status2" class="radious10" style="text-align:center; border: solid 1px #E07628;  background-color:#E9AF32; padding:10px; color:white;">{LOGIN_ERROR}</div>
<img style="margin-left:290px; margin-top: 5px;" src="{SITE_LOCATION}/images/baloon.png" />
</div>

<div style="height: 150px;">&nbsp;</div>


<script type="text/javascript">
$(document).ready(function() {
	// validate login form on keyup and submit
	$(".indexloginbutton").click(function (){   

       var username = $("#username").val();
       var password = $("#password").val();
       var returnurl = $("#returnurl").val();
       
       $('#status1').hide(); 
       
       if(username == "" || password == ""){
       $('#status2').html('Invalid login details');
       $('#status1').show(); return false; }
       
       
       $("#loading").html('<img src="{SITE_LOCATION}/images/smallloading.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "{LOGIN_FORM_LOCATION}",
                        data: 'username='+username+'&password='+password+"&returnurl="+returnurl,
                        cache: false,
                        success: function(html){
                            if(html != "bad"){
                             window.location.href = html;   
                                }else{
                        $("#loading").html('');
                        $('#status1').show()
                        $('#status2').html("Invalid login details"); 
                        }   
                      // if (html == 1) {window.parent.location.href = "webtop"; $('#status2').html('Sucessfull. Redirecting'); return false;} 
                      // if (html == 2) {window.parent.location.href = "index?p=4"; $('#status2').html('Sucessfull. Redirecting'); return false;}  
                     //window.location.href = '';
                            
                       //$('#status2').html(html);
                       
                       return false; 
                        }
                        });
       
       
     return false;  

	});    return false; })
</script>

<div style="overflow:hidden;">
<div id="loading" style="font-size: 14px;"></div>
<div style="float: left; width:160px; overflow:hidden;">
<div class="barmenu" style="padding:20px 30px;">
<img src="{SITE_LOGO}" width="100" />
</div>
<div class="activemenu" style="text-align:center; font-size: 10px; font-weight:bold; padding:7px 5px; font-family:Trebuchet MS; border-bottom-right-radius:5px; 	-moz-border-radius-bottomright:5px; 	-webkit-border-bottom-right-radius:5px; border-bottom-left-radius:5px; 	-moz-border-radius-bottomleft:5px; 	-webkit-border-bottom-left-radius:5px;">{MOTTO}</div>
</div>



<div style="margin-left:10px; float: right; width:160px; overflow:hidden;">
<form method="post" action="">
<div style="margin-top: 10px; font-size:11px; font-style: italic; color:#444444;">Email address or Username</div>
<div style="margin-top:5px;">{LOGIN_USERNAME}</div>

<div style="margin-top: 20px; font-size:11px; font-style: italic; color:#444444;">Password</div>
<div style="margin-top:5px; margin-bottom: 9px;">{LOGIN_PASSWORD}</div>
<div class="orangelink" style="text-align:right; font-weight:bold; margin-bottom: 4px; font-size:10px;"><a href="index?p=5">Forgotten your password?</a></div>

{LOGIN_RETURNURL}

</div>
</div>

<input type="submit" value="ENTER" class="shadow radious10 indexloginbutton loginbutton" style="color:black; margin-top:20px; margin-bottom:10px; text-align:center; font-weight:bold; border: solid 1px white; padding:6px 0px; width:330px; cursor:pointer;" />
</form>


</div>







</div>