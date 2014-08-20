<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "save"){
    $product_id = pg_escape_string($_POST['product_id']);
	$po_id = pg_escape_string($_POST['po_id']);
    $po_auto_id = pg_escape_string($_POST['po_auto_id']);
    $txt_carnum = pg_escape_string($_POST['txt_carnum']);
    $txt_marnum = pg_escape_string($_POST['txt_marnum']);
    $licenseplate = pg_escape_string($_POST['txt_licenseplate']);
    $txt_color = pg_escape_string($_POST['txt_color']);
    $combo_wh = pg_escape_string($_POST['combo_wh']);

    
    $product_name = pg_escape_string($_POST['product_name']);
    $cost_val = pg_escape_string($_POST['cost_val']);
    $cost_vat = pg_escape_string($_POST['cost_vat']);
    
	$date_regis = pg_escape_string($_POST['date_regis']);
	$date_regis_chk = checknull($date_regis);
	$province_regis = pg_escape_string($_POST['province_regis']);
	$province_regis_chk = checknull($province_regis);
	$txt_radio	= pg_escape_string($_POST['txt_radio']);
	$txt_radio_chk = checknull($txt_radio);
	$txt_years = pg_escape_string($_POST['txt_years']);
	$combo_warehouse = pg_escape_string($_POST['combo_warehouse']);
	
	// กรณีเพิ่มชื่อลูกค้าใหม่
	$txt_pre_name = pg_escape_string($_POST['txt_pre_name']);
    $txt_firstname = pg_escape_string($_POST['txt_firstname']);
    $txt_lastname = pg_escape_string($_POST['txt_lastname']);
    $txt_address = pg_escape_string($_POST['txt_address']);
    $txt_post = pg_escape_string($_POST['txt_post']);
	$txt_name_reg = pg_escape_string($_POST['txt_name_reg']);
	$rdo_reg_address = pg_escape_string($_POST['rdo_reg_address']);
	$txt_address_reg = pg_escape_string($_POST['txt_address_reg']); 
    $txt_post_reg = pg_escape_string($_POST['txt_post_reg']);
    $chkContact = pg_escape_string($_POST['chkContact']);
    $txt_contact = pg_escape_string($_POST['txt_contact']); 
	$txt_post_contract = pg_escape_string($_POST['txt_post_contract']);
    $txt_phone = pg_escape_string($_POST['txt_phone']);
    $txt_reg = pg_escape_string($_POST['txt_reg']);
    $txt_barthdate = pg_escape_string($_POST['txt_barthdate']);
    $combo_cardtype = pg_escape_string($_POST['combo_cardtype']);
    $txt_cardother = pg_escape_string($_POST['#txt_cardother']);
    $txt_cardno  = pg_escape_string($_POST['txt_cardno']);
    $txt_carddate = pg_escape_string($_POST['txt_carddate']);
    $txt_cardby = pg_escape_string($_POST['txt_cardby']);
    $txt_job = pg_escape_string($_POST['txt_job']);
	//จบข้อมูลลูกค้าใหม่
	
	if($txt_years != ""){
		$nowyear = $txt_years;
	}else{
		$txt_years = $txt_years;
	}
	
	
	// table waiver	
	$car_value = $_POST['car_value'];
	
		if($car_value == ""){
			$txt_car_value = 0;
		}else {
			$txt_car_value = $car_value;
		}
		
	$sale = pg_escape_string($_POST['txt_sale']); 
	$array_sale = explode("#",$sale);
	$txt_sale = $array_sale[0];
		
	$hire = pg_escape_string($_POST['txt_hire']);
				
	$attestor = pg_escape_string($_POST['txt_attestor']);
	$array_attestor = explode("#",$attestor);
	$txt_attestor = $array_attestor[0];
	
	$note = pg_escape_string($_POST['note']);
	$txt_condate = pg_escape_string($_POST['txt_condate']);
	
    pg_query("BEGIN WORK");
	
    $status = 0;
    $txt_error = "Error!";
    
	$car_idno = genCarIDNO($po_id,$po_auto_id,$nowdate);
    $car_id = GetCarID();
	$car_type_id = getCarTypeID($po_id);
	
	$potype = substr($po_id, 0, 4);
	
	$generate_id = pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],15,'$potype')");
    $po_con = pg_fetch_result($generate_id,0);

	$txt_licenseplate = checknull($licenseplate);
	
	if($hire == "ไม่พบข้อมูลเก่า - เพิ่มข้อมูลใหม่"){
					
					$cus_id = GetCusID();

					if($chkContact == 1){ $str_contact = $txt_address; $str_post_contract = $txt_post; }else{ $str_contact = $txt_contact; $str_post_contract = $txt_post_contract; }
					if($rdo_reg_address == 1){$str_reg_address = $txt_address;$str_reg_post = $txt_post;}else{$str_reg_address = $txt_address_reg;$str_reg_post = $txt_post_reg;}
					if($combo_cardtype != "อื่นๆ"){ $str_cardtype = $combo_cardtype; }else{ $str_cardtype = $txt_cardother; }
					
					$in_qry="INSERT INTO \"Customers\" (\"cus_id\",\"pre_name\",\"cus_name\",\"surname\",\"address\",\"add_post\",\"nationality\",\"birth_date\",
														\"card_type\",\"card_id\",\"card_do_date\",\"card_do_by\",\"job\",\"contract_add\",\"telephone\",reg_customer,
														reg_address,reg_post,contract_post) 
												VALUES ('$cus_id','$txt_pre_name','$txt_firstname','$txt_lastname','$txt_address','$txt_post','$txt_reg','$txt_barthdate',
												'$str_cardtype','$txt_cardno','$txt_carddate','$txt_cardby','$txt_job','$str_contact','$txt_phone','$txt_name_reg',
												'$str_reg_address','$str_reg_post','$str_post_contract')";
												
					if(!$res=@pg_query($in_qry)){
						$txt_error .= "บันทึก Customers ไม่สำเร็จ $in_qry";
						$status++;
					}else{
						$txt_hire = $cus_id;
					}
	}else{
		$array_hire = explode("#",$hire);
		$txt_hire = $array_hire[0];
	}
				
    if( empty($txt_carnum) OR empty($txt_marnum) OR empty($txt_color) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน ! \n";
        $status++;
    }
    
	//ตรวจสอบเลขถังซ้ำ
    $qry_c1 = pg_query("SELECT COUNT(car_id) as car_id FROM \"Cars\" WHERE car_num='$txt_carnum' ");
    if($res_c1 = pg_fetch_array($qry_c1)){
        $count_car_num = $res_c1['car_id'];
    }
    
    //ตรวจสอบเลขเครื่องซ้ำ
    $qry_c2 = pg_query("SELECT COUNT(car_id) as car_id FROM \"Cars\" WHERE mar_num='$txt_marnum' ");
    if($res_c2 = pg_fetch_array($qry_c2)){
        $count_mar_num = $res_c2['car_id'];
    }
	
	if($car_type_id == '1'){
		if($count_car_num>0){
			$txt_error .= "หมายเลขถังนี้มีในระบบแล้ว ! \n";
			$status++;
		}
		if($count_mar_num>0){
			$txt_error .= "หมายเลขเครื่องนี้มีในระบบแล้ว ! \n";
			$status++;
		}
		
			//ตรวจสอบ car_idno ซ้ำ (concurrent)
			$qry = pg_query("select car_idno from \"Cars\" where car_idno = '$car_idno'");
			$num_caridno = pg_num_rows($qry);
			if($num_caridno > 0){
				$txt_error .= " มีการบันทึกรายการไปก่อนหน้านี้ กรุณาทำรายการใหม่ ! \n";
				$status++;
			}
	
			$qry = "INSERT INTO \"Cars\" (\"car_id\",\"car_num\",\"mar_num\",\"car_year\",\"color\",\"license_plate\",\"regis_by\",\"regis_date\",\"radio_id\",\"product_id\",\"po_id\",\"po_auto_id\",\"car_name\",\"car_type_id\",\"car_idno\",\"po_con\") VALUES 
			('$car_id','$txt_carnum','$txt_marnum','$nowyear','$txt_color',$txt_licenseplate,$province_regis_chk,$date_regis_chk,$txt_radio_chk,'$product_id','$po_id','$po_auto_id','$product_name','$car_type_id','$car_idno','$po_con')";
	
	
			if(!$res=@pg_query($qry)){
			$txt_error .= "INSERT Cars ไม่สำเร็จ $qry \n";
			$status++;
			}
			
			$qry = "UPDATE \"PurchaseOrders\" SET vender_id='$combo_warehouse' where po_id='$po_id'";
			if(!$res=@pg_query($qry)){
			$txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry \n";
			$status++;
			}
	}else {
		if($count_car_num>0 and $count_mar_num>0){
			
			//ตรวจสอบ car_idno ซ้ำ (concurrent)
			$qry = pg_query("select car_idno from \"Cars\" where car_idno = '$car_idno'");
			$num_caridno = pg_num_rows($qry);
			if($num_caridno > 0){
				$txt_error .= " มีการบันทึกรายการไปก่อนหน้านี้ กรุณาทำรายการใหม่ ! \n";
				$status++;
			}
		
			$qry = "UPDATE \"Cars\" set \"car_year\"='$nowyear',\"color\"='$txt_color',\"license_plate\"=$txt_licenseplate,\"regis_by\"=$province_regis_chk,\"regis_date\"=$date_regis_chk,\"radio_id\"=$txt_radio_chk,\"product_id\"='$product_id',
			\"po_id\"='$po_id',\"po_auto_id\"='$po_auto_id',\"car_name\"='$product_name',\"car_type_id\"='$car_type_id',\"car_idno\"='$car_idno',car_status='A',\"po_con\"='$po_con' 
			where \"car_num\"='$txt_carnum' and mar_num='$txt_marnum' and car_status='S' returning car_id";
			
			if(!$res=@pg_query($qry)){
				
				$txt_error .= "UPDATE Cars ไม่สำเร็จ $qry \n";
				$status++;
			}else{
				$car_id = pg_fetch_result($res,0); // ถ้าซ้ำจะใช้ car_id เดิม
			}
			
		}else{
		
			$qry = "INSERT INTO \"Cars\" (\"car_id\",\"car_num\",\"mar_num\",\"car_year\",\"color\",\"license_plate\",\"regis_by\",\"regis_date\",\"radio_id\",\"product_id\",\"po_id\",\"po_auto_id\",\"car_name\",\"car_type_id\",\"car_idno\",\"po_con\") VALUES 
			('$car_id','$txt_carnum','$txt_marnum','$nowyear','$txt_color',$txt_licenseplate,$province_regis_chk,$date_regis_chk,$txt_radio_chk,'$product_id','$po_id','$po_auto_id','$product_name','$car_type_id','$car_idno','$po_con')";
	
	
			if(!$res=@pg_query($qry)){
				$txt_error .= "INSERT Cars ไม่สำเร็จ $qry \n";
				$status++;
			}
		}
		
		$venter = checknull($txt_hire);
		$witness = checknull($txt_attestor);
		$finance_date = checknull($txt_condate);
		
		$qry = "insert into waiver (car_id_no,value_remain,venter,witness,comment,po_id,finance_date,sale,po_con) values ('$car_idno',$txt_car_value,$venter,$witness,'$note','$po_id',$finance_date,'$txt_sale','$po_con')";
		if(pg_query($qry)){
		}else{
			$txt_error .= "INSERT waiver ไม่สำเร็จ $qry \n";
			$status++;
		}
	}
	
    $qry = "INSERT INTO \"CarMove\" (\"car_id\",\"color\",\"wh_id\",\"date_in\") VALUES 
    ('$car_id','$txt_color','$combo_wh','$nowdate')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT CarMove ไม่สำเร็จ $qry \n";
        $status++;
    }
   
    $generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],4)");
    $rg_id=@pg_fetch_result($generate_id,0);

    $qry = "INSERT INTO \"ReceiveGoods\" (\"rg_id\",\"po_id\",\"po_auto_id\",\"date_in\",\"car_id\",\"user_id\") VALUES 
    ('$rg_id','$po_id','$po_auto_id','$nowdate','$car_id','$_SESSION[ss_iduser]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT ReceiveGoods ไม่สำเร็จ $qry \n";
        $status++;
    }
  
    $sum_unit = 0;
	$qry_sum_unit = "SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE'";
    $qry_detail = pg_query($qry_sum_unit);
	
    if($res_detail = pg_fetch_array($qry_detail)){
        $sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }else{
		$txt_error .= "Select ไม่สำเร็จ  $qry_sum_unit \n";
		$status++;
	}
     
    $sum_unit_add = 0;
	$qry_sum_unti_add = "SELECT COUNT(po_auto_id) as sum_unit_add FROM \"ReceiveGoods\" WHERE po_id='$po_id'";

    $qry_detail_add = pg_query($qry_sum_unti_add);

    if($res_detail = pg_fetch_array($qry_detail_add)){
        $sum_unit_add = $res_detail['sum_unit_add']; //จำนวน unit ที่ทำรายการแล้ว
    }else{
		$txt_error .= "Select ไม่สำเร็จ  $qry_sum_unti_add \n";
		$status++;
	}
   
    if($sum_unit == $sum_unit_add){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry \n";
            $status++;
        }
    }
	
	$qry_car_history = "insert into car_history (po_id,idno,car_idno,car_num,mar_num) values ('$po_id',null,'$car_idno','$txt_carnum','$txt_marnum')";
	if(pg_query($qry_car_history)){
	}else{
		$txt_error .= "insert car_history ไม่สำเร็จ $qry_car_history \n";
		$status++;
	}
	
    if($status == 0){
        pg_query("COMMIT");
		$data = $car_idno;
    }else{
        pg_query("ROLLBACK");
		$data = $txt_error;
    }
	
   echo $data;
    
}

elseif($cmd == "mat_save"){
    $product_id = $_POST['product_id'];
    $po_id = $_POST['po_id'];
    $po_auto_id = $_POST['po_auto_id'];
    $txt_mat_num = $_POST['txt_mat_num'];
    $combo_mat_wh = $_POST['combo_mat_wh'];
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "Error!";
    
    $car_id = GetCarID();
    
    if( empty($txt_mat_num) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน ! ";
        $status++;
    }
    
    $sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND product_id='$product_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด ของ product รายการนี้
    }

    $GetAmountPO = GetAmountPO("I",$po_id,$product_id);
    
    if($txt_mat_num > ($sum_unit-$GetAmountPO)){
        $txt_error .= "จำนวนรับเข้าไม่ถูกต้อง ! ";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$product_id','$txt_mat_num','I','$nowdate','$po_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT StockMovement ไม่สำเร็จ $qry ";
        $status++;
    }
    
    $last_sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $last_sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }
    
    $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
    
    if($last_sum_unit == $GetAmountPO_Last){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry ";
            $status++;
        }
    }

    if($status == 0){
        pg_query("COMMIT");
        $data = "success";
    }else{
        pg_query("ROLLBACK");
         $data = $txt_error;
    }
    echo $data;
    
}

elseif($cmd == "P_CouponGas_Save"){
    $po_id = pg_escape_string($_POST['po_id']);
    $product_id = pg_escape_string($_POST['product_id']);
    $txt_type = pg_escape_string($_POST['txt_type']);
    $txt_lam = pg_escape_string($_POST['txt_lam']);
    $txt_num = pg_escape_string($_POST['txt_num']);
    $txt_no = pg_escape_string($_POST['txt_no']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "Error!";
    
    if( empty($txt_type) OR empty($txt_lam) OR empty($txt_num) OR empty($txt_no) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน ! <br>";
        $status++;
    }
    
    $sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND product_id='$product_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด ของ product รายการนี้
    }

    $GetAmountPO = GetAmountPO("I",$po_id,$product_id);
    
    if($txt_num > ($sum_unit-$GetAmountPO)){
        $txt_error .= "จำนวนฉบับ ไม่ถูกต้อง ! <br>";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$product_id','$txt_num','I','$nowdate','$po_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT StockMovement ไม่สำเร็จ $qry <br>";
        $status++;
    }
    
    for($k=0; $k<$txt_num; $k++){
        $qry = "INSERT INTO \"P_CouponGas\" (po_id,book_id,running_id,amount) VALUES 
        ('$po_id','$txt_lam','$txt_no','$txt_type')";
        if(!$res=@pg_query($qry)){
            $txt_error .= "INSERT P_CouponGas ไม่สำเร็จ $qry <br>";
            $status++;
        }
        $txt_no++;
    }
    
    $last_sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $last_sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }
    
    $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
    
    if($last_sum_unit == $GetAmountPO_Last){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry <br>";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data = "success";
    }else{
        pg_query("ROLLBACK");
        $data = $txt_error;
    }
    echo $data;
}

elseif($cmd == "P_NewCarPlate_Save"){
    $po_id = pg_escape_string($_POST['po_id']);
    $product_id = pg_escape_string($_POST['product_id']);
    $txt_num = pg_escape_string($_POST['txt_num']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "Error!";
    
    if( empty($txt_num) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน ! <br>";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$product_id','1','I','$nowdate','$po_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT StockMovement ไม่สำเร็จ $qry <br>";
        $status++;
    }

    $qry = "INSERT INTO \"P_NewCarPlate\" (new_plate,date_in) VALUES ('$txt_num','$nowdate')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT P_NewCarPlate ไม่สำเร็จ $qry <br>";
        $status++;
    }

    $last_sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $last_sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }
    
    $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
    
    if($last_sum_unit == $GetAmountPO_Last){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry <br>";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data = "success";
    }else{
        pg_query("ROLLBACK");
		$data = $txt_error;
    }
    echo $data;
}

elseif($cmd == "P_Shirt_Save"){
    $po_id = pg_escape_string($_POST['po_id']);
    $product_id = pg_escape_string($_POST['product_id']);
    $combo_size = pg_escape_string($_POST['combo_size']);
    $txt_num = pg_escape_string($_POST['txt_num']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "Error!";
    
    if( empty($txt_num) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน !";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$product_id','$txt_num','I','$nowdate','$po_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT StockMovement ไม่สำเร็จ $qry";
        $status++;
    }

    for($k=0; $k<$txt_num; $k++){
        $qry = "INSERT INTO \"P_Shirt\" (po_id,size_shirt) VALUES ('$po_id','$combo_size')";
        if(!$res=@pg_query($qry)){
            $txt_error .= "INSERT P_Shirt ไม่สำเร็จ $qry";
            $status++;
        }
    }

    $last_sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $last_sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }
    
    $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
    
    if($last_sum_unit == $GetAmountPO_Last){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data= "success";
    }else{
        pg_query("ROLLBACK");
        $data = $txt_error;
    }
    echo $data;
}

elseif($cmd == "P_SignFrame_Save"){
    $po_id = pg_escape_string($_POST['po_id']);
    $product_id = pg_escape_string($_POST['product_id']);
    $txt_num = pg_escape_string($_POST['txt_num']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "Error!";
    
    if( empty($txt_num) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน !";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$product_id','$txt_num','I','$nowdate','$po_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT StockMovement ไม่สำเร็จ $qry";
        $status++;
    }

    for($k=0; $k<$txt_num; $k++){
        $qry = "INSERT INTO \"P_SignFrame\" (po_id) VALUES ('$po_id')";
        if(!$res=@pg_query($qry)){
            $txt_error .= "INSERT P_SignFrame ไม่สำเร็จ $qry";
            $status++;
        }
    }

    $last_sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $last_sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }
    
    $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
    
    if($last_sum_unit == $GetAmountPO_Last){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data = "success";
    }else{
        pg_query("ROLLBACK");
		$data = $txt_error;
    }
    echo $data;
}

elseif($cmd == "P_WrapSeat_Save"){
    $po_id = pg_escape_string($_POST['po_id']);
    $product_id = pg_escape_string($_POST['product_id']);
    $txt_license_plate = pg_escape_string($_POST['txt_license_plate']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "Error!";
    
    if( empty($txt_license_plate) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน ! <br>";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$product_id','1','I','$nowdate','$po_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT StockMovement ไม่สำเร็จ $qry <br>";
        $status++;
    }

    $qry = "INSERT INTO \"P_WrapSeat\" (po_id,license_plate) VALUES ('$po_id','$txt_license_plate')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT P_WrapSeat ไม่สำเร็จ $qry <br>";
        $status++;
    }

    $last_sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $last_sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }
    
    $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
    
    if($last_sum_unit == $GetAmountPO_Last){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry <br>";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data = "success";
    }else{
        pg_query("ROLLBACK");
        $data = $txt_error;
    }
    echo $data;
}

elseif($cmd == "P_Meter_Save"){
    $po_id = pg_escape_string($_POST['po_id']);
    $product_id = pg_escape_string($_POST['product_id']);
    $unit = pg_escape_string($_POST['unit']);
    $txt_num = pg_escape_string($_POST['txt_num']);
    $val_chkbox = pg_escape_string($_POST['val_chkbox']);
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = "Error!";
    
    if( empty($unit) OR empty($txt_num) ){
        $txt_error .= "กรุณากรอกข้อมูลให้ครบถ้วน !";
        $status++;
    }

    $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) VALUES 
    ('$product_id','$txt_num','I','$nowdate','$po_id',DEFAULT,'$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
    if(!$res=@pg_query($qry)){
        $txt_error .= "INSERT StockMovement ไม่สำเร็จ $qry";
        $status++;
    }
    
    $count_meter_val =  0;
    $arr_meter = explode(",", $val_chkbox);
    $c1 = count($arr_meter);
    foreach($arr_meter AS $v){
        if($v != ""){
            $count_meter_val++;
            $qry = "INSERT INTO \"P_Meter\" (mt_id) VALUES ('$v')";
            if(!$res=@pg_query($qry)){
                $txt_error .= "INSERT P_Meter ไม่สำเร็จ $qry";
                $status++;
            }
        }
    }
    
    if($count_meter_val != $txt_num){
        $txt_error .= "กรุณาตรวจสอบจำนวน และ รหัส ไม่เท่ากัน !";
        $status++;
    }


    $last_sum_unit = 0;
    $qry_detail = pg_query("SELECT SUM(unit) as sum_unit FROM \"PurchaseOrderDetails\" WHERE po_id='$po_id' AND cancel='FALSE' ");
    if($res_detail = pg_fetch_array($qry_detail)){
        $last_sum_unit = $res_detail['sum_unit']; //จำนวน unit ทั้งหมด
    }
    
    $GetAmountPO_Last = GetAmountPO("I",$po_id,'');
    
    if($last_sum_unit == $GetAmountPO_Last){ //ถ้ามี unit จำนวนเท่ากันให้อัพเดท Po ให้จบรายการด้วย
        $qry="UPDATE \"PurchaseOrders\" SET receive_all='TRUE' WHERE po_id='$po_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error .= "UPDATE PurchaseOrders ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data = "success";
    }else{
        pg_query("ROLLBACK");
        $data = $txt_error;
    }
    echo $data;
}
?>