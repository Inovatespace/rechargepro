<script type="text/javascript">
function empty(varable){
    if(varable == null){return true;}
    var n=varable.replace(" ","");
    if(n.length > 0){return false;}
    return true;
}

$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}//$.urlParam('p')

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

jQuery(document).ready(function($){
 $(document).mouseup(function (e)
{
    var container = $(".specialhide");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        container.hide(50);
    }
});

//$(document).mouseover(function (e)
//{
// var container = $("#content_body");
//if (container.is(e.target))
// {
//$(".specialhide").hide(50);
// }
//});



$(".specialhide").hover(function()
{
  $(this).show();
},
function()
{
  $(this).hide();
});


    })


Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };


</script>

<script type="text/javascript">
	function checkStrength(password)
	{
		//initial strength
		var strength = 0
		
		//if the password length is less than 6, return message.
		if (password.length < 6) { 
			//$('#result').removeClass()
			//$('#result').addClass('short')
			return 'Too short' 
		}
		
		//length is ok, lets continue.
		
		//if length is 8 characters or more, increase strength value
		if (password.length > 7) strength += 1
		
		//if password contains both lower and uppercase characters, increase strength value
		if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))  strength += 4
		
		//if it has numbers and characters, increase strength value
		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  strength += 4 
		
		//if it has one special character, increase strength value
		if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  strength += 4
		
		//if it has two special characters, increase strength value
		if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
		
		//now we have calculated strength value, we can return messages
		
		//if value is less than 2
		if (strength < 9 )
		{
			//$('#result').removeClass()
			//$('#result').addClass('weak')
			return 'weak'			
		}
		else if (strength == 9 )
		{
			//$('#result').removeClass()
			//$('#result').addClass('good')
			return 'good'		
		}
		else
		{
			//$('#result').removeClass()
			//$('#result').addClass('strong')
			return 'strong'
		}
	}
</script>



<?php // if($engine->config("messaging_alert")){?>
<script type="text/javascript">
	$(function(){
  
  //element cache
  var $nnAlert = $('.nn-alert-container');
  
  //.on instead of .click for live binding
  $nnAlert.on('click', '.nn-alert', function(){
     hideAlert($(this));
  });
  
  function bindAnimationEnd(){
    //listen to animation end, then hide the element definitely
    $('.nn-alert').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
      $(this).html('');
      $(this).addClass('hide');
    });
  }
    
  //one call for handwritten html alerts
  bindAnimationEnd();
  
  
  function createAlert(color,text){
    alertTemplate = '<div class="nn-alert '+color+'"><div>'+text+'</div></div>';
    $(alertTemplate).fadeIn().appendTo('.nn-alert-container');
    bindAnimationEnd();
  }

  function hideAlert($alert){
    $alert.addClass('animated bounceOutLeft');
  }
   

  var cnt = 2;
  
  //<div class="nn-alert yellow">{LANMAIN_WELCOME} :: {LOGIN_NAME}</div>
  
  
  
  
  //create alert with random icon 
  setInterval(function(){
    hideAlert($('.nn-alert').eq(cnt));
    //createAlert("pink","shola");
    cnt++;
  },7000);
  //demo end
});
</script>
<?php //} ?>