<link href="{SITE_LOCATION}/css/medium.css" rel="stylesheet" type="text/css" />


<div class="shadow topbg" style="overflow: hidden; padding:5px; margin-bottom:10px;">
<div style="float: left; margin-right:10px;"><img class="shadow radious10" src="{AVATER_SMALL}" width="20" style="vertical-align: middle;" /> Welcome :: {LOGIN_NAME}</div>
<div style="float: right;">{TOP_MENU}</div>






<div style="float: left; margin-top:2px; margin-right:5px;">
<a href="parking_tools&p=task"><img id="task" src="{SITE_LOCATION}/images/alert/setting.png" height="16" style="float:left; vertical-align: middle;" /> 
<div class="radious10 shadow" id="tasknotificationcount" style="float:left; margin-left:-5px; margin-top:-5px; width:18px; height:18px; background-color:red; border:solid 2px white; color:white; padding:1px 3px 3px 1px; font-size:10px;">{NOTIFICATION_TASK_COUNT}</div></a>
</div>
</div>


<script type="text/javascript">
jQuery(document).ready(function($){
    if("{NOTIFICATION_EMAIL}" == "1"){$("#mail").addClass("transparent");}
    if("{NOTIFICATION_EMAIL_COUNT}" == "0"){$("#mailnotificationcount").hide();}
    if("{NOTIFICATION_MEMO}" == "1"){$("#memo").addClass("transparent");}
    if("{NOTIFICATION_MEMO_COUNT}" == "0"){$("#memonotificationcount").hide();}
    if("{NOTIFICATION_TASK}" == "1"){$("#task").addClass("transparent");}
    if("{NOTIFICATION_TASK_COUNT}" == "0"){$("#tasknotificationcount").hide();}
//var lefpo = $("#languagemenu").position().left + 20;
//$("#language").css("left",lefpo+"px");
});
</script>



<div style="margin-left:auto; margin-right:auto; width:990px;">

<div class="shadow activemenu" style="margin-bottom:8px; overflow: hidden; position:relative;">{SITE_MENU}</div>


<div class="whitemenu shadow" style="padding: 5px; overflow:hidden;">{PLUGIN_MENU}</div>
<div class="shadow" id="{BODY_HOLDER}" style="background-color: white; overflow:hidden;">{BODY}</div>
</div>




<div class="whitemenu shadow" style="margin-top:20px; margin-bottom:0px;">{FOOTER}</div>
<div style="clear:both;"></div>