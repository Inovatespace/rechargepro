<style type="text/css">
#sitenameholder a:hover{color:white;}
</style>

<link rel="stylesheet" href="{SITE_LOCATION}/css/font-awesome/css/fontawesome-all.min.css"/>

<div id="sitenameholder" style="background-image: url({SITE_LOCATION}/images/bg.png); background-color: #cccccc; background-size:100% 100%; width:100%; z-index:99; overflow: hidden; position:fixed; padding:10px 5px;">
<div style="float: left; padding-left:15%; font-size:250%; font-weight:bold; color: transparent;
background: #666666;
-webkit-background-clip: text;
-moz-background-clip: text;
background-clip: text;
text-shadow: 0px 3px 3px rgba(255,255,255,0.5);">RECHARGE PRO</div>
<!-- <div class="whitelink" style="float: left; padding: 10px 15px;; font-size: 18px; line-height: 20px; text-transform: uppercase;"><a href="index">{SITE_NAME}</a></div> -->
<div style="float: right; overflow:hidden;">

<img class="radious10" src="{AVATER_SMALL}" width="30" style="float:left; margin-top:5px; vertical-align: middle; margin-right:5px; padding: 2px;
    border: 2px solid #dbdfe6;" /> 

<div style="float:left; margin-top:10px; margin-right:20px;">{LOGIN_NAME}</div>
<a>




<img id="languagemenu" onclick="$('#language').toggle()" style="float:left; margin-top:8px; vertical-align: middle;" src="{SITE_LOCATION}/images/language/{LANGUAGE}.png"  />

</a></div>


<div style="float: right; padding-top:10px;">{TOP_MENU}</div>
</div>



<div style="margin:0px; padding:0px; top:50px; right:0px; position:fixed; width:85.5%; z-index: 99;">
<div class="whitemenu shadow" style="margin-bottom:4px; padding:10px; overflow:hidden;">
<div style="color: #888; font-size:26px; margin-bottom:10px;"><span class="{PLUGIN_IMAGE_CLASS}"></span> {PLUGIN_NAME} <span style="margin-top:-5px; font-size:12px;">&raquo; {PLUGIN_DETAILS}</span></div>
<div>{PLUGIN_MENU}</div>
</div>
</div>


<script type="text/javascript">
jQuery(document).ready(function($){
    if("{NOTIFICATION_EMAIL}" == "1"){$("#mail").addClass("transparent");}
    if("{NOTIFICATION_EMAIL_COUNT}" == "0"){$("#mailnotificationcount").hide();}
    if("{NOTIFICATION_TASK}" == "1"){$("#task").addClass("transparent");}
    if("{NOTIFICATION_TASK_COUNT}" == "0"){$("#tasknotificationcount").hide();}
});
</script>


<div id="language"  class="specialhide" style="display:none; z-index:102; position:fixed; top:35px; right:10px;">{LANGUAGE_FILE}</div>

<div class="sidebar-background" style="left:14.4%; width:0.1%; height:55px; border-right:none; overflow: hidden; z-index:101;"></div>

<div class="sidebar-background" style="overflow: hidden; z-index:100;">



<div style="height: 120px; overflow:hidden; text-align: center; margin-top:30px; ">
<a><img src="{SITE_LOCATION}/images/logo2.png" style="width:100px; margin-left:10px;;" /></a>
</div>


<div style="margin-left: 7%; margin-top:150px; background-color:white; border: solid #A0A59B 1px;" class="radious10 primary-sidebar-background">
<div style="padding:5% 0px 0px 5%; overflow: hidden;">{SITE_MENU}</div>
</div>

</div>



<script type="text/javascript">

jQuery(document).ready(function($){
    
var docwidth = $(document).width()-240;
//console.log(docwidth+"-"+$(document).width());
//$("#rightright").css("width",docwidth+"px");





});
</script>


<script type="text/javascript">
function heading(){
    if ($("#sub_{BREAD_CRUMB}").length)
{
var bread = $("#sub_{BREAD_CRUMB}").html();
}
else
{
var bread = $("#sub_index").html();
}

if(bread){
var string = bread.split('</span>')[1];
string = string.split('</a>')[0];
$("#headertitle").html(string);
}else{
$("#headertitle").html("{BREAD_CRUMB}");
}
}
//jQuery(document).ready(function($){
//    if(("{BREAD_CRUMB}" != "shuzia_setting")){//("{BREAD_CRUMB}" != "dashboard") && 
//heading();
//}else{
//    $("#heading").hide();
//}
//})
</script>


<div style="background-color:#2B2B8F;  margin:0px 0.5%;  margin-top:140px; padding:0px; float:right; width:84.5%;  z-index: 8; position: relative;">
<header id="heading" style="display: block; box-sizing: border-box; color: #FFF;  background: #4c4f53; -webkit-box-shadow: inset 0 -2px 0 rgba(255,255,255,.05); line-height: normal;  border-bottom: 1px solid #C2C2C2; background: #4c4f53; font-family: 'Open Sans',Arial,Helvetica,Sans-Serif; font-size: 13px; padding:5px; text-transform: capitalize;">
<span class="fa fa-bar-chart-o">  </span> <span id="headertitle"></span></header>
<div id="{BODY_HOLDER}" style="overflow:hidden;">{BODY}</div>
</div>






<div  style="margin-top:20px; margin-bottom:0px;">{FOOTER}</div>
<div style="clear:both;"></div>

<div style="" class="nn-alert-container"></div><!-- <div class="nn-alert yellow">{LANMAIN_WELCOME} :: {LOGIN_NAME}</div> -->