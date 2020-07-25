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
    url: "plugin/admin/pages/pro/removeterminal.php",
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
<div class="profilebg tunnel" name="plugin/admin/pages/newterminal.php?width=400" style="border-left:solid 1px #CCCCCC; cursor:pointer; padding:5px; float: right;"><img src="images/small_icons/plus.png" width="16" height="16"/></div>
</div>
<div style="clear: both;"></div>

<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:12%;">Account ID</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:28%;">Name</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:45%;">Access</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:8%;">Date</div>
</div>

<?php
$array = array();
$row = $engine->db_query("SELECT id, name FROM terminal_permission",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$array[$row[$dbc]['id']] =  $row[$dbc]['name'];
}

$color = 1;
$row = $engine->db_query("SELECT members.name, terminal_acces.account_id, terminal_acces.access, terminal_acces.date, terminal_acces.id FROM terminal_acces JOIN members ON terminal_acces.account_id = members.staffid",array()); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$name = $row[$dbc]['name'];
$access = $row[$dbc]['access'];


$newarray = array();
$explode = explode(",",$access);
foreach($explode AS $value){
$valueb = explode("=",$value);
$newarray[] = $array[$valueb[0]];
}


$access = "<div style='float:left; padding:2px 5px; margin:2px;' class='shadow profilebg radious10'>".implode("</div><div style='float:left; padding:2px 5px; margin:2px;' class='shadow profilebg radious10'>",$newarray)."</div>";



$acountid = $row[$dbc]['account_id'];
$id = $row[$dbc]['id'];
$date = date("Y-m-d",  strtotime("+0 day", strtotime($row[$dbc]['date'])));
if($color==1){
?>
<div id="a<?php echo $id;?>" class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:12%;"><?php echo $acountid;?> <img class="tunnel" name="plugin/admin/pages/editterminal.php?width=400&id=<?php echo $id;?>" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/Edit2.png" width="11" height="14" /> <img onclick="remove_me('<?php echo $id;?>')" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/cross.png" width="16" height="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:28%;"><?php echo $name;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:45%;"><?php echo $access;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:9%;"><?php echo $date;?></div>
</div>
<?php
$color=2;
}else{
?>
<div id="a<?php echo $id;?>" class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:12%;"><?php echo $acountid;?> <img class="tunnel" name="plugin/admin/pages/editterminal.php?width=400&id=<?php echo $id;?>" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/Edit2.png" width="11" height="14" /> <img onclick="remove_me('<?php echo $id;?>')" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/cross.png" width="16" height="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:28%;"><?php echo $name;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:45%;"><?php echo $access;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:9%;"><?php echo $date;?></div>
</div>
<?php    
$color=1;    
}
}

if(!isset($name)){ echo '<div class="nWarning">No user found</div>';}
?>

</div>