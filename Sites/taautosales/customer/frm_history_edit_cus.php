<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "รายงานการแก้ไขข้อมูลลูกค้า";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
	
	<script>
		function ShowDetail(id)
		{
			$('body').append('<div id="divdialogshow"></div>');
			$('#divdialogshow').load('appv_cus.php?auto_id='+id+'&popupType=viewOnly');
			$('#divdialogshow').dialog({
				title: 'รายละเอียดการแก้ไขข้อมูลลูกค้า',
				resizable: false,
				modal: true,  
				width:900,
				height: 750,
				close: function(ev, ui){
					$('#divdialogshow').remove();
				}
			});
		}
	</script>

</head>
<body>
	<div class="roundedcornr_box" style="width:900px">
		<div class="roundedcornr_top"><div></div></div>
		<div class="roundedcornr_content">

			<?php
			include_once("../include/header_popup.php");
			?>

			<table width="100%" border="0" cellpadding="0" cellspacing="1"  style="font-size:small; background-color:#CCCCCC;">
				<tr style="background-color:#E4EEFC">
					<th style="padding-left:3px;">No.</th>
					<th style="padding-left:3px;">รหัสลูกค้า</th>
					<th style="padding-left:3px;">ชื่อลูกค้า</th>
					<th style="padding-left:3px;">ผู้ทำรายการ</th>
					<th style="padding-left:3px;">วันเวลาที่ทำรายการ</th>
					<th style="padding-left:3px;">ผู้อนุมัติรายการ</th>
					<th style="padding-left:3px;">วันเวลาที่อนุมัติ</th>
					<th style="padding-left:3px;">ผลการอนุมัติ</th>
					<th style="padding-left:3px;">หมายเหตุการอนุมัติ</th>
					<th style="padding-left:3px;">รายละเอียด</th>
				</tr>
				<?php
				$qry_c = pg_query("select * from \"Customers_temp\" order by auto_id DESC ");
				while($res_c = pg_fetch_array($qry_c))
				{
					$n++;
					
					$doer_id = $res_c["doer_id"]; // รหัสพนักงานที่ทำรายการ
					$appv_id = $res_c["appv_id"]; // รหัสพนักงานที่อนุมัติรายการ
					$status_appv = $res_c["status_appv"]; // รหัสผลการอนุมัติ
					
					// หาชื่อพนักงานที่ทำรายการ
					$qry_doerName = pg_query("select \"fullname\" from \"v_users\" where \"id_user\" = '$doer_id' ");
					$doerName = pg_result($qry_doerName,0);
					
					// หาชื่อพนักงานที่อนุมัติรายการ
					$qry_appvName = pg_query("select \"fullname\" from \"v_users\" where \"id_user\" = '$appv_id' ");
					$appvName = pg_result($qry_appvName,0);
					
					// ข้อความผลการอนุมัติ
					if($status_appv == 0)
					{
						$status_appv_text = "<font color=\"FF0000\">ไม่อนุมัติ</font>";
					}
					elseif($status_appv == 1)
					{
						$status_appv_text = "<font color=\"00CC00\">อนุมัติ</font>";
					}
					elseif($status_appv == 9)
					{
						$status_appv_text = "<font color=\"CCCC00\">รออนุมัติ</font>";
					}
					else
					{
						$status_appv_text = "";
					}
				?>
					<tr bgcolor="#FFFFFF" onMouseover="this.bgColor='#FFFF6F'" onMouseout="this.bgColor='#FFFFFF'">
						<td style="padding-left:3px;" align="center"><?php echo $n; ?></td>
						<td style="padding-left:3px;" align="center"><?php echo $res_c["cus_id"]; ?></td>
						<td style="padding-left:3px;" align="left"><?php echo $res_c["pre_name"].$res_c["cus_name"]." ".$res_c["surname"]; ?></td>
						<td style="padding-left:3px;" align="left"><?php echo $doerName; ?></td>
						<td style="padding-left:3px;" align="center"><?php echo $res_c["doer_stamp"]; ?></td>
						<td style="padding-left:3px;" align="left"><?php echo $appvName; ?></td>
						<td style="padding-left:3px;" align="center"><?php echo $res_c["appv_stamp"]; ?></td>
						<td style="padding-left:3px;" align="center"><?php echo $status_appv_text; ?></td>
						<td style="padding-left:3px;" align="left"><?php echo $res_c["remark"]; ?></td>
						<td align="center"><img src="../images/detail.gif" onclick="javascript:ShowDetail('<?php echo $res_c["auto_id"]; ?>')" style="cursor:pointer;"/></td>
					</tr>
				<?php
				}
				?>
			</table>

		</div>
		<div class="roundedcornr_bottom"><div></div></div>
	</div>
</body>
</html>