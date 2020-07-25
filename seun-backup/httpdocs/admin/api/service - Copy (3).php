<?php
include "../engine.autoloader.php";

$version = $_REQUEST['u'];
define("CLASS_MODEL_DIR","source/" . $version."/");
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
<div style="font-size:28px;"><a href="<?php echo $version;?>"><?php echo $version;?></a></div>

<?php if(!empty($_REQUEST['p'])){?>
<div>All request should be done using POST</div>
<div style="margin-bottom: 20px;">To spefify a return format change the extension between xml,txt,json and printr</div>
<?php }else{
echo '<div style="font-size:18px;">Services</div>';    
echo 'Method not allowed. Please see the service help page for constructing valid requests to the service.</div>';    
} ?>


<?php
if(empty($_REQUEST['p'])){
echo '<div class="CSSTable"><table style="">
<tr>
	<th>URL</th>
	<th>METHOD</th>
	<th>DESCRIPTION</th>
</tr>
<ul>';
foreach($classarray AS $service){
  echo "<tr>
	<td><a href='$version/$service'>/$service</a></td>
	<td>POST</td>
	<td>Call this service to retreive $service when setting up a terminal for the first time. For Technical support, contact ".$engine->config("admin_email")."</td>
</tr>"; 	
}
echo '</table></div>';
}


if(isset($_REQUEST['p']) && !empty($_REQUEST['p'])){
    
    
$class = str_ireplace('/',"",$_REQUEST['p']);
$class = htmlentities($class);//$classarray[2]; //the dynamic

if(!class_exists($class)){
 echo "dddddd"; exit;   
}

$methodcontent = file_get_contents(CLASS_MODEL_DIR.$class.".php");
$methodcontent = str_ireplace('"',"'",$methodcontent);
$methodcontent = htmlentities($methodcontent);
$methodcontent = str_ireplace('"',"'",$methodcontent);

$classmethod = get_class_methods($class);
echo '<ul>';
foreach($classmethod AS $method){
echo "<li>$method</li>";  
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
preg_match_all("/parameter[[]['][A-Z a-z 0-9]{0,}/",$methodcontentc,$matches);

$parameters = array();
foreach($matches[0] AS $value){
    $explodec = explode("parameter['",$value);
    $parameters[] = trim($explodec[1]);
}

$parameters = array_unique($parameters);

echo '<ul>';
foreach($parameters AS $service){
  echo "<li>$service</li>";  
}
echo '</ul>';
}
echo '</ul>';

}
?>


<style type="text/css">
.CSSTable table, caption, tbody, tfoot, thead, tr, th, td {
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	vertical-align: baseline;
	background: transparent;
     white-space: nowrap;
}

.CSSTable table {border-spacing: 0; } /* IMPORTANT, I REMOVED border-collapse: collapse; FROM THIS LINE IN ORDER TO MAKE THE OUTER BORDER RADIUS WORK */
.CSSTable table a:link {
	color: #666;
	font-weight: bold;
	text-decoration:none;
}
.CSSTable table a:visited {
	color: #999999;
	font-weight:bold;
	text-decoration:none;
}
.CSSTable table a:active,
.CSSTable table a:hover {
	color: #bd5a35;
	text-decoration:underline;
}
.CSSTable table {
	font-family:Arial, Helvetica, sans-serif;
	color:#666;
	font-size:12px;
	text-shadow: 1px 1px 0px #fff;
	background:#eaebec;
	margin:2px;
	border:#ccc 1px solid;

	-moz-border-radius:3px;
	-webkit-border-radius:3px;
	border-radius:3px;

	-moz-box-shadow: 0 1px 2px #d1d1d1;
	-webkit-box-shadow: 0 1px 2px #d1d1d1;
	box-shadow: 0 1px 2px #d1d1d1;
}
.CSSTable table th {
	padding:9px;
	border-top:1px solid #fafafa;
	border-bottom:1px solid #e0e0e0;

	background: #ededed;
	background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
	background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
}
.CSSTable table th:first-child{
	text-align: left;
	padding-left:4px;
}
.CSSTable table tr:first-child th:first-child{
	-moz-border-radius-topleft:3px;
	-webkit-border-top-left-radius:3px;
	border-top-left-radius:3px;
}
.CSSTable table tr:first-child th:last-child{
	-moz-border-radius-topright:3px;
	-webkit-border-top-right-radius:3px;
	border-top-right-radius:3px;
}
.CSSTable table tr{
	text-align: left;
	padding-left:5px;
}
.CSSTable table tr td:first-child{
	text-align: left;
	padding-left:5px;
	border-left: 0;
}
.CSSTable table tr td {
	padding:5px 5px;
	border-top: 1px solid #ffffff;
	border-bottom:1px solid #e0e0e0;
	border-left: 1px solid #e0e0e0;
	
	background: #fafafa;
	background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
	background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
}



.CSSTable table tr.even td{
	background: #f6f6f6;
	background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
	background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
}
.CSSTable table tr:last-child td{
	border-bottom:0;
}
.CSSTable table tr:last-child td:first-child{
	-moz-border-radius-bottomleft:3px;
	-webkit-border-bottom-left-radius:3px;
	border-bottom-left-radius:3px;
}
.CSSTable table tr:last-child td:last-child{
	-moz-border-radius-bottomright:3px;
	-webkit-border-bottom-right-radius:3px;
	border-bottom-right-radius:3px;
}
.CSSTable table tr:hover td{
	background: #f2f2f2;
	background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
	background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);	
}

</style>
