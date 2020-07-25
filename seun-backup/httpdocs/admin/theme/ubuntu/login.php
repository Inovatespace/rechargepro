<link href="{SITE_LOCATION}/css/frontpage.css" rel="stylesheet" type="text/css" />
<div>


	<script type="text/javascript">
        jQuery(document).ready(function($){
  startTime()  
    })

        
        function startTime()
{
var today=new Date();
var y=today.getFullYear();
var mm=today.getMonth();
var d=today.getDay();
var h=today.getHours();
var m=today.getMinutes();
var s=today.getSeconds();
var a= "AM";
if(h > 12){
  a= "PM";  
}
// add a zero in front of numbers<10

var weekday=new Array(7);
weekday[0]="Sun";
weekday[1]="Mon";
weekday[2]="Tue";
weekday[3]="Wed";
weekday[4]="Thur";
weekday[5]="Fri";
weekday[6]="Sat";


var month=new Array(12);
month[0]="Jan";
month[1]="Feb";
month[2]="Mar";
month[3]="Apr";
month[4]="May";
month[5]="Jun";
month[6]="Jul";
month[7]="Aug";
month[8]="Sep";
month[9]="Oct";
month[10]="Nov";
month[11]="Dec";



m=checkTime(m);
s=checkTime(s);
document.getElementById('txt').innerHTML= weekday[d]+" "+month[mm]+" "+d+", "+h+":"+m+":"+s +" "+a;
t=setTimeout(function(){startTime()},500);
}

function checkTime(i)
{
if (i<10)
  {
  i="0" + i;
  }
return i;
}
</script>
        
	

<div style="margin-left: auto; margin-right: auto; width:330px; position:relative;">

<div id="status1" style="display:none; margin-left:-30px; width:400px; top:35px; position:absolute; z-index:3;">
<div id="status2" class="radious10" style="text-align:center; border: solid 1px #E07628;  background-color:#E9AF32; padding:10px; color:white;">{LOGIN_ERROR}</div>
<img style="margin-left:290px; margin-top: 5px;" src="{SITE_LOCATION}/images/baloon.png" />
</div>

<div style="height: 150px;">&nbsp;</div>


<script type="text/javascript">
$(document).ready(function() {
	// validate login form on keyup and submit
	$(".indexloginbutton").click(function (){   

       var username = $("#username").val();
       var password = $("#password").val();
       var returnurl = $("#returnurl").val();
       
       $('#status1').hide(); 
       
       if(username == "" || password == ""){
       $('#status2').html('Invalid login details');
       $('#status1').show(); return false; }
       
       
       $("#loading").html('<img src="{SITE_LOCATION}/images/smallloading.gif" width="16" height="16" /> loading...');
                                  
                        $.ajax({
                        type: "POST",
                        url: "{LOGIN_FORM_LOCATION}",
                        data: 'username='+username+'&password='+password+"&returnurl="+returnurl,
                        cache: false,
                        success: function(html){
                            if(html != "bad"){
                             window.location.href = html;   
                                }else{
                        $("#loading").html('');
                        $('#status1').show()
                        $('#status2').html("Invalid login details"); 
                        }   
                      // if (html == 1) {window.parent.location.href = "webtop"; $('#status2').html('Sucessfull. Redirecting'); return false;} 
                      // if (html == 2) {window.parent.location.href = "index?p=4"; $('#status2').html('Sucessfull. Redirecting'); return false;}  
                     //window.location.href = '';
                            
                       //$('#status2').html(html);
                       
                       return false; 
                        }
                        });
       
       
     return false;  

	});    return false; })
</script>






<div id="loading" style="font-size: 14px;"></div>


<div style="text-align: center;"><img src="{SITE_LOGO}" height="100"  /></div>

<div style="color:white; overflow:hidden; background: url('{SITE_LOCATION}/images/inbg.png') repeat-x; width:339px; height:340px;">
<div style="padding:20px 30px;">

<form method="post" action=""  autocomplete="off">

<div style="margin-top: 20px; color:black; font-weight:bold;">Email address or Username:</div>
<div style="margin-top:5px;">{LOGIN_USERNAME}</div>

<div style="margin-top: 10px; color:black; font-weight:bold;">Password:</div>
<div style="margin-top:5px;">{LOGIN_PASSWORD}</div>


{LOGIN_RETURNURL}



<input type="submit" value="Login" class="shadow radious10 indexloginbutton middlemenu" style="margin-top:20px; margin-bottom:10px; text-align:center; font-weight:bold; border: none; padding:6px 0px; width:99%; cursor:pointer;" />

</form>
</div>
</div>






</div>

<div style="overflow: hidden; position:absolute; bottom:10px; width:100%;">
<div style="background: url('{SITE_LOCATION}/images/inbg.png') repeat-x; height:5px;"></div>
<img  style="margin-left:20px; color:white; float:left; margin-top:10px;" src="{SITE_LOCATION}/images/setting.png" width="20"/>


<div  style="margin-right:20px; color:white; float:right; margin-top:10px;"><span style="font-weight: bold;">ubuntu</span> // <span id="txt"></span></div>	
</div>




</div>