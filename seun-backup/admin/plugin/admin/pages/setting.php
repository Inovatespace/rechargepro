<?php
$engine = new engine();

?>
<div  style="padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">
<div class="admin_page_title">Application Setting</div>
</div>

<div style="padding:20px 40px; overflow:hidden;">
<div style="border-bottom: solid #DDDDDD 1px;padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">System Setting</div>
<form action="plugin/admin/pages/pro/settingpro.php" method="post">
<div style="overflow: hidden;">
<div style="float: left; width:57%; overflow:hidden;">
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Website Location</div><div style="float: left; width:50%"><input name="websitelocation" value="<?php echo $engine->config("website_root");?>" type="text" class="input" style="width:99%" /></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Administrator Email</div><div style="float: left; width:50%"><input name="email" value="<?php echo $engine->config("admin_email");?>" type="text" class="input" style="width:99%" /></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Company Name</div><div style="float: left; width:50%"><input name="companyname" value="<?php echo $engine->config("author");?>" type="text" class="input" style="width:99%" /></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Company Mobile</div><div style="float: left; width:50%"><input name="mobile" value="<?php echo $engine->config("admin_mobile");?>" type="text" class="input" style="width:99%" /></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Company Website</div><div style="float: left; width:50%"><input name="website" value="<?php echo $engine->config("admin_website");?>" type="text" class="input" style="width:99%" /></div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Application Name</div><div style="float: left; width:50%"><input name="appname" value="<?php echo $engine->config("app_name");?>" type="text" class="input" style="width:99%" /></div>
</div>


<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Site Width</div><div style="float: left; width:15%"><input name="sitewidth" value="<?php $width = explode(":",$engine->config("site_width")); $width = explode("px",strtolower($width[1])); $width = explode("%",strtolower($width[0])); $width = explode("cm",strtolower($width[0])); echo $width[0];?>" type="text" class="input" style="width:99%" /></div>
<div style="float: left; text-align:right; width:20%">Unit =&raquo; </div>
<div style="float: left; width:15%">
<select class="input" style="width:98%" name="sitewidthunit">
<?php
$theunit = explode("px",strtolower($engine->config("site_width")));
$unit = "px";

if(stripos($theunit[0],"%") > 0){
$theunit = explode("%",strtolower($width[0]));
$unit = "%";
}

if(stripos($theunit[0],"cm") > 0){
$theunit = explode("cm",strtolower($width[0]));
$unit = "cm";
}
?>
<option><?php echo trim($unit);?></option>
<option>%</option>
<option>px</option>
<option>cm</option>
</select>
</div>
</div>

<div style="overflow: hidden; margin-bottom:20px;">
<div style="float: left; width:50%">Site Key</div><div style="float: left; width:50%"><input name="sitekey" value="<?php echo $engine->config("user_key");?>" type="text" class="input" style="width:99%" /></div>
</div>
</div>
<div class="profilebg" style="height:160px; padding:10px; margin:10px; float: right; width:33%; border:solid 1px #EEEEEE;">
System Setting:<br />
Email to receive alart, Portal with, portal name.<br />
NOTE :: Changing of site key will prevent existing users from using the system
</div>
</div>











<div style="border-bottom: solid #DDDDDD 1px;padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">User Setting</div>
<div style="overflow: hidden;">
<div style="float: left; width:57%">
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Enable dashboard</div><div style="float: left; width:50%">
<select  name="show_dashboard" class="input" style="width:99%">
<?php if($engine->config("show_dashboard")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select>
</div>
</div>


<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Dashboard Height</div><div style="float: left; width:50%">
<input name="dashboard_size" value="<?php echo $engine->config("dashboard_size");?>" type="text" class="input" style="width:99%" />
</div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Dashboard Format</div><div style="float: left; width:50%">
<select  name="dashboard_format" class="input" style="width:99%">
<?php if($engine->config("dashboard_format") == 1){echo '<option value="1">Dashboard</option>';}else{echo '<option value="2">Blog</option>';}?>
	<option value="1">Dashboard</option>
	<option value="2">Blog</option>
</select>
</div>
</div>


<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Allow user to change theme</div><div style="float: left; width:50%">
<select  name="allow_user_theme_sellection" class="input" style="width:99%">
<?php if($engine->config("allow_user_theme_sellection")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select>
</div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Allow IP change</div><div style="float: left; width:50%">
<select  name="allow_ipchange" class="input" style="width:99%">
<?php if($engine->config("allow_ipchange")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select>
</div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Allow User to change widget stats</div><div style="float: left; width:50%">
<select  name="allow_widgetstate" class="input" style="width:99%">
<?php if($engine->config("user_widget_state_change")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select>
</div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Allow User to change Profile Photo</div><div style="float: left; width:50%">
<select  name="user_change_image" class="input" style="width:99%">
<?php if($engine->config("user_change_image")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select>
</div>
</div>

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Allow User to change profile Information</div><div style="float: left; width:50%">
<select  name="user_edit_information" class="input" style="width:99%">
<?php if($engine->config("user_edit_information")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select>
</div>
</div>
</div>

</div>

<div style="border-bottom: solid #DDDDDD 1px;padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">Database Setting</div>
<div style="overflow: hidden;">
<div style="float: left; width:57%">
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Database Host</div><div style="float: left; width:50%"><input name="dbhost" value="<?php $host = explode("host=",$engine->config("database_dsn")); $host = explode(";",$host[1]); echo $host[0];?>" type="text" class="input" style="width:99%" /></div>
</div>
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Database Name</div><div style="float: left; width:50%"><input name="dbname" value="<?php $dbname = explode("mysql:dbname=",$engine->config("database_dsn")); $dbname = explode(";",$dbname[1]); echo $dbname[0];?>" type="text" class="input" style="width:99%" /></div>
</div>
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Database User</div><div style="float: left; width:50%"><input name="dbuser" value="<?php echo $engine->config("database_user");?>" type="text" class="input" style="width:99%" /></div>
</div>
<div style="border-bottom: solid #DDDDDD 1px;overflow: hidden; margin-bottom:30px;">
<div style="float: left; width:50%">Database Password</div><div style="float: left; width:50%"><input name="dbpassword" value="<?php echo $engine->config("database_pass");?>" type="password" class="input" style="width:99%" /></div>
</div>
</div>
<div class="profilebg" style="height:90px; padding:10px; margin:10px; float: right; width:33%; border:solid 1px #EEEEEE;">For debuging and system administration, do not change unless you know what you are doing</div>
</div>




<div style="border-bottom: solid #DDDDDD 1px;padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">Developer Mode</div>
<div style="overflow: hidden;">
<div style="float: left; width:57%">
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Log Error</div><div style="float: left; width:50%">
<select  name="logerror" class="input" style="width:99%">
<?php if($engine->config("log_error")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select>
</div>
</div>
<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; width:50%">Display Error</div><div style="float: left; width:50%">
<select  name="displayerror"class="input" style="width:99%">
<?php if($engine->config("display_error")){echo '<option>true</option>';}else{echo '<option>false</option>';}?>
	<option>true</option>
	<option>false</option>
</select></div>
</div>
</div>
<div class="profilebg" style="padding:10px; margin:10px; float: right; width:33%; border:solid 1px #EEEEEE;">For debuging : <br /> Log error should be set to true always, set display error to true only when system is undergoing development</div>
</div>
<div><input type="submit" /></div>
</form>
</div>