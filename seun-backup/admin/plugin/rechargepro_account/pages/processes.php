<?php
$engine = new engine();
require "plugin/parking_core/parking_core.php";

 ?>   
<div class="profilebg" style="border:solid 1px #EEEEEE; position:relative; z-index:43;;">
   <link href="plugin/parking_hr/pages/workflow/ruler.css" rel="stylesheet" type="text/css" />
   <script src="plugin/parking_hr/pages/workflow/jquery.ruler.js"></script>
   <script type="text/javascript">
$(function() {
  $('#rul').ruler({
    vRuleSize: 18,
    hRuleSize: 18,
    showCrosshair : false,
    showMousePos: false
  });    
});
</script>

<?php
	function wt($tx="",$ti="",$color ="red",$tp="200",$lenght=""){
	   return '<div id="sp0" class="shadow radious3 '.$color.'arrow_box" style="text-align:center; padding:5px; margin:5px; margin-left: 20px; color:white; margin-top: '.$tp.'px; position:relative; z-index:4;">'.$tx.'<div style="color: #626569; color: rgba(0, 0, 0, 0.4); text-shadow: 0 1px 1px rgba(255, 255, 255, 0.5);">'.$ti.'</div></div><div class="'.$color.'menu shadow '.$color.'menu_arrow" style="margin-top:-5px; position:relative; z-index:3; height: '.$lenght.'px; width:3%; margin-left:48.5%;"></div>';
	}
?>



<style type="text/css">
.arrow_box {
	position: relative;
	background: #88b7d5;
	border: 4px solid #c2e1f5;
    text-align:center;
    font-weight:bold;
    
      z-index:2;
        text-transform: uppercase;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
  color: #ddf8c6;
}
.arrow_box:after, .arrow_box:before {
	top: 100%;
	left: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
}

.arrow_box:after {
	border-color: rgba(136, 183, 213, 0);
	border-top-color: #88b7d5;
	border-width: 30px;
	margin-left: -30px;
}
.arrow_box:before {
	border-color: rgba(194, 225, 245, 0);
	border-top-color: #c2e1f5;
	border-width: 36px;
	margin-left: -36px;
}


.redarrow_box {
	position: relative;
	background: #CA5E58;
}
.redarrow_box:after {
	right: 100%;
	top: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(202, 94, 88, 0);
	border-right-color: #CA5E58;
	border-width: 10px;
	margin-top: -10px;
}
.redmenu_arrow:after {
	top: 100%;
	left: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(136, 183, 213, 0);
	border-top-color: #AB4546;
	border-width: 7px;
	margin-left: -7px;
}


.greenarrow_box {
	position: relative;
	background: #73B573;
}
.greenarrow_box:after {
	right: 100%;
	top: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(202, 94, 88, 0);
	border-right-color: #73B573;
	border-width: 10px;
	margin-top: -10px;
}
.greenmenu_arrow:after {
	top: 100%;
	left: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(136, 183, 213, 0);
	border-top-color: #468C46;
	border-width: 7px;
	margin-left: -7px;
}

.middlearrow_box {
	position: relative;
	background: #F9A937;
}
.middlearrow_box:after {
	right: 100%;
	top: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(202, 94, 88, 0);
	border-right-color: #F9A937;
	border-width: 10px;
	margin-top: -10px;
}
.middlemenu_arrow:after {
	top: 100%;
	left: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(136, 183, 213, 0);
	border-top-color: #CF6D0C;
	border-width: 7px;
	margin-left: -7px;
}


.bararrow_box {
	position: relative;
	background: #747474;
}
.bararrow_box:after {
	right: 100%;
	top: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(202, 94, 88, 0);
	border-right-color: #747474;
	border-width: 10px;
	margin-top: -10px;
}
.barmenu_arrow:after {
	top: 100%;
	left: 50%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
	border-color: rgba(136, 183, 213, 0);
	border-top-color: #000000;
	border-width: 7px;
	margin-left: -7px;
}

.ancor_class{width: 155px; height:100%; margin-right:20px; float:left; overflow:hidden; position:relative;}
</style>

<div id="rul" style="position:relative; height:2750px; width:100%; overflow:hidden;">

<style type="text/css">
.radious20{  -webkit-border-radius:25px;  -moz-border-radius:25px;  border-radius:25px; -khtml-border-radius:25px;}
</style>
<div style="float: left; width:10%;   height:100%; position:relative;">
<?php
$ii = 130;
for($i = 7; $i <20; $i++){
    $time = date("gA",  strtotime("+0 day", strtotime(date("Y-m-d $i:i:s"))));
    ?>
<div class="greenmenu radious20 shadow" style="top:<?php echo $ii;?>px; text-align:center; padding-top:15px; left:25%; border:solid 3px white; width: 50px; height:50px; position:absolute;"><?php echo $time;?></div>
<?php
   $ii = $ii + 200;
}	
?>

<div class="greenmenu shadow" style="height: 100%; width:10%; margin-left:40%;"></div>

</div>

<div style="float: left; width:90%; height: 100%;">
<!-- operarion -->
<div class="ancor_class">
<div class="profilebg shadow" style="z-index:1; position:absolute; height: 100%; width:10%; margin-left:45%;"></div>
<div class="greenmenu shadow arrow_box" style="padding: 20px 10px; margin-top:10px;">Operations</div>
<?php	echo wt("Attendance","7:00 AM","red",60,60);?>
<?php	echo wt("Morning Drill","7:30 AM","green",9,10);?>
<?php	echo wt("Pickup Keys and Clamps","7:50 AM","middle",9,5);?>
<?php	echo wt("Departure for field","8:10 AM","middle",9,9);?>
</div>

<!-- Admin Assitant -->
<div class="ancor_class">
<div class="profilebg shadow" style="z-index:1; position:absolute; height: 100%; width:10%; margin-left:45%;"></div>
<div class="greenmenu shadow arrow_box" style="padding: 20px 10px; margin-top:10px;">Admin Assistant</div>
<?php	echo wt("Washing of vehicle","7:00 AM","red",60,60);?>
<?php	echo wt("Realease of vehicle for operations","7:30 AM","green",9,10);?>
<?php	echo wt("Go on patrol twice(2X) a week, for driver's behaviour","12:00 PM","middle",830,150);?>
<?php	echo wt("Collect fuel money for shuzia day operations","4:00 PM","bar",580,10);?>
<?php	echo wt("Buying of fuel","5:30 PM","middle",230,50);?>
</div>


<!-- Front Dest -->
<div class="ancor_class">
<div class="profilebg shadow" style="z-index:1; position:absolute; height: 100%; width:10%; margin-left:45%;"></div>
<div class="greenmenu shadow arrow_box" style="padding: 20px 10px; margin-top:10px;">Front Desk</div>
<?php	echo wt("Open Office for cleaning and Attendance","7:00 AM","red",60,30);?>
<?php	echo wt("Print MD's Report","7:30 AM","green",10,30);?>
<?php	echo wt("Registration of new staff and taking of bio data","8:00 AM","bar",10,200);?>
</div>


<!-- IT -->
<div class="ancor_class">
<div class="profilebg shadow" style="z-index:1; position:absolute; height: 100%; width:10%; margin-left:45%;"></div>
<div class="greenmenu shadow arrow_box" style="padding: 20px 10px; margin-top:10px;">IT</div>
<?php	echo wt("Give DCT to Customer Care","7:00 AM","red",60,60);?>
<?php	echo wt("Collection and Charging of DCT","5:30 PM","green",2000,50);?>
</div>



<!-- Customer Care -->
<div class="ancor_class">
<div class="profilebg shadow" style="z-index:1; position:absolute; height: 100%; width:10%; margin-left:45%;"></div>
<div class="greenmenu shadow arrow_box" style="padding: 20px 10px; margin-top:10px;">Customer Care</div>
<?php	echo wt("Give out keys and DCT","7:50 PM","red",230,10);?>
<?php	echo wt("Collection of Keys and DCT / Account Settlement","5:30 PM","green",1860,50);?>
</div>

<!-- Accountant -->
<div class="ancor_class">
<div class="profilebg shadow" style="z-index:1; position:absolute; height: 100%; width:10%; margin-left:45%;"></div>
<div class="greenmenu shadow arrow_box" style="padding: 20px 10px; margin-top:10px;">Accountant</div>
<?php	echo wt("Disbusment of cash for fuel","4:00 PM","red",1880,10);?>
<?php	echo wt("Receiving of daily revenue","5:30 PM","green",220,50);?>
</div>


</div>

</div>
   
    
    </div>