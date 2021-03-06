<?php
include_once("../include/config.php");
include_once("../include/function.php");
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){

    }

    public function Footer(){
	
	}
}
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$id = $_REQUEST['id'];

if(empty($id) OR $id == ""){
    echo "invalid param.";
    exit;
}
$generate_id = pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],11)");
$co_id = pg_fetch_result($generate_id,0);

$qry = pg_query("SELECT * FROM \"CarMove\" WHERE \"auto_id\"='$id' AND date_out IS NOT NULL ORDER BY auto_id DESC LIMIT 1 ");
if($res = pg_fetch_array($qry)){
	$car_id = $res['car_id'];
    $date_out = $res['date_out'];
    $wh_id = $res['wh_id'];
    $target_go = $res['target_go'];
	$car_owner = $res['car_owner'];
	$remark = $res['remark'];
}

$qry = pg_query("SELECT * FROM \"Cars\" WHERE \"car_id\"='$car_id'");
if($res = pg_fetch_array($qry)){
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $color = $res['color'];
    $license_plate = $res['license_plate'];
    $product_id = $res['product_id'];
    $product_name = GetProductName($product_id);
	$car_idno = $res['car_idno'];
	$radio_id = $res['radio_id'];
}

// สถานะรถจะต้อง ยังไม่ อนุมัติ ส่งมอบลูกค้า
//$qry = pg_query("select res_id from \"CarMoveToCus\" where auto_id in (select max(auto_id) from \"CarMoveToCus\" where car_id='$car_id' and status_appv='9' ) ");
// ถ้า reprint ให้ใช้ อันนี้
$qry = pg_query("select res_id from \"CarMoveToCus\" where auto_id in (select max(auto_id) from \"CarMoveToCus\" where car_id='$car_id' ) ");

$res_id = pg_fetch_result($qry,0);

$qry_res = pg_query("select cus_id from \"Reserves\" where res_id = '$res_id' ");
$cus_id = pg_fetch_result($qry_res,0);

if($target_go == '0'){
	$insertTR = '<tr>
    <td>ชื่อลูกค้า</td>
    <td>'.GetCusName($cus_id).'</td>
    <td>เลขที่ใบจอง</td>
    <td>'.$res_id.'</td>
	</tr>';
}else{
	$insertTR = '';
}

//Load Product Image
$image_product_strQuery = "
	SELECT 
		product_image
	FROM 
		\"Products\"
	WHERE
		product_id = '".$product_id."'
";
$image_product_query = @pg_query($image_product_strQuery);
$image_product_result = @pg_fetch_result($image_product_query, 0);


$txt = '
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
<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-weight:bold; text-align:left; font-size:large">
<tr>
    <td width="50%">เอกสารรับรถ</td>
    <td width="50%" align="right">เลขที่ : '.$co_id.'</td>
</tr>
<tr>
    <td width="50%">ทะเบียนรถใน STOCK : '.$car_idno.'</td>
    <td width="50%" align="right"></td>
</tr>
</table>

<table cellpadding="2" cellspacing="0" border="1" width="100%">
<tr>
    <td width="65">วันที่</td>
    <td width="190">'.formatDate($date_out,'/').'</td>
    <td width="75">ประเภทรายการ</td>
    <td width="180">รับมอบรถยนต์</td>
</tr>
<tr>
    <td>จาก</td>
    <td>'.GetWarehousesName($wh_id).'</td>
    <td>มาที่</td>
    <td>'.GetWarehousesName($target_go).'</td>
</tr>'.$insertTR.'
<tr>
    <td>รุ่นรถ</td>
    <td>'.$product_name.'</td>
    <td>ทะเบียนรถ</td>
    <td>'.$license_plate.'</td>
</tr>
<tr>
    <td>เลขเครื่อง</td>
    <td>'.$mar_num.'</td>
    <td>เลขถัง</td>
    <td>'.$car_num.'</td>
</tr>
<tr>
    <td>สีรถ</td>
    <td>'.getCarColor($color).' เลขวิทยุ '.$radio_id.'</td>
	 <td>ผู้รับผิดชอบ</td>
    <td>'.$car_owner.'</td>
</tr>
</table>

<table width="95%">
<tr>
	<td align="left"><b><u>เงื่อนไขการรับมอบรถยนต์</u></b></td>
</tr>
<tr>
	<td colspan="2">
		1.การเบิกสินค้าระหว่างคลังนี้ ต้องมีผู้เซ็นทั้งผู้รับโอน และผู้โอน พร้อมทั้งลายเซ็นผู้จัดการจึงจะถือว่าสมบูรณ์ <br>
		2.ในการเบิกหรือโอนสินค้าระหว่างคลังต้องแจ้งผู้จัดการให้ทราบทุกครั้งไป และนับจำนวนสินค้าที่โอนออกและรับเข้าให้ตรงกัน
	</td>
</tr>
</table>

<table width="95%">
<tr>
	<td align="left"><b><u>หมายเหตุ</u></b></td>
</tr>
<tr>
	<td colspan="3">'.$remark.'</td>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr align="center">
  <td>
	<table align="center" border="1" >
		<tr>
			<td align="center">
				ลงชื่อ.....................................ผู้อนุมัติ <br>
				(.......................................................)<br>
				วันที่.....................................<br />
				ผู้จัดการ
			</td>
			<td align="center">
				ลงชื่อ.....................................ผู้รับรถยนต์ <br>
				(.......................................................)<br />
				วันที่.....................................
			</td>
			<td align="center">
				ลงชื่อ...............................ผู้ส่งมอบรถยนต์ <br>
				(.......................................................)<br />
				วันที่.....................................
			</td>
		</tr>
	</table>
  </td>
</tr>
<tr align="center">
  <td>
	<table align="center" border="1">
		<tr>
			<td align="center">ลงชื่อ.....................................ผู้รับรถยนต์ <br>
				(.....................................................................)<br />
				วันที่.....................................</td>
			<td align="center">ลงชื่อ.....................................ผู้รับรถยนต์ <br>
				(.....................................................................)<br />
				วันที่.....................................</td>
		</tr>
	</table>
  </td>
</tr>
</table>
<span>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -</span> <br>
<table align="center">
<tr align="center">
	<td>
		<img src="../images/product_image/'.$image_product_result.'" width="550px" height="250px">
	</td>
</tr>
<tr>
	<td align="right">'.GetUserName($_SESSION["ss_iduser"]).' : '.$nowDateTime.' (ผู้ทำรายการ)</td>
</tr>
</table>
';


//START PDF

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(20, 10, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 10);

// set font
$pdf->SetFont('AngsanaUPC', '', 15); //AngsanaUPC  CordiaUPC


$pdf->AddPage();
$pdf->writeHTML($txt, true, false, true, false, '');


$pdf->Output('po_receive_'.$po_id.'.pdf', 'I');
?>