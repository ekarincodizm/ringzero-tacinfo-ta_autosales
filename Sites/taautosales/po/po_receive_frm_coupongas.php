<?php
if($id != "") {

    $qry = pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE auto_id='$id' AND cancel='FALSE' ");
    if($res = pg_fetch_array($qry)){
        $auto_id = $res['auto_id'];
        $po_id = $res['po_id'];
        $unit = $res['unit'];
        $product_id = $res['product_id'];
        $product_name = GetRawMaterialProductName($product_id);
    }
}
?>
	<div>
		<table cellpadding="5" cellspacing="0" border="0" width="100%">
			<tr>
				<td width="20%"><b>PO ID :</b></td><td width="30%"><?php echo $po_id; ?></td>
				<td width="20%"><b>Product Name :</b></td><td width="30%"><?php echo $product_name; ?></td>    
			</tr>
			<tr>
				<td><b>จำนวนชิ้นที่สั่ง :</b></td><td><?php echo $unit; ?></td>
				<td colspan="2"></td>
			</tr>
			<tr>
				<td>ชนิดมูลค่าของคูปอง :</td><td><input type="text" name="txt_type" id="txt_type" style="width:100px"></td>
				<td>เริ่มเล่มที่ :</td><td><input type="text" name="txt_lam" id="txt_lam" style="width:100px"></td>
			</tr>
			<tr>
				<td>จำนวน (ฉบับ) :</td><td><input type="text" name="txt_num" id="txt_num" style="width:100px"></td>
				<td>เริ่มเลขที่ :</td><td><input type="text" name="txt_no" id="txt_no" style="width:100px"></td>
			</tr>
		</table>

		<div style="text-align:right; margin-top:10px">
			<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
		</div>
	</div>
	
<script>
$('#btnSubmit').click(function(){
    $.post('po_receive_api.php',{
        cmd: 'P_CouponGas_Save',
        po_id: '<?php echo $po_id; ?>',
        product_id: '<?php echo $product_id; ?>',
        txt_type: $('#txt_type').val(),
        txt_lam: $('#txt_lam').val(),
        txt_num: $('#txt_num').val(),
        txt_no: $('#txt_no').val()
    },
    function(data){
        if(data.success){
			alert('บันทึกเรียบร้อยแล้ว');
           $('#divdialogadd').remove();
			$('#list_receive').load('po_list_receive.php',{
				condition: '<?php echo $condition; ?>',
				keyword: '<?php echo $keyword; ?>'
			});
        }else{
            alert('ไม่สามารถบันทึกได้ '+ data);
        }
    });
});
</script>