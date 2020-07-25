$(document).ready(function()
{

$(".step").click(function(){


var element = $(this);
var Id = element.attr("title");
var Name = element.attr("name");
if (Id == "yes") {
var newincrement = parseInt($("#increment"+Name).html()) + 1;
	} else {
var newincrement = parseInt($("#increment"+Name).html()) - 1;
}

var ratename = $('#ratename').val();
var rateid = element.attr("theid");
var dataString = 'name=' + Name + '&type=' + Id +'&ratename=' + ratename + '&rateid=' + rateid;

console.log(dataString);

$("#spinner"+Name).fadeIn(400).html('<img src="plugin/admin/rating/images/loading.gif" border="0" align="absmiddle"> loading.....');


$.ajax({
type: "POST",
url: "plugin/admin/rating/rate.php",
data: dataString,
cache: false,
success: function(html){
$(".step"+Name).attr("disabled", "disabled");
$("#increment"+Name).html(newincrement);
$("#Yes"+Name).attr("src", "plugin/admin/rating/images/rb1.png");
$("#No"+Name).attr("src", "plugin/admin/rating/images/lb1.png");
$("#background"+Name).css({'width' : newincrement+'px'});
$("#spinner"+Name).html(html);
}
});


return false;});


});