<?php
include "../../../engine.autoloader.php";
$email = $engine->get_session("rechargeproemail");

$row = $engine->db_query2("SELECT trackid,locked, id, name,email, dt, ip, message, subject,admin_lock FROM contact_tickets WHERE (id = ? OR trackid=?) LIMIT 1",array($_REQUEST['id'],$_REQUEST['id']));
$thesubject = $row[0]['subject'];
$themessage = htmlentities($row[0]['message']);
$thetrackid = $row[0]['trackid'];
$theid = $row[0]['id'];
$theip = $row[0]['ip'];
$thename = $row[0]['name'];
$theemail = $row[0]['email'];
$thedate = $row[0]['dt'];
$thelocked = $row[0]['locked'];
$admin_lock = $row[0]['admin_lock'];



if (!empty($theid)) {
    
    $islocked = 0;
    
    
    $nowlock = $engine->get_session("adminid")."_".date('Y-m-d H:i:s', strtotime('+5 minutes', strtotime(date("Y-m-d H:i:s"))));
    if(empty($admin_lock)){
    $islocked = 1;
    $engine->db_query2("UPDATE contact_tickets SET admin_lock = ? WHERE id = ? LIMIT 1",array($nowlock,$theid));
    }
    
    if(!empty($admin_lock)){
     $ex = explode("_",$admin_lock);
     if($ex[0] == $engine->get_session("adminid")){
    $islocked = 1;
    $engine->db_query2("UPDATE contact_tickets SET admin_lock = ? WHERE id = ? LIMIT 1",array($nowlock,$theid));
    }
    
    if(strtotime($ex[1]) <= strtotime(date("Y-m-d H:i:s"))){
    $islocked = 1;
     $engine->db_query2("UPDATE contact_tickets SET admin_lock = ? WHERE id = ? LIMIT 1",array($nowlock,$theid));   
    }
    
    }
?>


 <div style="padding:10px; background-color:#E6FEFE; margin-bottom:10px; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="float:left;">Track ID: <?php echo $thetrackid;?></div>
 <div style="float:right;">Date: <?php echo $thedate;?></div>
 <div style="clear: both;"></div>
 
 <div style="font-weight: bold; margin-top:5px; margin-bottom:5px;"><?php echo $thesubject;?></div>
 <div style="margin-bottom:5px;"><?php echo $themessage;?></div>
 <div style=""><?php echo $thename;?></div>
 <div style="font-size:10px;"><img src="plugin/rechargepro_support/pages/images/flag_red.png" /><?php echo $theip;?></div>
 
 
 <div style="overflow: hidden;">
 <?php 
$row = $engine->db_query2("SELECT attachment FROM contact_attachment WHERE postid = ?",array($theid));
 for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
 $attachment = $row[$dbc]['attachment'];
 if(file_exists("../../../../ticket/".$attachment)){
 $path_info = pathinfo("../../../../ticket/".$attachment);
echo '<a target="_blank" href="../../../../ticket/'.$attachment.'"><div class="profilebg radious10 shadow" style="cursor:pointer; margin:3px; font-size:85%; float: left; padding:0px 5px;"><span class="fas fa-paperclip"></span> attachment<span style="text-transform: uppercase;">['.$path_info['extension'].']</span> <span class="fas fa-save"></span></div></a>';} }?>
 </div>
 </div>
 <?php
	}
?>
 <ol class="row" id="updates">
<?php
$color="1";
$row = $engine->db_query2("SELECT name, message, dt, reply_type FROM contact_replies WHERE replyto = ?",array($theid));
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
    }else if($reply_type == "2"){
        ?>
        
         <li id="you" style="color:#842F2F; background-color:white;">
 <div style="padding:0px; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="margin-top:5px;"><strong>Admin comment : <?php echo $subject;?></strong> on <?php echo $date;?></div>
 </div>   
  </li>  
<div style="height: 10px;">&nbsp;</div>

        <?php
    } else{

    
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
    
        if (empty($theid)) {
echo '<div class="nWarning" style="text-align:center; background-color:#F7F0C3; border: solid green 1px;">No support ticket found width this ID '.$_REQUEST['id'].'</div>';	
}
?>

    </ol>
<script type="text/javascript">
function showticket(Id){
$("#call").html('<img src="images/loading.gif"  />');  
var dataString = "id="+Id; 
$.ajax({
type: "POST",
url: "plugin/rechargepro_support/pages/viewticket.php",
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
url: "plugin/rechargepro_support/pages/lock.php",
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
url: "plugin/rechargepro_support/pages/unlock.php",
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
    var tosend = $("#message").html();
    var memo = $("#memo").val();
    var open = $("#open").val();
    
    if(empty(tosend)){
        $.alert("Message cannot be empty"); return false;
    }
$("#call").html('<img src="images/loading.gif"  />');  
var dataString = "comment="+encodeURIComponent(tosend)+"&id=<?php echo $theid;?>&memo="+encodeURIComponent(memo)+"&open="+open+"&name=<?php echo urlencode($thename);?>&email=<?php echo urlencode($theemail);?>&trackid=<?php echo urlencode($thetrackid);?>"; 

$.ajax({
type: "POST",
url: "plugin/rechargepro_support/pages/replypro.php",
data: dataString,
cache: false,
success: function(html){
showticket(<?php echo $theid;?>);
}
});  
}



function canned_message(Id){
    
    var canned = "";
    
    switch (Id){ 
	case "1": canned = "Dear {ticket.name}<br /><br />This transaction failed and has been refunded to your Wallet.<br />Kindly check your wallet.<br /><br />Thank You<br />RechargePro Team";
	break;

	case "2": canned = "Dear {ticket.name}<br /><br />This transaction was successful and the beneficiary got value. <br />Please check our support section for contact details on your service provider, to escalate the issue if it persist.<br /><br />Thank You<br />RechargePro Team";
	break;

	case "3": canned = "Dear {ticket.name},<br /><br />This ticket was previously logged and response was given. kindly refer to your previous ticket to confirm status. <br />If you have any observations as regards the ticket or you want to make any contribution as regards that ticket, kindly make it within that ticket thread.<br /><br />Kindly Note that Opening multiple tickets for the same transaction makes it hard even for you to track.<br /><br />Thank You<br />RechargePro Team";
	break;

	case "4": canned = "Dear {ticket.name}<br /><br />We apologize for your troubles on the platform. However we require you to kindly provide us with the account number, amount and date of the transaction, to enable us assist you.<br /><br />Thank you<br />RechargePro Team";
	break;
    }
    
    return canned;
    
}


function set_canned(){
    var choice = $("#canned").val();
    var returnedmes = canned_message(choice);
    
    
    
    returnedmes = returnedmes.replace("{ticket.name}", "<?php echo $thename;?>");
    
    $("#message").prepend(returnedmes+"<br />");
}
</script>

<?php if($islocked == 1){?>
    <div style="font-size:20px; overflow:hidden;">Internal Memo <span style="font-size:80%;">{message will only be seen by admin}</span></div>
    <textarea style="width: 99.9%; height:100px; margin-bottom:10px;  border: solid 1px green;" class="input" id="memo"></textarea>
    
    <div style="font-size:20px; overflow:hidden;">Reply</div>
<div>
<select onchange="set_canned()"  style="width: 99.9%; padding:3px;;  border: solid 1px green;" id="canned" class="input">
    <option value="0">Canned Response</option>
	<option value="1">You have been refunded please check your wallet</option>
    <option value="2">Successful Tranasaction</option>
    <option value="3">Previously Logged</option>
    <option value="4">No Account Number</option>
</select>
</div>

    <div id="reply" style="color: red; font-weight:bold;"></div>
    <div style="width: 99.9%; height:150px;  border: solid 1px green; overflow-x:hidden; overflow-y: auto;" contenteditable="true" class="input" id="message"></div>
    
    <div><select  style="padding:3px;width: 99.9%; border: solid 1px green;;" class="input" id="open">
	<option value="0">Open</option>
	<option value="1">Close</option>
</select></div>
    
    <input type="submit" onclick="save_reply()" style="margin-top:10px; margin-bottom:50px; width: 99.9%; padding:7px 0px; border:none; background-color:#2B2B8F; color:white" value="Submit"/>
<?php }else{
    
    echo '<div class="nInformation" style="text-align:center; padding:10px;">This Ticket is on Five(5) Minutes lock, to prevent duplicate reply, try again later</div>';
}

	}else{
echo '<div class="nWarning" style="text-align:center; padding:10px;"><img src="plugin/rechargepro_support/pages/images/unlock.png" style="cursor:pointer; vertical-align: middle;" onclick="unlock()" width="15" /> This Ticket is closed, to open <a onclick="unlock()" href="#">click here</a></div>';
	}
?>

