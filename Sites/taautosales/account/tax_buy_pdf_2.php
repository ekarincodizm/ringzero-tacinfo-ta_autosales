<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$mm = $_GET['mm'];
$yy = $_GET['yy'];


$txtdate = get_thai_month($mm)." ".$yy;

$save_data = "";
$save_data .= '
<table cellpadding="2" cellspacing="0" border="0" width="100%" style="font-size:36px;" >
<tr style="font-weight:bold;text-align:center" >
	<td colspan="4" align="center"><b>รายงานภาษีซื้อ</b></td>
</tr>	
<tr>
	<td colspan="4" align="center"><b>ประจำเดือน '.$txtdate.'</b></td>
</tr>
<tr>
	<td align="left" width="12%">ชื่อผู้ประกอบการ : </td>
	<td align="left">บริษัท ที.เอ.โอโตเซลส์ จำกัด</td>
	<td  align="right" width="45%"></td>
	<td align="left" colspan="3">เลขประจำตัวผู้เสียภาษี 0105546153597</td>
</tr>
<tr>
	<td align="left" width="12%">ชื่อสถานประกอบการ : </td>
	<td align="left">บริษัท ที.เอ.โอโตเซลส์ จำกัด</td>
	<td  align="right"></td>
	<td align="left">สำนักงานใหญ่</td>
	
</tr>
<tr>
	<td align="left"  width="12%">ที่อยู่ : </td>
	<td colspan="2" align="left">555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111</td>
</tr>
</table>
<br>
<table cellpadding="2" cellspacing="0" border="1" width="100%" style="font-size:36px;">
<tr style="font-weight:bold;text-align:center" bgcolor="#F0F0F0">
    <td width="3%" rowspan="2">ลำดับที่</td>
    <td width="6%" rowspan="2">เลขที่ใบสำคัญ</td>
    <td width="5%" rowspan="2">วันที่</td>
    <td width="7%" rowspan="2">เลขที่ใบกำกับ</td>
    <td width="19%" rowspan="2">ชื่อผู้ขายสินค้า/ผู้ให้บริการ</td>
    <td width="20%" rowspan="2">รายละเอียด</td>
    <td width="7%" rowspan="2">เลขประจำตัว<br>ผู้เสียภาษี</td>
	<td colspan="2" width="12%">สถานประกอบการ</td>
    <td rowspan="2" width="8%">มูลค่าสินค้า<br>หรือบริการ</td>
	<td width="7%" rowspan="2">ภาษีมูลค่าเพิ่ม</td>
	<td width="8%" rowspan="2">รวมทั้งสิ้น</td>
</tr>
<tr style="font-weight:bold;text-align:center" bgcolor="#F0F0F0">
	<td>สำนักงานใหญ่</td>
	<td>สาขาที่</td>
</tr>';


$nub = 0;
$query=pg_query("SELECT \"acb_id\",\"acb_date\",\"acb_detail\" FROM account.\"AccountBookHead\" 
WHERE (EXTRACT(MONTH FROM \"acb_date\")='$mm') AND (EXTRACT(YEAR FROM \"acb_date\")='$yy') AND \"ref_id\"='VATB' AND \"cancel\"='FALSE' ORDER BY acb_date,\"acb_id\" ASC ");
while($resvc=pg_fetch_array($query)){
    $nub++;
    $acb_id= $resvc['acb_id'];
    $acb_date = $resvc['acb_date'];
    $acb_detail = $resvc['acb_detail'];
    $arr_detail = explode(",",$acb_detail);
	$acb_detail = nl2br($acb_detail);
        
    $buy_from = "";
    $buy_receiptno = "";
    $query_bookbuy=pg_query("SELECT bh_id,buy_from,buy_receiptno,vender_id,to_hp_id FROM account.\"BookBuy\" WHERE bh_id='$acb_id' ");
    if($resvc_bookbuy=pg_fetch_array($query_bookbuy)){
        $buy_from = $resvc_bookbuy['buy_from'];
        $buy_receiptno = $resvc_bookbuy['buy_receiptno'];
		$vender_id = $resvc_bookbuy['vender_id'];
		$to_hp_id = $resvc_bookbuy['to_hp_id'];
		$bh_id = $resvc_bookbuy['bh_id'];
		
		
		$qry_product_id = pg_query("select product_id from \"Cars\" where car_id='$to_hp_id' ");
		$product_id = pg_fetch_result($qry_product_id,0);
		
		$product_name = GetProductName($product_id);
		
	
		if($vender_id != ""){
			if(preg_match('/CUS/',$vender_id)){
				$cus_id = $vender_id;
			}else{
				$qry_cus = pg_query("select cus_id from \"Venders\" where vender_id='$vender_id'");
				$cus_id = pg_fetch_result($qry_cus,0);
			}
		
			$qry_brance = pg_query("select branch_id,card_id,cus_type from \"Customers\" where cus_id='$cus_id' ");
			$branch_id = pg_fetch_result($qry_brance,0);
			$card_id = pg_fetch_result($qry_brance,1);
			$cus_type = pg_fetch_result($qry_brance,2);
			if($cus_type == 2){
				if($branch_id == 0){
					$main_brance = "X";
					$brance = "";
				}else{
					$main_brance = "";
					$brance = $branch_id;
				}
			}
		}
    }
        
    $sum_amtdr = 0;
    $sum_amtcr = 0;
    $amt_vat = 0;
    $query_detail=pg_query("SELECT \"AcID\",\"AmtDr\",\"AmtCr\" FROM account.\"AccountBookDetail\" WHERE \"acb_id\"='$acb_id' ");
    while($resvc_detail=pg_fetch_array($query_detail)){
        $AcID = "";
        $AcID = $resvc_detail['AcID'];
        $AmtDr = round($resvc_detail['AmtDr'],2);
        $AmtCr = round($resvc_detail['AmtCr'],2);

        $sum_amtdr += $AmtDr;
        $sum_amtcr += $AmtCr;

        if($AcID == '1999'){
            if($AmtDr == 0 AND $AmtCr != 0){
                $type = 1;
                $amt_vat += $AmtCr;
            }else{
                $type = 2;
                $amt_vat += $AmtDr;
            }
        }
    }

    if($type == 1){
        $txt_show1 = ($sum_amtcr-$amt_vat)*-1;
        $txt_show2 = $amt_vat*-1;
        $txt_show3 = $sum_amtdr*-1;
    }elseif($type == 2){
        $txt_show1 = ($sum_amtdr-$amt_vat);
        $txt_show2 = $amt_vat;
        $txt_show3 = $sum_amtcr;
    }

    $sum_1+=$txt_show1;
    $sum_2+=$txt_show2;
    $sum_3+=$txt_show3;
    
	
	
$save_data .= '
<tr style="font-size:36px">
    <td align="center">'.$nub.'</td>
    <td align="left">'.$bh_id.'</td>
    <td align="center">'.date_dmy($acb_date).'</td>
    <td align="left">'.$buy_receiptno.'</td>
    <td align="left">'.$buy_from.'</td>
    <td align="left">'.$arr_detail[0].'<br>'.$arr_detail[1].'</td>
    <td align="center">'.$card_id.'</td>
	<td align="center">'.$main_brance.'</td>
    <td align="center">'.$brance.'</td>
	<td align="right">'.number_format($txt_show1,2).'</td>
	<td align="right">'.number_format($txt_show2,2).'</td>
	<td align="right">'.number_format($txt_show3,2).'</td>
</tr>';
}

if($nub == 0){
    $save_data .= "<tr><td colspan=\"11\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    $save_data .= "<tr style=\"font-size:36px\">
    <td colspan=\"9\" align=\"right\"><b>รวม</b></td>
    <td align=\"right\"><b>".number_format( $sum_1,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_2,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_3,2)."</b></td>
    </tr>"; 
}

$save_data .= '</table>';
//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdfLegal.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
		$this->SetFont('AngsanaUPC', '', 12);// Set font
        //$this->Line(10, 200, 340, 200);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(280, 5, 'หน้า '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 'R', 0, 0, '', '', true);
    }
}

//$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);    // A4
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LEGAL', true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(10, 5, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage('L');

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('tax_buy.pdf', 'I');
?>