<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$get_id = $_GET['id'];

if( empty($get_id) ){
    echo "invalid param.";
    exit;
}


//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){

    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

//Start Process

$arr_id = explode(",", $get_id);

foreach($arr_id as $id){
    
$pdf->AddPage('P');

$qry = pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE acb_id='$id' ");
if($res = pg_fetch_array($qry)){
    $acb_detail = $res['acb_detail'];
    $acb_date = $res['acb_date'];
}

$save_data = "";

$save_data .= '<div style="font-size:18pt; font-weight:bold; text-align:center">'. $company_name .'<br />ใบสำคัญปรับปรุง</div>';

$save_data .= '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" align="left">วันที่ '.$acb_date.'</td>
    <td width="50%" align="right">เลขที่ '.$id.'</td>
</tr>
</table>
';

$save_data .= '
<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="300">รายการ</td>
    <td width="120">Dr</td>
    <td width="120">Cr</td>
</tr>
';

$qry = pg_query("SELECT * FROM account.\"AccountBookDetail\" WHERE acb_id='$id' AND \"canceltimes\" is null ORDER BY \"AmtDr\" DESC ");
while($res = pg_fetch_array($qry)){
    $AcID= $res['AcID'];
    $AmtDr = $res['AmtDr'];
    $AmtCr = $res['AmtCr'];
    
    $qry_name=@pg_query("SELECT \"AcName\" FROM account.\"AcTable\" WHERE \"AcID\"='$AcID'");
    if($res_name=@pg_fetch_array($qry_name)){
        $AcName = $res_name["AcName"];
    }

$save_data .= '
<tr>
    <td width="300">['.$AcID.'] '.$AcName.'</td>
    <td width="120" align="right">'.number_format($AmtDr,2).'</td>
    <td width="120" align="right">'.number_format($AmtCr,2).'</td>
</tr>';
}


$save_data .= '
<tr>
    <td colspan="3">'.$acb_detail.'</td>
</tr>';

$save_data .= '</table>';

$save_data .= '<br><br>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="33%" align="center">ผู้เตรียม _______________________</td>
    <td width="33%" align="center">ผู้ตรวจสอบ _______________________</td>
    <td width="33%" align="center">ผู้อนุมัติจ่าย _______________________</td>
</tr>
</table>
';

$pdf->writeHTML($save_data, true, false, true, false, '');

}

$pdf->Output('cert_update.pdf', 'I');
?>