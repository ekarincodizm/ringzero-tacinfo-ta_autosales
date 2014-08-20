<?php
include_once("../include/config.php");
include_once("../include/function.php");


$cmd = $_REQUEST['cmd'];

if($cmd == "cancel"){ //ยกเลิกการจอง ให้ยกเลิก invoice ด้วย
    $id = $_POST['id'];
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $qry_res = pg_query("SELECT car_id FROM \"Reserves\" WHERE res_id='$id' ");
    if($res_res = pg_fetch_array($qry_res)){
        $car_id = $res_res['car_id'];
    }
    
    if(!empty($car_id)){
        $in_qry="UPDATE \"Cars\" SET res_id=DEFAULT WHERE car_id='$car_id' ";
        if(!$res=@pg_query($in_qry)){
            $status++;
            $txt_error[] = "ไม่สามารถบันทึกได้! $in_qry";
        }
    }

		$in_qry="UPDATE \"Reserves\" SET cancel = 'TRUE',car_id=DEFAULT,reserve_status='0',remark_cancel='$remark' WHERE res_id='$id' ";
		if(!$res=@pg_query($in_qry)){
			$status++;
			$txt_error[] = "ไม่สามารถบันทึกได้! $in_qry";
		}
		
		//ดึงข้อมูล invoice เพื่อจะมาทำการ ยกเลิก
		$qry_inv = pg_query(" SELECT inv_no,res_id FROM \"Invoices\" WHERE res_id='$id' ");
		$arr_inv_no = array();
		if($res_res = pg_fetch_array($qry_inv)){
			$inv_no = $res_res['inv_no'];
			$arr_inv_no[] = $inv_no;
		}
		$str_arr_inv_no = "('" .implode("','",$arr_inv_no)."')";
		
		$up_inv_detail = "UPDATE \"InvoiceDetails\" SET cancel = 'TRUE' WHERE inv_no in $str_arr_inv_no ";
		if(!$res=@pg_query($up_inv_detail)){
			$status++;
			$txt_error[] = "ไม่สามารถบันทึกได้! $up_inv_detail";
		}
		
		
		$up_inv = "UPDATE \"Invoices\" SET cancel = 'TRUE' WHERE res_id = '$id' ";
		if(!$res=@pg_query($up_inv)){
			$status++;
			$txt_error[] = "ไม่สามารถบันทึกได้! $up_inv";
		}
		
		
    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
        
    echo json_encode($data);
}elseif($cmd == "cancel_invoice"){ //============= ยกเลิกใบ invoice ============//

$param_inv_no = $_POST['param_inv_no'];

pg_query("BEGIN WORK");
	$status = 0;
	
	$delete_invoice_detail = "UPDATE \"InvoiceDetails\" SET cancel ='TRUE' WHERE inv_no = '$param_inv_no' ";
		if(!$res = pg_query($delete_invoice_detail)){
			$txt_error[] = "ลขข้อมูล InvoiceDetails ไม่สำเร็จ $delete_invoice_detail";
			$status++;
		}
		
	$delete_invoice = "UPDATE \"Invoices\" SET cancel = 'TRUE' WHERE inv_no = '$param_inv_no' ";
		if(!$res = pg_query($delete_invoice)){
			$txt_error[] = "ลขข้อมูล Invoices ไม่สำเร็จ $delete_invoice_detail";
			$status++;
		}
		
		
	if($status == 0){
		pg_query("COMMIT");
		$data['success'] = true;
		$data['message'] = "ยกเลิกใบแจ้งหนี้เรียบร้อยแล้ว ";
	}else{
		pg_query("ROLLBACK");
		$data['success'] = false;
		$data['message'] = "ไม่สามารถลบข้อมูลได้! $txt_error[0]";
	}
echo json_encode($data);

}elseif($cmd == "plus"){ // ชำระเงินจองเพิ่มเติม
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $resid = $_POST['resid'];
    $cusid = $_POST['cusid'];
    $product_id = $_POST['product_id'];
    $txt_moneyreserv = $_POST['txt_moneyreserv'];
    $txt_dateoutcar = $_POST['txt_dateoutcar'];
    $span_appointment_amt = $_POST['span_appointment_amt'];
	$span_balance_amt = $_POST['span_balance_amt'];
    $txt_hid_dialog_type = $_POST['txt_hid_dialog_type'];
    $area_remark = $_POST['area_remark'];
    $car_id = $_POST['car_id'];

        $in_qry="INSERT INTO \"ReserveDetails\" (res_id,do_date,cash_amt,cheque_no,cheque_bank,cheque_branch,cheque_amt,cheque_date,
												appointment_date,appointment_amt) 
										VALUES ('$resid','$nowdate','0',DEFAULT,DEFAULT,DEFAULT,'0',DEFAULT,'$txt_dateoutcar','$span_appointment_amt')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก ReserveDetails ไม่สำเร็จ $in_qry";
            $status++;
        }

//============================ ตรวจสอบเงินดาวน์คงเหลือว่าเท่ากับ ศูนย์ บาท หรือไม่ ===================================//
        $carpr = "";
		if($txt_hid_dialog_type == 1){ // ถ้าจำนวนงวดเท่ากับ 0 คือซื้อเงินสด
			//if( $span_appointment_amt != 0 ){ //ถ้าดาวน์คงเหลือไม่เท่ากับ ศูนย์ บาท
			 if($span_balance_amt != 0){
				$carpr = GetServicesConstant('CARRE'); //จองเพิ่ม
				$str_car_id = "DEFAULT";
			}else{
				$carpr = GetServicesConstant('CARCA'); //ออกรถ ซื้อสด
			}
        }else{ //ซื้อเงินผ่อน
			//if( $span_appointment_amt != 0 ){
			if($span_balance_amt !=0 ){
				$carpr = GetServicesConstant('CARRE'); 
				$str_car_id = "DEFAULT";
			}else{ //เงินดาวน์เป็น ศูนย์  บาท
                $carpr = GetServicesConstant('CARDW');//ออกรถ ซื้อผ่อน
            }
        }
		
		if( empty($car_id) ){
			$str_car_id =  "'$car_id'";
		}
		
	/*	$str_car_id = "'$car_id'"; 
        
		if($car_id == ""){
           $txt_error[] = "ไม่พบ CAR_ID";
           $status++;
        }*/
		
		
//==================================== สร้างเลขที่ใบแจ้งหนี้ invoice =================================//
        $generate_id=@pg_query("select gen_rec_no('$nowdate',1)");
        $inv_no=@pg_fetch_result($generate_id,0);
//------------------------------------------------------------------------------------//

//========================== บันทึกข้อมูลลง table Invoices และ InvoiceDetails ====================//
        $in_qry="INSERT INTO \"Invoices\" (inv_no,res_id,cus_id,\"IDNO\",inv_date,prn_date,branch_out,
											status,user_id,car_id) 
									VALUES ('$inv_no','$resid','$cusid',DEFAULT,'$nowdate','$nowdate','$_SESSION[ss_office_id]',
											DEFAULT,'$_SESSION[ss_iduser]','$str_car_id')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก Invoices ไม่สำเร็จ $in_qry";
            $status++;
        }

        $in_qry="INSERT INTO \"InvoiceDetails\" (inv_no,service_id,amount,vat) 
										VALUES('$inv_no','$carpr','$txt_moneyreserv','0')";
		
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก InvoiceDetails ไม่สำเร็จ $in_qry";
            $status++;
        }
 //--------------------------------------------------------------------------------------//
    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
        
    echo json_encode($data);
    
}

elseif($cmd == "editdown"){
    $id = $_GET['id'];

    $qry_res = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$id' ");
    if($res_res = pg_fetch_array($qry_res)){
        $cus_id = $res_res['cus_id'];
        $car_id = $res_res['car_id'];
        $product_id = $res_res['product_id'];
        $down_price = $res_res['down_price'];
        $car_price = $res_res['car_price'];
        $num_install = $res_res['num_install'];
        $installment = $res_res['installment'];
        $finance_price = $res_res['finance_price'];
        $finance_cus_id = $res_res['finance_cus_id'];
        $finance_cus_name = GetCusName($finance_cus_id);
        
       
	   $remark = $res_res['remark'];
        if(empty($car_id)){
            $arr_remark = explode("\n",$remark);
            $arr_remark1 = explode("=",$arr_remark[0]);
            $arr_remark2 = explode("=",$arr_remark[1]);
            
            if($arr_remark2[1] == "ฟ้า"){
                $str_edit_color1 = "checked";
            }elseif($arr_remark2[1] == "เหลือง"){
                $str_edit_color2 = "checked";
            }elseif($arr_remark2[1] == "เขียวเหลือง"){
                $str_edit_color3 = "checked";
            }
        }
        
    }
?>

<div style="margin:5px">
<b>รูปแบบการจอง</b> : 
 <?php
if(empty($car_id)){
    $hid_process_car = 1;
    echo "ไม่เจาะจงรถ";
}else{
    $hid_process_car = 0;
    echo "เจาะจงรถ (ไม่สามารถแก้ไขการจองได้)";
}

?>
</div>

<?php //========================== เจาะจงรถ ==============================//?>
<div id="edit_divresv1" style="margin-top:10px; margin-left:5px; <?php if(!empty($car_id)){ echo "display:none"; } ?>">
	<label>รูปแบบรถ :</label> 
	<select name="edit_resv_car_plan" id="edit_resv_car_plan" onchange="javascript:edit_change_car_plan()">
		<option value="">เลือก</option>
		<?php
		$qry = pg_query("SELECT * FROM \"Products\" WHERE \"link_table\"='Cars' ORDER BY \"name\" ASC ");
		while($res = pg_fetch_array($qry)){
			$pd_product_id = $res['product_id'];
			$pd_name = $res['name'];
			if($product_id == $pd_product_id){
				echo "<option value=\"$pd_product_id#$pd_name\" selected>$pd_name</option>";
			}else{
				echo "<option value=\"$pd_product_id#$pd_name\">$pd_name</option>";
			}
		}
		?>
	</select>&nbsp;
	<label>สีรถ :</label> 

	<input type="radio" name="edit_radio_car_color" id="edit_radio_car_color" value="ฟ้า" <?php echo $str_edit_color1; ?>>ฟ้า 
	<input type="radio" name="edit_radio_car_color" id="edit_radio_car_color" value="เหลือง" <?php echo $str_edit_color2; ?>>เหลือง 
	<input type="radio" name="edit_radio_car_color" id="edit_radio_car_color" value="เขียวเหลือง" <?php echo $str_edit_color3; ?>>เขียวเหลือง 
</div>

<div class="linedotted"></div>

<?php
if($down_price == 0){
    $hid_process_buy = 0;
    echo "<div style=\"margin:5px\"><b>รูปแบบการซื้อ :</b> ซื้อสด  (ไม่สามารถแก้ไขรูปแบบการซื้อได้)</div>";
}else{
    $hid_process_buy = 1;
?>

	<table cellpadding="5" cellspacing="0" border="0" width="100%">
		<tr>
			<td width="120"><b>รูปแบบการซื้อ :</b></td>
			<td>ซื้อผ่อน</td>
		</tr>
		<tr>
			<td>ราคารถ :</td>
			<td>
				<span id="span_edit_car_price"><?php echo $car_price; ?></span> 
				<span style="margin-left:10px; color:#c0c0c0; font-size:12px">[<?php echo $car_price; ?>]</span>
			</td>
		</tr>
		<tr>
			<td>ดาวน์ :</td>
			<td>
				<input style="width:80px; text-align:left" type="text" name="down_txt_moneydown" id="down_txt_moneydown" value="<?php echo $down_price; ?>" onkeyup="javascript:updatePrice()"> 
				<span style="margin-left:10px; color:#c0c0c0; font-size:12px">[<?php echo $down_price; ?>]</span>
			</td>
		</tr>
		<tr>
			<td>ยอดจัดเช่าซื้อ :</td>
			<td>
				<span id="span_finance_price"><?php echo $finance_price; ?></span> 
				<span style="margin-left:10px; color:#c0c0c0; font-size:12px">[<?php echo $finance_price; ?>]</span>
			</td>
		</tr>
		<tr>
			<td>จำนวนงวดผ่อน :</td>
			<td>
				<input style="width:40px; text-align:left" type="text" name="down_txt_num_install" id="down_txt_num_install" value="<?php echo $num_install; ?>"> 
				<span style="margin-left:10px; color:#c0c0c0; font-size:12px">[<?php echo $num_install; ?>]</span>
			</td>
		</tr>
		<tr>
			<td>ค่างวด :</td>
			<td>
				<input style="width:80px; text-align:left" type="text" name="down_txt_installment" id="down_txt_installment" value="<?php echo $installment; ?>"> 
				<span style="margin-left:10px; color:#c0c0c0; font-size:12px">[<?php echo $installment; ?>]</span>
			</td>
		</tr>
		<tr>
			<td>บริษัท Finance :</td>
			<td><?php echo $finance_cus_name; ?></td>
		</tr>
	</table>

<?php
}
?>

<div class="linedotted"></div>

<div style="margin-top:5px; text-align:right">
	<input type="button" name="btnDownSave" id="btnDownSave" value="บันทึก">
</div>

<script>
function edit_change_car_plan(){
    alert('การเปลี่ยนแปลงรูปแบบรถ จะมีผลต่อราคารถ !');

    var str_plan = $("#edit_resv_car_plan").val();
    var arr_plan = str_plan.split("#");
    
    $.get('reserv_car_new_api.php?cmd=changePlan&id='+arr_plan[0], function(data){
        $('#span_edit_car_price').text(data);
        updatePrice();
    });
}
    
function updatePrice(){
    var s = 0;
    var ton = parseFloat( $('#span_edit_car_price').text() );
    var down = parseFloat($('#down_txt_moneydown').val());

    if ( isNaN(ton) || ton == ""){
        ton = 0;
    }
    if ( isNaN(down) || down == ""){
        down = 0;
    }

    s = ton-down;
    $('#span_finance_price').text(s);
}


$('#btnDownSave').click(function(){
    $.post('reserv_car_edit_api.php',{
        cmd: 'savedown',
        id: '<?php echo $id?>',
        span_edit_car_price: $('#span_edit_car_price').text(),
        down_txt_moneydown: $('#down_txt_moneydown').val(),
        down_txt_num_install: $('#down_txt_num_install').val(),
        down_txt_installment: $('#down_txt_installment').val(),
        span_finance_price: $('#span_finance_price').text(),
        edit_resv_car_plan: $('#edit_resv_car_plan').val(),
        edit_radio_car_color: $('input[id=edit_radio_car_color]:checked').val(),
        hid_process_car: <?php echo $hid_process_car; ?>,
        hid_process_buy: <?php echo $hid_process_buy; ?>
    },
    function(data){
        if(data.success){
            alert(data.message);
            $("#div_dialog").load('reserv_car_edit_dialog.php?id=<?php echo $id?>');
            $('#divdialogedit').remove();
        }else{
            alert(data.message);
        }
    },'json');
});
</script>

<?php
}

elseif($cmd == "savedown"){
    $id = $_POST['id'];
    $span_edit_car_price = $_POST['span_edit_car_price'];
    $down_txt_moneydown = $_POST['down_txt_moneydown'];
    $down_txt_num_install = $_POST['down_txt_num_install'];
    $down_txt_installment = $_POST['down_txt_installment'];
    $span_finance_price = $_POST['span_finance_price'];
    $edit_resv_car_plan = $_POST['edit_resv_car_plan'];
    $edit_radio_car_color = $_POST['edit_radio_car_color'];

    $hid_process_car = $_POST['hid_process_car'];
    $hid_process_buy = $_POST['hid_process_buy'];

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    if($hid_process_car == 1){
        $arr_edit_resv_car_plan = explode("#",$edit_resv_car_plan);
        $remark = "CarType=$arr_edit_resv_car_plan[1]\nColor=$edit_radio_car_color";

        $qry = "UPDATE \"Reserves\" SET remark='$remark' WHERE res_id='$id' ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE Reserves 1 ไม่สำเร็จ $qry";
            $status++;
        }
    }
    
    if($hid_process_buy == 1){
        $qry = "UPDATE \"Reserves\" SET car_price='$span_edit_car_price',down_price='$down_txt_moneydown',finance_price='$span_finance_price',installment='$down_txt_installment',num_install='$down_txt_num_install' WHERE res_id='$id' ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE Reserves 2 ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);
}


elseif($cmd == "selectcars"){ //ไม่มีการเรียกใช้แล้ว
    $id = $_GET['id'];
    $pid = $_GET['pid'];
?>
<div style="margin-top:10px">
<b>เลือกรถ : </b>
<select name="combo_stock" id="combo_stock" onchange="javascript:checkColor()">
    <option value="">เลือก</option>
<?php
$VStockCars = pg_query("SELECT * FROM \"VStockCars\" WHERE \"IDNO\" IS NULL AND product_id='$pid' AND res_id IS NULL ORDER BY car_num ASC ");
while($res_VStockCars = pg_fetch_array($VStockCars)){
    $stock_car_id = $res_VStockCars['car_id'];
    $stock_car_num = $res_VStockCars['car_num'];
    $stock_license_plate = $res_VStockCars['license_plate'];
    $stock_color = $res_VStockCars['color'];
    $stock_product_id = $res_VStockCars['product_id'];
    $stock_product_name = GetProductName($stock_product_id);
?>
    <option value="<?php echo "$stock_car_id"; ?>"><?php echo "$stock_product_name $stock_car_num $stock_license_plate";?></option>
<?php
}
?>
</select>
</div>

<div class="linedotted"></div>

<div style="margin-top:5px; text-align:right">
<input type="button" name="btnSelectCarsSave" id="btnSelectCarsSave" value="บันทึก">
</div>

<script>
$('#btnSelectCarsSave').click(function(){
    $.post('reserv_car_edit_api.php',{
        cmd: 'saveselectcars',
        id: '<?php echo $id?>',
        combo_stock: $('#combo_stock').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
            $("#div_dialog").load('reserv_car_edit_dialog.php?id=<?php echo $id?>');
            $('#divdialogeditcar').remove();
        }else{
            alert(data.message);
        }
    },'json');
});


function checkColor(){
    if( $("#combo_stock").val() == ""){ return false; }
    $.get('reserv_car_edit_api.php?cmd=checkColor&cid='+$("#combo_stock").val()+'&id=<?php echo $id?>', function(data){
        if(data == "2"){
            alert('รถที่เลือกยังเป็นสีอื่น ที่ไม่ตรงกับการจอง\nกดปุ่มบันทึกเพื่อยืนยัน หรือเลือกรถคันอื่น');
        }
    });
}

</script>

<?php
}

elseif($cmd == "saveselectcars"){ //ไม่มีการเรียกใช้แล้ว
    $id = $_POST['id'];
    $combo_stock = $_POST['combo_stock'];

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    if(empty($combo_stock)){
        $txt_error[] = "กรุณาเลือกรถก่อนค่ะ";
        $status++;
    }
    
    $in_qry="UPDATE \"CarMove\" SET target_go='0',date_out='$nowdate' WHERE car_id='$combo_stock' AND date_out IS NULL AND target_go IS NULL ";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE CarMove ไม่สำเร็จ $in_qry";
        $status++;
    }

    $in_qry="UPDATE \"Reserves\" SET car_id='$combo_stock' ,reserve_color='$reserve_color' WHERE res_id='$id' ";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE Reserves ไม่สำเร็จ $in_qry";
        $status++;
    }
    
    $in_qry="UPDATE \"Cars\" SET res_id='$id' WHERE car_id='$combo_stock' ";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE Cars ไม่สำเร็จ $in_qry";
        $status++;
    }
    
    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
        
    echo json_encode($data);
}

elseif($cmd == "checkColor"){
   $cid = $_GET['cid'];
   $id = $_GET['id'];

    $VStockCars = pg_query("SELECT color FROM \"VStockCars\" WHERE \"IDNO\" IS NULL AND car_id='$cid' AND res_id IS NULL ");
    if($res_VStockCars = pg_fetch_array($VStockCars)){
        $stock_color = $res_VStockCars['color'];
    }

    $qry = pg_query("SELECT remark FROM \"Reserves\" WHERE res_id='$id' ");
    if($res = pg_fetch_array($qry)){
        $remark = $res['remark'];
        $arr_remark = explode("\n",$remark);
            $arr_remark1 = explode("=",$arr_remark[0]);
            $arr_remark2 = explode("=",$arr_remark[1]);
    }
    

    if($arr_remark2[1] != $stock_color){
        echo "2";
    }else{
        echo "1";
    }
}

//=============================================== ดึงข้อมูลขึ้นมาเพื่อแก้ไข ====================================================//
elseif($cmd == "edit_resv_all"){
    $res_id = $_GET['id'];
    
    $cmd2 = $_GET['cmd2'];
    
    $qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$res_id' ");
    if($res = pg_fetch_array($qry)){
        $cus_id = $res['cus_id'];
        $car_id = $res['car_id'];
        $car_price = $res['car_price'];
        $down_price = $res['down_price'];
        $finance_price = $res['finance_price'];
        $installment = $res['installment'];
        $num_install = $res['num_install'];
        $finance_cus_id = $res['finance_cus_id'];
        $remark = $res['remark'];
        $product_id = $res['product_id'];
        $IDNO = $res['IDNO'];
        $cus_year = $res['cus_year'];
        $type_insure = $res['type_insure'];
        $use_radio = $res['use_radio'];
        $user_id = $res['user_id'];
		$reserve_color = $res['reserve_color'];
		$remark = $res['remark'];
    }
    
    $qry_name=pg_query("select * from \"Customers\" WHERE \"cus_id\" = '$cus_id' ");
    if($res_name=pg_fetch_array($qry_name)){
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        $full_name = $cus_name." ".$surname;
    }
?>


<?php //=============================================== ส่วนของการแก้ไขการจอง ================================================================?>
<div>
<b>ผู้จอง</b> ค้นจากฐานข้อมูลที่มี <input type="text" name="edit_txt_name" id="edit_txt_name" size="50" onkeyup="javascript:edit_CheckNaN()" value="<?php echo "$cus_id#$full_name"; ?>">
</div>



<div class="linedotted"></div>

<?php //=========================================================== แสดงหน้าจอเปลี่ยนแปลงการจอง   ซื้อผ่อน =================================================== ?>
<div style="margin-top:10px; float:left; width:400px; border-right:1px dotted #E0E0E0">
	<b>รูปแบบการจอง</b><br/>
		<input type="radio" name="edit_radio_resv_type" id="edit_radio_resv_type" value="1" <?php if( empty($car_id) ){ echo "checked"; } ?>> ไม่เจาะจงรถ 
		<input type="radio" name="edit_radio_resv_type" id="edit_radio_resv_type" value="2" <?php if( !empty($car_id) ){ echo "checked"; } ?>> เจาะจงรถ
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>สีรถแท๊กซี่:</label>
		<select name="ddl_car_color_change" id="ddl_car_color_change" >
			<option value="not change">เลือก</option>
			<option value="ฟ้า" <?php if($reserve_color == "ฟ้า" ){echo "selected";} ?>>ฟ้า</option>
			<option value="เหลือง"<?php if($reserve_color == "เหลือง" ){echo "selected";} ?>>เหลือง</option>		
			<option value="เขียวเหลือง"<?php if($reserve_color == "เขียวเหลือง" ){echo "selected";} ?>>เขียวเหลือง</option>
			<option value="สีเดิม" >สีเดิม</option>
		</select>
		<div id="div_car_change_type" style="margin-top:10px; margin-left:20px; display:none">
			<input type="radio" name="radio_car_change_type" id="radio_car_change_type" value="1"> รถใหม่
			<input type="radio" name="radio_car_change_type" id="radio_car_change_type" value="2"> รถใช้แล้ว
		</div>

	<div id="div_show_resv_car_type_edit"></div>
	
	<?php //===================================================== รถใหม่ ไม่เจาะจงรถ ==========================================================?>
	<div id="edit_divresv1" style="margin-top:10px; margin-left:25px; <?php if( !empty($car_id) ){ echo "display:none"; } ?>">
		<label>รูปแบบรถ :</label> 
		<select name="edit_resv_car_plan" id="edit_resv_car_plan" onchange="javascript:edit_changePlan()">
				<option value="">เลือก</option>
				<?php
				$qry = pg_query("SELECT * FROM \"Products\" WHERE \"link_table\"='Cars' ORDER BY \"name\" ASC ");
				while($res = pg_fetch_array($qry)){
					$db_product_id = $res['product_id'];
					$name = $res['name'];
					if($product_id == $db_product_id)
						echo "<option value=\"$product_id#$name\" selected>$name</option>";
					else
						echo "<option value=\"$product_id#$name\">$name</option>";
				}
				?>
		</select>
		<br>
	</div>

	<?php//====================================== เจาะจงรถยนต์ =======================================//?>
	<div id="edit_divresv2" style="margin-top:10px; margin-left:25px; <?php if( empty($car_id) ){ echo "display:none"; } ?>">
	
		<?php
		// หาว่าเป็นรถใหม่หรือรถใช้แล้ว
		if($car_id != "")
		{
			$qry_type = pg_query("select \"car_type_id\" FROM v_cars WHERE \"car_id\" = '$car_id' ");
			$car_type_id = pg_result($qry_type,0);
			
			if($car_type_id == "1")
			{
				$car_type_id_name = "รถใหม่ : ";
				$car_type_id_where = "\"car_type_id\" = '1' and";
			}
			elseif($car_type_id == "2" || $car_type_id == "3" || $car_type_id == "4")
			{
				$car_type_id_name = "รถใช้แล้ว : ";
				$car_type_id_where = "\"car_type_id\" in('2','3','4') and";
			}
		}
		?>
		
		<!--<label>เลือกรถ : <label> onchange="javascript:display_reserve()"-->
		<br> <!-- onchange="javascript:display_reserve()" -->
		<?php echo $car_type_id_name; ?>
		<select name="edit_combo_car_stock" id="edit_combo_car_stock" onchange="javascript:display_reserve()" >
		<option value="">กรุณาเลือกรถ</option>
			<?php
			$qry_cars = pg_query("SELECT * FROM v_cars WHERE $car_type_id_where (car_status = 'A' or car_status = 'R') ORDER BY car_name ASC ");
			while($res = pg_fetch_array($qry_cars)){
				$db_car_id = $res['car_id'];
				$car_name = $res['car_name'];
				$car_num = $res['car_num'];
				$mar_num = $res['mar_num'];
				$car_year = $res['car_year'];
				$color = $res['color'];
				$color_name = $res['color_name'];
				$license_plate = $res['license_plate'];
				$car_type_name = $res['car_type_name'];
				$car_status = $res['car_status'];
				$car_idno = $res['car_idno'];
				
				$text_show_1 = "$car_name/$car_num/$mar_num/$car_year/$color_name/$car_idno/$car_status";
				$text_show_2 = "$car_idno#$color#$color_name#$db_car_id#$car_status";
			   
			?>
				<option value="<?php echo "$car_idno#$car_name#$color#$color_name#$db_car_id#$car_status"; ?>" <?php if($car_id == $db_car_id ){echo "selected";} ?>   ><?php echo "$text_show_1";?></option>
			<?php
			}
			?>
	</select>
	</div>
	<?php //----------------------------------------------------------------------------------- สิ้นสุดการเช็ครถยนต์ --------------------------------------------------------------------------------------//?>
</div>

<div style="margin-top:10px; float:right; width:300px">
	<b>อุปกรณ์ส่วนควบ</b><br/>
	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="150">ประกันประเภท</td>
		<td>
	<select name="edit_cb_insure" id="edit_cb_insure">
		<option value="not">กรุณาเลือก</option>
		<option value="1" <?php if($type_insure == 1) echo "selected"; ?>>1</option>
		<option value="2" <?php if($type_insure == 2) echo "selected"; ?>>2</option>
		<option value="3" <?php if($type_insure == 3) echo "selected"; ?>>3</option>
	</select>
		</td>
	</tr>
	<tr>
		<td>ติดตั้งเครื่องวิทยุสื่อสาร</td>
		<td>
			<input type="radio" name="edit_radio_commu" id="edit_radio_commu" value="1" <?php if($use_radio == "t") echo "checked"; ?>> ติดตั้ง 
			<input type="radio" name="edit_radio_commu" id="edit_radio_commu" value="2" <?php if($use_radio == "f") echo "checked"; ?>> ไม่ติดตั้ง
		</td>
	</tr>
	</table>
</div>

<div style="clear:both"></div>

<div class="linedotted"></div>

<div style="margin-top:10px">
<b>รูปแบบการซื้อ</b><br />
<input type="radio" name="edit_radio_buy_type" id="edit_radio_buy_type" value="1" <?php if($num_install != 0){ echo "checked"; } ?>> ซื้อผ่อน 
<input type="radio" name="edit_radio_buy_type" id="edit_radio_buy_type" value="2" <?php if($num_install == 0){ echo "checked"; } ?>> ซื้อสด

<div id="edit_divbuy2" style="margin-top:10px; margin-left:25px; <?php if($num_install != 0){ echo "display:none"; } ?>">
<table>
	<tr>
		<td width="15px" ></td>
		<td width="80px" align="left">ราคารถ</td>
		<td>
			<input type="text" name="edit_txt_buy_price1" id="edit_txt_buy_price1" size="10" onblur="javascript:edit_CheckPlan()" onkeyup="javascript:edit_changeBtn()" value="<?php echo $car_price; ?>"><br />
		</td>
	</tr>

	</table>
 
</div>

<div id="edit_divbuy1" style="margin-top:10px; margin-left:25px; <?php if($num_install == 0){ echo "display:none"; } ?>">
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ราคารถ</td>
	<td><input type="text" name="edit_txt_buy_price2" id="edit_txt_buy_price2" size="10" onblur="javascript:edit_CheckPlan()" value="<?php echo $car_price; ?>" onkeyup="javascript:change_interest_rate()"></td>
</tr>
<tr>
    <td>ดาวน์</td><td><input type="text" name="edit_txt_buy_down" id="edit_txt_buy_down" size="10" onkeyup="javascript:edit_changeBtn(); javascript:change_interest_rate()" <?php if($num_install != 0){ echo "value=\"$down_price\" "; } ?>></td>
</tr>
<tr>
    <td>จำนวนงวดผ่อน</td><td><input type="text" name="edit_txt_buy_numdue" id="edit_txt_buy_numdue"size="5" <?php if($num_install != 0){ echo "value=\"$num_install\" "; } ?>  onkeyup="javascript:change_interest_rate()"></td>
</tr>
<tr>
    <td>ค่างวด</td><td><input type="text" name="edit_txt_buy_monnydue" id="edit_txt_buy_monnydue" size="10" <?php if($num_install != 0){ echo "value=\"$installment\" "; } ?>  onkeyup="javascript:change_interest_rate()"> ดอกเบี้ย = <span id="span_interest_rate_edit"><?php echo interest_rate($car_price-$down_price,$installment,$num_install); ?></span></td>
</tr>
<tr>
    <td>บริษัท Finance</td>
    <td>
		<select name="edit_cb_finance" id="edit_cb_finance">
		<?php
		$qry = pg_query("SELECT * FROM \"Finances\" ORDER BY \"finance_id\" ASC ");
		while($res = pg_fetch_array($qry)){
			$finance_id = $res['finance_id'];
			$cus_id = $res['cus_id'];

			$cus_name = GetCusName($cus_id);
			if($finance_cus_id == $cus_id)
				echo "<option value=\"$cus_id\" selected>$cus_name</option>";
			else
				echo "<option value=\"$cus_id\">$cus_name</option>";
		}
		?>
		</select>
    </td>
</tr>
</table>

</div>
	<table>
	<tr>
		<td width="15px" ></td>
		<td width="105px" align="left">หมายเหตุ</td>
		<td>
			<textarea name="edit_area_remark" id="edit_area_remark" rows="3" cols="50"><?php echo $remark; ?></textarea>
		</td>
	</tr>

	</table>
</div>

<div class="linedotted"></div>

<?php //================================================= บันทึกการแก้ไข ===================================================================//?>
<div style="margin-top:10px; text-align:right">
<input type="button" name="edit_btnSaveNormal" id="edit_btnSaveNormal" value="บันทึก" onclick="javascript:edit_SaveNewCar()">
</div>
<?php //-------------------------------------------------------------------------------------------------------------//?>

<script type="text/javascript">
function change_interest_rate(){
    $.get('reserv_car_new_api.php?cmd=interest_rate&car_price='+$("#edit_txt_buy_price2").val()+'&down_price='+$("#edit_txt_buy_down").val()+'&installment='+$("#edit_txt_buy_monnydue").val()+'&num_installment='+$("#edit_txt_buy_numdue").val(), function(data){
        $('#span_interest_rate_edit').text( data );
    });
}

function edit_changeBtn(){
    if( $('input[id=edit_radio_buy_type]:checked').val() == "2" ){
        if( $('#edit_txt_resv_money').val() ==  $('#edit_txt_buy_price1').val() && $('#edit_txt_resv_money').val() != "" && $('#edit_txt_buy_price1').val() != "" ){
            $('#edit_span_btn_out').show('fast');
            $('#edit_span_btn_normal').hide('fast');
            $('#edit_hid_constant_var').val('CARCA');
        }else{
            $('#edit_span_btn_out').hide('fast');
            $('#edit_span_btn_normal').show('fast');
            $('#edit_hid_constant_var').val('CARRE');
        }
    }else{
        if( $('#edit_txt_resv_money').val() ==  $('#edit_txt_buy_down').val() && $('#edit_txt_resv_money').val() != "" && $('#edit_txt_buy_down').val() != "" ){
            $('#edit_span_btn_out').show('fast');
            $('#edit_span_btn_normal').hide('fast');
            $('#edit_hid_constant_var').val('CARDW');
        }else{
            $('#edit_span_btn_out').hide('fast');
            $('#edit_span_btn_normal').show('fast');
            $('#edit_hid_constant_var').val('CARRE');
        }
    }
}

//=============== แสดงรายการรถที่ถูกจองไปแล้ว ============================//
function display_reserve(){
	//$('#edit_txt_buy_price2').val('');
        var edit_combo_car_stock = $("#edit_combo_car_stock").val();
        var arr_car_stock = edit_combo_car_stock.split("#");

		if(arr_car_stock[5] == "R"){
			show_car_reserve(arr_car_stock[4]);
		}
}

//========= เปิดหน้าจอสำหรับแสดงรายการรถที่ถูกจองแล้ว =========//
function show_car_reserve(car_id){
    $('body').append('<div id="dialog-form"></div>');
    /*$('#dialog-form').load('../report/report_reserve.php');*/
	$('#dialog-form').load('list_car_reserve.php?car_id='+car_id);
		$('#dialog-form').dialog({ 
			title: 'แสดงรายการรถที่ถูกจอง   '+car_id,
			resizable: false,
			modal: true,  
			width: 850,
			height:600,
		close: function(ev, ui){
				$('#dialog-form').remove();
                }
        });
}

$(document).ready(function(){
    
    $("#edit_txt_barthdate, #edit_txt_carddate, #edit_txt_date_car, #edit_txt_cheque_date").datepicker({
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        showOn: 'both'
    });
    
	//=================================== ค้นหาชื่อผู้จอง  ในกรณีที่ต้องการเปลี่ยนแปลงผู้จอง=================================//
    $("#edit_txt_name").autocomplete({
        source: "reserv_car_new_api.php?cmd=autocomplete",
        minLength:1,
        select: function(event, ui) {
            if(ui.item.value == 'ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่'){
			
			//================ กรณีเปิดเป็น New Window ====================//
			//window.open('../user/regular_customer.php','dfsd342ss7fs789','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=950,height=600');
			
			//================ กรณีต้องการเปิดแบบ Model Form ==============//
			show_customer();
            }else{}
        }
    });
	
//---------------------------------------------------------------------//	
    
    $("input[name='edit_chkContact']").change(function(){
        if( $('input[id=edit_chkContact]:checked').val() == "2" ){
            $('#edit_divcontact').show('fast');
        }else{
            $('#edit_divcontact').hide('fast');
        }
    });
    
    $("#edit_combo_cardtype").change(function(){
        if( $("#edit_combo_cardtype").val() == "อื่นๆ" ){
            $('#edit_span_card').show('fast');
        }else{
            $('#edit_span_card').hide('fast');
        }
    });
    
    $("input[name='edit_radio_resv_type']").change(function(){
        if( $('input[id=edit_radio_resv_type]:checked').val() == "2" ){
            $('#edit_divresv1').hide('fast');
            //$('#edit_divresv2').show('fast');
            $('#edit_resv_car_plan').val('');
            $('#edit_txt_buy_price1').val('');
            $('#edit_txt_buy_price2').val('');
            $('#div_car_change_type').show('fast');
            $('#div_show_resv_car_type_edit').show('fast');
        }else{
            $('#edit_divresv1').show('fast');
            $('#edit_divresv2').empty();
            $('#div_car_change_type').hide('fast');
            $('#div_show_resv_car_type_edit').empty();
        }
        edit_changeBtn();
        edit_Summary();
    });
    
	//เลือกว่าเป็นรถใหม่หรือรถใช้แล้ว
    $("input[name='radio_car_change_type']").change(function(){
        if( $('input[id=radio_car_change_type]:checked').val() == "1" ){
            $('#div_show_resv_car_type_edit').empty();
            $('#div_show_resv_car_type_edit').load('reserv_car_edit_api.php?cmd=Change_Resv_Car_type_edit&t=1');
        }else{
            $('#div_show_resv_car_type_edit').empty();
            $('#div_show_resv_car_type_edit').load('reserv_car_edit_api.php?cmd=Change_Resv_Car_type_edit&t=2');
        }
    });
    
    
    $("input[name='edit_radio_car_color']").change(function(){
        if( $('input[id=edit_radio_car_color]:checked').val() == "ฟ้า" ){
            $('#edit_cb_finance').val('CUS00001');
        }else if( $('input[id=edit_radio_car_color]:checked').val() == "เหลือง" ){
            $('#edit_cb_finance').val('CUS00002');
        }else{
            
        }
    });
    
    $("input[name='edit_radio_buy_type']").change(function(){
        if( $('input[id=edit_radio_buy_type]:checked').val() == "2" ){
            //$('#txt_buy_price1').val('');
            $('#edit_divbuy1').hide('fast');
            $('#edit_divbuy2').show('fast');
        }else{
            //$('#txt_buy_price2').val('');
            //$('#edit_txt_buy_down').val('');
            //$('#edit_txt_buy_numdue').val('');
            //$('#edit_txt_buy_monnydue').val('');
            $('#edit_divbuy1').show('fast');
            $('#edit_divbuy2').hide('fast');
        }
        edit_changeBtn();
        edit_Summary();
    });
    
    $("input[name='edit_chk_buy_cash']").change(function(){
        
        if( $('input[id=edit_chk_buy_cheque]:checked').val() ){
            var m1 = parseFloat( $('#edit_txt_resv_money').val() );
            var m2 = parseFloat( $('#edit_txt_cheque_monny').val() );
            $('#edit_txt_cash').val( m1-m2 );
        }else{
            $('#edit_txt_cash').val( $('#edit_txt_resv_money').val() );
        }
        
        if( $('input[id=edit_chk_buy_cash]:checked').val() ){
            $('#edit_divcash').show('fast');
        }else{
            $('#edit_txt_cash').val('');
            $('#edit_divcash').hide('fast');
        }
    });
    
    $("input[name='edit_chk_buy_cheque']").change(function(){
        
        if( $('input[id=edit_chk_buy_cash]:checked').val() ){
            var m1 = parseFloat( $('#edit_txt_resv_money').val() );
            var m2 = parseFloat( $('#edit_txt_cash').val() );
            $('#edit_txt_cheque_monny').val( m1-m2 );
        }else{
            $('#edit_txt_cheque_monny').val( $('#edit_txt_resv_money').val() );
        }
        
        if( $('input[id=edit_chk_buy_cheque]:checked').val() ){
            $('#edit_divcheque').show('fast');
        }else{
            $('#edit_txt_cheque_monny').val('');
            $('#edit_divcheque').hide('fast');
        }
    });

});

//======================== แสดงข้อมูลลูกค้า ========================//
function show_customer(){
 $('body').append('<div id="div_customer"></div>');
    /*$('#dialog-form').load('../report/report_reserve.php');*/
	$('#div_customer').load('../customer/customer_api.php?tab=2');
		$('#div_customer').dialog({ 
			title: 'เพิ่มข้อมูลใหม่  ',
			resizable: false,
			modal: true,  
			width: 850,
			height:650,
		close: function(ev, ui){
				$('#div_customer').remove();
                }
        });
}

function edit_Summary(){
    var s1 = 0;
    var a1 = parseFloat($('#edit_txt_resv_money').val());
    var a2 = parseFloat($('#edit_txt_buy_price1').val());
    var a3 = parseFloat($('#edit_txt_buy_price2').val());

    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    if ( isNaN(a3) || a3 == ""){
        a3 = 0;
    }
    
    if( $('input[id=edit_radio_buy_type]:checked').val() == "2" ){
        s1 = a2-a1;
    }else{
        s1 = a3-a1;
    }

    $('#edit_span_sum').text(s1);
}

function edit_chkSum(){
    var s1 = 0;
    var a1 = parseFloat($('#edit_txt_resv_money').val());
    var a2 = parseFloat($('#edit_txt_cash').val());
    var a3 = parseFloat($('#edit_txt_cheque_monny').val());

    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    if ( isNaN(a3) || a3 == ""){
        a3 = 0;
    }
    
    if( $('input[id=edit_chk_buy_cash]:checked').val() ){
        s1+=a2;
    }
    if( $('input[id=edit_chk_buy_cheque]:checked').val() ){
        s1+=a3;
    }
    
    if(a1 != s1){
        return false;
    }else{
        return true;
    }
}

//============================ ค้นหาข้อมูลผู้จอง ====================================//
function edit_CheckNaN(){
    if( $('#edit_txt_name').val() == '' ){
        /*$('#edit_divnewcus').hide('fast');*/
		
    }
}
//-----------------------------------------------------------------//

function edit_changePlan(){
    if( $('#edit_resv_car_plan').val() == '' ){
        $('#edit_txt_buy_price1').val('');
        $('#edit_txt_buy_price2').val('');
        edit_Summary();
        return false;
    }
    
    var str_plan = $("#edit_resv_car_plan").val();
    var arr_plan = str_plan.split("#");
    
    $.get('reserv_car_new_api.php?cmd=changePlan&id='+arr_plan[0], function(data){
        $('#edit_txt_buy_price1').val(data);
        $('#edit_txt_buy_price2').val(data);
        edit_Summary();
    });
}

function edit_CheckPlan(){
    if( $('input[id=edit_radio_resv_type]:checked').val() == "1" ){

    var str_plan = $("#edit_resv_car_plan").val();
    
    if( str_plan == "" ){
        alert('กรุณาเลือกรูปแบบรถ !');
        $('#edit_txt_buy_price1').val('');
        $('#edit_txt_buy_price2').val('');
        return false;
    }
    
    var arr_plan = str_plan.split("#");

    $.get('reserv_car_new_api.php?cmd=CheckPlan&id='+arr_plan[0], function(data){
        if( $('input[id=edit_radio_buy_type]:checked').val() == "2" ){
            if( parseFloat( data ) > parseFloat( $('#edit_txt_buy_price1').val() ) ){
                alert('ราคารถ ต่ำกว่าต้นทุน ที่จะขายได้ กรุณาใส่ใหม่');
                edit_changePlan();
                $('#edit_txt_buy_price1').focus();
            }
        }else{
            if( parseFloat( data ) > parseFloat( $('#edit_txt_buy_price2').val() ) ){
                alert('ราคารถ ต่ำกว่าต้นทุน ที่จะขายได้ กรุณาใส่ใหม่');
                edit_changePlan();
                $('#edit_txt_buy_price2').focus();
            }
        }
    });
    
    }else{
        var combo_car_stock = $("#edit_combo_car_stock").val();
        if( combo_car_stock == "" ){
            alert('กรุณาเลือกรถ !');
            $('#edit_txt_buy_price1').val('');
            $('#edit_txt_buy_price2').val('');
            return false;
        }
        
        var arr_car_stock = combo_car_stock.split("#");
        
        $.get('reserv_car_new_api.php?cmd=CheckCarsCostPrice&id='+arr_car_stock[1], function(data){
            if( $('input[id=edit_radio_buy_type]:checked').val() == "2" ){
                if( parseFloat( data ) > parseFloat( $('#edit_txt_buy_price1').val() ) ){
                    alert('ราคารถ ต่ำกว่าต้นทุน ที่จะขายได้ กรุณาใส่ใหม่');
                    edit_changePlan();
                    $('#edit_text_buy_price1').focus();
                }
            }else{
                if( parseFloat( data ) > parseFloat( $('#edit_txt_buy_price2').val() ) ){
                    alert('ราคารถ ต่ำกว่าต้นทุน ที่จะขายได้ กรุณาใส่ใหม่');
                    edit_changePlan();
                    $('#edit_txt_buy_price2').focus();
                }
            }
        });

    }
    edit_Summary();
}

//================================================ ส่งข้อมูลไปบันทึกการแก้ไข ====================================================//
function edit_SaveNewCar(){

        if( $('#edit_txt_name').val() == "" ){
            alert('กรุณาระบุ ผู้จอง !');
            return false;
        }
        
        if( $('input[id=edit_radio_resv_type]:checked').val() == "2" ){
            if( $('#edit_combo_car_stock').val() == "" ){
                alert('กรุณาเลือกรถ !');
                return false;
            }
        }else if( $('input[id=edit_radio_resv_type]:checked').val() == "1" ){
            if( $('#edit_resv_car_plan').val() == "" ){
                alert('กรุณาเลือกรูปแบบรถ !');
                return false;
            }
        }
        
        if( $('input[id=edit_radio_buy_type]:checked').val() == "2" ){
            if( $('#edit_txt_buy_price1').val() == "" ){
                alert('กรุณากรอก ราคารถ !');
                return false;
            }
        }else if( $('input[id=edit_radio_buy_type]:checked').val() == "1" ){
            if( $('#edit_txt_buy_price2').val() == "" ){
                alert('กรุณากรอก ราคารถ !');
                return false;
            }
        }
		
		if( $('#edit_cb_insure').val() == "not" ){
                alert('กรุณาเลือกประเภทประกันภัย!');
                return false;
        }
		
		if (!$('input[name=edit_radio_commu]:checked').val() ){ 
			alert('กรุณาเลือกการติดตั้งเครื่องวิทยุสื่อสาร!');
            return false;
		}
        
	
        $.post('reserv_car_edit_api.php',{
            cmd: 'save_edit_resv',
            res_id:'<?php echo $res_id; ?>',    //'<?php echo $id; ?>',
            txt_name: $('#edit_txt_name').val(),
            txt_pre_name: $('#edit_txt_pre_name').val(),
            txt_firstname: $('#edit_txt_firstname').val(),
            txt_lastname: $('#edit_txt_lastname').val(),
            txt_address: $('#edit_txt_address').val(),
            txt_post: $('#edit_txt_post').val(),
            chkContact: $('input[id=edit_chkContact]:checked').val(),
            txt_contact: $('#edit_txt_contact').val(),
            txt_phone: $('#edit_txt_phone').val(),
            txt_reg: $('#edit_txt_reg').val(),
            txt_barthdate: $('#edit_txt_barthdate').val(),
            combo_cardtype: $("#edit_combo_cardtype").val(),
            txt_cardother: $('#edit_txt_cardother').val(),
            txt_cardno: $('#edit_txt_cardno').val(),
            txt_carddate: $('#edit_txt_carddate').val(),
            txt_cardby: $('#edit_txt_cardby').val(),
            txt_job: $('#edit_txt_job').val(),
            radio_resv_type: $('input[id=edit_radio_resv_type]:checked').val(),
            resv_car_plan: $("#edit_resv_car_plan").val(),
            radio_car_color: $('input[id=edit_radio_car_color]:checked').val(),
            edit_combo_car_stock: $('#edit_combo_car_stock').val(), //เลือกรถ
            radio_buy_type: $('input[id=edit_radio_buy_type]:checked').val(),
            txt_buy_price1: $('#edit_txt_buy_price1').val(),
            txt_buy_price2: $('#edit_txt_buy_price2').val(),
            txt_buy_down: $('#edit_txt_buy_down').val(),
            txt_buy_numdue: $('#edit_txt_buy_numdue').val(),
            txt_buy_monnydue: $('#edit_txt_buy_monnydue').val(),
            cb_finance: $('#edit_cb_finance').val(),
            cb_insure: $('#edit_cb_insure').val(),
            radio_commu: $('input[id=edit_radio_commu]:checked').val(),
			remark: $('#edit_area_remark').val() ,
			select_car_color: $('#ddl_car_color_change').val()
        },
        function(data){
            if(data.success){
                alert(data.message);
                $('#divdialogedit').remove();
                // location.reload();
				<?php if($cmd2 == "t"){ ?>
                            location.reload();
                <?php }else{ ?>
               // ShowDialog('<?php echo $id; ?>');
                <?php } ?>
            }else{
                alert(data.message);
            }
        },'json');

}
</script>

<?php
//================================================== บันทึกการแก้ไข ================================================//
}elseif($cmd == "save_edit_resv"){ 

$res_id = $_POST['res_id'];

   
$qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$res_id' ");
if($res = pg_fetch_array($qry)){
    $old_cus_id = $res['cus_id'];
    $old_car_id = $res['car_id'];
    $old_car_price = $res['car_price'];
    $old_down_price = $res['down_price'];
    $old_finance_price = $res['finance_price'];
    $old_installment = $res['installment'];
    $old_num_install = $res['num_install'];
    $old_finance_cus_id = $res['finance_cus_id'];
    $old_remark = $res['remark'];
    $old_product_id = $res['product_id'];
    $old_IDNO = $res['IDNO'];
    $old_cus_year = $res['cus_year'];
    $old_type_insure = $res['type_insure'];
    $old_use_radio = $res['use_radio'];
    $old_user_id = $res['user_id'];
}
	$txt_name=$_POST['txt_name'];
	$txt_pre_name=$_POST['txt_pre_name'];
	$txt_firstname=$_POST['txt_firstname'];
	$txt_lastname=$_POST['txt_lastname'];
	$txt_address=$_POST['txt_address'];
	$txt_post=$_POST['txt_post'];
	$chkContact=$_POST['chkContact'];
	$txt_contact=$_POST['txt_contact'];
	$txt_phone=$_POST['txt_phone'];

	$txt_reg=$_POST['txt_reg'];
	$txt_barthdate=$_POST['txt_barthdate'];
	$combo_cardtype=$_POST['combo_cardtype'];
	$txt_cardother=$_POST['txt_cardother'];
	$txt_cardno=$_POST['txt_cardno'];
	$txt_carddate=$_POST['txt_carddate'];
	$txt_cardby=$_POST['txt_cardby'];
	$txt_job=$_POST['txt_job'];

	$radio_resv_type=$_POST['radio_resv_type'];
	$resv_car_plan=$_POST['resv_car_plan'];
	$arr_resv_car_plan = explode("#",$resv_car_plan);
	$radio_car_color=$_POST['radio_car_color'];
	
	$edit_combo_car_stock=$_POST['edit_combo_car_stock']; // ดึงรถที่จอง
	$arr_combo_car_stock = explode("#",$edit_combo_car_stock);
	$radio_buy_type=$_POST['radio_buy_type'];
	$txt_buy_price1=$_POST['txt_buy_price1'];
	$txt_buy_price2=$_POST['txt_buy_price2'];
	$txt_buy_down=$_POST['txt_buy_down'];
	$txt_buy_numdue=$_POST['txt_buy_numdue'];
	$txt_buy_monnydue=$_POST['txt_buy_monnydue'];
	$cb_finance=$_POST['cb_finance'];
	$remark = $_POST['remark'];
	$select_car_color = $_POST['select_car_color'];

	$cb_insure=$_POST['cb_insure'];
	$radio_commu=$_POST['radio_commu'];
	

	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();

	if($radio_resv_type == 1){//ตรวจสอบการเปลี่ยนเจาะจงรถ > ไม่เจาะจงรถ
		if(!empty($old_car_id)){
			$in_qry="UPDATE \"CarMove\" SET target_go=DEFAULT,date_out=DEFAULT WHERE car_id='$old_car_id' AND target_go='0' ";
			if(!$res=@pg_query($in_qry)){
				$txt_error[] = "UPDATE CarMove ไม่สำเร็จ $in_qry";
				$status++;
			}

			$in_qry="UPDATE \"Cars\" SET res_id=DEFAULT WHERE car_id='$old_car_id' ";
			if(!$res=@pg_query($in_qry)){
				$txt_error[] = "UPDATE Cars ไม่สำเร็จ $in_qry";
				$status++;
			}
			
			$in_qry="UPDATE \"Reserves\" SET car_id=DEFAULT WHERE res_id='$res_id' ";
			if(!$res=@pg_query($in_qry)){
				$txt_error[] = "UPDATE Reserves ไม่สำเร็จ $in_qry";
				$status++;
			}
		}
	}

	if($txt_name == "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){

		$cus_id = GetCusID();

		if($chkContact == 1){ $str_contact = $txt_address; }else{ $str_contact = $txt_contact; }
		if($combo_cardtype != "อื่นๆ"){ $str_cardtype = $combo_cardtype; }else{ $str_cardtype = $txt_cardother; }
		
		$in_qry="INSERT INTO \"Customers\" (\"cus_id\",\"pre_name\",\"cus_name\",\"surname\",\"address\",\"add_post\",\"nationality\",\"birth_date\",\"card_type\",\"card_id\",\"card_do_date\",\"card_do_by\",\"job\",\"contract_add\",\"telephone\") values 
		('$cus_id','$txt_pre_name','$txt_firstname','$txt_lastname','$txt_address','$txt_post','$txt_reg','$txt_barthdate','$str_cardtype','$txt_cardno','$txt_carddate','$txt_cardby','$txt_job','$str_contact','$txt_phone')";
		if(!$res=@pg_query($in_qry)){
			$txt_error[] = "บันทึก Customers ไม่สำเร็จ $in_qry";
			$status++;
		}else{
			$txt_update_cus = "\nเปลี่ยนแปลงผู้จองเรียบร้อยแล้ว > $cus_id";
		}
	}else{
		$arr_txt_name = explode("#",$txt_name);
		$cus_id = $arr_txt_name[0];
	}

	if($old_cus_id != $cus_id){ //กรณีอัพเดทลูกค้า ให้อัพเดท Invoices และ ChqDetail ด้วย
		$qry = pg_query("SELECT * FROM \"Invoices\" WHERE res_id='$res_id' ORDER BY inv_no ASC ");
		while($res = pg_fetch_array($qry)){
			$inv_no = $res['inv_no'];
			
			$up_inv_qry="UPDATE \"Invoices\" SET cus_id='$cus_id' WHERE inv_no='$inv_no' ";
			if(!$res_inv_qry=@pg_query($up_inv_qry)){
				$txt_error[] = "UPDATE Invoices ไม่สำเร็จ $up_inv_qry";
				$status++;
			}
			
			$up_chq_dt = pg_query("SELECT COUNT(auto_id) AS c_auto_id FROM \"ChequeDetails\" WHERE inv_no='$inv_no' ");
			if($res_chq_dt = pg_fetch_array($up_chq_dt)){
				$c_auto_id = $res['c_auto_id'];
				if($c_auto_id > 0){
					$up_inv_qry="UPDATE \"ChequeDetails\" SET cus_id='$cus_id' WHERE inv_no='$inv_no' ";
					if(!$res_inv_qry=@pg_query($up_inv_qry)){
						$txt_error[] = "UPDATE ChequeDetails ไม่สำเร็จ $up_inv_qry";
						$status++;
					}
				}
			}  
		}
	}

	if($radio_buy_type == 1){
		$str_car_price = $txt_buy_price2;
		$str_down_price = $txt_buy_down;
		$str_installment = $txt_buy_monnydue;
		$str_num_install = $txt_buy_numdue;
		$str_finance_price = $txt_buy_price2-$txt_buy_down;
	}else{
		$str_car_price = $txt_buy_price1;
		$str_down_price = $txt_buy_price1; //edit down price = car price
		$str_installment = 0;
		$str_num_install = 0;
		$str_finance_price = 0;
		if($radio_car_color == "เขียวเหลือง"){
			$cb_finance = "";
		}
	}

	if($radio_commu == 1){
		$str_radio_commu = "TRUE";
	}else{
		$str_radio_commu = "FALSE";
	}

//============================ ตรวจสอบรูปแบบการจอง ว่าเป็นชนิดไหน เจาะจงรถ หรือ ไม่เจาะจงรถ ===================================//

	if($radio_resv_type == 1){ //ไม่เจาะจงรถ
		
		$in_qry="UPDATE \"Reserves\" SET cus_id='$cus_id',car_price='$str_car_price',down_price='$str_down_price',finance_price='$str_finance_price',
										installment='$str_installment',num_install='$str_num_install',finance_cus_id='$cb_finance',product_id='$arr_resv_car_plan[0]',
										type_insure='$cb_insure',use_radio='$str_radio_commu',user_id='$_SESSION[ss_iduser]',remark='$remark' 
									WHERE res_id='$res_id' ";
		if(!$res=@pg_query($in_qry)){
			$txt_error[] = "UPDATE Reserves ไม่สำเร็จ $in_qry";
			$status++;
		}
	}else{ //เจาะจงรถ 
		$qry_product = pg_query("SELECT product_id from v_product WHERE car_id = '$arr_combo_car_stock[4]' ");
		if( $res_product= pg_fetch_array($qry_product) ){
			$product_id = $res_product['product_id'];
		}
		
		//=========================== ปรับปรุงสถานะการจอง ==============================================================//
		
		$qry_reserve = pg_query(" SELECT car_id,product_id,reserve_status FROM \"Reserves\" WHERE res_id = '$res_id' ");
		if( $res_reserve = pg_fetch_array($qry_reserve) ){
			$select_old_car_id = $res_reserve['car_id'];
			$product_id = $res_reserve['product_id'];
			$reserve_status = $res_reserve['reserve_status'];
		}
		
		if( !empty($select_old_car_id) ){ //เลขที่การจองนี้เคยมีการจองแบบเจาะจงรถ
			$qry_car_reserve = pg_query(" SELECT car_id FROM \"Reserves\" WHERE car_id = '$select_old_car_id' ");
			$num_rows_car_reserve = pg_num_rows($qry_car_reserve);
			
			if( ($num_rows_car_reserve == '1') and  ($arr_combo_car_stock[5] != 'R') ){ //มีการจอง 1 คันและคันที่เลือกใหม่เป็นคนละคันกับคันเดิม ให้ไป UPDATE field "car_status = A"
				
				$update_car_status = " UPDATE \"Cars\" SET car_status = 'A' WHERE car_id = '$select_old_car_id' ";
				if(!$res = pg_query($update_car_status)){
					$txt_error[] = "UPDATE car_status ไม่สำเร็จ $update_car_status";
					$status++;
				}
				
				$up_car_status_new_res = " UPDATE \"Cars\" SET car_status = 'R' WHERE car_id = '$arr_combo_car_stock[4]' ";
				if(!$res = pg_query($up_car_status_new_res)){
					$txt_error[] = "UPDATE car_status ไม่สำเร็จ $up_car_status_new_res";
					$status++;
				}
				//($select_old_car_id != $arr_combo_car_stock[4]) and
			}else { //มีการจองมากกว่า 1 รายการ
				$up_reserve_status = "UPDATE \"Reserves\" SET reserve_status ='3' WHERE res_id = '$res_id' ";
				if(!$res = pg_query($up_reserve_status)){
					$txt_error[] = "UPDATE reserve_status ไม่สำเร็จ $up_reserve_status";
					$status++;
				}
			}
		}else{ //เลขที่การจองนี้  ยังไม่มีการ Assign รถ
			if($arr_combo_car_stock[5] == 'R'){ //มีการจองซ้อนคัน
				$up_reserve_status = "UPDATE \"Reserves\" SET reserve_status ='3' WHERE res_id = '$res_id' ";
				if(!$res = pg_query($up_reserve_status)){
					$txt_error[] = "UPDATE reserve_status ไม่สำเร็จ $up_reserve_status";
					$status++;
				}
			}else{//ไม่มีการจองซ้อนคัน
				$up_car_status = "UPDATE \"Cars\" SET car_status ='R' WHERE car_id = '$arr_combo_car_stock[4]' ";
				if(!$res = pg_query($up_car_status)){
					$txt_error[] = "UPDATE car_status ไม่สำเร็จ $up_car_status";
					$status++;
				}
			}	
		}
		
		$in_qry = "UPDATE \"Reserves\" SET cus_id='$cus_id',car_id='$arr_combo_car_stock[4]',car_price='$str_car_price',down_price='$str_down_price',
										finance_price='$str_finance_price',installment='$str_installment',num_install='$str_num_install',
										finance_cus_id='$cb_finance',product_id='$product_id',type_insure='$cb_insure',reserve_color='$select_car_color',
										use_radio='$str_radio_commu',user_id='$_SESSION[ss_iduser]',remark='$remark' 
									WHERE res_id='$res_id' ";
		if(!$res=@pg_query($in_qry)){
			$txt_error[] = "UPDATE Reserves ไม่สำเร็จ $in_qry";
			$status++;
		}	
	}

	if($status == 0){
		pg_query("COMMIT");
		$data['success'] = true;
		$data['message'] = "บันทึกเรียบร้อยแล้ว ";
	}else{
		pg_query("ROLLBACK");
		$data['success'] = false;
		$data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
	}
    
echo json_encode($data);


}

//------------------------------------------------------------------------------//

elseif($cmd == "Change_Resv_Car_type_edit"){
   $t = $_GET['t'];
    if($t == 1)
	{ // รถใหม่
?>
		<div id="divresv_edit2" style="margin-top:10px; margin-left:25px">
			<label>เลือกรถ :</label> <input type="text" name="edit_combo_car_stock" id="edit_combo_car_stock" size="70" >
		</div>

		<script>
			$(document).ready(function(){
				//ค้นหารถใหม่เจาะจงรถ
				$("#edit_combo_car_stock").autocomplete({
					source: "reserv_car_new_api.php?cmd=car_new",
					minLength:1,
					select: function(event, ui) {
						if(ui.item.value != ''){
							var combo_car_stock = ui.item.value;  //$("#edit_combo_car_stock").val();
							var arr_car_stock = combo_car_stock.split("#");
							if(arr_car_stock[5] == "R"){
								show_car_reserve(arr_car_stock[4]);
							}
						}
					}
				});
			});
		</script>
<?php
    }
	else
	{ //รถใช้แล้ว
?>
		<div id="divresv_edit2" style="margin-top:10px; margin-left:25px">
			<label>เลือกรถใช้แล้ว :</label> <input type="text" name="edit_combo_car_stock" id="edit_combo_car_stock" size="70" >
		</div>

		<script>
			$(document).ready(function(){
				//ค้นหารถใหม่เจาะจงรถ
				$("#edit_combo_car_stock").autocomplete({
					source: "reserv_car_new_api.php?cmd=car_used",
					minLength:1,
					select: function(event, ui) {
						if(ui.item.value != ''){
							var combo_car_stock = ui.item.value;  //$("#edit_combo_car_stock").val();
							var arr_car_stock = combo_car_stock.split("#");
							if(arr_car_stock[5] == "R"){
								show_car_reserve(arr_car_stock[4]);
							}
						}
					}
				});
			});
		</script>
<?php
    }
}
?>