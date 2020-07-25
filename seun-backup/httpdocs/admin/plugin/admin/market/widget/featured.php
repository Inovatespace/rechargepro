<div style="padding:5px 10px; overflow: hidden;">
<div style="margin-bottom: 10px; font-size:14px; font-weight:bold;">Featured Widgets</div>
<?php
for($i=0;$i<4;$i++){
$i++;
if ($i == 4){$marginright = 0;}else{$marginright = 20;}	 	  
?>
<div style="width: 210px; overflow:hidden; float:left; margin-right:<?php echo $marginright;?>px;">
<div style="float: left; width:70px;"><img src="plugin/admin/123G7JK5U34.jpg" width="60"/></div>
<div style="float: left; width:140px;">
<div style="margin-bottom: 5px; color: #CD4902;">widget Name</div>
<div style="margin-bottom: 5px; overflow:hidden;">
<div style="float: left; margin-right:5px;">Rating:</div>
<div style="float: left; width:90px;"><?php echo $rate->showrate("widget$i",'star',"23","7","0",$i,"W");?></div>
</div>
<div style="position: relative;">
<div id="itloading<?php echo $i;?>" style="display:none; position: absolute; padding-left:40px; padding-top:5px;"><img src="plugin/admin/market/images/smallloading.gif" width="16" height="16" /></div>
<input type="button"  value="Plugin" class="profilebg shadow radious10" style="margin:5px; border:none; padding:0px 35px;" /></div>
</div>
</div>
        
  <?php
        }
?>
</div>

