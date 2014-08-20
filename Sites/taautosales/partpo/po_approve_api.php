<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "divapprove"){
    $id = pg_escape_string($_GET['id']);
	$condition = pg_escape_string($_GET['condition']);
	$keyword =  pg_escape_string($_GET['keyword']);

?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Product ID</td>
    <td>Name</td>
    <td>Unit</td>
    <td>Cost</td>
    <td>Vat</td>
</tr>
<?php
$qry = pg_query("SELECT * FROM \"v_po_detail\" WHERE po_id='$id' AND cancel='FALSE' ORDER BY auto_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $product_id = $res['product_id'];
    $product_cost = $res['product_cost'];
    $vat = $res['vat'];
    $unit = $res['unit'];
	$po_remark = $res['po_remark'];
    
    if(substr($product_id, 0, 1)=="P"){
        $product_name = GetProductName($product_id);
    }else{
        $product_name = GetRawMaterialName($product_id);
    }
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $product_id; ?></td>
    <td><?php echo $product_name; ?></td>
    <td align="right"><?php echo $unit; ?></td>
    <td align="right"><?php echo number_format($product_cost,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
</tr>
<?php
}
?>
</table>

<table width="100%">
	<tr>
		<td align="left"><b>หมายเหตุ  :</b><?php echo $po_remark; ?></td>
	</tr>
</table>


<div style="margin-top:10px" align="center"><b>บันทึกการอนุมัติ:</b><br><textarea name="remark" id="remark" cols="30" rows="3"></textarea></div>
<div style="width:300px; margin: 0 auto; padding: 15px 0 0 0">
    <div style="float:left">
<input type="button" name="btnApprove" id="btnApprove" value="อนุมัติ">
    </div>
    <div style="float:right">
<input type="button" name="btnApproveCancel" id="btnApproveCancel" value="ไม่อนุมัติ">
    </div>
</div>

<script>
$('#btnApprove').click(function(){
	var remark = $('#remark').val();
	if(remark == ""){
		alert('กรุณาระบุหมายเหตุ');
		return false;
	}else{
    $.post('po_approve_api.php',{
        cmd: 'approve',
        cancel: 'f',
        id: '<?php echo $id; ?>',
		txtremark: remark,
		condition:'<?php echo $condition; ?>',
		keyword:'<?php echo $keyword; ?>'
    },
    function(data){
        if(data.success){
            alert(data.message);
			$.post('ajax_query_po.php',{
				condition: data.condition,
				keyword: data.keyword
			},
			function(data){
				$('#div_search_result').html(data);
			},'html');
			$('#divdialogadd').remove();
        }else{
            alert(data.message);
        }
    },'json');
	}
});

$('#btnApproveCancel').click(function(){
	var remark = $('#remark').val();
	if(remark == ""){
		alert('กรุณาระบุหมายเหตุ');
		return false;
	}else{
    $.post('po_approve_api.php',{
        cmd: 'approve',
        cancel: 't',
        id: '<?php echo $id; ?>',
		txtremark: remark,
		condition:'<?php echo $condition; ?>',
		keyword:'<?php echo $keyword; ?>'
    },
    function(data){
        if(data.success){
            alert(data.message);
            $.post('ajax_query_po.php',{
				condition: data.condition,
				keyword: data.keyword
			},
			function(data){
				$('#div_search_result').html(data);
			},'html');
			$('#divdialogadd').remove();
        }else{
            alert(data.message);
        }
    },'json');
	}
});
</script>

<?php
}

elseif($cmd == "approve"){
    $id = pg_escape_string($_POST['id']);
    $cancel = pg_escape_string($_POST['cancel']);
    $txtremark = pg_escape_string($_POST['txtremark']);
	$condition = pg_escape_string($_POST["condition"]);
	$keyword =  pg_escape_string($_POST["keyword"]);
	
    if($cancel == 'f'){
        $in_qry="UPDATE \"PurchaseOrders\" SET approve='TRUE',approve_by='$_SESSION[ss_iduser]',approve_date='$nowdate',approved_remark='$txtremark' WHERE po_id='$id' ";
    }else{
        $in_qry="UPDATE \"PurchaseOrders\" SET approve='TRUE',approve_by='$_SESSION[ss_iduser]',approve_date='$nowdate',cancel='TRUE',approved_remark='$txtremark' WHERE po_id='$id' ";
    }

    if($result=@pg_query($in_qry)){
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
		$data['condition'] = $condition;
		$data['keyword'] = $keyword;
    }else{
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $in_qry";
    }
    echo json_encode($data);
}
?>