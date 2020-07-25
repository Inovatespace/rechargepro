<div class="shadow topbg" style="width:99.1%; z-index:999; position:fixed; overflow: hidden; padding:5px; margin-bottom:10px;">
<div style="float: left; overflow:hidden;">

<img class="shadow" src="{AVATER_SMALL}" width="20" style="float:left; margin-right:5px; vertical-align: middle;" /> 

 
<div style="float:left;  margin-right:20px;">{LANMAIN_WELCOME} :: {LOGIN_NAME}</div>



<div style="float: left;  margin-right:2px;">
<img id="mail" src="{SITE_LOCATION}/images/alert/mail{NOTIFICATION_EMAIL}.png" height="16" style="float:left; vertical-align: middle;" /> 
<div class="radious10 shadow" id="mailnotificationcount" style="float:left; margin-left:-5px; width:18px; height:18px; background-color:red; border:solid 2px white; color:white; padding:1px 3px 3px 1px; font-size:10px;">{NOTIFICATION_EMAIL_COUNT}</div>
</div>

<div style="float: left;  margin-right:2px;">
<a href="parking_tools&p=memo"><img id="memo" src="{SITE_LOCATION}/images/alert/memo.png" height="16" style="float:left; vertical-align: middle;" /> 
<div class="radious10 shadow" id="memonotificationcount" style="float:left; margin-left:-5px; width:18px; height:18px; background-color:red; border:solid 2px white; color:white; padding:1px 3px 3px 1px; font-size:10px;">{NOTIFICATION_MEMO_COUNT}</div></a>
</div>

<div style="float: left; margin-right:5px;">
<a href="parking_tools&p=task"><img id="task" src="{SITE_LOCATION}/images/alert/setting.png" height="16" style="float:left; vertical-align: middle;" /> 
<div class="radious10 shadow" id="tasknotificationcount" style="float:left; margin-left:-5px; width:18px; height:18px; background-color:red; border:solid 2px white; color:white; padding:1px 3px 3px 1px; font-size:10px;">{NOTIFICATION_TASK_COUNT}</div></a>
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


<img id="languagemenu" onclick="$('#language').toggle()" style="float:left; vertical-align: middle;" src="{SITE_LOCATION}/images/language/{LANGUAGE}.png"  />


</div>


<div style="float: right;">{TOP_MENU}</div></div>

<script type="text/javascript">
jQuery(document).ready(function($){
    if("{NOTIFICATION}" == "1"){$("#mail").addClass("transparent");}
var lefpo = $("#languagemenu").position().left + 20;
$("#language").css("left",lefpo+"px");
});
</script>
<div id="language" style="display:none; z-index:999; position:fixed; top:35px;">{LANGUAGE_FILE}</div>





<div style="margin-top:35px; margin-left:auto; margin-right:auto; width:1200px;">

<div style="float:left; width:20%; overflow:hidden; position:relative;">
<div style="display:none; padding: 5px; margin-bottom:20px;"><img src="{SITE_LOGO}" width="201" /></div>

<!-- left menu -->
<div style="overflow: hidden; position:relative;">{SITE_MENU}</div>

<!-- left widget -->
<div style="overflow: hidden; position:relative;">{LEFT_WIDGET}</div>
</div>

<div style="float:right; width:77%;">

<div class="whitemenu shadow" style="padding: 5px; overflow:hidden;">{PLUGIN_MENU}</div>
<div class="shadow" id="{BODY_HOLDER}" style="background-color: white; overflow:hidden;">{BODY}</div>
</div>
<div style="clear:both;"></div>


</div>


<div class="whitemenu shadow" style="margin-top:20px; margin-bottom:0px;">{FOOTER}</div>
<div style="clear:both;"></div>

<div style="" class="nn-alert-container"><div class="nn-alert yellow">{LANMAIN_WELCOME} :: {LOGIN_NAME}</div></div>  