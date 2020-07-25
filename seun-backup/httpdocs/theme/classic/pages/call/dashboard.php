<?php
	//include "engine.autoloader.php";
?>
<style type="text/css">
.dashiconwrapper{background-color:white; float:left; width:42%; margin:2%;  border: solid 1px #E1E1E1;  padding:10px 0.5%; cursor: pointer;}
.dashicon{font-size: 400%;}
.board{width:29.1%; height:145px; margin:10px 1.5%; padding:15px 0.5%;}
.boardheader{font-size:130%; max-width: 90%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 10px; }

@media (max-width: 715px) {
.board{width:45.2%; height:95px; margin:4px 1.5%;}
.boardtitle{display:none;}
.boardheader{font-size:110%; margin-bottom: 0px;  padding-left:1%; }
}

@media (max-width: 500px) {
.board{width:97%; height:65px; margin:4px 1.5%; padding:10px 0.5%; padding-top:0px; margin-top:0px;}
.boardtitle{display:none;}
.boardheader{margin-bottom: 0px; padding-left:1%; }
.dashicon{font-size: 200%;}
.dashiconwrapper{padding:2px 0.5%;}
#emptyholder{display:none;}
}
</style>

<div class="shadow radious5 profilebg board" style="background-color: white;   float:left;">
<div class="boardheader">Transaction History</div>
<div style="overflow: hidden; text-align: center;">
<div  class="dashiconwrapper tunnel" name="theme/classic/pages/call/receipt.php?width=300" ><div  class="fas fa-print dashicon"></div><div style="margin-top: 5px;" class="boardtitle">Re-Print</div></div>
<div class="dashiconwrapper"><a href="/history"><div  class="fas fa-history dashicon"></div><div style="margin-top: 5px;" class="boardtitle">History</div></a></div>
</div>
</div>

<?php $transparent = ""; $authlink = "/authorisation"; if(isset($_SESSION['main'])){ if($_SESSION['main'] == 2){ $transparent = "transparent"; $authlink = "#";}}?>
<div class="shadow radious5 profilebg board <?php echo $transparent;?>" style="background-color: white;  float:left;">
<div  class="boardheader">Authorization</div>
<div style="overflow: hidden; text-align: center;">
<div  class="dashiconwrapper"><a href="<?php echo $authlink;?>"><div  class="fas fa-fingerprint dashicon"></div><div  class="boardtitle" style="margin-top: 5px; max-width: 90%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Account Access</div></a></div>
<div  class="dashiconwrapper transparent"><div  class="fas fa-coins dashicon"></div><div style="margin-top: 5px;" class="boardtitle">API</div></div>
</div>
</div>


<div class="shadow radious5 profilebg board" style="background-color: white; float:left;">
<div  class="boardheader">Referral</div>
<div style="overflow: hidden; text-align: center;">
<div  class="dashiconwrapper"><a href="/myinvite"><div  class="fas fa-network-wired dashicon"></div><div class="boardtitle" style="margin-top: 5px;">My Invite</div></a></div>
<div  class="dashiconwrapper"><a href="/invite"><div  class="fas fa-bullhorn dashicon"></div><div class="boardtitle" style="margin-top: 5px; max-width: 90%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Invite People</div></a></div>
</div>
</div>



<div class="shadow radious5 profilebg board  transparent" style="background-color: white;  float:left;">
<div  class="boardheader " >Recurrent Transaction</div>
<div style="overflow: hidden; text-align: center;">
<div class="dashiconwrapper" style="float:none; width:95%; "><div class="fas fa-calendar-check dashicon">/0</div></div>
</div>
</div>

<?php
$transparent = "transparent";
$link = "";
if($engine->get_session("rechargeproid")){
    
    if($engine->get_session("rechargeprorole") < 3){
     $transparent = "";
     $link = 'href="agent"'; 
    }
    
}
?>
<div class="shadow radious5 profilebg board <?php echo $transparent;?>" style="background-color: white; float:left;">
<div  class="boardheader" >Sales Agent</div>
<div style="overflow: hidden; text-align: center;">
<div  class="dashiconwrapper" style="float:none; width:95%; "><a <?php echo $link;?>><div  class="fas fa-user-secret dashicon">/0</div></a></div>
</div>
</div>

<?php $transparent = "transparent"; if(isset($_SESSION['main'])){ if($_SESSION['main'] == 1){ $transparent = "";}}?>
<div class="shadow radious5 profilebg board <?php echo $transparent;?>" style="background-color: white; float:left;">
<div  class="boardheader">Fund</div>
<div style="overflow: hidden; text-align: center;">
<div  class="dashiconwrapper"><a <?php  if(isset($_SESSION['main'])){ if($_SESSION['main'] == 1){ echo 'class="tunnel"  name="theme/classic/pages/transfer/transfer.php?width=500"';  }}?>><div  class="fas fa-exchange-alt dashicon"></div><div class="boardtitle" style="margin-top: 5px;">Fund Transfer</div></a></div>
<div  class="dashiconwrapper"><a  <?php  if(isset($_SESSION['main'])){ if($_SESSION['main'] == 1){ echo 'class="tunnel"  name="theme/classic/pages/topup/onlinetopup.php?width=500"';  }}?>><div  class="fas fa-credit-card dashicon"></div><div class="boardtitle" style="margin-top: 5px; max-width: 90%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Add Fund</div></a></div>
</div>
</div>

</div>