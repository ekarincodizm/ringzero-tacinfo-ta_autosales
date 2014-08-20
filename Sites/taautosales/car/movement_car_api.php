<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = pg_escape_string($_REQUEST['cmd']);
$iduser = pg_escape_string($_SESSION["ss_iduser"]);
$nowDateTime = nowDateTime();

if($cmd == "content"){
	$condition = pg_escape_string($_GET['condition']);
	$keyword  = pg_escape_string($_GET['keyword']);
	
	if($condition == "all"){
		$keyword = "";
	}else if($condition == "car_type"){
		$keyword = "and car_type_id = '$keyword'";
	}else if($condition == "car_idno"){
		$keyword = "and car_idno like '%$keyword%'";
	}else if($condition == "license_plate"){
		$keyword = "and license_plate like '%$keyword%'";
	}
	
?>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Car ID</td>
    <td>เลขถัง</td>
	<td>ทะเบียนสต๊อก</td>
    <td>ทะเบียน</td>
    <td>Product ID</td>
	<td>เลขจอง</td>

</tr>

<?php
$j = 0;
//$qry = pg_query("SELECT * FROM \"VCarMovement\" WHERE car_status<>'S' and wh_id not in (0,95,96,97,100) and date_out IS NULL $keyword ORDER BY car_id ASC ");
//แสดงเฉพาะ รายการล่าสุด
$qry = pg_query("SELECT * FROM \"VCarMovement\" WHERE auto_id in (select max(auto_id) from \"VCarMovement\" group by car_id) and car_status<>'S' and wh_group <> '0' and date_out IS NULL $keyword ORDER BY car_id ASC ");
//echo  "SELECT * FROM \"VCarMovement\" WHERE auto_id in (select max(auto_id) from \"VCarMovement\" group by car_id) and car_status<>'S' and wh_group <> '0' and date_out IS NULL $keyword ORDER BY car_id ASC " ;
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $car_num = $res['car_num'];
	$car_idno = $res['car_idno'];
    $license_plate = $res['license_plate'];
    $product_id = $res['product_id'];
    $color = $res['color'];
	
    $product_name = GetProductName($product_id);
    
	$qry_r = pg_query("SELECT * FROM \"V_CarsReserve\" WHERE \"car_id\"='$car_id'");
    if($res_r = pg_fetch_array($qry_r)){
        $res_id = $res_r['res_id'];
		$reserve_status = $res_r['reserve_status'];
    }
	$num = pg_num_rows($qry_r);
	
	if($num>1){
		$txt_res_id = "<span id=\"listR\" onclick=\"javascript:show_car_reserve('$car_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>จอง > 1</u></font></span>";
	}else if($num==1){
		if($res_id == "" or ($res_id !="" and $reserve_status == '0')){
			$txt_res_id = ""; //ยังไม่มีการจอง
		}else{
			$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
		}
	}
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>')"><u><?php echo $car_id; ?></u></a></td>
    <td><?php echo $car_num; ?></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $license_plate; ?></td>
    <td><?php echo $product_name; ?></td>
	<td><?php echo $txt_res_id; ?></td>

</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>
<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
function show_car_reserve(car_id){
	popU('list_car_reserve.php?car_id='+car_id,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
}
function ShowDetailres(id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+id);
		$('#divdetail').dialog({
			title: 'รายละเอียดการจอง : '+id,
			resizable: false,
			modal: true,  
			width: 800,
			height: 450,
			close: function(ev, ui){
				$('#divdetail').remove();
			}
		});
}
</script>
<?php
}

elseif($cmd == "divshow"){
    $car_id = pg_escape_string($_GET['id']);
    $btn = pg_escape_string($_GET['btn']);
    
    $qry = pg_query("SELECT * FROM \"V_CarsReserve\" WHERE \"car_id\"='$car_id'");
    if($res = pg_fetch_array($qry)){
        $res_id = $res['res_id'];
        $license_plate = $res['license_plate'];
        $product_id = $res['product_id'];
        $product_name = $res['name'];
		$reserve_status = $res['reserve_status'];
    }
	$num = pg_num_rows($qry );
	
	if($num>1){
		$txt_res_id = "<span id=\"listR\" onclick=\"javascript:show_car_reserve('$car_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>มากกว่า 1 รายการ</u></font></span>";
	}else if($num==1){
		if($res_id == "" or ($res_id !="" and $reserve_status == '1')){
			$txt_res_id = "ยังไม่มีการจอง";
		}else{
			$txt_res_id = "<span id=\"R_id\" onclick=\"ShowDetailres('$res_id');\" style=\"cursor:pointer;\"><font color=\"blue\"><u>".$res_id."</u></font></span>";
		}
	}
		
	
?>

<div style="float:left; margin:5px 0 5px 0">
<b>Car ID :</b> <?php echo $car_id; ?><br />
<b>Product ID :</b> <?php echo "$product_id : $product_name"; ?><br />
</div>
<div style="float:right; margin:5px 0 5px 0">
<b>Res ID:</b> <?php echo $txt_res_id; ?><br />
<b>ทะเบียน :</b> <?php echo $license_plate; ?>
</div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#FFFFFF">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Date In</td>
    <td>WH ID</td>
    <td>Color</td>
    <td>Date Out</td>
    <td>Target Go</td>
	<td>Owner</td>
</tr>
<?php
$qry_max = pg_query("SELECT auto_id,office_id,wh_id FROM \"VCarMovement\" where auto_id in (select max(auto_id) from \"VCarMovement\" WHERE car_id='$car_id'  ) ");
$last_auto_id = pg_fetch_result($qry_max ,0);
$last_office_id = pg_fetch_result($qry_max ,1);
$last_wh_id = pg_fetch_result($qry_max ,2);

$qry_mv = pg_query("SELECT * FROM \"VCarMovement\" WHERE car_id='$car_id' ORDER BY auto_id DESC ");
while($res_mv = pg_fetch_array($qry_mv)){
    $auto_id = $res_mv['auto_id'];
    $date_in = $res_mv['date_in'];
    $wh_id = $res_mv['wh_id'];
    $color = $res_mv['color'];
    $date_out = $res_mv['date_out'];
    $target_go = $res_mv['target_go'];
    $office_id = $res_mv['office_id'];
	$car_owner = $res_mv['car_owner'];
    $wh_name = GetWarehousesName($wh_id);
    $target_go_name = GetWarehousesName($target_go);
	
	$qry_color = pg_query("select color_name from \"CarColor\" where color_id = '$color' ");
	$txt_color = pg_fetch_result($qry_color,0);
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $date_in; ?></td>
    <td><?php echo $wh_name; ?></td>
    <td><?php echo $txt_color; ?></td>
    <td><?php echo $date_out; ?></td>
    <td><?php echo $target_go_name; ?></td>
	<td><?php echo $car_owner; ?></td>
</tr>
<?php
}
?>
</table>

<div class="linedotted"></div>

<div style="text-align:right; margin-top:5px">
<?php

if($btn != 1){
    if( $last_office_id == $_SESSION["ss_office_id"] ){
        echo "<input type=\"button\" name=\"btnOut\" id=\"btnOut\" value=\"บันทึกออก\" onclick=\"javascript:Save('$last_auto_id','out','ออก')\">";
    }else{
        if( $last_office_id == 0 ){
            echo "<input type=\"button\" name=\"btnIn\" id=\"btnIn\" value=\"บันทึกเข้า\" onclick=\"javascript:Save('$last_auto_id','in','เข้า')\">";
        }else{
            $last_office_name = GetWarehousesByOfficeID($last_office_id);
            echo "ไม่สามารถบันทึกออกได้ ต้องให้สาขา <b>$last_office_name</b> ทำรายการบันทึกออก";
        }
    }

    /*
    $qry = pg_query("SELECT in_office FROM \"Warehouses\" WHERE wh_id='$last_wh_id' AND cancel='FALSE' ");
    if($res = pg_fetch_array($qry)){
        $in_office=$res['in_office'];
    }
    
    if($in_office == 'f'){
    ?>
    <input type="button" name="btnIn" id="btnIn" value="บันทึกเข้า" onclick="javascript:Save('<?php echo $last_auto_id; ?>','in','เข้า')">
    <?php
    }else{
        if($last_wh_id == $_SESSION["ss_office_id"]){
        ?>
        <input type="button" name="btnOut" id="btnOut" value="บันทึกออก" onclick="javascript:Save('<?php echo $last_auto_id; ?>','out','ออก')">
        <?php
        }else{
            $last_wh_name = GetWarehousesName($last_wh_id);
            echo "ไม่สามารถบันทึกออกได้ ต้องให้สาขา $last_wh_name ทำรายการบันทึกออก";
        }
    }*/
}
?>
</div>

<script>
function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
function show_car_reserve(car_id){
	popU('list_car_reserve.php?car_id='+car_id,'','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=980,height=550');
}
function Save(id,type,typethai){
    $('body').append('<div id="divdialog"></div>');
    $('#divdialog').load('movement_car_api.php?cmd=divconfirm&id='+id+'&type='+type+'&cid=<?php echo $car_id; ?>');
    $('#divdialog').dialog({
        title: 'บันทึกรถ'+typethai,
        resizable: false,
        modal: true,  
        width: 480,
        height: 235,
        close: function(ev, ui){
            $('#divdialog').remove();
        }
    });
}
function ShowDetailres(id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+id);
		$('#divdetail').dialog({
			title: 'รายละเอียดการจอง : '+id,
			resizable: false,
			modal: true,  
			width: 800,
			height: 450,
			close: function(ev, ui){
				$('#divdetail').remove();
			}
		});
}
</script>

<?php
}

elseif($cmd == "divconfirm"){
    $cid = pg_escape_string($_GET['cid']);
    $id = pg_escape_string($_GET['id']);
    $type = pg_escape_string($_GET['type']);
   
    $qry = pg_query("SELECT * FROM \"Cars\" WHERE \"car_id\"='$cid' ");
    if($res = pg_fetch_array($qry)){
        $license_plate = $res['license_plate'];
    }
    if($type == "out"){
        $qry_cr = pg_query("SELECT po_id FROM \"Cars\" WHERE car_id = '$cid' ");
        if($res_cr = pg_fetch_array($qry_cr)){
            $po_id= $res_cr['po_id'];
            $sub_po_id = substr($po_id, 0, 2);
        }else{
            echo "ผิดผลาด ไม่สามารถดึงข้อมูล Cars ได้";
            exit;
        }
?>
<div>
	<table cellpadding="5" cellspacing="0" border="0" width="100%">
		<tr>
			<td width="45"><b>ไปที่ :</b></td>
			<td>
				<select name="cb_wh" id="cb_wh" onchange="javascript:changeCb_wh()">
				<?php
				$qry_wh = pg_query("SELECT * FROM \"Warehouses\" WHERE cancel='FALSE' ORDER BY wh_name ASC ");
				while($res_wh = pg_fetch_array($qry_wh))
				{
					$wh_id = $res_wh['wh_id'];
					$wh_name = $res_wh['wh_name'];
					$wh_group = $res_wh['wh_group'];
					
					echo "<option value=\"$wh_id\">$wh_name</option>";
				}
				?>
				</select>
				
				<input type="hidden" name="wh_group" id="wh_group" value=""/>
			</td>
			<td>	
				<div id="attendant_panel" ><span><b>ผู้ดูแล :</b></span>  <input type="text" name="attendant" id="attendant"></div>
			</td>
		</tr>
		<tr>
			<td colspan = "3">
				<div id="deliver" style="display:none">
					<select name="res_id_choose" id="res_id_choose">
						<option value="">--เลือกเลขที่ใบจอง--</option>
				<?php
					$qry_res = pg_query("SELECT * FROM \"Reserves\" WHERE car_id = '$cid' and reserve_status not in ('1','0')");
					while($res = pg_fetch_array($qry_res)){
						$res_id = $res['res_id'];
						$reserve_date = $res['reserve_date'];
						$receive_date = $res['receive_date'];
						$cus_id = $res['cus_id'];
						$cus_name = GetCusName($cus_id);
			   
						echo "<option value=\"$res_id\">$res_id</option>";
					}
				?>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan = "3">
				<div id="divGas" style="display:none">
					<b>ประเภทติดตั้งแก๊ส  : </b>
					<select name="GasType" id="GasType">
						<option value="">--เลือกประเภทติดตั้งแก๊ส--</option>
						<?php
						$qry_res = pg_query("SELECT * FROM \"GasType\" ");
						while($res = pg_fetch_array($qry_res))
						{
							$GasTypeID = $res['GasTypeID'];
							$GasTypeName = $res['GasTypeName'];
							
							echo "<option value=\"$GasTypeID\">$GasTypeName</option>";
						}
						?>
					</select>
				</div>
			</td>
		</tr>
	</table>
</div >

<div style="float:left">
<b>หมายเหตุ</b><br />
<textarea name="area_remark_new" id="area_remark_new" rows="4" cols="50"></textarea>
</div>
	
<div style="margin-top:50px;"align="center">
	<input type="button" name="btnSaveOut" id="btnSaveOut" value="บันทึก" onclick="javascript:SaveOutCars('<?php echo $sub_po_id; ?>')">
<div>
<script>
function ClosePopup(){
    $('#divdialog').remove();
}
function changeCb_wh()
{
	var cb_wh_id = $('#cb_wh').val();
	if(cb_wh_id == '0'){
		//$('#attendant_panel').hide();
		//$('#attendant').val('');
		$('#deliver').show();
	}else{
		//$('#attendant_panel').show();
		$('#deliver').hide();
		$('#res_id_choose').val('');
	}
	
	// ตรวจสอบว่าเป็นประเภท Gas หรือไม่
	$.post('check_GasType_from_Warehouses.php',{
		wh_id : document.getElementById("cb_wh").value
	},
	function(data){
		document.getElementById("wh_group").value = data;
		
		if(data == 3)
		{
			$('#divGas').show();
		}
		else
		{
			$('#divGas').hide();
			document.getElementById("GasType").value = '';
		}
	});
}
function SaveOutCars(t){

	var car_owner = $('#attendant').val();
	if(car_owner == ""){
		alert('กรุณาระบุผู้ดูแลด้วย !');
		return false;
	}
	
	if($('#wh_group').val() == '3' && $('#GasType').val() == '')
	{
		alert('กรุณาระ ประเภทติดตั้งแก๊ส ด้วย !');
		return false;
	}
	
    if( $('#cb_wh').val() == "0" ){
 
		if(t == "PO"){
		
            var chk_res_id = $('#res_id_choose').val();
            var chk_license_plate = "<?php echo $license_plate; ?>";
			
            if( chk_res_id == "" ){
                alert('กรุณาระบุหมายเลขจองที่ต้องการส่งมอบ !');
                return false;
            }
			$.get('movement_car_api.php?cmd=check_wait_appv&car_id=<?php echo $cid; ?>', function(data){
				if(data == 'f'){
					alert('รถคันนี้กำลังรออนุมัติส่งมอบอยู่ กรุณาทำรายการใหม่คราวหลัง !');
					return false;
				} else {
					
					if(chk_license_plate == ""){
						$('body').append('<div id="divdialogconfirmcar_plate"></div>');
						$("#divdialogconfirmcar_plate").html('รถคันนี้ยังไม่มีการบันทึกป้ายแดง!<br>- ต้องการยืนยันว่าต้องการติดป้ายแดง กด <b>YES</b><br>- ออกรถโดยไม่ต้องการติดป้ายแดง กด <b>NO</b>');
						$("#divdialogconfirmcar_plate").dialog({
							title: 'ยืนยัน',
							resizable: false,
							height:180,
							modal: true,
							buttons:{
								"YES": function(){
									ShowCarPlate('<?php echo $license_plate;?>','<?php echo $cid;?>');
									$( this ).dialog( "close" );
									$('#divdialog').remove();
								},
								"NO": function(){
									SaveOutCars2(t);
									$( this ).dialog( "close" );
								}
							}
						});
					}else{
						SaveOutCars2(t);
					}
				}
			});
		}
    }else{//else cb_wh 0
        SaveOutCars2(t);
    }
}//end function

function SaveOutCars2(t){
		$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกใช่หรือไม่?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
					$.post('movement_car_api.php',{
						cmd: 'save',
						type: 'out',
						id: '<?php echo $id; ?>',
						cb_wh : $('#cb_wh').val(),
						car_owner : $('#attendant').val(),
						res_id : $('#res_id_choose').val(),
						txt_area_remark_new: $('#area_remark_new').val(),
						wh_group: document.getElementById("wh_group").value,
						GasType: document.getElementById("GasType").value
					},
					function(data){
						if(data.success){
							//alert(data.message);
							$('#dev_show_content').load('movement_car_api.php?cmd=content');
							$("#divdialogconfirm").remove();
							$('#divdialogadd').remove();
							
							if(data.wh_group == '3') // ถ้าเป็นการติดตั้งแก๊ส
							{
								$('#divdialog').html("<div style=\"text-align:center\">"+data.message+"<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบส่งรถ\" onclick=\"javascript:popU('../report/car_out.php?id=<?php echo $id; ?>','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=800,height=600')\"> <input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบสั่งติดตั้งแก๊ส\" onclick=\"javascript:popU('../report/car_out_gas.php?id=<?php echo $id; ?>','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=800,height=600')\"></div>");
							}
							else
							{
								$('#divdialog').html("<div style=\"text-align:center\">"+data.message+"<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบส่งรถ\" onclick=\"javascript:ClosePopup(); javascript:popU('../report/car_out.php?id=<?php echo $id; ?>','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=800,height=600')\"></div>");
							}
						}else{
							alert(data.message);
						}
					},'json');
				},
				ไม่ใช่: function(){
					$( this ).dialog( "close" );
				}
			}
		});
}

function ShowCarPlate(id,cid){
    $('body').append('<div id="DivCarPlate" style="margin:0; font-size:12px"></div>');
    $('#DivCarPlate').load('../drawn/withdrawal_carplate_api.php?cmd=divshow&license_plate='+id+'&carmove_p=t&car_id='+cid);
    $('#DivCarPlate').dialog({
        title: 'เบิกป้ายเหล็ก',
        resizable: false,
        modal: true,  
        width: 800,
        height: 350,
        close: function(ev, ui){
            $('#DivCarPlate').remove();
        }
    });
}
</script>

<?php
    }
	else // ถ้าเป็นบันทึกเข้า
	{
		$qry = pg_query("SELECT \"color\", \"wh_id\" FROM \"CarMove\" WHERE \"auto_id\"='$id' ");
		if($res = pg_fetch_array($qry)){
			$color = $res['color'];
			$wh_id = $res['wh_id'];
		}
		
		// ตรวจสอบ Warehouses ว่าเป็นกลุ่มคลังใด
		$qry_wh_group = pg_query("select \"wh_group\" from \"Warehouses\" where \"wh_id\" = '$wh_id' ");
		$wh_group = pg_fetch_result($qry_wh_group,0);
		
		if($wh_group == "3") // ถ้าเป็น คลังติดแกีส ภายนอกบริษัท
		{
?>
			<table cellpadding="5" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="200"><b>วันที่ออกใบแจ้งหนี้/ใบส่งของ :</b></td>
					<td><input type="text" id="gasInvoiceDate" name="gasInvoiceDate" size="15" style="text-align:center"></td>
				</tr>
				<tr>
					<td><b>เลขที่ใบแจ้งหนี้/ใบส่งของ :</b></td>
					<td><input type="text" name="gasInvoiceNo" id="gasInvoiceNo"></td>
				</tr>
				<tr>
					<td><b>ผู้ดูแล :</b></td>
					<td><input type="text" name="attendant" id="attendant"></td>
				</tr>
				<tr>
					<td><b>หมายเหตุ :</b></td>
					<td><textarea name="area_remark_new" id="area_remark_new" rows="2" cols="30"></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><input type="button" name="btnSaveInFromGas" id="btnSaveInFromGas" value="บันทึก"></td>
				</tr>
			</table>

			<script>
				$(document).ready(function(){
					$("#gasInvoiceDate").datepicker({
						showOn: 'button',
						buttonImage: '../images/calendar.gif',
						buttonImageOnly: true,
						changeMonth: true,
						changeYear: true,
						dateFormat: 'yy-mm-dd'
					});
				});
	
				$('#btnSaveInFromGas').click(function(){
				
					var car_owner = $('#gasInvoiceDate').val();
					if(car_owner == ""){
						alert('กรุณาระบุ วันที่ออกใบแจ้งหนี้/ใบส่งของ ด้วย !');
						return false;
					}
					
					var cb_color_txt = $('#gasInvoiceNo').val();
					if(cb_color_txt == ""){
						alert('กรุณาระบุ เลขที่ใบแจ้งหนี้/ใบส่งของ ด้วย !');
						return false;
					}
					
					var car_owner = $('#attendant').val();
					if(car_owner == ""){
						alert('กรุณาระบุ ผู้ดูแล ด้วย !');
						return false;
					}
						
					$('body').append('<div id="divdialogconfirm"></div>');
						$("#divdialogconfirm").text('ต้องการบันทึกใช่หรือไม่?');
						$("#divdialogconfirm").dialog({
							title: 'ยืนยัน',
							resizable: false,
							height:140,
							modal: true,
							buttons:{
								"ใช่": function(){
									$.post('movement_car_api.php',{
									cmd: 'save',
									type: 'inFromGas',
									id: '<?php echo $id; ?>',
									gasInvoiceDate : $('#gasInvoiceDate').val(),
									gasInvoiceNo : $('#gasInvoiceNo').val(),
									car_owner : $('#attendant').val(),
									txt_area_remark_new: $('#area_remark_new').val()
								},
								
								function(data){
									if(data.success){
										$('#dev_show_content').load('movement_car_api.php?cmd=content');
										$("#divdialogconfirm").remove();
										$('#divdialogadd').remove();
										$('#divdialog').html("<div style=\"text-align:center\">"+data.message+"<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบรับรถ\" onclick=\"javascript:ClosePopup(); javascript:popU('../report/car_in_out.php?id=<?php echo $id; ?>','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=800,height=600')\"></div>");
									}else{
										alert(data.message);
									}
								},'json');				
						},
								ไม่ใช่: function(){
									$( this ).dialog( "close" );
								}
							}
						});
				});
			</script>
<?php
		}
		else // ถ้าเป็นคลังอื่นๆ
		{
?>
			<table cellpadding="5" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="65"><b>สีรถ :</b></td>
					<td>
						<select name="cb_color" id="cb_color" onchange="javascript:changeColor()">
							<option value="">เลือกสีรถ</option>
							<?php
								$qry = pg_query("select * from \"CarColor\" order by color_name ASC");
								while($res = pg_fetch_array($qry)){
									$color_name = $res['color_name'];
									$color_id = $res['color_id'];
									
									echo "<option value=\"$color_id\">$color_name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><b>เลขวิทยุ :</b></td>
					<td><input type="text" name="txt_radio_id" id="txt_radio_id" style="width:80px"></td>
				</tr>
				<tr>
					<td><b>ผู้ดูแล :</b></td>
					<td><input type="text" name="attendant" id="attendant"></td>
				</tr>
				<tr>
					<td><b>หมายเหตุ :</b></td>
					<td><textarea name="area_remark_new" id="area_remark_new" rows="2" cols="40"></textarea></td>
					<td><input type="button" name="btnSaveIn" id="btnSaveIn" value="บันทึก"></td>
				</tr>
			</table>

			<script>
				function ClosePopup(){
					$('#divdialog').remove();
				}

				$('#btnSaveIn').click(function(){
					var car_owner = $('#attendant').val();
					if(car_owner == ""){
						alert('กรุณาระบุผู้ดูแลด้วย !');
						return false;
					}
					var cb_color_txt = $('#cb_color').val();
					if(cb_color_txt == ""){
						alert('กรุณาระบุสีรถด้วย !');
						return false;
					}
						
					$('body').append('<div id="divdialogconfirm"></div>');
						$("#divdialogconfirm").text('ต้องการบันทึกใช่หรือไม่?');
						$("#divdialogconfirm").dialog({
							title: 'ยืนยัน',
							resizable: false,
							height:140,
							modal: true,
							buttons:{
								"ใช่": function(){
									$.post('movement_car_api.php',{
									cmd: 'save',
									type: 'in',
									id: '<?php echo $id; ?>',
									cb_color : $('#cb_color').val(),
									txt_hid_color : $('#txt_hid_color').val(),
									txt_radio_id : $('#txt_radio_id').val(),
									car_owner : $('#attendant').val(),
									txt_area_remark_new: $('#area_remark_new').val()
								},
								
								function(data){
									if(data.success){
										$('#dev_show_content').load('movement_car_api.php?cmd=content');
										$("#divdialogconfirm").remove();
										$('#divdialogadd').remove();
										$('#divdialog').html("<div style=\"text-align:center\">"+data.message+"<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบรับรถ\" onclick=\"javascript:ClosePopup(); javascript:popU('../report/car_in_out.php?id=<?php echo $id; ?>','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=800,height=600')\"></div>");
									}else{
										alert(data.message);
									}
								},'json');				
						},
								ไม่ใช่: function(){
									$( this ).dialog( "close" );
								}
							}
						});
					
					
					
					
				});
			</script>
<?php
		}
    }
}

elseif($cmd == "save"){
    $type = pg_escape_string($_POST['type']);
    $id = pg_escape_string($_POST['id']);
    $cb_color = pg_escape_string($_POST['cb_color']);
    $txt_hid_color = pg_escape_string($_POST['txt_hid_color']);
    $txt_radio_id = pg_escape_string($_POST['txt_radio_id']);
    $cb_wh = pg_escape_string($_POST['cb_wh']);
    $car_owner = pg_escape_string($_POST['car_owner']);
	$res_id = pg_escape_string($_POST['res_id']);
	$txt_area_remark_new = $_POST['txt_area_remark_new'];
	$wh_group = pg_escape_string($_POST['wh_group']);
	$GasType = pg_escape_string($_POST['GasType']);
	$gasInvoiceNo = pg_escape_string($_POST['gasInvoiceNo']); // เลขที่ใบแจ้งหนี้/ใบส่งของ
	$gasInvoiceDate = pg_escape_string($_POST['gasInvoiceDate']); // วันที่ออกใบแจ้งหนี้/ใบส่งของ
	
	$str_color = $cb_color;
	
	$GasTypeID = checknull($GasType);
	
    $qry = pg_query("SELECT car_id,color FROM \"CarMove\" WHERE \"auto_id\"='$id' ");
    if($res = pg_fetch_array($qry)){
        $car_id = $res['car_id'];
        $color = $res['color'];
    }
    
	$qry = pg_query("select car_idno from \"Cars\" where car_id = '$car_id' ");
	$car_idno = pg_fetch_result($qry,0);
	
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    if($type == "out"){
		// ตรวจสอบข้อมูลเดิม
		$qry_oldData = pg_query("select \"target_go\", \"wh_id\" from \"CarMove\" where auto_id = '$id' ");
		$target_go_old = pg_fetch_result($qry_oldData,0);
		$wh_id_old = pg_fetch_result($qry_oldData,1);
		
		// ตรวจสอบว่ามีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
		if($target_go_old != "")
		{
			$txt_error[] = "มีการทำรายการไปก่อนหน้านี้แล้ว";
			$status++;
		}
		
		// ตรวจสอบว่า ย้ายรถไปที่เดิมหรือไม่
		if($wh_id_old == $cb_wh)
		{
			$txt_error[] = "ไม่สามารถย้ายรถไว้ที่เดิมได้";
			$status++;
		}
		
		if($cb_wh != 0){
			$qry = "UPDATE \"CarMove\" SET date_out='$nowdate',target_go='$cb_wh',car_owner='$car_owner' , remark = '$txt_area_remark_new', \"GasTypeID\" = $GasTypeID
					WHERE auto_id='$id' ";
			if(!$res=@pg_query($qry)){
				$txt_error[] = "UPDATE CarMove ไม่สำเร็จ $qry";
				$status++;
			}
        
			//ตรวจสอบ หากไม่ใช่ส่งมอบลูกค้า ให้ insert ด้วย //update 2012-01-11
			$qry="INSERT INTO \"CarMove\" (car_id,color,wh_id,date_in,car_owner) values ('$car_id','$color','$cb_wh','$nowdate','$car_owner')";
			if(!$res=@pg_query($qry)){
                $txt_error[] = "INSERT CarMove ไม่สำเร็จ $qry";
                $status++;
            }
        }else{
			$qry = "UPDATE \"CarMove\" SET date_out='$nowdate',target_go='$cb_wh',car_owner='$car_owner' , remark = '$txt_area_remark_new' WHERE auto_id='$id' ";
			if(!$res=@pg_query($qry)){
				$txt_error[] = "UPDATE CarMove ไม่สำเร็จ $qry";
				$status++;
			}
			//เข้า process อนุมัติส่งมอบรถให้ลูกค้าต่อไป
			$qry = "insert into \"CarMoveToCus\" (car_id,res_id,doer_id,doer_stamp,status_appv) values ('$car_id','$res_id','$iduser','$nowdate','9') ";
			if(!$res=@pg_query($qry)){
                $txt_error[] = "INSERT CarMoveToCus ไม่สำเร็จ $qry";
                $status++;
            }
		}
    }elseif($type == "in"){
		// ตรวจสอบข้อมูลเดิม
		$qry_oldData = pg_query("select \"target_go\", \"wh_id\" from \"CarMove\" where auto_id = '$id' ");
		$target_go_old = pg_fetch_result($qry_oldData,0);
		$wh_id_old = pg_fetch_result($qry_oldData,1);
		
		// ตรวจสอบว่ามีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
		if($target_go_old != "")
		{
			$txt_error[] = "มีการทำรายการไปก่อนหน้านี้แล้ว";
			$status++;
		}
		
		// ตรวจสอบว่า ย้ายรถไปที่เดิมหรือไม่
		if($wh_id_old == $_SESSION[ss_office_id])
		{
			$txt_error[] = "ไม่สามารถย้ายรถไว้ที่เดิมได้";
			$status++;
		}

        $qry = "UPDATE \"CarMove\" SET date_out='$nowdate',target_go='$_SESSION[ss_office_id]',car_owner='$car_owner' , remark = '$txt_area_remark_new' WHERE auto_id='$id' ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE CarMove ไม่สำเร็จ $qry";
            $status++;
        }
        
        $qry="INSERT INTO \"CarMove\" (car_id,color,wh_id,date_in,car_owner) values ('$car_id','$str_color','1','$nowdate','$car_owner')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT CarMove ไม่สำเร็จ $qry";
            $status++;
        }
        
        $qry = "UPDATE \"Cars\" SET color='$str_color',radio_id='$txt_radio_id' WHERE car_id='$car_id' ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE Cars ไม่สำเร็จ $qry";
            $status++;
        }
		
		$qry="INSERT INTO car_history (car_idno,car_color) values ('$car_idno','$str_color')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT car_history ไม่สำเร็จ $qry";
            $status++;
        }
    }elseif($type == "inFromGas"){ // บันทึกเข้า จากการติดตั้งแก๊ส
	
		// ตรวจสอบข้อมูลเดิม
		$qry_oldData = pg_query("select \"gasInvoiceNo\", \"wh_id\" from \"CarMove\" where auto_id = '$id' ");
		$gasInvoiceNo_old = pg_fetch_result($qry_oldData,0);
		$wh_id_old = pg_fetch_result($qry_oldData,1);
		
		// ตรวจสอบว่ามีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
		if($gasInvoiceNo_old != "")
		{
			$txt_error[] = "มีการทำรายการไปก่อนหน้านี้แล้ว";
			$status++;
		}
		
		// ตรวจสอบว่า ย้ายรถไปที่เดิมหรือไม่
		if($wh_id_old == $_SESSION[ss_office_id])
		{
			$txt_error[] = "ไม่สามารถย้ายรถไว้ที่เดิมได้";
			$status++;
		}
	
		// หาสีเดิมที่มีอยู่
		$qry_color = pg_query("select \"color\" from \"CarMove\" where auto_id = '$id' ");
		$str_color = pg_fetch_result($qry_color,0);

        $qry = "UPDATE \"CarMove\" SET date_out = '$nowdate', target_go = '$_SESSION[ss_office_id]', car_owner = '$car_owner', remark = '$txt_area_remark_new',
				\"gasInvoiceNo\" = '$gasInvoiceNo', \"gasInvoiceDate\" = '$gasInvoiceDate'
				WHERE auto_id='$id' ";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "UPDATE CarMove ไม่สำเร็จ $qry";
            $status++;
        }
        
        $qry="INSERT INTO \"CarMove\" (car_id,color,wh_id,date_in,car_owner) values ('$car_id','$str_color','1','$nowdate','$car_owner')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT CarMove ไม่สำเร็จ $qry";
            $status++;
        }
		
		$qry="INSERT INTO car_history (car_idno,car_color) values ('$car_idno','$str_color')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT car_history ไม่สำเร็จ $qry";
            $status++;
        }
    }

    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
	
	$data['wh_group'] = "$wh_group";
    
    echo json_encode($data);
}

elseif($cmd == "CarsEditTemp") // หน้าจอแก้ไขรายละเอียดรถ
{
    $car_id = pg_escape_string($_GET['id']);

	$qry = pg_query("SELECT * FROM \"Cars\" WHERE \"car_id\" = '$car_id'");
	if($res = pg_fetch_array($qry)){
		$license_plate = $res['license_plate'];
		$product_id = $res['product_id'];
		$car_num = $res['car_num'];
		$mar_num = $res['mar_num'];
		$car_year = $res['car_year'];
		$color = $res['color'];
	}
	$num = pg_num_rows($qry );
	?>
	
	<form id="frmSentEditCar" name="frmSentEditCar" method="post" action="../car/movement_car_api.php">
		<table>
			<tr>
				<td align="right">ทะเบียนรถ : </td>
				<td align="left"><input type="textbox" id="new_license_plate" name="new_license_plate" value="<?php echo $license_plate; ?>"></td>
			</tr>
			<tr>
				<td align="right">Product : </td>
				<td align="left">
					<select id="new_product_id" name="new_product_id">
						<option value="">เลือก Product</option>
						<?php
						$qry_Product = pg_query("select * from \"Products\" where \"link_table\" = 'Cars' order by \"name\" ");
						while($res_Product = pg_fetch_array($qry_Product))
						{
							if($res_Product["product_id"] == $product_id)
							{
								echo "<option value=\"$res_Product[product_id]\" selected>$res_Product[name]</option>";
							}
							else
							{
								echo "<option value=\"$res_Product[product_id]\">$res_Product[name]</option>";
							}
						}
						?>
					</select> <font color="FF0000">*</font>
				</td>
			</tr>
			<tr>
				<td align="right">เลขถัง : </td>
				<td align="left"><input type="textbox" id="new_car_num" name="new_car_num" value="<?php echo $car_num; ?>"> <font color="FF0000">*</font></td>
			</tr>
			<tr>
				<td align="right">เลขเครื่อง  : </td>
				<td align="left"><input type="textbox" id="new_mar_num" name="new_mar_num" value="<?php echo $mar_num; ?>"> <font color="FF0000">*</font></td>
			</tr>
			<tr>
				<td align="right">ปีรถ (ค.ศ.) : </td>
				<td align="left"><input type="textbox" id="new_car_year" name="new_car_year" value="<?php echo $car_year; ?>"> <font color="FF0000">*</font></td>
			</tr>
			<tr>
				<td align="right">สีรถ : </td>
				<td align="left">
					<select id="new_color" name="new_color">
						<option value="">เลือกสีรถ</option>
						<?php
						$qry_CarColor = pg_query("select * from \"CarColor\" order by \"color_name\" ");
						while($res_CarColor = pg_fetch_array($qry_CarColor))
						{
							if($res_CarColor["color_id"] == $color)
							{
								echo "<option value=\"$res_CarColor[color_id]\" selected>$res_CarColor[color_name]</option>";
							}
							else
							{
								echo "<option value=\"$res_CarColor[color_id]\">$res_CarColor[color_name]</option>";
							}
						}
						?>
					</select> <font color="FF0000">*</font>
				</td>
			</tr>
		</table>
		
		<input type="hidden" name="cmd" value="processCarsEditTemp"/>
		<input type="hidden" name="car_id" value="<?php echo $car_id; ?>"/>
	</form>

	<div style="text-align:right; margin-top:5px">
		<input type="button" id="btnSave" value="บันทึกการแก้ไขรถ" onClick="CarEditTimp();">
	</div>

<script>
	function CarEditTimp()
	{
		if($('#new_product_id').val() == ""){
			alert('กรุณาเลือก Product !');
			return false;
		}
		
		if($('#new_car_num').val() == ""){
			alert('กรุณาระบุ เลขถัง !');
			return false;
		}
		
		if($('#new_mar_num').val() == ""){
			alert('กรุณาระบุ เลขเครื่อง !');
			return false;
		}
		
		if($('#new_car_year').val() == ""){
			alert('กรุณาระบุ ปีรถ !');
			return false;
		}
		
		if($('#new_color').val() == ""){
			alert('กรุณาเลือก สีรถ !');
			return false;
		}
		
		$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกใช่หรือไม่?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
					$('#frmSentEditCar').submit();
				},
				"ไม่ใช่": function(){
					$( this ).dialog( "close" );
				}
			}
		});
	}
</script>
<?php
}

elseif($cmd == "processCarsEditTemp") // process แก้ไขรายละเอียดรถ
{
    $car_id = pg_escape_string($_POST['car_id']);
    $new_license_plate = pg_escape_string($_POST['new_license_plate']);
    $new_product_id = pg_escape_string($_POST['new_product_id']);
    $new_car_num = pg_escape_string($_POST['new_car_num']);
    $new_mar_num = pg_escape_string($_POST['new_mar_num']);
    $new_car_year = pg_escape_string($_POST['new_car_year']);
	$new_color = pg_escape_string($_POST['new_color']);
	
	pg_query("BEGIN WORK");
    $status = 0;
	
	// ตรวจสอบก่อนว่า มีรายการรออนุมัติอยู่แล้วหรือไม่
	$qry_chkWait = pg_query("select * from \"CarsEditTemp\" where \"car_id\" = '$car_id' and \"appvStatus\" = '9' ");
	$row_chkWait = pg_num_rows($qry_chkWait);
	
	if($row_chkWait > 0)
	{
		$txt_error .= "Car ID $car_id อยู่ระหว่างรอการอนุมัติแก้ไข ";
		$status++;
	}
	
	// หาว่าเป็นการแก้ไขครั้งที่เท่าไหร่
	$qry_editTime = pg_query("select max(\"editTime\") from \"CarsEditTemp\" where \"car_id\" = '$car_id' ");
	$old_editTime = pg_fetch_result($qry_editTime,0);
	
	if($old_editTime == "")
	{ // ถ้ายังไม่เคยทำรายการ
		$new_editTime = 1;
		
		// หาข้อมูลเดิม
		$qry = pg_query("SELECT * FROM \"Cars\" WHERE \"car_id\" = '$car_id'");
		if($res = pg_fetch_array($qry)){
			$old_license_plate = $res['license_plate'];
			$old_product_id = $res['product_id'];
			$old_car_num = $res['car_num'];
			$old_mar_num = $res['mar_num'];
			$old_car_year = $res['car_year'];
			$old_color = $res['color'];
		}
		
		// บันทึกการแก้ไขครั้งที่ 0 ก่อน
		$qry_ins_old = "insert into \"CarsEditTemp\"(\"car_id\", \"license_plate\", \"product_id\", \"car_num\", \"mar_num\", \"car_year\", \"color\", \"editTime\",
							\"doerID\", \"doerStamp\", \"appvID\", \"appvStamp\", \"appvStatus\")
						values('$car_id', '$old_license_plate', '$old_product_id', '$old_car_num', '$old_mar_num', '$old_car_year', '$old_color', '0',
							'000', '$nowDateTime', '000', '$nowDateTime', '1') ";
		if(!$res=@pg_query($qry_ins_old)){
			$txt_error .= "บันทึกข้อมูลเก่า ไม่สำเร็จ $qry_ins_old ";
			$status++;
		}
	}
	else
	{
		$new_editTime = $old_editTime + 1;
	}
    
	// บันทึกข้อมูลการขอแก้ไข
	$qry_ins_new = "insert into \"CarsEditTemp\"(\"car_id\", \"license_plate\", \"product_id\", \"car_num\", \"mar_num\", \"car_year\", \"color\", \"editTime\",
						\"doerID\", \"doerStamp\", \"appvStatus\")
					values('$car_id', '$new_license_plate', '$new_product_id', '$new_car_num', '$new_mar_num', '$new_car_year', '$new_color', '$new_editTime',
						'$iduser', '$nowDateTime', '9') ";
	if(!$res=@pg_query($qry_ins_new)){
		$txt_error .= "บันทึกข้อมูลการขอแก้ไข ไม่สำเร็จ $qry_ins_new ";
		$status++;
	}

    if($status == 0)
	{
		//pg_query("ROLLBACK");
		pg_query("COMMIT");
		echo "<center>";
		echo "<br>";
		echo "<font color=\"0000FF\">บันทึกสำเร็จ</font>";
		echo "<br><br>";
		echo "<input type=\"button\" value=\"ตกลง\" onClick=\"window.location = '../report/report_warehouse.php';\">";
		echo "</center>";
    }
	else
	{
		pg_query("ROLLBACK");
		echo "<center>";
		echo "<br>";
		echo "<font color=\"FF0000\">บันทึกผิดพลาด</font>";
		echo "<br><br>";
		echo "$txt_error";
		echo "<br><br>";
		echo "<input type=\"button\" value=\"ตกลง\" onClick=\"window.location = '../report/report_warehouse.php';\">";
		echo "</center>";
    }
}

elseif($cmd == "div_admin_dialog"){
    $id = pg_escape_string($_GET['id']);
    $cid = pg_escape_string($_GET['cid']);
?>
<div style="color:orange; text-align:center">การส่งมอบรถให้ลูกค้า ต้องผ่านการยืนยันจาก Admin ก่อนค่ะ !</div>
<table width="230" cellspacing="0" cellpadding="3" border="0" align="center">
<tr>
    <td><B>Admin</B></td>
    <td><input type="text" name="username" id="username" style="width:150px"></td>
</tr>
<tr>
    <td><B>Password</B></td>
    <td><input type="password" name="password" id="password" style="width:150px"></td>
</tr>
<tr>
    <td>&nbsp;</TD>
    <td><input type="button" value="ยืนยัน" name="btnAdmin" id="btnAdmin"></td>
</tr>
</table>

<script>
$('#btnAdmin').click(function(){
    $.post('movement_car_api.php',{
        cmd: 'check_admin',
        user: $('#username').val(),
        pass: $('#password').val()
    },
    function(data){
        if(data.success){
            //alert(data.message);
            out_admin_car();
        }else{
            alert(data.message);
        }
    },'json');
});

function out_admin_car(){
		$.post('movement_car_api.php',{
			cmd: 'save',
			type: 'out',
			id: '<?php echo $id; ?>',
			cb_wh : '0'
		},
		function(data){
			if(data.success){
			$('#dev_show_content').load('movement_car_api.php?cmd=content');
			$('#divdialogadd').remove();
			$('#divdialog').remove();
			$('#div_admin_dialog').html("<div style=\"text-align:center\">"+data.message+"<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบส่งรถ\" onclick=\"javascript:ClosePopupAD(); javascript:popU('../report/car_out.php?id=<?php echo $id; ?>','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=800,height=600')\"></div>");
			}else{
				alert(data.message);
				}
			},'json');
}

function ClosePopupAD(){
    $('#div_admin_dialog').remove();
}
</script>
<?php
}

elseif($cmd == "check_admin"){
    $user = $_POST['user'];
    $pass = $_POST['pass'];
        $pass = md5($pass);
    
    $qry = pg_query("SELECT id_user FROM fuser WHERE username='$user' AND password='$pass' AND user_group='AD' AND status_user='TRUE' ");
    if($res = pg_fetch_array($qry)){
        $data['success'] = true;
        $data['message'] = "ok";
    }else{
        $data['success'] = false;
        $data['message'] = "Username หรือ Password ไม่ถูกต้อง !";
    }
    
    echo json_encode($data);
}

elseif($cmd == "check_money_outcar"){
    $res_id = pg_escape_string($_GET['res_id']);
    if( empty($res_id) ){
        echo "f"; 
        exit;
    }
    
    $down_price = "";
    $qry = pg_query("SELECT down_price FROM \"Reserves\" WHERE res_id='$res_id' AND cancel='FALSE' ");
    if($res = pg_fetch_array($qry)){
        $down_price = $res['down_price'];
        
        if($down_price == 0){
            echo "t";
        }else{
            //$qry_amt = pg_query("SELECT SUM(amount) AS amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND o_receipt IS NOT NULL AND constant_var IS NOT NULL ");
            $qry_amt = pg_query("SELECT SUM(amount) AS amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND constant_var LIKE 'CAR%' ");
            if($res_amt = pg_fetch_array($qry_amt)){
                $sum_amount = $res_amt['amount'];
            }
            if($down_price == $sum_amount){
                echo "t";
            }else{
                echo "f";
            }
        }
    }else{
        echo "f";
    }
}

elseif($cmd == "check_wait_appv"){
	$car_id = pg_escape_string($_GET['car_id']);
    if( empty($car_id) ){
        echo "f"; 
        exit;
    }
	$qry_appv = pg_query("select auto_id from \"CarMoveToCus\" where car_id='$car_id' and status_appv='9' ");
	$numrow = pg_num_rows($qry_appv);
		if($numrow == 0){
			echo "t"; 
		}else{
			echo "f";
		}
}
?>