<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "divshow"){
	$carmove_p = $_GET['carmove_p'];
	$select_license_plate = $_GET['license_plate'];
	$car_id = $_GET['car_id'];
	
	$qry = pg_query("select car_idno from \"Cars\" where car_id = '$car_id' ");
	$g_car_idno = pg_fetch_result($qry,0);
	
    if($select_license_plate == ""){
		$url = "withdrawal_carplate_api.php";
		
    }else{
        $url = "../drawn/withdrawal_carplate_api.php";
        echo "<div style=\"font-size:15px; font-weight:bold\">ทำรายการเบิกป้ายเหล็ก</div>";
    }
	
	if($carmove_p == "t"){
		$url = "../drawn/withdrawal_carplate_api.php";
        echo "<div style=\"font-size:15px; font-weight:bold\">ทำรายการเบิกป้ายเหล็ก</div>";
	}
?>
<script language="javascript">

	function popU(U,N,T) {
    newWindow = window.open(U, N, T);
}
	
	</script>

<div>
<table cellpadding="5" cellspacing="0" border="0" width="100%" bgcolor="#F0F0F0" style="border:1px dashed #CCCCCC">
<tr>
    <td width="20%">เลือกป้ายเหล็กทะเบียน</td>
    <td width="30%">
<select name="cb_carplate" id="cb_carplate">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"P_NewCarPlate\" WHERE date_out IS NULL AND license_plate IS NULL ORDER BY new_plate ASC");
while( $res = pg_fetch_array($qry) ){
    $new_plate = $res['new_plate'];
?>
    <option value="<?php echo $new_plate; ?>"><?php echo "$new_plate"; ?></option>
<?php
}
?>
</select>
<a onclick="javascript:popU('frm_addCarPlate.php','','toolbar=no,menubar=no,resizable=no,scrollbars=yes,status=no,location=no,width=600,height=300')" style="cursor:pointer;"><font color="#0000FF" size="2"><u> เพิ่มป้ายเหล็ก</u></font></a>
<form id="form1" name="form1" method="post" action="">
  <input type="button" name="updatelistbox" id="updatelistbox" value="click" onclick="refreshListBox()" />
</form></td>
    <td width="20%">ใส่รถป้ายแดงคัน</td>
    <td width="30%">
<select name="cb_stockcars" id="cb_stockcars">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"VNewCarNotPlate\" where car_type_id = '1' and car_status<> 'S' ORDER BY license_plate ASC");
while( $res = pg_fetch_array($qry) ){
    $car_idno = $res['car_idno'];
?>
    <option value="<?php echo $car_idno; ?>" <?php if($g_car_idno == $car_idno){ echo "selected ";}?>><?php echo $car_idno; ?></option>
<?php
}
?>
</select>
    </td>
</tr>
<tr>
    <td>เบิกเพื่อ</td>
    <td>
<input type="radio" name="radio_wd_type" id="radio_wd_type" value="1" checked /> ติดรถที่จะขาย <input type="radio" name="radio_wd_type" id="radio_wd_type" value="2"> ใช้ภายใน
    </td>
    <td rowspan="2" colspan="2">
      <p><span id="wd_hp" style="display:none; color:#FFA500">
        เหตุผลในการเบิกใช้<br>
  <textarea name="txt_hp" id="txt_hp" rows="1" cols="1" style="width:300px; height:43px"></textarea>
        </span>
      </p>
      <p>&nbsp; </p></td>
</tr>
<tr>
    <td>ผู้เบิก</td>
    <td>
<select name="cb_fuser" id="cb_fuser">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"fuser\" WHERE status_user='TRUE' and user_group='3' ORDER BY fullname ASC");
while( $res = pg_fetch_array($qry) ){
    $id_user = $res['id_user'];
    $fullname = $res['fullname'];
?>
    <option value="<?php echo $id_user; ?>"><?php echo "$fullname"; ?></option>
<?php
}
?>
</select>
    </td>
</tr>
</table>
</div>

<div style="margin-top:10px; text-align:right">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

<script>
$("#updatelistbox").hide();
$("input[name='radio_wd_type']").change(function(){
    if( $('input[id=radio_wd_type]:checked').val() == "1" ){
        $('#txt_hp').val('');
        $('#wd_hp').hide();
    }else{
        $('#txt_hp').val('');
        $('#wd_hp').show('fast');
        $('#txt_hp').focus();
    }
});

$('#btnSave').click(function(){
	var chk =0;
	var Error = 'กรุณาระบุข้อมูลดังนี้! \n';
	if($('#cb_carplate').val()==''){
		Error += 'ป้ายเหล็กทะเบียน \n';
		chk++;
	}
	if($('#cb_stockcars').val()==''){
		Error += 'ใส่รถป้ายแดงคัน \n';
		chk++;
	}
	if($('input[id=radio_wd_type]:checked').val() == '2'){
		if($('#txt_hp').val()==''){
			Error += 'เหตุผลในการเบิกใช้ \n';
			chk++;
		}	
	}
	if($('#cb_fuser').val()==''){
		Error += 'ผู้เบิก \n';
		chk++;
	}
	
	if(chk>0){
		alert(Error);
		return false;
	}else{
    $.post('<?php echo $url; ?>',{
        cmd: 'save',
        cb_carplate: $('#cb_carplate').val(),
        cb_stockcars: $('#cb_stockcars').val(),
        radio_wd_type: $('input[id=radio_wd_type]:checked').val(),
        txt_hp: $('#txt_hp').val(),
        cb_fuser: $('#cb_fuser').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
<?php
if($select_license_plate == "" and $carmove_p != "t"){
?>
    location.reload();
<?php
}else{
?>
    $('#DivCarPlate').remove();
<?php
}
?>
            
        }else{
            alert(data.message);
        }
    },'json');
	}
});

function refreshListBox() // refreshทั้งหมด
{  
	
	var datanumplate = $.ajax({    // รับค่าจาก ajax เก็บไว้ที่ตัวแปร dataAssetsList  
		  url: "dataForPlateList.php", // ไฟล์สำหรับการกำหนดเงื่อนไข  
		 // data:"selectColor="+$(f_carcolor).val(), // ส่งตัวแปร GET ชื่อ list1
		  async: false  
	}).responseText;
		$("select#cb_carplate").html(datanumplate); // นำค่า dataAssetsList มาแสดงใน listbox ที่ชื่อ assets..
	
}


</script>
<?php
}

elseif($cmd == "save"){
    $cb_carplate = $_POST['cb_carplate'];
    $cb_stockcars = $_POST['cb_stockcars'];
    $radio_wd_type = $_POST['radio_wd_type'];
    $txt_hp = $_POST['txt_hp'];
    $cb_fuser = $_POST['cb_fuser'];
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    if($radio_wd_type == 1){
        $str_for_sale = "T";
        $txt_hp = "DEFAULT";
    }elseif($radio_wd_type == 2){
        $str_for_sale = "F";
        $txt_hp = "'$txt_hp'";
    }
    
    $in_qry="UPDATE \"P_NewCarPlate\" SET date_out='$nowdate',for_sale='$str_for_sale',memo_use_inhouse=$txt_hp,maker_id='$_SESSION[ss_iduser]',receive_id='$cb_fuser',car_idno='$cb_stockcars'
    WHERE new_plate='$cb_carplate' AND date_out IS NULL AND car_idno IS NULL";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE P_NewCarPlate ไม่สำเร็จ $in_qry";
        $status++;
    }
	
	$in_qry="UPDATE \"Cars\" SET license_plate='$cb_carplate'
    WHERE car_idno = '$cb_stockcars' AND license_plate IS NULL";
    if(!$res=@pg_query($in_qry)){
        $txt_error[] = "UPDATE Cars ไม่สำเร็จ $in_qry";
        $status++;
    }
	
    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! เนื่องจาก $txt_error[0]";
    }
    echo json_encode($data);
}
?>