<?php
header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<script type="text/javascript">
var sending = 0;
function savemea() { //0-0
	var account = $("#account").val();
	var type = $("#type").val();
	var domain = $("#domain").val();
	var url = $("#url").val();
	var max = $("#max").val();

    
	if (empty(account) || empty(type) || empty(domain) || empty(url) || empty(max)) {
		$.alert('All field are compulsory', 'Alert');
		return false;
	}
	if (sending == 1) {
		return false;
	}
    
    
    $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'You are abouth  to authorise '+domain+' ?',
    buttons: {
        confirm: function () {
            
			sending = 1;
			$("#loader").show();
			$.ajax({
				type: "POST",
				url: "plugin/admin/pages/pro/newapipro.php",
				data: "account=" + account + "&type=" + type + "&domain=" + domain + "&url=" + url + "&max=" + max,
				cache: false,
				success: function(html) {
					$("#loader").hide();
					if (html == "ok") {
						jQuery.fn.refresh("admin&p=api");
					} else {
						$("#status").html(html).show();
					}
					sending = 0;
				},
				error: function(mgd) {
					sending = 0;
					$("#loader").hide();
					$.alert('Connection Error', 'Alert');
				}
			});
    
    },
        cancel: function () {
            
        }
    }
});
    
}



</script>
<div class="barmenu" style="text-align: left; padding: 5px; margin: -15px -5px 0px -5px;">New Ticket</div>
<div style="text-align: left; padding:5px 20px;" class="profilebg">

<div id="status" style="color: red; margin-bottom:5px; display:none;"></div>





<div style="overflow: hidden;padding:2px 2px 10px 2px; -moz-box-shadow: 0px 1px 0px 0px #E9E9E9; -webkit-box-shadow: 0px 1px 0px 0px #E9E9E9; box-shadow: 0px 1px 0px 0px #E9E9E9; border-bottom: 1px solid #F5F5E5;   margin-bottom:10px;">
<div style="float: left; width:150px;">Account ID</div><input id="account" type="text" class="input" style="width:200px; float:left;"/>
</div>

<div style="overflow: hidden; margin-bottom: 5px;">
<div style="float: left; width:150px;">API type</div>
<select id="type" class="input" style="width:200px; float:left;">
<option >Web</option>
<option >Mobile APP</option>
<option >Mobile Web</option>
</select> </div>



<div style="overflow: hidden; margin-bottom: 5px;"><div style="float: left; width:150px;">Domain</div><input id="domain" type="text" class="input" style="width:200px; float:left;"/> </div>
<div style="overflow: hidden; margin-bottom: 5px;"><div style="float: left; width:150px;">Return URL</div><input id="url" type="text" class="input" style="width:200px; float:left;"/> </div>

<div style="overflow: hidden; margin-bottom: 5px;"><div style="float: left; width:150px;">Max Request</div><input id="max" type="text" class="input" style="width:200px; float:left;"/> </div>













<div style="overflow: hidden;">
<img id="loader" src="images/loading6.gif" width="105" height="16" style="float: left; display:none;" />
<input class="sbtn shadow activemenu" style="padding:2px 25px; border: none; margin:3px; float:right; cursor: pointer;" onclick="savemea()" type="button" value="Save" /> 
</div>







</div>