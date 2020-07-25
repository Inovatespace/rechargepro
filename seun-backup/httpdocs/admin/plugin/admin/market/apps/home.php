<div style="border-bottom: solid 1px #000000; color:white; overflow: hidden;"> 
<div class="tsprite whitelink" style="background-position:right -526px; background-color:#000000; float: left; padding:4px 90px 5px 40px">Plugin</div>
</div>



<div style="margin-bottom:20px; padding:20px;  border:solid 1px #CCCCCC; border-top:none;">

<div style="overflow: hidden; margin-bottom:10px;">
<div style="float: left; font-size:15px; font-weight:bold;">SPL &raquo; All</div>

<div style="float: right;"><input name="q" class="input" style="font-size:11px; background-color: white; border:solid #CCCCCC 1px;float:left; width:350px; height:19px; padding:2px;" id="searchbox" type="text"  value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}" autocomplete="off"/></div>
</div>




<div style="overflow: hidden;">



<div style="overflow:hidden; padding:10px; border:dashed 1px #CCCCCC; background-color:white;">
<div class="shadow tsprite" style="background-position:0px -485px; background-repeat: repeat-x;overflow:hidden; padding:4px; margin: -5px -5px 30px -5px;">
<div class="radious3" style="float: right; background-color:white;">
<div class="activemenu radious3" style="font-weight:bold; font-size:11px; border:solid 1px black; line-height:normal; padding:2px 10px;">Manage Your Plugin</div>
</div>
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
    
    


?>

<div style="position: relative;">
<?php
include_once ('plugin/admin/rating/rate.php');
$color=1;
$json = json_decode(curlit($resource->config('server_address')."/api/v1/plugin/pluginlist.json","licence=".$resource->config('licence')));
for($b =0;$b<count($json);$b++){
    
$pluginid = $json[$b]->{'pluginid'};
$name = $json[$b]->{'name'};
$pluginkey = $json[$b]->{'pluginkey'};
$businesstype = $json[$b]->{'businesstype'};
$version = $json[$b]->{'version'};
$compatibility = $json[$b]->{'compatibility'};
$date = $json[$b]->{'date'};
$description = htmlentities($json[$b]->{'description'});

    $yes=htmlentities(20);	
    $no=htmlentities(5);  	  
?>
<div class="shadow" style="font-size:11px; padding:10px; overflow:hidden; margin-bottom:20px;">
<div style="cursor:pointer; font-weight: bold; font-size:12px;"><?php echo $name;?></div>
<div style="float: left; width:120px; cursor:pointer;"><img src="<?php echo $resource->config('server_address');?>/plugin_image/<?php echo $pluginkey;?>.png" width="100" /></div>
<div style="float: right; width:500px;">
<div><?php echo $description;?></div>
<div style="float: left;"><?php echo $rate->showrate("plugin$pluginid",'star',$yes,$no,"0",$pluginkey,"A");?></div>
<div class="darkcolor" style="float: right; font-weight: bold;">Version :: <?php echo $version;?></div>
</div>
</div>	


        
  <?php
        }
?>

</div>



</div>
</div>

</div>
