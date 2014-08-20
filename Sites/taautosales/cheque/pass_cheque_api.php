<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "chequedetail"){
    $id = pg_escape_string($_GET['id']);
?>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>no.</td>
    <td>res_id</td>
    <td>Name</td>
    <td>service_id</td>
    <td>Amt</td>
</tr>
<?php
$qry = pg_query("SELECT * FROM \"VChequeDetail\" WHERE running_no='$id' ORDER BY res_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $res_id = $res['res_id'];
    $full_name = $res['full_name'];
    $service_id = $res['service_id'];
    $cus_amount = $res['cus_amount'];
    
    $service_name = GetServicesName($service_id);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $j; ?></td>
    <td><?php echo $res_id; ?></td>
    <td><?php echo $full_name; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($cus_amount,2); ?></td>
</tr>
<?php
}
?>
</table>

<?php
}

elseif($cmd == "save"){
    $rid = pg_escape_string($_POST['rid']);
    $cid = pg_escape_string($_POST['cid']);
	$appdate = pg_escape_string($_POST['appdate']);
	
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $qry = pg_query("SELECT date_enter_bank FROM \"Cheques\" WHERE running_no='$rid' AND cheque_no='$cid' ");
    if($res = pg_fetch_array($qry)){
        $date_enter_bank = $res['date_enter_bank'];
    }
    
    $in_qry="UPDATE \"Cheques\" SET is_pass='TRUE', pass_by_user='$_SESSION[ss_iduser]' , pass_date = '$appdate' WHERE running_no='$rid' AND cheque_no='$cid' ";
		
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE Cheques ไม่สำเร็จ $in_qry";
        $status++;
    }
	
	
    /*  concept เดิม  เผื่อไว้ เมื่อเช็ค ผ่าน ทำการ gen เลขที่ใบเสร็จ ขึ้นมาใหม่
    $arr_inv_no = array();
    $qry = pg_query("SELECT inv_no FROM \"VChequeDetail\" WHERE running_no='$rid' AND cheque_no='$cid' ");
    while($res = pg_fetch_array($qry)){
        $arr_inv_no[] = $res['inv_no'];
    }
    
    if(count($arr_inv_no) > 1){
        $arr_inv_no = array_unique($arr_inv_no);
    }
    
    foreach($arr_inv_no AS $inv_no){
        $qry_inv = pg_query("SELECT status FROM \"Invoices\" WHERE inv_no='$inv_no' ");
        if($res_inv = pg_fetch_array($qry_inv)){
            $invoices_status = $res_inv['status'];
            $sub_status_1 = substr($invoices_status, 0, 2);
            $sub_status_2 = substr($invoices_status, 2, 2);
        }
        
        $generate_id=@pg_query("select gen_rec_no('$date_enter_bank',0)");
        $o_receipt_no=@pg_fetch_result($generate_id,0);
        if(empty($o_receipt_no)){
            $txt_error[] = "สร้าง o_receipt ไม่สำเร็จ";
            $status++;
        }

		
        $in_qry="INSERT INTO \"Otherpays\" (o_receipt,o_date,money_way,money_type,o_prndate,user_id) values 
        ('$o_receipt_no','$date_enter_bank','$sub_status_1','$sub_status_2','$nowdate','$_SESSION[ss_iduser]')";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "INSERT Otherpays ไม่สำเร็จ $in_qry";
            $status++;
        } 

        //loop insert detail
        $qry = pg_query("SELECT * FROM \"VChequeDetail\" WHERE running_no='$rid' AND cheque_no='$cid' AND inv_no='$inv_no' ORDER BY service_id ASC ");
        while($res = pg_fetch_array($qry)){
            $service_id = $res['service_id'];
            $cus_amount = $res['cus_amount'];
            $res_id = $res['res_id'];
            
            $GetConstantVar = GetConstantVar($service_id);
            if($GetConstantVar == "CARCA" OR $GetConstantVar == "CARDW"){
                
                $qry_vcar1 = "SELECT po_id FROM \"VStockCars\" WHERE res_id='$res_id' ";
                $qry_vcar = pg_query($qry_vcar1);
                if($res_vcar = pg_fetch_array($qry_vcar)){
                    $vcar_po_id = $res_vcar['po_id'];
                }else{
                    $txt_error[] = "ไม่สามารถตรวจสอบ VStockCars.po_id ได้ !\n$qry_vcar1";
                    $status++;
                }
                
                if(substr($vcar_po_id, 0, 2) == "PO"){ //กรณีมีการออกรถเลย และเป็นรายการ PO ต้อง gen_id มาใช้งาน
                        $generate_id=@pg_query("select generate_id('$date_enter_bank',1,2)");
                        $idno=@pg_fetch_result($generate_id,0);
                        if(empty($idno)){
                            $txt_error[] = "สร้าง generate_id ไม่สำเร็จ";
                            $status++;
                        }
                }else{ //หากไม่ใช่รายการ PO ให้ดึง po_id จาก VStockCars มาใช้งานได้เลย เช่น DS-1111111
                    $idno=$vcar_po_id;
                }

                $in_qry="UPDATE \"Reserves\" SET \"IDNO\"='$idno', receive_date='$date_enter_bank' WHERE res_id='$res_id' ";
                if(!$res=@pg_query($in_qry)){
                    $txt_error[] = "UPDATE Reserves ไม่สำเร็จ $in_qry";
                    $status++;
                } 
            }

            $in_qry="INSERT INTO \"OtherpayDtl\" (o_receipt,inv_no,amount,service_id,status) values ('$o_receipt_no','$inv_no','$cus_amount','$service_id','CQ')";
            if(!$res=@pg_query($in_qry)){
                $txt_error[] = "INSERT OtherpayDtl ไม่สำเร็จ $in_qry";
                $status++;
            }

			//กรณี เมื่อบันทึกเช็คผ่าน แล้ว ทำการ สร้าง receipt_no ใหม่ เข้าไปใน cheque
           /* $in_qry="UPDATE \"ChequeDetails\" SET receipt_no='$o_receipt_no', prn_date='$nowdate' WHERE inv_no='$inv_no' AND running_no='$rid' ";
            if(!$res=@pg_query($in_qry)){
                $txt_error[] = "UPDATE ChequeDetails ไม่สำเร็จ $in_qry";
                $status++;
            }  
        }
    }*/

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
?>