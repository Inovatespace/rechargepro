<div id="main1" style="padding: 20px;" class="profilebg">

<div style="font-size: 140%; margin-bottom:10px;">ELECTRICITY VENDING</div>
<div style="overflow: hidden;"></div>







<style type="text/css">
.inputholder{float:left; width:48%;}
.inputholder2{margin-right:4%; }
@media only screen and (max-width: 525px) {
    /* For mobile phones: */
.inputholder{float:none; width:100%;}
.inputholder2{margin-right:0%; }
}
</style>



<div style="overflow: hidden;">

<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<select id="utility" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" >
<option  value="" hidden="hidden">Select Utility</option>
</select>
</div>
  
  
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="meter" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;"  placeholder="Enter Meter Number"/>
</div>

</div>





<div style="overflow: hidden;">
<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<input id="amount" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" type="number" placeholder="Enter Amount"/>
</div>
  
  
<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="phone" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" type="tel" placeholder="Enter Phone Number"/>
</div>

</div>





<div style="overflow: hidden;">

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input id="email" class="input" style="padding:20px 1%; margin:0px; float:left; width:99%;" type="email" placeholder="Email Address not compulsory"/>
</div>
  
</div>




<div style="margin-bottom:10px;">Token will be sent to the email address and phone number entered above</div>


<div style="overflow: hidden;">

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<button onclick="sendservice()" id="sendutility" class="mainbg" style=" color:white; cursor:pointer; border:none; padding:20px 1%; margin:0px; float:left; width:100%;">PAY</button>
</div>
  
</div>

<div style="cursor:pointer; margin-bottom:10px; color: maroon;" onclick="$('#main1').hide(); $('#main2').show();">Recover your lost token?</div>

</div>



<!--  -->

<div id="main2" style="padding: 20px; display:none;" class="profilebg">

<div style="font-size: 140%; margin-bottom:10px;">ELECTRICITY VENDING</div>
<div style="overflow: hidden;"></div>
<div style="margin-bottom:10px;">Recover your lost token</div>

<input id="tel" class="input" style="padding:20px 1%; margin:0px; margin-bottom:10px; width:100%;" type="tel" placeholder="Enter Phone Number"/>

<button class="mainbg" id="recover" onclick="recover()" style="cursor:pointer; border:none; padding:20px 1%; margin:0px; float:left; width:100%; margin-bottom:10px; color:white;">Recover</button>

<div style="cursor:pointer; margin-bottom:10px; color: orange;" onclick="$('#main1').show(); $('#main2').hide();">&laquo; Back</div>
</div>