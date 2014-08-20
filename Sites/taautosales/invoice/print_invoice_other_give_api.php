<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = $_REQUEST['cmd'];
if($cmd == "ChangeProduct"){
    $id = $_GET['id'];
    $product = $_GET['product'];
    $t = $_GET['t'];
        if($t == 1){
            unset($_SESSION["details_data"][$id]);
        }
    $qry_list = pg_query("SELECT * FROM \"ListForSale\" WHERE product_id='$product' ");
    if( $res_list = pg_fetch_array($qry_list) ){
        $sale_price = $res_list['sale_price'];
        if(empty($sale_price) OR $sale_price == ""){
            echo 0;
        }else{
            echo $sale_price;
        }
    }else{
        echo 0;
    }
}

elseif($cmd == "save"){
    $res_id = $_POST['res_id'];
    $license_plate = $_POST['license_plate'];
    $arradd = json_decode(stripcslashes($_POST["arradd"]));

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    foreach($arradd as $key => $value){
        $id = $value->id;
        $product = $value->product;
        $unit = $value->unit;
        $gif_unit = $value->unit;
        if(empty($product) or empty($unit)){
            continue;
        }

        $unit = $unit*-1;
        $qry = "INSERT INTO \"StockMovement\" (product_id,amount,type_inout,date_inout,ref_1,ref_2,user_id,wh_id) 
										VALUES ('$product','$unit','O','$nowdate','$license_plate','Free','$_SESSION[ss_iduser]','$_SESSION[ss_office_id]')";
		
		 if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
            $status++;
        }
		
		//========================= บันทึกรายการของแถม ================================
		$qry_gif = "INSERT INTO gif_detail(res_id,product_id,amount)
								VALUES('$res_id','$product','$gif_unit')";				
		if(!$res=@pg_query($qry_gif)){
            $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry_gif";
            $status++;
        }						
		
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT StockMovement ไม่สำเร็จ $qry";
            $status++;
        }
            
        for($k=1; $k<=$unit; $k++){
            $field_name_data = array();
            $qry_list = pg_query("SELECT field_name FROM \"ProjectField\" WHERE project_id='$product' ORDER BY id ASC");
            while( $res_list = pg_fetch_array($qry_list) ){
                $field_name = $res_list['field_name'];
                $field_name_data[] = $_SESSION["details_give_data"][$id][$k][$field_name];
            }

            $update_product=@pg_query("select update_product('$license_plate','$product','$field_name_data[0]','$field_name_data[1]')");
            $res_product=@pg_fetch_result($update_product,0);
            if(!$res_product){
                $txt_error[] = "update_product ไม่สำเร็จ \nรายการที่ $id : $license_plate,$product,$field_name_data[0],$field_name_data[1]";
                $status++;
            }
        }
    }//end foreach
	
	


    if($status == 0){
        pg_query("COMMIT");
        //pg_query("ROLLBACK");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        unset($_SESSION["details_give_data"]);
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "div_dialog_details"){
    $id = $_GET['id'];
    $product = $_GET['product'];
    $unit = $_GET['unit'];
?>
<form id="form<?php echo $id; ?>">

<?php
for($k=1; $k<=$unit; $k++){
    $j = 0;
?>

<table cellpadding="3" cellspacing="0" border="0" width="100%">
<?php
    $qry_list = pg_query("SELECT * FROM \"ProjectField\" WHERE project_id='$product' ORDER BY id ASC");
    while( $res_list = pg_fetch_array($qry_list) ){
        $j++;
        $field_name = $res_list['field_name'];
        $label_name = $res_list['label_name'];
?>
<tr>
    <td width="8%"><?php if($j == 1){ echo "<b>#$k</b>"; } ?></td>
    <td width="30%"><?php echo $label_name; ?> :</td>
    <td><input type="text" name="<?php echo $id."__".$k."__".$field_name; ?>" id="<?php echo $id."__".$k."__".$field_name; ?>" value="<?php echo $_SESSION["details_give_data"][$id][$k][$field_name]; ?>"></input></td>
</tr>
<?php
    }
?>
</table>

<div class="linedotted" style="margin:0; padding:0"></div>
    
<?php
}
?>
    
<?php if($j > 0){ ?>
<div style="margin-top:8px; text-align:right">
<input type="button" name="btnSaveDetails<?php echo $id; ?>" id="btnSaveDetails<?php echo $id; ?>" value="บันทึก">
</div>
<?php }else{ echo "<div style=\"padding:5px\">Product รายการนี้ - ไม่มีรายละเอียดที่ต้องใส่เพิ่ม</div>"; } ?>
</form>

<script>
$('#btnSaveDetails<?php echo $id; ?>').click(function(){
    var bstr=$('#form<?php echo $id; ?>').serialize();
    $.post('print_invoice_other_give_api.php',{
        cmd: 'save_details',
        id: <?php echo $id; ?>,
        results: bstr
    },
    function(data){
        if(data.success){
            $('#div_details_dialog').remove();
        }else{
            alert(data.message);
        }
    },'json');
});
</script>
<?php
}

elseif($cmd == "save_details"){
    $id = $_POST['id'];
    $results = $_POST['results'];
    $perfs = explode("&", $results);
    foreach($perfs as $perf){
        $perf_key_values = explode("=", $perf);
        $key = urldecode($perf_key_values[0]);
        $values = urldecode($perf_key_values[1]);
        
        $arr_key = explode("__", $key);
        $id = $arr_key[0];
        $unit = $arr_key[1];
        $field_name = $arr_key[2];
        $_SESSION["details_give_data"][$id][$unit][$field_name] = $values;
    }

    if($_SESSION["details_give_data"][$id] != ""){
        $data['success'] = true;
    }else{
        $data['success'] = false;
        $data['message'] = "ผิดผลาด !";
    }
    echo json_encode($data);
}

elseif($cmd == "unset_session"){
    $id = $_GET['id'];
    unset( $_SESSION["details_give_data"][$id] );
    echo "clear success.";
}
?>