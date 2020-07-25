<?php
$thefile = "progress.txt";
if(file_exists($thefile)){

    
$file = fopen($thefile,"r");
$read = fread($file,filesize($thefile));
fclose($file);
$read = explode(" ",$read);
$barpercent = trim($read[0]);

if($barpercent == "done"){
;?>
<div style="margin-bottom: 40px;">
<div style="margin-bottom: 6px;">Upload Progress, Upload Done <img style="vertical-align: middle;" src="images/Clear Green.png" width="32" height="32" /></div>
        <section class="work">
          <div class="ui-progress-bar ui-container" id="progress_bar">
            <div class="ui-progress" style="width: 100%;">
              <span class="ui-label" style="display:none;">
                Loading Resources
                <b class="value">7%</b>
              </span>
            </div>
          </div>
          </section>
          </div>
 <script type="text/javascript">
 jQuery(document).ready(function($){
	intt = window.clearInterval(intt);
    })
</script>
          <?php
          }elseif($barpercent == "error"){?>
            <div style="margin-bottom: 40px;">There was an error uploading the file...</div>
             <script type="text/javascript">
 jQuery(document).ready(function($){
	intt = window.clearInterval(intt);
    })
</script>
<?php            
          }else{
?>
<div style="margin-bottom: 40px;">
<div style="margin-bottom: 6px;">Upload Progress</div>
        <section class="work">
          <div class="ui-progress-bar ui-container" id="progress_bar">
            <div class="ui-progress" style="width: <?php echo $barpercent;?>%;">
              <span class="ui-label" style="display:none;">
                Loading Resources
                <b class="value">7%</b>
              </span>
            </div>
          </div>
          </section>
          </div>
<?php
          }
    }
?>