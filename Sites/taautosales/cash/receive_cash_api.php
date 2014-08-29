<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "autocomplete"){
    $term = $_GET['term'];
    $qry_name=pg_query("select * from \"VAllCustomers\" WHERE cus_name LIKE '%$term%' OR surname LIKE '%$term%' OR res_id LIKE '%$term%' ORDER BY \"cus_name\" ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $res_id = $res_name["res_id"];
        $cus_name = trim($res_name["cus_name"]);
        $surname = trim($res_name["surname"]);
        $dt['value'] = $res_id."#".$cus_name." ".$surname;
        $dt['label'] = "{$res_id}, {$cus_name} {$surname}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}

elseif($cmd == "divdetail"){
    $id = $_GET['id'];
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Service ID</td>
    <td>Name</td>
    <td>Cost</td>
    <td>Vat</td>
    <td>Total</td>
</tr>
<?php
$qry = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$id' AND cancel='FALSE' ORDER BY service_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $service_id = $res['service_id'];
    $amount = $res['amount'];
    $vat = $res['vat'];
    
    if(substr($service_id,0,1) == "S"){
        $service_name = GetServicesName($service_id);
    }else{
        $service_name = GetProductName($service_id);
    }
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $service_id; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
    <td align="right"><?php echo number_format($amount+$vat,2); ?></td>
</tr>
<?php
}
?>
</table>

<?php
}

elseif($cmd == "divshow"){
    $id = pg_escape_string($_GET['id']);
?>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td width="50">Select</td>
    <td>inv_no</td>
    <td>inv_date</td>
    <td>Detail</td>
    <td>Amount</td>
    <td>Vat</td>
    <td>Total</td>
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE res_id = '$id' AND status IS NULL AND cancel = 'FALSE' ORDER BY inv_no ASC ");
while( $res = pg_fetch_array($qry) ){
    $j++;
    $inv_no = $res['inv_no'];
    $inv_date = $res['inv_date'];
    
    $amount = 0;
    $vat = 0;
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $amount += $res2['amount'];
        $vat += $res2['vat'];
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
    
    $name = implode(",", $arr_name);
    
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><input type="checkbox" name="chk_box" id="chk_box<?php echo $j; ?>" value="<?php echo $inv_no; ?>" onchange="javascript:ChkCheck(<?php echo $j; ?>)"></td>
    <td align="center"><a href="javascript:ShowDetail('<?php echo $inv_no; ?>')"><u><?php echo $inv_no; ?></u></a></td>
    <td align="center"><?php echo $inv_date; ?></td>
    <td align="left"><?php echo $name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
    <td align="right"><?php echo number_format($amount+$vat,2); ?><input type="hidden" name="txt_hid_money<?php echo $j; ?>" id="txt_hid_money<?php echo $j; ?>" value="<?php echo $amount+$vat; ?>"></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

<div style="margin-top:5px; padding: 8px; background-color:#FFFFCE; border: 1px dotted #D0D0D0; font-weight:bold">
    <div style="float:left">จำนวนรายการที่เลือก : <span id="span_select_check">0</span> รายการ</div>
    <div style="float:right">ยอดเงินทั้งหมด : <span id="span_select_money">0.00</span> บาท</div>
    <div style="clear:both"></div>
</div>

<div style="margin:5px 0 10px 0; padding: 5px; background-color:#FFE1E1; border: 1px dotted #D0D0D0">
    <div style="float:left"></div>
    <div style="float:right"><input type="button" name="btnSave" id="btnSave" value="ทำรายการที่เลือก"></div>
    <div style="clear:both"></div>
</div>

<script>
$('#btnSave').click(function(){
    if($('#span_select_check').text() == '0'){
        alert('กรุณาเลือกรายการ');
        return false;
    }
    
    var val_chkbox = $("input[name=chk_box]:checked").map(function(){
        return this.value;
    }).get().join(",");

    $.post('receive_cash_api.php',{
        cmd: 'save',
        select: val_chkbox
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

function ShowDetail(id){
    $('body').append('<div id="divdetail"></div>');
    $('#divdetail').load('receive_cash_api.php?cmd=divdetail&id='+id);
    $('#divdetail').dialog({
        title: 'แสดงรายละเอียดของใบแจ้งหนี้ : '+id,
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
</script>

<?php
}

elseif($cmd == "save"){
    $select = pg_escape_string($_POST['select']);

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $arr_select = explode(",",$select);
    foreach($arr_select as $v){
        $in_qry="UPDATE \"Invoices\" SET status='OCCA' WHERE inv_no='$v' ";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "UPDATE Invoices ไม่สำเร็จ $in_qry";
            $status++;
        }

        $qry = @pg_query("SELECT constant_var,res_id FROM \"VInvDetail\" WHERE inv_no='$v'");
        if($res = @pg_fetch_array($qry)){
            $constant_var = $res['constant_var'];
            $res_id = $res['res_id'];

            if( substr($constant_var, 0, 3) == "CAR" ){
                $amount = 0;
                $qry2 = @pg_query("SELECT amount FROM \"VInvDetail\" WHERE inv_no='$v'");
                while($res2 = @pg_fetch_array($qry2)){
                    $amount += $res2['amount'];
                }

                $in_qry2="UPDATE \"ReserveDetails\" SET cash_amt='$amount' WHERE res_id='$res_id' AND do_date='$nowdate' AND cash_amt='0' AND cheque_amt='0' AND cancel='FALSE' ";
                if(!$res2=@pg_query($in_qry2)){
                    $txt_error[] = "UPDATE ReserveDetails ไม่สำเร็จ $in_qry2";
                    $status++;
                }
            }
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
    echo json_encode($data);
}
?>