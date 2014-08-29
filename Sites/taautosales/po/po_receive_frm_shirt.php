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
				<td>ขนาดเสื้อ :</td><td>
					<select name="combo_size" id="combo_size">
						<option value="s">S</option>
						<option value="m">M</option>
						<option value="l">L</option>
						<option value="xl">XL</option>
						<option value="xxl">XXL</option>
					</select>
				</td>
				<td>จำนวน :</td><td><input type="text" name="txt_num" id="txt_num" style="width:100px" onkeypress="check_num(event);"></td>
			</tr>
		</table>

		<div style="text-align:right; margin-top:10px">
			<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
		</div>
	</div>
	
<script>
function check_num(e){ 
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			
		}
		else{
			window.event.returnValue = false;
		}
    }
	else
	{
        key = e.which; // Firefox       
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			
		}
		else
		{
			key = e.preventDefault();
		}
	}
}

$('#btnSubmit').click(function(){
    $.post('po_receive_api.php',{
        cmd: 'P_Shirt_Save',
        po_id: '<?php echo $po_id; ?>',
        product_id: '<?php echo $product_id; ?>',
        combo_size: $('#combo_size').val(),
        txt_num: $('#txt_num').val()
    },
    function(data){
        if(data == "success"){
			alert('บันทึกเรียบร้อยแล้ว');
			location.reload();
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