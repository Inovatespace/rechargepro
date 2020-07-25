<link href="{SITE_LOCATION}/css/frontpage.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{SITE_LOCATION}/css/font-awesome/css/fontawesome-all.min.css"/>
<div>


	

<div style="margin-left: auto; margin-right: auto; width:330px; position:relative;">

<div id="status1" style="display:none; margin-left:-30px; width:400px; top:35px; position:absolute; z-index:3;">
<div id="status2" class="radious10" style="text-align:center; border: solid 1px #E07628;  background-color:#E9AF32; padding:10px; color:white;">{LOGIN_ERROR}</div>
<img style="margin-left:290px; margin-top:5px;" src="{SITE_LOCATION}/images/baloon.png" />
</div>

<div style="height: 150px;">&nbsp;</div>


<script type="text/javascript">
$(document).ready(function() {
	// validate login form on keyup and submit
	$(".loginbutton").click(function (){   

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




<style type="text/css">
:placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
  color: #87A4AF;
  opacity: 1; /* Firefox */
}

:-ms-input-placeholder { /* Internet Explorer 10-11 */
  color: #87A4AF;
}

::-ms-input-placeholder { /* Microsoft Edge */
  color: #87A4AF;
}
</style>

<div id="loading" style="font-size: 14px;"></div>

<div style="width:340px; overflow:hidden; margin-left: auto; margin-right: auto;">
<div style="text-align: center; margin-bottom:40px;"><img src="{SITE_LOCATION}/images/logo.png" width="330"  /></div>


<form method="post" action=""  autocomplete="off">

<div style="color:white; overflow:hidden; width:339px; border-bottom:solid 3px #87A4AF; background-color:white; -webkit-border-top-right-radius: 30px;
-moz-border-radius-topright: 30px;
border-top-right-radius: 30px;">

<div style="padding:20px 30px;">

<div style="margin-top:10px; margin-bottom:20px;"><input class="fas" placeholder="&#xf007; Username" id="username" autocomplete="off" name="username" type="text" style="font-size:130%; background-color: #EAEEF1; color:#87A4AF; border:none; border-bottom: solid 3px #87A4AF;; width:98%; padding:17px 3%; outline: 0;"/></div>


<div style="margin-top:5px; margin-bottom:10px;"><input class="fas" placeholder="&#xf023 Password" id="password" autocomplete="off" name="password" type="password"  style="font-size:130%; background-color: #EAEEF1; color:#87A4AF; border:none; border-bottom: solid 3px #87A4AF;; width:98%; padding:17px 3%; outline: 0;"/></div>

<div style="text-align: right; color:#87A4AF; margin-bottom:10px;">Forgot Password?</div>

{LOGIN_RETURNURL}
</div>



</div>
<input type="submit" value="Login" class="loginbutton" style="margin-top:15px; text-align:center; font-weight:bold; border: none; border-top:solid 3px white; padding:6px 0px; width:99.9%; cursor:pointer; background-color:#87A4AF; color:white; -webkit-border-bottom-left-radius: 10px;
-moz-border-radius-bottomleft: 10px;
border-bottom-left-radius: 10px;" />

</form>

</div>






</div>

<div style="overflow: hidden; position:absolute; bottom:0px; padding-bottom: 10px; width:100%; background-color:white;">
<div style="background: url('{SITE_LOCATION}/images/diamond.png') repeat-x; height:3px;"></div>
<img  style="margin-left:20px;  float:left; margin-top:10px;" src="{SITE_LOCATION}/images/setting.png" width="20"/>


<div  style="margin-right:20px; float:right; margin-top:10px;"><span style="font-weight: bold;">VERTIS TECHNOLOGY</span></div>	
</div>




</div>