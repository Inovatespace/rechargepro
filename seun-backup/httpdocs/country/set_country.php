<?php
include "../engine.autoloader.php";


if(isset($_REQUEST['c'])){
    $inTwoMonths = 60 * 60 * 24 * 60 + time();
                $c = $_REQUEST['c'];
                
                
                $row = $engine->db_query("SELECT country,iso,services,currency,dial_code FROM recharge4_country WHERE country = ? AND status = '1' LIMIT 1",array($c));
                $country = $row[0]['country'];
                $iso = $row[0]['iso'];
                $services = $row[0]['services'];
                $currency = $row[0]['currency'];
                $dial_code = $row[0]['dial_code'];
                if(!empty($country)){
                $_SESSION['country'] = $country;
                $_SESSION['iso'] = $iso;
                $_SESSION['services'] = $services;
                $_SESSION['currency'] = $currency; 
                $_SESSION['dial_code'] = $dial_code;
                }
    echo "<meta http-equiv='refresh' content='0;url=/index'>"; exit;
    exit;
}

$language = $engine->getlanguage();
?>
<script type="text/javascript">

function set_country(){
    
var country = $("#country").val(); 

$.ajax({
type: "POST",
url: "/country/set_country.php",
data: "country="+country,
cache: false,
success: function(html){
window.location.reload();
 }
});  
 

}
</script>
<script>
var visibilitycount = 0;
function myFunction() {
    visibilitycount = 0;
  // Declare variables
  var input, filter, ul, li, a, i, txtValue;
  input = document.getElementById('myInput');
  filter = input.value.toUpperCase();
  ul = document.getElementById("myUL");
  li = ul.getElementsByTagName('li');

  // Loop through all list items, and hide those who don't match the search query
  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("a")[0];
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
        visibilitycount++;
    //count if 6 none else ""
    if(visibilitycount > 6){
        li[i].style.display = "none";
    }else{
       li[i].style.display = "";  
    }
     
    } else {
      li[i].style.display = "none";
    }
  }
}
</script>
<style type="text/css">
#myUL {
  /* Remove default list styling */
  list-style-type: none;
  padding: 0;
  margin: 0;
}

#myUL li a {
  border: 1px solid #ddd; /* Add a border to all links */
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6; /* Grey background color */
  padding: 5px 12px; /* Add some padding */
  text-decoration: none; /* Remove default text underline */
  font-size: 15px; /* Increase the font-size */
  color: black; /* Add a black text color */
  display: block; /* Make it into a block element to fill the whole list */
}

#myUL li a:hover:not(.header) {
  background-color: #eee; /* Add a hover effect to all links, except for headers */
}

.imgc{margin-right:5px; vertical-align: middle; width:25px;}
</style>
<div style="padding: 20px; overflow: hidden;" class="menubody">
<div class="nInformation">Search over 80 countries</div>
<div class="inputholder inputholder2" style="overflow: hidden; margin-bottom:10px;" >

<div class="inputholder" style="overflow: hidden; margin-bottom:10px;" >
<input  id="myInput" onkeyup="myFunction()" class="input" style="padding:10px 1%; margin:0px; float:left; width:100%;" placeholder="Search Country"/>
<span class="focus-border"><i></i></span>
</div>
<div style="clear: both;"></div>
<div style="height: 350px; overflow-y: scroll;;">
<ul id="myUL">
<?php
$ds = "";
$row = $engine->db_query("SELECT country,iso,services,currency FROM recharge4_country WHERE status = '1'",array());
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
    //if($dbc > 6){$ds = "display:none;";};
?>
  <li style="<?php echo $ds;?>"><a href="/country/set_country.php?c=<?php echo $row[$dbc]['country'];?>"><img class="imgc" src="/theme/classic/flag/<?php echo $row[$dbc]['country'];?>.png" /> <?php echo $row[$dbc]['country'];?></a></li>
  <?php
	}  
?>
</ul>
</div>
</div>


</div>