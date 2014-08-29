<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
$type = pg_escape_string($_GET['type']);

if($cmd == "changetype"){
    $type = $_GET['type'];
	if($type == 2 ){
        $data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
?>
        <select name="cb_month" id="cb_month">
        <?php
        foreach ($data_month as $k => $v){
            if(date('m') == $k){
                echo "<option value=\"$k\" selected>$v</option>";
            }else{
                echo "<option value=\"$k\">$v</option>";
            }
        }
        ?>
        </select>
        
        <select name="cb_year" id="cb_year">
        <?php
        $year_plus = $nowyear + 5; 
        $year_lob =  $nowyear - 5;

        for($i=$year_lob; $i<=$year_plus; $i++){
            if($nowyear == $i){
                echo "<option value=\"$i\" selected>$i</option>";
            }else{
                echo "<option value=\"$i\">$i</option>";
            }
        }
        ?>
        </select>
<?php
    }
    elseif($type == 1){
?>
		<input type="text" name="txt_date" id="txt_date" value="<?php echo $nowdate; ?>" style="width:80px; text-align:center">
	    <script type="text/javascript">
        $("#txt_date").datepicker({
            showOn: 'button',
            buttonImage: '../images/calendar.gif',
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
        </script>	
<?php
    }
}
elseif($cmd == "report"){
    $type = $_GET['type'];
	//echo "$cmd$type";
	
    if($type == 1){
        $date = $_GET['date'];
		$qry = "SELECT cheque_no,bank_name,bank_branch,sum(amt_on_cheque) as amt_on_cheque,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date 
		FROM \"Cheques\" WHERE date_enter_bank = '$date' 
		GROUP BY cheque_no,bank_name,bank_branch,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date
		ORDER BY cheque_no ASC ";
        $pdf = "report_cheque_in_pdf.php?cmd=report&type=1&date=$date";
		}
	elseif($type == 2){
	    $data_month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
        $cb_month = $_GET['cb_month'];
        $cb_year = $_GET['cb_year'];
		$qry = "SELECT cheque_no,bank_name,bank_branch,sum(amt_on_cheque) as amt_on_cheque,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date 
		FROM \"Cheques\" WHERE EXTRACT(MONTH FROM \"date_enter_bank\") = '$cb_month' AND EXTRACT(YEAR FROM \"date_enter_bank\") = '$cb_year'
		GROUP BY cheque_no,bank_name,bank_branch,receive_date,date_on_cheque,out_bangkok,reenter_date,num_reenter,is_return,accept,accept_by_user,is_pass,pass_by_user,date_enter_bank,acc_bank_enter,memo,cancel,is_transfer,pass_date ";
		$type_txt = "รายงานนำเช็คเข้าประจำเดือน".$data_month[$cb_month]." ปี $cb_year";
        $pdf = "report_cheque_in_pdf.php?cmd=report&type=2&cb_month=$cb_month&cb_year=$cb_year";
		
    }
?>	
<div style="text-align:right">
<a href="<?php echo $pdf; ?>" target="_blank">
<span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span>
</a>
</div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่เช็ค</td>
    <td>ธนาคาร</td>
    <td>สาขา</td>
    <td>วันที่บนเช็ค</td>
	<td>วันที่นำเข้า</td>
	<td>วันที่ผ่าน</td>
    <td>จำนวนเงิน</td>
    <td>สถานะเช็ค</td>
</tr>

<?php
		$j = 0;
		$sum = 0;
		
		//echo "AAA$qry";
		
		$qry = pg_query($qry);
		while($res = pg_fetch_array($qry))
		{
		$j++;
		$cheque_no = $res['cheque_no'];
		$bank_name = $res['bank_name'];
		$bank_branch = $res['bank_branch'];
		$date_on_cheque = $res['date_on_cheque'];
		$amt_on_cheque = $res['amt_on_cheque'];
		$is_pass = $res['is_pass'];
		$date_enter_bank = $res['date_enter_bank'];
		$pass_date = $res['pass_date'];

		$sum += $amt_on_cheque;

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>

	<td align="center"><?php echo $cheque_no; ?></td>
    <td><?php echo $bank_name; ?></td>
    <td><?php echo $bank_branch; ?></td>
    <td align="center"><?php echo $date_on_cheque; ?></td>
	<td align="center"><?php echo $date_enter_bank; ?></td>
	<td align="center"><?php echo $pass_date; ?></td>
    <td align="right"><?php echo number_format($amt_on_cheque,2); ?></td>
    <td align="center"><?php if($is_pass == "f"){ echo "รอ"; }else{ echo "ผ่าน"; } ?></td>
</tr>
	<?php
	}

	if($j == 0){
		echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
	}else{
		echo "<tr bgcolor=\"#FFFFD9\"><td colspan=\"6\"><b>รวม</b></td><td align=\"right\"><b>".number_format($sum,2)."</b></td><td></td></tr>";
	}
?>
</table>	
<?php
}
?>	







