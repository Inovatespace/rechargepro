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
<div id="topmenu" class="bottom shadow" style="width: 100%; background-color:white; z-index:99;">

<div class="profilebg" style="z-index:5; position:relative; width:100%; margin-bottom:1px; border-bottom: solid 1px #CCCCCC;">
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
<span class="fas fa-phone"></span>
<span style="margin-right: 10px;">0809 386 9012</span>
</div>

<div style="float: right;">
<span style="margin-right: 20px;"><span id="balancetitle"></span> :: &#8358;{BALANCE}</span>
<span class="blacklink">{ACCOUNT}</span>
</div>
</div>
</div>


<div style="z-index:5; position:relative; width:100%;">
<div class="sitewidth" style="margin-left:auto; margin-right:auto; padding:10px 10px; overflow:hidden;">
<a href="/index"><img src="/{SITE_LOCATION}/images/logo.png" height="50" style="float: left; margin-right:3%;" /></a>

<style type="text/css">
#menub{display:none;}
.menu{float:left; padding:5px 10px; border-left: solid 1px #CCCCCC; cursor: pointer;}
.menu .fas{font-size:30px; color:#013299;}
.menu .fab{font-size:30px; color:#013299;}
@media (max-width: 800px) {
    #menu{display:none;}
    #menub{display:block;}
}
</style>
<div style="float: left; margin-top:10px; text-align:center;" id="menu">
<div class="menu"><a  onclick="call_page('utility')"><div class="fas fa-lightbulb"></div><br />POWER</a></div>
<div class="menu"><a  onclick="call_page('airtime')"><div class="fas fa-mobile"></div><br />AIRTIME</a></div>
<div class="menu"><a  onclick="call_page('data')"><div class="fab fa-internet-explorer"></div><br />DATA</a></div>
<div class="menu"><a  onclick="call_page('tv')"><div class="fas fa-tv"></div><br />TV</a></div>
<div class="menu"><a  onclick="call_page('sendmoney')"><div class="fas fa-money-bill-wave-alt"></div><br />FUND TRANSFER</a></div>
</div>


<div style="float: right; margin-top:10px; text-align:center;" id="menu">
<div class="menu"><a href="/dashboard" style="font-size: 140%;">Dashboard</a></div>
</div>

<div style="float: right; margin-top:10px; font-size:200%; cursor: pointer;" id="menub" onclick="$('#langmenu').toggle();" class="fas fa-bars"></div>

</div>
</div>



</div>

<div id="langmenu" class="langmenucontent specialhide" style="display:none; position: absolute; right: 10px; top:90px; z-index: 993;">
<ul>
<li><a href="/dashboard">DASHBOARD</a></li>
<li><a href="/authorisation">HISTORY</a></li>
<li><a href="/support">AGENT</a></li>
<li><a href="/developer">SALES</a></li>
</ul>
</div>

<div  id="content_body" style="margin-top:100px;">{BODY}</div>

<div  style="">{FOOTER}</div>
<div style="clear:both;"></div>






