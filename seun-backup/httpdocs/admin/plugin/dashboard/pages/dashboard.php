<?php
$widgetarray = $engine->show_widget();
$userstate = $engine->config("user_widget_state_change");
?>
 



<script>
function changestate(Id,type){
    

				//jConfirm('Do you want to Delete this User?', 'Confirmation Dialog', 
				  //  function(r) {
				//	if(r==true)
				//	{
                       var dataString = 'type='+type+'&id='+Id;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/dashboard/pages/pro/changestate.php",
                        data: dataString,
                        cache: false,
                        success: function(){
location.reload();
}
                        });
     
				//	}
                        
				//	});    
    
}
  
  
  
  
$(document).ready(function() {
   //$(".draggables").draggable({ containment: "#containment-wrapper", scroll: false,  handle: ".handle", cursor: "move" });
});

  function save_myposition(left,top,Id){
                           var dataString = 'left='+left+'&top='+top+"&id="+Id;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/dashboard/pages/pro/updateposition.php",
                        data: dataString,
                        cache: false,
                        success: function(){}
                        });
  }
  
  
  function dragme(Id){
    $(".draggables").css("zIndex","2");
    $("#"+Id+"ts").css("zIndex","999");
    $("#"+Id+"ts").draggable({ containment: "#containment-wrapper", scroll: false,  handle: ".handle", cursor: "move", stop: function(event, ui) {
        
        var Startpos = $(this).position();
        save_myposition(Startpos.left,Startpos.top,Id);
      }  });
  };
  </script>




<?php if($userstate){?>
<div class="whitemenu" style="border-left:solid 1px #eeeeee; float: right; height: <?php echo $engine->config("dashboard_size");?>; width:10%;">
<?php
for($icount=0; $icount<count($widgetarray); $icount++){
$status = '<img style="float: right; cursor:pointer;" onclick="changestate(\''.$widgetarray[$icount]['id'].'\',\'on\')" src="plugin/dashboard/images/off.png" width="17" height="60" />';
if($widgetarray[$icount]['status'] == 1){ $status = '<img style="float: right; cursor:pointer;" onclick="changestate(\''.$widgetarray[$icount]['id'].'\',\'off\')" src="plugin/dashboard/images/on.png" width="17" height="60" />';} 
  
  $parameter = $engine->readparameter($widgetarray[$icount]['widgetkey']); 
?>
<div class="submenu" id="<?php echo $widgetarray[$icount]['id'];?>tso" style="overflow:hidden; padding:5px; border:solid 1px #EEEEEE; margin:5px;">
<div style="float: left; width:62%; overflow:hidden;">
<div  style="height:20px; overflow:hidden; font-size:11px; line-height:10px;"><?php echo ucwords(strtolower($parameter["title"]));?></div>
<div  style="height:40px; border: solid 1px #EEEEEE; overflow: hidden;"><img src="widget/<?php echo $widgetarray[$icount]['widgetkey'];?>/thumb.png" width="45" height="40" /></div>
</div>
<?php echo $status;?>
</div>
<?php }	?>
</div>
<?php }?>

<div id="containment-wrapper" style="float:left; position:relative; height: <?php echo $engine->config("dashboard_size");?>;  overflow: hidden; <?php if($userstate){echo "width:89.5%";}else{echo "width:100%";}?>">
<script type="text/javascript">
var thejson = {}
</script>
<?php
for($icount=0; $icount<count($widgetarray); $icount++){
  if($widgetarray[$icount]['status'] == 1 && $widgetarray[$icount]['position'] == 0){
    $empty = "";
    $parameter = $engine->readparameter($widgetarray[$icount]['widgetkey']);
    
    $widgetcalss = 'dashboardwidget_title handle';
    $widgethandle = '';
    if(isset($parameter['header_class'])){$widgetcalss = $parameter['header_class']; $widgethandle = 'handle';}  
?>
<div class="draggables <?php echo $widgetarray[$icount]['widgetkey'];?>" id="<?php echo $widgetarray[$icount]['id'];?>ts" onclick="dragme('<?php echo $widgetarray[$icount]['id'];?>')" style="overflow: hidden; width:<?php echo $parameter["width"];?>; top:<?php echo $widgetarray[$icount]['widgettop'];?>px; left:<?php echo $widgetarray[$icount]['widgetleft'];?>px;">
<div class="<?php echo $widgetcalss;?>" style="padding:5px; overflow: hidden;"><?php echo $parameter["title"];?></div>
<div class="<?php echo $widgethandle;?>" id="inner<?php echo $widgetarray[$icount]['id'];?>" style="overflow: hidden; <?php if(isset($parameter["height"])){echo "height:".$parameter["height"];}?>;">
<?php //	include "widget/".$widgetarray[$icount]['widgetkey']."/index.php";?>
<img src="plugin/admin/images/loading.gif"  />

<script type="text/javascript">
thejson["<?php echo	"widget/".$widgetarray[$icount]['widgetkey']."/index.php";?>"] = "<?php echo $widgetarray[$icount]['id'];?>";
</script>
</div>
</div>
<?php   
}
}

if(!isset($empty)){
    echo '<div class="nInformation" style="margin:90px 20px; padding:50px 20px; font-size:25px;">No Widget Installed</div>';
}else{?>
    
<script type="text/javascript">
function startprocess(Id){
    var cout = 0;
for(var i in thejson)
{       cout++;
     var link = i;
     var id = thejson[i];
     if(cout == Id){
      callindex(link,id,cout)  
     }
     
     if(cout > 20){
        break;
     }
}
}

  function callindex(link,id,cout){
                           var dataString = 'link='+encodeURIComponent(link);                            
$("#inner"+id).html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/dashboard/pages/calllink.php",
                        data: dataString,
                        cache: false,
                        success: function(html){
                       $("#inner"+id).html(html); 
                       cout = cout+1;
                       startprocess(cout);   
                        }
                        });
  }
//console.log(thejson[0]);

$(document).ready(function() {
startprocess(1);
});
</script>



<?php }	?>






</div>