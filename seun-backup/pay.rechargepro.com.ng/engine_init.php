<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1"/>
<!-- Always force latest IE rendering engine or request Chrome Frame -->
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible"/>


<!--
<meta http-equiv="X-Frame-Options" content="deny"/>
-->


<script type="text/javascript">
if ( window.self !== window.top ) {
    window.top.location.href = window.location.href;
}
</script>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="author" content="<?php echo $engine->config("author");?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>

<title><?php echo $engine->config("app_name");?></title>
<meta name="description" content="Pay Ref" />
<meta name="keywords" content="Pay Ref" />
<meta property="og:title" content="<?php echo $engine->config("app_name");?> Pay Ref/>    
<meta property="og:type" content="Payment Gateway"/>    
<meta property="og:image" content="https://quickpay.com.ng/<?php echo $engine->config("theme_folder").$engine->config("theme");?>/images/logo.png"/>    
<meta property="og:description" content="<?php echo $engine->config("app_name");?> - Pay Ref/>    
<meta property="og:site_name" content="<?php echo $engine->config("app_name");?>"/>                
<meta property="og:url" content="https://quickpay.com.ng/" />
<link rel="canonical" href="https://quickpay.com.ng/"/>


<meta name="robots" content="INDEX,FOLLOW" />

    
<meta name="referrer" content="unsafe-url"/>  
<link hreflang="en-ng" rel="alternate" href="https://quickpay.com.ng/" />
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

<link rel="ico" href="/favicon.ico" type="image/png" sizes="16x16"/>
<link rel="shortcut icon" href="/favicon.ico" type="image/png" />



<?php	if(!file_exists($engine->config("theme_folder").$engine->config("theme")."/$init.php")){?>
<link href="/<?php echo $engine->config("theme_folder");?>/default/css/main.css" rel="stylesheet" type="text/css" />
<?php }else{ ?>  <link href="/<?php echo $engine->config("theme_folder").$engine->config("theme");?>/css/main.css" rel="stylesheet" type="text/css" /> <?php  }?>

   
    
    
<script type="text/javascript" src="/java/jquery-2.1.4.min.js"></script>


<link rel="stylesheet" type="text/css" href="/java/date/dhtmlxcalendar.css"/>
<link rel="stylesheet" type="text/css" href="/java/date/skins/dhtmlxcalendar_dhx_skyblue.css"/>
<script src="/java/date/dhtmlxcalendar.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
var myCalendar;
myCalendar = new dhtmlXCalendarObject(["calendar1", "calendar2", "calendar3","calendar4","calendar5","calendar6","calendar7","calendar8","calendar9"]);
myCalendar.hideTime();
});
</script>


<link rel="stylesheet" href="/java/alert/dist/jquery-confirm.min.css"/>
<script src="/java/alert/dist/jquery-confirm.min.js"></script>


<!--[if lt IE 9]><script src="http://cdnjs.cloudflare.com/ajax/libs/es5-shim/2.0.8/es5-shim.min.js"></script><![endif]-->
<link href="/theme/classic/js/selectize/css/selectize.css" rel="stylesheet" type="text/css" />
<link href="/theme/classic/js/selectize/css/selectize.default.css" rel="stylesheet" type="text/css" />
<script src="/theme/classic/js/selectize/js/standalone/selectize.min.js"></script>
<script type="text/javascript">
  jQuery(document).ready(function($){
    
    $('.select').selectize({
    persist: false,
  // allowEmptyOption: true,
    create: true
});
  });
  
</script>

<link type="text/css" rel="stylesheet" href="/java/tootip/jquery.qtip.css" />
<script type="text/javascript" src="/java/tootip/jquery.qtip.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('[title!=""]').qtip();  
    })
</script>


        <script type="text/javascript" src="/java/display/display.js" charset="utf-8"></script> 
    <link href="/java/display/display.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" charset="utf-8">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>

<?php include_once "functions.php";?>

</head>
<body>

<?php
$file = $engine->config("theme_folder").$engine->config("theme")."/$init.php";
$call = 1;
  
    






if($init == "forgetpassword"){
    include $file;
}else{
$opts = array(
  'http'=>array(
  'method'=>"GET",
  'header'=>"Accept-language: en\r\n" .
  "Cookie: foo=bar\r\n"
  )
);

$context = stream_context_create($opts);
$file = file_get_contents($file, false, $context);
$themeparameters = $engine->theme_parameter($init,$call);
$themeparameterskey = array_keys($themeparameters);
$themeparametersvalue = array_values($themeparameters);
$file = str_replace($themeparameterskey, $themeparametersvalue, $file);
echo $file;
}
?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-115116543-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-115116543-1');
</script>

</body>
</html>