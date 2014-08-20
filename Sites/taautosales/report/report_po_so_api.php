<?php
include_once("../include/config.php");
include_once("../include/function.php");

//$condition = $_REQUEST['condition'];
//$keyword = $_POST['keyword'];
//echo $keyword;
//========== แสดงทั้งหมด ============//
	//$qry = pg_query(" SELECT car_id,car_name,mar_num,car_num,license_plate  FROM \"Cars\" ");	
	/*$qry = pg_query("SELECT 
					  po.po_date,
					  po.vender_id,					  
					  c.license_plate,
					  c.car_name,
					  c.mar_num, 
					  c.car_num, 
					  c.car_id, 
					  po_detail.product_id
					FROM 
					  public.\"PurchaseOrders\" po
					LEFT JOIN public.\"PurchaseOrderDetails\" po_detail on po.po_id::text = po_detail.po_id::text
					LEFT JOIN public.\"Products\" proc on po_detail.product_id::text = proc.product_id::text
					LEFT JOIN public.\"Cars\" c on  proc.product_id::text = c.product_id::text 
					ORDER BY po_date DESC");*/
					
	/*
	SELECT 
	  po.po_date, 
	  po.vender_id, 
	  po_detail.po_id, 
	  po_detail.product_id, 
	  po.po_type_id
	FROM 
	  public."PurchaseOrderDetails" po_detail
	LEFT JOIN "PurchaseOrders" po ON po_detail.po_id = po.po_id
	LEFT JOIN car_history car_hist ON car_hist.po_id = po.po_id
*/	
					
					
					
	$qry = pg_query("SELECT 
					  \"Cars\".car_name, 
					  \"Cars\".license_plate, 
					  \"Cars\".mar_num, 
					  \"Cars\".car_num,
					  \"PurchaseOrders\".po_id,					  
					  \"PurchaseOrders\".po_date, 
					  \"Cars\".car_status, 
					  \"Reserves\".finance_date,
					  car_history.auto_id,
					  car_history.car_idno, 
					  car_history.idno
					FROM 
					  public.\"PurchaseOrders\", 
					  public.\"PurchaseOrderDetails\", 
					  public.car_history, 
					  public.\"Cars\", 
					  public.\"Reserves\"
					WHERE 
					  \"PurchaseOrders\".po_id = \"PurchaseOrderDetails\".po_id AND
					  car_history.po_id = \"PurchaseOrders\".po_id AND
					  car_history.car_idno = \"Cars\".car_idno AND
					  car_history.idno = \"Reserves\".\"IDNO\"
					  AND \"Reserves\".finance_date IS NOT NULL ");

?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>แบบรถ</td>
    <td>เลขเครื่อง</td>
    <td>เลขตัวถัง</td>
    <td>ทะเบียนรถ</td>
    <td>วันที่ซื้อ</td>
	<td>วันที่ขาย</td>
	<td></td>
	
   <!--<td>เลขที่รับสินค้า</td>
    <td>ซื้อจาก</td>
    <td>เลขที่สัญญาขาย</td>
	<td>ขายให้</td>-->
</tr>
<?php
$j = 0;
while($res = pg_fetch_array($qry)){
    $j++;
	//$car_id = $res['car_id'];
	//$buy_from = get_vender_name($res['vender_id']);
	
	/*$qry_reserve = pg_query(" SELECT finance_date,\"IDNO\" as idno  FROM  \"Reserves\" 
							  WHERE car_id = '$car_id' 
							  AND finance_date IS NOT NULL ");*/
	/*while($res_reserve = pg_fetch_array($qry_reserve)){
		$finance_date = $res_reserve['finance_date'];
		$idno = $res_reserve['idno'];
	}*/
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    
    <td><?php echo $res['car_name']; ?></td>
    <td><?php echo $res['mar_num']; ?></td>
    <td><?php echo $res['car_num']; ?></td>
    <td><?php echo $res['license_plate']; ?></td>
    <td><?php echo $res['po_date']; ?></td>
	<td><?php echo $res['finance_date']; ?></td>
	<td align="center"><img src="../images/detail.gif" border="0" width="15" height="15" title="ดูรายละเอียด" style="cursor:pointer" onclick="javascript:show_detail('<?php echo $res['po_id']; ?>','<?php echo $res['idno']; ?>')" /></td>
	
   <!-- <td></td>
    <td><?php //echo $buy_from; ?></td>
	
	<td><?php //echo $idno; ?></td>
	<td></td>-->
</tr>
<?php
	}
?>
</table>


<script>

function show_detail(po_id,idno){
    $('body').append('<div id="div_detail"></div>');
    $('#div_detail').load('report_po_so_detail.php?po_id='+po_id+'&idno='+idno);
    $('#div_detail').dialog({
        title: 'รายละเอียดการซื้อขาย :  เลขที่ใบสั่งซื้อ = '+po_id +',  เลขที่การขาย = '+idno,
        resizable: false,
        modal: true,  
        width: 650,
        height: 450,
        close: function(ev, ui){
            $('#div_detail').remove();
        }
    });
}

</script>
