<?php 
$engine = new engine();
if(!$engine->get_session("recharge4id")){ echo "<meta http-equiv='refresh' content='0;url=/signin'>"; exit;}; 
?>
<style type="text/css">
.sitewidth{max-width:100%; min-width:250px;}
</style>


<div class="whole_container" style="font-family: 'Poppins', sans-serif; font-weight: 400; overflow: hidden;">


<style type="text/css">
.leftcontainer{ width:15%;}
.rightcontainer{float:left; width:85%; }
#firstleft{margin-left:15%;}

  @media only screen and (max-width:1350px) {
    .leftcontainer{ width:17%;}
.rightcontainer{width:83%; }
    }
    
      @media only screen and (max-width:1193px) {
    .leftcontainer{ width:19%;}
.rightcontainer{width:81%; }
    }
    
  @media only screen and (max-width:1130px) {
    .leftcontainer{ width:20%;}
.rightcontainer{width:80%; }
    }
  @media only screen and (max-width:1028px) {
    .leftcontainer{ width:23%;}
.rightcontainer{width:76%; }
    }
    

</style>

<div class="leftcontainer" style="left:0px; top:70px; bottom:0px; background-color:#0756AD; position: fixed; height:100%; z-index:2;"></div>

<div class="leftcontainer" style="color:white; float:left; background-color:#0756AD; height:100%; position:relative; z-index:3;">
<style type="text/css">
.dashmenu ul{list-style: none; padding:0px; margin: 0px; border-left:1px solid #902922; margin-left: 50px;}
.dashmenu li{padding:10px; padding-left:10px; cursor: pointer;}
.dashmenu ul li:hover{background:#0756AD;}
.dashmenu ul li a{color:white;}
.dashmenu ul li a:link{color:white;}
.dashmenu ul li a:visited{color:white;}
.dashmenu ul li a:hover{color:orange;}

.dashmenuicon{display:none;}
@media (max-width: 870px) {
    .dashmenubody{display:none;}
    .dashmenuicon{display:inline; font-size:250%;}
    .dashmenu ul{border-left:none; margin-left: 5px;}
    }
</style>


<div style="padding:10px 10px 10px 20px; font-size:16px; text-transform: uppercase;" class="whitelink"> <a href="/clientarea" class="whitelink"><span style="margin-right: 10px;" class="fas fa-table"></span><span class="dashmenubody">{%DASHBOARD%}</span><span class="dashmenuicon"></span></a></div>
 <div class="dashmenu">
<ul class="dashmenuul">
<li><a href="/clientarea/history"><span class="dashmenubody">{%TRAN_HISTORY%}</span><span title="{%TRAN_HISTORY%}" class="dashmenuicon fas fa-history"></span></a></li>

<?php
if($engine->get_session("recharge4role") < 3){	
?>
<li><a href="/clientarea/agent"><span class="dashmenubody">{%AGENT_LIST%}</span><span title="{%AGENT_LIST%}" class="dashmenuicon fas fa-user-secret"></span></a></li>
<li><a href="/clientarea/agentanalysis"><span class="dashmenubody">{%SALES_REPORT%}</span><span title="{%SALES_REPORT%}" class="dashmenuicon fas fa-chart-pie"></span></a></li>
<li><a href="/clientarea/commission"><span class="dashmenubody">{%AGENT_COMMISION%}</span><span title="{%AGENT_COMMISION%}" class="dashmenuicon fas fa-list-ul"></span></a></li>
<?php
	}
?>
<li><a href="/clientarea/setting"><span class="dashmenubody">{%SETTING%}</span><span title="{%SETTING%}" class="dashmenuicon fas fa-cogs"></span></a></li>
<li><a href="/logout"><span class="dashmenubody">{%LOGOUT%}</span><span title="{%LOGOUT%}" class="dashmenuicon fas fa-sign-out-alt"></span></a></li>
</ul>
</div>



</div>

<div class="rightcontainer" style="height:100%; font-family: 'Poppins', sans-serif; overflow: hidden;">
  
 
<?php
        $s = "";
        $bc = "";
        if(isset($_REQUEST['s'])){$s = $engine->safe_html($_REQUEST['s']);}
        $s = str_replace("/","",$s);
        
        switch ($s){
        case "ile": $ile = "dashboard/ile.php"; $bc = "{%DASHBOARD%}";
        break;
        
        case "history": $ile = "dashboard/history.php"; $bc = "{%TRAN_HISTORY%}";
        break;
        
        case "agentanalysis": $ile = "dashboard/payment_daily.php"; $bc = "{%SALES_REPORT%}";
        break;        
        
        case "agent": $ile = "dashboard/agent.php"; $bc = "{%AGENT_LIST%}";
        break;   
        
        case "setting": $ile = "dashboard/setting.php"; $bc = "{%SETTING%}";
        break;    
                
        case "commission": $ile = "dashboard/commission.php"; $bc = "{%AGENT_COMMISION%}";
        break; 
        default : $ile = "dashboard/ile.php"; $bc = "{%DASHBOARD%}";
        }
?>



<style type="text/css">
.rotate{
    -moz-transition: all 2s linear;
    -webkit-transition: all 2s linear;
    transition: all 2s linear;
}

.rotate.down{
    -moz-transform:rotate(180deg);
    -webkit-transform:rotate(180deg);
    transform:rotate(180deg);
}
</style>


 
 <div style="background: #F6F6F6; padding:18px 10px; color: black; overflow: hidden; border-bottom: solid 1px #CCCCCC;">
<div style="float: left; width: 49%;  white-space: nowrap; overflow: hidden;  text-overflow: ellipsis;"><span onclick="set_container()" class="fas fa-bars" id="hidemenu" style="cursor: pointer;"></span> <span id="breadcrumb"><?php echo $bc;?></span></div>

<script type="text/javascript">
function set_container(){
    
  if ($(".leftcontainer").is(":visible")) {
  $(".leftcontainer").hide();
  $(".rightcontainer").css("width","100%");
  }else{
  
  $(".leftcontainer").show();
  var f = $(".leftcontainer").width() / $('.leftcontainer').parent().width() * 100;
  f = 100 - f;
  f = f.toFixed(0);
  $(".rightcontainer").css("width",f+"%"); 
  console.log(f);
  }
}
</script>


<?php
	
    $row = $engine->db_query("SELECT ac_ballance,name,currency FROM recharge4_account WHERE recharge4id = ?",array($engine->get_session("recharge4id")));
$name = $row[0]['name'];
$ac_ballance = $row[0]['ac_ballance'];
$currency = $row[0]['currency'];
?>
<div style="float:right; width: 49%; text-align: right;">
<?php echo $name;?> <?php echo $currency;?>:<?php echo $ac_ballance;?>
</div>
</div>


<div id="dashbody">
<?php	include $ile;?>
</div>

 
 
 
 </div>
 
 
 
 
 
 
 
 
 
 
 
 
 
</div>


<div style="clear: both;"></div>
