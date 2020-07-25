<script type="text/javascript">
        function setCookie(key, value) {
            var expires = new Date();
            expires.setTime(expires.getTime() + (30 * 24 * 60 * 60 * 1000));
            document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
        }

        function getCookie(key) {
            var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
            return keyValue ? keyValue[2] : null;
        }
        
/*
         function setCookie(key, value) {
                 var expires = new Date();
                 expires.setTime(expires.getTime() + (1 * 24 * 60 * 60 * 1000));
                 document.cookie = key + '=' + value +';path=/'+ ';expires=' + expires.toUTCString();
             }
 */
            //60 * 1000 = 60 second 60* (60 * 1000) = 60 mins which is 1 hour 24* (60* (60 * 1000)) = 1 day which 24 hours 
</script>
<script type="text/javascript">
function empty(varable){
    if(varable == null){return true;}
    var n=varable.replace(" ","");
    if(n.length > 0){return false;}
    return true;
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
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

jQuery(document).ready(function($){
 $(document).mouseup(function (e)
{
    var container = $(".specialhide");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        container.hide(50);
    }
});


$(".specialhide").hover(function()
{
  $(this).show();
},
function()
{
  $(this).hide();
});


    })
</script>
