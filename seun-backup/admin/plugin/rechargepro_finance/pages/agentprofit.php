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
                url: "plugin/rechargepro_finance/pages/agentprofitb.php",
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








<div class="profilebg" id="acholder" style="padding:10px; border:solid 1px #EEEEEE; overflow:hidden;">

<script type="text/javascript">
$(document).ready(function () {

    $("#s").keyup(function () {
        var searchbox = $(this).val();
        var dataString = 'q=' + searchbox+"&start="+$("#calendar5").val()+"&end="+$("#calendar4").val();

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_finance/pages/users.php",
                data: dataString,
                cache: false,
                success: function (html) {
                      $("#pagination").hide();
                      $("#page-contentb").html(html);

                }


            });
        }
        return false;

    });


});

</script>

<div style="overflow: hidden; margin-bottom:10px;">
<input autocomplete="off" type="text" id="s" placeholder="Search Name" style="width:150px; float:left; padding:5px;" class="input radious5" />

<input autocomplete="off" type="text" id="calendar4" name="date2" placeholder="End Date" value="<?php echo date("Y-m-d");?>" style="width:140px; float:right; padding:5px;" class="input  radious5" />
<input autocomplete="off" type="text" id="calendar5" name="date1" placeholder="Start Date" value="<?php echo date("Y-m-d");?>" style="width:140px; float:right; margin-right:5px; padding:5px;" class="input  radious5" />
</div>

<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Account Sales</div></div>

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />





<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>
<?php $rowcount = $engine->db_query2("SELECT transactionid FROM rechargepro_refund", array(), true);?>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "plugin/rechargepro_finance/pages/users.php",
    type: "POST",
    data : {page:page,start:$("#calendar5").val(),end:$("#calendar4").val()},
    success: function(data, textStatus, jqXHR)
    {
        $("#page-contentb").html(data);
    }
});
        }
    });
    })
</script>

<div id="page-contentb"></div>
<div style="clear: both;"></div>
<ul style="margin-left: 10px;" id="pagination" class="pagination-sm"></ul>

<?php 	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>






