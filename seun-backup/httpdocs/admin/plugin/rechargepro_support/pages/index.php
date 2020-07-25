<?php
$engine = new engine();

$department = $engine->get_session("department");


switch ($department){ 
	case "admin": $rowcount = $engine->db_query2("SELECT id FROM contact_tickets WHERE admin_status = '0'", array(), true);
	break;

	case "finance":$rowcount = $engine->db_query2("SELECT id FROM contact_tickets WHERE admin_status = '0' AND category IN (1,3)", array(), true);
	break;

	case "support":$rowcount = $engine->db_query2("SELECT id FROM contact_tickets  WHERE admin_status = '0' AND category IN (2,3)", array(), true);
	break;

	default :$rowcount = 0;
}



$newdate = date("Y-m-d", strtotime("-2 day", strtotime(date("Y-m-d"))));
$lastdate = date("Y-m-d", strtotime("-14 day", strtotime(date("Y-m-d"))));
//$unread = $engine->db_query2("SELECT id FROM contact_tickets WHERE admin_status = ?",array(0),true); //1
$closing = $engine->db_query2("SELECT id FROM contact_tickets WHERE lastupdate < ?",array($newdate),true); //+2 weeks
$allticket = $engine->db_query2("SELECT id FROM contact_tickets",array(),true);
$unresolved = $engine->db_query2("SELECT id FROM contact_tickets WHERE locked = '0'",array(),true); 
?>



<div class="profilebg" style="margin: 10px 0px; overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left; width:15%; border-right:solid 1px #EEEEEE; padding:10px 4.5%; "><span style="color: #428bca;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #428bca; font-size:24px;"><?php echo $allticket;?></span> <small>All Tickets</small></div>

<div style="float: left; width:15%; border-right:solid 1px #EEEEEE; padding:10px 4.5%; "><span style="color: #1aae88;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #1aae88; font-size:24px;"><?php echo $unresolved;?></span> <small>Unresolved</small></div>

<div style="float: left; width:15%; border-right:solid 1px #EEEEEE; padding:10px 4.5%; "><span style="color: #e33244;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #e33244; font-size:24px;"><?php echo $rowcount?></span> <small>Unread</small></div>


<div style="float: right; width:15%; padding:10px 4.5%;"><span style="color: #1ccacc;" class="fa fa-circle-o-notch fa-spin fa-3x"></span> <span style="color: #1ccacc; font-size:24px;"><?php echo $closing;?></span> <small>Closing</small></div>

</div>


<div id="searchbarholder" style="margin-bottom: 20px; overflow: hidden;">
<button class="outline" style="float: right; width:25%; background-color: #0F73C9; color:white; border:none; padding:8px 0.5%;" ><span class="fas fa-search"></span></button>
<input class="input" type="text" id="search" onkeyup="search_ticket()" placeholder="Search Ticket Id/Subject" style="width:74%; border:solid 1px #CCCCCC; float: left; padding:7px 0.5%; margin-right:0.5%;" /> 
</div>


<script type="text/javascript">

function assign_agent(){
    
    
    if($("#agent").val() == "0"){return false;}
    
    var fa = [];
            $.each($("input[type='checkbox']:checked"), function(){            
                fa.push($(this).val());
            });
            
            
            if(fa.length < 1){
                $.alert("Select at least one check box"); return false;
            }
            
            var dataString = "selection="+fa.toString()+"&agent="+$("#agent").val(); 
            $.ajax({
            type: "POST",
            url: "plugin/rechargepro_support/pages/pro/assign_agent.php",
            data: dataString,
            cache: false,
            success: function(html){
            window.location.reload();
            }
            });
 
}



function change_status(){
    
    
    if($("#status").val() == "2"){return false;}
    var fa = [];
            $.each($("input[type='checkbox']:checked"), function(){            
                fa.push($(this).val());
            });
            
            
            if(fa.length < 1){
                $.alert("Select at least one check box"); return false;
            }
            
            var dataString = "selection="+fa.toString()+"&id="+$("#status").val(); 
            $.ajax({
            type: "POST",
            url: "plugin/rechargepro_support/pages/pro/change_status.php",
            data: dataString,
            cache: false,
            success: function(html){
               window.location.reload();
            }
            });
}



function search_ticket(){
$("#acholder").html('<img src="../theme/classic/images/rechargepro.gif"  />');  
//var status = $("#status").val();
var dataString = "q="+encodeURIComponent($("#search").val())+"&status=0"; 
$.ajax({
type: "POST",
url: "plugin/rechargepro_support/pages/ticket.php",
data: dataString,
cache: false,
success: function(html){
$("#call").html(html);
}
});  
}

function showticket(Id){
$("#call").html('<img src="../theme/classic/images/images/rechargepro.gif"  />');  
var dataString = "id="+Id; 
$.ajax({
type: "POST",
url: "plugin/rechargepro_support/pages/viewticket.php",
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




<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>




<div id="call">

<script type="text/javascript">
jQuery(document).ready(function($){
$("#checkAll").click(function(){
    $('input:checkbox').not(this).prop('checked', this.checked);
});
})
</script>

<div style="overflow: hidden; margin-bottom: 10px;">

<div style="float:left;;"><span class="fas fa-paperclip"></span> With Attachment</div>


<div style="float:right;">
<select onchange="assign_agent()"  class="input" id="agent" style="width: 150px; border:solid 1px #CCCCCC; padding:3px; margin:3px;;">
	<option value="0">Assign to Agent</option>
    <?php
$row = $engine->db_query("SELECT name,adminid FROM admin WHERE adminid != '1'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
$adminname = $row[$dbc]['name'];
$adminid = $row[$dbc]['adminid'];
?>
    <option value="<?php echo $adminid;?>"><?php echo $adminname;?></option>
<?php
	}
?>
</select>
</div>

<div style="float:right;">
<select onchange="change_status()" class="input" id="status" style="width: 150px; border:solid 1px #CCCCCC; padding:3px; margin:3px;;">
    <option value="2">Change Status</option>
	<option value="0">Open</option>
	<option value="1">Close</option>
</select>

</div>



</div>


 <div style="font-weight:bold; font-size:15px; padding:5px 0.5%; background-color:#E6FEFE; margin-bottom:10px; overflow:hidden; border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;">
 <div style="float: left; width:20%; margin-right:1%;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><input  type="checkbox" id="checkAll" /><label for="checkAll"><span></span>TRACKING ID</label></div>
 <div style="float: left; width:20%;">NAME</div>
 <div style="float: left; width:35%;">SUBJECT</div>
 <div style="float: right; 20%;  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">LAST ACTIVITY</div>
 </div>


<?php 
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
    url : "plugin/rechargepro_support/pages/ticketb.php",
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

<?php 	}else{ echo '<div class="nWarning" style="text-align:center; background-color:#F7F0C3; border: solid green 1px;">No support ticket found</div>';}?>

    
</div>