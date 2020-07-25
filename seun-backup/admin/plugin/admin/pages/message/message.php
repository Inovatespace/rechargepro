
<div style="padding: 10px;">

<style type="text/css">
.stats{position: relative; overflow:hidden; background-color:#F6F6F6; border-bottom:1px solid white; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color:#F9F9F9; border-bottom:1px solid white; padding:1px;}
.stats:hover {background: #F3F3F3; color:#F9C93A;}
</style>


<?php
$color=1;
for($i=0;$i<10;$i++){
    

     
    if($color==1){
           
?>
<div class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE;overflow: hidden;">
<div style="float: left; font-size: 15px; color:red;">Subject</div>
<div style="float: right;">Date</div>
</div>
<div style="text-align: justify;">n Java along side How to set classpath for Java in windows and UNIX environment.  I have experience in finance and insurance domain and Java is heavily used in this domain for writing sophisticated Equity, Fixed income trading applications. Most of these investment banks has written test as part of there core Java interview questions and I always find at least one question related to CLASSPATH in Java on those interviews. Java CLASSPATH is one of the most important concepts in Java,  but,  I must say mostly overlooked. This should be the first thing you should learn while writing Java programs because without correct understanding of Classpath in Java you can't </div>
</div>
<?php
	
    $color=2;
}else{ 
?>
<div class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE;overflow: hidden;">
<div style="float: left;">Subject</div>
<div style="float: right;">Date</div>
</div>
<div>n Java along side How to set classpath for Java in windows and UNIX environment.  I have experience in finance and insurance domain and Java is heavily used in this domain for writing sophisticated Equity, Fixed income trading applications. Most of these investment banks has written test as part of there core Java interview questions and I always find at least one question related to CLASSPATH in Java on those interviews. Java CLASSPATH is one of the most important concepts in Java,  but,  I must say mostly overlooked. This should be the first thing you should learn while writing Java programs because without correct understanding of Classpath in Java you can't </div>
</div>
<?php    
$color=1;    
}
	}
?>

</div>