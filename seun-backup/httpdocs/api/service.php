<?php
include "../engine.autoloader.php";

$version = $_REQUEST['u'];
define("CLASS_MODEL_DIR","source/" . $version."/");
?>
<style>body { color: #000000; background-color: white; margin-left: 0px; margin-top: 0px; } #content { margin-left: 30px; padding-bottom: 2em; } A:link { color: #336699; font-weight: bold; text-decoration: underline; } A:visited { color: #6699cc; font-weight: bold; text-decoration: underline; } A:active { color: #336699; font-weight: bold; text-decoration: underline; } .heading1 { background-color: #003366; border-bottom: #336699 6px solid; color: #ffffff;   font-weight: normal;margin: 0em 0em 10px -20px; padding-bottom: 8px; padding-left: 30px;padding-top: 16px;} .heading1 a:link, .heading1 a:visited{text-decoration: none; color:white;} pre { font-size:small; background-color: #e5e5cc; padding: 5px; font-family: Courier New; margin-top: 0px; border: 1px #f0f0e0 solid; white-space: pre-wrap; white-space: -pre-wrap; word-wrap: break-word; } table { border-collapse: collapse; border-spacing: 0px; } table th { border-right: 2px white solid; border-bottom: 2px white solid; font-weight: bold; background-color: #cecf9c;} table td { border-right: 2px white solid; border-bottom: 2px white solid; background-color: #e5e5cc;}
a:link, a:visited{text-decoration: none; color:black;}
</style>

<?php

if(!file_exists(CLASS_MODEL_DIR)){
   echo '<div style="font-size:28px;" class="heading1">Service :: '.$version.'</div><div style="padding:20px 40px; overflow;hidden;"><div style="">Service Not found. Please see the service help page for constructing valid requests to the service.</div></div>';  exit;   
}



function class_autoload($class)
{
    $class = str_replace('_', '', strtolower($class . '.php'));
    if (file_exists(CLASS_MODEL_DIR. $class)) {
        include_once (CLASS_MODEL_DIR. $class);
    }
}

spl_autoload_register('class_autoload');


class Api{}

$class_array = scandir(CLASS_MODEL_DIR);
$classarray = array();
foreach ($class_array as $class) {

if($engine->getExtension($class) == "php"){
    $explode = explode(".", $class);
    $classarray[] = $explode[0];
}
}

?>

<div style="font-size:28px;" class="heading1"><a href="<?php echo $version;?>">Service :: <?php echo $version;?></a></div>
<div style="padding:20px 40px; overflow;hidden;">

<?php if(!empty($_REQUEST['p'])){?>
<div>All request should be done using POST Method</div>
<div style="margin-bottom: 20px;">To spefify a return format change the extension between xml,txt,json and printr</div>

<?php }else{ 
echo '<div style="">Method not allowed. Please see the service help page for constructing valid requests to the service.</div>';    
} ?>


<?php
if(empty($_REQUEST['p'])){
echo '<table>
        <tbody>
<tr>
	<th>URL</th>
	<th>METHOD</th>
	<th>DESCRIPTION</th>
</tr>
';
foreach($classarray AS $service){
  echo "<tr>
	<td><a href='$version/$service'>/$service</a></td>
	<td>POST</td>
	<td>Call this service to retreive $service when setting up a terminal for the first time. For Technical support, contact ".$engine->config("admin_email")."</td>
</tr>"; 	
}
echo '</tbody></table>';
}


if(isset($_REQUEST['p']) && !empty($_REQUEST['p'])){
    
    
$class = $_REQUEST['p'];//str_ireplace('',"",$_REQUEST['p']);
$class = explode("/",$class);
$class = htmlentities($class[1]);//$classarray[2]; //the dynamic

if(!class_exists($class)){
echo '<div style="">Service Not found. Please see the service help page for constructing valid requests to the service.</div>';  exit;   
}

$methodcontent = file_get_contents(CLASS_MODEL_DIR.$class.".php");
$methodcontent = str_ireplace('"',"'",$methodcontent);
$methodcontent = htmlentities($methodcontent);
$methodcontent = str_ireplace('"',"'",$methodcontent);

$classmethod = get_class_methods($class);





echo '<table>
        <tbody>
        <tr>
        <th>API</th>
          <th>Message direction</th>
          <th>Format</th>
          <th>Body</th>
        </tr>';
   foreach($classmethod AS $arraymethod){
    if($arraymethod != "__construct"){
     echo '   <tr>
        <td>/'.$arraymethod.'</td>
          <td>Request</td>
          <td>xml</td>
          <td>
            <a href="#'.$arraymethod.'response-xml">Example</a>
          </td>
        </tr>
               <tr>
        <td>/'.$arraymethod.'</td>
          <td>Request</td>
          <td>Json</td>
          <td>
            <a href="#'.$arraymethod.'request-json">Example</a>
          </td>
        </tr>';
        }
         	}
echo '</tbody></table>';





echo '<ul>';
foreach($classmethod AS $method){
    if($method != "__construct"){
echo "<li>API: $method</li>";  
echo "<li>Url: ".$engine->config("website_root")."api/$version/$class/$method.xml</li>"; 
echo '</ul>';
//$method = $classmethod[1];// the dynamic
$methodkey = array_search($method,$classmethod);
$nextkey = $methodkey+1;
if(!key_exists($nextkey,$classmethod)){
  $nextkey = $methodkey;  
}


$explode = explode("$method(",$methodcontent);
$methodcontentb = $explode[1];
$explodeb = explode("$classmethod[$nextkey](",$methodcontentb);
$methodcontentc = $explodeb[0]; 
//print_r($methodcontentc);
$matches = array();
preg_match_all("/parameter[[]['][A-Z a-z 0-9_]{0,}/",$methodcontentc,$matches);

$parameters = array();
foreach($matches[0] AS $value){
    $explodec = explode("parameter['",$value);
    $parameters[] = trim($explodec[1]);
}

$parameters = array_unique($parameters);

echo '<p></p><p><a name="#'.$method.'request-xml">The following is an example request Xml body:</a></p>
<pre class="request-xml">&lt;'.$method.'Req xmlns="http://schemas.datacontract.org/2004/07/SPLICSWebService.Model &gt;';
 
 
foreach($parameters AS $service){
  echo "<br />&lt;$service&gt;".gettype($service)." content&lt;/$service&gt;";  
}
echo '<br />&lt;/'.$method.'Req></pre><p></p><p><a name="#'.$method.'request-json">The following is an example request Json body:</a></p>
 <pre class="request-json">{';

foreach($parameters AS $service){
  echo "<br />\"$service\":\"".gettype($service)." content\",";  
}

echo '<br />}</pre>';
}
}

}
?>
</div>
      
      