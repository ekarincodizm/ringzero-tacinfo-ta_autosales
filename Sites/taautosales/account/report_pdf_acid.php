<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$f_acid = pg_escape_string($_GET["qry1"]);
$f_year = pg_escape_string($_GET["qry2"]);
$f_month = pg_escape_string($_GET["qry3"]);
$f_name = pg_escape_string($_GET["m_name"]);

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
$title=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(180,4,$title,0,'C',0);

$pdf->SetFont('AngsanaNew','',14);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',"รายงานสมุดบัญชีแยกประเภท ");
$pdf->MultiCell(180,4,$buss_name,0,'C',0);

$pdf->SetFont('AngsanaNew','',12);
$pdf->SetXY(10,23);
$buss_name=iconv('UTF-8','windows-874',"ชื่อบัญชี ".$f_name." [ ".$f_acid." ] ");
$pdf->MultiCell(180,4,$buss_name,0,'C',0);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ประจำปี ".$f_year." เดือน ".$f_month);
$pdf->MultiCell(180,4,$buss_name,0,'L',0);

$pdf->SetXY(0,23);
$buss_name=iconv('UTF-8','windows-874'," วันที่พิมพ์ $nowdate");
$pdf->MultiCell(202,4,$buss_name,0,'R',0);

$pdf->SetXY(4,24); 
$buss_name=iconv('UTF-8','windows-874',"____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

$pdf->SetXY(5,30); 
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(10,4,$buss_name,0,'C',0);

$pdf->SetXY(32,30); 
$buss_name=iconv('UTF-8','windows-874',"เลขที่รายการ");
$pdf->MultiCell(50,4,$buss_name,0,'L',0);


$pdf->SetXY(90,30); 
$buss_name=iconv('UTF-8','windows-874',"Dr");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);

$pdf->SetXY(125,30); 
$buss_name=iconv('UTF-8','windows-874',"Cr");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);

$pdf->SetXY(160,30); 
$buss_name=iconv('UTF-8','windows-874',"Bl");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);


$pdf->SetXY(4,32); 
$buss_name=iconv('UTF-8','windows-874',"____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

$pdf->SetFont('AngsanaNew','',10);
$cline = 37;
$i = 1;
$j = 0;

    $gri=0;

	$sql_acid=pg_query("select * from account.\"VAccountBook\" WHERE  (EXTRACT(YEAR FROM \"acb_date\")='$f_year') and (EXTRACT(MONTH FROM \"acb_date\")='$f_month') and (\"AcID\"='$f_acid') and (type_acb!='zz') ORDER BY acb_date,type_acb,acb_id ");
  while($res_acb=pg_fetch_array($sql_acid))
  {
     $gri++;
	 $as_date=$res_acb["acb_date"];
	 $res_acid=$res_acb["acb_id"]; 
	 $res_dr=$res_acb["AmtDr"];
	 $res_cr=$res_acb["AmtCr"];
	 if(($res_cr==0) and ($res_dr!=0))
	 {
	 $total_sum_bl=$total_bl+$res_dr;
	 }
	 else
	 {
	  $total_sum_bl=$total_bl-$res_cr;
	 }

      $total_bl=$total_sum_bl;

	
	//insert Line //
	 $sline=$res_acb["type_acb"];
	 if($sline=="ZZ")
	 {
	   $prn_line=1;
	 }
	 else
	 {
	   $prn_line=0;
	 } 
	
	
	
	
	/*
	$qry_m=pg_query($sql_acc);
    
    while($res_m=pg_fetch_array($qry_m))
   	{
	    $qri++;
	    $ss=$res_m["acb_id"];
		$sName=$res_m["acb_detail"];
	    $sql_ls=pg_query("select * from account.\"VAccountBook\" WHERE acb_id='$ss'");
		while($res_ls=pg_fetch_array($sql_ls))
	    {
		
		$j+=1;
    	$aa+=1;
    
        $a_date=$res_ls["acb_date"];
        $a_type=$res_ls["AcID"];
		$a_name=$res_ls["AcName"];
		$a_dr=$res_ls["AmtDr"];
		$a_cr=$res_ls["AmtCr"];
		
       
       */
		
	
    
        
if($i > 45){ 
    $pdf->AddPage(); $cline = 37; $i=1; 

$pdf->SetFont('AngsanaNew','B',15);
$pdf->SetXY(10,10);
$title=iconv('UTF-8','windows-874',$company_name);
$pdf->MultiCell(180,4,$title,0,'C',0);

$pdf->SetFont('AngsanaNew','',14);
$pdf->SetXY(10,16);
$buss_name=iconv('UTF-8','windows-874',"รายงานสมุดบัญชีแยกประเภท ");
$pdf->MultiCell(180,4,$buss_name,0,'C',0);

$pdf->SetFont('AngsanaNew','',12);
$pdf->SetXY(10,23);
$buss_name=iconv('UTF-8','windows-874',"ชื่อบัญชี ".$f_name." [ ".$f_acid." ]");
$pdf->MultiCell(180,4,$buss_name,0,'C',0);

$pdf->SetXY(5,23);
$buss_name=iconv('UTF-8','windows-874',"ประจำปี ".$f_year." เดือน ".$f_month);
$pdf->MultiCell(180,4,$buss_name,0,'L',0);
$pdf->SetXY(0,23);
$buss_name=iconv('UTF-8','windows-874'," วันที่พิมพ์ $nowdate");
$pdf->MultiCell(202,4,$buss_name,0,'R',0);

$pdf->SetXY(4,24); 
$buss_name=iconv('UTF-8','windows-874',"____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

$pdf->SetXY(5,30); 
$buss_name=iconv('UTF-8','windows-874',"วันที่");
$pdf->MultiCell(10,4,$buss_name,0,'C',0);

$pdf->SetXY(32,30); 
$buss_name=iconv('UTF-8','windows-874',"เลขที่รายการ");
$pdf->MultiCell(50,4,$buss_name,0,'L',0);


$pdf->SetXY(90,30); 
$buss_name=iconv('UTF-8','windows-874',"Dr");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);

$pdf->SetXY(125,30); 
$buss_name=iconv('UTF-8','windows-874',"Cr");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);

$pdf->SetXY(160,30); 
$buss_name=iconv('UTF-8','windows-874',"Bl");
$pdf->MultiCell(30,4,$buss_name,0,'C',0);



$pdf->SetXY(4,32); 
$buss_name=iconv('UTF-8','windows-874',"____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);
}

$pdf->SetFont('AngsanaNew','',10); 
   

$pdf->SetXY(3,$cline); 

 $trn_date=pg_query("select * from c_date_number('$as_date')");
 $a_date=pg_fetch_result($trn_date,0);

$buss_name=iconv('UTF-8','windows-874',$a_date);
$pdf->MultiCell(20,4,$buss_name,0,'C',0);

$pdf->SetXY(32,$cline); 
$buss_name=iconv('UTF-8','windows-874',$res_acid);
$pdf->MultiCell(50,4,$buss_name,0,'L',0);

$pdf->SetXY(90,$cline); 
$buss_name=iconv('UTF-8','windows-874',$res_dr);
$pdf->MultiCell(30,4,$buss_name,0,'R',0);

$pdf->SetXY(125,$cline); 
$buss_name=iconv('UTF-8','windows-874',$res_cr);
$pdf->MultiCell(30,4,number_format($buss_name,2),0,'R',0);

$pdf->SetXY(160,$cline); 
$buss_name=iconv('UTF-8','windows-874',$total_bl);
$pdf->MultiCell(30,4,number_format($buss_name,2),0,'R',0);

if($prn_line==1)
{
$pdf->SetXY(7,$cline+1.5); 
$buss_name=iconv('UTF-8','windows-874',"_____________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'L',0);
}
else
{

}



$cline+=5; 

$i+=1;       

}

$arr_sName = explode("\n",$sName);
$count_arr_sName = count($arr_sName);
$nub = 0;
foreach($arr_sName AS $v){
    if(!empty($v)){
        $nub++;
        $pdf->SetXY(50,$cline);
        if($nub==1)
            $buss_name=iconv('UTF-8','windows-874',$ss." ".$v);
        else
            $buss_name=iconv('UTF-8','windows-874',$v);
        $pdf->MultiCell(100,4,$buss_name,0,'L',0);

        $cline+=5;
        $i+=1;
    }
}

$cline-=5;
/*
$pdf->SetXY(50,$cline); 
$buss_name=iconv('UTF-8','windows-874',$ss." ".$sName);
$pdf->MultiCell(70,4,$buss_name,0,'L',0);
*/
$pdf->SetXY(5,$cline+1); 
$buss_name=iconv('UTF-8','windows-874',"_________________________________________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'L',0);

$cline+=5; 

$i+=1; 



$pdf->SetFont('AngsanaNew','',12);

$pdf->SetXY(5,$cline-2); 
$buss_name=iconv('UTF-8','windows-874',"_____________________________________________________________________________________________________________________________________________");
$pdf->MultiCell(200,4,$buss_name,0,'C',0);

$pdf->SetFont('AngsanaNew','B',11);


$pdf->SetXY(5,$cline+3); 
$buss_name=iconv('UTF-8','windows-874',"ทั้งหมด $gri รายการ ");
$pdf->MultiCell(50,4,$buss_name,0,'L',0);        
        


$pdf->Output();
?>