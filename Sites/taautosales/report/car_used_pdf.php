<?php
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
		$this->SetFont('AngsanaUPC', '', 14);// Set font
       // $this->Line(10, 286, 200, 286);
        $this->MultiCell(50, 0, 'วันที่พิมพ์ '.date('Y-m-d'), 0, 'L', 0, 0, '', '', true);
        $this->MultiCell(130, 5, 'login user: '.GetUserName($_SESSION["ss_iduser"]), 0, 'R', 0, 0, '', '', true);
	}
}
if($car_idno!=""){

$company = "บริษัท ที.เอ.โอโตเซลส์ จำกัด";	
$save_data = array();
$iduser = $_SESSION["ss_iduser"];

$qry_detail = pg_query("select * from \"Cars\" where car_idno='$car_idno'");
	if($res = pg_fetch_array($qry_detail)){
		
		
		$carname = $res['car_name'];
		$carnum = $res['car_num'];
		$marnum = $res['mar_num'];
		
		$color_id = $res['color']; 
		$qry_color = pg_query("select color_name from \"CarColor\" where color_id='$color_id' ");
		$carcolor = pg_fetch_result($qry_color,0);
		
		$caryear = $res['car_year'];
		$licenplate = $res['license_plate'];
		$po_con = $res['po_con'];
		
	}
$qry_waiver = pg_query("select value_remain,venter,witness,comment,sale,finance_date::date from waiver where car_id_no='$car_idno'");
	if($res_w = pg_fetch_array($qry_waiver)){
	
		$venter = $res_w['venter'];
		$qry_ven = pg_query("select pre_name,cus_name,surname from \"Customers\" where cus_id='$venter' ");
			$res_n = pg_fetch_array($qry_ven);
		$venter = trim($res_n['pre_name'])." ".trim($res_n['cus_name'])." ".trim($res_n['surname']); 
		
		$finance_date  = $res_w['finance_date'];
		
		$witness = $res_w['witness'];
			$qry_wit = pg_query("select fullname from fuser where id_user = '$witness' ");
		$attestor = pg_fetch_result($qry_wit,0);
	
		$sale_id = $res_w['sale'];
	
		$qry_name = pg_query("select fullname from fuser where id_user = '$sale_id' ");
		$salename = pg_fetch_result($qry_name,0);
		
		$remark = $res_w['comment'];
	}
for($j=0;$j<=2;$j++){

	if($j == 0){
		$pageName = "(ต้นฉบับ สำหรับลูกค้า)";
	}else{
		$pageName = "(สำเนา)";
	}
$save_data[$j] .= '

<table cellpadding="1" cellspacing="0" border="0" width="100%">
	<tr >
		<td width="15%"><img src="../images/logo.jpg" border="0" width="80" height="80" ></td>
		<td width="85%" style="font-size:smaller; text-align:left"><font size="18px">บริษัท ที.เอ.โอโตเซลส์  จำกัด <br>
			T.A. AUTOSALES CO.,LTD.</font>
			<hr/><br>
			<font size="12px">สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br>
			เลขประจำตัวผู้เสียภาษี 0105546153597</font>
		</td>
	</tr>
</table><br>
<table border="0" width="100%" >
	<tr><td align="right"><font size="16px">'.$pageName.'</font></td></tr>
	<tr><td align="right"><font size="10px" > เอกสารฉบับนี้ไม่ใช่ใบเสร็จรับเงิน <br></font></td></tr>
	<tr><td align="center"><b><font size="20px">หนังสือสัญญาซื้อขายรถยนต์</font></b></td></tr>
</table> 
<br><br>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="65%"><b>เลขที่เอกสาร:</b> '.$po_con.'</td>
		<td width="35%"><b>ทำที่:</b> '.$company.'</td>
	</tr>
	<tr>
		<td width="65%"></td>
		<td width="35%"><b>วันที่ :</b>  '.formatDate($po_date,"/").'</td>
	</tr>
</table>

<br><br>

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-size:40px" align="left">
   <td><p><font color="white">................</font>ข้าพเจ้า <b>'.trim($cusname).'</b> ซึ่งเป็นเจ้าของกรรมสิทธิ์ ได้ตกลงขายรถยนต์ <br> ยี่ห้อ  <b>'.$carname.'</b>
 เลขตัวถัง  <b>'.$carnum.'</b>  เลขเครื่อง   <b>'.$marnum.'</b>  สีรถ <b>'.$carcolor.'</b> 
ปีรถ   <b>'.$caryear.'</b><br> หมายเลขทะเบียน <b>'.$licenplate.'</b>  ให้กับ  <b>'.$salename.'</b>
ในราคาก่อนภาษี  <b>'.$price.' </b>บาท  ภาษีมูลค่าเพิ่ม <b>'.$vat.'</b> บาท  <br>ราคารวมทั้งสิ้น <b>'.$total_price.'</b> บาท<br>
</p></td>
</tr>
<tr style="font-size:40px" align="left">
	<td>
		<p><font color="white">................</font>	ผู้ซื้อได้ชำระเงินครบถ้วนแล้วให้กับผู้ขาย ผู้ขายได้เซ็นต์หนังสือโอนรถยนต์ หนังสือมอบอำนาจในการโอนรถยนต์ พร้อมทั้งรับรถยนต์ดังกล่าวเป็นที่เรียบร้อยแล้วในวันนี้ โดยภายในรถยนต์ไม่มีทรัพย์สินใดๆ ทั้งสิ้น <br></p>
	</td>
</tr>
<tr style="font-size:40px" align="left">
	<td>
		<p><font color="white">................</font>	เพื่อเป็นหลักฐานจึงได้ทำเป็นหนังสือสัญญานี้ไว้ต่อหน้าพยาน <br><br></p>
	</td>
</tr>
</table>
<br />

<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr align="left">
					<td width="270" colspan="2"><b>หมายเหตุ :</b></td>
				</tr>
				<tr align="left">
					<td width="270" colspan="2">'.$remark.'</td>
				</tr>
				<tr align="left">
					<td width="270" colspan="2"><b>ทะเบียนในสต๊อกเลขที่:</b></td>
				</tr>
				<tr align="left">
					<td width="270" colspan="2">'.$car_idno.'</td>
				</tr>
			</table>
		</td>
		<td>
			<table cellpadding="5" cellspacing="5" border="0" width="100%">
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ..................................... ผู้ขาย</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">(  '.$cusname.'  )</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ..................................... ผู้ซื้อ</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">(  '.$salename.'  )</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">เจ้าหน้าที่บริษัท</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ..................................... พยาน</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">(  '.$attestor.'  )</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';
}
//START PDF

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

//set margins
$pdf->SetMargins(20, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

for($k=0;$k<=2;$k++){
$pdf->AddPage();
$pdf->writeHTML($save_data[$k], true, false, true, false, '');
}

$pdf->Output('po_receive_'.$po_id.'.pdf', 'I');
}
?>