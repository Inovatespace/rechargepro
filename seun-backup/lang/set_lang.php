<?php
include "../engine.autoloader.php";


if(isset($_REQUEST['lang'])){
    $inTwoMonths = 60 * 60 * 24 * 60 + time();
                switch ($_REQUEST['lang']) {
                case 'es':
                    setrawcookie('language', "es", $inTwoMonths, "/", false);
                    break;

                case 'fr':
                    setrawcookie('language', "fr", $inTwoMonths, "/", false);
                    break;

                default:
                    setrawcookie('language', "en", $inTwoMonths, "/", false);
            }
    
    exit;
}

$language = $engine->getlanguage();
?>
<script type="text/javascript">

function set_language(){
    
var lang = $("#lang").val(); 

$.ajax({
type: "POST",
url: "/lang/set_lang.php",
data: "lang="+lang,
cache: false,
success: function(html){
window.location.reload();
 }
});  
 

}
</script>
<div style="padding: 20px;  overflow: hidden" class="menubody">
<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >
<select id="lang" style="padding:10px 1%; padding-left:50px; margin:0px; float:left; width:100%;" class="input">
<option value="en">ENGLISH</option>
<option value="fr">FRENCH</option>
<option value="es">SPANISH</option>
</select><span class="focus-border"><i></i></span>
</div>

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<div class="container-contact100-form-btn">
<div class="wrap-contact100-form-btn">
<div class="contact100-form-bgbtn"></div>
<button class="contact100-form-btn" onclick="set_language()">
<span> <?php echo $language["{%CHOOSE%}"]; ?> <i class="fa fa-long-arrow-right m-l-7" aria-hidden="true"></i>
</span>
</button>
</div>
</div>
</div>
</div>