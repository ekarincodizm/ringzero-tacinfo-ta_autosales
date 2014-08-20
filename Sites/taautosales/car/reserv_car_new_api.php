<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "autocomplete"){
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
        $matches[] = "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
}elseif($cmd == "sale_autocomplete"){
	$term = $_GET['term'];

    $qry_name=pg_query("select * from  v_sale_group WHERE fullname LIKE '%$term%' ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        
        $id_user = trim($res_name["id_user"]);
		$fullname = trim($res_name["fullname"]);
        
        $dt['value'] = $id_user."#".$fullname;
        $dt['label'] = "{$id_user} , {$fullname} ";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);

}elseif($cmd == "witness_autocomplete"){
	$term = $_GET['term'];

    $qry_name=pg_query("select * from fuser WHERE fullname LIKE '%$term%' ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        
        $id_user = trim($res_name["id_user"]);
		$fullname = trim($res_name["fullname"]);
        
        $dt['value'] = $id_user."#".$fullname;
        $dt['label'] = "{$id_user} , {$fullname} ";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);

}elseif($cmd == "car_new"){
	$term = $_GET['term'];

	$qry_cars = pg_query(" SELECT \"Cars\".car_id, 
							\"Cars\".car_name, 
							\"Cars\".car_num, 
							\"Cars\".mar_num, 
							\"Cars\".car_year, 
							\"Cars\".color, 
							\"Cars\".car_idno, 
							\"CarColor\".color_name, 
							\"Cars\".license_plate, 
							\"Cars\".car_type_id, 
							\"CarType\".car_type_name, 
							\"Cars\".car_status
						   FROM \"Cars\", 
							\"CarType\", 
							\"CarColor\"
						  WHERE \"CarType\".car_type_id::text = \"Cars\".car_type_id::text 
						  AND \"CarColor\".color_id::text = \"Cars\".color::text
						  AND \"Cars\".car_type_id = '1' 
						  AND (\"Cars\".car_status = 'A' OR \"Cars\".car_status = 'R')
						  AND  (\"Cars\".car_idno LIKE '%$term%' OR \"Cars\".license_plate LIKE '%$term%')	");
						  
						  
	/*$qry_cars = pg_query(" SELECT * FROM v_cars WHERE car_type_id = '1' 
							AND (car_status = 'A' or car_status = 'R')
							car_idno LIKE '%$term%' OR license_plate LIKE '%$term%'
							");*/
							
    $numrows = pg_num_rows($qry_cars);
    while($res_cars = pg_fetch_array($qry_cars)){
		$car_name = trim($res_cars["car_name"]);
		$car_id = $res_cars['car_id'];
		$car_name = $res_cars['car_name'];
		$car_num = $res_cars['car_num'];
		$mar_num = $res_cars['mar_num'];
		$car_year = $res_cars['car_year'];
		$color = $res_cars['color'];
		$color_name = $res_cars['color_name'];
		$car_idno = $res_cars['car_idno'];
		$license_plate = $res_cars['license_plate'];
		$car_type_name = $res_cars['car_type_name'];
		$car_status = $res_cars['car_status'];
		
        //$dt['value'] = $car_idno."#".$car_name;
		//$dt['value'] = $car_name."#".$car_num."#".$mar_num."#".$car_year."#".$color_name."#".$car_idno."#".$car_status;
		//$dt['value'] = $car_id."#".$car_status."#".$car_idno."#".$car_name."#".$color_name;
		$dt['value'] = $car_idno."#".$car_name."#".$color."#".$color_name."#".$car_id."#".$car_status;
		$dt['label'] = "{$car_name}/{$car_num}/{$mar_num}/{$car_year}/{$color_name}/{$car_idno}/{$car_status}";
        $matches[] = $dt;
    }
	
    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
}elseif($cmd == "car_used"){
	$term = $_GET['term'];

	$qry_cars = pg_query(" SELECT \"Cars\".car_id, 
							\"Cars\".car_name, 
							\"Cars\".car_num, 
							\"Cars\".mar_num, 
							\"Cars\".car_year, 
							\"Cars\".color, 
							\"Cars\".car_idno, 
							\"CarColor\".color_name, 
							\"Cars\".license_plate, 
							\"Cars\".car_type_id, 
							\"CarType\".car_type_name, 
							\"Cars\".car_status
						   FROM \"Cars\", 
							\"CarType\", 
							\"CarColor\"
						  WHERE \"CarType\".car_type_id::text = \"Cars\".car_type_id::text 
						  AND \"CarColor\".color_id::text = \"Cars\".color::text
						  AND \"Cars\".car_type_id <> '1' 
						  AND (\"Cars\".car_status = 'A' OR \"Cars\".car_status = 'R')
						  AND (\"Cars\".car_idno LIKE '%$term%' OR \"Cars\".license_plate LIKE '%$term%')	");
						
    $numrows = pg_num_rows($qry_cars);
    while($res = pg_fetch_array($qry_cars)){
		$car_id = $res['car_id'];
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
		
		$dt['value'] = $license_plate."#".$car_name."#".$color."#".$color_name."#".$car_id."#".$car_status;
		$dt['label'] = "{$car_name}/{$car_num}/{$mar_num}/{$car_year}/{$color_name}/{$license_plate}/{$car_type_name}/{$car_status}";
        $matches[] = $dt;
    }
	
    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
	
}elseif($cmd == "changePlan"){
    $id = $_GET['id'];
    $qry=pg_query("select sale_price from \"Products\" WHERE \"product_id\" = '$id' ");
    if($res=pg_fetch_array($qry)){
        echo $sale_price = $res["sale_price"];
    }
}elseif($cmd == "CheckPlan"){
    $id = $_GET['id'];
    $qry=pg_query("select cost_price from \"Products\" WHERE \"product_id\" = '$id' ");
    if($res=pg_fetch_array($qry)){
        echo $cost_price = $res["cost_price"];
    }
}

elseif($cmd == "CheckCarsCostPrice"){
    $id = $_GET['id'];
    $qry=pg_query("select cost_val,cost_vat from \"Cars\" WHERE \"car_id\" = '$id' ");
    if($res=pg_fetch_array($qry)){
        echo $cost_val = round($res["cost_val"]+$res["cost_vat"]);
    }
}elseif($cmd == "save"){
	$car_type = pg_escape_string($_POST['car_type']);
	$txt_name=$_POST['txt_name'];
	$txt_pre_name = pg_escape_string($_POST['txt_pre_name']);
	$txt_firstname = pg_escape_string($_POST['txt_firstname']);
	$txt_lastname = pg_escape_string($_POST['txt_lastname']);
	$txt_address = pg_escape_string($_POST['txt_address']);
	$txt_post = pg_escape_string($_POST['txt_post']);
	$chkContact = pg_escape_string($_POST['chkContact']);
	$txt_contact = pg_escape_string($_POST['txt_contact']);
	$txt_phone = pg_escape_string($_POST['txt_phone']);

	$txt_reg = pg_escape_string($_POST['txt_reg']);
	$txt_barthdate = pg_escape_string($_POST['txt_barthdate']);
	$combo_cardtype = pg_escape_string($_POST['combo_cardtype']);
	$txt_cardother = pg_escape_string($_POST['txt_cardother']);
	$txt_cardno = pg_escape_string($_POST['txt_cardno']);
	$txt_carddate = pg_escape_string($_POST['txt_carddate']);
	$txt_cardby = pg_escape_string($_POST['txt_cardby']);
	$txt_job = pg_escape_string($_POST['txt_job']);

	$radio_resv_type = pg_escape_string($_POST['radio_resv_type']);
	$resv_car_plan = pg_escape_string($_POST['resv_car_plan']); // รูปแบบรถ กรณีไม่เจาะจงรถ
	$arr_resv_car_plan = explode("#",$resv_car_plan);
	$radio_car_color = pg_escape_string($_POST['radio_car_color']);
	$car_color = pg_escape_string($_POST['car_color']);
	$combo_car_stock = pg_escape_string($_POST['combo_car_stock']);
	$arr_combo_car_stock = explode("#",$combo_car_stock);  //เลือกรถ
	$radio_buy_type = pg_escape_string($_POST['radio_buy_type']);
	$txt_buy_price1 = pg_escape_string($_POST['txt_buy_price1']);
	$txt_buy_price2 = pg_escape_string($_POST['txt_buy_price2']);
	$txt_buy_down = pg_escape_string($_POST['txt_buy_down']);
	$txt_buy_numdue = pg_escape_string($_POST['txt_buy_numdue']);
	$txt_buy_monnydue = pg_escape_string($_POST['txt_buy_monnydue']);
	$cb_finance = pg_escape_string($_POST['cb_finance']);
	$cb_insure = pg_escape_string($_POST['cb_insure']);
	$radio_commu = pg_escape_string($_POST['radio_commu']);
	$txt_resv_money = pg_escape_string($_POST['txt_resv_money']);
	$txt_date_car = pg_escape_string($_POST['txt_date_car']);
	$span_sum = pg_escape_string($_POST['span_sum']);
	$area_remark_new = pg_escape_string($_POST['area_remark_new']);
	$sale = explode("#",$_POST['sale']);
	$witness = explode("#",$_POST['witness']);

	//echo "<script>alert('".$combo_car_stock."%".$arr_combo_car_stock[4]."')</script>";
	
	
	pg_query("BEGIN WORK");
	$status = 0;
	$txt_error = array();

if($txt_name == "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){

    $cus_id = GetCusID();

    if($chkContact == 1){ $str_contact = $txt_address; }else{ $str_contact = $txt_contact; }
    if($combo_cardtype != "อื่นๆ"){ $str_cardtype = $combo_cardtype; }else{ $str_cardtype = $txt_cardother; }
    
    $in_qry="INSERT INTO \"Customers\" (cus_id,pre_name,cus_name,surname,address,add_post,nationality,birth_date,
										card_type,card_id,card_do_date,card_do_by,job,contract_add,telephone) 
							VALUES ('$cus_id','$txt_pre_name','$txt_firstname','$txt_lastname','$txt_address','$txt_post','$txt_reg','$txt_barthdate',
									'$str_cardtype','$txt_cardno','$txt_carddate','$txt_cardby','$txt_job','$str_contact','$txt_phone')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Customers ไม่สำเร็จ $in_qry";
        $status++;
    }

}else{
    $arr_txt_name = explode("#",$txt_name);
    $cus_id = $arr_txt_name[0];
}

//============================== สร้างเลขที่การจอง  res_id ============================//
$generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],1)");
$res_id=@pg_fetch_result($generate_id,0);

//****************************************************บันทึกอุปกรณ์มาตรฐาน *****************************************//
	$qry_std_proc = pg_query(" SELECT * from std_products ");
	$num_rows = pg_num_rows($qry_std_proc);
		$std_proc_id = array();
		while( $res_std_proc = pg_fetch_array($qry_std_proc) ){
			$std_proc_id[] = $res_std_proc['std_proc_id'];
		}
	if($num_rows != 0){
		foreach($std_proc_id as $i => $val){
			$tmp_std_proc_id = $val;
			
			$in_qry_std_proc = "INSERT INTO reserve_std_proc(res_id,std_proc_id)
									VALUES('$res_id','$tmp_std_proc_id')";
									
				if(!$res=@pg_query($in_qry_std_proc)){
					$txt_error[] = "บันทึก  reserve_std_proc ไม่สำเร็จ $in_qry_std_proc";
					$status++;
				}
		}
	}

//============================== รูปแบบการซื้อ ============================================//
if($radio_buy_type == "1"){ //ซื้อผ่อน
    $str_car_price = $txt_buy_price2;
    $str_down_price = $txt_buy_down;
    $str_installment = $txt_buy_monnydue;
    $str_num_install = $txt_buy_numdue; //จำนวนงวดที่ผ่อน
    $str_finance_price = $txt_buy_price2-$txt_buy_down;
	$str_finance = $cb_finance;
}else if($radio_buy_type == "2"){ //ซื้อสด
    $str_car_price = $txt_buy_price1;
    $str_down_price = $txt_buy_price1; //เงินดาวน์ ต้องเท่ากับเงินจอง
    $str_installment = 0;
    $str_num_install = 0;
    $str_finance_price = 0;
	$str_finance = "";
}
//---------------------------------------------------------------------//


if($radio_commu == 1){
    $str_radio_commu = "TRUE";
}else{
    $str_radio_commu = "FALSE";
}

if($arr_combo_car_stock[4] == ""){ //ไม่เจาะจง
    
    $in_qry="INSERT INTO \"Reserves\" (res_id,cus_id,reserve_date,receive_date,car_price,down_price,finance_price,
										installment,num_install,finance_cus_id,remark,
										product_id,type_insure,use_radio,user_id,witness,reserve_status,reserve_color,\"IDNO\") 
							VALUES('$res_id','$cus_id','$nowdate','$txt_date_car','$str_car_price','$str_down_price','$str_finance_price',
										'$str_installment','$str_num_install','$str_finance','$area_remark_new',
										'$arr_resv_car_plan[0]','$cb_insure','$str_radio_commu','$sale[0]','$witness[0]',DEFAULT,'$car_color',null)";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Reserves ไม่สำเร็จ $in_qry";
        $status++;
    }
}else{ //เจาะจงรถ
	//============================================= เลือก product =========================================//
	$qry_product = pg_query("SELECT product_id from v_product WHERE car_id = '$arr_combo_car_stock[4]' ");
		if( $res_product= pg_fetch_array($qry_product) ){
			$product_id = $res_product['product_id'];
		}
	//------------------------------------------------------------------------------------------//
	
    if($txt_buy_down == 0){
        if(substr($stkcar_po_id, 0, 2) == "PO"){ //กรณีมีการออกรถเลย และเป็นรายการ PO ต้อง gen_id มาใช้งาน
            $generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],2)");
            $genidno=@pg_fetch_result($generate_id,0);
        }else{ //หากไม่ใช่รายการ PO ให้ดึง po_id จาก VStockCars มาใช้งานได้เลย เช่น DS-1111111
            $genidno=$stkcar_po_id;
        }
        
        $in_qry="INSERT INTO \"Reserves\"(res_id,cus_id,car_id,reserve_date,receive_date,car_price,down_price,
											finance_price,installment,num_install,finance_cus_id,remark,product_id,\"IDNO\",
											type_insure,use_radio,user_id,witness,reserve_color) 
									VALUES('$res_id','$cus_id','$arr_combo_car_stock[4]','$nowdate','$txt_date_car','$str_car_price','$str_down_price',
											'$str_finance_price','$str_installment','$str_num_install','$str_finance','$area_remark_new','$product_id',null,
											'$cb_insure','$str_radio_commu','$sale[0]','$witness[0]','$car_color')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก Reserves ไม่สำเร็จ $in_qry";
            $status++;
        }
    }else{ // ซื้อเงินดาวน์
        $in_qry="INSERT INTO \"Reserves\" (res_id,cus_id,car_id,reserve_date,receive_date,car_price,down_price,
											finance_price,installment,num_install,finance_cus_id,remark,product_id,\"IDNO\",
											type_insure,use_radio,user_id,witness,reserve_color) 
								VALUES ('$res_id','$cus_id','$arr_combo_car_stock[4]','$nowdate','$txt_date_car','$str_car_price','$str_down_price',
										'$str_finance_price','$str_installment','$str_num_install','$str_finance','$area_remark_new','$product_id',null,
										'$cb_insure','$str_radio_commu','$sale[0]','$witness[0]','$car_color')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก Reserves ไม่สำเร็จ $in_qry";
            $status++;
        }
    }
	//=========================== ปรับปรุงสถานะการจอง ==============================================================//
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
	
//-------------------------------------------------------------------------------------------------------//
}//end เจาะจงรถ

// ทำการตั้งหนี้ ทุก กรณ๊ แม้แต่ ดาวน์ 0 บาท
if($txt_buy_down >= 0){
    $in_qry="INSERT INTO \"ReserveDetails\" (res_id,do_date,cash_amt,cheque_no,cheque_bank,cheque_branch,cheque_amt,
											cheque_date,appointment_date,appointment_amt) 
								    VALUES ('$res_id','$nowdate','0',DEFAULT,DEFAULT,DEFAULT,'0',
											DEFAULT,'$txt_date_car','$span_sum')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก ReserveDetails ไม่สำเร็จ $in_qry";
        $status++;
    }
    
    if($radio_buy_type == "1"){//ซื้อผ่อน
        if($span_sum == 0){
            $GetServicesConstant = GetServicesConstant("CARDW");
        }elseif( $txt_resv_money == $txt_buy_down ){
            $GetServicesConstant = GetServicesConstant("CARDW");
        }else{
            $GetServicesConstant = GetServicesConstant("CARRE");
        }
    }elseif($radio_buy_type == "2"){//ซื้อสด
        if($span_sum == 0){
            $GetServicesConstant = GetServicesConstant("CARCA");
        }elseif( $txt_resv_money == $txt_buy_down ){
            $GetServicesConstant = GetServicesConstant("CARCA");
        }else{
            $GetServicesConstant = GetServicesConstant("CARRE");
        }
    }
    
	//=========================== สร้างเลขที่ใบแจ้งหนี้ =========================================//
    $generate_id=@pg_query("select gen_rec_no('$nowdate',1)");
    $inv_no=@pg_fetch_result($generate_id,0);
    //--------------------------------------------------------------------------------//
	
    $in_qry="INSERT INTO \"Invoices\" (inv_no,res_id,cus_id,\"IDNO\",inv_date,prn_date,branch_out,status,user_id,car_id) 
							VALUES ('$inv_no','$res_id','$cus_id','','$nowdate','$nowdate','$_SESSION[ss_office_id]',DEFAULT,'$sale[0]','$arr_combo_car_stock[4]')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Invoices ไม่สำเร็จ $in_qry";
        $status++;
    }
    
    $in_qry="INSERT INTO \"InvoiceDetails\" (inv_no,service_id,amount,vat) 
									VALUES ('$inv_no','$GetServicesConstant','$txt_resv_money','0')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Cash InvoiceDetails ไม่สำเร็จ $in_qry";
        $status++;
    }
}

if($status == 0){
    pg_query("COMMIT");
    $data['success'] = true;
    $data['resid'] = "$res_id";
    //$data['message'] = "บันทึกเรียบร้อยแล้ว $radio_resv_type  $combo_car_stock  $arr_combo_car_stock[4]";
	$data['message'] = "บันทึกเรียบร้อยแล้ว";


}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
}
    
echo json_encode($data);
}

elseif($cmd == "Change_Resv_Car_type"){
    $t = $_GET['t'];
    if($t == 1){
?>
<input type="radio" name="radio_resv_type" id="radio_resv_type" value="1" checked> ไม่เจาะจงรถ 
<input type="radio" name="radio_resv_type" id="radio_resv_type" value="2" > เจาะจงรถ

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>สีรถแท๊กซี่:</label>
	<select name="ddl_car_color" id="ddl_car_color" >
		<option value="not">เลือก</option>
		<option value="ฟ้า">ฟ้า</option>
		<option value="เหลือง">เหลือง</option>		
		<option value="เขียวเหลือง">เขียวเหลือง</option>
		<option value="สีเดิม">สีเดิม</option>
	</select>
	
<div id="divresv1" style="margin-top:10px; margin-left:25px;">
<?php //====================== รถใหม่ ไม่เจาะจงรถ ===========================//?>
รูปแบบรถ : <select name="resv_car_plan" id="resv_car_plan" onchange="javascript:changePlan()">
			<option value="">เลือก</option>
				<?php
				$qry = pg_query("SELECT * FROM \"Products\" WHERE \"link_table\"='Cars' ORDER BY \"name\" ASC ");
				while($res = pg_fetch_array($qry)){
					$product_id = $res['product_id'];
					$name = $res['name'];
					echo "<option value=\"$product_id#$name\">$name</option>";
				}
				?>
		</select>
<?php //------------------------------------------------------//?>

<!--สีรถ : <input type="radio" name="radio_car_color" id="radio_car_color" value="ฟ้า" checked>ฟ้า 
<input type="radio" name="radio_car_color" id="radio_car_color" value="เหลือง">เหลือง 
<input type="radio" name="radio_car_color" id="radio_car_color" value="เขียวเหลือง">เขียวเหลือง  -->

</div>
<?php //====================== รถใหม่ เจาะจงรถ =================================?>
<div id="divresv2" style="margin-top:10px; margin-left:25px; display:none">


<!--<label>เลือกรถ :</label> -->
<select name="combo_car_stock" id="combo_car_stock" onchange="javascript:display_reserve()">
		<option value="">กรุณาเลือกรถ</option>
			<?php
			$qry_cars = pg_query("SELECT * FROM v_cars WHERE car_type_id = '1' and (car_status = 'A' or car_status = 'R') ORDER BY car_name ASC ");
			while($res = pg_fetch_array($qry_cars)){
				$car_id = $res['car_id'];
				$car_name = $res['car_name'];
				$car_num = $res['car_num'];
				$mar_num = $res['mar_num'];
				$car_year = $res['car_year'];
				$color = $res['color'];
				$color_name = $res['color_name'];
				$car_idno = $res['car_idno'];
				$license_plate = $res['license_plate'];
				$car_type_name = $res['car_type_name'];
				$car_status = $res['car_status'];
			   
			?>
				<option value="<?php echo "$car_id#$car_status#$color#$color_name"; ?>"><?php echo "$car_name/$car_num/$mar_num/$car_year/$color_name/$car_idno/$car_status"; ?></option>
			<?php
			}
			?>
	</select>
</div><br>

<script>
$(document).ready(function(){
	
});

    $("input[name='radio_resv_type']").change(function(){
        if( $('input[id=radio_resv_type]:checked').val() == "2" ){
            $('#divresv1').hide('fast');
            $('#divresv2').show('fast');
            $('#resv_car_plan').val('');
            $('#txt_buy_price1').val('');
            $('#txt_buy_price2').val('');
        }else{
            $('#divresv1').show('fast');
            $('#divresv2').hide('fast');
        }
        changeBtn();
        Summary();
    });
</script>
<?php
//=========================== ===========สิ้นสุดรถใหม่ ===========================================//
    }else{
?>

<input type="radio" name="radio_resv_type" id="radio_resv_type" value="2" checked style="display:none">

<div id="divresv1" style="margin-top:10px; margin-left:25px; display:none">
รูปแบบรถ : <select name="resv_car_plan" id="resv_car_plan" onchange="javascript:changePlan()">
<option value="not">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Products\" WHERE \"link_table\"='Cars' ORDER BY \"name\" ASC ");
while($res = pg_fetch_array($qry)){
    $product_id = $res['product_id'];
    $name = $res['name'];
    echo "<option value=\"$product_id#$name\">$name</option>";
}
?>

</select>
<!--&nbsp;สีรถ : <input type="radio" name="radio_car_color" id="radio_car_color" value="ฟ้า" checked>ฟ้า 
<input type="radio" name="radio_car_color" id="radio_car_color" value="เหลือง">เหลือง 
<input type="radio" name="radio_car_color" id="radio_car_color" value="เขียวเหลือง">เขียวเหลือง -->
</div>

<?php //==================  รถใช้แล้ว  =================== ?>
<div id="divresv2" style="margin-top:10px; margin-left:5px">
<br>
	<select name="combo_car_stock" id="combo_car_stock" onchange="javascript:display_reserve()">
		<option value="not" width="150px">กรุณาเลือกรถ</option>
			<?php
				$qry_cars = pg_query("SELECT * FROM v_cars WHERE car_type_id <> '1' and (car_status = 'A' or car_status = 'R') ORDER BY car_name ASC ");
				while($res = pg_fetch_array($qry_cars)){
					$car_id = $res['car_id'];
					$car_name = $res['car_name'];
					$car_num = $res['car_num'];
					$mar_num = $res['mar_num'];
					$car_year = $res['car_year'];
					$color = $res['color'];
					$color_name = $res['color_name'];
					$license_plate = $res['license_plate'];
					$car_type_name = $res['car_type_name'];
					$car_status = $res['car_status'];
					
					$display = "$car_name/$car_num/$mar_num/$car_year/$color_name/"."\n"."$license_plate/$car_type_name/$car_status";
				   
				   /*$str = "123456789";
	$str_arr = str_split($str,5);
	
	for($i=0;$i<count($str_arr);$i++){
		$str_all .= $str_arr[$i]."<br/>";
	}
      echo $str_all;*/
				   
				?>
					<option value="<?php echo "$car_id#$car_status#$color#$color_name"; ?>"><?php echo $display;?></option>
				<?php
				}
				?>
	</select>
</div>
<?php
    }
}

elseif($cmd == "interest_rate"){
    $car_price = $_GET['car_price'];
    $down_price = $_GET['down_price'];
    $installment = $_GET['installment'];
    $num_installment = $_GET['num_installment'];
    
    echo interest_rate($car_price-$down_price,$installment,$num_installment);
}
?>