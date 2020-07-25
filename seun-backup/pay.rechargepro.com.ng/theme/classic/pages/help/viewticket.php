<?php
include "../../../../engine.autoloader.php";
$email = $engine->get_session("quickpayemail");

$row = $engine->db_query("SELECT trackid,locked, id, name, dt, ip, message, subject FROM contact_tickets WHERE (id = ? OR trackid=?) AND email = ? LIMIT 1",array($_REQUEST['id'],$_REQUEST['id'],$email));
$thesubject = $row[0]['subject'];
$themessage = htmlentities($row[0]['message']);
$thetrackid = $row[0]['trackid'];
$theid = $row[0]['id'];
$theip = $row[0]['ip'];
$thename = $row[0]['name'];
$thedate = $row[0]['dt'];
$thelocked = $row[0]['locked'];


if (!empty($theid)) {
    $engine->db_query("UPDATE contact_tickets SET status = '1' WHERE id = ? LIMIT 1",array($theid));
?>


<a style="cursor:pointer; color: black; font-size:120%; font-weight:bold;" href="/support">Home < BACK</a>
 <div style="padding:10px; background-color:#E6FEFE; margin-bottom:10px; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="float:left;">Track ID: <?php echo $thetrackid;?></div>
 <div style="float:right;">Date: <?php echo $thedate;?></div>
 <div style="clear: both;"></div>
 
 <div style="font-weight: bold; margin-top:5px; margin-bottom:5px;"><?php echo $thesubject;?></div>
 <div style="margin-bottom:5px;"><?php echo $themessage;?></div>
 <div style=""><?php echo $thename;?></div>
 <div style="font-size:10px;"><img src="/theme/classic/pages/help/images/flag_red.png" /><?php echo $theip;?></div>
 
 
 <div style="overflow: hidden;">
 <?php 
 $row = $engine->db_query("SELECT attachment FROM contact_attachment WHERE postid = ?",array($theid));
 for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
 $attachment = $row[$dbc]['attachment'];
 if(file_exists("../../../../ticket/".$attachment)){
 $path_info = pathinfo("../../../../ticket/".$attachment);
echo '<a target="_blank" href="../../../../ticket/'.$attachment.'"><div class="profilebg radious10 shadow" style="cursor:pointer; margin:3px; font-size:85%; float: left; padding:0px 5px;"><span class="fas fa-paperclip"></span> attachment<span style="text-transform: uppercase;">['.$path_info['extension'].']</span> <span class="fas fa-save"></span></div></a>';}
}?>

 </div>
 </div>

 <ol class="row" id="updates">
<?php
$color="1";
$row = $engine->db_query("SELECT name, message, dt, reply_type FROM contact_replies WHERE replyto = ? AND reply_type != '2'",array($theid));
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $trackid = $row[$dbc]['name'];
    $subject = $row[$dbc]['message'];
    $date = $row[$dbc]['dt'];
    $reply_type = $row[$dbc]['reply_type'];
    
    if($reply_type == "1"){
                ?>
        
         <li id="you" style="color: #615757; background-color:white;">
 <div style="padding:0px; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="margin-top:5px;"><strong><?php echo $subject;?></strong> on <?php echo $date;?></div>
 </div>   
  </li>  
<div style="height: 10px;">&nbsp;</div>

        <?php
    }else{

    
    if($color==1){
 ?>
 <li class="me">
 <div style="background-color:#FBFBF3; padding:10px;overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="float:left;"><?php echo $trackid;?></div>
 <div style="float:right;"><?php echo $date;?></div>
  <div style="clear: both;"></div>
 
 <div style="margin-top:10px;"><?php echo $subject;?></div>
 </div>   
   </li> 
<div style="height: 10px;">&nbsp;</div>
<?php 
$color="2";
}else{
    
 ?>
  <li id="you">
 <div style="padding:10px; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="float:left;"><?php echo $trackid;?></div>
 <div style="float:right;"><?php echo $date;?></div>
  <div style="clear: both;"></div>
 
 <div style="margin-top:10px;"><?php echo $subject;?></div>
 </div>   
  </li>  
<div style="height: 10px;">&nbsp;</div>
<?php 
$color="1";    
} 

    }  
    }
    
        
?>

    </ol>
<script type="text/javascript">
function showticket(Id){
$("#call").html('<img src="images/loading.gif"  />');  
var dataString = "id="+Id; 
$.ajax({
type: "POST",
url: "theme/classic/pages/help/viewticket.php",
data: dataString,
cache: false,
success: function(html){
$("#call").html(html);
}
});  
}
  
function lock(){
  $.ajax({
type: "POST",
url: "theme/classic/pages/help/lock.php",
data: "id=<?php echo $theid;?>",
cache: false,
success: function(html){
showticket('<?php echo $theid;?>');
}
});    
}

function unlock(){
  $.ajax({
type: "POST",
url: "theme/classic/pages/help/unlock.php",
data: "id=<?php echo $theid;?>",
cache: false,
success: function(html){
showticket('<?php echo $theid;?>');
}
});
}
</script>
    <?php
	if ($thelocked == 0) {
?>


<script type="text/javascript">
function save_reply(){
    var tosend = $("#message").val();
    if(empty(tosend)){
        $("#reply").html("Message cannot be empty"); return false;
    }
$("#call").html('<img src="images/loading.gif"  />');  
var dataString = "comment="+encodeURIComponent(tosend)+"&id=<?php echo $theid;?>"; 

$.ajax({
type: "POST",
url: "theme/classic/pages/help/replypro.php",
data: dataString,
cache: false,
success: function(html){
showticket(<?php echo $theid;?>);
}
});  
}
</script>

    <div style="font-size:25px; overflow:hidden;">
    <div style="float: left;">Reply</div>
    <img src="theme/classic/pages/help/images/unlock.png" onclick="lock()" style="cursor:pointer; float:right; vertical-align: middle;" width="25" /> 
    </div>
    
    <div id="reply" style="color: red; font-weight:bold;"></div>
    <textarea style="width: 99.9%; height:150px; background-color: white;" class="input" id="message"></textarea>
    
    <input type="submit" onclick="save_reply()" style="margin-top:10px; width: 99.9%; padding:7px 0px; border:none; background-color:#2B2B8F; color:white" value="Submit"/>

<?php
	}else{
echo '<div style="text-align:center; padding:10px; background-color:#F7F0C3; border: solid green 1px;"><img src="theme/classic/pages/help/images/unlock.png" style="cursor:pointer; vertical-align: middle;" onclick="unlock()" width="15" /> This Ticked is closed, to open <a onclick="unlock()" href="#">click here</a></div>';
	}
?>


 <?php
	}else{
	   if($engine->get_session("quickpayid")){
echo '<div class="nWarning" style="text-align:center; background-color:#F7F0C3; border: solid green 1px;">No support ticket found width this ID '.$engine->safe_html($_REQUEST['id']).'</div>';	}else{
    echo '<div class="nWarning" style="text-align:center; background-color:#F7F0C3; border: solid green 1px;">Please login to view ticket width this ID '.$engine->safe_html($_REQUEST['id']).'</div>';	
}
}
?>
