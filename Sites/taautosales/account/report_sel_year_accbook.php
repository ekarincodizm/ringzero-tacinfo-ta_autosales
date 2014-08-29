<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$fs_mon=$_GET["qry1"];
$fs_year=$_GET["qry2"];

	$sql_acc="select DISTINCT acb_id,type_acb,acb_date,acb_detail from account.\"VAccountBook\" WHERE (EXTRACT(YEAR FROM \"acb_date\")='$fs_year') AND (EXTRACT(MONTH FROM \"acb_date\")='$fs_mon') AND (type_acb !='ZZ') AND (type_acb !='AA')  ORDER BY acb_date  ";
	


$nowdate = date('Y/m/d');



//------------------- PDF -------------------//
require('../thaipdfclass.php');


class PDF extends ThaiPDF
{

    function Header()    {
        $this->SetFont('AngsanaNew','',12);
        $this->SetXY(10,16); 
        $buss_name=iconv('UTF-8','windows-874',"หน้า ".$this->PageNo()."/tp");
        $this->MultiCell(192,4,$buss_name,0,'R',0);
 
    }
 
}


$pdf=new PDF('P' ,'mm','a4');
$pdf->SetLeftMargin(0);
$pdf->SetTopMargin(0);
$pdf->AliasNbPages( 'tp' );
$pdf->SetThaiFont();
$pdf->AddPage();

$page = $pdf->PageNo();

$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',"รายงานสมุดบัญชี".$st_m.$st_y);
$pdf->MultiCell(180,4,$title,0,'C',0);

$pdf->SetFont('AngsanaNew','',12);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(180,4,$buss_name,0,'C',0);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ประจำปี ".$fs_year);
$pdf->MultiCell(180,4,$buss_name,0,'L',0);

$pdf->SetXY(0,23);
$buss_name=iconv('UTF-8','windows-874'," วันที่พิมพ์ $nowdate");
$pdf->MultiCell(202,4,$buss_name,0,'R',0);

$pdf->SetXY(4,24); 
$buss_name=iconv('UTF-8','windows-874',"_____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'L',0);

$pdf->SetXY(5,30); 
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(10,4,$buss_name,0,'C',0);

$pdf->SetXY(32,30); 
$buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
$pdf->MultiCell(20,4,$buss_name,0,'L',0);


$pdf->SetXY(55,30); 
$buss_name=iconv('UTF-8','windows-874',"รายการ");
$pdf->MultiCell(100,4,$buss_name,0,'L',0);

$pdf->SetXY(150,30); 
$buss_name=iconv('UTF-8','windows-874',"Dr");
$pdf->MultiCell(30,4,$buss_name,0,'L',0);

$pdf->SetXY(180,30); 
$buss_name=iconv('UTF-8','windows-874',"Cr");
$pdf->MultiCell(30,4,$buss_name,0,'L',0);


$pdf->SetXY(4,32); 
$buss_name=iconv('UTF-8','windows-874',"_____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'L',0);

$pdf->SetFont('AngsanaNew','',10);
$cline = 37;
$i = 1;


    
	

	

	
	$qry_m=pg_query($sql_acc);
    
    while($res_m=pg_fetch_array($qry_m))
   	{
	    $qri++;
		$j=0;
	    $ss=$res_m["acb_id"];
		$sName=$res_m["acb_detail"];
	    $sql_ls=pg_query("select * from account.\"VAccountBook\" WHERE acb_id='$ss' order by \"AmtDr\" desc ");
		while($res_ls=pg_fetch_array($sql_ls))
	    {
		
		$j=$j+1;
    	$aa+=1;
    
	    $as_date=$res_ls["acb_date"];
		$trn_date=pg_query("select * from c_date_number('$as_date')");
	    $a_date=pg_fetch_result($trn_date,0);
	
        //
        $a_type=$res_ls["AcID"];
		$a_name=$res_ls["AcName"];
		$a_dr=$res_ls["AmtDr"];
		$a_cr=$res_ls["AmtCr"];
		
		
		
       if($i > 35)
        { 
            $pdf->AddPage(); $cline = 40; 
			
			$i=1; 

			$pdf->SetFont('AngsanaNew','B',15);
			$pdf->SetXY(10,10);
			$title=iconv('UTF-8','windows-874',"รายงานสมุดบัญชี");
			$pdf->MultiCell(180,4,$title,0,'C',0);
			
			$pdf->SetFont('AngsanaNew','',12);
			$pdf->SetXY(10,16);
			$buss_name=iconv('UTF-8','windows-874',$company_name);
			$pdf->MultiCell(180,4,$buss_name,0,'C',0);
			
			$pdf->SetXY(5,23);
			$buss_name=iconv('UTF-8','windows-874',"ประจำปี ".$year_ac);
			$pdf->MultiCell(180,4,$buss_name,0,'L',0);
			
			$pdf->SetXY(0,23);
			$buss_name=iconv('UTF-8','windows-874'," วันที่พิมพ์ $nowdate");
			$pdf->MultiCell(202,4,$buss_name,0,'R',0);
			
			$pdf->SetXY(4,24); 
			$buss_name=iconv('UTF-8','windows-874',"_____________________________________________________________________________________________________________________________________________");
			$pdf->MultiCell(200,4,$buss_name,0,'L',0);
			
			$pdf->SetXY(5,30); 
			$buss_name=iconv('UTF-8','windows-874',"วันที่");
			$pdf->MultiCell(10,4,$buss_name,0,'C',0);
			
			$pdf->SetXY(32,30); 
			$buss_name=iconv('UTF-8','windows-874',"รหัสบัญชี");
			$pdf->MultiCell(20,4,$buss_name,0,'L',0);
			
			
			$pdf->SetXY(55,30); 
			$buss_name=iconv('UTF-8','windows-874',"รายการ");
			$pdf->MultiCell(100,4,$buss_name,0,'L',0);
			
			$pdf->SetXY(150,30); 
			$buss_name=iconv('UTF-8','windows-874',"Dr");
			$pdf->MultiCell(30,4,$buss_name,0,'L',0);
			
			$pdf->SetXY(180,30); 
			$buss_name=iconv('UTF-8','windows-874',"Cr");
			$pdf->MultiCell(30,4,$buss_name,0,'L',0);
			
			
			
			$pdf->SetXY(4,32); 
			$buss_name=iconv('UTF-8','windows-874',"_____________________________________________________________________________________________________________________________________________");
			$pdf->MultiCell(200,4,$buss_name,0,'L',0);
		}

$pdf->SetFont('AngsanaNew','',14); 


$pdf->SetXY(3,$cline); 
$buss_name=iconv('UTF-8','windows-874',$a_date);
$pdf->MultiCell(20,4,$buss_name,0,'C',0);

$pdf->SetXY(32,$cline); 
$buss_name=iconv('UTF-8','windows-874',$a_type);
$pdf->MultiCell(30,4,$buss_name,0,'L',0);

$pdf->SetXY(55,$cline); 
$buss_name=iconv('UTF-8','windows-874',$a_name);
$pdf->MultiCell(100,4,$buss_name,0,'L',0);

$pdf->SetXY(140,$cline); 
$buss_name=iconv('UTF-8','windows-874',$a_dr);
$pdf->MultiCell(25,4,number_format($buss_name,2),0,'R',0);

$pdf->SetXY(170,$cline); 
$buss_name=iconv('UTF-8','windows-874',$a_cr);
$pdf->MultiCell(25,4,number_format($buss_name,2),0,'R',0);


 
  

	 
  


$cline+=5; 

$i+=1;  

   


}
 
   

 $exp_dtl=str_replace("\n","#",$sName);
 $ep_dtl=explode("#",$exp_dtl);
 $total_str=count($ep_dtl);
	 	 
	 
		for($is=$total_str-1;$is<$total_str;$is++)
		{
		  $res_i=$ep_dtl[$is];
		} 
	$cline+=5;

/*
$count_arr_sName = count($arr_sName);
$nub = 0;
foreach($arr_sName AS $v){
    if(!empty($v)){
        $nub++;
        $pdf->SetXY(30,$cline);
        if($nub==1)
            $buss_name=iconv('UTF-8','windows-874',$ss." ".$v);
        else
            $buss_name=iconv('UTF-8','windows-874',$v);
        $pdf->MultiCell(170,4,$buss_name,0,'L',0);

        $cline+=5;
        $i+=1;
/*/	


$cline-=5;
$i+=1;

$sName = str_replace("<br />", "\n", $sName);
	
	 if($j==2)
	{
	//$cline=$cline;
    $pdf->SetXY(3,$cline);  
	$buss_name=iconv('UTF-8','windows-874'," ");
	$pdf->MultiCell(20,4,$buss_name,0,'C',0);
	
	$cline=$cline+5;
	
	$pdf->SetXY(20,$cline);	
$buss_name=iconv('UTF-8','windows-874',$ss."     ".$sName);
	$pdf->MultiCell(180,4,$buss_name,0,'L',0);	

	
    }
	else
	{
	$pdf->SetXY(20,$cline);	
$buss_name=iconv('UTF-8','windows-874',$ss."     ".$sName);
	$pdf->MultiCell(180,4,$buss_name,0,'L',0);	
	
	}

/*
$pdf->SetXY(20,$cline);	
$buss_name=iconv('UTF-8','windows-874',$ss."     ".$res_i);
	$pdf->MultiCell(180,4,$buss_name,0,'L',0);	
*/	



//$cline+=10; 

//$i+=1;


$arr_nub_line_plus = explode("\n", $sName);
$arr_nub_line_plus = count( $arr_nub_line_plus );

$cline += $arr_nub_line_plus*6;
$i+=$arr_nub_line_plus;

$pdf->SetXY(5,$cline-5); 
$buss_name=iconv('UTF-8','windows-874',"______________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'L',0);

}

$pdf->SetFont('AngsanaNew','',12);

$pdf->SetXY(5,$cline-2); 
$buss_name=iconv('UTF-8','windows-874',
"_____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'L',0);

$pdf->SetFont('AngsanaNew','B',11);


$pdf->SetXY(5,$cline+3); 
$buss_name=iconv('UTF-8','windows-874',"ทั้งหมด $qri รายการ ");
$pdf->MultiCell(50,4,$buss_name,0,'L',0);        
        


$pdf->Output();
?>