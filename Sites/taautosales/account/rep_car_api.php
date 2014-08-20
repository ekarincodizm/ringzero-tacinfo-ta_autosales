<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "show_car"){
?>

<div style="text-align:right; margin-bottom:10px">
<b>กรุณาระบุเลขทะเบียนรถในสต๊อก</b> <input type="text" name="txt_stock_no" id="txt_stock_no" size="35" >
</div>

<div id="div_show_stock_no">
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td width="120">เลขที่</td>
	<td>ทะเบียนรถในสต๊อก</td>
    <td>เลขถัง</td>
    <td>เลขเครื่อง</td>
    <td>สีรถ</td>
    <td>ทะเบียนรถ</td>
</tr>

<?php
$qry = pg_query("SELECT * from \"Cars\" c left join \"PurchaseOrders\" p on c.po_id=p.po_id WHERE  c.cost_val='0' AND c.cost_vat='0' AND \"Pass_TA\"='1' ");
while($res = pg_fetch_array($qry)){
    $j++;
    $car_id = $res['car_id'];
    $car_num = $res['car_num'];
    $mar_num = $res['mar_num'];
    $color = $res['color'];
    $license_plate = $res['license_plate'];
    $car_idno = $res['car_idno'];
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><a href="javascript:ShowDetail('<?php echo $car_id; ?>');"><u><?php echo $car_id; ?></u></a></td>
	<td><?php echo $car_idno; ?></td>
    <td><?php echo $car_num; ?></td>
    <td><?php echo $mar_num; ?></td>
    <td><?php echo getCarColor($color); ?></td>
    <td><?php echo $license_plate; ?></td>
</tr>
<?php
}
?>
</table>
</div>


<script>
$("#txt_stock_no").autocomplete({
    source: "rep_car_api.php?cmd=autocomplete",
    minLength:1,
    select: function(event, ui) {
        if(ui.item.value != 'ไม่พบข้อมูล'){
            var str_plan = ui.item.value;
            var arr_plan = str_plan.split("#");
            ShowDetail( arr_plan[0] );
        }
    }
});

function ShowDetail(id){
    $('body').append('<div id="divdialog"></div>');
    $('#divdialog').empty();
    $('#divdialog').load('rep_car_api.php?cmd=divdialog&id='+id);
    $('#divdialog').dialog({
        title: 'แสดงรายละเอียด : ' + id,
        resizable: false,
        modal: true,  
        width: 660,
        height: 400,
        close: function(ev, ui){
            $('#divdialog').remove();
        }
    });
}

</script>
<?php
}

elseif($cmd == "autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("SELECT * FROM \"Cars\" WHERE car_idno LIKE '%$term%' and cost_val = 0 ORDER BY car_idno ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $car_idno = $res_name["car_idno"];
        $car_id = $res_name['car_id'];
        $car_num = $res_name['car_num'];
        $mar_num = $res_name['mar_num'];
        $color = $res_name['color'];
        
        $dt['value'] = $car_id."#".$car_idno;
        $dt['label'] = "{$car_id} , {$car_idno} ";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 20);
    print json_encode($matches);
}

elseif($cmd == "divdialog"){
    $id = $_GET['id'];

    $qry_name=pg_query("SELECT * FROM \"Cars\" WHERE car_id = '$id' ");
    if($res_name=pg_fetch_array($qry_name)){
        $po_id = $res_name["po_id"];
        $license_plate = $res_name["license_plate"];
        $car_id = $res_name['car_id'];
        $car_num = $res_name['car_num'];
        $mar_num = $res_name['mar_num'];
        $color = $res_name['color'];
        $car_name = $res_name['car_name'];
        $product_id = $res_name['product_id'];
		$car_idno = $res_name['car_idno'];
    }
  
    $qry_name=pg_query("SELECT * FROM \"PurchaseOrders\" WHERE po_id = '$po_id' ");
    if($res_name=pg_fetch_array($qry_name)){
        $user_id = $res_name["user_id"];
        $vender_id = $res_name["vender_id"];
		$po_type_id = $res_name["po_type_id"];
            $vender_name = getCusNameFromVender($vender_id,$po_type_id);
        $po_date = $res_name["po_date"];
        //$amount = $res_name["amount"];
        //$vat = $res_name["vat"];
    }
    
    $qry_name=pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE po_id = '$po_id' AND product_id='$product_id' ");
    if($res_name=pg_fetch_array($qry_name)){
        $unit = $res_name["unit"];
        $amount = round($res_name["product_cost"]/$unit,2);
        $vat = round($res_name["vat"]/$unit,2);
    }
?>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="140">เลขที่ po : </td><td><?php echo "$po_id"; ?></td>
</tr><tr>
    <td>ทะเบียนรถ : </td><td><?php echo "$license_plate"; ?></td>
</tr><tr>
    <td>ทะเบียนรถในสต๊อก : </td><td><?php echo "$car_idno"; ?></td>
</tr><tr>
    <td>เลขตัวถัง : </td><td><?php echo "$car_num"; ?></td>
</tr><tr>
    <td>เลขเครื่อง : </td><td><?php echo "$mar_num"; ?></td>
</tr><tr>
    <td>ผู้ขาย : </td><td><?php echo "$vender_name"; ?></td>
</tr><tr valign="top">
    <td>ชำระโดย : </td><td style="font-size:11px">
        
<?php
$other_hid_txt = "";
$qry = pg_query("SELECT * FROM account.\"VoucherDetails\" WHERE vc_detail LIKE '%$po_id%' ");
while($res = pg_fetch_array($qry)){
    $cash_amt = $res['cash_amt'];
    $chque_no = $res['chque_no'];

    if(!empty($chque_no)){
        $qry7 = pg_query("SELECT * FROM account.\"ChequeAccDetails\" WHERE chq_id = '$chque_no' ");
        if($res7 = pg_fetch_array($qry7)){
            $date_on_chq = $res7['date_on_chq'];
            $ch_amount = $res7['amount'];
            $ac_id = $res7['ac_id'];
        }
        
        $qry8 = pg_query("SELECT * FROM account.\"ChequeAccs\" WHERE ac_id = '$ac_id' ");
        if($res8 = pg_fetch_array($qry8)){
            $bank_name = $res8['bank_name'];
            $bank_branch = $res8['bank_branch'];
        }
    }
    
    if($cash_amt != 0 AND !empty($chque_no)){
        //ทั้ง 2อย่าง
        $other_hid_txt .= "- เงินสด ".number_format($cash_amt,2)." บาท<br />";
        $other_hid_txt .= "- เช็ค ธนาคาร $bank_name สาขา $bank_branch เลขที่ $chque_no ลงวันที่ $date_on_chq ยอดเงิน ".number_format($ch_amount,2)." บาท<br />";
    }elseif($cash_amt == 0 AND !empty($chque_no)){
        //จ่ายเช็ค
        $other_hid_txt .= "- เช็ค ธนาคาร $bank_name สาขา $bank_branch เลขที่ $chque_no ลงวันที่ $date_on_chq ยอดเงิน ".number_format($ch_amount,2)." บาท<br />";
    }elseif($cash_amt != 0 AND empty($chque_no)){
        //เงินสด
        $other_hid_txt .= "- เงินสด<br />";
    }else{
        $other_hid_txt .= "- ข้อมูลผิดผลาด !<br />";
    }
}

if( empty($other_hid_txt) ){
    $other_hid_txt = "ไม่พบข้อมูลการชำระ";
}

echo $other_hid_txt;
?>
        
    </td>
</tr><tr>
    <td>ยอดเงินมูลค่า : </td><td><input type="text" name="txt_val" id="txt_val" size="15" value="<?php echo "$amount"; ?>" style="text-align:right"></td>
</tr><tr>
    <td>ยอด vat : </td><td><input type="text" name="txt_vat" id="txt_vat" size="15" value="<?php echo "$vat"; ?>" style="text-align:right"></td>
</tr><tr>
    <td>วันที่บนใบเสร็จ : </td><td><input type="text" name="txt_date" id="txt_date" size="12" value="<?php echo "$po_date"; ?>" style="text-align:center"></td>
 </tr><tr>
    <td>เลขที่ใบกำกับภาษี : </td><td><input type="text" name="txt_tax" id="txt_tax" size="15"></td>
  </tr>
</table>

<hr>

<div style="text-align:right">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

<script>
$("#txt_date").datepicker({
    showOn: 'button',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});

$('#btnSave').click(function(){
    $.post('rep_car_api.php',{
        cmd: 'save',
        po_id: '<?php echo $po_id; ?>',
        txt_val: $('#txt_val').val(),
        txt_vat: $('#txt_vat').val(),
        txt_date: $('#txt_date').val(),
        txt_tax: $('#txt_tax').val(),
        other_hid_txt: '<?php echo $other_hid_txt; ?>',
        buy_from:  '<?php echo $vender_name; ?>',
		vender_id:  '<?php echo $vender_id; ?>',
        car_id: '<?php echo $car_id; ?>',
        car_name: '<?php echo $car_name; ?>',
        car_num: '<?php echo $car_num; ?>',
        license_plate: '<?php echo $license_plate; ?>',
		car_idno: '<?php echo $car_idno; ?>'
    },
    function(data){
        if(data.success){
            ShowPrint( data.genno );
            $('#divdialog').remove();
            //alert(data.message);
            //location.reload();
        }else{
            alert(data.message);
        }
    },'json');
});


function ShowPrint(id){
    $('body').append('<div id="div_print"></div>');
    $('#div_print').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบสำคัญปรับปรุง\" onclick=\"javascript:window.open('../report/cert_update.php?id="+ id +"','d92jd8sh19','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:close_prnt()\"></div>");
    $('#div_print').dialog({
        title: 'พิมพ์เอกสาร : '+id,
        resizable: false,
        modal: true,  
        width: 400,
        height: 150,
        close: function(ev, ui){
            $('#div_print').remove();
        }
    });
}

function close_prnt(){
    $('#div_print').remove();
    location.reload();
}
</script>
<?php
}

elseif($cmd == "save"){
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();

    $po_id=$_POST['po_id'];
    $txt_val=$_POST['txt_val'];
    $txt_vat=$_POST['txt_vat'];
    $txt_date=$_POST['txt_date'];
    $txt_tax=$_POST['txt_tax'];
    $other_hid_txt=$_POST['other_hid_txt']; if( $other_hid_txt == "ไม่พบข้อมูลการชำระ" ){ $other_hid_txt = ""; }
    $other_hid_txt = str_replace("<br />", "\n", $other_hid_txt);
    
    $buy_from =$_POST['buy_from'];
	$vender_id =$_POST['vender_id'];
    $car_id =$_POST['car_id'];
    
    $car_name=$_POST['car_name'];
    $car_num=$_POST['car_num'];
    $license_plate=$_POST['license_plate'];
	$car_idno=$_POST['car_idno'];
    
    if( $po_id=="" OR $txt_val=="" OR $txt_vat=="" OR $txt_date=="" OR $txt_tax=="" OR $buy_from=="" OR $car_id=="" OR $car_name=="" OR $car_num=="" OR $vender_id==""){
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "กรุณากรอกข้อมูลให้ครบถ้วน ! $po_id,$txt_val,$txt_vat,$txt_date,$txt_tax,$buy_from,$car_id,$car_name,$car_num,$car_idno,$vender_id ";
        echo json_encode($data);
        exit;
    }

    $gen_no=@pg_query("select account.gen_no('$txt_date','AP')");
    $gen_no=@pg_fetch_result($gen_no,0);

//ข้อความที่แสดงในรายงาน ภาษีซื้อ - รายละเอียด
    $acb_detail = "ค่ารถยนต์ยี่ห้อ : $car_name , เลขตัวถัง : $car_num, เลขสต๊อก : $car_idno, 
ชำระโดย $other_hid_txt";
    
    $qry_vatb=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='NWGD'");
    if($res_vatb=@pg_fetch_array($qry_vatb)){
        $NWGD = $res_vatb["AcID"];
    }
    if(@empty($NWGD)){
        $txt_error[] = "NOT SELECT NWGD";
        $status++;
    }
    
    $qry_vatb=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='VATB'");
    if($res_vatb=@pg_fetch_array($qry_vatb)){
        $VATB = $res_vatb["AcID"];
    }
    if(@empty($VATB)){
        $txt_error[] = "NOT SELECT VATB";
        $status++;
    }
	
	 $qry_vatb=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='CUR2'");
    if($res_vatb=@pg_fetch_array($qry_vatb)){
        $CUR2 = $res_vatb["AcID"];
    }
    if(@empty($CUR2)){
        $txt_error[] = "NOT SELECT CUR2";
        $status++;
    }
	
    /* comment ไว้เพราะไม่แน่ใจว่า AcType DEP ถูกต้องหรือป่าว
    $qry_vatb=@pg_query("SELECT \"AcID\" FROM account.\"AcTable\" WHERE \"AcType\"='DEP'");
    if($res_vatb=@pg_fetch_array($qry_vatb)){
        $DEP = $res_vatb["AcID"];
    }
    if(@empty($DEP)){
        $txt_error[] = "NOT SELECT DEP";
        $status++;
    }*/
    
    $in_sql="insert into account.\"AccountBookHead\" (type_acb,acb_id,acb_date,acb_detail,sub_type,ref_id) values ('AP','$gen_no','$txt_date','$acb_detail',DEFAULT,'VATB')";
    if(!$res_in_sql=@pg_query($in_sql)){
        $txt_error[] = "INSERT AccountBookHead $in_sql";
        $status++;
    }
        
    $in_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") values  
    ('$gen_no','$NWGD','$txt_val','0',DEFAULT)";
    if(!$res_in_sql=@pg_query($in_sql)){
        $txt_error[] = "INSERT AccountBookDetail1 $in_sql<br />";
        $status++;
    }
    
    $in_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") values  
    ('$gen_no','$VATB','$txt_vat','0',DEFAULT)";
    if(!$res_in_sql=@pg_query($in_sql)){
        $txt_error[] = "INSERT AccountBookDetail2 $in_sql<br />";
        $status++;
    }
    
    $sum_all_amt = round($txt_val+$txt_vat,2);
    $in_sql="insert into account.\"AccountBookDetail\" (acb_id,\"AcID\",\"AmtDr\",\"AmtCr\",\"RefID\") values  
    ('$gen_no','$CUR2','0','$sum_all_amt',DEFAULT)";
    if(!$res_in_sql=@pg_query($in_sql)){
        $txt_error[] = "INSERT AccountBookDetail3 $in_sql<br />";
        $status++;
    }
    
    $in_sql="insert into account.\"BookBuy\" (bh_id,buy_from,buy_receiptno,pay_buy,to_hp_id,vender_id) values  
    ('$gen_no','$buy_from','$txt_tax','$other_hid_txt','$car_id','$vender_id')";
    if(!$res_in_sql=@pg_query($in_sql)){
        $txt_error[] = "INSERT BookBuy $in_sql<br />";
        $status++;
    }
    
    $qry="UPDATE \"Cars\" SET cost_val='$txt_val',cost_vat='$txt_vat' WHERE car_id='$car_id' ";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "UPDATE Cars ไม่สำเร็จ $qry";
        $status++;
    }

    if($status == 0){
        pg_query("COMMIT");
        //pg_query("ROLLBACK");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
        $data['genno'] = $gen_no;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }

    echo json_encode($data);
    
}

elseif($cmd == "show_other"){
    echo "กำลังจัดทำ !";
}

elseif($cmd == "show_reprint"){
?>
<div style="margin-top:10px">
<b>ระบุ ID</b> <input type="text" name="txt_no" id="txt_no" size="25" >
<input type="button" id="btn_print" name="btn_print" value="พิมพ์">
</div>

<script>
$("#txt_no").autocomplete({
    source: "rep_car_api.php?cmd=cert_autocomplete",
    minLength:1
});

$('#btn_print').click(function(){
    var id = $('#txt_no').val();
    if(id == ""){
        alert('กรุณากรอกเลข ID !');
        return false;
    }
    window.open('../report/cert_update.php?id='+ id +'','d92jd8sh19','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');
});
</script>

<?php
}

elseif($cmd == "cert_autocomplete"){
    $term = $_GET['term'];

    $qry_name=pg_query("SELECT * FROM account.\"AccountBookHead\" WHERE acb_id LIKE '%$term%' ORDER BY acb_id ASC ");
    $numrows = pg_num_rows($qry_name);
    while($res_name=pg_fetch_array($qry_name)){
        $acb_id = $res_name["acb_id"];
        
        $dt['value'] = $acb_id;
        $dt['label'] = "{$acb_id}";
        $matches[] = $dt;
    }

    if($numrows==0){
        $matches[] = "ไม่พบข้อมูล";
    }

    $matches = array_slice($matches, 0, 100);
    print json_encode($matches);
}
?>