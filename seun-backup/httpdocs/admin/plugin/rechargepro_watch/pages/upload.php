<?php
include "../../../engine.autoloader.php";
$id = $_REQUEST['id'];
$row = $engine->db_query2("SELECT rechargeproid, name, email, active, created_date, mobile, rechargeprorole, ac_ballance,rechargeproid FROM rechargepro_account WHERE rechargeproid = ? LIMIT 1",array($id));

if(!isset($_SESSION['adminme'])){exit;};

?>


<div class="barmenu" style="padding: 10px; margin:-15px -5px 0px -5px;">Upload Bio data</div>
<div class="profilebg" style="padding: 10px;">


<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="../theme/classic/pages/contact/import/js/vendor/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="../theme/classic/pages/contact/import/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="../theme/classic/pages/contact/import/js/jquery.fileupload.js"></script>



<div class="container">

<input id="fileupload" onclick="load_upload()" type="file" class="input" style="margin-bottom: 5px; width:99%;" />

<div style="margin:5px 0px; text-align: left;"> 
<input name="cat" value="passport" type="radio" id="aa" onclick="clickme()" /><label for="aa"><span></span>Photo</label>
<input name="cat" value="a" type="radio" id="ab" onclick="clickme()" /><label for="ab"><span></span>ID Card</label>
<input name="cat" value="b" type="radio" id="ac" onclick="clickme()" /><label for="ac"><span></span>Utility Bill</label>
<input name="cat" value="c" type="radio" id="ad" onclick="clickme()" /><label for="ad"><span></span>CAC Certificate</label>
<input name="cat" value="d" type="radio" id="ae" onclick="clickme()" /><label for="ae"><span></span>Agent onboarding</label>
</div>

    <!-- The global progress bar -->
    <div id="progress" class="progress">
        <div class="progress-bar progress-bar-success"></div>
    </div>

  
</div>

<div class="nInformation">Supported file types: .jpg, .jpeg, .png .pdf</div>


<script>

var checked_site_radio;

function clickme(){
    checked_site_radio = $('input:radio[name=cat]:checked').val();
}


function load_upload(){
    
    checked_site_radio = $('input:radio[name=cat]:checked').val();
 /*jslint unparam: true */
/*global window, $ */
//$(function () {
   // 'use strict';
    // Change this to the location of your server-side upload handler:
    var url = 'plugin/rechargepro_watch/pages/uploadpro.php';
    $('#fileupload').fileupload({
        formData: {id:"<?php echo $id;?>@"+checked_site_radio},
        url: url,
        dataType: 'json',
        beforeSend: function(xhr, settings) {
                checked_site_radio = $('input:radio[name=cat]:checked').val();
                if(checked_site_radio == undefined)
                {
                    $.alert("please select upload type");
                    return false;
                }
            },
        done: function (e, data) {
            if(data.result.files[0].done.trim() == "ok"){
               
               window.location.reload();
            }else{
                $.alert(data.result.files[0].done.trim());
            }
           // $.each(data.result.files, function (index, file) {
              //  $('<p/>').text(file.name).appendTo('#files');
            //});
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
//});   
}

</script>

<style type="text/css">
progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}.progress{height:20px;margin-bottom:20px;overflow:hidden;background-color:#f5f5f5;border-radius:4px;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}.progress-bar{float:left;width:0;height:100%;font-size:12px;line-height:20px;color:#fff;text-align:center;background-color:#337ab7;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition:width .6s ease;-o-transition:width .6s ease;transition:width .6s ease}.progress-bar-striped,.progress-striped .progress-bar{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);-webkit-background-size:40px 40px;background-size:40px 40px}.progress-bar.active,.progress.active .progress-bar{-webkit-animation:progress-bar-stripes 2s linear infinite;-o-animation:progress-bar-stripes 2s linear infinite;animation:progress-bar-stripes 2s linear infinite}.progress-bar-success{background-color:#5cb85c}.progress-striped .progress-bar-success{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-info{background-color:#5bc0de}.progress-striped .progress-bar-info{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-warning{background-color:#f0ad4e}.progress-striped .progress-bar-warning{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-danger{background-color:#d9534f}.progress-striped .progress-bar-danger{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);
</style>


</div>