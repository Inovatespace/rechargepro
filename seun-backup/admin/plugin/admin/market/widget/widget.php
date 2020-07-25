<div style="margin: 0px 20px; margin-bottom:20px; overflow:hidden; border: 1px solid #F0F0F0;">

<?php	include_once ('plugin/admin/rating/rate.php');?>




<?php
function catigory($catigory){
    switch ($catigory) {

    case "1":
        return "Trending";
        break;

    case "2":
        return "Life Style";
        break;

    case "3":
        return "Technology";
        break;

    case "4":
        return "News";
        break;
        

    case "5":
        return "Tools";
        break;
        
    case "6":
        return "Sport";
        break;
        
    case "7":
        return "Finance";
        break;
        
        
   case "8":
        return "Games";
        break;
        
        
   case "9":
        return "Arts";
        break;
        
   case "10":
        return "Religion";
        break;
        
    case "11":
        return "Finance";
        break;
        
        
   case "12":
        return "Music";
        break;
        
        
   case "13":
        return "Micelenous";
        break;
        

    default:
        return "All";
        break;

}
}
?>

<div class="barmenu whitelink" style="display:none; padding:5px; color:white; overflow:hidden; border-top:solid 1px #CCCCCC;">
<?php echo $subtitle;?>
</div>

<div class="bodybg" style="overflow:hidden;">

<?php
	include $npage;
?>


</div>
</div>