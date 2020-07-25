<?php
$engine = new engine();




$id = $_REQUEST['id'];



if(isset($_REQUEST['today'])){
$today = $_REQUEST['today'];
}else{
$today = date("Y-m-d");    
}
?>
<script type="text/javascript">
$(document).ready(function () {

    $("#search").keyup(function () {
        var searchbox = $(this).val();
        var active = "0";
        $("input[id='active']:checked").each(function(i){
active = "1";
});
        var dataString = 'q=' + searchbox+"&active="+active;

        if (searchbox == '') {

        } else {

            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_billers/pages/biller_logb.php",
                data: dataString,
                cache: false,
                success: function (html) {
                      $("#pagination").hide();
                      $("#page-content").html(html);

                }


            });
        }
        return false;

    });


});



function setactive(Id,what){
            $.ajax({
                type: "POST",
                url: "plugin/rechargepro_billers/pages/pro/statuschange.php",
                data: "buserid="+Id+"&status="+what,
                cache: false,
                success: function (html) {
                      window.location.reload();
                }
            });     
}
</script>

<link rel="stylesheet" href="java/sort/themes/blue/style.css" type="text/css" id="" media="print, projection, screen" />





<div class="shadow" style="margin: 10px 0px; padding:10px 20px; overflow:hidden; background-color:white;">
<div style="float: left; width:40%;"><input autocomplete="off" id="search" type="text" placeholder="Name / Code" style="width: 90%;" class="input" /></div>


<a href="rechargepro_billers&p=newbiller"><div style="float: right; font-size: 200%; color:#088427;" class="fas fa-plus-square"></div></a>
</div>



<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Billers</div></div>



















<div style="overflow-x:auto;">
<table id="myTable" class="tablesorter">
<thead>
<tr style="text-transform: uppercase;">
<th>#</th>
<th>services code</th>
<th>service name</th>
<th>Return url</th>
<th>verify url</th>
<th>percentage</th>
<th>category</th>
<th>subcategory</th>
<th>status</th>
<th>dateadeded</th>
</tr>
</thead>
<tbody>
<?php
function  category($id){
switch ($id){ 
	case "0": $return = "-";
	break;

	case "1":$return = "ELECTRICITY";
	break;

	case "2":$return = "AIRTIME";
	break;
    
    	case "3":$return = "DATA";
	break;
    
    	case "4":$return = "PIN";
	break;
    
    	case "5":$return = "TV";
	break;
    
    	case "6":$return = "LOTTERY/BETTING";
	break;
    
    	case "7":$return = "BILLS";
	break;

	default :$return = "-";
}

return $return;
}


function  sub_category($id,$engine){
if($id == 0){return "-";}


$row = $engine->db_query2("SELECT name FROM rechargepro_subcategory WHERE subcategory_id = ? LIMIT 1",array($id));

return $row[0]['name'];
}




$row = $engine->db_query2("SELECT id,services_key,service_name,bill_return_url,bill_verify_url,percentage,bill_formular,services_category,service_subcategory,status,dateadeded FROM rechargepro_services WHERE id = ? LIMIT 1",array($id));
$sub = array();
    if(!array_key_exists($row[0]['service_subcategory'],$sub)){
    $sub[$row[0]['service_subcategory']] = sub_category($row[0]['service_subcategory'],$engine); 
    }
    
    $id = $row[0]['id']; 
    $services_key = $row[0]['services_key']; 
    $service_name = $row[0]['service_name']; 
    $bill_return_url = $row[0]['bill_return_url']; 
    $bill_verify_url = $row[0]['bill_verify_url']; 
    $percentage = $row[0]['percentage']; 
    $bill_formular = $row[0]['bill_formular']; 
    $services_category = category($row[0]['services_category']); 
    $service_subcategory = $sub[$row[0]['service_subcategory']];
    $status = $row[0]['status']; 
    $dateadeded = $row[0]['dateadeded'];
    
    switch ($bill_formular){ 
	case "0":
    $what = "%";
	break;
    
	case "1":
    $what = "+";
	break;

      
	default : $what = "";
}


     $st = '<div class="redmenu radious3 shadow" style="margin:5px; padding:2px 4px; overflow:hidden; display:inline-block; ">Not Active</div>'; 
          
    if($status == 1){
     $st = '<div class="greenmenu radious3 shadow" style="margin:5px; padding:2px 4px; display:inline-block; overflow:hidden;">Active</div>';
    }
    
?>
<tr >
<td><img src="../theme/classic/icons/<?php echo $id;?>.jpg" style="padding: 2px; border: solid 1px #EEEEEE;" width="40"/></td>
<td><?php echo $services_key;?> <a href="rechargepro_billers&p=editbiller&id=<?php echo $id;?>"><span class="fas fa-edit"></span></a></td>
<td><?php echo $service_name;?></td>
<td><?php echo $bill_return_url;?></td>
<td><?php echo $bill_verify_url;?></td>
<td><?php echo $percentage;?><?php echo $what;?></td>
<td><?php echo $services_category;?></td>
<td><?php echo $service_subcategory;?></td>
<td><?php echo $st." "; echo $del;?></td>
<td><?php echo $dateadeded;?></td>
</tr>







</tbody>
</table>

</div>





























<div class="profilebg" id="acholder" style="margin-top:50px; border:solid 1px #EEEEEE; overflow:hidden;">

<div class="adminheader shadow" style="overflow:hidden; border-bottom: solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px;">
<div style="float: left;">Transaction Log</div></div>


<script type="text/javascript" src="java/jquery.twbsPagination.js"></script>
<?php $rowcount = $engine->db_query2("SELECT transactionid FROM rechargepro_transaction_log WHERE rechargepro_subservice = ?", array($services_key), true);?>
<script type="text/javascript">
jQuery(document).ready(function($){
  $('#pagination').twbsPagination({
        totalPages: <?php if($rowcount < 1){echo 0;}else{echo ceil($rowcount / 30);}?>,
        visiblePages: 16,
        onPageClick: function (event, page) {
              $.ajax({
    url : "plugin/rechargepro_billers/pages/biller_logb.php",
    type: "POST",
    data : {page:page,key:"<?php echo $services_key;?>"},
    success: function(data, textStatus, jqXHR)
    {
        $("#page-content").html(data);
    }
});
        }
    });
    })
</script>

<div id="page-content"></div>
<div style="clear: both;"></div>
<ul style="margin-left: 10px;" id="pagination" class="pagination-sm"></ul>

<?php 	if($rowcount < 1){  echo "<div style='padding:5%; margin:5%; overflow:hidden;'> 
    <div style='float:left; width:70%;'>
    <div style='font-size:200%;'  class='shuziacolor'>Oops! No results were found</div>
    <div>We're sorry. It seems as though we were not able to locate exactly what you were looking for. Please try your search again or contact one of our team members through the customer care line.</div>
    </div>
    <img src='../theme/classic/images/no-result.png' style='float:right; width:30%;'/>
    </div>";}?>

</div>












