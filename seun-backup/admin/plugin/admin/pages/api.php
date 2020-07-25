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
<div class="profilebg tunnel" name="plugin/admin/pages/newapi.php?width=400" style="border-left:solid 1px #CCCCCC; cursor:pointer; padding:5px; float: right;"><img src="images/small_icons/plus.png" width="16" height="16"/></div>
</div>
<div style="clear: both;"></div>

<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:12%;">Account ID</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:23%;">Key</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;">Type</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;">Domain</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;">Max Request</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:17%;">Return Url</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:8%;">Date</div>
</div>

<?php
$color = 1;
$row = $engine->db_query("SELECT * FROM api",array()); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$returnurl = $row[$dbc]['returnurl'];
$domain = $row[$dbc]['domain'];
$max_request = $row[$dbc]['max_request'];
$type = $row[$dbc]['type'];
$key = $row[$dbc]['apikey'];
$acountid = $row[$dbc]['acountid'];
$id = $row[$dbc]['id'];
$date = date("Y-m-d",  strtotime("+0 day", strtotime($row[$dbc]['date'])));
if($color==1){
?>
<div id="a<?php echo $id;?>" class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:12%;"><?php echo $acountid;?> <img class="tunnel" name="plugin/admin/pages/editapi.php?width=400&id=<?php echo $id;?>" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/Edit2.png" width="11" height="14" /> <img onclick="remove_me('<?php echo $id;?>')" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/cross.png" width="16" height="16" /> <img style="vertical-align: middle;" src="images/small_icons/pause.png" width="16" height="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:23%;"><?php echo $key;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;"><?php echo $type;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;"><?php echo $domain;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $max_request;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:17%;"><?php echo $returnurl;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:9%;"><?php echo $date;?></div>
</div>
<?php
$color=2;
}else{
?>
<div id="a<?php echo $id;?>" class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:12%;"><?php echo $acountid;?> <img class="tunnel" name="plugin/admin/pages/editapi.php?width=400&id=<?php echo $id;?>" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/Edit2.png" width="11" height="14" /> <img onclick="remove_me('<?php echo $id;?>')" style="cursor:pointer; vertical-align: middle;" src="images/small_icons/cross.png" width="16" height="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:23%;"><?php echo $key;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;"><?php echo $type;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;"><?php echo $domain;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $max_request;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:17%;"><?php echo $returnurl;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:9%;"><?php echo $date;?></div>
</div>
<?php    
$color=1;    
}
}

if(!isset($returnurl)){ echo '<div class="nWarning">No account found</div>';}
?>

</div>