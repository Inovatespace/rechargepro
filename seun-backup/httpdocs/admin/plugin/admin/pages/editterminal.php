<?php
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include_once "../../../engine.autoloader.php";

$id = $_REQUEST['id'];

$row = $engine->db_query("SELECT * FROM terminal_acces WHERE id = ? LIMIT 1",array($id)); 
$acountid = $row[0]['account_id'];
$password = $row[0]['password'];
$access = $row[0]['access'];
?>
<div class="barmenu" style="text-align: left; padding: 5px; margin: -15px -5px 0px -5px;">Edit Permission</div>
<div style="text-align: left; padding:5px 20px;" class="profilebg">

<div id="status" style="color: red; margin-bottom:5px; display:none;"></div>



<form method="post" action="plugin/admin/pages/pro/newterminalpro.php">

<div style="overflow: hidden; padding:2px 2px 10px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5;   margin-bottom:10px;">
<div style="float: left; width:150px;">Account ID</div><input name="account" readonly="readonly" value="<?php echo $acountid;?>" type="text" class="input" style="width:200px; float:left;"/>
</div>

<div style="overflow: hidden; padding:2px 2px 10px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5;   margin-bottom:10px;">
<div style="float: left; width:150px;">Password</div><input value="<?php echo $password;?>" name="password" type="text" class="input" style="width:200px; float:left;"/>
</div>



<div style="overflow: hidden; padding:2px 2px 10px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5; margin-bottom: 5px;">
<div style="float: left; width:150px;">Access</div>
<div style="float: left; width:70px;">None</div>
<div style="float: left; width:60px;">Read</div>
<div style="float: left;">Read/Write</div>
</div>
<?php

$newarray = array();
$explode = explode(",",$access);
foreach($explode AS $value){
$valueb = explode("=",$value);
$newarray[$valueb[0]] = $valueb[1];
}



$row = $engine->db_query("SELECT id, name FROM terminal_permission",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$id = $row[$dbc]['id'];
$name = $row[$dbc]['name'];


$a = "";
$b = "";
$c = "";
if(key_exists($id,$newarray)){
if($newarray[$id] == "1"){$a = 'checked="checked"';}
if($newarray[$id] == "2"){$b = 'checked="checked"';}
if($newarray[$id] == "3"){$c = 'checked="checked"';}
}




?>
<div style="overflow: hidden; padding:2px 2px 10px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5; margin-bottom: 5px;">
<div style="float: left; width:160px;"><?php echo $name;?></div>
<input style="float: left; margin-right:50px" name="<?php echo $id;?>" <?php echo $a;?>  name="ade" value="1" type="radio" />
<input style="float: left; margin-right:50px" name="<?php echo $id;?>" <?php echo $b;?> name="ade" value="2" type="radio" />
<input style="float: left;" name="<?php echo $id;?>" <?php echo $c;?> name="ade" value="3" type="radio" />
</div>


<?php }?>








<div style="overflow: hidden;">
<img id="loader" src="images/loading6.gif" width="105" height="16" style="float: left; display:none;" />
<input class="sbtn shadow activemenu" style="padding:2px 25px; border: none; margin:3px; float:right; cursor: pointer;" type="submit" value="Save" /> 
</div>






</form>


</div>