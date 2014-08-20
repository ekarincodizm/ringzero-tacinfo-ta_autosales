<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Pass Cheque";
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

<div id="dev_edit" style="margin-top:15px">

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>No.</td>
    <td>เลขที่เช็ค</td>
    <td>running_no</td>
    <td>ธนาคาร</td>
    <td>สาขา</td>
    <td>วันที่เข้าธนาคาร</td>
	<td>ยอดเงิน</td>
	<td>วันที่เช็คผ่าน/เงินโอนเข้า</td>
    <td>&nbsp;</td>
</tr>

<?php
$j = 0;
//$qry = pg_query("SELECT * FROM \"Cheques\" WHERE is_pass = 'FALSE' AND accept = 'TRUE' AND date_enter_bank < '$nowdate' ORDER BY acc_bank_enter,cheque_no ASC ");
//ให้เลือก วันที่เช็ค ผ่าน เอง ตอน ทำรายการ
$qry = pg_query("SELECT * FROM \"Cheques\" WHERE is_pass = 'FALSE' AND accept = 'TRUE' AND cancel = 'FALSE' ORDER BY acc_bank_enter,cheque_no ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $cheque_no = $res['cheque_no'];
    $running_no = $res['running_no'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    $date_enter_bank = $res['date_enter_bank'];
    $amt_on_cheque = $res['amt_on_cheque'];
    $acc_bank_enter = $res['acc_bank_enter'];
    
    
    $qry_accname = pg_query("SELECT * FROM \"BankEnter\" WHERE accno='$acc_bank_enter' ");
    if( $res_accname = pg_fetch_array($qry_accname) ){
        $accno = $res_accname['accno'];
        $accname = $res_accname['accname'];
    }
    
    if($accname != $old_accname){
        echo "<tr><td style=\"font-weight:bold; background-color:#FFFAF0\" colspan=\"8\">ธนาคารที่เข้า : $accname</td></tr>";
    }

    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo "$j"; ?></td>
    <td><a href="javascript:ShowDetail('<?php echo $running_no; ?>')"><u><?php echo $cheque_no; ?></u></a></td>
    <td><?php echo $running_no; ?></td>
    <td><?php echo $bank_name; ?></td>
    <td><?php echo $bank_branch; ?></td>
    <td align="center"><?php echo $date_enter_bank; ?></td>
    <td align="right"><input type="hidden" name="txt_hid_money<?php echo $j; ?>" id="txt_hid_money<?php echo $j; ?>" value="<?php echo $amt_on_cheque; ?>"><?php echo number_format($amt_on_cheque,2); ?></td>
	
	<td align="center"><input type="text" <?php echo "name=\"appdate$j\""; ?> value="<?php echo $nowdate; ?>" <?php echo "id=\"appdate$j\""; ?> size="10" /></td>
	
    <td align="center"><input type="button" name="btnSave" id="btnSave" value="ทำรายการ" onclick="javascript:Save('<?php echo $running_no; ?>','<?php echo $cheque_no; ?>','<?php echo $j; ?>')"></td>
</tr>
<?php
    $old_accname = $accname;
}

if($j == 0){
    echo "<tr><td colspan=8 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>
function Save(rid,cid,row){
	$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกเช็คผ่านใช่หรือไม่ ?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
				
					var appdate = $('#appdate'+row).val();
					$.post('pass_cheque_api.php',{
					cmd: 'save',
					rid: rid,
					cid: cid,
					appdate: appdate
					},
					function(data){
						if(data.success){
							alert(data.message);
							location.reload();
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

function ShowDetail(id){
    $('body').append('<div id="divdetail"></div>');
    $('#divdetail').load('pass_cheque_api.php?cmd=chequedetail&id='+id);
    $('#divdetail').dialog({
        title: 'แสดงรายละเอียด : '+id,
        resizable: false,
        modal: true,  
        width: 600,
        height: 350,
        close: function(ev, ui){
            $('#divdetail').remove();
        }
    });
}

//วิธีทำ Date picker ตามจำนวนข้อมูล
var j = '<?php echo $j; ?>';
for(var k = 1; k<=j; k++)
{
	//document.getElementById("appdate"+k).datepicker({
	$("#appdate"+k).datepicker({
		showOn: 'button',
		buttonImage: '../images/calendar.gif',
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
}
</script>
</body>
</html>