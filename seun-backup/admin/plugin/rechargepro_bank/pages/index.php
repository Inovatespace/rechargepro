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
                url: "plugin/rechargepro_bank/pages/transactionlogb.php",
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

</script>

<div class="shadow" style="margin: 10px 0px; padding:10px 20px; overflow:hidden; background-color:white;">

<div style="overflow: hidden;">
<select id="chice" style="float: left; margin-right:10px; width:20%;" class="input" onchange="updateme()">
<option value="All">All</option>
<option >DEBIT</option>
<option >CREDIT</option>
</select>


<input type="text" id="search" placeholder="Ref" class="input search" style="float: left; width:20%;"/>


<div style="float: right;">
<form action="plugin/rechargepro_bank/pages/exportdownload.php">
<input autocomplete="off" type="text" id="calendar2" name="date2" placeholder="End Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right;" class="input" />
<input autocomplete="off" type="text" id="calendar3" name="date1" placeholder="Start Date" value="<?php echo date("Y-m-d");?>" style="width:100px; float:right; margin-right:5px;" class="input" />
<input type="submit" value="Download Report" style="margin:3px; float: right; color:white; border:none; padding:5px 10px; margin-right:5px;" class="greenmenu shadow"/>
</form>
</div>
</div>

</div>
<style type="text/css">
#arrow1{border-top: 18px solid #dd1111;}
#arrow2{border-top: 18px solid #223F9F;}
#arrow3{border-top: 18px solid #3C7628;}
#arrow4{border-top: 18px solid #BA8A30;}
#arrow5{border-top: 18px solid #C01462;}
</style>
<script type="text/javascript">
jQuery(document).ready(function($){
    
$.ajax({
    url : "plugin/rechargepro_bank/pages/charge.php",
    type: "POST",
    data : {},
    success: function(data, textStatus, jqXHR)
    {
        $("#leftpage-content").html(data);
    }
});
    
    })
</script>
<div style="float: left; width:150px;" id="leftpage-content">
</div>

<div style="float: right; width:calc(100% - 160px);">

<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Transaction Log</div></div>



<div class="profilebg" id="acholder" style="padding:10px; border:solid 1px #EEEEEE; overflow:hidden;">
<style type="text/css">
.stats{position: relative; overflow:hidden; border-bottom:1px solid #EEEEEE; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #F1F1F1; border-bottom:1px solid #DDDDDD; padding:1px;}
.stats:hover {background: #F2F2F2; color:#F9C93A;}
</style>


<script type="text/javascript">
function updateme(){
   var chice = $("#chice").val();
   
   window.location.href = "rechargepro_bank&p=index&t="+chice; 
}


</script>

<script type="text/javascript">
jQuery(document).ready(function($){
    
      <?php if(isset($_REQUEST['t'])){?>
        $("#chice").val("<?php echo $_REQUEST['t'];?>");
   <?php }
    ?>
    
    })
</script>


<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />





<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>
<?php if(isset($_REQUEST['t'])){
    if($_REQUEST['t'] == "All"){
         $rowcount = $engine->db_query("SELECT id FROM bank_alert", array(), true);
    }else{
    $rowcount = $engine->db_query("SELECT id FROM bank_alert WHERE transaction_type = ? ", array($_REQUEST['t']), true);
    }
    }else{
        $rowcount = $engine->db_query("SELECT id FROM bank_alert", array(), true);
    }
    
    if($rowcount > 0){
    ?>
<script type="text/javascript">
jQuery(document).ready(function($){
    
    var type = "All";
    <?php if(isset($_REQUEST['t'])){?>
        type = "<?php echo $_REQUEST['t'];?>";
   <?php }
    ?>
    
    
    
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 80);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "plugin/rechargepro_bank/pages/transactionlogb.php",
    type: "POST",
    data : {page:page,type:type},
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

<?php 	}else{  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='nextcolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>
</div>





<style type="text/css">
.arrow
{
    position: relative;
    height: 0px;
    width: 0px;
    border-left: 11px solid transparent;
    border-right: 11px solid transparent;
    position:absolute;
    bottom:40px;
    left:57px;
    z-index:1;
    animation: load-arrow 1.6s linear;
    animation-fill-mode:forwards;
    -webkit-animation: load-arrow 1.6s linear;
    -webkit-animation-fill-mode:forwards;
}

@keyframes load-arrow
{
    from { transform: translate(0,0); }
    to { transform: translate(0,55px); }
}

@-webkit-keyframes load-arrow
{
    from { -webkit-transform: translate(0,0); }
    to { -webkit-transform: translate(0,55px); }
}

.pie
{
    width: 140px;
    height: 140px;
    position: relative;
    border-radius: 140px;
    float:left;
    margin-right:10px;
    margin-bottom:50px;
}

.pie .title
{
    position:absolute;
    bottom:-40px;
    text-align: center;
    width:100%;
    font-weight:bold;
}

.mask
{
    position: absolute;
    width: 100%;
    height: 100%;
}

.pie1 .inner-right
{
    transform: rotate(160deg);
    animation: load-right-pie-1 1s linear;
    -webkit-animation: load-right-pie-1 1s linear;
    -webkit-transform: rotate(160deg);
}

@keyframes load-right-pie-1
{
    from {transform: rotate(0deg);}
    to {transform: rotate(160deg);}
}

@-webkit-keyframes load-right-pie-1
{
    from {-webkit-transform: rotate(0deg);}
    to {-webkit-transform: rotate(160deg);}
}

.outer-left
{
    clip: rect(0px 70px 140px 0px);
}.outer-right
{
    clip: rect(0px 140px 140px 70px);
}

.inner-left
{
    background-color: #710000;
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 100%;
    clip: rect(0px 70px 140px 0px);
    transform: rotate(-180deg);
    -webkit-transform: rotate(-180deg);
}
.inner-right
{
    background-color: #710000;
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 100%;
    clip: rect(0px 70px 140px 0px);
    transform: rotate(180deg);
    -webkit-transform: rotate(180deg);
}

.content
{

    width:100px;
    height:100px;
    border-radius:50%;
    background-color:#fff;
    position:absolute;
    top:20px;
    left:20px;
    line-height: 100px;
    font-family:arial, sans-serif;
    font-size:12px;
    text-align: center;
    z-index:2;
}

.content span
{
    opacity: 0;
    animation: load-content 3s;
    animation-fill-mode:forwards;
    animation-delay: 0.6s;
    -webkit-animation: load-content 3s;
    -webkit-animation-fill-mode:forwards;
    -webkit-animation-delay: 0.6s;
}

@keyframes load-content
{
    from {opacity:0;}
    to {opacity:1;}
}

@-webkit-keyframes load-content
{
    from {opacity:0;}
    to {opacity:1;}
}
</style>
