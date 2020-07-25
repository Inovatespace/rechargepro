<style type="text/css">
#sitenameholder a:hover{color:white;}
</style>
<link rel="stylesheet" href="{SITE_LOCATION}/css/font-awesome/css/fontawesome-all.min.css"/>

<script src="theme/ubuntu/js/jquery.cookie.js"></script>

<div class="shadow topbg" id="sitenameholder" style="position: fixed; width:100%; z-index:999; overflow: hidden; padding:3px 5px;">
<div  style="float: left; padding-left:15px;"><img id="closeholder" style="display:none; margin-top:5px;" src="{SITE_LOCATION}/images/close.png"  height="11" /> </div>
<div style="float: right; oerflow:hidden;"><a>


<div style="float:left; margin-top:5px; margin-right:20px;">{LOGIN_NAME}</div>


<div style="float: left; margin-top:5px; margin-right:2px;">
<a href="#"><img id="mail" src="{SITE_LOCATION}/images/alert/mail{NOTIFICATION_EMAIL}.png" height="16" style="float:left; vertical-align: middle;" /> 
<div class="radious10 shadow" id="mailnotificationcount" style="float:left; margin-left:-5px; margin-top:-5px; width:18px; height:18px; background-color:red; border:solid 2px white; color:white; padding:1px 3px 3px 1px; font-size:10px;">{NOTIFICATION_EMAIL_COUNT}</div></a>
</div>

<div style="float: left; margin-top:5px; margin-right:2px;">
<a href="#"><img id="memo" src="{SITE_LOCATION}/images/alert/memo.png" height="16" style="float:left; vertical-align: middle;" /> 
<div class="radious10 shadow" id="memonotificationcount" style="float:left; margin-left:-5px; margin-top:-5px; width:18px; height:18px; background-color:red; border:solid 2px white; color:white; padding:1px 3px 3px 1px; font-size:10px;">{NOTIFICATION_MEMO_COUNT}</div></a>
</div>

<div style="float: left; margin-top:5px; margin-right:5px;">
<a href="#"><img id="task" src="{SITE_LOCATION}/images/alert/setting.png" height="16" style="float:left; vertical-align: middle;" /> 
<div class="radious10 shadow" id="tasknotificationcount" style="float:left; margin-left:-5px; margin-top:-5px; width:18px; height:18px; background-color:red; border:solid 2px white; color:white; padding:1px 3px 3px 1px; font-size:10px;">{NOTIFICATION_TASK_COUNT}</div></a>
</div>

<img id="languagemenu" onclick="$('#language').toggle()" style="float:left; margin-top:5px; vertical-align: middle;" src="{SITE_LOCATION}/images/language/{LANGUAGE}.png"  /></a></div>


<div style="float: right; padding-top:1px;">{TOP_MENU}</div>

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

<div id="language"  class="specialhide" style="display:none; z-index:999; position:fixed; top:35px; right:10px;">{LANGUAGE_FILE}</div>





<div style="overflow:hidden;">
<div class="sidebar-background"><div class="primary-sidebar-background"></div></div>
<div style="position: fixed; top:35px; z-index:999; width:4.5%; overflow:hidden;">
<!-- left menu -->
<div  style="overflow: hidden; position:relative;">{SITE_MENU}</div>
<!-- left widget -->

</div>


<script type="text/javascript">
function showme(Id){
var width = $("#left_"+Id).width();
var height = $("#left_"+Id).height();
var pos = $("#left_"+Id).offset();
var content = $("#menuicon2_"+Id).html();
//console.log(pos.left)
var postop = (pos.top)+2;
$("body").append("<div class='overredmenu touglemenu' style='position:absolute; top:"+postop+"px; left:"+width+"px; height:"+height+"px;'>"+content+"<div>"); 
}

function hideme(){
  $(".touglemenu").hide();  
}

jQuery(document).ready(function($){
    
var docwidth = $(document).width()-240;
//console.log(docwidth+"-"+$(document).width());
//$("#rightright").css("width",docwidth+"px");





$(".menu_icon1").each(function( index ) {
    var id = $(this).attr("id");
    var newid = id.split(/_(.*)/);
   $("#left_"+newid[1]).attr("onmouseover","showme('"+newid[1]+"')");
   $("#left_"+newid[1]).attr("onmouseout","hideme()");
    
    //console.log(content);
})


$(".sitemenu_ancor").click(function(event){
   $.cookie('ubuntuscreen', 'no', { expires: 7, path: '/' }); 
   event.preventDefault(); 
   var url = $(this).attr('href');
   window.location = url;
});

$(".inner_submenu_tree").click(function(){
   $.cookie('ubuntuscreen', 'yes', { expires: 7, path: '/' }); 
      event.preventDefault(); 
   var url = $(this).attr('href');
   window.location = url;
});

$("#closeholder").click(function(){
   $.cookie('ubuntuscreen', 'no', { expires: 7, path: '/' }); 
   window.location.reload();
});

});
</script>
<div style="margin:0px; margin-top:25px; padding:0px; float:right; width:94.5%;">
<div id="pluginid" style="padding:10px; overflow:hidden;">
<div>{PLUGIN_MENU}</div>
</div>



<script type="text/javascript">
jQuery(document).ready(function($){
 if($.cookie('ubuntuscreen') == "yes"){
    $("#{BODY_HOLDER}").show();
    $("#pluginid").hide();
    $("#closeholder").show();
    $(".clockholder").hide();
 }
 
  if("{PLUGIN_KEY_MENU_COUNT}" < 2){
    $("#{BODY_HOLDER}").show();
    $("#pluginid").hide();
    $("#closeholder").show();
    $(".clockholder").hide();
 }
 
    


$(".sub_{PLUGIN_KEY}").each(function(){
var id = $(this).attr("id");

var mysplit = id.split("_");   
var klas = "{PLUGIN_KEY}"+mysplit[1];
var img = $('.img_'+klas);
var default_url = "images/Clear Green.png";
img.css("height","49px");
img.error(function(){
img.attr('src', default_url) 
});
});    


var i = 0;
//console.log($(".sub_{PLUGIN_KEY}").length);
$(".sub_{PLUGIN_KEY}").each(function(){
 var id = $(this).attr("id"); 

 i++;  
 if(i > 5 || i == 9){
    var width = $("#"+id).width();
    var height = $("#"+id).height();
    $.cookie('icon'+i, width, { expires: 7, path: '/' }); 
    $("#"+id).hide();
     setposition(i,width,height,$("#"+id));
  }
  
   if(i > 9 || i == 12){
         var width = $("#"+id).width();
    var height = $("#"+id).height();
    $.cookie('icon'+i, width, { expires: 7, path: '/' });
    $("#"+id).hide();
     setposition(i,width,height,$("#"+id));
    }
    
       if(i > 12 || i == 14){
         var width = $("#"+id).width();
    var height = $("#"+id).height();
    $.cookie('icon'+i, width, { expires: 7, path: '/' });
    $("#"+id).hide();
     setposition(i,width,height,$("#"+id));
    }
 });    
   


  
    })
    
    function setposition(num,width,height,Id){
        
        Id.css("position","absolute");
        var newwidth = 0;
        //$.cookie('icon'+num);
        if(num == 6){ Id.show(); Id.css("top","140px"); Id.css("left","95px");}
        if(num == 7){ Id.show(); newwidth = parseInt($.cookie('icon'+6))+95+20; Id.css("top","140px"); Id.css("left",newwidth+"px");}
        if(num == 8){ Id.show(); newwidth = parseInt($.cookie('icon'+6))+parseInt($.cookie('icon'+7))+95+40; Id.css("top","140px"); Id.css("left",newwidth+"px");}
        if(num == 9){ Id.show(); newwidth = parseInt($.cookie('icon'+6))+parseInt($.cookie('icon'+7))+parseInt($.cookie('icon'+8))+95+60; Id.css("top","140px"); Id.css("left",newwidth+"px");}
        
        
        
        if(num == 10){ Id.show(); Id.css("top","240px"); Id.css("left","95px");}
        if(num == 11){ Id.show(); newwidth = parseInt($.cookie('icon'+10))+95+20; Id.css("top","240px"); Id.css("left",newwidth+"px");}
        if(num == 12){ Id.show(); newwidth = parseInt($.cookie('icon'+10))+parseInt($.cookie('icon'+11))+95+40; Id.css("top","240px"); Id.css("left",newwidth+"px");}
        
        if(num == 13){ Id.show(); Id.css("top","340px"); Id.css("left","95px");}
        if(num == 14){ Id.show(); newwidth = parseInt($.cookie('icon'+13))+95+20; Id.css("top","340px"); Id.css("left",newwidth+"px");}
        
        if(num == 15){ Id.show(); Id.css("top","440px"); Id.css("left","95px");}
        
    }
</script>


	<link rel="stylesheet" href="{SITE_LOCATION}/js/flipclock/flipclock.css"/>
		<script src="{SITE_LOCATION}/js/flipclock/flipclock.js"></script>
        <div class="clockholder" style="position:absolute; right:20px; top:250px;">	
		<div class="clock transparent" style="margin: 2em;"></div>
        </div>	
		<script type="text/javascript">
			var clock;
			$(document).ready(function() {
				clock = $('.clock').FlipClock({
					clockFace: 'TwelveHourClock'
				});
                
                $("#content_body").css("background-color","white");  
                    if("{BREAD_CRUMB}" == "newbiller" || "{BREAD_CRUMB}" == "dashboard"){
                       $("#content_body").css("background-color","");   
                    }else{
                      $("#content_body").addClass("shadow");  
                    }    
                    
                                          
			});
		</script>



<div class="" id="{BODY_HOLDER}" style="display:none; margin-top:25px; margin-right:20px; overflow: hidden;">{BODY}</div>





</div>

</div>

<div style="display:none;" class="nn-alert-container"><div class="nn-alert yellow">{LANMAIN_WELCOME} :: {LOGIN_NAME}</div></div> 


<div  style="margin-top:20px; margin-bottom:0px;">{FOOTER}</div>
<div style="clear:both;"></div>

