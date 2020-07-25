<?php
$engine = new engine();



$recharge4role = $engine->get_session("recharge4role"); 
$profile_creator = $engine->get_session("recharge4id");

if(isset($_REQUEST['today'])){
$today = $engine->safe_html($_REQUEST['today']);
}else{
$today = date("Y-m-d");    
}


function rangeMonth($daclassicr){
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($daclassicr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d 23:23:59', strtotime('last day of this month', $dt));
    return $res;
}
 
$range = rangeMonth($today);
$start = $range['start'];
$end = $range['end'];



?>
<script type="text/javascript">
$(document).ready(function () {

    $("#searchb").keyup(function () {
        var searchbox = $(this).val();
        var active = "0";
        $("input[id='active']:checked").each(function(i){
active = "1";
});
        var dataString = 'q=' + searchbox+"&active="+active;

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "/theme/classic/pages/agentb.php",
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


function updaclassicatus(Id,what){
    
    var status = 1;
    if($("#all"+what+Id).prop("checked") == true){
        status = 2;
    }
    
    var my_proile = "<?php echo $profile_creator;?>";

	if(my_proile != Id){

            $.ajax({
                type: "POST",
                url: "/theme/classic/pages/dashboard/pro/statuschange.php",
                data: "trecharge4id="+Id+"&status="+status+"&what="+what,
                cache: false,
                success: function (html) {
                     window.location.reload();
                }
            }); 
          
	}

}

</script>



<div style="background-color: white;">
<div style="padding:10px; margin-bottom:10px; min-height: 500px;">







<div class="profilebg" style="overflow:hidden; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Agent Account Manager</div></div>
<div class="shadow" style="margin-bottom: 10px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="searchb" type="text" placeholder="Name / Email / Mobile" style="padding:5px 5px; width: 90%;" class="input" /></div>

<?php
	
if(isset($_SESSION['main'])){
    if($_SESSION['main'] == 2){
        
    }else{
     ?>
<button style="cursor:pointer; float: right; border: none; padding:5px 10px; margin:3px;"  name="/theme/classic/pages/dashboard/pro/newprofile.php?width=300" class="tunnel mainbg shadow"><span class="fas fa-user-tie"></span> New Profile</button>
<?php   
    }
}else{
?>
<button style="cursor:pointer; float: right; border: none; padding:5px 10px; margin:3px;"  name="/theme/classic/pages/dashboard/pro/newprofile.php?width=300" class="tunnel mainbg shadow"><span class="fas fa-user-tie"></span> New Profile</button>
<?php
	}
?>
</div>






<div id="acholder" style="overflow:hidden;">


<link rel="stylesheet" href="/java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />

<script type="text/javascript" src="/java/sort/jquery.tablesorter.js"></script>

<script type="text/javascript" src="/java/jquery.twbsPagination.js"></script>
<?php  
switch ($recharge4role){
	case "1":
    $rowcount = $engine->db_query("SELECT recharge4id FROM recharge4_account WHERE profile_agent = ? ", array($profile_creator), true);
	break;

	case "2":
   $rowcount = $engine->db_query("SELECT recharge4id FROM recharge4_account WHERE profile_agent = ?", array($profile_creator), true);
	break;
    
    case "3":
   $rowcount = $engine->db_query("SELECT recharge4id FROM recharge4_account WHERE profile_creator = ?", array($profile_creator), true);
	break;

	default :
    $rowcount = 0;
};


if($rowcount > 0){
?>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "/theme/classic/pages/dashboard/agentb.php",
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

<div id="page-content"></div>
<div style="clear: both;"></div>
<ul style="margin-left: 10px;" id="pagination" class="pagination-sm"></ul>

<?php }	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;' >Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='/theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>




















</div></div>





