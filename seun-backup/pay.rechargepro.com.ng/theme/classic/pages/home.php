<?php  
$engine = new engine();
?>
<script type="text/javascript">
  jQuery(document).ready(function($){
    $('.tunnel').tunnel();
  });
</script>
<?php
	$rand = 1;//rand(1,2);
?>
<div style=""><!-- background: url(theme/classic/images/bg/<?php echo $rand;?>.jpg) repeat; -->

<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px;">



<script type="text/javascript">
function call_page(Id){
    $('#pageholder').prepend('<div class="pageloader" style="left:40px; top:40px; position:absolute; font-size:200%;"><span class="fa fa-spinner fa-pulse fa-5x fa-fw"></span>Loading...</div>');
    
    var data = "";
    <?php if(isset($_REQUEST['key']) && isset($_REQUEST['cat'])){?> 
    data = {key:"<?php echo $engine->safe_html($_REQUEST['key']);?>",cat:"<?php echo $engine->safe_html($_REQUEST['cat']);?>"};
    <?php }?>
    
    $.post("theme/classic/pages/call/"+Id+".php", data).done(
    function(response){
        
    switch (Id){ 
	case "airtime": $("#breadcrumb").html("Airtime/Data");
	break;

	case "data": $("#breadcrumb").html("Data");
	break;
    	case "sendmoney": $("#breadcrumb").html("Fund Transfer");
	break;

	case "tv": $("#breadcrumb").html("Cable/TV");
	break;
    
	case "utility": $("#breadcrumb").html("Utility");
	break;
    
    case "bills": $("#breadcrumb").html("Bills");
	break;
    }
         location.hash = Id;
          
         //window.history.pushState("QuickPay", "QuickPay", "/index#"+Id);
        $('#pageholder').html(response);
        //alert("gg");
        
        $("html, body").animate({ scrollTop: 0 }, "slow");
     }
   ).error(
    function(jqXHR, textStatus, errorThrown) {
        $('.pageloader').remove();
          $.alert("Please Check your network");
     }
 );
 
}

</script>
<script type="text/javascript">
$(function () {
  
  var hash = location.hash.substr(1);
  switch (hash){ 
	case "airtime": call_page("airtime");
	break;

	case "sendmoney": call_page("sendmoney");
	break;
    	case "data": call_page("data");
	break;

	case "tv": call_page("tv");
	break;
    
	case "utility": call_page("utility");
	break;
    
        
	case "bills": call_page("bills");
	break;
}
        
})
</script>


<!-- 33333333333333333333333333333333333333 -->
<div style="position: relative; float:left;  overflow: hidden; background-color: white; width:35%;" class="radious10">


<div style="z-index:1; position: relative; padding:10px; min-height: 400px; color: #214673;">
<div id="pageholder"><?php	include "call/utility.php";?></div>
<div style="clear: both;"></div>
</div>

</div>


<div style="float: right; overflow: hidden; width:64%; padding-top:30px;">
<div style="background-color:#013299; color:white; padding:10px; margin-bottom:5px;">BECOME AN AGENT</div>
<img src="theme/classic/images/bg/data.jpg" width="100%" />
</div>

<div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>
</div>


<div style="clear: both;"></div>



</div>
</div>