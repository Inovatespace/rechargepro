<?php
if(!isset($_SESSION)){
 include "../../../engine.autoloader.php";   
}

$engine = new engine();
if(!$engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};

if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        echo "<div class='nWarning' style='color:white; font-size:150%; color:black; text-align:center; margin-top:50px;'>this Account is not authorised for this action</div>"; exit;
    }
}



if(isset($_REQUEST['getemail'])){

$mid = $_REQUEST['mid'];
$dt = $_REQUEST['getemail'];
$engine->db_query("UPDATE rechargepro_account_read SET 	receive_email = ? WHERE readid = ? AND rechargeproid = ? LIMIT 1",array($dt,$mid,$engine->get_session("rechargeproid")));
   
   //email
}


if(isset($_REQUEST['freme'])){


	switch ($_REQUEST['freme']){ 
	case "0": $dt = '0'; $os = "NONE";
	break;

	case "1": $dt = '1'; $os = "ANDROID";
	break;

	case "2": $dt = '2'; $os = "IOS";
	break;
    
    case "3": $dt = '3'; $os = "WINDOWS";
	break;

	default : $dt = '0'; $os = "NONE";
    }

   $engine->db_query("UPDATE rechargepro_account SET 	bypass = ? WHERE rechargeproid = ? LIMIT 1",array($dt,$engine->get_session("rechargeproid")));
   
   //email
}


$row = $engine->db_query("SELECT bypass FROM rechargepro_account WHERE rechargeproid = ?",array($engine->get_session("rechargeproid")));
$bypass = $row[0]['bypass'];


?>
<script type="text/javascript">
function delete_access(Id){
    
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to Delete this device?',
    buttons: {
        confirm: function () {
            
    	$.ajax({
		type: "POST",
		url: "theme/classic/pages/authorization/delete.php",
		data: "id="+Id,
		cache: false,
		success: function(html){
		  //alert(html);
		  window.location.reload();
		}
	   });
    
    },
        cancel: function () {
            
        }
    }
}); 
  
  }
    
function delete_readaccess(Id){
    
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to Delete this account?',
    buttons: {
        confirm: function () {
            
    	$.ajax({
		type: "POST",
		url: "theme/classic/pages/authorization/readdelete.php",
		data: "id="+Id,
		cache: false,
		success: function(html){
		  //alert(html);
		  window.location.reload();
		}
	   });
    
    },
        cancel: function () {
            
        }
    }
}); 
         
}
</script>
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px; overflow: hidden;">
<script type="text/javascript">
  jQuery(document).ready(function($){

$('.arrow-tabs a').click(function (e) {
  e.preventDefault()
  var selectedTab = $(this).parent();
  var ul = selectedTab.parent();
  ul.find("li").removeClass("ui-tabs-active ui-state-active");
  selectedTab.addClass("ui-tabs-active ui-state-active");
  
  // show/hide content
    var content = ul.parent().find(".contents .ui-tabs-panel");
    content.hide();  
    ul.parent().find($(this).attr("href")).fadeIn(200);   
});
});
</script>
<style type="text/css">
.arrow-tabs {
  width: 90%;
  min-width: 360px;
  margin: auto;
  
}
.arrow-tabs > ul {
  text-align: left;
  font-weight: 500;
  padding: 0;
  position: relative;
  border-bottom: 1px solid rgba(0, 0, 0, 0.2);
  z-index: 1;
}
.arrow-tabs > ul li{text-align:center;}
.arrow-tabs > ul > li {
  display: inline-block;
  background: #FFFFFF;
  padding: 0.6em 0;
  position: relative;
  width: 33%;
  margin: 0 0 0 -4px;
}
.arrow-tabs > ul > li:before, .arrow-tabs > ul > li:after {
  opacity: 0;
  transition: 0.3s ease;
}
.arrow-tabs > ul > li.ui-tabs-active:before, .arrow-tabs > ul > li.ui-tabs-active:after {
  opacity: 1;
}
.arrow-tabs > ul > li.ui-tabs-active a {
  color: #009994;
}
.arrow-tabs > ul > li:hover:before, .arrow-tabs > ul > li:focus:before, .arrow-tabs > ul > li:hover:after, .arrow-tabs > ul > li:focus:after {
  opacity: 1;
}
.arrow-tabs > ul > li:before, .arrow-tabs > ul > li.ui-state-active:hover:before, .arrow-tabs > ul > li.ui-state-active:focus:before {
  content: "";
  position: absolute;
  z-index: -1;
  box-shadow: 0 2px 3px rgba(0, 153, 148, 0.5);
  top: 50%;
  bottom: 0px;
  left: 5px;
  right: 5px;
  border-radius: 100px / 10px;
}
.arrow-tabs > ul > li:after, .arrow-tabs > ul > li.ui-state-active:hover:after, .arrow-tabs > ul > li.ui-state-active:focus:after {
  content: "";
  background: #fafafa;
  position: absolute;
  width: 12px;
  height: 12px;
  left: 50%;
  bottom: -6px;
  margin-left: -6px;
  transform: rotate(45deg);
  box-shadow: 3px 3px 3px rgba(0, 153, 148, 0.5), 1px 1px 1px rgba(0, 0, 0, 0.3);
}
.arrow-tabs > ul > li:hover:before, .arrow-tabs > ul > li:focus:before {
  box-shadow: 0 2px 3px rgba(0, 0, 0, 0.2);
}
.arrow-tabs > ul > li:hover:after, .arrow-tabs > ul > li:focus:after {
  box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.2), 1px 1px 1px rgba(0, 0, 0, 0.3);
}
.arrow-tabs > ul > li:focus {
  outline: none;
}
.arrow-tabs > ul > li a {
  color: #444;
  text-decoration: none;
}
.arrow-tabs > ul > li a:focus {
  outline: none;
  text-decoration: none;
}
.arrow-tabs > ul > li a span {
  position: relative;
  top: -0.5em;
}

.contents {
  padding: 20px 10px;
  min-height: 200px;
}

</style>

<div class="lightpage">
  
<div class="arrow-tabs ui-tabs ui-widget ui-widget-content ui-corner-all">
  
  <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
      
      <li class="ui-state-default ui-corner-top ui-tabs-active" role="tab">          
        <a href="#Beyonce" class="ui-tabs-anchor" role="presentation">
            <span>Account Authourization</span>            
        </a>        
      </li>
      
      <li class="ui-state-default ui-corner-top" role="tab">
        <a href="#Fergie" class="ui-tabs-anchor" role="presentation">
            <span>Account Read Access</span>        
        </a>        
      </li>

      
  </ul>
  
  <div class="contents">
    <div id="Beyonce" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel">
<?php
	if($bypass > 0){
	  switch ($bypass){ 
	case "0": $dt = 'fab fa-internet-explorer'; $os = "WEB";
	break;

	case "1": $dt = 'fab fa-android'; $os = "ANDROID";
	break;

	case "2": $dt = 'fab fa-apple'; $os = "IOS";
	break;
    
    case "3": $dt = 'fab fa-windows'; $os = "WINDOWS";
	break;

	default : $dt = 'fab fa-windows'; $os = "-";
    }
    
    ?>
    <div style="color: red; font-size: 150%;"><span class="fas fa-exclamation-triangle"></span> NOTE : You have disabled security on <?php echo $os;?> devices</div>
    
    <?php  
       
       }
?>

<div style="margin-bottom: 20px; font-size:120%; float:left;"><span class="fas fa-check-circle" style="color: #07A23B; font-size: 120%;"></span> This Device is registered for rechargepro</div>

<input type="button" value="Generate Authentication Code" name="/theme/classic/pages/authorization/generate.php?width=200" style="float: right; border:none; padding:5px; cursor: pointer; margin-bottom:10px;" class="mainbg tunnel" />
<div style="clear: both;"></div>









<div style="margin-bottom: 20px;">

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />


<div class="nInformation">You can use multiple password to access your account from deferent device, the password you set here does not affect your main password</div>


<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>#</th>
<th>NAME</th>
<th>OS</th>
<th>SET PASSWORD</th>
<th>REMOVE</th>
<th>DATE ADDED</th>
</tr>
</thead>
<tbody>

<?php
$regdevice = 0;
$email = $engine->get_session("rechargeproemail");
$row = $engine->db_query("SELECT device_type,id,email,mac,name,date,password FROM rechargepro_access WHERE email = ?",array($email));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $device_type = $row[$dbc]['device_type'];
    $id = $row[$dbc]['id']; 
    $email = $row[$dbc]['email']; 
    $mac = $row[$dbc]['mac']; 
    $name = $row[$dbc]['name']; 
    $date = $row[$dbc]['date']; 
    $password = $row[$dbc]['password'];
    $regdevice++;
    
    switch ($device_type){ 
	case "0": $dt = 'fab fa-internet-explorer'; $os = "WEB";
	break;

	case "1": $dt = 'fab fa-android'; $os = "ANDROID";
	break;

	case "2": $dt = 'fab fa-apple'; $os = "IOS";
	break;
    
    case "3": $dt = 'fab fa-windows'; $os = "WINDOWS";
	break;

	default : $dt = 'fab fa-windows'; $os = "WINDOWS";
    }


$p = "******";
if($password == ""){
    $p = "";
}
    
?>
<tr >
<td style="font-size: 180%;" class="<?php echo $dt;?>"></td>
<td><?php echo $name;?></td>
<td><?php echo $os;?></td>
<td><?php echo $p;?> <span style="cursor: pointer;" class="fa fa-edit tunnel" name="theme/classic/pages/authorization/changepassword.php?id=<?php echo $id;?>&width=200"></span></td>
<td><span class="fas fa-trash-alt" style="cursor: pointer;" onclick="delete_access(<?php echo $id;?>)"></span></td>
<td><?php echo $date;?></td>
</tr>
<?php
	}
?>

    
</tbody>
</table>

</div>


</div>


<div>Maximum Device - 3</div>
<div style="margin-bottom: 20px;">Remaining <?php echo 3-$regdevice;?></div>


<div style="font-weight: bold;">You are permited to register a maximum of three devices.</div>
<div style="font-weight: bold; margin-bottom:30px;">you can add and remove any device at anytime</div> 



<div class="nWarning" style="text-align: left;">You can by pass security on any of the platform below, <br /><strong style="color: red;">NOTE:</strong> This is not recommended, this features is only usefull in a large  super market/mall  where a cashier/operator is allowed to switch till/pay point at anytime</div>

<script type="text/javascript">
function freeme(Id){
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/authorisation.php",
		data: "freme="+$("input[name='freme']:checked").val(),
		cache: false,
		success: function(html) {
		      $.alert("Saved");
              window.location.reload();
		}
	});
}
</script>


<div>
<?php

$c0 = "";
$c1 = "";
$c2 = "";
$c3 = "";

switch ($bypass){
	case "1": $c1 = 'checked="checked"';
	break;

	case "2": $c2 = 'checked="checked"';
	break;

	case "3": $c3 = 'checked="checked"';
	break;

	default : $c0 = 'checked="checked"';
}
?>
<ul>
<li><input <?php echo $c0;?> value="0" type="radio"  name="freme" id="freme1" onchange="freeme(1)" /> NONE</li>
<li><input <?php echo $c3;?> value="3" type="radio" name="freme" id="freme2" onchange="freeme(2)" /> WINDOWS</li>
<li><input <?php echo $c2;?> value="2" type="radio" name="freme" id="freme3" onchange="freeme(3)" /> IOS</li>
<li><input <?php echo $c1;?> value="1" type="radio" name="freme" id="freme4" onchange="freeme(4)" /> ANDROID</li>
</ul>
</div>
    </div>
    
    <div id="Fergie" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" style="display: none;">
      


<script type="text/javascript" src="/java/sort/jquery.tablesorter.js"></script>

<script type="text/javascript">
$(document).ready(function(){$("#myTable2").tablesorter();});
</script>
<script type="text/javascript">
function check_me(Id){
    	$.ajax({
		type: "POST",
		url: "/theme/classic/pages/authorisation.php",
		data: "getemail="+$("input[name='getemail']:checked").val()+"&mid="+Id,
		cache: false,
		success: function(html) {
		      $.alert("Saved");
              window.location.reload();
		}
	});
}
</script>

<button name="/theme/classic/pages/authorization/readnewprofile.php?width=350" style="float: left; border:none; padding:5px; cursor: pointer; margin-bottom:10px;" class="mainbg tunnel" ><span class="fas fa-plus"></span> New Account </button>
<div style="clear: both;"></div>

<table id="myTable2" class="tablesorter" style="font-family:'Trebuchet MS', Verdana, Arial, Helvetica, sans-serif;; font-size:85%;">
<thead>
<tr style="text-transform: uppercase; padding:5px;">
<th>Name</th>
<th>Email</th>
<th>End of Day Email</th>
<th>#</th>
</tr>
</thead>
<tbody>
<?php
$row = $engine->db_query("SELECT readid,reademail,receive_email,readname FROM rechargepro_account_read WHERE rechargeproid=?",array($engine->get_session("rechargeproid")));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    
    //$profile_percentage = $row[$dbc]['profile_percentage'];
    $readid = $row[$dbc]['readid']; 
    $reademail = $row[$dbc]['reademail']; 
    $receive_email = $row[$dbc]['receive_email']; 
    $reaname = $row[$dbc]['readname']; //

    $check = '';
    $v = '1';
    if($receive_email == 1){$check = 'checked="checked"'; $v = '0';}
?>

<tr style="font-size: 150%;  padding:5px;">
<td><?php echo $reaname;?> </td>
<td><?php echo $reademail;?></td>
<td><input type="checkbox"  name="getemail" <?php echo $check;?> value="<?php echo $v;?>" id="getemail" onclick="check_me('<?php echo $readid;?>')" /></td>
<td><span style="cursor: pointer;" class="fa fa-edit tunnel" name="theme/classic/pages/authorization/readchangepassword.php?id=<?php echo $readid;?>&width=200"></span><span class="fas fa-trash-alt" style="cursor: pointer;" onclick="delete_readaccess(<?php echo $readid;?>)"></span></td>
</tr>
<?php
	}
 	if(!isset($reaname)){  echo "<tr><td colspan='4'><div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div></td></tr>";}?>






</tbody>
</table>
    </div>
    
  </div>
</div>

</div>



















</div> 





