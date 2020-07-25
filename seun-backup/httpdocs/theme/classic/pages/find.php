<?php  
$engine = new engine();
?>
<script type="text/javascript">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>


<style type="text/css">
.downloadlogo{width:30%;}
@media only screen and (max-width: 525px) {
    /* For mobile phones: */
    .downloadlogo{width:90%;}
.downloadtext{display:none;}
}
</style>


<script type="text/javascript">
function searchagent(){
    var state = $("#stateb").val();
    var s = $("#qb").val();
    
       if(empty(s)){
        $.alert("Search field is empty"); return false;
       }
       
       $("#agentholder").html('<img src="/theme/classic/images/camera-loader.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "/theme/classic/pages/findb.php",
                        data: 'q='+s+"&state="+state,
                        cache: false,
                        success: function(html){
                        $("#agentholder").html(html).show();      
                            }
                        });
    
}
</script>


<div style="width:100%; border-top:solid 2px #F94925; border-bottom:solid 2px #F94925;  color:black;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto;">




<div style="font-size: 150%;">How to fund wallet instantly</div>
<div class="nInformation" style="text-align: left;">All our payment options are instant crediting</div>

<div style="text-align: left;">
<ul>
<li>
<strong>Bank Name:</strong>First Bank <br/>
<strong>Account Name:</strong>Vertis Technologies Ltd<br/>
<strong>Account Number:</strong>3132981212 </div>
<strong style="color: red;">NOTE: For instant crediting make bank-deposit/transfer to the account number below, use your rechargepro phone number as narration/description</strong>
</li>
<li>Use the search box below to locate any of our agent, for account topup. This comes at a fee depending on the amount</li>
<li>If your account is not credited instantly please call our support lines or open a support ticket</li>
</ul>

</div>



<div style="text-align: right; margin-top:15px; padding-top:15px; border-top: 1px solid #CCCCCC; overflow: hidden;">
<input class="input" onclick="searchagent()" value="Find Location" style="padding:10px; border: none; cursor:pointer; background-color:#3880F4; color:white; float: right; margin-right:5px" type="submit" />
<input type="text" id="qb" placeholder="Enter location" class="input" style="padding:10px; float: right; margin-right:5px"/>
<select class="input" id="stateb" style="padding:10px; float: right; margin-right:5px">
<option>Abuja</option>
<option>Anambra</option>
<option>Enugu</option>
<option>Akwa Ibom</option>
<option>Adamawa</option>
<option>Abia</option>
<option>Bauchi</option>
<option>Bayelsa</option>
<option>Benue</option>
<option>Borno</option>
<option>Cross River</option>
<option>Delta</option>
<option>Ebonyi</option>
<option>Edo</option>
<option>Ekiti</option>
<option>Gombe</option>
<option>Imo</option>
<option>Jigawa</option>
<option>Kaduna</option>
<option>Kano</option>
<option>Katsina</option>
<option>Kebbi</option>
<option>Kogi</option>
<option>Kwara</option>
<option>Lagos</option>
<option>Nasarawa</option>
<option>Niger</option>
<option>Ogun</option>
<option>Ondo</option>
<option>Osun</option>
<option>Oyo</option>
<option>Plateau</option>
<option>Rivers</option>
<option>Sokoto</option>
<option>Taraba</option>
<option>Yobe</option>
<option>Zamfara</option>
</select>
<div style="float: right; margin-right: 5px;">Search for agent location around you</div>
</div>

<div style="margin-bottom: 20px;" id="agentholder"></div>




</div>
</div>
