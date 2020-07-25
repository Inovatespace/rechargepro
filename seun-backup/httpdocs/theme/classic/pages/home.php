<?php  
$engine = new engine();
?>
<script type="text/javascript">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>
<?php
	$rand = rand(1,2);
?>
<div style="background: url(theme/classic/images/bg/<?php echo $rand;?>.jpg) no-repeat center;">

<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px;">
<!-- 11111111111111111111111111111111111 -->
<div style="padding:5px 0px; margin-bottom:5px; margin-top:10px; overflow: hidden;">

    <?php
    switch ($rand){ 
    case "2": $cl = "white";
    break;
    
    case "1": $cl = "black";
    break;
    }
    ?>
    
<style type="text/css">
#breadcrumb{float: left; color:<?php echo $cl;?>; font-size:140%;}
#searchbarholder{float: right; width:50%;}
@media (max-width: 400px) {
#breadcrumb{display:none;}
#searchbarholder{float: none; width:100%;}
}
</style>

<div id="breadcrumb" >Dashboard</div>
<div id="searchbarholder">
<button class="outline" style="float: right; width:25%; background-color: #0F73C9; color:white; border:none; padding:8px 0.5%;" ><span class="fas fa-search"></span></button>
<input class="outline" type="text" id="search" placeholder="Search" onkeyup="call_search()" style="width:72.5%; float: right; padding:8px 0.5%; border: solid 1px #CCCCCC; margin-right:0.5%;" /> 
</div>
</div>
<div style="clear: both;"></div>
<!-- 2222222222222222222222222 -->
<style type="text/css">
.main-1{width: 10%;}
.main-2{width: 89.5%;float:right; }
.main-3 {display:none;}
@media (max-width: 1274px) {
    .main-1 {width: 12%;}
    .main-2 {width: 87.5%;}
}

@media (max-width: 1050px) {
    .main-1 {width: 15%;}
    .main-2 {width: 84.5%;}
}

@media (max-width: 756px) {
    .main-1 {width: 15%;}
    .main-2 {width: 84.5%;}
}


@media (max-width: 580px) {
    .main-1 {width: 20%;}
    .main-2 {width: 79.5%;}
}


@media (max-width: 426px) {
    .main-1 {display:none;}
    .main-3 {display:inline;}
    .main-2 {width: 100%;}
}
</style>

<!--
.main-1 {width: 25%;}
    .main-2 {width: 74.5%;}
-->

<script type="text/javascript">
function call_search(){
    $('#pageholder').prepend('<div class="pageloader" style="left:40px; top:40px; position:absolute; font-size:200%;"><span class="fa fa-spinner fa-pulse fa-5x fa-fw"></span>Loading...</div>');
    
    var search = $("#search").val();
    var data = {s:search};
    $.post("theme/classic/pages/search.php", data).done(
    function(response){
          location.hash = search;
        $('#pageholder').html(response);
     }
   ).error(
    function(jqXHR, textStatus, errorThrown) {
        $('.pageloader').remove();
        $.alert("Please Check your network");
     }
 );
 
}


function call_page(Id){
    $('#pageholder').prepend('<div class="pageloader" style="left:40px; top:40px; position:absolute; font-size:200%;"><span class="fa fa-spinner fa-pulse fa-5x fa-fw"></span>Loading...</div>');
    
    var data = "";
    <?php if(isset($_REQUEST['key']) && isset($_REQUEST['cat'])){?> 
    data = {key:"<?php echo $engine->safe_html($_REQUEST['key']);?>",cat:"<?php echo $engine->safe_html($_REQUEST['cat']);?>"};
    <?php }?>
    
    $.post("theme/classic/pages/call/"+Id+".php", data).done(
    function(response){
        
    switch (Id){ 
	case "airtime": $("#breadcrumb").html("Airtime/Data");
	break;

	case "dashboard": $("#breadcrumb").html("Dashboard");
	break;

	case "tv": $("#breadcrumb").html("Cable/TV");
	break;
    
	case "utility": $("#breadcrumb").html("Utility");
	break;
    
    case "bills": $("#breadcrumb").html("Bills");
	break;
    }
         location.hash = Id;
          
         //window.history.pushState("RechargePro", "RechargePro", "/index#"+Id);
        $('#pageholder').html(response);
        //alert("gg");
     }
   ).error(
    function(jqXHR, textStatus, errorThrown) {
        $('.pageloader').remove();
          $.alert("Please Check your network");
     }
 );
 
}

</script>
<script type="text/javascript">
$(function () {
  
  var hash = location.hash.substr(1);
  switch (hash){ 
	case "airtime": call_page("airtime");
	break;

	case "dashboard": call_page("dashboard");
	break;

	case "tv": call_page("tv");
	break;
    
	case "utility": call_page("utility");
	break;
    
        
	case "bills": call_page("bills");
	break;
}
        
})
</script>

<div class="main-3" style="overflow: hidden; text-align: center; width: 100%; margin-top: 5px;">
<div style="height: 1px; margin-bottom:5px; background-color: #B9B3B3;">&nbsp;</div>
<div style="overflow: hidden; color:white; cursor: pointer; float:left; width:24%;" onclick="call_page('airtime')">
<div style="padding:5px 0px;"><span style="font-size: 250%; margin-bottom:10px; " class="fas fa-broadcast-tower"></span></div>
</div>

<div style="overflow: hidden; color:white; cursor: pointer; float:left; width:24%;" onclick="call_page('utility')">
<div style="padding:5px 0px;"><span style="font-size: 250%; margin-bottom:10px; padding-bottom:0px; " class="fas fa-lightbulb"></span></div>
</div>

<div style="overflow: hidden; color:white; cursor: pointer; float:left; width:24%;" onclick="call_page('tv')">
<div style="padding:5px 0px;"><span style="font-size: 250%; margin-bottom:10px; " class="fas fa-tv"></span></div>
</div>

<div style="overflow: hidden; color:white; cursor: pointer; float:left; width:24%;" onclick="call_page('bills')">
<div style="padding:5px 0px;"><span style="font-size: 250%; margin-bottom:10px; " class="fas fa-globe-africa"></span></div>
</div>
<div style="clear: both;"></div>
</div>



<div class="main-1" style="text-align:center; height: 500px; float:left; font-size:90%;">

<div style="margin-bottom:5px; position: relative; overflow: hidden; color:white; cursor: pointer;" onclick="call_page('airtime')">
<div class="transparent"  style="position: absolute; z-index: 1; background-color:black;  width:100%; height:250px;"></div>
<div style="position: relative; z-index: 2; text-align:center; padding:20px 0px;"><span style="font-size: 250%; margin-bottom:10px; " class="fas fa-broadcast-tower"></span><br /><span>Airtime/Data</span></div>
</div>

<div style="margin-bottom:5px; position: relative; overflow: hidden; color:white; cursor: pointer;" onclick="call_page('utility')">
<div class="transparent"  style="position: absolute; z-index: 1; background-color:black;  width:100%; height:250px;"></div>
<div style="position: relative; z-index: 2; text-align:center; padding:20px 0px;"><span style="font-size: 250%; margin-bottom:10px; padding-bottom:0px; " class="fas fa-lightbulb"></span><br /><span>Electricity</span></div>
</div>

<div style="margin-bottom:5px; position: relative; overflow: hidden; color:white; cursor: pointer;" onclick="call_page('tv')">
<div class="transparent"  style="position: absolute; z-index: 1; background-color:black;  width:100%; height:250px;"></div>
<div style="position: relative; z-index: 2;  text-align:center; padding:20px 0px;"><span style="font-size: 250%; margin-bottom:10px; " class="fas fa-tv"></span><br /><span>Cable TV</span></div>
</div>

<div style="margin-bottom:5px; position: relative; overflow: hidden; color:white; cursor: pointer;" onclick="call_page('bills')">
<div class="transparent"  style="position: absolute; z-index: 1; background-color:black;  width:100%; height:250px;"></div>
<div style="position: relative; z-index: 2;  text-align:center; padding:20px 0px;"><span style="font-size: 250%; margin-bottom:10px; " class="fas fa-globe-africa"></span><br /><span style="max-width: 98%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Bills</span></div>
</div>


</div>
<!-- 33333333333333333333333333333333333333 -->
<div class="main-2"  style="position: relative;  overflow: hidden;">
<div class="transparent" style="    -ms-filter:'progid:DXImageTransform.Microsoft.Alpha(Opacity=50)';    filter: alpha(opacity=50);    -khtml-opacity: 0.5;    -moz-opacity: 0.5;    opacity: 0.5; z-index:1; background-color: black; height: 820px; width:100%; position: absolute;">&nbsp; </div>

<div style="z-index:1; position: relative; padding:10px; min-height: 400px; color: #214673;">
<div id="pageholder"><?php	include "call/dashboard.php";?></div>
<div style="clear: both;"></div>
</div>

</div>
<div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>
</div>

<!--  #3B3B3B -->
<div class="mainbg" style="background-color:#3B3B3B; border-top: solid 2px gold; padding-top:5px;">
<div style="text-align:center;">
<div style="display: inline-block; margin-right:85px;"><a href="https://play.google.com/store/apps/details?id=com.mcbpay.mcbpay"><img src="theme/classic/images/1.png" style="height: 42px;" /></a></div>
<div style="display: inline-block; margin-right:85px;"><img src="theme/classic/images/2.png" style="height: 42px;"   /></div>
<div style="display: inline-block;"><a href="/setup.exe"><img src="theme/classic/images/3.png" style="height: 42px;"   /></a></div>
<div style="clear: both;"></div>
</div>
</div>



<div style="clear: both;"></div>


<div style="background-color: white;">
<div style="color:black; font-size: 300%; text-align: center; font-weight:700; margin-top:20px; margin-bottom:20px;">Why Choose <strong style="color: #5290F3;">Rechargepro?</strong></div>


<style type="text/css">
.column {
    
    float: left;
    width: 23%;
    padding:0px 1%;
}

/* Clear floats after the columns */
.row:after {
    content: "";
    display: table;
    clear: both;
}
@media screen and (max-width: 600px) {
    .column {
        width: 100%;
        padding:0px;
    }
}
</style>
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px; text-align: center;">
<div class="row">
  <div class="column">
  <div class="fas fa-hashtag" style="color: #5290F3; font-size:300%;"></div>
  <div style="font-size: 200%; font-weight:700; margin:10px 0px;">Low Cost</div>
  <div style="line-height: 155%;">Recharge your phone line instantly. Prepaid and Postpaid lines supported</div>
  </div>
  
  
  <div class="column">  <div class="fas fa-rocket" style="color: #5290F3; font-size:300%;"></div>
  <div style="font-size: 200%; font-weight:700; margin:10px 0px;">Super Fast</div>
  <div style="line-height: 155%;">Our online mobile recharge transaction is completely guaranteed and secure</div></div>
  
  
  <div class="column">  <div class="fab fa-simplybuilt" style="color: #5290F3; font-size:300%;"></div>
  <div style="font-size: 200%; font-weight:700; margin:10px 0px;">Simple</div>
  <div style="line-height: 155%;">Earn reward points & get bonus anytime, anywhere. Renew your tv subscriptions.</div></div>
  
  <div class="column">  <div class="fas fa-fingerprint" style="color: #5290F3; font-size:300%;"></div>
  <div style="font-size: 200%; font-weight:700; margin:10px 0px;">Trusted</div>
  <div style="line-height: 155%;">Recharge your electric meters instantly. Prepaid and Postpaid meters supported</div></div>
</div>
</div>

</div>





<div style="margin-top:20px;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px; text-align: center; color:white;">
<style type="text/css">
.ref_title{font-size: 120%; font-weight:400; margin:20px 0px;}
#ref-1{background-color: #303443; height:580px; width:28%; margin-right:1%; display: inline-table; overflow: hidden;}
#ref-2{background-color: #303443; height:580px; width:28%; margin-right:1%; display: inline-table; overflow: hidden;}
#ref-3{background-color: #303443; height:580px; width:28%; margin-right:1%; display: inline-table; overflow: hidden;}
@media (max-width: 750px) {
.ref_title{font-size: 120%; font-weight:400; margin:10px 0px;}
#ref-1{width:100%; margin-right:0%; height:0; margin-bottom:5px; padding:5px 2%;}
#ref-2{width:100%; margin-right:0%; height:0; margin-bottom:5px; padding:5px 2%;}
#ref-3{width:100%; margin-right:0%; height:0; padding:5px 2%;}
}
</style>

<div id="ref-1">
<img src="/theme/classic/images/Man on Mobile Phone.webp" style="width: 100%; margin-bottom:-20px;" />
<a href="/invite"> <button style="width:50%; cursor:pointer; color:white; background-color:#3880F4; border:none; font-size:120%; padding:10px 20px;">Invite/Refer</button></a>
<div class="ref_title">Refer A Friend & Enjoy up to 5%</div>
<div style="margin:0px 10px;">Refer a friend and Enjoy up to 5%..Earn reward points & get bonus anytime, anywhere</div>
</div>
  
  
<div id="ref-2"> 
<img src="/theme/classic/images/Taking Note.webp" style="width: 100%; margin-bottom:-20px;" />
<a href="/register"><button style="width:50%; cursor:pointer; color:white; background-color:#3880F4; border:none; font-size:120%; padding:10px 20px;">Register</button></a>
<div class="ref_title">Join Us Right Now</div>
<div style="margin:0px 10px;">Join us right now and Earn reward points & get bonus anytime, anywhere</div>
</div>
  
<div id="ref-3"> 
<img src="/theme/classic/images/Los Angeles.webp" style="width: 100%; margin-bottom:-20px;" />
<a href="/find"><button style="width:50%; cursor:pointer; color:white; background-color:#3880F4; border:none; font-size:120%; padding:10px 20px;">Find  Location</button></a>
<div class="ref_title">Find a location close to you</div>
<div style="margin:0px 10px;">Find a location close to you ,our online mobile recharge transaction is completely guaranteed and secure.</div>
</div>
  
</div></div>


<div style="background-color: #F2F3F4;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px;">
<div style="color:black; font-size: 300%; text-align: center; font-weight:700; margin-top:20px; margin-bottom:20px;">Frequently Asked <strong style="color: #5290F3;">Questions</strong></div>

<div style="overflow: hidden;">
<style type="text/css">
.questioncontainer{float: left; width:48%;}
.qa{float:left;}
.qb{float:right;}
@media (max-width: 750px) {
.questioncontainer{float:none; width:100%;}
}
</style>

<div class="questioncontainer qa">


<script type="text/javascript">
function queation(Id){
  $(".answer").hide();
  $("#q"+Id).show();
  
  $(".ques").removeClass("fa-plus-square");
  $(".ques").removeClass("fa-minus-square");
  $(".ques").addClass("fa-plus-square");
  
  $("#qq"+Id).removeClass("fa-plus-square");
  $("#qq"+Id).addClass("fa-minus-square");
    
}
</script>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">Who can be an Agent?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-minus-square" id="qq1" onclick="queation('1')"></div></div>
<div id="q1" class="answer" style="border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Individuals, small scale businesses, companies, institutions etc. can become rechargepro Agents provided they have at least minimum required documents and funds.</div>
</div>

<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">What are the benefits of becoming an Agent?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq2" onclick="queation('2')"></div></div>
<div  id="q2" class="answer" style="display:none;border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;"><ul><li>Get discounts on major recurrent operations cost.</li>
 	<li>You get instant commission for every transaction performed. The more transaction volume you get the more money you make. </li>
 	<li>Additional revenue from commissions and incentives.</li>
 	<li>Recharge multiple lines instantly with one click.</li>
 	<li>Access to detailed accounting records.</li>
 	<li>Fast and reliable with multiple services from one platform that guarantees customer's satisfaction.</li></ul></div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">What do I do when transaction fails?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq3" onclick="queation('3')"></div></div>
<div  id="q3" class="answer" style="display:none; border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Irrespective of the type of transaction, go to history log and click on the red button displayed. This ensures that the transaction is retried without you being debited again. If this doesn't work, call the customer care line to resolve the issue.</div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">DSTV/GOTV account not activated after payment</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq7" onclick="queation('7')"></div></div>
<div  id="q7" class="answer" style="display:none; border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Most issues like this might be due to change in bouquet options in which case, contact the customer service of the service provider.</div>
</div>


</div>
<!-- 222222222222222222222222222222 -->

<div  class="questioncontainer qb">

<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">Clearing the E16 error on DSTV & GoTV after subscribing.</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq5" onclick="queation('5')"></div></div>
<div  id="q5" class="answer" style="display:none; border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;"><strong>A: </strong>The error notification you notice on your TV is usually because of an expired subscription. It only becomes a problem when you still see the E16 error code even though you have active subscription. The reason why this happens is because when you make your payment it's possible that your decoder is turned off- and if that's the case, activating the subscription might be delayed. Hence the E16 error. The following methods can be introduced in solving the above problem;<br /><br />

<strong>A.</strong>	Ensure your smartcard is properly inserted.<br /><br />

<strong>B.</strong>	Send A Text:<br />
	i.  On DSTV send "RESET (Smartcard Number)" to "30333"<br />
	ii. On GOTV send "RESET (Smartcard Number)" to "4688"<br />

<strong>C.</strong>	Get Online (DSTV):<br />
	<strong>i.</strong> Go to DSTV's Self Service portal eazy.dstv.com/Self/Service<br />
	<strong>ii.</strong> Enter your Smartcard number<br />
	<strong>iii.</strong> Enter the captcha<br />
	<strong>iv.</strong> Click "Clear error code"<br /><br />

<strong>D.</strong>	Get Online (GoTV):<br />
	<strong>i.</strong> Go to GoTV Eazy portal eazy.gotvafrica.com/gotv/max<br />
	<strong>ii.</strong> Enter the captcha<br />
	<strong>iii.</strong> Click "Clear error code" <br />

<strong>E.</strong>	Call Customer Care Representative;<br />
* Call DSTV Customer Care Line: 012703232, 08039003788<br />
	* Call GoTV Customer Care Line: 08039044688<br />
	* Toll Free Customer Care Line:  08149860333</div>
</div>

<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">How do I make payment?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq6" onclick="queation('6')"></div></div>
<div  id="q6" class="answer" style="display:none; border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">For instant crediting via Bank transfer/deposit, please use the details below<br/>
<strong>Bank Name </strong>: FIDELITY BANK PLC<br />
<strong>Account Name</strong>: Vertis Technologies Ltd<br />
<strong>Account Number</strong>:6060430194<br />
<strong>Description</strong>: &lt;Your RechargePro Phone Number&gt;
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="width:80%; float: left;">What do i do when i dont get exact value for money on electricity recharge?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq4" onclick="queation('4')"></div></div>
<div  id="q4" class="answer" style="display:none; border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Check the 'Debt Section' of your receipt to verify that you are not owing any arrears. Most time DISCOs start deducting arrears from 4-6 months after installation.<br />
<strong>A (2):</strong> When DISCOs go for inspections they switch meter to appropriate plan, if any error is discovered. Check your receipt for your current plan</div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">Debited but no value received after purchase on a service</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq8" onclick="queation('8')"></div></div>
<div  id="q8" class="answer" style="display:none; border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Refunds are done automatically for failed transactions. However, if the transaction is in red, and no refund, click on the red icon to push the transaction again, you will not be charged.</div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #BBBBBB; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">Airtime purchase but no value received</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq9" onclick="queation('9')"></div></div>
<div  id="q9" class="answer" style="display:none; border: solid 2px #BBBBBB; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Check the airtime balance of the network</div>
</div>












</div>
</div>


</div>
</div>