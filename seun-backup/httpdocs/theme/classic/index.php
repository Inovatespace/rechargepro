<style type="text/css">
.sitewidth{max-width:1200px; min-width:250px;}
</style>


<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet"/>
<link rel="stylesheet" href="/{SITE_LOCATION}/css/font-awesome/css/all.min.css"/>

<style type="text/css">
#topmenu.bottom {
    position: absolute;
    top: 0;
}
#topmenu.top {
    position: fixed;
    top: 0;
}
</style>

<script type="text/javascript">
var main = function(){
    var menu = $('#topmenu')

    $(document).scroll(function(){
        if ( $(this).scrollTop() >= 5){
        menu.removeClass('bottom').addClass('top')
        } else {
        menu.removeClass('top').addClass('bottom')
        }
    })
}
$(document).ready(main);
</script>


<!--  -->
<div id="topmenu" class="bottom" style="width: 100%; background-color:white; z-index:99;">
<div style="z-index:5; position:relative; width:100%;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px 10px; overflow:hidden;">
<a href="/index"><img src="/{SITE_LOCATION}/images/logo.png" height="50" style="float: left; margin-right:3%;" /></a>

<style type="text/css">
#menub{display:none;}
.menu{float:left; padding:10px;}
@media (max-width: 800px) {
    #menu{display:none;}
    #menub{display:block;}
}
</style>
<div style="float: right; margin-top:10px;" id="menu">
<div class="menu"><a href="/">DASHBOARD</a></div>
<div class="menu"><a href="/authorisation">AUTHORIZATION</a></div>
<div class="menu"><a href="/support">SUPPORT/ENQUIRIES</a></div>
<div class="menu"><a href="/developer">DEVELOPER</a></div>
</div>

<div style="float: right; margin-top:10px; font-size:200%; cursor: pointer;" id="menub" onclick="$('#langmenu').toggle();" class="fas fa-bars"></div>

</div>
</div>


<div class="headerbg shadow" style="z-index:5; position:relative; width:100%; margin-bottom:1px;">
<style type="text/css">
 #balancetitle:after{content: "Balance";}
 #profittitle:after{content: "Profit";}
 #logout{display:inline;}
@media (max-width: 900px) {
    #balancetitle:after{content: "B";}
    #profittitle:after{content: "P";}
    #logout{display:none;}
    }
@media (max-width: 600px) {
    #socialshare1{display:none;}
}
</style>
<div class="sitewidth" style="margin-left:auto; margin-right:auto; font-size:95%; padding:5px 10px; overflow:hidden;">
<div style="float: left;" id="socialshare1">
<span class="fab fa-facebook-f" style="margin-right: 10px;"></span>
<span class="fab fa-twitter" style="margin-right: 10px;"></span>
<span class="fas fa-phone-square"></span>
<span style="margin-right: 10px;">08165080992</span>
<span class="fas fa-print"></span>
<span class="tunnel" style="cursor: pointer;" name="theme/classic/pages/call/receipt.php?width=300">Print Receipt</span>
</div>

<div style="float: right;">
<span style="margin-right: 20px;"><span id="balancetitle"></span> :: &#8358;{BALANCE} - <span id="profittitle"></span> :: &#8358;{PROFIT}</span>
<span class="fas fa-user-circle" style="font-size:150%; margin-right:5px; color: #5290F4;"></span>
<span class="whitelink">{ACCOUNT}</span>
</div>
</div>
</div>
</div>

<div id="langmenu" class="langmenucontent specialhide" style="display:none; position: absolute; right: 10px; top:90px; z-index: 993;">
<ul>
<li><a href="/">DASHBOARD</a></li>
<li><a href="/authorisation">AUTHORISATION</a></li>
<li><a href="/support">SUPPORT/ENQUIRIES</a></li>
<li><a href="/developer">DEVELOPER</a></li>
</ul>
</div>

<div  id="content_body" style="margin-top:100px;">{BODY}</div>

<div  style="">{FOOTER}</div>
<div style="clear:both;"></div>






<script type="text/javascript">function add_chatinline(){var hccid=69472142;var nt=document.createElement("script");nt.async=true;nt.src="https://mylivechat.com/chatinline.aspx?hccid="+hccid;var ct=document.getElementsByTagName("script")[0];ct.parentNode.insertBefore(nt,ct);}
add_chatinline(); </script>