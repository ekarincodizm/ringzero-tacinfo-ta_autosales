<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "ยกเลิกใบเสร็จ (บัญชี)";
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
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once("../include/header_popup.php");
?>

<div>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#CCCCCC">
<tr bgcolor="#F0F0F0">
    <td width="20%" style="font-weight:bold">เลขที่ใบเสร็จ</td>
    <td><input type="text" name="txt_no" id="txt_no" size="20"></td>
</tr>
<tr bgcolor="#F0F0F0">
    <td style="font-weight:bold">เหตุผลที่ต้องการยกเลิก</td>
    <td><textarea name="area_memo" id="area_memo" rows="4" cols="50"></textarea></td>
</tr>
<tr bgcolor="#F0F0F0">
    <td style="font-weight:bold">ประเภทการยกเลิก</td>
    <td>
        <select name="cb_type" id="cb_type">
            <option value="NOT">เลือก</option>
            <option value="NRF">NRF (No Refund)</option>
            <option value="CUS">CUS (Return Customer)</option>
        </select>
    </td>
</tr>
<tr bgcolor="#F0F0F0">
    <td></td>
    <td><input type="button" name="btnSave" id="btnSave" value="บันทึก"></td>
</tr>
</table>

</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>

$('#btnSave').click(function(){
	if( $('#txt_no').val() == "" ){
		alert("กรุณา ระบุเลขที่ใบเสร็จ");
		return false;
	}else if( $('#area_memo').val() == "" ){
		alert("กรุณา ระบุเหตุผลที่ต้องการยกเลิก");
		return false;
	}else if( $('#cb_type').val() == 'NOT' ){
		alert("กรุณาเลือก ประเภทการยกเลิก");
		return false;
	}else{}
	
    $.post('cancel_receipt_api.php',{
        cmd: 'save_cancel_receipt',
        receipt_no: $('#txt_no').val(),
        area_memo: $('#area_memo').val(),
        cb_type: $('#cb_type').val()
    },
    function(data){
        if(data.success){
            alert(data.message);
            location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});

</script>

</body>
</html>