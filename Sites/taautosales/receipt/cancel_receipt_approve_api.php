<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "divshow"){
?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่</td>
    <td>เลขที่จอง</td>
    <td>วันที่</td>
    <td>ยอดเงิน</td>
    <td>ประเภท</td>
    <td>ผู้ขออนุมัติ</td>
    <td>เหตุผล</td>
    <td>ทำรายการ</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"CancelReceipt\" WHERE approveuser IS NULL ");
while($res = pg_fetch_array($qry)){
    $j++;
    $c_receipt = $res['c_receipt'];
    $res_id = $res['res_id'];
    $c_date = $res['c_date'];
    $c_money = $res['c_money'];
    $return_to = $res['return_to'];
    $postuser = $res['postuser'];
    $c_memo = $res['c_memo'];
    
    $post_name = GetUserName($postuser);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $c_receipt; ?></td>
    <td align="center"><?php echo $res_id; ?></td>
    <td align="center"><?php echo $c_date; ?></td>
    <td align="right"><?php echo number_format($c_money, 2); ?></td>
    <td align="center"><?php echo $return_to; ?></td>
    <td><?php echo $post_name; ?></td>
    <td><?php echo $c_memo; ?></td>
    <td align="center"><input type="button" value="อนุมัติ" name="btnApp" id="btnApp" onclick="javascript:Approve('<?php echo $c_receipt; ?>')"></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=\"7\" align=\"center\">- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<script>
function Approve(id){
    $('body').append('<div id="divdialogconfirm"></div>');
    $("#divdialogconfirm").text('ต้องการยกเลิกใบเสร็จใช่หรือไม่ ?');
    $("#divdialogconfirm").dialog({
        title: 'ยืนยัน',
        resizable: false,
        height:140,
        modal: true,
        buttons:{
            "Approve": function(){
                $.post('cancel_receipt_approve_api.php',{
                    cmd: 'save',
                    id: id
                },
                function(data){
                    if(data.success){
                        $("#divdialogconfirm").remove();
                        alert(data.message);
                        location.reload();
                    }else{
                        alert(data.message);
                    }
                },'json');
            },
            Cancel: function(){
                $( this ).dialog( "close" );
            }
        }
    });
}
</script>
<?php
}

elseif($cmd == "save"){
    $txt_no = $_POST['id'];
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    $stat_ok = 0;
    
    $sub_id = substr($txt_no, 2, 1);
    
    if( $sub_id == "A" ){
        $qry = pg_query("SELECT res_id,amount,vat FROM \"VReceipt\" WHERE r_receipt='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $money = $amount+$vat;
        
            $qry="UPDATE \"Receipts\" SET cancel = 'TRUE' WHERE r_receipt='$txt_no' ";
            if(!$res=@pg_query($qry)){
                $status++;
                $txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
            }
            $qry="UPDATE \"Vats\" SET cancel = 'TRUE' WHERE v_receipt='$txt_no' ";
            if(!$res=@pg_query($qry)){
                $status++;
                $txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
            }
            
            $stat_ok++;
        }
    }elseif( $sub_id == "N" ){ //ใบเสร็จรับเงินชั่วคราว
       $qry = pg_query(" SELECT res_id,chq_amount,cash_amount,(chq_amount+cash_amount)as amount FROM receipt_tmp WHERE receipt_no='$txt_no'  ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
			$money = $res['amount'];
			$chq_amount = $res['chq_amount'];
			$cash_amount = $res['cash_amount'];
			$discount_amount = $res['discount_amount'];
			
			if( !empty($chq_amount) ){ // กรณีชำระเป็น chq
				 $qry_chq = pg_query(" SELECT 
										  chq_detail.receipt_no,
										  chq_detail.inv_no,
										  chq.cancel, 
										  chq.cheque_no, 
										  chq.running_no
										FROM 
										  public.\"ChequeDetails\" chq_detail
										LEFT JOIN public.\"Cheques\" chq on (chq.running_no = chq_detail.running_no AND chq.cheque_no = chq_detail.cheque_no)
										WHERE receipt_no='$txt_no' ");
				
				$arr_cheque_no = array();
				$arr_inv_no = array();
				while($res = pg_fetch_array($qry_chq)){
					$cheque_no = $res['cheque_no'];
					$running_no = $res['running_no'];
					$inv_no = $res['inv_no'];
					$arr_cheque_no[] = $running_no;
					$arr_inv_no[] = $inv_no;
				}
				
				$str_arr_cheque_no = "('" .implode("','",$arr_cheque_no)."')";
				$str_arr_inv_no = "('" .implode("','",$arr_inv_no)."')";
				
			    //ยกเลิก receipt_no ที่มีการชำระด้วย chq
				/*$up_chq_detail=" UPDATE \"ChequeDetails\" SET cancel_status = '1' WHERE receipt_no='$txt_no' "; 
				if(!$res=@pg_query($up_chq_detail)){
					$status++;
					$txt_error[] = "ผิดผลาด TYPE $sub_id : $up_chq_detail";
				}
				$stat_ok++;*/
				
				//update invoices ด้วย
				$up_inv = " UPDATE \"Invoices\" SET status = null , receipt_memo = '' WHERE inv_no in $str_arr_inv_no "; 
				if(!$res=@pg_query($up_inv)){
					$status++;
					$txt_error[] = "ผิดผลาด TYPE $sub_id : $up_inv";
				}
				 $stat_ok++;
				 
				//ยกเลิก cheque_no ใน table "Cheques" ด้วย
				//$up_chq=" UPDATE \"Cheques\" SET cancel = 'TRUE' WHERE cheque_no IN $str_arr_cheque_no "; 
				// ต้อง  ยกเลิก  cheque ตาม  running number
				$up_chq=" UPDATE \"Cheques\" SET cancel = 'TRUE' WHERE running_no IN $str_arr_cheque_no "; 
				
				if(!$res=@pg_query($up_chq)){
					$status++;
					$txt_error[] = "ผิดผลาด TYPE $sub_id : $up_chq";
				}
				$stat_ok++;
				
			}
			
			//ชำระเงินสด
			if( !empty($cash_amount) ){ 
			
				//หา inv_no ในใบเสร็จที่รับชำระ
				$qry_inv_no = pg_query(" SELECT inv_no FROM \"OtherpayDtl\"
										WHERE o_receipt = '$txt_no' ");
				$arr_inv_no = array();
				while($res = pg_fetch_array($qry_inv_no)){
					$inv_no = $res['inv_no'];
					$arr_inv_no[] = $inv_no;
				}
				$str_arr_inv_no = "('" .implode("','",$arr_inv_no)."')";
				
				//update invoices ด้วย
				$up_inv = " UPDATE \"Invoices\" SET status = null ,receipt_memo = '' WHERE inv_no in $str_arr_inv_no "; 
				if(!$res=@pg_query($up_inv)){
					$status++;
					$txt_error[] = "ผิดผลาด TYPE $sub_id : $up_inv";
				}
				 $stat_ok++;
			
				$qry = " UPDATE \"Otherpays\" SET cancel = 'TRUE' WHERE o_receipt = '$txt_no' "; 
				if(!$res=@pg_query($qry)){
					$status++;
					$txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
				}
				 $stat_ok++;
			}
           
		   //ชำระเงิน เป็นส่วนลด
			if( !empty($cash_amount) ){ 
			
				//หา inv_no ในใบเสร็จที่รับชำระ
				$qry_inv_no = pg_query(" SELECT inv_no FROM \"OtherpayDtl\"
										WHERE o_receipt = '$txt_no' ");
				$arr_inv_no = array();
				while($res = pg_fetch_array($qry_inv_no)){
					$inv_no = $res['inv_no'];
					$arr_inv_no[] = $inv_no;
				}
				$str_arr_inv_no = "('" .implode("','",$arr_inv_no)."')";
				
				//update invoices ด้วย
				$up_inv = " UPDATE \"Invoices\" SET status = null ,receipt_memo = '' WHERE inv_no in $str_arr_inv_no "; 
				if(!$res=@pg_query($up_inv)){
					$status++;
					$txt_error[] = "ผิดผลาด TYPE $sub_id : $up_inv";
				}
				 $stat_ok++;
			
				$qry = " UPDATE \"Discountpays\" SET cancel = 'TRUE' WHERE o_receipt = '$txt_no' "; 
				if(!$res=@pg_query($qry)){
					$status++;
					$txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
				}
				 $stat_ok++;
			}
			
		   //cancel ในตารางเก็บการชำระเงิน
			$up_receipt_tmp = "UPDATE receipt_tmp SET status = '0' WHERE receipt_no='$txt_no' ";
            if(!$res=@pg_query($up_receipt_tmp)){
                $status++;
                $txt_error[] = "ผิดผลาด TYPE $sub_id : $up_receipt_tmp";
            }
            $stat_ok++;
        }
    }elseif( $sub_id == "R" ){ //ใบเสร็จรับเงิน
        $qry = pg_query("SELECT res_id,amount,vat FROM v_receipt WHERE r_receipt='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $money = $amount+$vat;
        
            $qry="UPDATE \"Receipts\" SET cancel = 'TRUE' WHERE r_receipt='$txt_no' ";
            if(!$res=@pg_query($qry)){
                $status++;
                $txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
            }
            $stat_ok++;
			
			// คืนค้า ให้สามาถกลับไปตั้ง ออกใบเสร็จ  ใหม่ได้
			$str_replace_v =  str_replace("R", "V", $txt_no);
			$up_inv = "UPDATE \"Invoices\" SET cancel = 'TRUE' WHERE inv_no = '$str_replace_v' ";
            if(!$res=@pg_query($up_inv)){
                $status++;
                $txt_error[] = "ผิดผลาด TYPE $sub_id : $up_inv";
            }
			
			$qry="UPDATE \"Vats\" SET cancel = 'TRUE' WHERE v_receipt='$txt_no' ";
            if(!$res=@pg_query($qry)){
                $status++;
                $txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
            }
			
            $stat_ok++;
        }
    }elseif( $sub_id == "V" ){
        $qry = pg_query("SELECT res_id,amount,vat FROM \"VVat\" WHERE v_receipt='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $res_id = $res['res_id'];
            $amount = $res['amount'];
            $vat = $res['vat'];
            $money = $amount+$vat;
            
            $qry="UPDATE \"Vats\" SET cancel = 'TRUE' WHERE v_receipt='$txt_no' ";
            if(!$res=@pg_query($qry)){
                $status++;
                $txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
            }
            $stat_ok++;
        }
    }elseif( $sub_id == "I" ){
        $qry = pg_query("SELECT r_receipt,o_receipt,res_id,amount,vat FROM \"VAccPayment\" WHERE inv_no='$txt_no' ");
        if($res = pg_fetch_array($qry)){
            $r_receipt = $res['r_receipt'];
            $o_receipt = $res['o_receipt'];  
            $res_id = $res['res_id'];
        
            $qry = pg_query("SELECT SUM(amount) AS money FROM \"InvoiceDetails\" WHERE inv_no='$txt_no' ");
            if($res = pg_fetch_array($qry)){
                $money = $res['money'];
            }
            
            if( (empty($r_receipt) OR $r_receipt = "") AND (empty($o_receipt) OR $o_receipt = "") ){
                $qry="UPDATE \"Invoices\" SET cancel = 'TRUE' WHERE inv_no='$txt_no' ";
                if(!$res=@pg_query($qry)){
                    $status++;
                    $txt_error[] = "ผิดผลาด TYPE $sub_id : $qry";
                }
            }else{
                $status++;
                $txt_error[] = "ไม่สามารถยกเลิกรายการนี้ได้ ต้องทำการยกเลิกใบเสร็จก่อนค่ะ !";
            }
            $stat_ok++;
        }else{
            $status++;
            $txt_error[] = "ไม่พบข้อมูลใน VAccPayment";
        }
    }else{
        $status++;
        $txt_error[] = "กรุณาตรวจสอบ เลขที่สำคัญ";
    }
    
    if($stat_ok == 0){
        $status++;
        $txt_error[] = "ไม่พบ เลขที่สำคัญ ในระบบ !";
    }else{

        $qry="UPDATE \"CancelReceipt\" SET approveuser = '$_SESSION[ss_iduser]' WHERE c_receipt='$txt_no' ";
        if(!$res=@pg_query($qry)){
            $status++;
            $txt_error[] = "ผิดผลาด $qry";
        }

    }
    
    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = $txt_error[0];
    }

    echo json_encode($data);
}
?>