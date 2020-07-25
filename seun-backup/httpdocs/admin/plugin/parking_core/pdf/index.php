<?php
require "../../../engine.autoloader.php";
//require "../../../engine/class/numbertoworld.php";
require "../../../plugin/parking_core/parking_core.php";
require('tcpdf/tcpdf.php');
$CONN = $engine->db();
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

case "Road obstruction violation": $fine = 25000;
break;


case "Forged papers and licence": $fine = 25000;
break;


case "Driving against traffic": $fine = 50000;
break;


case "Pedestrian cross violation": $fine = 1000;
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



class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		$image_file = 'logo.png';
		$this->Image($image_file, 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 20);
		// Title
		$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}


}
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);



$pdf->SetCreator("SPL");
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 009');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->AddPage();

$pdf->Image('logo.png', 10, 10, 40, 31, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
$pdf->SetFont('times', '', 12);
$pdf->SetTextColor(0,0,45);
$pdf->Ln(15);
$pdf->SetFont('times','',15);
$pdf->SetTextColor(220,50,50);

$pdf->Write(0,'NOTICE OF VIOLATION', '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0,strtoupper($offence).' OFFENCE', '', 0, 'R', true, 0, false, false, 0);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('times','',11);
$pdf->Write(0,"Longitude:".$longitude.' - Latitude:'.$latitude, '', 0, 'R', true, 0, false, false, 0);


$pdf->Ln(3);

//$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Image('1.png', 10,45,60, 31, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
$pdf->Cell(115);
$pdf->Cell(75,5,'PCN Number    ::    '.$pcn,1,30,'L');
$pdf->Cell(75,5,'Car Number      ::    '.$carnumber,1,30,'L');
$pdf->Cell(75,5,'Fine                   ::    N'.$fine,1,30,'L');
$pdf->Cell(75,5,'Offence Date    ::    '.$offencedate,1,30,'L');
$pdf->Cell(75,5,'Fine Due Date  ::    '.$offencedate,1,30,'L');
$pdf->Cell(75,5,'Location            ::    '.$parking_core->limit_text($location,0,18),1,30,'L');
$pdf->SetFont('times','',11);
$pdf->Ln(3);

$html = "Please take notice that, as the registrant of the vehicle appearing in the photo Above, you are liable to pay a fine in the amount indicated above Pursuant to Section 86 of the Enugu State Road  Traffic Law 2012, You are liable for the fine because at the time, date and location indicated above, the driver of your vehicle, in violation of Section 86 of the Enugu State Road  Traffic Law 2012, committed the offence stated above. You must pay your fine by the payment due date indicated above,  or pay a demurrage fine of <strong>N500</strong> per night. <strong>Towing attracts a fine of N5000</strong>"; 
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln();




 if(file_exists("../evidence/".$carnumber."a.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."a.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"a.png");
if(file_exists("a.png")){
$pdf->Image("a.png",10,110,90, 0, 'PNG', '', '', true, 150, '', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."b.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."b.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"b.png");
if(file_exists("b.png")){
$pdf->Image("b.png",110,110,90, 0, 'PNG', '', '', true, 150, '', false, false, 1, true, false, true);
}}


   
if(file_exists("../evidence/".$carnumber."c.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."c.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"c.png");
if(file_exists("c.png")){
$pdf->Image("c.png",10,110,90, 0, 'PNG', '', '', true, 150, '', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."d.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."d.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"d.png");
if(file_exists("d.png")){
$pdf->Image("d.png",110,110,90, 0, 'PNG', '', '', true, 150, '', false, false, 1, true, false, true);
}}

  
if(file_exists("../evidence/".$carnumber."a.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."a.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"a.jpg");
if(file_exists("a.jpg")){
$pdf->Image("a.jpg",10,110,90, 0, 'JPG', '', '', true, 150, '', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."b.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."b.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"b.jpg");
if(file_exists("b.jpg")){
$pdf->Image("b.jpg",110,110,90, 0, 'JPG', '', '', true, 150, '', false, false, 1, true, false, true);
}
}

if(file_exists("../evidence/".$carnumber."c.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."c.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"c.jpg");
if(file_exists("c.jpg")){
$pdf->Image("c.jpg",10,110,90, 0, 'JPG', '', '', true, 150, '', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."d.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."d.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"d.jpg");
if(file_exists("d.jpg")){
$pdf->Image("d.jpg",110,110,90, 0, 'JPG', '', '', true, 150, '', false, false, 1, true, false, true);
}}

$pdf->Ln();

$pdf->Cell(69,10,"To view this citation online at anytime visit"); 
$pdf->SetFont('times','B',10);
$pdf->SetTextColor(0,0,255);
$pdf->Cell(60,10,"http://www.safeparkinglimited.com",0,1,'L'); 
$pdf->SetFont('times','',11);
$pdf->SetTextColor(0,0,0);





$pdf->SetTextColor(220,50,50);
$pdf->Cell(0,-15,"You can Pay online Or Pay to Any of the bank Below",0,1,'C'); 
$pdf->SetTextColor(0,0,0);
//$pdf->Ln();

$pdf->Cell(120,5,"Account Name :: Safe Parking Limited Enugu Project");       $pdf->Cell(50,5,"Account Name :: Safe Parking Limited Enugu Project",0,1,'L');
$pdf->Cell(120,5,"Account Number :: 1014237442");                    $pdf->Cell(50,5,"Account Number :: 1014237442",0,1,'L'); 
$pdf->Cell(120,5,"Bank :: Zenith Bank");                       $pdf->Cell(50,5,"Bank :: Zenith Bank",0,1,'L'); 
$pdf->Ln();
$pdf->SetFont('times','I',8);
$pdf->SetTextColor(220,50,50);
$pdf->Cell(0,5,'No 14 Bola Ige Close Asokoro Abuja',0,1,'C');
$pdf->Cell(0,0,'www.safeparkinglimited.com, info@safeparkingltd.com 08095775454',0,1,'C');



$pdf->AddPage();

$pdf->Image('logo.png', 10, 10, 40, 31, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
$pdf->SetFont('times', '', 12);
$pdf->SetTextColor(0,0,45);
$pdf->Ln(20);
$pdf->SetFont('times','',15);
$pdf->SetTextColor(220,50,50);
$pdf->Write(0,'PHOTO EVIDENCE', '', 0, 'R', true, 0, false, false, 0);
$pdf->Write(0,strtoupper($offence).' OFFENCE', '', 0, 'R', true, 0, false, false, 0);
$pdf->SetTextColor(0,0,0);
$pdf->SetFont('times','',11);
$pdf->Ln();




 if(file_exists("../evidence/".$carnumber."a.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."a.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"ab.png");
if(file_exists("ab.png")){
$pdf->Image("ab.png",10,50,0, 70, 'PNG', '', '', true, 600, 'C', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."b.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."b.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"bb.png");
if(file_exists("bb.png")){
$pdf->Image("bb.png",10,125,0, 70, 'PNG', '', '', true, 600, 'C', false, false, 1, true, false, true);
}}


   
if(file_exists("../evidence/".$carnumber."c.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."c.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"cb.png");
if(file_exists("cb.png")){
$pdf->Image("cb.png",10,50,0, 70, 'PNG', '', '', true, 600, 'L', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."d.png")){
$source = imagecreatefrompng("../evidence/".$carnumber."d.png");
$rotate = imagerotate($source, 0, 0);
imagepng($rotate,"db.png");
if(file_exists("db.png")){
$pdf->Image("db.png",10,125,0, 70, 'PNG', '', '', true, 600, 'L', false, false, 1, true, false, true);
}}

  
if(file_exists("../evidence/".$carnumber."a.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."a.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"ab.jpg");
if(file_exists("ab.jpg")){
$pdf->Image("ab.jpg",10,50,0, 70, 'JPG', '', '', true, 600, 'L', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."b.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."b.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"bb.jpg");
if(file_exists("bb.jpg")){
$pdf->Image("bb.jpg",10,125,0, 70, 'JPG', '', '', true, 600, 'L', false, false, 1, true, false, true);
}
}

if(file_exists("../evidence/".$carnumber."c.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."c.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"cb.jpg");
if(file_exists("cb.jpg")){
$pdf->Image("cb.jpg",10,50,0, 70, 'JPG', '', '', true, 600, 'L', false, false, 1, true, false, true);
}}

if(file_exists("../evidence/".$carnumber."d.jpg")){
$source = imagecreatefromjpeg("../evidence/".$carnumber."d.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"db.jpg");
if(file_exists("db.jpg")){
$pdf->Image("db.jpg",10,125,0, 70, 'JPG', '', '', true, 600, 'L', false, false, 1, true, false, true);
}}


 if(!empty($longitude) && !empty($latitude)){
$gmap = new SplFileObject('google_map.png','w');
$image = @file_get_contents("http://maps.google.com/maps/api/staticmap?center=$longitude,$latitude&zoom=15&size=300x280&sensor=false&markers=$longitude,$latitude");
$gmap->fwrite($image);

if(file_exists("google_map.png")){
$pdf->Image("google_map.png",10,50,0, 87, 'PNG', '', '', true, 600, 'R', false, false, 1, true, false, true);
}
}


if(file_exists("../poundyard/".$carnumber."b.jpg")){
$source = imagecreatefromjpeg("../poundyard/".$carnumber."b.jpg");
$rotate = imagerotate($source, 0, 0);
imagejpeg($rotate,"bb.jpg");
if(file_exists("bb.jpg")){
$pdf->Image("bb.jpg",10,125,0, 70, 'JPG', '', '', true, 600, 'R', false, false, 1, true, false, true);
}}

$pdf->Ln(210);
$pdf->SetFont('times','I',8);
$pdf->SetTextColor(220,50,50);
$pdf->Cell(0,5,'No 14 Bola Ige Close Asokoro Abuja',0,1,'C');
$pdf->Cell(0,0,'www.safeparkinglimited.com, info@safeparkingltd.com 08095775454',0,1,'C');




















$pdf->lastPage();

//Close and output PDF document
$pdf->Output('memo.pdf', 'I');
exit;





?>