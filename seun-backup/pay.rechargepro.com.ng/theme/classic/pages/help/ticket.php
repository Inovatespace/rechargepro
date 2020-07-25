<?php
if(!isset($_SESSION)){include "../../../../engine.autoloader.php";}
$email = $engine->get_session("quickpayemail");
?>








<div id="call">



<div style="margin-bottom: 10px;;"><span class="fas fa-paperclip"></span> With Attachment</div>


 <div style="font-weight:bold; font-size:15px; padding:5px 0.5%; background-color:#E6FEFE; margin-bottom:10px; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="float: left; width:20%; margin-right:1%;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">TRACKING ID</div>
 <div style="float: left; width:55%;">SUBJECT</div>
 <div style="float: right; 20%;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">LAST ACTIVITY</div>
 </div>

<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>
<?php 
$rowcount = $engine->db_query("SELECT id FROM contact_tickets WHERE email = ?", array($email), true);
$t = "";
if(isset($_REQUEST['q'])){
    $q = $_REQUEST['q'];
$status = $_REQUEST['status'];
$t = "&q=$q&status=$status";
}

if($rowcount > 0){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 14);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "theme/classic/pages/help/ticketb.php",
    type: "POST",
    data : "page="+page+"<?php echo $t;?>",
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

<?php }else{ echo '<div class="nWarning" style="text-align:center; background-color:#F7F0C3; border: solid green 1px;">No support ticket found</div>';}?>

    
</div>