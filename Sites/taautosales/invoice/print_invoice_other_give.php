<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}
unset( $_SESSION["details_give_data"] );
$page_title = "ของแถม";
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
	
</head>
<body>

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top">
		<div></div>
   </div>
      <div class="roundedcornr_content">

		<?php
		include_once("../include/header_popup.php");

		$resid = $_GET['resid'];
		$btn_back = $_GET['back'];

		if( empty($resid) ){
			echo "invalid param.";
			exit;
		}
		?>
		
	<!--<div id="div_gif" style="margin: 10px 0 5px 0">-->
			<div style="margin: 10px 0 5px 0">
				<?php
				$qry=pg_query("select \"IDNO\",cus_id from \"Reserves\" WHERE \"res_id\" = '$resid' ");
				if($res=pg_fetch_array($qry)){
					$IDNO = $res["IDNO"];
					$cus_id = $res["cus_id"];

					if(empty($IDNO)){
						$qry=pg_query("select * from \"VContract\" WHERE \"IDNO\" is null AND res_id='$resid' AND cus_id='$cus_id' ");
					}else{
						$qry=pg_query("select * from \"VContract\" WHERE \"IDNO\" = '$IDNO' AND res_id='$resid' AND cus_id='$cus_id' ");
					}
					if($res=pg_fetch_array($qry)){
						$IDNO = $res["IDNO"];
						$car_id = $res["car_id"];
						$pre_name = $res["pre_name"];
						$cus_name = $res["cus_name"];
						$surname = $res["surname"];
						$car_num = $res["car_num"];
						$mar_num = $res["mar_num"];
						$license_plate = $res["license_plate"];
						$color = $res["color"];
						$name = $res["name"];
						
						//********************************************** ไม่ต้องเช็คว่ายังไม่ได้เลือกรถ *************************************************//
					   /* if(empty($car_id)){
							echo "รายการจองนี้ ยังไม่ได้เลือกรถ !";
							exit;
						}*/
						
						echo "<b>ชื่อลูกค้า :</b> $pre_name $cus_name $surname<br><b>รายละเอียดรถ :</b> เลขถัง $car_num เลขเครื่อง $mar_num ทะเบียนรถ $license_plate สีรถ $color ยี่ห้อ/รุ่น $name";
					}else{
					  //  echo "not query ! select * from \"VContract\" WHERE \"IDNO\" = '$IDNO' AND res_id='$resid' AND cus_id='$cus_id'";
					}
				}else{
					echo "IDNO not found.";
					exit;
				}
				?>
			</div>
			<br>
			<div style="margin-top:10px; font-size:13px; font-weight:bold">รายการของแถมที่ได้เลือกไว้</div>
			<div id ="div_show_gif" style="margin-top:2px"></div>
			<br>
			<div style="margin-top:10px; font-size:13px; font-weight:bold">เลือกรายการของแถม</div>
					  
			<div style="margin-top:2px">
				<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
					<tr style="font-weight:bold; text-align:center" bgcolor="#D0D0D0">
						<td width="10%">เลือก</td>
						<td width="75%">รายการ</td>
						<td width="15%">จำนวน</td>
					</tr>
					<?php
					$j = 0;
					$qry=pg_query("SELECT * FROM \"Products\" WHERE (link_table <> 'Cars' OR link_table IS NULL) AND cancel='FALSE' ORDER BY name ASC");
					while($res=pg_fetch_array($qry)){
						$j++;
						$product_id = $res["product_id"];
						$name = $res["name"];
						$cost_price = $res["cost_price"];
						$sale_price = $res["sale_price"];
					?>
					<tr bgcolor="#FFFFFF">
					   <!-- <td align="center"><input type="checkbox" name="chk_box" id="chk_box<?php //echo $j; ?>" value="<?php// echo "$product_id"; ?>" onchange="javascript:ChkBoxSelect(<?php //echo $j; ?>)"></td> -->
						<td align="center"><input type="checkbox" name="chk_box" id="chk_box<?php echo $j; ?>" value="<?php echo "$product_id"; ?>"></td>
						<td><?php echo $name; ?> <span id="div_details<?php echo $j; ?>"></span></td>
						<td><input type="text" name="txt_unit<?php echo $j; ?>" id="txt_unit<?php echo $j; ?>" style="width:95%; text-align:right" value="1"></td>
					</tr>
					<?php
					}
					?>
				</table>
			</div>

			<div class="linedotted"></div>
			<div style="float:left; margin-top:10px">
				<?php
				if($_GET['back'] == "1"){
				?>
					<input type="button" name="btnBack" id="btnBack" value="กลับ" onclick="window.location='print_invoice_other_give_select.php' ">
				<?php
				}
				?>
			</div>

			<div style="float:right; margin-top:10px">
				<input type="button" name="btnSubmit" id="btnSubmit" value="บันทึก">
			</div>
			
			<div style="clear:both"></div>
		<!--</div> -->
		
	   <div class="roundedcornr_bottom">
			<div></div> 
	   </div>
	</div>
	
</div>



<script>
$(document).ready(function(){
	$('#div_show_gif').empty();
	$('#div_show_gif').load('../gif/list_gif.php?res_id=<?php echo $resid;?>');
});	
function ChkBoxSelect(id){
    if( $('input[id=chk_box'+ id +']:checked').val() ){
        var pid = $('#chk_box'+id).val();
        $('#div_details'+id).empty();
        $('#div_details'+id).html(" <img src=\"../images/edit.png\" border=\"0\" width=\"16\" height=\"16\" onclick=\"javascript:ShowDialogDetail("+id+",'"+pid+"')\" style=\"cursor: pointer\">");
        ShowDialogDetail(id,pid);
    }else{
        $('#div_details'+id).empty();
        $.get('print_invoice_other_give_api.php?cmd=unset_session&id='+id,function(data){
            console.log(data);
        });
    }
}

function ShowDialogDetail(id,product){
    var unit = $('#txt_unit'+id).val();
    $('body').append('<div id="div_details_dialog"></div>');
    $('#div_details_dialog').load('print_invoice_other_give_api.php?cmd=div_dialog_details&id='+id+'&product='+product+'&unit='+unit);
    $('#div_details_dialog').dialog({
        title: 'รายละเอียด : '+product,
        resizable: false,
        modal: true,  
        width: 600,
        height: 400,
        close: function(ev, ui){
            $('#div_details_dialog').remove();
        }
    });
}
    
$('#btnSubmit').click(function(){
    var j = 0;
    var arr_chk_box = [];
    for (var i=1; i <= <?php echo $j; ?>; i++){
        if( $('input[id=chk_box'+ i +']:checked').val() ){
            j++;
            var product = $('#chk_box'+i).val();
            var unit = $('#txt_unit'+i).val();
            arr_chk_box[j] = { id:i, product:product, unit:unit };
        }
    }
    
    if(j == 0){
        alert('กรุณาเลือกรายการ !');
        return false;
    }

    $.post('print_invoice_other_give_api.php',{
        cmd: 'save',
        res_id: '<?php echo $resid; ?>',
        license_plate: '<?php echo $license_plate; ?>',
        arradd: JSON.stringify(arr_chk_box)
    },
    function(data){
        if(data.success){
			alert(data.message);
			//location.reload();
			window.location = "print_invoice_other_give.php?resid=<?php echo $resid; ?>";
            //window.location="print_invoice_other_give_select.php";
        }else{
            alert(data.message);
        }
    },'json');
});
</script>

</body>
</html>