<?php
$engine = new engine();
//require "plugin/parking_core/parking_core.php";







if(isset($_REQUEST['today'])){
$today = $_REQUEST['today'];
}else{
$today = date("Y-m-d");    
}
?>
<script type="text/javascript">
$(document).ready(function () {

    $("#search").keyup(function () {
        var searchbox = $(this).val();
        var active = "0";
        $("input[id='active']:checked").each(function(i){
active = "1";
});
        var dataString = 'q=' + searchbox+"&active="+active;

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/membersb.php",
                data: dataString,
                cache: false,
                success: function (html) {
                      $("#pagination").hide();
                      $("#page-content").html(html);

                }


            });
        }
        return false;

    });


});



function setactive(Id,what){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/pro/statuschange.php",
                data: "buserid="+Id+"&status="+what,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}



function set_sms(Id,user){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/pro/statuschange.php",
                data: "smsid="+Id+"&user="+user,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}



function set_merge(Id,user){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/pro/statuschange.php",
                data: "mergeid="+Id+"&user="+user,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}





function set_transfer(Id,user){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/pro/statuschange.php",
                data: "transferid="+Id+"&user="+user,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}

function set_scharge(Id,user){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/pro/statuschange.php",
                data: "scharge="+Id+"&user="+user,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}

function set_autofeed(Id,user){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/pro/statuschange.php",
                data: "autofeed="+Id+"&user="+user,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}


function set_autofeed_amount(user){
    var amount = $("#autofeedamount"+user).val();
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_account/pages/pro/statuschange.php",
                data: "autofeedamount="+amount+"&user="+user,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}
</script>

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />


<div style="background-color: white;">
<div style="border-bottom: solid 1px #EEEEEE; padding:10px; margin-bottom:10px;">Demographics </div>

<div style="float: left; width:42%; margin-left:3%; margin-bottom:10px;">
<div style="float:left; width:39%; overflow:hidden;">
<div style="margin-bottom: 5px;"><span style="font-weight: bold;">Gender</span><br />
Based on the share of rechargepro_account whose gender we were able to determinate through social profile analysis.</div>
<div class="fa fa-female" style="color: #F5888D !important; font-size:400%;"> 85%</div>
</div>


<div style="overflow: hidden; width:60%; float:right; ">
<div style="margin-bottom: 5px;"><span style="font-weight: bold;">Age</span><br />Based on the share of rechargepro_account whose age we were able to determinate through social profile analysis.</div>
<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>

<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>

<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>

<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>
</div>
</div>

<div style="float: right; width:49%; margin-bottom:10px;">
<div style="float:left; width:39%; overflow:hidden;">
<div style="margin-bottom: 5px;"><span style="font-weight: bold;">Gender</span><br />
Based on the share of rechargepro_account whose gender we were able to determinate through social profile analysis.</div>
<div class="fa fa-male" style="color: #5B9BD1 !important; font-size:400%;"> 85%</div>
</div>


<div style="overflow: hidden; width:60%; float:right; ">
<div style="margin-bottom: 5px;"><span style="font-weight: bold;">Age</span><br />Based on the share of rechargepro_account whose age we were able to determinate through social profile analysis.</div>
<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>

<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>

<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>

<div style="background-color:#EEEEEE; font-size:90%; overflow: hidden; margin-bottom:5px;">
<div style="width: 40%; background-color:#46b8da; color:white; text-align:center;">18-19</div></div>
</div>
</div>
<div style="clear: both;"></div>
</div>



<?php
	$permission =	$engine->admin_permission("rechargepro_account","index");
?>

<div class="shadow" style="margin: 10px 0px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="search" type="text" placeholder="Name / Email / Mobile" style="width: 90%;" class="input" /></div>
<?php if($permission >= 3){ ?>
<a href="plugin/rechargepro_account/pages/pro/exportusers.php"><input style="float: right; border: none; padding:3px 10px; margin:3px;" type="button" value="Export Users" class="middlemenu shadow" /></a>
<?php
	}
?>
</div>



<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Account Manager</div></div>



<div class="profilebg" id="acholder" style="border:solid 1px #EEEEEE; overflow:hidden;">


<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>
<?php $rowcount = $engine->db_query2("SELECT rechargeproid FROM rechargepro_account", array(), true);?>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "plugin/rechargepro_account/pages/membersb.php",
    type: "POST",
    data : {page:page},
    success: function(data, textStatus, jqXHR)
    {
        $("#page-content").html(data);
    }
});
        }
    });
    })
</script>

<div id="page-content"></div>
<div style="clear: both;"></div>
<ul style="margin-left: 10px;" id="pagination" class="pagination-sm"></ul>

<?php 	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>












