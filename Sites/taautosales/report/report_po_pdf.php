<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$cmd = $_REQUEST['cmd'];

if(empty($cmd) OR $cmd == ""){
    echo "invalid param.";
    exit;
}

$save_data = "";

if($cmd == "report"){

    $type = $_GET['type'];

    if($type == 1){
        $date = $_GET['date'];
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE po_date='$date' ORDER BY po_id ASC ";
        $title = "วันที่ออก PO $date";
    }elseif($type == 2){
        $status_po = $_GET['status_po'];
        if($status_po == "1"){
            $str_status = "FALSE";
            $str_status2 = "ACTIVE";
            $str_status_apv = "AND approve='TRUE'";
        }elseif($status_po == "2"){
            $str_status = "TRUE";
            $str_status2 = "CANCEL";
            $str_status_apv = "";
        }elseif($status_po == "3"){
            $str_status = "FALSE";
            $str_status2 = "WAIT AP";
            $str_status_apv = "AND approve='FALSE'";
        }
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE cancel='$str_status' $str_status_apv ORDER BY po_id ASC ";
        $title = "สถานะ PO $str_status2";
    }elseif($type == 3){
        $receive_all = $_GET['receive_all'];
        if($receive_all == "1"){
            $str_status = "TRUE";
            $str_status2 = "ครบ";
        }else{
            $str_status = "FALSE";
            $str_status2 = "ขาดส่ง";
        }
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE receive_all='$str_status' ORDER BY po_id ASC ";
        $title = "สถานะของส่ง $str_status2";
    }elseif($type == 4){
        $pay = $_GET['pay'];
        if($pay == "1"){
            $str_status = "TRUE";
            $str_status2 = "READY";
        }else{
            $str_status = "FALSE";
            $str_status2 = "HOLD";
        }
        $qry = "SELECT * FROM \"PurchaseOrders\" WHERE pay='$str_status' ORDER BY po_id ASC ";
        $title = "สถานะการชำระ $str_status2";
    }elseif($type == "all"){
        $qry = "SELECT * FROM \"PurchaseOrders\" ORDER BY po_id ASC ";
        $title = "แสดงทั้งหมด";
    }
    
$save_data .= '
<span style="font-weight:bold; font-size:xx-large; text-align:left">รายงาน PO : '.$title.'</span>

<hr>

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="80">Po ID</td>
    <td width="60">Po Date</td>
    <td width="140">ผู้ขาย</td>
    <td width="80">ยอดเงิน</td>
    <td width="60">สถานะ PO</td>
    <td width="60">สถานะของส่ง</td>
    <td width="60">การชำระ</td>
</tr>
<hr>';

$j = 0;
$qry = pg_query($qry);
while($res = pg_fetch_array($qry)){
    $j++;
    $po_id = $res['po_id'];
    $po_date = $res['po_date'];
    $vender_id = $res['vender_id'];
    $amount = $res['amount'];
    $cancel = $res['cancel'];
    $receive_all = $res['receive_all'];
    $pay = $res['pay'];
    $approve = $res['approve'];

    if($cancel == "t"){
        $str_a_cancel = "CANCEL";
    }elseif($cancel == "f" AND $approve == "t"){
        $str_a_cancel = "ACTIVE";
    }elseif($cancel == "f" AND $approve == "f"){
        $str_a_cancel = "WAIT AP";
    }
    
    if($receive_all == "f"){ $str_a_receive_all = "ขาดส่ง"; }else{ $str_a_receive_all = "ครบ"; }
    if($pay == "f"){ $str_a_pay = "HOLD"; }else{ $str_a_pay = "READY"; }
    
	$vender_name = GetVender($vender_id);
	
	if($vender_name == "" || empty($vender_name)){
		$vender_name = GetCusName($vender_id);
	}
	
$save_data .= '
<tr>
    <td align="center">'.$po_id.'</td>
    <td align="center">'.$po_date.'</td>
    <td>'.$vender_name.'</td>
    <td align="right">'.number_format($amount,2).'</td>
    <td align="center">'.$str_a_cancel.'</td>
    <td align="center">'.$str_a_receive_all.'</td>
    <td align="center">'.$str_a_pay.'</td>
</tr>';

}

if($j == 0){
    $save_data .= "<tr><td colspan=\"10\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}

$save_data .= '</table>';

}

elseif($cmd == "report_po"){
    $id = $_GET['id'];
    
    $qry = pg_query("SELECT * FROM \"PurchaseOrders\" WHERE po_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $vender_id = $res['vender_id'];
        $cancel = $res['cancel'];
        $user_id = $res['user_id'];
        $approve_by = $res['approve_by'];
        $pay = $res['pay'];
        
        if($cancel == "f"){ $str_cancel = "ACTIVE"; }else{ $str_cancel = "CANCEL"; }
        if($pay == "f"){ $str_pay = "HOLD"; }else{ $str_pay = "READY"; }
    }
    
$save_data .= '
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="left">PO ID : '.$id.'</td>
    <td align="right">สถานะ PO : '.$str_cancel.'</td>
</tr>
<tr>
    <td align="left">ผู้ขาย : '.GetVender($vender_id).'</td>
    <td align="right">ผู้ตั้งเรื่อง : '.GetUserName($user_id).'</td>
</tr>
<tr>
    <td align="left">&nbsp;</td>
    <td align="right">ผู้อนุมัติ : '.GetUserName($approve_by).'</td>
</tr>
</table>

<div style="margin-top:5px">
<table cellpadding="3" cellspacing="0" border="1" width="100%">
<tr style="font-weight:bold; text-align:center">
    <td width="55">Product ID</td>
    <td width="150">Product Name</td>
    <td width="50">Unit</td>
    <td width="50">Receive</td>
    <td width="80">Amount</td>
    <td width="70">Vat</td>
    <td width="80">Total</td>
</tr>';

$j = 0;
    
    $qry_dt = pg_query("SELECT auto_id,product_id,unit,product_cost,vat FROM \"PurchaseOrderDetails\" WHERE po_id='$id' ");
    while($res_dt = pg_fetch_array($qry_dt)){
	$j++;
        $auto_id = $res_dt['auto_id'];
		$product_id = $res_dt['product_id'];
		$unit = $res_dt['unit'];
        $product_cost = $res_dt['product_cost'];
        $vat = $res_dt['vat'];
    

    $sumall_amount+=$product_cost;
    $sumall_vat+=$vat;
    $sumall_all+=($product_cost+$vat);

    $count_unit=@pg_query("select count_receive_good('$auto_id')");
    $receive=@pg_fetch_result($count_unit,0);

$save_data .= '
<tr>
    <td align="center">'.$product_id.'</td>
    <td>'.GetProductName($product_id).'</td>
    <td align="right">'.$unit.'</td>
    <td align="right">'.$receive.'</td>
    <td align="right">'.number_format($product_cost,2).'</td>
    <td align="right">'.number_format($vat,2).'</td>
    <td align="right">'.number_format($product_cost+$vat,2).'</td>
</tr>';

}

if($j == 0){
    $save_data .= "<tr><td colspan=\"7\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{

$save_data .= '
<tr style="font-weight:bold">
    <td colspan="4" align="right">รวมเงิน</td>
    <td align="right">'.number_format($sumall_amount,2).'</td>
    <td align="right">'.number_format($sumall_vat,2).'</td>
    <td align="right">'.number_format($sumall_all,2).'</td>
</tr>';

}

$save_data .= '</table></div>';

$save_data .= '<span>การชำระเงิน : '.$str_pay.'<br />ชำระโดย<br />';

$qry = pg_query("SELECT * FROM account.\"VoucherDetails\" WHERE vc_detail LIKE '%$id%' ");
while($res = pg_fetch_array($qry)){
    $cash_amt = $res['cash_amt'];
    $chque_no = $res['chque_no'];

    if(!empty($chque_no)){
        $qry7 = pg_query("SELECT * FROM account.\"ChequeAccDetails\" WHERE chq_id = '$chque_no' ");
        if($res7 = pg_fetch_array($qry7)){
            $date_on_chq = $res7['date_on_chq'];
            $amount = $res7['amount'];
            $ac_id = $res7['ac_id'];
        }
        
        $qry8 = pg_query("SELECT * FROM account.\"ChequeAccs\" WHERE ac_id = '$ac_id' ");
        if($res8 = pg_fetch_array($qry8)){
            $bank_name = $res8['bank_name'];
            $bank_branch = $res8['bank_branch'];
        }
    }
    
    if($cash_amt != 0 AND !empty($chque_no)){
        //ทั้ง 2อย่าง
        $save_data .= "- เงินสด ".number_format($cash_amt,2)." บาท<br />";
        $save_data .= "- เช็ค ธนาคาร $bank_name สาขา $bank_branch เลขที่ $chque_no ลงวันที่ $date_on_chq ยอดเงิน ".number_format($amount,2)." บาท<br />";
    }elseif($cash_amt == 0 AND !empty($chque_no)){
        //จ่ายเช็ค
        $save_data .= "- เช็ค ธนาคาร $bank_name สาขา $bank_branch เลขที่ $chque_no ลงวันที่ $date_on_chq ยอดเงิน ".number_format($amount,2)." บาท<br />";
    }elseif($cash_amt != 0 AND empty($chque_no)){
        //เงินสด
        $save_data .= "- เงินสด<br />";
    }else{
        $save_data .= "- ข้อมูลผิดผลาด !<br />";
    }
}
$save_data .= '</span>';

}

//START PDF
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
//$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage();

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('receipt_'.$id.'.pdf', 'I');
?>