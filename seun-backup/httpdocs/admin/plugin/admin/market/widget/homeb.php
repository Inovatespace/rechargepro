<?php
include_once ('../../../../plugin/admin/rating/rate.php');


for($id=0;$id<11;$id++){
$author=htmlentities("Seun Makinde WIlliams"); 
$website=htmlentities("http://www.seuntech.com"); 
$widgetname=htmlentities("widgetname"); 
$about=htmlentities("about"); 
$logo=htmlentities("logo"); 
$language=htmlentities("english");	
$users=htmlentities("300"); 
$yes=htmlentities(40);	
$no=htmlentities(20);   
?>
<div style="overflow:hidden; padding:5px; margin-bottom:10px;">
<div style="float: left; width:20%;"><img src="plugin/admin/123G7JK5U34.jpg" width="50"/></div>
<div style="float: left; width:60%;">
<div style="color: #CD4902; font-weight:bold; margin-bottom:5px;"><?php echo $widgetname;?></div>
<div style="margin-bottom:5px;"><?php echo $about;?></div>
</div>
<div style="float: left; width:20%; position:relative;">
<?php echo $rate->showrate('widget'.$id,'star',$yes,$no,'1',$id,'W');?>
<div id="itloading<?php echo $id;?>" style="display:none; position: absolute; padding-left:40px; padding-top:5px;"><img src="smallloading.gif" width="16" height="16" /></div>
<input type="button"  value="Plugin" class="profilebg shadow radious10" style="margin-top:5px; border:none; padding:0px 35px;" />
</div>
</div>
        
  <?php
        }
?>