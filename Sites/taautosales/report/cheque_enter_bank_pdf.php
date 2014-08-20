<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$id = $_REQUEST['id'];

if(empty($id) OR $id == ""){
    echo "invalid param.";
    exit;
}

include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
        $this->SetFont('AngsanaUPC', '', 14);// Set font
        $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(160, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set font
$pdf->SetFont('AngsanaUPC', '', 16); //AngsanaUPC  CordiaUPC

$pdf->AddPage();

$txt = '<div style="font-weight:bold; font-size:x-large; text-align:center">นำเช็คเข้าธนาคาร</div><br>';

$txt .= '
<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr style="text-align:center; font-weight:bold">
    <td width="80">เลขที่เช็ค</td>
    <td width="80">running_no</td>
    <td width="100">ธนาคาร</td>
    <td width="100">สาขา</td>
    <td width="80">วันที่บนเช็ค</td>
    <td width="100">ยอดเงิน</td>
</tr>';

$arr_select = explode(",",$id);
foreach($arr_select as $v){
    $arr_v = explode("|",$v);
    $run_no = $arr_v[0];
    $cq_no = $arr_v[1];
    
    $qry = pg_query("SELECT * FROM \"Cheques\" WHERE cheque_no = '$cq_no' AND running_no = '$run_no' ");
    if($res = pg_fetch_array($qry)){
        $bank_name = $res['bank_name'];
        $bank_branch = $res['bank_branch'];
        $date_on_cheque = $res['date_on_cheque'];
        $amt_on_cheque = $res['amt_on_cheque'];
        $sum_all += $amt_on_cheque;
    }
    
$txt .= '
<tr>
    <td align="center">'.$cq_no.'</td>
    <td align="center">'.$run_no.'</td>
    <td>'.$bank_name.'</td>
    <td>'.$bank_branch.'</td>
    <td align="center">'.$date_on_cheque.'</td>
    <td align="right">'.number_format($amt_on_cheque,2).'</td>
</tr>';
}

$txt .= '
<tr>
    <td align="right" colspan="5">รวมเงิน</td>
    <td align="right">'.number_format($sum_all,2).'</td>
</tr>';

$txt .= '
</table>
';

$pdf->writeHTML($txt, true, false, true, false, '');

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>