<?php
if(!isset($_SESSION)){include "../../../engine.autoloader.php";}
?>





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
$q = $_REQUEST['q'];
$rowcount = $engine->db_query2("SELECT id FROM contact_tickets WHERE (trackid LIKE ? OR subject = ?) LIMIT 50", array("%$q%","%$q%"), true);

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
    data : "page="+page+"&q=<?php echo $q;?>",
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