<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "นำเช็คเข้าธนาคาร";
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
   <!-- <td>เลือก</td>  ยกลิก แบบ check box ให้ ทำ ทีละรายการ จะได้เลือกวันที่ได้ -->
    <td>No.</td>
    <td>เลขที่เช็ค</td>
    <td>running_no</td>
    <td>ธนาคาร</td>
    <td>สาขา</td>
    <td>วันที่บนเช็ค</td>
    <td>ยอดเงิน</td>
	<td>วันที่นำเข้า</td>
	<td>&nbsp;</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"Cheques\" WHERE is_pass = 'FALSE' AND cancel = 'FALSE' AND accept = 'TRUE' AND date_on_cheque <= '$nowdate' AND date_enter_bank IS NULL ORDER BY acc_bank_enter,cheque_no ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $cheque_no = $res['cheque_no'];
    $running_no = $res['running_no'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    $date_on_cheque = $res['date_on_cheque'];
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
    <!-- <td align="center"><input type="checkbox" name="chk_box" id="chk_box<?php echo $j; ?>" value="<?php echo "$running_no#$cheque_no"; ?>" onchange="javascript:ChkCheck(<?php echo $j; ?>)"></td>  -->
    <td align="center"><?php echo $j; ?></td>
    <td align="center"><a href="javascript:ShowDetail('<?php echo $running_no; ?>')"><u><?php echo $cheque_no; ?></u></a></td>
    <td align="center"><?php echo $running_no; ?></td>
    <td><?php echo $bank_name; ?></td>
    <td><?php echo $bank_branch; ?></td>
    <td align="center"><?php echo $date_on_cheque; ?></td>
    <td align="right"><input type="hidden" name="txt_hid_money<?php echo $j; ?>" id="txt_hid_money<?php echo $j; ?>" value="<?php echo $amt_on_cheque; ?>"><?php echo number_format($amt_on_cheque,2); ?></td>
	<td align="center"><input type="text" <?php echo "name=\"enterdate$j\""; ?> value="<?php echo $nowdate; ?>" <?php echo "id=\"enterdate$j\""; ?> size="10" /></td>
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

<!-- ยกเลิก แบบ เลือก ให้ ทำทีละรายการ 
<div style="margin-top:5px; padding: 8px; background-color:#FFFFCE; border: 1px dotted #D0D0D0; font-weight:bold">
    <div style="float:left">จำนวนรายการที่เลือก : <span id="span_select_check">0</span> รายการ</div>
    <div style="float:right">ยอดเงินทั้งหมด : <span id="span_select_money">0.00</span> บาท</div>
    <div style="clear:both"></div>
</div>


	<div style="margin:5px 0 10px 0; padding: 5px; background-color:#FFE1E1; border: 1px dotted #D0D0D0">
    <div style="float:left"></div>
    <div style="float:right"><input type="button" name="btnConfirm" id="btnConfirm" value="บันทึก"></div> 
    <div style="clear:both"></div>
</div>  -->

</div>

      </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script>

/*  บันทึก แบบ เลือก หลายรายการ
$('#btnConfirm').click(function(){
    if($('#span_select_check').text() == '0'){
        alert('กรุณาเลือกรายการ');
        return false;
    }
    
	$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกนำเข้าเช็คใช่หรือไม่ ?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
				var val_chkbox = $("input[name=chk_box]:checked").map(function(){
				return this.value;
				}).get().join(",");
				
				var enterdate = $('#enterdate'+row).val();
				
				$.post('enter_date_bank_api.php',{
					cmd: 'save',
					select: val_chkboxม
					enterdate: enterdate
				},
				function(data){
					if(data.success){
						ShowPrint(data.message);
						//alert(data.message);
						//location.reload();
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
	
});  */

// บันทึกทีละรายการ
function Save(rid,cid,row){
	$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกนำเข้าเช็คใช่หรือไม่ ?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
					var enterdate = $('#enterdate'+row).val();
					$.post('enter_date_bank_api.php',{
					cmd: 'save',
					rid: rid,
					cid: cid,
					enterdate: enterdate
					
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


function ShowPrint(id){
    $('body').append('<div id="divdialogprint"></div>');
    $('#divdialogprint').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/cheque_enter_bank_pdf.php?id="+ id +"','cq_enter','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:location.reload();\"></div>");
    $('#divdialogprint').dialog({
        title: 'พิมพ์เอกสาร',
        resizable: false,
        modal: true,  
        width: 300,
        height: 200,
        close: function(ev, ui){
            $('#divdialogprint').remove();
        }
    });
}

function ShowDetail(id){
    $('body').append('<div id="divdetail"></div>');
    $('#divdetail').load('enter_date_bank_api.php?cmd=chequedetail&id='+id);
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

function ChkCheck(id){
    var n = 0;
    var p = 0;
    for (var i=1; i <= <?php echo $j; ?>; i++){
        if( $('input[id=chk_box'+ i +']:checked').val() ){
            p += parseFloat( $('#txt_hid_money'+i).val() );
            n++;
        }
    }

    $('#span_select_check').text(n);
    $('#span_select_money').text( formatMoney( p.toFixed(2) ) );
}
</script>

<script type="text/javascript">
function formatMoney(inum){
    if(inum == "0.00" || inum == ""){
        return 0;
    }else{
        // ฟังก์ชันสำหรับแปลงค่าตัวเลขให้อยู่ในรูปแบบ เงิน 
        var s_inum=new String(inum);
        var num2=s_inum.split(".",s_inum);
        var l_inum=num2[0].length;
        var n_inum=""; 
        for(i=0;i<l_inum;i++){
            if(parseInt(l_inum-i)%3==0){
                if(i==0){
                    n_inum+=s_inum.charAt(i);
                }else{
                    n_inum+=","+s_inum.charAt(i);
                }
            }else{
                n_inum+=s_inum.charAt(i);
            }
        }

        if(num2[1]!=undefined){
            n_inum+="."+num2[1];
        }

        return n_inum;
    }
}
// การใช้งาน var inum=65120.45;
// alert(formatMoney(inum));
//วิธีทำ Date picker ตามจำนวนข้อมูล
var j = '<?php echo $j; ?>';
for(var k = 1; k<=j; k++)
{
	//document.getElementById("appdate"+k).datepicker({
	$("#enterdate"+k).datepicker({
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