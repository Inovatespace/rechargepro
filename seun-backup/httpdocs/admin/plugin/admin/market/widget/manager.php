<?php	include_once ('plugin/admin/market/widget/featured.php');?>

<div style="overflow:hidden;">

<div style="padding: 20px; background-color:white;">
<style type="text/css">
<!--
	.widgetmenu :hover{
	   border:solid black 1px; cursor:pointer;
	}
-->
</style>
<div class="adminheader" style="margin:0px; height:21px; overflow:hidden; padding:2px; border:solid #BCC1CA 1px; border-top:none;">
<div style="float:left;  margin-right:40px; padding-left:20px;">Purchased Widget</div>
<div style="float:left;  width:1px; border:solid transparent 1px; margin-right:40px; border-right:solid #BCC1CA 1px;">&nbsp;</div>
</div>

                                 
<div style=" margin-top:5px;">&nbsp;</div>


<script type="text/javascript">
$(document).ready( function() {
$(".stats").click( function() 
{
var element = $(this);
var Id = element.attr("id");

if($('#checkbox'+Id).is(':checked')){
    $('input[id=checkbox'+Id+']').attr('checked', false); 
    $("#star"+Id).css('background-position','3px -237px');
}else{
$('input[id=checkbox'+Id+']').attr('checked', true); 
$("#star"+Id).css('background-position','3px -261px');
};  
    

});
})
</script>
<style type="text/css">
<!--
	.unmenu{
        background-color: #C3C9CC;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#D6DBDD), to(#C3C9CC));
        background-image: -webkit-linear-gradient(top, #D6DBDD, #C3C9CC);
        background-image: -moz-linear-gradient(top, #D6DBDD, #C3C9CCc);
        background-image: -ms-linear-gradient(top, #D6DBDD, #C3C9CC);
        background-image: -o-linear-gradient(top, #D6DBDD, #C3C9CC);
        background-image: linear-gradient(top, #D6DBDD, #C3C9CC);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#D6DBDD', EndColorStr='#C3C9CC');
        -ms-filter: "progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr='#D6DBDD', endColorstr='#C3C9CC')"; 
        float: left; padding:2px; border:solid 1px #B8B8B8; 
}
.unmenu a{
    color:black;
    text-decoration:none;
}

.barmenu{
border-right:none; padding:2px; height:20px;
}	
.stats:hover {
        background: #F3F3F3; color:#F9C93A; cursor:pointer;
}
-->
</style>
<div style="overflow: hidden; position:relative; z-index:13;">
<div class="unmenu barmenu" style="width:4%; border-top-left-radius:5px; -moz-border-radius-topleft:5px; -webkit-border-top-left-radius:5px;">SN</div>
<div class="unmenu barmenu" style="width:22%;">Name</div>
<div class="unmenu barmenu" style="width:20%;">Dependency</div>
<div class="unmenu barmenu" style="width:25%;">Description</div>
<div class="unmenu barmenu" style="width:20%;">Date Purchased</div>
<div class="unmenu barmenu" style="width:5%; border:solid 1px #B8B8B8; border-top-right-radius:5px; -moz-border-radius-topright:5px; -webkit-border-top-right-radius:5px;">D</div>
</div>
<?php
    function curlit($url,$what=""){
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $what);
       // Set a referer
       curl_setopt($ch, CURLOPT_REFERER, "http://safeparkingltd.com");
       // User agent
       curl_setopt($ch, CURLOPT_USERAGENT, "Firefox (WindowsXP) – Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
       // Include header in result? (0 = yes, 1 = no)
       curl_setopt($ch, CURLOPT_HEADER, 0);
       // Should cURL return or print out the data? (true = return, false = print)
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       // Timeout in seconds
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       // Download the given URL, and return output
       $output = curl_exec($ch);
       // Close the cURL resource, and free system resources
       curl_close($ch); 
       
       return $output;
    }
    
$color=1;
$json = json_decode(curlit($resource->config('server_address')."/api/v1/plugin/myplugin.json","licence=".$resource->config('licence')));
for($b =0;$b<count($json);$b++){
    

    
$id = $json[$b]->{'id'};
$date = $json[$b]->{'date'};
$name = $json[$b]->{'name'};
$pluginkey = $json[$b]->{'pluginkey'};
$businessname = $json[$b]->{'businessname'};
$description = htmlentities(substr($json[$b]->{'description'}, 0, 35));
    
    
if($color==1){
?>
<div class="stats"  style="overflow:hidden; background-color:#F2F2F2;">
<div style="padding-left:5px; float:left; width:4%; border-right:solid 1px #B8B8B8;"><?php echo $id;?></div>
<div style="padding-left:5px; float:left; width:22%; border-right:solid 1px #B8B8B8;"><img src="<?php echo $resource->config('server_address');?>/plugin_image/<?php echo $pluginkey;?>.png" width="20" height="20" style="vertical-align: middle;"/> <?php echo $name;?></div>
<div style="padding-left:5px; float:left; width:19.5%; border-right:solid 1px #B8B8B8;"><?php echo $businessname;?></div>
<div style="padding-left:5px; float:left; width:25%; border-right:solid 1px #B8B8B8;"><?php echo $description;?></div>
<div style="padding-left:5px; float:left; width:20%; border-right:solid 1px #B8B8B8;"><?php echo $date;?></div>
<div style="padding-left:5px; float:left; width:5%;">D</div>
</div>
<?php
$color="2";
	}else{
?>
<div class="stats"  style="overflow:hidden; background-color:#FAFAF4;">
<div style="padding-left:5px; float:left; width:4%; border-right:solid 1px #B8B8B8;"><?php echo $id;?></div>
<div style="padding-left:5px; float:left; width:22%; border-right:solid 1px #B8B8B8;"><img src="<?php echo $resource->config('server_address');?>/plugin_image/<?php echo $pluginkey;?>.png" width="20" height="20" style="vertical-align: middle;"/> <?php echo $name;?></div>
<div style="padding-left:5px; float:left; width:19.5%; border-right:solid 1px #B8B8B8;"><?php echo $businessname;?></div>
<div style="padding-left:5px; float:left; width:25%; border-right:solid 1px #B8B8B8;"><?php echo $description;?></div>
<div style="padding-left:5px; float:left; width:20%; border-right:solid 1px #B8B8B8;"><?php echo $date;?></div>
<div style="padding-left:5px; float:left; width:5%;">D</div>
</div>  
<?php       
$color="1";	   
	} 
}
?>
<div style="border-bottom:solid #E8E8E8 1px; z-index: 4; position:relative;">&nbsp;</div>




 <style type="text/css">
<!--.first_li{overflow:hidden;}
	   .vmenu{overflow:hidden; border:1px solid #E2E2E2;position:absolute; z-index:15; display:none;}
       .vmenu .first_li span{height:19px; float:left; display:block;padding:5px 10px 5px 2px;cursor:pointer}
       .vmenu .sep_li{border-top: 1px ridge #E2E2E2;}
-->
</style>


  
<div class="vmenu">
<div class="first_li submenu" accesskey="remove"><span style="width:17px; border-right:1px solid #E2E2E2;  padding:5px 2px;"><img src="../../images/icons/delete.png" width="17" height="18" /></span><span>Remove</span></div>
<div class="first_li submenu" accesskey="site"><span style="width:17px; border-right:1px solid #E2E2E2;  padding:5px 2px;"><img src="../../images/icons/check_light.png" width="14" height="14" /></span><span>Visite Site</span></div>
<div class="sep_li"></div>
<div class="first_li submenu" accesskey="change"><span style="width:17px; border-right:1px solid #E2E2E2;  padding:5px 2px;"><img src="../../images/icons/Edit.png" width="11" height="14" /></span><span>Change Page</span></div></div>       
        
<script type="text/javascript" >
	$(document).ready(function(){
 $('.stats').bind('contextmenu',function(e){
 		var element = $(this);
		var Id = element.attr("id");  
        var widgetid = element.attr("lang"); 
         
    $('.first_li').attr("id",Id);
    $('.first_li').attr("lang",widgetid);
			 
$('.vmenu').css({
        top: e.pageY+'px',
        left: e.pageX+'px'
    }).show();
    
			return false;
			 });
 
			 $('.first_li').live('click',function() {
			      		var element = $(this);
		                var Id = element.attr("id");  
                        var widgetid = element.attr("lang"); 
                        var type = element.attr("accesskey");
				
                    if (type == "remove") {
	                   rightremove(Id,widgetid);
                        };
					$('.vmenu').hide();
			 });
 
 
			$(".first_li").hover(function () {
				$(this).css({backgroundColor : '#E0EDFE' , cursor : 'pointer'});
			}, 
			function () {
				$(this).addClass("gorupgradient");
				$(this).find('.inner_li').hide();
			});
 
 			 $(document).bind('click',function() {
					$('.vmenu').hide();
			 });
		});
		</script>

</div>

</div>