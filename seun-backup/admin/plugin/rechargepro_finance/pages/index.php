<?php
$engine = new engine();
//require "plugin/parking_core/parking_core.php";


//when pickup/delivery enter payment method
//show next or phamarcy for approved oders


if(isset($_REQUEST['today'])){
$today = $_REQUEST['today'];
}else{
$today = date("Y-m-d");    
}

?>
<script type="text/javascript">
$(document).ready(function () {
call_page("1","1");
});

function changeit(){
   var c = $("#changeit").val(); 
  call_page(c,"1");  
}


function setit(){
   var c = $("#calendar1").val()+"@"+$("#calendar2").val(); 
  call_page(c,"2");  
}


function call_page(page,type="1"){
    
     $("#page-content").html('<img src="../theme/classic/images/loading.gif" width="124" height="124" />');
     
     
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_finance/pages/indexb.php",
                data: "type="+type+"&page="+page,
                cache: false,
                success: function (html) {
                      $("#page-content").html(html);

                }
            });
        
}

</script>



<div class="profilebg" id="acholder" style="padding:10px; border:solid 1px #EEEEEE; overflow:hidden;">

<div style="overflow: hidden;">
<select onchange="changeit()" class="input" id="changeit">
	<option value="1">Daily</option>
    <option value="2">Weekly</option>
    <option value="3">Monthly</option>
</select>


<div style="overflow: hidden; float:right;">
<input type="submit" value="Open Report" onclick="setit()" style="margin:3px; float: right; color:white; border:none; padding:5px 10px; margin-right:5px;" class="greenmenu shadow"/>
<input autocomplete="off" type="text" id="calendar2" name="date2" placeholder="End Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right; margin-right:5px;" class="input" />

<input autocomplete="off" type="text" id="calendar1" name="date1" placeholder="Start Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right; margin-right:5px;" class="input" />
</div>

</div>


<div id="page-content"> </div>


</div>

