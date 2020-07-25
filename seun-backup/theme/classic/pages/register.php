<?php 
$engine = new engine();
$pp = "index";
if(isset($_REQUEST['pp'])){$pp = $_REQUEST['pp'];}
$returnurl = str_replace('&', '%', $pp);
$returnurl = str_replace('#', '/', $pp);
if($engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/index'>"; exit;};
?>





<style type="text/css">
#content_body{background: url(theme/classic/images/innerbg.png) center repeat;}
</style>
<script type="text/javascript">
$(document).ready(function() {


    $('#mobile').keypress(function(e) {
        if($('#mobile').val().length > 10){return false;}
            var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9]/);
            if (verified) {e.preventDefault();}
    });
    
   
$(".registerbuttonb").click(function (){
//var title = $("#title").val();
var name = $("#name").val();
var sex = $("#sex").val();
var email = $("#email").val();
var mobile = $("#mobile").val();
var password = $("#password2").val();
var referer = $("#referer").val();
       
       $('#status').html("").hide(); 
       
 if(!$("input[id='iagree']").prop("checked")){
     $('#status').html('You must agree to terms and condition').show(); return false;
 }    
       
       
       if(empty(name) || empty(email) || empty(mobile) || empty(password)){
       $('#status').html('All fields are compulsory').show();
       $('#loading2').remove(); return false; }
       
       
       $("#loading2").html('<img src="/theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                         $(".registerbutton").prop('disabled', true);             
                        $.ajax({
                        type: "POST",
                        url: "/secure/register",
                        data: 'name='+name+'&sex='+sex+'&email='+email+'&mobile='+mobile+'&password='+password+'&referer='+referer,
                        cache: false,
                        success: function(html){
                            $(".registerbutton").prop('disabled', false);    
                            if(html == "good"){
                             window.location.href = "home";   
                                }else{
                        $('#loading2').remove();
                        $('#status').html(html).show(); 
                        }   
                       
                       return false; 
                        }
                        });
       
       
     return false;  

	}); 
    

    
   })
    
</script>




<div style="line-height: 25px;">

<div class="sitewidth"  style="overflow:hidden; margin-right: auto; margin-left: auto; margin-bottom:30px;">

<div class="nextcolor welcome" style="text-align: center; font-size:25px; margin-bottom: 10px; margin-top:10px; font-weight:bold;">Welcome!</div>
<div id="regf" style="text-align: center;  margin-bottom: 20px; padding-bottom:10px; border-bottom: solid 1px #CCCCCC;">User Registration Form</div>




<div id="status" class="nWarning" style="<?php if(!isset($_SESSION['error'])){echo "display: none;";}?>"><?php if(isset($_SESSION['error'])){echo $_SESSION['error']; unset($_SESSION['error']);}?></div>


<div class="registerholder" style="padding:0px 3%; overflow: hidden;"> 
<div style="border: solid 1px #EEEEEE; padding:3%; background-color: white;">
<div style="font-size: 20px; margin-bottom:5px;">Register For Great experience</div>
<div style=" margin-bottom:5px;">Please fill the space below to continue</div>

<script type="text/javascript" src="/theme/classic/js/jquery.form.js"></script>

<script type="text/javascript">
$(document).ready(function() { 
    var options = { 
        target:        '#status',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse  // post-submit callback 
    }; 
 
    // bind to the form's submit event 
    $('#myForm2').submit(function() { 
        $(this).ajaxSubmit(options); 
        return false; 
    }); 
}); 
 
// pre-submit callback 
function showRequest(formData, jqForm, options) { 
    // formData is an array; here we use $.param to convert it to a string to display it 
    // but the form plugin does this for you automatically when it submits the data 
    var queryString = $.param(formData); 
 $(".submit").prop('disabled', true);  
var title = "";//$("#title").val();
var name = $("#name").val();
var sex = $("#sex").val();
var email = $("#email").val();
var mobile = $("#mobile").val();
var password = $("#password2").val();
       
       $('#status').html("").hide(); 
       
 if(!$("input[id='iagree']").prop("checked")){
    $(".submit").prop('disabled', false);  
     $('#status').html('You must agree to terms and condition').show(); return false;
 }    
       

if(empty(name) || empty(email) || empty(mobile) || empty(password)){
    $(".submit").prop('disabled', false);  
$('#status').html('All fields are compulsory').show();
$('#loading2').remove(); return false; }
       
        $("#loading2").html('<img src="/theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
    return true; 
} 
 
// post-submit callback 
function showResponse(responseText, statusText, xhr, $form)  { 
    
    $(".submit").prop('disabled', false);    
            if(responseText == "good"){
             window.location.href = "home";   
                }else{
        $('#loading2').remove();
        $('#status').html(responseText).show(); 
        }   
                       
       
 
   // alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
       // '\n\nThe output div should have already been updated with the responseText.'); 
} 
</script>
<form id="myForm2" action="/secure/register" method="post" enctype="multipart/form-data">



<div>Full Name</div>
<div style="margin-bottom: 5px;"><input id="name" name="name" class="input" type="text" style="padding:5px; width: 98%;" /></div>
<div>Sex</div>
<div style="margin-bottom: 5px;"><select id="sex" name="sex" class="input" style="padding:5px; width: 98%;">
	<option>Male</option>
	<option>Female</option>
</select></div>
<div>Email Address</div>
<div style="margin-bottom: 5px;"><input id="email" name="email" class="input" type="text" style="padding:5px; width: 98%;" /></div>
<div>Mobile Number <span style="font-size: 90%;">{e.g 08022233456}</span></div>
<div style="margin-bottom: 5px;"><input id="mobile" name="mobile" class="input" type="text" style="padding:5px; width: 98%;" /></div>
<div>Create Password</div>
<div style="margin-bottom: 5px;"><input id="password2" name="password2" class="input" type="password" style="padding:5px; width: 98%;" /></div>
<div>Verify Password</div>
<div style="margin-bottom: 5px;"><input id="password2" name="password2" class="input" type="password" style="padding:5px; width: 98%;" /></div>
<style type="text/css">
.mbtn{margin-bottom: 10px; overflow: hidden;}
</style>

<div>Referrer Mobile (Not compulsory)</div>
<div style="margin-bottom: 5px;"><input id="referer" name="referer" class="input" type="text" style="padding:5px; width: 98%;" /></div>


<div id="submitholder1">
<div style="margin-bottom: 10px;"> <input name="iagree" value="1" type="checkbox" id="iagree" /><label for="iagree"><span></span>I agree to the Terms & Condition</label> </div><span id="loading2"></span>
</div>
<button  type="submit" class="submit mainbg" id="mysub" style="cursor:pointer; border:none; color:white; padding:10px 20px; margin-bottom: 10px; width:99%;">REGISTER</button>
</div>
</div>



















</div>
</div>


