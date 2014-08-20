<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
<?php
include_once("../include/config.php");

function checknull($data){
	if($data == ""){		
		$a1 = "null";	
	}else{	
		$a1 = "'".$data."'";	
	}
	return $a1;
}
function ddmmyy_to_yymmdd($date){
	if($date != ""){
		list($dd,$mm,$yy) = explode("/",$date);
		return $yy."-".$mm."-".$dd;
	}else{
		return "";
	}
}
pg_query("BEGIN");
$status=0;

$strFileName = "Products.csv"; // ที่เก็บไฟล์ CSV
$rowsData = 17; // จำนวนแถวของข้อมูลทั้งหมด รวมหัวข้อด้วย

if (!file_exists($strFileName))
{ //file_exists() ตรวจสอบว่ามีไฟล์ชื่อนี้อยู่แล้วหรือไม่ 
	echo $strFileName."<br>"."<b>"."ไม่พบไฟล์ดังกล่าว"."<b>";
	exit;
}
else
{
	$objFopen=fopen($strFileName,"r");
	if($objFopen)
	{
		$i=0;
		while (!feof($objFopen))
		{
			$buffer = fgets($objFopen, 4096);
			$buffer=iconv('TIS-620', 'UTF-8', $buffer);
			
			$i++;

			//เริ่มบันทึกข้อมูล DETAIL
			if($i>1)
			{
				$objdata=explode(",",$buffer); //สำหรับตรวจสอบข้อมูลของแถว
				
				$product_id = checknull(trim($objdata[0]));
				$name = checknull(trim($objdata[1]));
				$cost_price = checknull(trim($objdata[2]));
				$sale_price = checknull(trim($objdata[3]));
				$use_vat = trim($objdata[4]);
				$type_rec = checknull(trim($objdata[5]));
				$link_table= checknull(trim($objdata[6]));
				$cancel= trim($objdata[7]);
				
				
				//$res_genpost=checknull($res_genpost);
				
				$insrow="INSERT INTO \"Products\" (product_id,name,cost_price,sale_price,use_vat,type_rec,link_table,cancel) values ($product_id,$name,$cost_price,$sale_price,$use_vat,$type_rec,$link_table,$cancel)";
						
				if($resinjrow=pg_query($insrow)){
				}else{
					$status++;
					echo "<br>$insrow : $i</br>";
				}
			}
			
			if($i == $rowsData)
			{ // ถ้าทำถึงแถวสุดท้ายแล้วให้หยุด
				break;
			}
		} //end while
		
		fclose($objFopen);
	}
	else
	{
		echo "<br><b>"."ไม่สามารถเปิดไฟล์ได้ กรุณาตรวจสอบ"."<b>";	
	}
}

if($status==0)
{	
	pg_query("COMMIT");
	echo "<br>"."<b>"."บันทึกข้อมูลเรียบร้อย  "."</b>";
}
else
{
	pg_query("ROLLBACK");
	echo "มีข้อผิดพลาดในการบันทึก กรุณาทำรายการใหม่";
}

?>