<?php
$engine = new engine();
?>

<script type="text/javascript">
function remove_me(id){
    $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to remove authorisation?',
    buttons: {
        confirm: function () {
            
    var dataString = "id="+id;                            
    $("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
    $.ajax({
    type: "POST",
    url: "plugin/admin/pages/pro/removeapi.php",
    data: dataString,// + "&id="+ Id,
    cache: false,
    success: function(){
    $("#a"+id).fadeOut(300);
    }
    });
    
    },
        cancel: function () {
            
        }
    }
}); 
   
}

</script>
<div  style="padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">
<div class="admin_page_title">Manage Authorisation</div>
</div>

<?php include "notification.php";?>

<div style="padding: 10px;">

<style type="text/css">
.stats{position: relative; overflow:hidden; background-color:#E7E7E7; border-bottom:1px solid white; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #DDDDDD; border-bottom:1px solid white; padding:1px;}
.stats:hover {background: #F3F3F3; color:#F9C93A;}
</style>

<div class="profilebg" style="border:solid 1px #CCCCCC; overflow: hidden;">
<div class="profilebg tunnel" name="plugin/admin/pages/newemail.php?width=400" style="border-left:solid 1px #CCCCCC; cursor:pointer; padding:5px; float: right;"><img src="images/small_icons/plus.png" width="16" height="16"/></div>
</div>
<div style="clear: both;"></div>

<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:21%;">Email</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;">Domain</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;">Host</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;">Protocal</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;">Name</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:9%;">Date</div>
</div>

<?php
$color = 1;
$row = $engine->db_query("SELECT id, email_account.host, email_account.email, email_account.acountid, admin.name, email_account.date, email_account.protocal  FROM email_account JOIN admin ON admin.adminid = email_account.acountid",array()); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$id = $row[$dbc]['id'];
$email = $row[$dbc]['email'];
$host = $row[$dbc]['host'];
$domain = explode("@",$email);
$name = $row[$dbc]['name'];
$acountid = $row[$dbc]['acountid'];
$protocal = $row[$dbc]['protocal'];

$date = date("Y-m-d",  strtotime("+0 day", strtotime($row[$dbc]['date'])));
if($color==1){
?>
<div id="a<?php echo $id;?>" class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:21%;"><?php echo $email;?> <img onclick="remove_me('<?php echo $id;?>')" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/cross.png" width="16" height="16" /> </div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $domain[1];?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $host;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $protocal;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $name;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:9%;"><?php echo $date;?></div>
</div>
<?php
$color=2;
}else{
?>
<div id="a<?php echo $id;?>" class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:21%;"><?php echo $email;?> <img onclick="remove_me('<?php echo $id;?>')" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/cross.png" width="16" height="16" /> </div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $domain[1];?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $host;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $protocal;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:16%;"><?php echo $name;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:9%;"><?php echo $date;?></div>
</div>
<?php    
$color=1;    
}
}

if(!isset($acountid)){ echo '<div class="nWarning">No account found</div>';}
?>

</div>