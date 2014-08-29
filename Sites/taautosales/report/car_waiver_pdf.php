<?php
if($car_idno!=""){

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
		$cusname = trim($res_n['pre_name'])." ".trim($res_n['cus_name'])." ".trim($res_n['surname']); 
		
		$finance_date  = $res_w['finance_date'];
		
		$witness = $res_w['witness'];
			$qry_wit = pg_query("select fullname from fuser where id_user = '$witness' ");
		$attestor = pg_fetch_result($qry_wit,0);
		
		$remark = $res_w['comment'];
		$price = $res_w['value_remain'];
		
		$salename = GetUserName($res_w['sale']);
	}
for($j=0;$j<=2;$j++){

	if($j == 0){
		$pageName = "(ต้นฉบับ สำหรับลูกค้า)";
	}else{
		$pageName = "(สำเนา)";
	}
if($company == "thaiace"){
$company_name = "บริษัท ไทยเอซ ลิสซิ่ง จำกัด"; 
$save_data[$j] .= '
<table cellpadding="1" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="15%"><img src="../images/THAIACELOGO.jpg" border="0" width="80px" height="60px" ></td>
		<td width="85%" style="font-size:smaller; text-align:left"><font size="18px">บริษัท ไทยเอซ ลิสซิ่ง จำกัด <br>
			THAI ACE LEASING CO.,LTD.</font>
			<hr/><br>
			<font size="12px">สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br>
			เลขประจำตัวผู้เสียภาษี 0105526038482</font>
		</td>
	</tr>
</table>';
}else if($company == "capital"){
$company_name = "บริษัท ไทยเอซ แคปปิตอล จำกัด"; 
$save_data[$j] .= '
<table cellpadding="1" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="15%"><img src="../images/THCAP.jpg" border="0" width="80px" height="60px" ></td>
		<td width="85%" style="font-size:smaller; text-align:left"><font size="18px">บริษัท ไทยเอซ แคปปิตอล จำกัด <br>
			THAIACE CAPITAL CO.,LTD.</font>
			<hr/><br>
			<font size="12px">สำนักงานใหญ่ 555 ถนนนวมินทร์ แขวงคลองกุ่ม เขตบึงกุ่ม กรุงเทพมหานคร 10240 โทรศัพท์ 0-2744-2222 โทรสาร 0-2379-1111 <br>
			เลขประจำตัวผู้เสียภาษี 0105553136996</font>
		</td>
	</tr>
</table>';
}
$save_data[$j] .= '
<table border="0" width="100%" >
	<tr><td align="right"><font size="16px">'.$pageName.'</font></td></tr>
	<tr><td align="right"><font size="10px" > เอกสารฉบับนี้ไม่ใช่ใบเสร็จรับเงิน <br></font></td></tr>
	<tr><td align="center"><b><font size="20px">หนังสือสละสิทธิ์รถยนต์</font></b></td></tr>
</table> 
<br><br>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="65%"><b>เลขที่เอกสาร:</b> '.$po_con.'</td>
		<td width="35%"><b>ทำที่:</b> '.$company_name.'</td>
	</tr>
	<tr>
		<td width="65%"></td>
		<td width="35%"><b>วันที่ :</b> '.formatDate($po_date,"/").'</td>
	</tr>
</table>

<br><br>

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr style="font-size:40px" align="left" >
   <td cellpadding="3"><font color="white">................</font>ข้าพเจ้า<b>'.$cusname.'</b> ได้เช่าซื้อรถยนต์ยี่ห้อ<b> '.$carname.'</b> <br> เลขตัวถัง  <b>'.$carnum.'</b>
   เลขเครื่อง  <b>'.$marnum.'</b> สีรถ  <b>'.$carcolor.'</b>
   ปีรถ  <b>'.$caryear.'</b> หมายเลขทะเบียน   <b>'.$licenplate.'</b>  <br>กับ   <b>'.$company_name.'</b>	<br>
</td>
</tr>
<tr style="font-size:40px" align="left">
	<td>
		<font color="white">................</font> บัดนี้ เนื่องจากข้าพเจ้าไม่มีความประสงค์จะเช่าซื้อรถยนต์คันดังกล่าวอีกต่อไป จึงได้ตกลงใจเป็นฝ่ายบอกเลิกสัญญาเช่าซื้อ และขอสละสิทธิ์รถยนต์คันดังกล่าวคืนบริษัทฯ ตามสัญญาเช่าซื้อข้อ 14 ทั้งนี้เป็นความประสงค์ของข้าพเจ้าเอง <br>
	</td>
</tr>
<tr style="font-size:40px" align="left">
	<td>
		<p><font color="white">................</font>การคืนรถยนต์ในครั้งนี้ ข้าพเจ้าได้รับเงินส่วนที่เหลือของค่ารถยนต์จากบริษัทเป็นจำนวนเงิน <b>'.number_format($price,2).'</b> บาทไปเรียบร้อย ในวันนี้  
		ซึ่งข้าพเจ้าพอใจและข้าพเจ้าตกลงให้บริษัทฯ สามารถขายรถยนต์คันดังกล่าวต่อบุคคลภายนอกได้ทันที โดยข้าพเจ้าจะไม่ 
		โต้แย้งใดๆ ทั้งสิ้น ข้าพเจ้าได้มอบคืนรถยนต์ให้กับบริษัทฯ แล้วในวันนี้ โดยภายในรถยนต์ไม่มีทรัพย์สินใดๆ ทั้งสิ้น <br></p>
	</td>
</tr>
<tr style="font-size:40px" align="left">
	<td>
		<p><font color="white">................</font>เพื่อเป็นหลักฐานจึงได้ทำเป็นหนังสือสัญญานี้ไว้ต่อหน้าพยาน <br><br></p>
	</td>
</tr>
</table>

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
			<table cellpadding="3" cellspacing="5" border="0" width="100%">
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ..................................... ผู้คืนรถยนต์</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">(  '.$cusname.'  )</td>
				</tr>
				<tr align="center">
					<td width="270" colspan="2">ลงชื่อ ..................................... ผู้รับรถยนต์</td>
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

$pdf->Output('po_buy_'.$po_id.'.pdf', 'I');
}
?>