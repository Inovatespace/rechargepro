<?php
$engine = new engine();
if(!$engine->get_session("rechargeproid")){ echo "<meta http-equiv='refresh' content='0;url=/signin&pp=".$engine->url_origin()."'>"; exit;};
$rechargeproid = $engine->get_session("rechargeproid");
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
                url: "/theme/classic/pages/myinvite/myinviteb.php",
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
<div style="float: left;">My Invites</div></div>
<div class="shadow" style="margin-bottom: 10px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="searchb" type="text" placeholder="Name" style="padding:5px 5px; width: 90%;" class="input" /></div>

<a style="float: right;" href="invite">Click Here to invite friends and start earning money</a>

</div>


<div class="nInformation">Invite your friends and earn commission on every transaction performed</div>


<script type="text/javascript" src="/java/jquery.twbsPagination.js"></script>


<?php
$rowcount = $engine->db_query("SELECT rechargeproid FROM rechargepro_account WHERE profile_creator = ?", array($rechargeproid), true);
if($rowcount > 0){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    
   
    
    
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 12);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "/theme/classic/pages/myinvite/myinviteb.php",
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
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>


</div>