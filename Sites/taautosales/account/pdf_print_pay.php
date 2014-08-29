<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

require('../thaipdfclass.php');

$comp_name=$company_name; 
$p_mon = pg_escape_string($_GET["mmon"]);
$p_year = pg_escape_string($_GET["myear"]);
$p_type = pg_escape_string($_GET["mtype"]);

//echo $p_mode." - ".$p_year." - ".$p_type;


$pdf=new ThaiPDF('P' ,'mm','slip_av');  
$pdf->SetLeftMargin(0);
$pdf->SetTopMargin(0);
$pdf->SetThaiFont();


$qry_m=pg_query("select * from account.\"AccountBookHead\" where (EXTRACT(YEAR FROM acb_date)='$p_year')AND (EXTRACT(MONTH FROM acb_date)='$p_mon') AND (type_acb='$p_type') AND (cancel=false) ORDER BY acb_id ");
  while($res_m=pg_fetch_array($qry_m))
  {
    $a_id=$res_m["acb_id"];
	$as_date=$res_m["acb_date"];
	
	$trn_date=pg_query("select * from c_date_number('$as_date')");
	$a_date=pg_fetch_result($trn_date,0);
	 
	$pdf->AddPage();
    $pdf->SetFont('AngsanaNew','B',22);
	$pdf->SetXY(15,10);
	$head_comp=iconv('UTF-8','windows-874',$comp_name);
	$pdf->MultiCell(180,6,$head_comp,0,'C',0);
	$pdf->SetFont('AngsanaNew','B',20);
	$pdf->SetXY(15,18);
    
    if( substr($a_id,0,2) == "AJ" ){
        $head_imp=iconv('UTF-8','windows-874',"ใบสำคัญปรับปรุง");
    }else{
        $head_imp=iconv('UTF-8','windows-874',"ใบสำคัญจ่าย");
    }
    
	$pdf->MultiCell(180,6,$head_imp,0,'C',0);

	
	$pdf->SetFont('AngsanaNew','',16);
	$str_number="เลขที่ ";
	$str_date=iconv('UTF-8','windows-874',"วันที่  ");
	$icon_daterec=iconv('UTF-8','windows-874',$str_number);
	$pdf->Text(15,26.0+$arow,$str_date.$a_date); //วันที่ใบเสร็จ
	$pdf->Text(155,26.0+$arow,$icon_daterec.$a_id); //เลขที่ใบเสร็จ

       $pdf->SetXY(15,32);
	   $is_acid=iconv('UTF-8','windows-874'," รายการ" );
	   $pdf->MultiCell(100,8,$is_acid,1,'C',0);
	  
	   $pdf->SetXY(115,32);//Dr
	   $is_dr=iconv('UTF-8','windows-874',Dr);
	   $pdf->MultiCell(35,8,$is_dr,1,'C',0);
	   
	   $pdf->SetXY(150,32);//Cr
	   $is_cr=iconv('UTF-8','windows-874',Cr);
	   $pdf->MultiCell(35,8,$is_cr,1,'C',0);
	   $crow=$crow+8;


    
     $crow=0;
	 $qry_vacc=pg_query("select *  from account.\"VAccountBook\" where acb_id='$a_id'  ORDER BY \"AmtDr\" DESC ");
	 while($res_vacc=pg_fetch_array($qry_vacc))
	 {
	     
	   $v_acname=$res_vacc["AcName"];
	   $v_acid=" [ ".$res_vacc["AcID"]." ]"." ".$v_acname;
	   $v_dr=number_format($res_vacc["AmtDr"],2);
	   $v_cr=number_format($res_vacc["AmtCr"],2);
	      
	    $vs_dt=$res_vacc["acb_detail"];
	   
	    $exp_dtl=str_replace("\n","#",$vs_dt);
	    $sep_dtl=explode("#",$exp_dtl);
	    
		$sp_dtl=str_replace("\n"," ",$vs_dt); 
	   
	   
	   
	   $pdf->SetXY(15,40+$crow);
	   $i_acid=iconv('UTF-8','windows-874',$v_acid);
	   $pdf->MultiCell(100,8,$i_acid,1,'L',0);
	  
	   $pdf->SetXY(115,40+$crow);//Dr
	   $i_dr=iconv('UTF-8','windows-874',$v_dr);
	   $pdf->MultiCell(35,8,$i_dr,1,'R',0);
	   
	   $pdf->SetXY(150,40+$crow);//Cr
	   $i_cr=iconv('UTF-8','windows-874',$v_cr);
	   $pdf->MultiCell(35,8,$i_cr,1,'R',0);
	   $crow=$crow+8;
	  
	  $total_str=count($sep_dtl);
	   for($is=$total_str-1;$is<$total_str;$is++)
		{
		  $res_i=$sep_dtl[$is];
		} 
	 
	 } 
	   $pdf->SetXY(15,40+$crow);//Detail
	   $i_dt=iconv('UTF-8','windows-874',"  ".$sp_dtl);
	   $pdf->MultiCell(170,10,$i_dt,1,'L',0);
	   
	   $pdf->SetXY(15,105);//Detail
	   $i_pre=iconv('UTF-8','windows-874',"ผู้เตรียม ___________________ ");
	   $pdf->MultiCell(55,10,$i_pre,0,'L',0);
	   
	   $pdf->SetXY(72,105);//Detail
	   $i_pre=iconv('UTF-8','windows-874',"ผู้ตรวจสอบ _________________ ");
	   $pdf->MultiCell(55,10,$i_pre,0,'L',0);
	   
	   $pdf->SetXY(130,105);//Detail
	   $i_pre=iconv('UTF-8','windows-874',"ผู้อนุมัติจ่าย _________________");
	   $pdf->MultiCell(55,10,$i_pre,0,'L',0);
	   
	 
  }		
$pdf->Output(); 		
/*
$pdf=new ThaiPDF('P' ,'mm','slip_av');  
$pdf->SetLeftMargin(0);
$pdf->SetTopMargin(0);
$pdf->SetThaiFont();

*/


/*
  $pdf->AddPage();
  $pdf->SetFont('AngsanaNew','',16);
		
  $col=10;
		
		$pdf->Text(163,25.0+$arow,$rec_id); //เลขที่ใบเสร็จ

$pdf->Output();
*/

?>