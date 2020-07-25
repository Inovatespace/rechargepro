<?php
$engine = new engine();
?>

<script type="text/javascript">
function remove_admin(Id,Username){
    
    $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to Delete this User?',
    buttons: {
        confirm: function () {
            
            var dataString = 'id='+Id+'&username='+Username;                            
            $("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
            $.ajax({
            type: "POST",
            url: "plugin/admin/pages/pro/deleteuser.php",
            data: dataString,
            cache: false,
            success: function(){
            location.reload();
            }
            });
    
    },
        cancel: function () {
            
        }
    }
}); 
  
    
}

function change_status(Id,Username){
    
    var sentvalue = 0;
    if($("#active"+Username).is(':checked')){
     sentvalue = 1;   
    }
                           var dataString = 'id='+Username+'&value='+sentvalue;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/changestatus.php",
                        data: dataString,
                        cache: false,
                        success: function(){
location.reload();
}
                        });
}


function changeit_dashboard(Id,Username,sentvalue){
    
    //var sentvalue = 0;
   // if($("#dash"+Username).is(':checked')){
   //  sentvalue = 1;   
  //  }
    
    //alert(sentvalue);
                           var dataString = 'id='+Username+'&value='+sentvalue;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/seedashboard.php",
                        data: dataString,
                        cache: false,
                        success: function(html){
location.reload();
}
                        });
}
</script>

<div>

<div  style="padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">
<div class="admin_page_title">Manage Admin</div>
<div id="name" style="float: right;"></div>
</div>

<?php include "notification.php";?>


<div style="margin-bottom:10px; background-color:white; padding:10px; overflow:hidden;">
<div class="radious10 profilebg" style="padding:20px; border:solid 1px #EEEEEE; overflow:hidden;">

<?php	if ($engine->config("local_authentication")) {?>
<div style="text-align: right; font-size:25px; margin-bottom:10px;">Add Admin <img class="tunnel" name="plugin/admin/pages/newadmin.php?width=500" style="vertical-align: middle; cursor: pointer;" src="plugin/admin/images/user.png" width="50" /></div><?php } ?>


<div style="float: left; width:33%; background-color:#F9F6F6; margin-right:20px;">
<div class="shadow greenmenu" style="padding: 2px 5px;">{LANMAIN_ADMINUSER}</div>
<div style="padding: 5px;">
<div class="profilebg" style="font-weight:bold; color:#2B649E; overflow:hidden; padding:0px 5px;">
<div style="float: left;" class="transparent">Name</div>
<?php	if ($engine->config("local_authentication")) {?>
<div style="float: right;"><img class="transparent" src="plugin/admin/images/cross.png" width="12" /></div>
<div style="float: right; margin-right:5px;" class="transparent"><img src="plugin/admin/images/pencil.png" width="12" /></div>
<?php	}?>
<div style="float: right; margin-right:5px;" class="transparent"><input disabled="disabled" type="checkbox" id="ab1" /><label for="ab1"><span></span></label></div>
<div style="float: right; margin-right:5px;"><span class="fa fa-dashboard fa-fw" style="color: black;"></span></div>
</div>
<div style="height:350px; overflow-y: auto; overflow-x: hidden;">
<?php 
$staffid = "";
$adminusers = $engine->admin_users();
for($i=0; $i < count($adminusers); $i++){
    
$uchech = "";
if($adminusers[$i]['active'] == 1){
$uchech = 'checked="checked"';
} 


$dashsee = "";
$dashval = "0";
if($adminusers[$i]['seedashboard'] == 0){
$dashsee = 'checked="checked"';
$dashval = "1";
}
    
if($i == 0){$staffid = $adminusers[$i]['adminid'];}

$role = '<img style="cursor: pointer;" onclick="remove_admin(\''.$adminusers[$i]['adminid'].'\',\''.$adminusers[$i]['username'].'\')" src="plugin/admin/images/cross.png" width="12" />';

$disable = '<input '.$uchech.' name="active'.$adminusers[$i]['username'].'" id="active'.$adminusers[$i]['username'].'" onchange="change_status(\''.$adminusers[$i]['adminid'].'\',\''.$adminusers[$i]['username'].'\')" type="checkbox" value="'.$adminusers[$i]['adminid'].'" /><label for="active'.$adminusers[$i]['username'].'"><span></span></label>';

$dashdisable = '<input '.$dashsee.' name="dash'.$adminusers[$i]['username'].'" id="dash'.$adminusers[$i]['username'].'" onchange="changeit_dashboard(\''.$adminusers[$i]['adminid'].'\',\''.$adminusers[$i]['username'].'\',\''.$dashval.'\')" type="checkbox" value="'.$adminusers[$i]['adminid'].'" /><label for="dash'.$adminusers[$i]['username'].'"><span></span></label>';

if($adminusers[$i]['role'] == "superadmin"){$role = "&nbsp;"; $disable = '&nbsp;'; $dashdisable = '&nbsp;';}
    ?>
    
<div class="profilebg" style="overflow:hidden; padding: 5px;">
<a href="switch?u=<?php echo $adminusers[$i]['adminid'];?>"><img src="plugin/admin/images/view.png" style="float:left; margin-right:3px; vertical-align: middle;" width="23" height="13" /></a>
<img style="float: left; margin-right: 4px;" src="plugin/admin/images/<?php echo $adminusers[$i]['type'];?>.png" width="12" height="12" />
<div id="n<?php echo $adminusers[$i]['adminid'];?>" style="float: left;"><a href="admin&p=index&i=<?php echo $adminusers[$i]['adminid'];?>"><?php echo substr($adminusers[$i]['name'], 0, 17);?></a></div>
<?php	if ($engine->config("local_authentication")) {?>
<div style="float: right;"><?php echo $role;?></div>
<div style="float: right; margin-right:5px;"><img style="cursor: pointer;" class="tunnel" name="plugin/admin/pages/editadmin.php?width=460&id=<?php echo $adminusers[$i]['adminid'];?>" src="plugin/admin/images/pencil.png" width="12" /></div>
<?php	}?>
<div style="float: right; margin-right:5px;"><?php echo $disable;?></div>
<div style="float: right; margin-right:5px;"><?php echo $dashdisable;?></div>
</div>
<?php } ?>
</div>
</div>
</div>
<?php if(isset($_REQUEST['i'])){$staffid = $_REQUEST['i'];}?>
<script type="text/javascript">
$(document).ready( function() {
    $("#name").html($("#n<?php echo $staffid;?>").html());
    })
</script>



<div style="float: left; width:30%; background-color:#F9F6F6; margin-right:20px;">
<div class="shadow redmenu" style="padding: 2px 5px; overflow:hidden;"><div style="float: left;">Plugins</div></div>
<form method="post" action="plugin/admin/pages/pro/pluginpro.php">
<input name="adminid" type="hidden" value="<?php echo $staffid;?>" />
<?php
$pluginarray = array();
$row = $engine->db_query("SELECT pluginid FROM admin_plugin WHERE adminid = ?",array($staffid));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){  
$pluginarray[] =  $row[$dbc]['pluginid'];      
    }
    
 $row = $engine->db_query("SELECT pluginid, name, pluginkey FROM plugin ",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){  

    $checkb = "";
    $pen = "";
    if(in_array($row[$dbc]['pluginid'],$pluginarray)){
     $checkb = 'checked="checked"';   
     $pen = '<img style="cursor: pointer;" class="tunnel" name="plugin/admin/pages/manageplugin.php?width=485&id='.$staffid.'&u='.urlencode($row[$dbc]['pluginid']).'&c='.urlencode($row[$dbc]['pluginkey']).'&n='.urlencode($row[$dbc]['name']).'" src="plugin/admin/images/pencil.png" width="16" height="16" />';
    }
        
 echo '<div class="shadow" style="margin: 5px; padding:5px; overflow:hidden;">
<div style="float: left; margin-right:5px;"><img src="plugin/admin/images/plugin.png" height="16" /></div>
<div style="float: left;">'.$row[$dbc]['name'].'</div>
<div style="float: right;"><input name="plugin[]" id="pid'.$row[$dbc]['pluginid'].'" value="'.$row[$dbc]['pluginid'].'" type="checkbox" '.$checkb.'/><label for="pid'.$row[$dbc]['pluginid'].'"><span></span></label></div>
<div style="float: right; margin-right:5px;">'.$pen.'</div>
</div>';   
};if(!isset($pen)){ echo '<div class="nWarning" style="margin:5px;">No Plugin Installed</div>';}
?>
<div style="overflow: hidden; z-index:5; position:relative;">
<div class="activemenu" style="margin-left:50px; float: left; width:10px; height:20px; margin-bottom:-5px;"></div>
<div class="activemenu" style="margin-right:50px; float: right; width:10px; height:20px; margin-bottom:-5px;"></div>
</div>
<div style="z-index: 2; position:relative;">
<input class="activemenu shadow" style="border: none; padding:3px 0px; width:99%;" type="submit" value="Save" />
</div>
</form>
</div>


<div style="float: left; width:30%; background-color:#F9F6F6;">
<div class="shadow middlemenu" style="padding: 2px 5px;">Widgets</div>
<form method="post" action="plugin/admin/pages/pro/widgetpro.php">
<input name="adminid" type="hidden" value="<?php echo $staffid;?>" />
<?php
$mywidgetarray = array();
$row = $engine->db_query("SELECT widgetid FROM admin_widget WHERE adminid = ?",array($staffid));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){ 
$mywidgetarray[] =  $row[$dbc]['widgetid'];  
    }
    
$row = $engine->db_query("SELECT widgetid, widgetkey FROM widget ",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){ 
    $check = "";
    if(in_array($row[$dbc]['widgetid'],$mywidgetarray)){
     $check = 'checked="checked"';   
    }
    
 echo '<div class="shadow" style="margin: 5px; padding:5px; overflow:hidden;">
 <div style="float: left; margin-right:5px;"><img src="plugin/admin/images/bulb.png"height="16" /></div>
<div  style="float: left;">'.$row[$dbc]['widgetkey'].'</div>
<div style="float: right;"><input name="widget[]" id="wid'.$row[$dbc]['widgetid'].'" value="'.$row[$dbc]['widgetid'].'" type="checkbox" '.$check.'/><label for="wid'.$row[$dbc]['widgetid'].'"><span></span></label></div>
</div>';
};
if(!isset($check)){ echo '<div class="nWarning" style="margin:5px;">No Widget Installed</div>';}  
?>
<div style="overflow: hidden; z-index:5;">
<div class="activemenu" style="margin-left:50px; float: left; width:10px; height:20px; margin-bottom:-5px;"></div>
<div class="activemenu" style="margin-right:50px; float: right; width:10px; height:20px; margin-bottom:-5px;"></div>
</div>
<div style="z-index: 2;">
<input class="activemenu shadow" style="border: none; padding:3px 0px; width:99%;" type="submit" value="Save" />
</div>
</form>
</div>




</div>
</div>
















</div>