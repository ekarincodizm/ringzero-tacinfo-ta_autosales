<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);

if($cmd == "autocomplete"){
    $term = pg_escape_string($_GET['term']);

    $qry_name=pg_query("select * from \"VAllCustomers\" WHERE \"cus_name\" LIKE '%$term%' ORDER BY \"cus_name\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $cus_id = trim($res_name["cus_id"]);
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        $car_id = $res_name["car_id"];
        $IDNO = $res_name["IDNO"];
        
        $dt['value'] = $cus_id."#".$cus_name." ".$surname."#".$car_id."#".$IDNO;
        $dt['label'] = "{$cus_id} , {$cus_name} {$surname} , {$car_id} , {$IDNO}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "changeProduct"){
    $pid = pg_escape_string($_GET['pid']);
    if(empty($pid)){
        echo 0;
        exit;
    }
    $qry = pg_query("SELECT sale_price FROM \"ProductService\" WHERE product_id='$pid' ");
    if( $res = pg_fetch_array($qry) ){
        echo $sale_price = $res['sale_price'];
    }else{
        echo 0;
    }
}

elseif($cmd == "CheckVat"){
    $pid = pg_escape_string($_GET['pid']);
    if(empty($pid)){
        echo "f";
        exit;
    }
    $qry = pg_query("SELECT use_vat FROM \"ProductService\" WHERE product_id='$pid' ");
    if( $res = pg_fetch_array($qry) ){
        echo $use_vat = $res['use_vat'];
    }else{
        echo "f";
    }
}

elseif($cmd == "checkTypeCus"){
    $idno = pg_escape_string($_GET['idno']);
    if(empty($idno)){
        echo "ไม่สามารถดึงข้อมูลได้ !";
        exit();
    }
    
    $qry=pg_query("select * from \"VAllCustomers\" WHERE \"IDNO\" = '$idno' ");
    if($res=pg_fetch_array($qry)){
        $type_cus = $res["type_cus"];
        if($type_cus == 0){
            echo "";
        }elseif($type_cus == 1){
            $qry=pg_query("select car_num,mar_num,license_plate,color,name from \"VContract\" WHERE \"IDNO\" = '$idno' ");
            if($res=pg_fetch_array($qry)){
                $car_num = $res["car_num"];
                $mar_num = $res["mar_num"];
                $license_plate = $res["license_plate"];
                $color = $res["color"];
                $name = $res["name"];
                echo "เลขถัง $car_num เลขเครื่อง $mar_num ทะเบียนรถ $license_plate สีรถ $color ยี่ห้อ/รุ่น $name";
            }
        }
    }
}

elseif($cmd == "save"){
    $txt_name = pg_escape_string($_POST['txt_name']);
        $arr_txt_name = explode("#",$txt_name);
    $arradd = json_decode(stripcslashes(pg_escape_string($_POST["arradd"])));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select gen_rec_no('$nowdate',1)");
    $inv_no=@pg_fetch_result($generate_id,0);

    $qry_c=pg_query("select \"IDNO\",res_id,car_id from \"VAllCustomers\" WHERE \"cus_id\" = '$arr_txt_name[0]' LIMIT 1 ");
    if($res_c=pg_fetch_array($qry_c)){
        $IDNO = $res_c["IDNO"];
        $res_id = $res_c["res_id"];
        $car_id = $res_c["car_id"];
    }
    
    $in_qry="INSERT INTO \"Invoices\" (\"inv_no\",\"res_id\",\"cus_id\",\"IDNO\",\"inv_date\",\"prn_date\",\"branch_out\",\"status\",\"user_id\",car_id) values 
    ('$inv_no','$res_id','$arr_txt_name[0]','$IDNO','$nowdate','$nowdate','$_SESSION[ss_office_id]','OCCR','$_SESSION[ss_iduser]','$car_id')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Invoices ไม่สำเร็จ $in_qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;
        $price = $value->price;
        $vat = $value->vat;
        
        if(empty($product) or empty($price)){
            continue;
        }

        $in_qry="INSERT INTO \"InvoiceDetails\" (\"inv_no\",\"service_id\",\"amount\",\"vat\") values 
        ('$inv_no','$product','$price','$vat')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก InvoiceDetails ไม่สำเร็จ $in_qry";
            $status++;
        }
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

elseif($cmd == "save_other"){
    $resid = pg_escape_string($_POST['resid']);
    $idno = pg_escape_string($_POST['idno']);
    $car_id = pg_escape_string($_POST['car_id']);
    $txt_name = pg_escape_string($_POST['txt_name']);
	$inv_date = pg_escape_string($_POST['inv_date']);
    $arr_txt_name = explode("#",$txt_name);
    $arradd = json_decode(stripcslashes(pg_escape_string($_POST["arradd"])));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $generate_id=@pg_query("select gen_rec_no('$inv_date',1)");  // ใช้วันที่ตั้งหนี้
    $inv_no=@pg_fetch_result($generate_id,0);
    if(empty($inv_no) OR $inv_no == ""){
        $txt_error[] = "gen_rec_no error";
        $status++;
    }
    
    $in_qry="INSERT INTO \"Invoices\" (\"inv_no\",\"res_id\",\"cus_id\",\"IDNO\",\"inv_date\",\"prn_date\",\"branch_out\",\"status\",\"user_id\",car_id) values 
    ('$inv_no','$resid','$arr_txt_name[0]',DEFAULT,'$inv_date','$nowdate','$_SESSION[ss_office_id]',DEFAULT,'$_SESSION[ss_iduser]','$car_id')";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "บันทึก Invoices ไม่สำเร็จ $in_qry";
        $status++;
    }

    foreach($arradd as $key => $value){
        $product = $value->product;
        $price = $value->price;
        $vat = $value->vat;
        
        if(empty($product) or empty($price)){
            continue;
        }

        $in_qry="INSERT INTO \"InvoiceDetails\" (\"inv_no\",\"service_id\",\"amount\",\"vat\") values 
        ('$inv_no','$product','$price','$vat')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "บันทึก InvoiceDetails ไม่สำเร็จ $in_qry";
            $status++;
        }
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
?>