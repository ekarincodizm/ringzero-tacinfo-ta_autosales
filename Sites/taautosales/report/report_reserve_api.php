<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "changetype"){
    $type = $_GET['type'];
    
    if($type == 1 or $type == 11 or $type == 7 ){
		if ($type == 7){
			echo "<select name=\"cb_user_sale\" id=\"cb_user_sale\">";
			$qry = pg_query("SELECT * FROM \"fuser\" WHERE user_group='3' AND status_user='TRUE' ORDER BY fullname ASC");
			while($res = pg_fetch_array($qry)){
				$id_user = $res['id_user'];
				$fullname = $res['fullname'];
				echo "<option value=\"$id_user\">$fullname</option>";
			}
			echo "</select>";
		}
        $data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
?>
        <select name="cb_month" id="cb_month">
        <?php
        foreach ($data_month as $k => $v){
            if(date('m') == $k){
                echo "<option value=\"$k\" selected>$v</option>";
            }else{
                echo "<option value=\"$k\">$v</option>";
            }
        }
        ?>
        </select>
        
        <select name="cb_year" id="cb_year">
        <?php
        $year_plus = $nowyear + 5; 
        $year_lob =  $nowyear - 5;

        for($i=$year_lob; $i<=$year_plus; $i++){
            if($nowyear == $i){
                echo "<option value=\"$i\" selected>$i</option>";
            }else{
                echo "<option value=\"$i\">$i</option>";
            }
        }
        ?>
        </select>
<?php
    }
    elseif($type == 2){
?>
    <input type="text" name="txt_name" id="txt_name" size="30">

    <script type="text/javascript">
    $("#txt_name").autocomplete({
        source: "report_reserve_api.php?cmd=autocomplete",
        minLength:1
    });
    </script>
<?php
    }
    elseif($type == 3){
        echo "<select name=\"cb_product\" id=\"cb_product\">";
        $qry = pg_query("SELECT * FROM \"Products\" WHERE cancel='FALSE' ORDER BY name ASC");
        while($res = pg_fetch_array($qry)){
            $product_id = $res['product_id'];
            $name = $res['name'];
            echo "<option value=\"$product_id\">$name</option>";
        }
        echo "</select>";
    }
    	
    else{
        echo "";
    }
	
}

elseif($cmd == "report"){
    $type = $_GET['type'];
    
    if($type == 1 or $type == 11 or $type == 7 ){
		$data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
        $cb_month = $_GET['cb_month'];
        $cb_year = $_GET['cb_year'];
		if($type == 1)	{
			$qry = pg_query("SELECT * FROM \"Reserves\" WHERE EXTRACT(MONTH FROM \"reserve_date\")='$cb_month' AND EXTRACT(YEAR FROM \"reserve_date\")='$cb_year' ORDER BY res_id ASC ");
			$type_txt = "ยอดจอง เดือน ".$data_month[$cb_month]." ปี $cb_year";
			$param_pdf = "type=1&cb_month=$cb_month&cb_year=$cb_year";
		}elseif($type == 7){
			$cb_user_sale = $_GET['cb_user_sale'];
			$qry = pg_query("SELECT * FROM \"Reserves\" WHERE user_id='$cb_user_sale' AND EXTRACT(MONTH FROM \"reserve_date\")='$cb_month' AND EXTRACT(YEAR FROM \"reserve_date\")='$cb_year' ORDER BY res_id ASC ");
			$type_txt = "ผู้รับจอง ".  GetUserName($cb_user_sale)."     ประจำเดือน   ".$data_month[$cb_month]." ปี $cb_year";;
			$param_pdf = "type=7&cb_user_sale=$cb_user_sale&cb_month=$cb_month&cb_year=$cb_year";
		}
		else{
			$qry = pg_query("SELECT * FROM \"Reserves\" WHERE \"reserve_status\" = '1' AND EXTRACT(MONTH FROM \"reserve_date\")='$cb_month' AND EXTRACT(YEAR FROM \"reserve_date\")='$cb_year' ORDER BY res_id ASC ");
			$type_txt = "ยอดขายรถ เดือน ".$data_month[$cb_month]." ปี $cb_year";
			$param_pdf = "type=11&cb_month=$cb_month&cb_year=$cb_year";
		}
    }
    elseif($type == 2){
        $txt_name = explode('#',$_GET['txt_name']);
		
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE cus_id='$txt_name[0]' ORDER BY res_id ASC ");
        $type_txt = "ค้นจากชื่อลูกค้า ".GetCusName($txt_name);
        $param_pdf = "type=2&txt_name=$txt_name[0]";
    }
    elseif($type == 3){
        $cb_product = $_GET['cb_product'];
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE product_id='$cb_product' ORDER BY res_id ASC ");
        $type_txt = "ยี่ห้อรุ่นรถ ".GetProductName($cb_product);
        $param_pdf = "type=3&cb_product=$cb_product";
    }
    elseif($type == 4){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '2' ORDER BY res_id ASC ");
        $type_txt = "เฉพาะรถที่จองอยู่";
        $param_pdf = "type=4";
    }
    elseif($type == 5){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '1' ORDER BY res_id ASC ");
        $type_txt = "ลูกค้ารับรถไปแล้ว";
        $param_pdf = "type=5";
    }
    elseif($type == 6){
        $qry = pg_query("SELECT * FROM \"Reserves\" ORDER BY res_id ASC ");
        $type_txt = "ทั้งหมด";
        $param_pdf = "type=6";
    }
    elseif($type == 8){
        $qry = pg_query("SELECT * FROM \"Reserves\" R WHERE reserve_status = '1' AND (down_price-(select sum(pay_amount) from v_down_balance D where D.res_id=R.res_id) > 0) ORDER BY R.res_id ASC ");
        $type_txt = "ลูกค้ารับรถแล้วแต่ค้างชำระ";
        $param_pdf = "type=8";
    }elseif($type == 9){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '3' ORDER BY res_id ASC ");
        $type_txt = "ใบจองซ้อนรอเปลี่ยนคัน";
        $param_pdf = "type=9";
    }elseif($type == 10){
        $qry = pg_query("SELECT * FROM \"Reserves\" WHERE reserve_status = '0' ORDER BY res_id ASC ");
        $type_txt = "ใบจองที่ยกเลิก";
        $param_pdf = "type=10";
    }
	
?>

<div style="float:left"><b>รูปแบบ :</b> <?php echo $type_txt; ?></div>
<div style="float:right">
<a href="report_reserve_pdf.php?<?php echo $param_pdf; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่จอง</td>
    <td>วันที่จอง</td>
    <td>ชื่อลูกค้า</td>
    <td>ยี่ห้อรถ</td>
    <td>ราคารถ</td>
    <td>ต้องการดาวน์</td>
    <td>เงินจอง</td>
    <td>เงินดาวน์คงค้าง</td>
</tr>
<?php
$j = 0;
$sum = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $res_id = $res['res_id'];
    $reserve_date = $res['reserve_date'];
    $receive_date = $res['receive_date'];
    $cus_id = $res['cus_id'];
        $cus_name = GetCusName($cus_id);
    //$product_id = $res['product_id'];
    $car_id = $res['car_id'];
	$product_id = $res['product_id'];
    $car_name = GetProductName($product_id);
        
    $car_price = $res['car_price'];
    $down_price = $res['down_price'];
    $cancel = $res['cancel'];
    
    $qry_resdt = pg_query("SELECT SUM(amount) as amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND o_receipt IS NOT NULL AND constant_var IS NOT NULL ");
    if($res_resdt = pg_fetch_array($qry_resdt)){
        $amount = $res_resdt['amount'];
    }
    
    $sum_car_price += $car_price;
    $sum_down_price += $down_price;
    $sum_amount += $amount;
	
	$sum_remain = 0;
	$qry_re = pg_query("SELECT * FROM \"Invoices\" WHERE res_id='$res_id' AND status IS NULL AND cancel = 'FALSE' ORDER BY inv_no ASC ");
	while($res_re = pg_fetch_array($qry_re) ){
    $j++;
    $inv_no = $res_re['inv_no'];
    
    $qry3 = pg_query("SELECT SUM(amount+vat) as amt FROM \"VInvDetail\" WHERE cancel='FALSE' AND inv_no='$inv_no' ");
    if( $res3 = pg_fetch_array($qry3) ){
        $remain_amount = $res3['amt'];
    }
	$sum_remain += $remain_amount;
	}
	
	$appointment_amt = $down_price;
	$reserve_amount = 0;
	$qry_resdt = pg_query("select sum(pay_amount)as amount from v_down_balance where res_id = '$res_id'");
	while($res_resdt = pg_fetch_array($qry_resdt)){
		$j++;

		$amount_V = $res_resdt['amount'];
    
		$appointment_amt -= $amount_V;
		$reserve_amount += $amount_V;
	}
	
	$sum_reserve += $reserve_amount;
	
    if($cancel == 't'){
        echo "<tr bgcolor=\"#ffcccc\">";
    }else{
        if($j%2==0){
            echo "<tr class=\"odd\">";
        }else{
            echo "<tr class=\"even\">";
        }
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $res_id; ?>')"><u><?php echo $res_id; ?></u></td>
    <td align="center"><?php echo $reserve_date; ?></td>
    <td><?php echo $cus_name; ?></td>
    <td><?php echo $car_name; ?></td>
    <td align="right"><?php echo number_format($car_price,2); ?></td>
    <td align="right"><?php echo number_format($down_price,2); ?></td>
    <td align="right"><?php echo number_format($reserve_amount,2); ?></td>
    <td align="right"><?php echo number_format($appointment_amt,2); ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"8\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}else{
    echo "<tr bgcolor=\"#FFFFD9\">
    <td colspan=\"4\"><b>รวม</b></td>
    <td align=\"right\"><b>".number_format($sum_car_price,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_down_price,2)."</b></td>
    <td align=\"right\"><b>".number_format($sum_reserve,2)."</b></td>
    <td></td>
    </tr>";
}
?>
</table>

<div style="margin-top:5px">รายการที่มีแถบ <span style="background-color:#ffcccc">สีแดง</span> คือ รายการที่ยกเลิก</div>

<script>
function ShowDetail(id){
    $('body').append('<div id="divdetail"></div>');
    $('#divdetail').load('report_reserve_api.php?cmd=showdetail&id='+id);
    $('#divdetail').dialog({
        title: 'รายละเอียดการจอง : '+id,
        resizable: false,
        modal: true,  
        width: 800,
        height: 450,
        close: function(ev, ui){
            $('#divdetail').remove();
        }
    });
}
</script>
<?php
}

elseif($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("select * from \"Customers\" WHERE \"cus_name\" LIKE '%$term%' ORDER BY \"cus_name\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $cus_id = trim($res_name["cus_id"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        
        $dt['value'] = $cus_id."#".$cus_name." ".$surname;
        $dt['label'] = "{$cus_id} , {$cus_name} {$surname}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "showdetail"){
    $id = $_GET['id'];

$qry_res = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$id' ");
if($res_res = pg_fetch_array($qry_res)){
    $cus_id = $res_res['cus_id'];
    $car_id = $res_res['car_id'];
    $product_id = $res_res['product_id'];    
    $remark = $res_res['remark'];
       /* if(empty($car_id)){
            $arr_remark = explode("\n",$remark);
            $arr_remark1 = explode("=",$arr_remark[0]);
            $arr_remark2 = explode("=",$arr_remark[1]);
     }*/
    $down_price = $res_res['down_price'];
    $car_price = $res_res['car_price'];
    $num_install = $res_res['num_install'];
    $installment = $res_res['installment'];
    $finance_price = $res_res['finance_price'];
    $finance_cus_id = $res_res['finance_cus_id'];
    
    $type_insure = $res_res['type_insure'];
    $use_radio = $res_res['use_radio'];
    $user_id = $res_res['user_id'];
	
	$reserve_status = $res_res['reserve_status'];
	$remark_cancel = $res_res['remark_cancel'];
	$IDNO = $res_res['IDNO'];
}

$user_name = GetUserName($user_id);
$cus_name = GetCusName($cus_id);
$finance_cus_name = GetCusName($finance_cus_id);

$money_buy_pay = $car_price-$finance_price;

$qry = pg_query("SELECT telephone FROM \"Customers\" WHERE cus_id='$cus_id' ");
if($res = pg_fetch_array($qry)){
    $telephone=$res['telephone'];
}

$qry = pg_query("select car_idno from \"Cars\" where car_id = '$car_id' ");
$car_idno = pg_fetch_result($qry,0);
?>
<div style="text-align:left">
<b>ผู้จอง : </b><?php echo $cus_name; ?>
<br>
<b>เบอร์โทรติดต่อ : </b><?php echo $telephone; ?>
<br>
<b>ผู้รับจอง : </b><?php echo $user_name; ?>
</div>

<?php if($reserve_status == 0){ ?>	
<div style="text-align:right">
	<b><font color = "red" >ยกเลิก</font></b>
</div>
<?php } ?>

<div class="linedotted"></div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td valign="top" width="50%">
<div style="margin-top:10px">
<?php
if(empty($car_id)){
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150"><b>รูปแบบการจอง :</b></td><td>ไม่เจาะจงรถ</td>
</tr>
<tr>
    <td><b>เลขที่ส่งมอบ :</b></td><td><?php echo $IDNO; ?></td>
</tr>
<tr>
    <td><b>รูปแบบรถ :</b></td><td><?php echo $arr_remark1[1]; ?></td>
</tr>
<tr>
    <td><b>สีรถ :</b></td><td><?php echo $arr_remark2[1]; ?></td>
</tr>
<tr>
    <td><b>ประกันประเภท :</b></td><td><?php echo $type_insure; ?></td>
</tr>
<tr>
    <td><b>ติดตั้งเครื่องวิทยุสื่อสาร :</b></td><td><?php if($use_radio == 't') echo "ติดตั้ง"; else echo "ไม่ติดตั้ง"; ?></td>
</tr>
</table>
<?php
}else{
    $qry_cname = pg_query("SELECT * FROM \"Cars\" WHERE car_id='$car_id' AND cancel='FALSE' ");
    if($res_cname = pg_fetch_array($qry_cname)){
        $car_license_plate = $res_cname['license_plate'];
        $car_num = $res_cname['car_num'];
        $mar_num = $res_cname['mar_num'];
        $car_year = $res_cname['car_year'];
        $color = $res_cname['color'];
        
        $cost_val = $res_cname['cost_val'];
        $cost_vat = $res_cname['cost_vat'];
        $car_name = $res_cname['car_name'];
    }
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150"><b>รูปแบบการจอง :</b></td><td>เจาะจงรถ</td>
</tr>
<tr>
    <td><b>เลขที่ส่งมอบ :</b></td><td><?php echo $IDNO; ?></td>
</tr>
<tr>
    <td><b>ทะเบียนรถ :</b></td><td><?php echo $car_license_plate; ?></td>
</tr>
<tr>
    <td><b>เลขเครื่อง :</b></td><td><?php echo $mar_num; ?></td>
</tr>
<tr>
    <td><b>เลขถัง :</b></td><td><?php echo $car_num; ?></td>
</tr>
<tr>
    <td><b>ปีรถ :</b></td><td><?php echo $car_year; ?></td>
</tr>
<tr>
    <td><b>สีรถ :</b></td><td><?php echo getCarColor($color); ?></td>
</tr>
<tr>
    <td><b>ประกันประเภท :</b></td><td><?php echo $type_insure; ?></td>
</tr>
<tr>
    <td><b>ติดตั้งเครื่องวิทยุสื่อสาร :</b></td><td><?php if($use_radio == 't') echo "ติดตั้ง"; else echo "ไม่ติดตั้ง"; ?></td>
</tr>
</table>
<?php
}
?>
</div>
    </td>
    <td valign="top" width="50%">
<div style="margin-top:10px">
<?php
if($car_price == $down_price){
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100"><b>รูปแบบการซื้อ :</b></td><td>ซื้อสด</td>
</tr>
<tr>
    <td><b>ราคารถ :</b></td><td><?php echo number_format($car_price,2); ?></td>
</tr>
</table>
<?php
}elseif($car_price > $down_price){
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100"><b>รูปแบบการซื้อ :</b></td><td>ซื้อผ่อน</td>
</tr>
<tr>
    <td><b>ราคารถ :</b></td><td><?php echo number_format($car_price,2); ?></td>
</tr>
<tr>
    <td><b>ดาวน์ :</b></td><td><?php echo number_format($down_price,2); ?></td>
</tr>
<tr>
    <td><b>ยอดจัดเช่าซื้อ :</b></td><td><?php echo number_format($finance_price,2); ?></td>
</tr>
<tr>
    <td><b>จำนวนงวดผ่อน :</b></td><td><?php echo $num_install; ?></td>
</tr>
<tr>
    <td><b>ค่างวด :</b></td><td><?php echo number_format($installment,2); ?> | ดอกเบี้ย = <?php echo interest_rate($car_price-$down_price,$installment,$num_install); ?></td>
</tr>
<tr>
    <td><b>บริษัท Finance :</b></td><td><?php echo $finance_cus_name; ?></td>
</tr>
</table>
<?php
}
?>
    
<?php
if(!empty($car_id)){
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%" style="margin-top:15px">
<tr>
    <td width="100"><b>ยี่ห้อรถยนต์ :</b></td><td><?php echo $car_name; ?></td>
</tr>
<tr>
	<td><b>ทะเบียนสต๊อก :</b></td><td><?php echo $car_idno; ?></td>
</tr>
<tr>
    <td><b>ต้นทุนราคารถ :</b></td><td><?php echo number_format($cost_val,2); ?></td>
</tr>
<tr>
    <td><b>Vat :</b></td><td><?php echo number_format($cost_vat,2); ?></td>
</tr>
<tr>
    <td><b>รวมต้นทุน :</b></td><td><?php echo number_format($cost_val+$cost_vat,2); ?></td>
</tr>
</table>
<?php
}
?>
    
</div>
    </td>
</tr>
</table>

<div class="linedotted"></div>

<div>
<div style="margin-top:5px"><b>รายละเอียดหนี้ที่ค้างชำระ</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#ffcccc" style="font-weight:bold; text-align:center">
    <td>เลขที่ใบแจ้งหนี้</td>
    <td>รายละเอียดค่าใช้จ่าย</td>
    <td>ยอดเงิน</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE res_id='$id' AND status IS NULL AND cancel = 'FALSE' ORDER BY inv_no ASC ");
while( $res = pg_fetch_array($qry) ){
    $j++;
    $inv_no = $res['inv_no'];
    $cus_id = $res['cus_id'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    $branch_out = $res['branch_out'];
    
    $cus_name = GetCusName($cus_id);
    $branch_out = GetWarehousesName($branch_out);
    
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
    $name = implode(",", $arr_name);
    
    $qry3 = pg_query("SELECT SUM(amount+vat) as amt FROM \"VInvDetail\" WHERE cancel='FALSE' AND inv_no='$inv_no' ");
    if( $res3 = pg_fetch_array($qry3) ){
        $amount = $res3['amt'];
    }

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $inv_no; ?></td>
    <td><?php echo $name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=4 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
</div>

<div class="linedotted"></div>

<div>
<div style="margin-top:5px"><b>รายละเอียดการจอง/มัดจำ</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>บริการ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
$j = 0;
$qry_resdt = pg_query("SELECT A.*,B.* FROM \"VOtherpay2\" A LEFT JOIN \"Services\" B on A.service_id = B.service_id 
WHERE A.res_id='$id' AND A.o_receipt IS NOT NULL AND B.constant_var IS NOT NULL ORDER BY inv_no ASC ");
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    $service_id = $res_resdt['service_id'];
    $service_name = $res_resdt['name'];
    $status = $res_resdt['status'];
    $o_date = $res_resdt['o_date'];
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $o_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$status"; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>

<div style="margin-top:5px"><b>รายละเอียดส่่วนลด</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>บริการ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
$j = 0;
$qry_resdt = pg_query("SELECT A.*,B.* FROM \"VDiscountpay\" A LEFT JOIN \"Services\" B on A.service_id = B.service_id 
WHERE A.res_id='$id' AND A.o_receipt IS NOT NULL AND B.constant_var IS NOT NULL ORDER BY inv_no ASC ");
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    $service_id = $res_resdt['service_id'];
    $service_name = $res_resdt['name'];
    $status = $res_resdt['status'];
    $o_date = $res_resdt['o_date'];
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $o_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td><?php echo $service_name.'-ส่วนลด'; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$status"; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>

<?php
$j = 0;
$qry_resdt = pg_query("SELECT A.*,B.* FROM \"VOtherpay2\" A LEFT JOIN \"Services\" B on A.service_id = B.service_id 
WHERE A.res_id='$id' AND A.o_receipt IS NOT NULL AND B.constant_var IS NULL ORDER BY inv_no ASC ");
$qry_resdt_num = pg_num_rows($qry_resdt);
if($qry_resdt_num > 0){
?>
<div style="margin-top:5px"><b>รายละเอียดค่าใช้จ่ายอื่นๆ</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#F5DEB3" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>บริการ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    $service_id = $res_resdt['service_id'];
    $service_name = $res_resdt['name'];
    $status = $res_resdt['status'];
    $o_date = $res_resdt['o_date'];
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $o_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$status"; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>
<?php
}
?>

<?php
$qry_vidt_num = 0;
/*$qry_vidt = pg_query("SELECT * FROM \"VChequeDetail\" 
WHERE res_id ='$id' AND accept = 'TRUE' AND is_pass = 'FALSE' 
ORDER BY inv_no ASC ");*/

$qry_vidt = pg_query("SELECT * FROM v_chq_detail WHERE res_id ='$id' ORDER BY inv_no ASC ");
$qry_vidt_num = pg_num_rows($qry_vidt);
if($qry_vidt_num > 0){
?>
<div style="margin-top:5px"><b>รายละเอียดการจ่ายเช็ค</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#C9C9E4" style="font-weight:bold; text-align:center">
    <td>วันที่รับ</td>
    <td>ธนาคาร</td>
    <td>เลขที่เช็ค</td>
    <td>วันที่บนเช็ค</td>
    <td>ค่าใช้จ่าย</td>
    <td>ยอดเงิน</td>
</tr>
<?php
while($res_vidt = pg_fetch_array($qry_vidt)){
    $receive_date = $res_vidt['receive_date'];
    $bank_name = $res_vidt['bank_name'];
    $cheque_no = $res_vidt['cheque_no'];
    $date_on_cheque = $res_vidt['date_on_cheque'];
    $cus_amount = $res_vidt['cus_amount'];
    $service_id = $res_vidt['service_id'];
        $service_name = GetServicesName($service_id);
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $receive_date; ?></td>
    <td align="left"><?php echo $bank_name; ?></td>
    <td align="left"><?php echo $cheque_no; ?></td>
    <td align="center"><?php echo $date_on_cheque; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($cus_amount,2); ?></td>
</tr>
<?php
}
?>
</table>

<?php
}
?>

<div class="linedotted"></div>
</div>

<div style="margin-top:5px">
<b>หมายเหตุ</b><br>
<?php echo $remark; ?>
</div>
<?php if($reserve_status == 0){ ?>	
<div style="text-align:left;margin-top:5px">
	<b>หมายุเหตุที่ยกเลิก</b></br>
	<?php echo $remark_cancel; ?>
</div>
<?php } ?>
<?php
}
?>