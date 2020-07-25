<?php
$engine = new engine();
if(!$engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};

?>

<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px; overflow: hidden;">


<script type="text/javascript">
$(document).ready(function () {

    $("#searchb").keyup(function () {
        var searchbox = $(this).val();
     
        var dataString = 'q=' + searchbox;

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "/theme/classic/pages/invite/inviteb.php",
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


<div class="profilebg" style="overflow:hidden; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Invite Friends</div></div>
<div class="shadow" style="margin-bottom: 10px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="searchb" type="text" placeholder="Name / Email / Mobile" style="padding:5px 5px; width: 90%;" class="input" /></div>

<a style="float: right;" href="myinvite">Click Here to view your invites</a>

</div>



<div class="nInformation">Import Contact from the list below and start earning money</div>


<a href="/yahoo"><img src="theme/classic/images/import/yahoo.jpg" class="profilebg"  style="border:solid 1px #EEEEEE; padding:3px; float:left; margin: 10px 0.5%; overflow:hidden; padding:5px; width:18%;"/></a>

<a href="/google"><img src="theme/classic/images/import/gmail.png"  class="profilebg" style="border:solid 1px #EEEEEE; padding:3px; float:left; margin: 10px 0.5%; overflow:hidden; padding:5px; width:18%;"/></a>


<a class="tunnel" name="/theme/classic/pages/invite/csv/importcontact.php?width=500"><img src="theme/classic/images/import/csv2.png"  class="profilebg" style="border:solid 1px #EEEEEE; padding:3px; float:left; margin: 10px 0.5%; overflow:hidden; padding:5px; width:18%;"/></a>


            

<img src="theme/classic/images/import/share.png"  class="profilebg" style="border:solid 1px #EEEEEE; padding:3px; float:left; margin: 10px 0.5%; overflow:hidden; padding:5px; width:18%;" />


<div style="clear: both;"></div>














<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />








<div style="margin-top:50px;">

<script type="text/javascript" src="/java/jquery.twbsPagination.js"></script>


<?php
$rechargeproemail = $engine->get_session("rechargeproemail");
$rowcount = $engine->db_query("SELECT id FROM myinvite WHERE sentemail = ?", array($rechargeproemail), true);
if($rowcount > 0){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    
   
    
    
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "/theme/classic/pages/invite/inviteb.php",
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

<div id="page-content"><div style="text-align:center; padding:20px;"><img src="/theme/classic/images/rechargepro.gif" width="124" height="124" /></div></div>
<div style="clear: both;"></div>
<ul style="margin-top: 5px;" id="pagination" class="pagination-sm"></ul>
<?php
	}
?>
<?php 	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden; background-color:white;' class=''> 
    <div style='float:left; width:70%;'>
    <div style='font-size:150%;'>You are yet to invite any of your contact, use our contact importer above and start earning money</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>


</div>





















</div>
