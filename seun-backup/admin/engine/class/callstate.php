<?php
	$country = $_REQUEST['country'];
    require "../../resource.php";
?>    

<select name="state" id="state" class="input" onchange="calllga()" style="width:99%;">
<?php foreach($resource->state($country) AS $stateb){
    echo '<option>'.$stateb.'</option>';
};?>
</select>
