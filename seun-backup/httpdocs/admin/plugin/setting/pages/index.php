<?php
$engine = new engine();
$adminid = $engine->get_session('adminid');
?>
<script type="text/javascript">
function callstate(){
var country = $("#country").val();

          $.ajax({
                type: "POST",
                url: "engine/class/callstate.php",
                data: "country="+country,
                cache: false,
                success: function (html) {
$("#statea").html(html);
                }
            });
}



function calllga(){
var state = $("#state").val();

if($("#country").val() == "Nigeria"){

          $.ajax({
                type: "POST",
                url: "engine/class/lga/"+state+".php",
                data: "",
                cache: false,
                success: function (html) {
 $("#lgaa").html('<select name="lga" id="lga" class="input" style="width:99%;">'+html+'</select>');
                }
            });
            
            }else{
              $("#lgaa").html('<input type="text" name="lga" id="iga" class="input" style="width:99%;" />');  
            }    
}
</script>
<?php
$editable = "";
if(!$engine->config("user_edit_information")){$editable = 'disabled="disabled"';}

$editimg = "";
if(!$engine->config("user_change_image")){$editimg = 'disabled="disabled"';}

$row = $engine->db_query("SELECT lga,country,adminid,username,email,name,address,mobile,dob,state,role,sex,reg_date,active FROM admin WHERE adminid = ? LIMIT 1",array($adminid)); 
$adminid = $row[0]['adminid'];
$username = $row[0]['username'];
$email = $row[0]['email'];
$name = $row[0]['name'];
$address = $row[0]['address'];
$mobile = $row[0]['mobile'];
$dob = $row[0]['dob'];
$role = $row[0]['role'];
$sex = $row[0]['sex'];
$state = $row[0]['state'];
$country = $row[0]['country'];
$lga = $row[0]['lga'];
?>
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                $('#targets').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#file").change(function(){
        readURL(this);
    });
    
$(document).ready(function() { 

	var progressbox     = $('#progressbox');
	var progressbar     = $('#progressbar');
	var statustxt       = $('#statustxt');
	var completed       = '0%';
	
	var options = { 
			target:   '#output',   // target element(s) to be updated with server response 
			beforeSubmit:  beforeSubmit,  // pre-submit callback 
			uploadProgress: OnProgress,
			success:       afterSuccess,  // post-submit callback 
			resetForm: false        // reset the form after successful submit 
		}; 
		
	 $('#MyUploadForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// return false to prevent standard browser submit and page navigation 
			return false; 
		});
	
//when upload progresses	
function OnProgress(event, position, total, percentComplete)
{
	//Progress bar
	progressbar.width(percentComplete + '%') //update progressbar percent complete
	statustxt.html(percentComplete + '%'); //update status text
	if(percentComplete>50)
		{
			statustxt.css('color','#fff'); //change status text to white after 50%
		}
}

//after succesful upload
function afterSuccess(responseText, statusText, xhr, $form)
{
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
    
if(responseText == "ok"){location.reload();}

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{
/*
 	   if(!empty($("#password").val())){
        if(checkStrength($("#password").val()) != "strong"){
         $('#submit-btn').show(); //hide submit button
 		$('#loading-img').hide(); //hide submit button
 		$("#output").html("<span style='color:red;'>Password is weak, it must contain at least 1 Uppercase, number and Symbol</span>");  return false;
        }
        }
 */
       
		if($('#file').val()) //check empty input filed
		{
		
		var fsize = $('#file')[0].files[0].size; //get file size
		var ftype = $('#file')[0].files[0].type; // get file type
        

		
		//allow only valid image file types 
		switch(ftype)
        {
            case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> Unsupported file type!");
				return false
        }
		
		//Allowed file size is less than 1 MB (1048576)
		if(fsize>1048576) 
		{
			$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
			return false
		}
		
		//Progress bar
		progressbox.show(); //show progressbar
		progressbar.width(completed); //initial value 0% of progressbar
		statustxt.html(completed); //set status text
		statustxt.css('color','#000'); //initial color of status text

				
		$('#submit-btn').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output").html("");  
        }
	}
	else
	{
		//Output error to older unsupported browsers that doesn't support HTML5 File API
		$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
		return false;
	}
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

}); 

</script>
<script type="text/javascript">
jQuery(document).ready(function($){
var myCalendar;
myCalendar = new dhtmlXCalendarObject(["calendar1", "calendar2", "calendar3","calendar4","calendar5","calendar6","calendar7"]);
myCalendar.hideTime();
});
</script>


<div style="overflow: hidden; text-align: left; padding:8px; border:solid 1px #EEEEEE; margin: 5px;">
<div id="output"></div>
<div style="float: left; width:65%;">
<form action="plugin/setting/pages/save.php" onsubmit="return false" method="post" enctype="multipart/form-data" id="MyUploadForm">

<input name="adminid" type="hidden" value="<?php echo $adminid;?>" />
<input name="username" type="hidden" value="<?php echo $username;?>" />

<div style="overflow: hidden; margin-bottom:10px; border-bottom:solid 1px #EEEEEE;">
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Old Password</div>
<div style="float:left; width:60%;"><input  name="oldpassword" class="input" type="password" style="width:99%;"/></div>
</div>
<script type="text/javascript">
function checkpassword(){
var strength = checkStrength($("#password").val());
$("#password_strenght").html("<span style='color:#6DABC9;'>Password strenght is <span style='text-transform: capitalize;'>"+strength+"</span></span>");
}
</script>
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">New Password</div>
<div style="float:left; width:60%;"><input id="password"  name="password" class="input" type="password" style="width:99%;"/></div>
</div>
<div style="overflow: hidden; margin-bottom:5px; text-align:right;" id="password_strenght"></div>


<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Verify Password</div>
<div style="float:left; width:60%;"><input  name="password2" class="input" type="password" style="width:99%;"/></div>
</div>
</div>

<div style="overflow: hidden; margin-bottom:10px; padding-bottom:10px; border-bottom:solid 1px #EEEEEE;">
<div style="float:left; width:40%;">Role</div>
<div style="float:left; width:60%;">
<select <?php echo $editable;?>  name="role" class="input" style="width: 99%;">
<?php echo '<option>'.$role.'</option>';
if($role != "superadmin"){
?>
<option value=" <?php echo $role;?>"> <?php echo $role;?></option>
<?php
}
?>
</select></div>
</div>

<?php
if($engine->config("allow_user_theme_sellection")){
?>
<div style="overflow: hidden; margin-bottom:10px; padding-bottom:10px; border-bottom:solid 1px #EEEEEE;">
<div style="float:left; width:40%;">Portal Theme</div>
<div style="float:left; width:60%;">
<select name="theme" class="input" style="width: 99%;">
<?php 
if($engine->get_session("theme")){
$explodetheme = explode("/",$engine->get_session("theme"));
echo '<option value="'.$explodetheme[0].'">'.strtoupper($explodetheme[0]).'</option>';
}
?>
<option value="default">System Default</option>
<?php
$dir = $engine->config("theme_folder");
$a = scandir($dir,1);
$dirarray = array(".","..");
foreach($a AS $dirit){
if(in_array($dirit,$dirarray)){
    break;
}
echo '<option value="'.$dirit.'">'.strtoupper($dirit).'</option>';
}
?>
</select></div>
</div>
<?php
}
?>


<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Name</div>
<div style="float:left; width:60%;"><input <?php echo $editable;?> value="<?php echo $name;?>" name="name" class="input" type="text" style="width:99%;"/></div>
</div>


<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Address</div>
<div style="float:left; width:60%;"><input <?php echo $editable;?> value="<?php echo $address;?>" name="address" class="input" type="text" style="width:99%;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Mobile</div>
<div style="float:left; width:60%;"><input <?php echo $editable;?> value="<?php echo $mobile;?>" name="mobile" class="input" type="text" style="width:99%;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Date Of Birth</div>
<div style="float:left; width:60%;"><input <?php echo $editable;?> id="calendar1" value="<?php echo $dob;?>" name="dob" class="input" type="text" style="width:99%;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Sex</div>
<div style="float:left; width:60%;">
<select <?php echo $editable;?> name="sex" class="input" style="width:99%;">
<?php echo '<option>'.$sex.'</option>';?>
	<option>Male</option>
	<option>Female</option>
</select></div>
</div>


<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">Country</div>
<div style="float:left; width:60%;">
<select <?php echo $editable;?> name="country" id="country" onchange="callstate()" class="input" style="width:99%;">
<?php echo '<option>'.$country.'</option>';?>
<?php echo $engine->country();?>
</select></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">State</div>
<div style="float:left; width:60%;" id="statea">
<select <?php echo $editable;?> name="state" id="state" class="input" onchange="calllga()" style="width:99%;">
<?php echo '<option>'.$state.'</option>';?>
<?php foreach($engine->state($country) AS $stateb){
    echo '<option>'.$stateb.'</option>';
};?>
</select></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float:left; width:40%;">LGA</div>
<div style="float:left; width:60%;" id="lgaa">
<select <?php echo $editable;?> name="lga" id="lga" class="input" style="width:99%;">
<?php echo '<option>'.$lga.'</option>';?>
<?php echo $engine->lga($state);?>
</select></div>
</div>

</div>

<div style="float: right; width:30%;">
<div>
<?php
if(file_exists("avater/".$username.".jpg")){
    echo '<img id="targets" src="avater/'.$username.'.jpg" class="shadow" style="width:99%; margin:5px;" />';
}else{
?>
<img id="targets" src="<?php echo $engine->config("theme_folder").$engine->config("theme");?>/images/default.png" class="shadow" style="width:99%; margin:5px;" />
<?php
	}
?>
</div>

<?php
if($engine->config("user_change_image")){	
?>
<input <?php echo $editimg;?> id="file" name="file" type="file" style="margin:5px; width:94%;" class="shadow input"/>
<?php
}	
?>
<div>
<input style="padding: 3px 0px; border:none; margin:5px; width:99%;"  id="submit-btn" class="shadow activemenu" type="submit" value="Save" />
<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
</div>
</form>
<div id="progressbox" style="display:none;"><div id="progressbar"></div ><div id="statustxt">0%</div></div>
</div>




</div>