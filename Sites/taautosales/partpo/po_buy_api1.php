<?php
//------------------------------------------------- สั่งซื้ออุปกรณ์อื่นๆ ------------------------------------------------------------
?>

<script>
  $(function() {
    $( ".datepicker" ).datepicker();
  });
</script>

<div style="font-size:12px">

<div style="margin: 10px 0 5px 0">
<b>วันที่ใบสั่งซื้อ :</b>
<input type="text" name="date" id="date" class="datepicker" />
</div>


<div style="margin: 10px 0 5px 0">
<b>ประเภทของ PO :</b>
<select name="type" id="type">
	<option value="1">สั่งซื้อของใหม่ (จาก Supplier)</option>
	<option value="2">สั่งซื้อของเก่า (จาก อะไหล่เก่า)</option>
</select>
</div>


<div style="margin: 10px 0 5px 0">
<b>เลขที่ใบสั่งซื้อที่ต้องการคัดลอก :</b>
<input type="text" name="copypo_id" id="copypo_id" />
</div>


<div style="margin: 10px 0 5px 0">
<b>กำหนดชำระเงิน :</b>
<select name="credit_terms" id="credit_terms">
	<option value="15">15</option>
	<option value="30">30</option>
	<option value="60">60</option>
	<option value="90">90</option>
</select>
</div>

<div style="margin: 10px 0 5px 0">
<b>วันที่นัดส่งของ :</b>
<input type="text" name="app_sentpartdate" id="app_sentpartdate" class="datepicker" />
</div>


<div style="margin: 10px 0 5px 0">
<b>ประมาณการวันที่ชำระเงิน :</b>
<input type="text" name="esm_paydate" id="esm_paydate" class="datepicker" />
</div>


<div style="margin: 10px 0 5px 0">
<b>ผู้ขาย :</b>
<select name="vender_id" id="vender_id">
	<option value="0"></option>
</select>
</div>


<div style="margin: 10px 0 5px 0">
<b>ใบสั่งซื้อนี้ คิด :</b>
<select name="vat_status" id="vat_status">
	<option value="1">คิด VAT</option>
	<option value="0">ไม่คิด VAT</option>
</select>
</div>




<div style="margin: 10px 0 5px 0">
<b>ผู้ขาย :</b>
<select name="combo_warehouse" id="combo_warehouse">
<?php
$qry = pg_query("SELECT * FROM \"VVenders\" WHERE type_ven = 'M' or type_ven='B' ORDER BY pre_name,cus_name ASC");
while( $res = pg_fetch_array($qry) ){
    $vender_id = $res['vender_id'];
    $pre_name = trim($res['pre_name']);
    $cus_name = trim($res['cus_name']);
    $surname = trim($res['surname']);
	$branch_id = trim($res['branch_id']);
?>
    <option value="<?php echo $vender_id; ?>"><?php echo "$pre_name $cus_name $surname"; if($branch_id != ""){echo "( $branch_id )"; } ?></option>
<?php
}
?>
</select>
</div>








</div>
</div>