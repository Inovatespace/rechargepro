<?php
require "../../../../engine.autoloader.php";

if(isset($_REQUEST['message'])){
$message = $_REQUEST['message'];
$email = $_REQUEST['email'];

if(!empty($email)){
$engine->db_query("INSERT INTO temp_sms (too,message,cat) VALUES (?,?,?)",array($email,$message,"papro"));
}

echo "ok"; exit;
}



$name = $_REQUEST['name'];
$email = $_REQUEST['email'];
?>
<script type="text/javascript">
function postme(){
        var message = $("#message").val();
        var id = $("#id").val();
        
        if(empty(message)){
         $("#status").html("<span style='color:red;'>Message cannot be blank</div>");
         return false;
        }
        
                        $.ajax({        
                        type: "POST",
                        url: "plugin/admin/pages/system_message/sms.php",
                        data: "id="+id+"&message="+message+"&email=<?php echo $email;?>&name=<?php echo $name;?>",
                        cache: false,
                        success: function(html){
                            html = html.replace(/^\s\s*/,'').replace(/\s*\s$/,'');
                            if(html == "ok"){
                                $("#status").html("<span style='color:green;'>SMS Sent</span>");
                                jQuery.fn.close(120);
                            }else{
                             $("#status").html(html);   
                            } } });
}
</script>
<div class="activemenu" style="margin-left:-5px; margin-right:-5px; margin-top:-15px; padding:5px; text-align:left; ">Email :: <?php echo $name;?></div>



<div  style="overflow:hidden; margin-top:5px; padding: 10px; border: solid 1px black;"> 
<div id="status"></div>

<div style="text-align:left; overflow:hidden; padding:2px 2px 10px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5; margin-bottom:10px;">

<div style="float: left; width:39%;">Subject</div>
<input id="subject" style="width: 60%; float:left;" type="text" class="input" />
<div style="clear: both;"></div>

<input id="email" type="hidden" value="<?php echo $email;?>" />

<textarea id="message" class="input" style="height:90px; width:99%;"></textarea>
<input class="shadow activemenu" style="cursor:pointer; border: none; padding:5px 0px; width:99%;" onclick="postme()" type="submit" value="Send" />





</div>
</div>