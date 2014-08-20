<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
$wh = pg_escape_string($_GET['wh']);

if($cmd == "tab1"){
    $radio_type = pg_escape_string($_GET['radio_type']);
    $chk_color = pg_escape_string($_GET['chk_color']);
    $cb_color = pg_escape_string($_GET['cb_color']);
    $chk_car_type = pg_escape_string($_GET['chk_car_type']);
    $cb_car_type = pg_escape_string($_GET['cb_car_type']);

    $str_where = "";

    if($radio_type == 2){
        $str_where .= "car_status <>'S'";
    }elseif($radio_type == 3){
        $str_where .= "car_status ='R'";
    }elseif($radio_type == 4){
        $str_where .= "car_status ='S'";
    }elseif($radio_type == 5){
        $str_where .= "car_id in (select car_id from \"CarMoveToCus\" where status_appv = '9')";
    }

    if($chk_color == "on"){
        if($radio_type == 1){
            if($cb_color != ""){
                $str_where .= " color='$cb_color'";
            }else{
                $str_where .= "";
            }
        }else{
            if($cb_color != ""){
                $str_where .= " AND color='$cb_color'";
            }else{
                $str_where .= "";
            }
        }
    }
    
    if($chk_car_type == "on"){
        if(!empty($str_where)){
            $str_where .= " AND product_id='$cb_car_type'";
        }else{
            $str_where .= " product_id='$cb_car_type'";
        }
    }
    
    if(!empty($str_where)){
        $where = " WHERE ".$str_where;
    }
?>

<div style="text-align:right">

<a href="report_warehouse_pdf.php?radio_type=<?php echo $radio_type; ?>&chk_color=<?php echo $chk_color; ?>&cb_color=<?php echo $cb_color; ?>&chk_car_type=<?php echo $chk_car_type; ?>&cb_car_type=<?php echo $cb_car_type; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a>
</div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
	<td>ทะเบียนในสต๊อก</td>
    <td>ทะเบียน</td>
    <td>Product</td>
    <td>เลขถัง</td>
    <td>เลขเครื่อง</td>
    <td>ปีรถ</td>
    <td>สีรถ</td>
    <td>เลขจอง</td>
</tr>
<?php
$j = 0;
$qry_str = "SELECT * FROM \"VStockCars\"$where ORDER BY car_id ASC";
$qry = pg_query($qry_str);
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $car_year = $res['car_year'];
    $color = $res['color'];
    $license_plate = $res['license_plate'];
    $IDNO = $res['IDNO'];
    $car_idno = $res['car_idno'];
    $product_name = GetProductName($product_id);
    
	$qry_r = pg_query("SELECT * FROM \"V_CarsReserve\" WHERE \"car_id\"='$car_id'");
    if($res_r = pg_fetch_array($qry_r)){
        $res_id = $res_r['res_id'];
		$reserve_status = $res_r['reserve_status'];
    }
	$num = pg_num_rows($qry_r);
	
	if($num>1){
		$txt_res_id = "<span id=\"listR\" onclick=\"javascript:show_car_reserve('$car_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>จอง > 1</u></font></span>";
	}else if($num==1){
		if($res_id == "" or ($res_id !="" and $reserve_status == '0')){
			$txt_res_id = "ยังไม่มีการจอง";
		}else{
			$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
		}
	}
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $product_name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
    <td><?php echo $car_year; ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td align="center"><?php echo $txt_res_id; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=9 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
function show_car_reserve(car_id){
	popU('../car/list_car_reserve.php?car_id='+car_id,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
}
function ShowDetail(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('../car/movement_car_api.php?cmd=divshow&btn=1&id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียดการเคลื่อนไหว : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
function ShowDetailres(id){
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

elseif($cmd == "tab2"){

if($wh == "all"){
?>
<div style="margin-top:5px; margin-bottom:10px; text-align:right">
<a href="report_warehouse_pdf1.php?wh=all" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> พิมพ์สรุป</span></a> |
<a href="report_warehouse_pdf2.php?wh=all" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> พิมพ์แสดงรายละเอียด</span></a>
</div>
<?php
    $qry_wh = pg_query("SELECT * FROM \"Warehouses\" WHERE cancel='FALSE' ORDER BY wh_name ASC ");
    while($res_wh = pg_fetch_array($qry_wh)){
        $wh_id = $res_wh['wh_id'];
        $wh_name = $res_wh['wh_name'];
        
$qry_num = 0;
$qry = pg_query("SELECT m.car_id,c.license_plate,c.product_id,c.car_num,m.date_in,
(select v1.\"color\" from \"VStockCars\" v1 where v1.car_id = m.car_id limit 1) as color,
(select v2.\"car_idno\" from \"VStockCars\" v2 where v2.car_id = m.car_id limit 1) as car_idno
FROM \"CarMove\" m LEFT JOIN \"Cars\" c ON m.car_id=c.car_id AND c.cancel='FALSE' 
WHERE m.date_out IS NULL AND m.wh_id='$wh_id' AND c.cancel='FALSE' 
ORDER BY m.car_id ASC ");
$qry_num = pg_num_rows($qry);

$count_all += $qry_num;
?>

<div style="float:left; font-weight:bold">สถานที่ : <?php echo $wh_name; ?></div>
<div style="float:right"><?php echo "ทั้งหมด $qry_num รายการ"; ?></div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
    <td>ทะเบียน</td>
	<td>ทะเบียนรถยนต์ในสต๊อก</td>
    <td>สีรถ</td>
    <td>Product</td>
    <td>เลขถัง</td>
    <td>Date In</td>
</tr>
<?php
$j = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $license_plate = $res['license_plate'];
    $color = $res['color'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $date_in = $res['date_in'];
	$car_idno = $res['car_idno'];
    
    $product_name = GetProductName($product_id);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
    <td><?php echo $license_plate; ?></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td><?php echo $product_name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $date_in; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<div style="margin-bottom:15px"></div>

<?php
    }
?>

<div class="linedotted"></div>
<div style="text-align:right; font-weight:bold; margin-top:5px">รวมทั้งหมด <?php echo $count_all; ?> รายการ</div>

<script>
function ShowDetail(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('../car/movement_car_api.php?cmd=divshow&btn=1&id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียดการเคลื่อนไหว : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
</script>

<?php
}

else{
$qry = pg_query("SELECT m.car_id,c.license_plate,c.product_id,c.car_num,m.date_in,
(select v1.\"color\" from \"VStockCars\" v1 where v1.car_id = m.car_id limit 1) as color,
(select v2.\"car_idno\" from \"VStockCars\" v2 where v2.car_id = m.car_id limit 1) as car_idno
FROM \"CarMove\" m LEFT JOIN \"Cars\" c ON m.car_id=c.car_id 
WHERE m.date_out IS NULL AND wh_id='$wh' AND c.cancel='FALSE' 
ORDER BY m.car_id ASC ");
$qry_num = pg_num_rows($qry);

$warehouse_name = GetWarehousesName($wh);
?>

<div style="margin-top:5px; margin-bottom:10px; text-align:right">
<a href="report_warehouse_pdf1.php?wh=<?php echo $wh; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> พิมพ์สรุป</span></a> |
<a href="report_warehouse_pdf2.php?wh=<?php echo $wh; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> พิมพ์แสดงรายละเอียด</span></a>
</div>

<div style="float:left">สถานที่ : <?php echo $warehouse_name; ?></div>
<div style="float:right"><?php echo "ทั้งหมด $qry_num รายการ"; ?></div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
    <td>ทะเบียน</td>
	<td>ทะเบียนรถยนต์ในสต๊อก</td>
    <td>สีรถ</td>
    <td>Product</td>
    <td>เลขถัง</td>
    <td>Date In</td>
</tr>
<?php
$j = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $license_plate = $res['license_plate'];
	$car_idno = $res['car_idno'];
    $color = $res['color'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $date_in = $res['date_in'];
    
    $product_name = GetProductName($product_id);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
    <td><?php echo $license_plate; ?></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td><?php echo $product_name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $date_in; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function ShowDetail(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('../car/movement_car_api.php?cmd=divshow&btn=1&id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียดการเคลื่อนไหว : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
</script>

<?php
}

}

if($cmd == "tab3"){

    $cartype = pg_escape_string($_GET['cartype']);

	if($cartype == ""){
				$where = "";
	}else if($cartype == "2"){
		$where = "where car_type_id in ('2','3','4') and substr(car_idno,3,2) != 'SE' ";
	}else if($cartype == "6"){
		$where = "where substr(car_idno,3,2) = 'SE' ";
	}else if($cartype == "7"){
		$where = "where car_status = 'R' ";
	}else if($cartype == "8"){
		$where = "where car_status = 'A' ";		
	}else if($cartype == "9"){
		$where = "where car_status = 'S' ";	
	}else if($cartype != "2"){
			$where = "where car_type_id = '$cartype' ";
	}
	
?>

<div>สถานะ (S) --> A-ว่าง รอขาย , R-จอง, Y-รถยึดฝากจอด , S-ขายแล้ว , P-รถซ่อมฝากจอด</div>

<div style="text-align:right">
<a href="report_warehouse3_pdf.php?&cartype=<?php echo $cartype; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a>
</div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
	<td>ทะเบียนในสต๊อก</td>
	<td>ทะเบียน</td>
    <td>Product</td>
    <td>เลขถัง</td>
    <td>เลขเครื่อง</td>
    <td>ปีรถ</td>
    <td>สีรถ</td>
    <td>เลขจอง</td>
	<td>S</td>
</tr>
<?php
$j = 0;
$qry_str = "SELECT * FROM \"VStockCars\" $where ORDER BY car_id ASC";

$qry = pg_query($qry_str);
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $car_year = $res['car_year'];
    $color = $res['color'];
    $license_plate = $res['license_plate'];
    $IDNO = $res['IDNO'];
    $car_idno = $res['car_idno'];
    $product_name = GetProductName($product_id);
	$car_status = $res['car_status'];
    
	$qry_res = pg_query("SELECT * FROM \"V_CarsReserve\" WHERE \"car_id\"='$car_id'");
	$num = pg_num_rows($qry_res);
	
    if($res_r = pg_fetch_array($qry_res)){
        $res_id = $res_r ['res_id'];
		$reserve_status = $res_r ['reserve_status'];
    }
	
	
	if($num>1){
		$txt_res_id = "<span id=\"listR\" onclick=\"javascript:show_car_reserve('$car_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>จอง > 1</u></font></span>";
	}else if($num==1){
		if($res_id == "" or ($res_id !="" and $reserve_status == '0')){
			$txt_res_id = "ยังไม่มีการจอง";
		}else{
			$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
		}
	}
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $product_name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
    <td><?php echo $car_year; ?></td>
       <td><?php echo getCarColor($color); ?></td>
   <td align="center"><?php echo $txt_res_id; ?></td>
   <td><?php echo $car_status; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=9 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
function show_car_reserve(car_id){
	popU('../car/list_car_reserve.php?car_id='+car_id,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
}
function ShowDetail(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('../car/movement_car_api.php?cmd=divshow&btn=1&id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียดการเคลื่อนไหว : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
function ShowDetailres(id){
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
?>

<?php
if($cmd == "tab4"){

    $condition = pg_escape_string($_GET['condition']);
	$keyword = pg_escape_string($_GET['keyword']);

	if($condition == "car_regis"){
		$where = "where license_plate like '%$keyword%' ORDER BY license_plate ASC";
	}else if($condition == "carnum"){
		$where = "where car_num like '%$keyword%' ORDER BY car_num ASC ";
	}else if($condition == "marnum"){
		$where = "where mar_num like '%$keyword%' ORDER BY mar_num ASC ";
	}else if($condition == "car_idno"){
		$where = "where car_idno like '%$keyword%' ORDER BY car_idno ASC ";
	}
	
?>


<div style="text-align:right">

<a href="report_warehouse4_pdf.php?&condition=<?php echo $condition; ?>&keyword=<?php echo $keyword; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a>
</div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
	<td>ทะเบียนในสต๊อก</td>
    <td>ทะเบียน</td>
    <td>Product</td>
    <td>เลขถัง</td>
    <td>เลขเครื่อง</td>
    <td>ปีรถ</td>
    <td>สีรถ</td>
    <td>เลขจอง</td>
	<td>แก้ไข</td>
</tr>
<?php
$j = 0;
$txt_qry = "SELECT * FROM \"VStockCars\" $where ";
$qry = pg_query($txt_qry);
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $product_id = $res['product_id'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $car_year = $res['car_year'];
    $color = $res['color'];
    $license_plate = $res['license_plate'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    $car_idno = $res['car_idno'];
    $product_name = GetProductName($product_id);
    
	$qry_res = pg_query("SELECT * FROM \"V_CarsReserve\" WHERE \"car_id\"='$car_id'");
	$num = pg_num_rows($qry_res);
	
    if($res_r = pg_fetch_array($qry_res)){
        $res_id = $res_r ['res_id'];
		$reserve_status = $res_r ['reserve_status'];
    }
	
	
	if($num>1){
		$txt_res_id = "<span id=\"listR\" onclick=\"javascript:show_car_reserve('$car_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>จอง > 1</u></font></span>";
	}else if($num==1){
		if($res_id == "" or ($res_id !="" and $reserve_status == '0')){
			$txt_res_id = "ยังไม่มีการจอง";
		}else{
			$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
		}
	}
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $product_name; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
    <td><?php echo $car_year; ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td align="center"><?php echo $txt_res_id; ?></td>
	<td align="center"><a href="javascript:CarsEdit('<?php echo $car_id; ?>')"><img src="../images/edit.png" border="0" width="20" height="20" title="แก้ไขรายละเอียดตัวรถ" alt="แก้ไขรายละเอียดตัวรถ"></a></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=9 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
function show_car_reserve(car_id){
	popU('../car/list_car_reserve.php?car_id='+car_id,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
}

function ShowDetail(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('../car/movement_car_api.php?cmd=divshow&btn=1&id='+id);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียดการเคลื่อนไหว : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}

function CarsEdit(id){
    $('body').append('<div id="divdialogadd"></div>');
    $('#divdialogadd').load('../car/movement_car_api.php?cmd=CarsEditTemp&id='+id);
    $('#divdialogadd').dialog({
        title: 'แก้ไขรายละเอียดตัวรถ : '+id,
        resizable: false,
        modal: true,  
        width: 400,
        height: 300,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}

function ShowDetailres(id){
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
?>