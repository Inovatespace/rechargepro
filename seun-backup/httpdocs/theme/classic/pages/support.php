<?php
$engine = new engine();
$email = $engine->get_session("rechargeproemail");
?>

<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px; overflow: hidden;">

<div class="profilebg" style="margin: 10px 0px; overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<?php
$newdate = date("Y-m-d", strtotime("-2 day", strtotime(date("Y-m-d"))));
$lastdate = date("Y-m-d", strtotime("-14 day", strtotime(date("Y-m-d"))));
$unread = $engine->db_query("SELECT id FROM contact_tickets WHERE status = ? AND email = ?",array(0,$email),true); //1
$closing = $engine->db_query("SELECT id FROM contact_tickets WHERE lastupdate < ? AND email = ?",array($lastdate,$email),true); //+2 weeks
$allticket = $engine->db_query("SELECT id FROM contact_tickets WHERE email = ?",array($email),true);
$unresolved = $engine->db_query("SELECT id FROM contact_tickets WHERE email =? AND locked = '0'",array($email),true); 
?>



<div style="float: left; width:15%; border-right:solid 1px #EEEEEE; padding:10px 4.5%; "><span style="color: #428bca;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #428bca; font-size:24px;"><?php echo $allticket;?></span> <small>All Tickets</small></div>

<div style="float: left; width:15%; border-right:solid 1px #EEEEEE; padding:10px 4.5%; "><span style="color: #1aae88;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #1aae88; font-size:24px;"><?php echo $unresolved;?></span> <small>Unresolved</small></div>

<div style="float: left; width:15%; border-right:solid 1px #EEEEEE; padding:10px 4.5%; "><span style="color: #e33244;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #e33244; font-size:24px;"><?php echo $unread;?></span> <small>Unread</small></div>


<div style="float: right; width:15%; padding:10px 4.5%;"><span style="color: #1ccacc;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #1ccacc; font-size:24px;"><?php echo $closing;?></span> <small>Closing</small></div>

</div>



<style type="text/css">
.cont1{float: left; width:57%; overflow:hidden;}
.cont2{float:right; width:40%;}

@media (max-width: 800px) {
    .cont1{width: 100%;}
    .cont2 {width:100%;}
}
</style>

<div class="cont1">

<div id="searchbarholder" style="margin-bottom: 20px; overflow: hidden;">
<button class="outline" style="float: right; width:25%; background-color: #0F73C9; color:white; border:none; padding:8px 0.5%;" ><span class="fas fa-search"></span></button>
<input class="input" type="text" id="search" onkeyup="search_ticket()" placeholder="Search Ticket Id/Subject" style="width:74%; float: left; padding:7px 0.5%; margin-right:0.5%;" /> 
</div>



<div onclick="gohome('newticket')"  style="float:right; cursor:pointer; color: #5290F3; font-size:120%; font-weight:bold;">Creat New Ticket</div>
<div style="clear: both;"></div>



<script type="text/javascript">
function showticket(Id){
$("#call").html('<img src="/theme/classic/images/images/rechargepro.gif"  />');  
var dataString = "id="+Id; 
$.ajax({
type: "POST",
url: "theme/classic/pages/help/viewticket.php",
data: dataString,
cache: false,
success: function(html){
$("#call").html(html);
      window.location.hash = Id;
  //e.preventDefault();
}
});  
}

jQuery(document).ready(function($){
if(window.location.hash) {
 var hash = window.location.hash; 
 hash = hash.replace("#", "");
 showticket(hash);
}
})
</script>


<script type="text/javascript">
function search_ticket(){
$("#acholder").html('<img src="/theme/classic/images/rechargepro.gif"  />');  
//var status = $("#status").val();
var dataString = "q="+encodeURIComponent($("#search").val())+"&status=0"; 
$.ajax({
type: "POST",
url: "theme/classic/pages/help/ticket.php",
data: dataString,
cache: false,
success: function(html){
$("#acholder").html(html);
}
});  
}


function gohome(Id){
$("#acholder").html('<img src="/theme/classic/images/rechargepro.gif"  />');  
var dataString = "id=s"; 
$.ajax({
type: "POST",
url: "theme/classic/pages/help/"+Id+".php",
data: dataString,
cache: false,
success: function(html){
$("#acholder").html(html);
}
});  
}
</script>

<div class="profilebg" id="acholder" style="padding:10px; border:solid 1px #EEEEEE; overflow:hidden;"><?php	include ('theme/classic/pages/help/ticket.php');?></div>



</div>














































<div class="cont2">

<div style="margin-bottom: 20px;  color: #5290F3; font-size:200%; font-weight: bold;">FAQ</div>

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
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">Who can be a rechargepro Agent?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-minus-square" id="qq1" onclick="queation('1')"></div></div>
<div id="q1" class="answer" style="border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Individuals, small scale businesses, companies, institutions etc. can become rechargepro Agents provided they have at least minimum required documents and funds.</div>
</div>

<div style="margin-bottom: 7px;">
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">What are the benefits of becoming an Agent?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq2" onclick="queation('2')"></div></div>
<div  id="q2" class="answer" style="display:none;border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;"><ul><li>Get discounts on major recurrent operations cost.</li>
 	<li>You get instant commission for every transaction performed. The more transaction volume you get the more money you make. </li>
 	<li>Additional revenue from commissions and incentives.</li>
 	<li>Recharge multiple lines instantly with one click.</li>
 	<li>Access to detailed accounting records.</li>
 	<li>Fast and reliable with multiple services from one platform that guarantees customer's satisfaction.</li></ul></div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">What do I do when transaction fails?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq3" onclick="queation('3')"></div></div>
<div  id="q3" class="answer" style="display:none; border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Irrespective of the type of transaction, go to history log and click on the red button displayed. This ensures that the transaction is retried without you being debited again. If this doesn't work, call the customer care line to resolve the issue.</div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">DSTV/GOTV account not activated after payment</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq7" onclick="queation('7')"></div></div>
<div  id="q7" class="answer" style="display:none; border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Most issues like this might be due to change in bouquet options in which case, contact the customer service of the service provider.</div>
</div>



<div style="margin-bottom: 7px;">
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">CLEARING THE E16 ERROR ON DSTV & GoTV AFTER SUBSCRIBING.</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq5" onclick="queation('5')"></div></div>
<div  id="q5" class="answer" style="display:none; border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;"><strong>A: </strong>The error notification you notice on your TV is usually because of an expired subscription. It only becomes a problem when you still see the E16 error code even though you have active subscription. The reason why this happens is because when you make your payment it's possible that your decoder is turned off- and if that's the case, activating the subscription might be delayed. Hence the E16 error. The following methods can be introduced in solving the above problem;<br /><br />

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
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">How do I make payment?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq6" onclick="queation('6')"></div></div>
<div  id="q6" class="answer" style="display:none; border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">For instant crediting via Bank transfer/deposit, please use the details below<br/>
<strong>Bank Name </strong>: FIRST BANK<br />
<strong>Account Name</strong>: Vertis Technologies Ltd<br />
<strong>Account Number</strong>:3132981212<br />
<strong>Description</strong>: &lt;Your RechargePro Phone Number&gt;
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="width:80%; float: left;">WHAT DO I DO WHEN I DON'T GET EXACT VALUE FOR MONEY ON ELECTRICITY RECHARGE?</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq4" onclick="queation('4')"></div></div>
<div  id="q4" class="answer" style="display:none; border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Check the 'Debt Section' of your receipt to verify that you are not owing any arrears. Most time DISCOs start deducting arrears from 4-6 months after installation.<br />
<strong>A (2):</strong> When DISCOs go for inspections they switch meter to appropriate plan, if any error is discovered. Check your receipt for your current plan</div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">Debited but no value received after purchase on a service</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq8" onclick="queation('8')"></div></div>
<div  id="q8" class="answer" style="display:none; border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Refunds are done automatically for failed transactions. However, if the transaction is in red, and no refund, click on the red icon to push the transaction again, you will not be charged.</div>
</div>


<div style="margin-bottom: 7px;">
<div style="border: solid 2px #E4E5E6; padding: 15px 30px; display: block; overflow: hidden;"><div style="float: left;">Airtime purchase but no value received</div> <div style="float: right; cursor:pointer;  width:20%; text-align: right; color: #5290F3;" class="fas ques fa-plus-square" id="qq9" onclick="queation('9')"></div></div>
<div  id="q9" class="answer" style="display:none; border: solid 2px #E4E5E6; border-top:none; padding: 30px; padding-top: 10px; background-color:white;">Check the airtime balance of the network</div>
</div>

</div>


</div></div>