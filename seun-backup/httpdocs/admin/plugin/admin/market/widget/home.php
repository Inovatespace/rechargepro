<?php	include_once ('plugin/admin/market/widget/featured.php');?>

<div style="border-right: 1px solid #CCCCCC; overflow:hidden;">
<!-- start -->
<div class="submenu" style="border-top: 1px solid #CCCCCC; padding: 5px; overflow:hidden;">
<div style="font-size: 16px; font-weight:bold; float:left;">Category</div>
<div style="float: right; overflow:hidden; text-align:right;">
<form method="post">
<input name="q" class="input tsprite" style="font-size:11px; background-color: white; background-position: right -103px; border:solid #CCCCCC 1px; width:350px; height:19px; padding:2px;" id="searchbox" type="text"  value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}" autocomplete="off"/>
</form>
</div>
</div>
<!-- start -->
<div style="background-color: #383838;overflow:hidden;">
<div style="float: left; border-top: 1px solid #CCCCCC; width:20%; font-size:15px;">

<?php
if (!isset($_REQUEST['c'])) {$getc = htmlentities("");} else {$getc = htmlentities($_REQUEST['c']);}

if ($getc == '1' || $getc == '') { $get1 = 'class="activemenu whitelink"';} else {$get1 = 'class="whitelink"';}
if ($getc == '2') { $get2 = 'class="activemenu"';} else {$get2 = '';}
if ($getc == '3') { $get3 = 'class="activemenu"';} else {$get3 = '';}
if ($getc == '4') { $get4 = 'class="activemenu"';} else {$get4 = '';}
if ($getc == '5') { $get5 = 'class="activemenu"';} else {$get5 = '';}
if ($getc == '6') { $get6 = 'class="activemenu"';} else {$get6 = '';}
if ($getc == '7') { $get7 = 'class="activemenu"';} else {$get7 = '';}
if ($getc == '8') { $get8 = 'class="activemenu"';} else {$get8 = '';}
if ($getc == '9') { $get9 = 'class="activemenu"';} else {$get9 = '';}
if ($getc == '10') { $get10 = 'class="activemenu"';} else {$get10 = '';}
if ($getc == '11') { $get11 = 'class="activemenu"';} else {$get11 = '';}
if ($getc == '12') { $get12 = 'class="activemenu"';} else {$get12 = '';}
if ($getc == '13') { $get13 = 'class="activemenu"';} else {$get13 = '';}



?>

<style type="text/css">
<!--
	.catigoryleft{
	   background-color: transparent; border:none; color:white; font-size:15px; cursor:pointer;
	}
-->
</style>

<!-- 1 -->
<div <?php echo $get1;?> style="padding: 5px 5px 5px 18px;"><a href="home?p=4&m=1">All</a></div>
<!-- 2 -->
<div <?php echo $get2;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("1"));?>" />
<input name="c" value="1" type="hidden" />
</form>
</div>
<!-- 3 -->
<div <?php echo $get3;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("2"));?>" />
<input name="c" value="2" type="hidden" />
</form>
</div>
<!-- 4 -->
<div <?php echo $get4;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("3"));?>" />
<input name="c" value="3" type="hidden" />
</form>
</div>
<!-- 5 -->
<div <?php echo $get5;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("4"));?>" />
<input name="c" value="4" type="hidden" />
</form>
</div>
<!-- 6 -->
<div <?php echo $get6;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("5"));?>" />
<input name="c" value="5" type="hidden" />
</form>
</div>
<!-- 7 -->
<div <?php echo $get7;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("6"));?>" />
<input name="c" value="6" type="hidden" />
</form>
</div>
<!-- 8 -->
<div <?php echo $get8;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("7"));?>" />
<input name="c" value="7" type="hidden" />
</form>
</div>
<!-- 9 -->
<div <?php echo $get9;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("8"));?>" />
<input name="c" value="8" type="hidden" />
</form>
</div>
<!-- 10 -->
<div <?php echo $get10;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("9"));?>" />
<input name="c" value="9" type="hidden" />
</form>
</div>
<!-- 11 -->
<div <?php echo $get11;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("10"));?>" />
<input name="c" value="10" type="hidden" />
</form>
</div>
<!-- 12 -->
<div <?php echo $get12;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("11"));?>" />
<input name="c" value="11" type="hidden" />
</form>
</div>
<!-- 13 -->
<div <?php echo $get13;?> style="padding: 5px 5px 5px 10px;">
<form method="post">
<input class="catigoryleft" type="submit" value="<?php echo htmlentities(catigory("12"));?>" />
<input name="c" value="12" type="hidden" />
</form>
</div>
</form>


</div>
<div style="float: right; width:80%; background-color: #FFFFFF; border-top: 1px solid #CCCCCC;">
<div style="padding:10px 0px 10px 20px;">
<div style="font-size: 15px; color:#777777; font-weight:bold; margin-bottom:20px;">Widget &raquo; <?php if (isset($_REQUEST['q'])) { echo htmlentities($_REQUEST['q']);	} else {echo htmlentities(catigory($getc));}?></div>

<script src="java/pagination.js" type="text/javascript"></script>
<link href="css/pagination.css" rel="stylesheet" type="text/css" />
<?php
$transcall ="";
$rowcount = 200;


if ($rowcount > 0) {	
?>
		<script type="text/javascript">
        $(document).ready(function(){
		$("#contentload").load("plugin/admin/market/widget/homeb.php?page=1&<?php echo $transcall;?>");
			
            
            $('#loadinbox').smartpaginator({ totalrecords: <?php echo $rowcount;?>, 
                                      recordsperpage: 10,
                                      length: 4,
                                      first: 'First',
                                      last: 'Last',
                                      go: 'Go',
                                      controlsalways:true,
                                      onchange: function(newPage) {
            $("#contentload").load("plugin/admin/market/widget/homeb.php?page=" + newPage+"&<?php echo $transcall;?>");
             }
            });
            

	
        });
		</script>



<div id="contentload"></div>
<div id="loadinbox">  </div>

<?php
	}else {
echo '<div class="empty" style="text-align:center; background-color:#F7F0C3; border: solid green 1px;">There is no Email on this folder</div>';
}
?>
</div>
</div>
</div>


</div>
<!-- start -->