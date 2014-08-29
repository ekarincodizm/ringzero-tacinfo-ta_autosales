<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$cmd = $_REQUEST['cmd'];
$date = $_REQUEST['date'];


if(empty($cmd) OR $cmd == ""){
    echo "invalid param.";
    exit;
}
if ($cmd =='report') {

$type = $_GET['type'];

    if($type == 1){
        $date = $_GET['date'];
        $qry = "SELECT cheque_no,bank_name,bank_branch,sum(amt_on_cheque) as amt_on_cheque,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date 
		FROM \"Cheques\" WHERE date_enter_bank = '$date' 
		GROUP BY cheque_no,bank_name,bank_branch,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date
		ORDER BY cheque_no ASC ";
    	$title = "รายงานนำเช็คเข้าธนาคาร ประจำวันที่  $date";
    }elseif($type == 2){
        $data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
        $cb_month = $_GET['cb_month'];
        $cb_year = $_GET['cb_year'];
		$qry = "SELECT cheque_no,bank_name,bank_branch,sum(amt_on_cheque) as amt_on_cheque,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date 
		FROM \"Cheques\" WHERE EXTRACT(MONTH FROM \"date_enter_bank\") = '$cb_month' AND EXTRACT(YEAR FROM \"date_enter_bank\") = '$cb_year'
		GROUP BY cheque_no,bank_name,bank_branch,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date ";
		$title = "รายงานนำเช็คเข้าประจำเดือน".$data_month[$cb_month]." ปี $cb_year";
    }
    
$save_data = "";
$save_data .= '
<span style="font-weight:bold; font-size:medium; text-align:left">'.$title.'</span>

<hr>

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="80">เลขที่เช็ค</td>
    <td width="150">ธนาคาร</td>
    <td width="150">สาขา</td>
    <td width="80">วันที่บนเช็ค</td>
	<td width="80">วันที่นำเข้า</td>
	<td width="80">วันที่ผ่าน</td>
    <td align="right" width="80">จำนวนเงิน</td>
    <td align="right" width="60">สถานะเช็ค</td>
</tr>
<hr>';

$j = 0;
$qry = pg_query($qry);
	while($res = pg_fetch_array($qry)){
    $j++;
    $cheque_no = $res['cheque_no'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    $date_on_cheque = $res['date_on_cheque'];
    $amt_on_cheque = $res['amt_on_cheque'];
    $is_pass = $res['is_pass'];
	$date_enter_bank = $res['date_enter_bank'];
	$pass_date = $res['pass_date'];
    $sum += $amt_on_cheque;
	$str_pass="";
	if($is_pass == "f"){ $str_pass = "รอ"; }else{ $str_pass = "ผ่าน"; } 
$save_data .= '
<tr>
    <td align="center">'.$cheque_no.'</td>
    <td align="left">'.$bank_name.'</td>
    <td align="left">'.$bank_branch.'</td>
	<td align="center">'.$date_on_cheque.'</td>
	<td align="center">'.$date_enter_bank.'</td>
	<td align="center">'.$pass_date.'</td>
    <td align="right">'.number_format($amt_on_cheque,2).'</td>
    <td align="right">'.$str_pass.'</td>
</tr>';

}

if($j == 0){
    $save_data .= "<tr><td colspan=\"10\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}
$save_data .= '</table>';
}

//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
        $this->SetFont('AngsanaUPC', '', 10);// Set font
        $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(230, 8, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>