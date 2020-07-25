<?php
require "../../../resource.php";
require "../../../plugin/parking_core/parking_core.php";
$CONN = $resource->sql_db();
require('fpdf.php');
include('../barcode/code128.class.php');

$today = date("Y-m-d");
function rangeMonth($datestr) {
    date_default_timezone_set(date_default_timezone_get());
    $dt = strtotime($datestr);
    $res['start'] = date('Y-m-d', strtotime('first day of this month', $dt));
    $res['end'] = date('Y-m-d', strtotime('last day of this month', $dt));
    return $res;
    }
    


$carnumber = $_REQUEST['id'];
$campresult = $CONN->prepare("SELECT enforcer,offence,location,date,towaway,V_type,payment_status,v_number,longitude,latitude FROM customer_care WHERE v_number = ?  LIMIT 1");
$campresult->execute(array($carnumber));
$rowcam = $campresult->fetch(PDO::FETCH_ASSOC);
$enforcer = $rowcam['enforcer'];
$towaway = $rowcam['towaway'];
$offencedate = date("Y-m-d", strtotime("+0 Day", strtotime($rowcam['date'])));
$location = $rowcam['location'];
$offence = $rowcam['offence'];
$longitude = $rowcam['longitude'];
$latitude = $rowcam['latitude'];
        
$pcn = date("dm", strtotime("+0 Day", strtotime($rowcam['date']))).preg_replace("/[^0-9,.]/", "", $carnumber);

switch ($offence){
case "Green verge": $fine = 15000;
break;

case "Refusal to pay": $fine = 5000;
break;

case "Double Parking": $fine = 5000;
break;

case "Expired Tickets": $fine = 5000;
break;

case "No Parking": $fine = 15000;
break;

case "Walk Way": $fine = 15000;
break;

case "Wrong Parking": $fine = 5000;
break;

}


        function dome($username){
        $barcode = new phpCode128($username, 120, 'verdana.ttf', 18);
        $barcode->setShowText(false);
        $barcode->setPixelWidth(2);
        $barcode->setBorderWidth(0);
        $barcode->setBorderSpacing(0);
        $barcode->setEanStyle(false);
        $barcode->setTextSpacing(20);
        $barcode->setAutoAdjustFontSize(true);
        $barcode->saveBarcode("1.png");
        //echo "<img src='parking/$username.png'>";
        //echo "<div style='font-size:35px;'>".$username."</div>";
        }

dome($carnumber);



class PDF extends FPDF
{

function LoadData($file)
{
   
   $file = explode("@",$file);
    foreach($file as $line)
        $data[] = explode(';',trim($line));
    return $data;
}

// Simple table
function BasicTable($header, $data)
{
    // Header
    foreach($header as $col)
        $this->Cell(40,7,$col,1);
    $this->Ln();
    // Data
    foreach($data as $row)
    {
        foreach($row as $col)
            $this->Cell(40,6,$col,1);
        $this->Ln();
    }
}

// Better table
function ImprovedTable($header, $data)
{
    // Column widths
    $w = array(40, 35, 40, 45);
    // Header
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    // Data
    foreach($data as $row)
    {
        $this->Cell($w[0],6,$row[0],'LR');
        $this->Cell($w[1],6,$row[1],'LR');
        $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R');
        $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R');
        $this->Ln();
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
}

// Colored table
function FancyTable($header, $data)
{
    // Colors, line width and bold font
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Header
    $w = array(58, 42, 25,22,43);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
    $fill = false;
    foreach($data as $row)
    {
        $this->Cell($w[0],5,$row[0],'LR',0,'L',$fill);
        $this->Cell($w[1],5,$row[1],'LR',0,'L',$fill);
        $this->Cell($w[2],5,$row[2],'LR',0,'L',$fill);
        $this->Cell($w[3],5,$row[3],'LR',0,'L',$fill);
        $this->Cell($w[4],5,$row[4],'LR',0,'R',$fill);
        $this->Ln();
        $fill = !$fill;
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T','');
}


function totaltable($header, $data)
{
    // Colors, line width and bold font
    $this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Header
    $w = array(63, 63, 63);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(224,235,255);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
    $fill = false;
    foreach($data as $row)
    {
        $this->Cell($w[0],5,$row[0],'LR',0,'L',$fill);
        $this->Cell($w[1],5,$row[1],'LR',0,'L',$fill);
        $this->Cell($w[2],5,number_format($row[2]),'LR',0,'R',$fill);
        $this->Ln();
        $fill = !$fill;
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
}

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();


$pdf->Image('logo.png',10,6,30);
$pdf->Image('1.png',10,35,60);
$pdf->SetFont('Arial','',15);
$pdf->SetTextColor(220,50,50);
$pdf->Cell(300,10,'NOTICE OF VIOLATION',0,0,'C');
$pdf->Cell(-300,23,strtoupper($offence).' OFFENCE',0,0,'C');
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('Arial','',12);
$pdf->Ln(18);
$pdf->Cell(115);
$pdf->Cell(75,5,'PCN Number    ::    '.$pcn,1,30,'L');
$pdf->Cell(75,5,'Car Number      ::    '.$carnumber,1,30,'L');
$pdf->Cell(75,5,'Fine                   ::    N'.$fine,1,30,'L');
$pdf->Cell(75,5,'Offence Date    ::    '.$offencedate,1,30,'L');
$pdf->Cell(75,5,'Fine Due Date  ::    '.$offencedate,1,30,'L');
$pdf->Cell(75,5,'Location            ::    '.$parking_core->limit_text($location,0,18),1,30,'L');
    // Line break
$pdf->Ln(2);


$pdf->SetFont('Arial','',10);
$pdf->Cell(30,5,"Please take notice that, as the registrant of the vehicle appearing in the photo Above, you are liable to pay a fine in the ",0,1,'L');  
$pdf->Cell(60,5,"amount indicated above Pursuant to Section 97 of the Federal Capital Road Transport Regulations 2005, You are liable ",0,1,'L');  
$pdf->Cell(60,5,"for the fine because at the time, date and location indicated above, the driver of your vehicle, in violation of Section 97 of ",0,1,'L');  
$pdf->Cell(60,5,"the Federal Capital Road Transport Regulations 2005, committed the offence stated above. You must pay your fine by the ",0,1,'L');  
$pdf->Cell(100,5,"payment due date indicated above,  or pay a demurrage fine of "); 
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,5,"One Thousand Naira (N1000)");
$pdf->SetFont('Arial','',10); 
$pdf->Cell(50,5,"per night"); 


$internet = 1;

if($internet == 0){
if(file_exists("../evidence/".$carnumber."b.jpg")){
$pdf->Image("../evidence/".$carnumber.'a.jpg',10,87,0,70);
}

if(file_exists("../evidence/".$carnumber."b.jpg")){
$pdf->Image("../evidence/".$carnumber.'b.jpg',70,87,0,70);
}
}else{   
if(file_exists("../evidence/".$carnumber."a.png")){
//$source = imagecreatefromjpeg("../evidence/".$carnumber."a.jpg");
//$rotate = imagerotate($source, 90, 0);
//imagejpeg($rotate,"a.jpg");
$pdf->Image("../evidence/".$carnumber."a.png",10,87,90);
}

if(file_exists("../evidence/".$carnumber."b.png")){
//$source = imagecreatefromjpeg("../evidence/".$carnumber."b.jpg");
//$rotate = imagerotate($source, 90, 0);
//imagejpeg($rotate,"b.jpg");
$pdf->Image("../evidence/".$carnumber."b.png",110,87,90);
}    
}


if($internet == 0){
$gmap = new SplFileObject('google_map.png','w');
$image = @file_get_contents("http://maps.google.com/maps/api/staticmap?center=$longitude,$latitude&zoom=15&size=280x200&sensor=false&markers=9.05213166666667,7.50837833333333");
$gmap->fwrite($image);
  
if(file_exists("../evidence/".$carnumber."b.jpg")){
$pdf->Image("google_map.png",130,87,70,35);
}
}

$pdf->Ln(80);
$pdf->Cell(69,5,"To view this citation online at anytime visit"); 
$pdf->SetFont('Arial','B',10);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(60,5,"http://www.safeparkinglimited.com",0,1,'L'); 
$pdf->SetFont('Arial','',10);
$pdf->SetTextColor(0,0,0);

$pdf->Cell(60,5,"________________________________________________________________________________",0,1,'L'); 




        $array = array();
        $campresult = $CONN->prepare("SELECT staffid, amount, charge_type, pos_date_time FROM sales_report WHERE carnumber = ? AND charge_type != ? ORDER BY id DESC LIMIT 5");
        $campresult->execute(array($carnumber, "monitor"));
        while ($rowcam = $campresult->fetch(PDO::FETCH_ASSOC))
        {
            $staffid = $rowcam['staffid'];
            $amount = $rowcam['amount'];
            $charge_type = $rowcam['charge_type'];
            $pos_date_time =  date("Y-m-d H:i A", strtotime("+0 Day", strtotime($rowcam['pos_date_time'])));
            $paname = $staffid;
            $location = $parking_core->palocation($staffid, $pos_date_time);
            $array[] = "$location;$paname;$charge_type;$amount;$pos_date_time";
        }
      
      $totalarray = count($array);
        
        if($totalarray > 0){
        $dbtable = implode("@", $array);
        }
        
$pdf->Cell(60,5,"Last Five (5) Parking History Summary",0,1,'L');
$header = array('Location','Attendant','Type','Amount','Date');
if($totalarray > 0){
$data = $pdf->LoadData($dbtable);
}else{
$data = $pdf->LoadData('0;0;0;0;0');    
}
$pdf->FancyTable($header,$data);

$pdf->Ln(5);

  $range = rangeMonth($today);
  $start = $range['start'];
  $end = $range['end'];

        $campresult = $CONN->prepare("SELECT id FROM sales_report WHERE carnumber = ? AND charge_type = ? AND date BETWEEN ? AND ?");
        $campresult->execute(array($carnumber,"parking",$start,$end));
        $parkingcount = $campresult->rowCount();
        
        $campresult = $CONN->prepare("SELECT id FROM sales_report WHERE carnumber = ? AND charge_type = ? AND date BETWEEN ? AND ?");
        $campresult->execute(array($carnumber,"warning",$start,$end));
        $warningcount = $campresult->rowCount();
        
        $campresult = $CONN->prepare("SELECT id FROM customer_care WHERE v_number = ? AND date BETWEEN ? AND ?");
        $campresult->execute(array($carnumber,$start,$end));
        $violationcount = $campresult->rowCount();
               
$pdf->Cell(60,5,"Summary For the Month",0,1,'L');
$header2 = array('Total Parking', 'Total Warning', 'Total Enforcement Record');
$data2 = $pdf->LoadData("$parkingcount;$warningcount;$violationcount");
$pdf->totaltable($header2,$data2);


$pdf->Ln(3);
$pdf->SetTextColor(220,50,50);
$pdf->Cell(0,9,"You can Pay online Or Pay to Any of the bank Below",0,1,'C'); 
$pdf->SetTextColor(0,0,0);


$pdf->Cell(120,5,"Account Name :: Safe Parking Limited");       $pdf->Cell(60,5,"Account Name :: Safe Parking Limited",0,1,'L');
$pdf->Cell(120,5,"Account Number :: 1013286687");                    $pdf->Cell(60,5,"Account Number :: 0028003103",0,1,'L'); 
$pdf->Cell(120,5,"Bank :: Zenith Bank");                       $pdf->Cell(60,5,"Bank :: Diamond Bank",0,1,'L'); 
$pdf->Ln(7);
$pdf->SetFont('Arial','I',8);
$pdf->SetTextColor(220,50,50);
$pdf->Cell(0,5,'No 14 Bola Ige Close Asokoro Abuja',0,1,'C');
$pdf->Cell(0,0,'www.safeparkinglimited.com, info@safeparkingltd.com 08095775454',0,1,'C');
$pdf->Output();
?>