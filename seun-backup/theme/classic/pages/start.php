<?php 
$engine = new engine();
$pp = "index";
if(isset($_REQUEST['pp'])){$pp = $_REQUEST['pp'];}
$returnurl = str_replace('&', '%', $pp);
$returnurl = str_replace('#', '/', $pp);
if($engine->get_session("recharge4id")){ echo "<meta http-equiv='refresh' content='0;url=/index'>"; exit;};
?>





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
var country = $("#country").val();
       
       $('#status').html("").hide(); 
       
 if(!$("input[id='iagree']").prop("checked")){
     $('#status').html('{%YOU_MUST_AGREE%}').show(); return false;
 }    
       
       
       if(empty(name) || empty(email) || empty(mobile) || empty(password)){
       $('#status').html('{%ALL_FAAC%}').show();
       $('#loading2').remove(); return false; }
       
       
       $("#loading2").html('<img src="/theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                         $(".registerbutton").prop('disabled', true);             
                        $.ajax({
                        type: "POST",
                        url: "/secure/register",
                        data: 'name='+name+'&sex='+sex+'&email='+email+'&mobile='+mobile+'&password='+password+'&referer='+referer+'&country='+country,
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

<div class="nextcolor welcome" style="padding-left:4%; padding-bottom:10px; font-size:25px; margin-bottom: 10px; margin-top:10px; font-weight:bold; border-bottom: solid 1px #CCCCCC;">{%REG_INFO%}</div>


<div id="status" class="nWarning" style="<?php if(!isset($_SESSION['error'])){echo "display: none;";}?>"><?php if(isset($_SESSION['error'])){echo $_SESSION['error']; unset($_SESSION['error']);}?></div>


<div class="registerholder" style="padding:0px 3%; overflow: hidden;"> 
<div style="padding:3%; background-color: white;">


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
     $('#status').html('{%YOU_MUST_AGREE%}').show(); return false;
 }    
       

if(empty(name) || empty(email) || empty(mobile) || empty(password)){
    $(".submit").prop('disabled', false);  
$('#status').html('{%ALL_FAAC%}').show();
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



<div>{%FULL_NAME%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="name" name="name" class="input" type="text" style="padding:10px 1%; width: 100%;" /><span class="focus-border"><i></i></span></div>

<script type="text/javascript">
function set_flag(){
    
    var country = $("#country").val();
    $("#imc").attr("src","/theme/classic/flag/"+country+".png");
    
}
</script>
<script type="text/javascript">
jQuery(document).ready(function($){
    <?php
	if(isset($_COOKIE['country'])){
	    $exp = explode("@",$_COOKIE['country']); 

?>
  $('#country').val("<?php echo $exp[1];?>");
  set_flag();
  <?php
	}
?>  
    })
</script>

<div>{%COUNTRY%}</div>
<div style="margin-bottom: 5px; position: relative; overflow: hidden; height:40px;">
<img class="imgc" id="imc" src="/theme/classic/flag/Nigeria.png" style="float: left; width:10%; height:57px; margin-top: -9px;" />
<select id="country" name="country" class="input" onchange="set_flag()" style="padding:10px 1%; width: 90%; float:left; height:40px;">
<?php
$row = $engine->db_query("SELECT country FROM recharge4_country WHERE status = '1'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
?>
<option><?php echo $row[$dbc]['country'];?></option>
<?php
}
?>
</select><span class="focus-border"><i></i></span></div>





<div>{%EMAIL_ADDRESS%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="email" name="email" class="input" type="text" style="padding:10px 1%; width: 100%;" /><span class="focus-border"><i></i></span></div>
<div>{%MOBILE_NUMBER%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="mobile" name="mobile" class="input" type="text" style="padding:10px 1%; width: 100%;" /><span class="focus-border"><i></i></span></div>
<div>{%CREAT_PASSWORD%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="password2" name="password2" class="input" type="password" style="padding:10px 1%; width: 98%;" /><span class="focus-border"><i></i></span></div>
<div>{%VERIFY_PASSWORD%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="password2" name="password2" class="input" type="password" style="padding:10px 1%; width: 98%;" /><span class="focus-border"><i></i></span></div>
<style type="text/css">
.mbtn{margin-bottom: 10px; overflow: hidden;}
</style>

<div>{%REFER_MOBILE%}</div>
<div style="margin-bottom: 5px; position: relative;"><input id="referer" name="referer" class="input" type="text" style="padding:10px 1%; width: 100%;" /><span class="focus-border"><i></i></span></div>


<div id="submitholder1">
<div style="margin-bottom: 10px; position: relative;"> <input name="iagree" value="1" type="checkbox" id="iagree" /><label for="iagree"><span></span>{%I_AGREE_TO_TERMS%}</label> <span class="focus-border"><i></i></span></div><span id="loading2"></span>
</div>

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<div class="container-contact100-form-btn">
<div class="wrap-contact100-form-btn">
<div class="contact100-form-bgbtn"></div>
<button class="contact100-form-btn  submit " id="mysub">
<span> {%REGISTER%} <i class="fa fa-long-arrow-right m-l-7" aria-hidden="true"></i>
</span>
</button>
</div>
</div>
</div>


</div>
</div>


</div>
















</div>
</div>


