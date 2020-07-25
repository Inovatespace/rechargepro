<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

 <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0"/>
  <!-- Always force latest IE rendering engine or request Chrome Frame -->
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>


	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="<?php echo $engine->config("author");?>" />
	<title><?php echo $engine->config("app_name");?></title>
    <link href="css/main.css" rel="stylesheet" type="text/css" />
    

<meta name="robots" content="INDEX,FOLLOW" />

    
<meta name="referrer" content="unsafe-url"/>  
<link hreflang="en-ng" rel="alternate" href="https://splvending.com/" />
<link rel="dns-prefetch" href="//www.google-analytics.com"/>
<link rel="dns-prefetch" href="//www.googleadservices.com"/>
<link rel="dns-prefetch" href="//connect.facebook.net"/>
<link rel="dns-prefetch" href="//googleads.g.doubleclick.net"/>
<link rel="dns-prefetch" href="//7205708.collect.igodigital.com"/>
<link rel="dns-prefetch" href="//assets.zendesk.com"/>
<link rel="dns-prefetch" href="//www.facebook.com"/>
<link rel="dns-prefetch" href="//adsearch.adkontekst.pl"/>
<link rel="dns-prefetch" href="//92.media.tumblr.com"/>
<link rel="preconnect" href="//www.gstatic.com"/>
<link rel="preconnect" href="//recs.richrelevance.com"/>
<link rel="preconnect" href="//static.dynamicyield.com"/>
<link rel="preconnect" href="//www.googletagmanager.com"/>
<link rel="preconnect" href="//px.dynamicyield.com"/>
<link rel="preconnect" href="//js-agent.newrelic.com"/>
<link rel="preconnect" href="//static.criteo.net"/>
<link rel="preconnect" href="//eu-sonar.sociomantic.com"/>
<meta name='msvalidate.01' content='66DCE7076C4C56245BAB83C687510491'/>

<link rel="ico" href="favicon.ico" type="image/png" sizes="16x16"/>
<link rel="shortcut icon" href="favicon.ico" type="image/png" />



    
<?php	if(!file_exists($engine->config("theme_folder").$engine->config("theme")."/$init.php")){?>
<link href="<?php echo $engine->config("theme_folder");?>/default/css/main.css" rel="stylesheet" type="text/css" />
<?php }else{ ?>  <link href="<?php echo $engine->config("theme_folder").$engine->config("theme");?>/css/main.css" rel="stylesheet" type="text/css" /> <?php  }?>
   
    
    
    <script type="text/javascript" src="java/jquery-2.1.4.min.js"></script>
    
<script type="text/javascript" src="java/jquery.form.min.js"></script>

    <script src="java/jquery-ui/js/jquery-ui-1.10.4.custom.min.js"></script>


<link type="text/css" rel="stylesheet" href="java/tootip/jquery.qtip.css" />
<script type="text/javascript" src="java/tootip/jquery.qtip.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('[title!=""]').qtip();  
    })
</script>



<script type="text/javascript" src="java/jquery.elastic.js"></script>

<link rel="stylesheet" href="java/alert/dist/jquery-confirm.min.css"/>
<script src="java/alert/dist/jquery-confirm.min.js"></script>


<link rel="stylesheet" type="text/css" href="java/date/dhtmlxcalendar.css"/>
<link rel="stylesheet" type="text/css" href="java/date/skins/dhtmlxcalendar_dhx_skyblue.css"/>
<script src="java/date/dhtmlxcalendar.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
var myCalendar;
myCalendar = new dhtmlXCalendarObject(["calendar1", "calendar2", "calendar3","calendar4","calendar5","calendar6","calendar7","calendar8","calendar9"]);
myCalendar.hideTime();
});
</script>
        <script type="text/javascript" src="java/display/display.js"></script> 
    <link href="java/display/display.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>




<?php include_once "functions.php";?>






<script src="plugin/admin/dhtmlx/chart/js/highcharts.js"></script>
<script src="plugin/admin/dhtmlx/chart/js/highcharts-more.js"></script>
<script src="plugin/admin/dhtmlx/chart/js/modules/exporting.js"></script>
<script src="plugin/admin/dhtmlx/chart/js/highcharts-3d.js"></script>
<script src="plugin/admin/dhtmlx/chart/js/modules/map.js"></script>
<script type="text/javascript">
$(function () {
    	// Radialize the colors
		Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
		    return {
		        radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
		        stops: [
		            [0, color],
		            [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
		        ]
		    };
		});
        
})
</script>



    </head>

<body>
<div style="margin-left:auto; margin-right:auto; overflow:hidden; <?php echo $engine->config("site_width");?>;">
<?php

include_once "MobileDetect.php";

$mobiledetector = new mobiledetector();
$mobile = $mobiledetector->mobile();


switch ($mobile){
	case "ipad":
    case "android": $file = $engine->config("theme_folder").$engine->config("theme")."/medium.php";
	break;

	case "iphone": $file = $engine->config("theme_folder").$engine->config("theme")."/small.php";
	break;
    
    default: $file = $engine->config("theme_folder").$engine->config("theme")."/$init.php";
    break;
    }
    
if($init != "index"){
  $file = $engine->config("theme_folder").$engine->config("theme")."/$init.php";  
 }   
    //if failed fall back to defualt $init set in index



if(!file_exists($file)){
$file = $file = $engine->config("theme_folder")."default/$init.php";
}


$opts = array(
  'http'=>array(
  'method'=>"GET",
  'header'=>"Accept-language: en\r\n" .
  "Cookie: foo=bar\r\n"
  )
);

require_once "browser.php";
if(!file_exists("language/$language/main.php")){
header("location:setlanguage?l=en"); exit;
//echo "<meta http-equiv='refresh' content='0;url=location:setlanguage?l=en'>"; exit;
}else{
require_once "language/$language/main.php"; 
}

$context = stream_context_create($opts);
$file = file_get_contents($file, false, $context);
$themeparameters = array_merge_recursive($engine->theme_parameter($init), $languagearray);
$themeparameterskey = array_keys($themeparameters);
$themeparametersvalue = array_values($themeparameters);
$file = str_replace($themeparameterskey, $themeparametersvalue, $file);
echo $file;
?>

</div>
</body>
</html>