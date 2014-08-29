<?php
include_once("../include/config.php");
include_once("../include/function.php");

$condition = pg_escape_string($_POST["condition"]);
$keyword =  pg_escape_string($_POST["keyword"]);



    $show = "";
    $show .= "<table width=\"100%\">";
	
	if ($condition == "all"){
		$select = "SELECT * FROM v_po_approve order by po_id asc";
	}
	else if($condition == "po_type"){
		$select = "SELECT * FROM v_po_approve WHERE po_type_id = '$keyword' order by po_id  asc";
	}else if($condition == "po_id"){
		$select = "SELECT * FROM v_po_approve WHERE po_id = '$keyword' order by po_id asc";
	}else if($condition == "po_date"){
		$arr_date = explode ( ",", $keyword );
		$select = "SELECT * FROM v_po_approve WHERE po_date between '$arr_date[0]' and '$arr_date[1]' order by po_id asc";
	}else if ($condition == "vender"){
		$select = "SELECT * FROM v_po_approve WHERE cus_id = '$keyword' order by po_id asc ";
	}
	
	$results = pg_query($select);						 
	$num_rows = pg_num_rows($results);
	
	if($num_rows == 0){
		$show .= "<tr><td class=\"no_result\" align=\"center\" ><br>- ไม่พบข้อมูล -</td></tr>";
	}else{
		$show .= "<tr><br>ผลการค้นพบ  $num_rows รายการ<tr>";
		$show .= "<tr bgcolor=\"#D0D0D0\" style=\"font-weight:bold; text-align:center\">
					<td>เลขที่ใบสั่งซื้อ</td>
					<td>วันที่สั่งซื้อ</td>
					<td>ผู้ขาย</td>
					<td>ยอดเงิน</td>
					<td>ภาษีมูลค่าเพิ่ม</td>
					<td>&nbsp;</td>
				 </tr>";

		$j = 0;
		while($res = pg_fetch_array($results)){
			$j++;
			$po_id = $res['po_id'];
			$po_date = $res['po_date'];
			$vender_id = $res['vender_id'];
			$cus_name = $res['cus_name'];
			$amount = $res['amount'];
			$vat = $res['vat'];
		
			$vender_name = GetVender($vender_id);
		
			if($j%2==0){
				$show .= "<tr class=\"odd\">";
			}else{
				$show .= "<tr class=\"even\">";
			}

		  $show .= "<td> $po_id</td>
					<td>$po_date</td>
					<td>$cus_name</td>
					<td align=\"right\">".number_format($amount,2)."</td>
					<td align=\"right\">".number_format($vat,2)."</td>
					<td align=\"center\"><input type=\"button\" name=\"btnShow\" id=\"btnShow\" value=\"แสดงรายการ\" onclick=\"javascript:ShowDetail('$po_id','$condition','$keyword');\"></td>";
		$show .= "</tr>";	
			
		}
	}

    $show .= "</table>";
    echo $show;
	
echo "<script>
function ShowDetail(id,cond,key){
    $('body').append('<div id=\"divdialogadd\"></div>');
    $('#divdialogadd').load('po_approve_api.php?cmd=divapprove&id='+id+'&condition='+cond+'&keyword='+key);
    $('#divdialogadd').dialog({
        title: 'แสดงรายละเอียด : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdialogadd').remove();
        }
    });
}
</script>";
?>