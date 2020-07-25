<?php
$engine = new engine();
?>

<style type="text/css">
.transparent0{
    -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";/*old explorer*/
filter: alpha(opacity=0); /* internet explorer */
-khtml-opacity: 0; /* khtml, old safari */
-moz-opacity: 0; /* mozilla, netscape */
opacity: 0; /* fx, safari, opera */
}
</style>

<script>
var img = "";
function uploadimg(url){
    'use strict';
    $('.fileupload').fileupload({
        url: url,
        //acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxFileSize: 10000000, // 5 MB
        dataType: 'json',
        done: function (e, data) {
              
              var fname = data._response.result.files[0].name;
              img = fname;
                $('#previewimg').html('<img src="tmp/'+fname+'?id='+Math.random()+'"  style="height:200px;" />');
                $("#psendholder").show();
        },
        beforeSend : function(xhr, opts){
            $("#previewimg").prepend('<div id="progress" class="progress" style="margin-bottom: 5px;"><div class="progress-bar progress-bar-success"></div></div>');
            },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).on('fileuploaddone', function (e, data) {
        $('#progress .progress-bar').css('width','0%');
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
};
</script>



<div style="float: left; width:49%;">
<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Form</div></div>
<div class="profilebg" id="acholder" style="border:solid 1px #EEEEEE; padding:10px; overflow:hidden;">



<link rel="stylesheet" href="../java/file-upload/css/jquery.fileupload.css"/>
<script src="../java/file-upload/js/vendor/jquery.ui.widget.js"></script>
<script src="../java/file-upload/js/jquery.iframe-transport.js"></script>
<script src="../java/file-upload/js/jquery.fileupload.js"></script>


<!-- The File Upload processing plugin -->
<script src="../java/file-upload/js/jquery.fileupload-process.js"></script>

<!-- The File Upload validation plugin -->
<script src="../java/file-upload/js/jquery.fileupload-validate.js"></script>

<style type="text/css">
.progress {
  height: 20px;
  margin-bottom: 20px;
  overflow: hidden;
  background-color: #f5f5f5;
  -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
          box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
}
.progress-bar {
  float: left;
  width: 0;
  height: 100%;
  font-size: 12px;
  line-height: 20px;
  color: #fff;
  text-align: center;
  background-color: #337ab7;
  -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
          box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .15);
  -webkit-transition: width .6s ease;
       -o-transition: width .6s ease;
          transition: width .6s ease;
}
.progress-striped .progress-bar,
.progress-bar-striped {
  background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
  background-image:      -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
  background-image:         linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
  -webkit-background-size: 40px 40px;
          background-size: 40px 40px;
}
.progress.active .progress-bar,
.progress-bar.active {
  -webkit-animation: progress-bar-stripes 2s linear infinite;
       -o-animation: progress-bar-stripes 2s linear infinite;
          animation: progress-bar-stripes 2s linear infinite;
}
.progress-bar-success {
  background-color: #5cb85c;
}
.progress-bar-success {
  background-image: -webkit-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
  background-image:      -o-linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
  background-image:         linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
}
</style>

<div style="margin-bottom:3%;" class="">
<div style="padding:1%;">
<img src="theme/ubuntu/images/default.png" style="height: 200px;" />

<div style="overflow: hidden; position:relative;">
<input type="file" onclick="uploadimg('plugin/rechargepro_billers/pages/fileprocessor/file.php')"  type="file" name="files[]" data-url="plugin/rechargepro_billers/pages/fileprocessor/file.php" data-form-data='{"script": "true"}' class="fileupload transparent0" style="width: 100%; position:absolute; z-index:2; padding:4px;" />
<div style="background-color: #EDEDED; border:solid 1px #DDDDDD; color:#727272; text-align: center; padding:4px; position:relative; z-index:1;">Browse Logo <strong style="color:brown;">{Compulsory}</strong></div>
</div>

</div>
</div>

<script type="text/javascript">
function updatepreview(Id){
    var val = $("#"+Id).val();
    $("#b"+Id).html(val);
}
</script>

<div style="overflow: hidden; margin-bottom: 10px;"><div>Biller Name <strong style="color:brown;">{Compulsory}</strong></div><div><input style="width: 99%;" type="text" class="input" id="billername" name="billername" onkeyup="updatepreview('billername')"/></div></div>




<script type="text/javascript">
$(document).ready(function() {
    $(".numbers").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
        
    });
    
    
     $("#billercode").keydown(function (e) { if($("#billercode").val().length >= 4){$("#billercode").val("");}});
    
    
    
    
    $('input').on('keypress', function (event) {
    var regex = new RegExp("^[-_ a-zA-Z0-9\s]+$");
    var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
       event.preventDefault();
       return false;
    }
});
});
</script>
<div style="overflow: hidden; margin-bottom: 10px;"><div>Biller Code {Assign a four(4) digits code} <strong style="color:brown;">{Compulsory}</strong></div><div><input min="4" max="4" type="number" style="width: 99%;"  class="input numbers" id="billercode" name="billercode" onkeyup="updatepreview('billercode')"/></div></div>

<div style="overflow: hidden; margin-bottom: 10px;"><div>Primary Field Name <strong style="color:brown;">{Compulsory}</strong></div><div><input style="width: 99%;" type="text" class="input" id="pfieldname" name="pfieldname" onkeyup="updatepreview('pfieldname')"/></div></div>

<div style="overflow: hidden; margin-bottom: 10px;"><div>Agent Share <strong style="color:brown;">{Compulsory}</strong></div><div><input type="number" style="width: 99%;" class="input numbers" id="ashare" name="ashare" onkeyup="updatepreview('ashare')"/></div></div>

<div style="overflow: hidden; margin-bottom: 10px;"><div>Agent Share Type <strong style="color:brown;">{Compulsory}</strong></div><div>
<select class="input" style="width: 99%;" id="astype" name="astype" onchange="updatepreview('astype')">
	<option>Percentage</option>
	<option>Fixed value</option>
</select>
</div></div>
<div style="overflow: hidden; margin-bottom: 10px;"><div>Category <strong style="color:brown;">{Compulsory}</strong></div><div><select class="input" style="width: 99%;" id="category" name="category">
<?php
$row = $engine->db_query2("SELECT subcategory_id,name FROM rechargepro_subcategory",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    $id = $row[$dbc]['subcategory_id']; 
    $name = $row[$dbc]['name']; 
    echo "<option value='$id'>$name</option>";
    }
?>
	
</select></div></div>


<div class="profilebg" style="padding:10px; border: solid 1px #CCCCCC; margin-bottom:10px;">
<script type="text/javascript">
var fsend = "";
function  updatesec(){
var  sfname = $("#sfname").val();
if(!empty(sfname)){
    
if($("#sftype").val() == "select"){
   $("#secholder").html('<div style="overflow: hidden; margin-bottom: 10px;"><div>'+sfname+'</div><div><select  class="input" style="width: 99%;">'+fsend+'</select></div></div>');
 }else{
    $("#secholder").html('<div style="overflow: hidden; margin-bottom: 10px;"><div>'+sfname+'</div><div><input style="width: 99%;" type="text" class="input" /></div></div>');
 }

 }
}


function updatesdrop(){
var fcount = 0;
fsend = "";
var values = $("input[name='valuea[]']").map(function(){return $(this).val();}).get();
$("input[name='keya[]']").each(function(){
    var fkey = $(this).val();
    var fvalue = values[fcount];
    if(!empty(fkey)){
    fsend += "<option value='"+fkey+"'>"+fvalue+"</option>"; 
    }
    fcount++;
    });
    updatesec();
}



function setstype(){
    updatesec();
 if($("#sftype").val() == "select"){
    $("#addsfieldholder").show();
 }else{
    $("#addsfieldholder").hide();
 }
}
</script>
<div style="overflow: hidden; margin-bottom: 10px;"><div>Secondary Field Name <strong style="color:brown;">{Not Compulsory, Please leave blank if not needed}</strong></div><div><input style="width: 99%;" type="text" class="input" id="sfname" name="sfname" onkeyup="updatesec()"/></div></div>
<div style="overflow: hidden; margin-bottom: 10px;"><div>Secondary Field Type</div><div><select onchange="setstype()" id="sftype" name="sftype" class="input" style="width: 99%;">
	<option value="text">Text</option>
	<option value="select">Select</option>
</select></div></div>


<script type="text/javascript">
function addsfield(){
    var html = '<div style="overflow: hidden; margin-bottom: 10px;"><div style="float: left; width:48%;"><input style="width: 99%;" type="text" class="input" id="keya[]" name="keya[]"  onkeyup="updatesdrop()" /></div><div style="float:right; width:48%;"><input style="width: 99%;" type="text" class="input"  onkeyup="updatesdrop()" id="valuea[]" name="valuea[]"/></div></div>';

$("#addsfieldholder").append(html);
}
</script>
<div style="overflow: hidden; display: none;" id="addsfieldholder">
<div style="text-align: right; margin-bottom: 5px;"> <span onclick="addsfield()" class="fa fa-plus-square" style="color: #118E60; font-size: 170%; cursor: pointer;"></span></div>
<div style="overflow: hidden; margin-bottom: 10px;">
<div class="adminheader" style="border-top:solid 1px #CCCCCC; float: left; width:48%;">Select Name</div>
<div class="adminheader" style="border-top:solid 1px #CCCCCC; float: right; width:48%;">Select Value</div>
</div>

<div style="overflow: hidden; margin-bottom: 10px;">
<div style="float: left; width:48%;"><input style="width: 99%;"  onkeyup="updatesdrop()" type="text" class="input" id="keya[]" name="keya[]"/></div>
<div style="float:right; width:48%;"><input style="width: 99%;"  onkeyup="updatesdrop()" type="text" class="input" id="valuea[]" name="valuea[]"/></div>
</div>


</div>
</div>














<div class="profilebg" style="padding:10px; border: solid 1px #CCCCCC; margin-bottom:10px;">
<script type="text/javascript">
var tsend = "";
function  updateter(){
var  tfname = $("#tfname").val();
if(!empty(tfname)){
    
if($("#tftype").val() == "select"){
   $("#terholder").html('<div style="overflow: hidden; margin-bottom: 10px;"><div>'+tfname+'</div><div><select  class="input" style="width: 99%;">'+tsend+'</select></div></div>');
 }else{
    $("#terholder").html('<div style="overflow: hidden; margin-bottom: 10px;"><div>'+tfname+'</div><div><input style="width: 99%;" type="text" class="input" /></div></div>');
 }

 }
}


function updatetdrop(){
var fcount = 0;
tsend = "";
var values = $("input[name='valueb[]']").map(function(){return $(this).val();}).get();
$("input[name='keyb[]']").each(function(){
    var fkey = $(this).val();
    var fvalue = values[fcount];
    if(!empty(fkey)){
    tsend += "<option value='"+fkey+"'>"+fvalue+"</option>"; 
    }
    fcount++;
    });
    updateter();
}



function setttype(){
    updateter();
 if($("#tftype").val() == "select"){
    $("#addtfieldholder").show();
 }else{
    $("#addtfieldholder").hide();
 }
}
</script>
<div style="overflow: hidden; margin-bottom: 10px;"><div>Tertiary Field Name <strong style="color:brown;">{Not Compulsory, Please leave blank if not needed}</strong></div><div><input style="width: 99%;" type="text" class="input" id="tfname" name="tfname" onkeyup="updateter()"/></div></div>
<div style="overflow: hidden; margin-bottom: 10px;"><div>Tertiary Field Type</div><div><select onchange="setttype()" id="tftype" name="tftype" class="input" style="width: 99%;">
	<option value="text">Text</option>
	<option value="select">Select</option>
</select></div></div>


<script type="text/javascript">
function addtfield(){
    var html = '<div style="overflow: hidden; margin-bottom: 10px;"><div style="float: left; width:48%;"><input style="width: 99%;" type="text" class="input" id="keyb[]" name="keyb[]"  onkeyup="updatetdrop()" /></div><div style="float:right; width:48%;"><input style="width: 99%;" type="text" class="input"  onkeyup="updatetdrop()" id="valueb[]" name="valueb[]"/></div></div>';

$("#addtfieldholder").append(html);
}
</script>
<div style="overflow: hidden; display: none;" id="addtfieldholder">
<div style="text-align: right; margin-bottom: 5px;"> <span onclick="addtfield()" class="fa fa-plus-square" style="color: #118E60; font-size: 170%; cursor: pointer;"></span></div>
<div style="overflow: hidden; margin-bottom: 10px;">
<div class="adminheader" style="border-top:solid 1px #CCCCCC; float: left; width:48%;">Select Name</div>
<div class="adminheader" style="border-top:solid 1px #CCCCCC; float: right; width:48%;">Select Value</div>
</div>

<div style="overflow: hidden; margin-bottom: 10px;">
<div style="float: left; width:48%;"><input style="width: 99%;"  onkeyup="updatetdrop()" type="text" class="input" id="keyb[]" name="keyb[]"/></div>
<div style="float:right; width:48%;"><input style="width: 99%;"  onkeyup="updatetdrop()" type="text" class="input" id="valueb[]" name="valueb[]"/></div>
</div>


</div>
</div>


















<div style="overflow: hidden; margin-bottom: 10px;"><div>Return Url <strong style="color:brown;">{Not Compulsory, Please leave blank if not needed}</strong></div><div><input id="rurl" name="rurl" style="width: 99%;" type="text" class="input"/></div></div>
<div style="overflow: hidden; margin-bottom: 10px;"><div>Verify Url <strong style="color:brown;">{Not Compulsory, Please leave blank if not needed}</strong></div><div><input id="vurl" name="vurl" style="width: 99%;" type="text" class="input"/></div></div>


<div style="overflow: hidden; margin-bottom: 10px;">
<input type="button" style="width: 99%; border: none; padding:3px; cursor: pointer;" onclick="save_me()" value="SAVE" class="activemenu shadow"/>
</div>


</div>

</div>


<div style="float: right; width:49%;">
<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Preview</div></div>

<div class="profilebg" id="acholder" style="border:solid 1px #EEEEEE; overflow:hidden;  padding: 10px;">
<div style="margin-bottom: 20px;" id="previewimg"><img src="theme/ubuntu/images/default.png" style="height: 200px;" /></div>

  
<div style="overflow: hidden; margin-bottom: 10px;"><span id="bbillername" style="font-size: 150%;"></span> <span id="bbillercode"></span></div>

<div style="overflow: hidden; margin-bottom: 10px;"><div id="bpfieldname">-</div><div><input style="width: 99%;" type="text" class="input"/></div></div>


<div style="overflow: hidden; margin-bottom: 10px;"><span>Agent Share : </span><span id="bashare"></span> <span id="bastype">Percentage</span></div>

<div style="overflow: hidden; margin-bottom: 10px;" id="secholder"></div>

<div style="overflow: hidden; margin-bottom: 10px;" id="terholder"></div>

</div>
</div>


<script type="text/javascript">
function save_me(){
var billername = $('#billername').val();
var billercode = $('#billercode').val();
var pfieldname = $('#pfieldname').val();
var ashare = $('#ashare').val();
var astype = $('#astype').val();
var category = $('#category').val();



if(empty(img) || empty(billername) || empty(billercode) || empty(pfieldname) || empty(ashare) || empty(astype) || empty(category)){
    $.alert("Please Enter all Compulsory fields");
    return false;
}

var tosend = "img="+img+"&billername="+billername+"&billercode="+billercode+"&pfieldname="+pfieldname+"&ashare="+ashare+"&astype="+astype+"&category="+category;




var sec = "";
var sfname = $('#sfname').val();
if(!empty(sfname)){
var sftype = $("#sftype").val();
sec = sfname+"@"+sftype+"@";
 
var fcount = 0;
var arraya = [];
var values = $("input[name='valuea[]']").map(function(){return $(this).val();}).get();
$("input[name='keya[]']").each(function(){
    var fkey = $(this).val();
    var fvalue = values[fcount];
    if(!empty(fkey)){
    arraya.push(fkey+"="+fvalue); 
    }
    fcount++;
    });
    
    var joined = arraya.join(";");
    sec += joined;
  }
  
  tosend += "&sec="+sec;
  
  
var ter = "";
var tfname = $('#tfname').val();
if(!empty(tfname)){
var tftype = $("#tftype").val();
ter = tfname+"@"+tftype+"@";
 
var fcount = 0;
var arrayb = [];
var values = $("input[name='valueb[]']").map(function(){return $(this).val();}).get();
$("input[name='keyb[]']").each(function(){
    var fkey = $(this).val();
    var fvalue = values[fcount];
    if(!empty(fkey)){
    arrayb.push(fkey+"="+fvalue); 
    }
    fcount++;
    });
        var joined = arrayb.join(";");
    ter += joined;
  }
  
tosend += "&ter="+ter;

var rurl = $('#rurl').val();
var vurl = $('#vurl').val();

tosend += "&rurl="+rurl+"&vurl="+vurl;

            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_billers/pages/pro/newbillerpro.php",
                data: tosend,
                cache: false,
                success: function (html) {
                    if(html == "ok"){
                      window.location.href = "rechargepro_billers";
                      }else{
                        $.alert(html);
                      }
                }
            }); 

}
</script>







