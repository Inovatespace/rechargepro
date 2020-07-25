<?php
include "../../../../engine.autoloader.php";
?>
<style type="text/css">
.important{
	color: Red;
}
</style>
<script type="text/javascript">
jQuery(function() {
jQuery('#widgetuploadform input[type="submit"]').click( function(e) {
    e.preventDefault();

        			
	if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
		jQuery.alert("Your browser does not support new File API! Please upgrade."); return false;//push error text
	}else{
	   
      $("#loading").show(); 
       
 var name = $("[name='name']").val();
var email = $("[name='email']").val();
var category = $("[name='category']").val();
var priority = $("[name='priority']").val();
var subject = $("[name='subject']").val();
var message = $("[name='message']").val();



if (empty(name) || empty(email) || empty(category) || empty(priority) || empty(subject) || empty(message)) {
$.alert("All field  asteric(*) are compulsory","Alert"); $("#loading").hide();return false;	
}

		


var form = document.getElementById('widgetuploadform');
var form_data = new FormData(form);

jQuery.ajax({
	url : "/theme/classic/pages/help/newticketpro.php",
	type: "POST",
	data : form_data,
	contentType: false,
	cache: false,
	processData:false,
	xhr: function(){
		//upload Progress
		var xhr = jQuery.ajaxSettings.xhr();
		if (xhr.upload) {
			xhr.upload.addEventListener('progress', function(event) {
				var percent = 0;
				var position = event.loaded || event.position;
				var total = event.total;
				if (event.lengthComputable) {
					percent = Math.ceil(position / total * 100);
				}
                
			}, true);
		}
		return xhr;
	},
	mimeType:"multipart/form-data"
}).done(function(res){ //
$.alert(res,"Alert");
gohome("newticket");

});
	
    }
    
   // console.log(datatosend);
    return false;
});
});
</script>
<script type="text/javascript">
function sendmessage(){
$("#loading").show(); 

var name = $("#name").val();
var email = $("#email").val();
var category = $("#category").val();
var priority = $("#priority").val();
var subject = $("#subject").val();
var message = $("#message").val();

if (empty(name) || empty(email) || empty(category) || empty(priority) || empty(subject) || empty(message)) {
$.alert("All field  asteric(*) are compulsory","Alert"); $("#loading").hide();return false;	
}
 
var dataString = "name="+encodeURIComponent(name)+"&email="+encodeURIComponent(email)+"&category="+encodeURIComponent(category)+"&priority="+priority+"&subject="+encodeURIComponent(subject)+"&message="+encodeURIComponent(message); 
$.ajax({
type: "POST",
url: "",
data: dataString,
cache: false,
success: function(html){
$.alert(html,"Alert");
gohome("newticket");
}
});  
}
</script>

<a style="cursor:pointer; color: black; font-size:120%; font-weight:bold;" href="/support">Home < BACK</a>
<div style="padding: 20px;">
<div id="status" style="color: red; display:none;" class="nWarning"><?php	if (isset($_SESSION['HESK_MESSAGE'])) { echo $_SESSION['HESK_MESSAGE'];}?></div>
<div style="margin-bottom: 20px;">Please use the form below to submit a ticket. Required fields are marked with<span class="important"> *</span></div>
<form action="" id="widgetuploadform" method="post" enctype="multipart/form-data">
  <?php
$t = "";
if(!$engine->get_session("rechargeproid")){	
?>
<div style="float: left; width:25%;">Name<span class="important">*</span></div>
<div style="float: left; width:69%;"><input class="input" type="text" name="name" style="width:99%; padding:5px 0px;"   /></div>
<div style="clear: both; height:8px;">&nbsp;</div>

<div style="float: left; width:25%;">Email<span class="important">*</span></div>
<div style="float: left; width:69%;"><input class="input" type="text" name="email" style="width:99%; padding:5px 0px;"/></div>
<div style="clear: both; border-bottom:dotted 1px #969292; height:8px; margin-bottom:5px;">&nbsp;</div>
<?php
	}else{
?>
<div style="float: left; width:25%;">Name<span class="important">*</span></div>
<div style="float: left; width:69%;"><input value="<?php echo $engine->get_session("name");?>" class="input" readonly="readonly" type="text" name="name" style="width:99%; padding:5px 0px;"   /></div>
<div style="clear: both; height:8px;">&nbsp;</div>

<div style="float: left; width:25%;">Email<span class="important">*</span></div>
<div style="float: left; width:69%;"><input value="<?php echo $engine->get_session("rechargeproemail");?>" class="input" readonly="readonly" type="text" name="email" style="width:99%; padding:5px 0px;"/></div>
<div style="clear: both; border-bottom:dotted 1px #969292; height:8px; margin-bottom:5px;">&nbsp;</div>
<?php
	}
?>

<div style="float: left; width:25%;">Catigory:<span class="important">*</span></div>
<div style="float: left; width:69%;">
<select class="input" name="category" style="width:99%; padding:5px 0px;">
	<option value="1">Technical Support</option>
    <option value="2">Account Crediting</option>
    <option value="3">General Enquiry</option>
	</select></div>
<div style="clear: both; height:8px;">&nbsp;</div>


<input name="priority" type="hidden" value="Medium" />
<div style="clear: both; border-bottom:dotted 1px #969292; height:8px; margin-bottom:5px;">&nbsp;</div>



<div style="float: left; width:25%;">Subject:<span class="important">*</span></div>
<div style="float: left; width:69%;"><input class="input" type="text" name="subject" style="width:99%; padding:5px 0px;"  value="<?php if (isset($_SESSION['c_subject'])) {echo stripslashes($_SESSION['c_subject']);} ?>"/></div>
<div style="clear: both; height:8px;">&nbsp;</div>
	
    
<div style="float: left; width:25%;">Message:<span class="important">*</span></div>
<div style="float: left; width:69%;"><textarea class="input" name="message" style="width:99%; padding:5px 0px; height:100px;"><?php if (isset($_SESSION['c_message'])) {echo stripslashes($_SESSION['c_message']);} ?></textarea></div>
<div style="clear: both; border-bottom:dotted 1px #969292; height:8px; margin-bottom:5px;">&nbsp;</div>    
    
                         


<div style="float: left; width:25%;">Attachment 1:</div>
<div style="float: left; width:69%;"><input class="input" name="file[]" type="file" style="width:99%; padding:5px 0px;" /></div>
<div style="clear: both; height:8px;">&nbsp;</div>
<div style="float: left; width:25%;">Attachment 2:</div>
<div style="float: left; width:69%;"><input class="input" name="file[]" type="file" style="width:99%; padding:5px 0px;" /></div>
<div style="clear: both; height:8px;">&nbsp;</div>
<div style="float: left; width:25%;">Attachment 3:</div>
<div style="float: left; width:69%;"><input class="input" name="file[]" type="file" style="width:99%; padding:5px 0px;" /></div>
<div style="clear: both; height:8px;">&nbsp;</div>

<div style="clear: both; border-bottom:dotted 1px #969292; height:8px; margin-bottom:5px;">&nbsp;</div>





    
<div style="float: left; width:25%;"></div>    
<div style="float: left; width:99%; margin-top:20px;">
<b>Before submitting please make sure of the following</b>
<ul>
<li>All necessary information has been filled out.</li>
<li>All information is correct and error-free.</li>
</ul>
<b>We have:</b>
<ul>
<li><?php echo $engine->getRealIpAddr(). ' recorded as your IP Address'; ?></li>
<li><?php echo htmlspecialchars(date("D M Y : H:i:s")); ?> recorded the time of your submission</li>
</ul>
</div>
<div style="clear: both; height:8px;">&nbsp;</div>


<?php
	if (isset($_SESSION['HESK_MESSAGE'])) { unset($_SESSION['HESK_MESSAGE']);}
?>
	
<div style="float: left; width:25%;"><img id="loading" style="width: 20%; display:none;" src="/theme/classic/images/rechargepro.gif"  /></div>
<input type="submit" class="mainbg" style="float: left; width: 69%; padding:7px 0px; border:none;" value="Submit Ticket"/>
<div style="clear: both; height:8px;">&nbsp;</div>
</form>
</div>