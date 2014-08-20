<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$q = $_GET["q"];
$pagesize = 10; // จำนวนรายการที่ต้องการแสดง
$sql = "select * from  account.\"AccountBookHead\" where acb_id like '%$q%' LIMIT 10  ";
$results=pg_query($sql);						 
$nrows=pg_num_rows($results);
if($nrows==0)
	{
	  $name="ไม่พบข้อมูล";
	  $display_name = preg_replace("/(" . $q . ")/i", "<b>$1</b>", $name);
	  echo "<li onselect=\"this.setText('$name'); \">$display_name</li>";
	 
	}
	else
	{						 
						 
		
		while($row = pg_fetch_array( $results )) 
		{
			$am_id = $row["acb_id"]; // ฟิลที่ต้องการส่งค่ากลับ
			$a_date=trim($row["acb_date"]);
			$a_dtls =trim($row["acb_detail"]);
			$a_dtl=substr($a_dtls,0,120)."...";
			
			
			
		
			// ป้องกันเครื่องหมาย '
			$name = str_replace("'", "\'",$am_id."  "." / ".$a_date." / ".$a_dtl);
			$upname= str_replace("'", "\'",$am_id."  ");
			// กำหนดตัวหนาให้กับคำที่มีการพิมพ์
			$display_name = preg_replace("/(" . $q . ")/i", "<b>$1</b>", $name);
			echo "<li onselect=\"this.setText('$upname').setValue('$am_id'); \">$display_name</li>";
		 }
     }
?>