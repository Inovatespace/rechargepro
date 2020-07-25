<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require "../../../resource.php";
//require "../../../engine/class/numbertoworld.php";
require "../../../plugin/parking_core/parking_core.php";
$CONN = $resource->sql_db();
require('tcpdf/tcpdf.php');


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

//$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);



$pdf->SetCreator("SPL");
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 009');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->AddPage();

$pdf->Image('logo.png', 5, 10, 40, 31, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
$pdf->SetFont('times', '', 12);
$pdf->SetTextColor(0,0,45);
$pdf->Ln(20);
$pdf->Write(0,'No.14, BOLA IGE CLOSE,ASOKORO, ABUJA', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0,'EMAIL: info@safeparkingltd.com', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0,'Phone: 08065605809', '', 0, 'C', true, 0, false, false, 0);
$pdf->Ln(3);





$id = $_REQUEST['id'];
$campresult = $CONN->prepare("SELECT fromwho,towho,title,filename1,filename2,filename3,filename4,filename5,file1,file2,file3,file4,file5,date_created,date_modified,date_comment,status, approval_level, body, comment_count FROM memo WHERE id = ? LIMIT 1"); 
$campresult->execute(array($id));
$rowcam=$campresult->fetch(PDO::FETCH_ASSOC);
$fromwho = $rowcam['fromwho']; 
$towho = $rowcam['towho']; 
$title = $rowcam['title']; 
$filename1 = $rowcam['filename1']; 
$filename2 = $rowcam['filename2']; 
$filename3 = $rowcam['filename3']; 
$filename4 = $rowcam['filename4']; 
$filename5 = $rowcam['filename5']; 
$date_created = date("d F Y",  strtotime("+0 day", strtotime($rowcam['date_created'])));
$date_modified = $rowcam['date_modified']; 
$date_comment = $rowcam['date_comment'];
$status = $rowcam['status'];
$approval_level = $rowcam['approval_level'];
$file1 = $rowcam['file1'];
$file2 = $rowcam['file2'];
$file3 = $rowcam['file3'];
$file4 = $rowcam['file4'];
$file5 = $rowcam['file5'];
$body = $rowcam['body'];
$comment_count = $rowcam['comment_count'];

$fromdatails = $resource->admin_details($fromwho);
$fromname = $fromdatails['name']." {".$fromdatails['val5']."}";
$fromdepartment = $fromdatails['val4'];


$todatails = $resource->admin_details($towho);
$toname = $todatails['name']." {".$todatails['val5']."}";


$cc = array();
$ccname = array();
$campresult = $CONN->prepare("SELECT username FROM memo_cc WHERE memoid = ?"); 
$campresult->execute(array($id));
while($rowcam=$campresult->fetch(PDO::FETCH_ASSOC)){
    
$username = $rowcam['username'];
if($towho != $username){
$cc[] = $username; 
$ccdatails = $resource->admin_details($username);
$cctmpname = $ccdatails['name']." {".$ccdatails['val5']."}";
$ccname[] = $cctmpname;
}

};
$ccname = implode(", ",$ccname);


//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$pdf->SetFillColor(219,229,241);
$pdf->SetTextColor(255,255,255);
$pdf->SetDrawColor(0,0,45);
$pdf->SetLineWidth(.3);
$pdf->SetFont('','B');
$pdf->Cell(0,8,"INTERNAL MEMORANDUM",1,0,'C',true);
$pdf->Ln(10);
$pdf->Cell(0,8,$fromdepartment." DEPARTMENT",1,0,'C',true);
$pdf->Ln(10);

$pdf->SetTextColor(0,0,0);
$pdf->SetFont('times', '', 12);
$pdf->Write(0,'DATE:	'.$date_created, '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0,'FROM: 	'.$fromname, '', 0, 'L', true, 0, false, false, 0);                                                      
$pdf->Write(0,'TO: 		'.$toname, '', 0, 'L', true, 0, false, false, 0);
$pdf->Write(0,'CC: 		 '.$ccname, '', 0, 'L', true, 0, false, false, 0);

$pdf->Ln(6);
$pdf->SetFont('times', 'BIU', 13);
$pdf->Write(0,$title, '', 0, 'C', true, 0, false, false, 0);

$pdf->Ln(3);
$pdf->SetFont('times', '', 12, '', true);

$pdf->writeHTML($body, true, false, true, false, '');  
$pdf->Ln(18); 



//attachment start

//sttachment ends


// comment STart
$cmentarray = array();
$result = $CONN->prepare("SELECT name, review, username, date, id FROM memo_comment WHERE memo_id = ? AND commenttype != ? ORDER BY id DESC");
$result->execute(array($id,1));
while($row = $result->fetch(PDO::FETCH_ASSOC)){
    $cmentarray[] = $row;
    }
   
   $countmemo = count($cmentarray);
    
    if($countmemo > 0){
$pdf->SetFont('times', 'BUI', 12);
$pdf->Write(0,'MEMO COMMENTS - ['.$countmemo.']', '', 0, 'L', true, 0, false, false, 0);
foreach($cmentarray AS $row){ 
$creview = $row['review'];
$cusername = $row['username'];
$cdate = date("m-d-Y g:i A", strtotime("+0 minutes", strtotime($row['date'])));
$cdetails = $resource->admin_details($cusername);
$cname = $cdetails['name']." {".$cdetails['val5']."}";

$pdf->SetFont('times', '', 11);
$pdf->Write(0,$cname.' - '.$cdate, '', 0, 'L', true, 0, false, false, 0); 
$pdf->SetFont('times', '', 12);
$pdf->SetFont('','');
$pdf->writeHTML($creview, true, false, true, false, '');
$pdf->Ln(5);
}
}
//Comment End



$pdf->Ln(18);       
$pdf->SetFillColor(219,229,241);
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(0,0,45);
$pdf->SetLineWidth(.3);
$pdf->SetFont('times', '', 11);
$pdf->Cell(0,8,"Approval",1,0,'C',true);
$pdf->Ln(8);
// create some HTML content
$html = '<table cellpadding="4" cellspacing="0" border="1" style="text-align:center;">
<tr><td>Name</td><td>Designation</td><td>Remark</td><td>Signature</td><td>Date</td></tr>
<tr><td></td><td></td><td></td><td></td><td></td></tr></table>';
// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');





$rowarray = array();
$moneysum = 0;
$campresultc = $CONN->prepare("SELECT id,memoid,username,department,category,item,cashrequest,cashgiven,daterequested,dateneeded,dategiven,status FROM cash_advance WHERE memoid = ?");
$campresultc->execute(array($id));
while($rowcamc=$campresultc->fetch(PDO::FETCH_ASSOC)){
    $rowarray[] = $rowcamc;
    $moneysum = $moneysum + $rowcamc['cashrequest'];
}


$countarray = count($rowarray);

if($countarray > 0){
    

$pdf->AddPage();

$pdf->Image('logo.png', 5, 10, 40, 31, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
$pdf->SetFont('times', '', 12);
$pdf->SetTextColor(0,0,45);
$pdf->Ln(20);
$pdf->Write(0,'No.14, BOLA IGE CLOSE,ASOKORO, ABUJA', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0,'EMAIL: info@safeparkingltd.com', '', 0, 'C', true, 0, false, false, 0);
$pdf->Write(0,'Phone: 08065605809', '', 0, 'C', true, 0, false, false, 0);
$pdf->Ln(3);

$pdf->Cell(0,8,"PURCHASE ADVANCE/CASH ADVANCE",1,0,'C',true);
$pdf->Ln(18);

                                  		  			 


$pdf->Write(0,'NAME: '.$fromname.'                        Dept: '.$fromdepartment, '', 0, 'L', true, 0, false, false, 0);$pdf->Ln(2);
$pdf->Write(0,'Please advance of '.$resource->toMoney($moneysum).' ('.$resource->convert_number($moneysum).' Naira)', '', 0, 'L', true, 0, false, false, 0);$pdf->Ln(2);
$pdf->SetFont('times', '', 15);
$pdf->Write(0,'Details of Expenditure:  '.$title, '', 0, 'L', true, 0, false, false, 0);
$pdf->SetFont('times', '', 12);


$html = '
<table  border="1" cellpadding="4" cellspacing="0" nobr="false">
  <thead>
    <tr>
      <th>Item Description</th>
      <th>Amount</th>
      <th>Amount Given</th>
      <th>Date Given</th>
      <th>Signature</th>
    </tr>
  </thead>
  <tbody>';
  
$cash = 0;
$given = 0;
foreach($rowarray  AS  $rowcamc){
$id = $rowcamc['id'];
$memoid = $rowcamc['memoid'];
$category = $rowcamc['category'];
$item = $rowcamc['item'];
$username = $rowcamc['username'];
$department = $rowcamc['department'];
$cashrequest = $rowcamc['cashrequest'];
$cashgiven = $rowcamc['cashgiven'];
$status = $rowcamc['status'];

$cash =  $cash + $cashrequest;
$given = $given + $cashgiven;



$html .=" 
    <tr>
      <td>$item</td>
      <td>".$resource->toMoney($cashrequest)."</td>
      <td>".$resource->toMoney($cashgiven)."</td>
      <td></td>
      <td></td>
    </tr>";
     
	}
$html .="
    <tr>
      <td><strong>Total</strong></td>
      <td>".$resource->toMoney($cash)."</td>
      <td>".$resource->toMoney($given)."</td>
      <td></td>
      <td></td>
    </tr>
  </tbody>
</table>";
$pdf->Ln(7);
$pdf->SetFont('','');
$pdf->writeHTML($html, true, false, true, false, '');


$pdf->Ln(3);
$html ="I undertake to settle the above not later than Seven days failing which Accounts & Finance Dept. is authorized to recover same from my next salary without notice. <br /> <br />Claimants signature: ---------------------------- Date: ------------------------------------------";
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);


$pdf->Ln(18);       
$pdf->SetFillColor(219,229,241);
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(0,0,45);
$pdf->SetLineWidth(.3);
$pdf->SetFont('times', '', 11);
$pdf->Cell(0,8,"Approval",1,0,'C',true);
$pdf->Ln(8);
// create some HTML content
$html = '<table cellpadding="4" cellspacing="0" border="1" style="text-align:center;">
<tr><td>Name</td><td>Designation</td><td>Remark</td><td>Signature</td><td>Date</td></tr>
<tr><td></td><td></td><td></td><td></td><td></td></tr></table>';
// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

}

$pdf->lastPage();

//Close and output PDF document
$pdf->Output('memo.pdf', 'D');























