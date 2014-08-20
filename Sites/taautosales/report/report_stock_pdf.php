<?php
set_time_limit(0);
ini_set('memory_limit', '512M');
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$mode = $_REQUEST['mode'];
$type = $_REQUEST['type'];

if( empty($mode) OR $mode == "" ){
    echo "invalid param.";
    exit;
}

if($mode == "full"){
    
    if( empty($type) OR $type == "" ){
        echo "invalid param.";
        exit;
    }
    
    if($type == "1"){
        $save_data = "<div style=\"font-size:large\"><b>รายงานสรุป สินค้าคงเหลือ</b></div>";

        $save_data .= '
        <table cellpadding="3" cellspacing="0" border="1" width="100%">
        <tr style="font-weight:bold" align="center" bgcolor="#CFCFCF">
            <td width="80">ลำดับ</td>
            <td width="90">ID</td>
            <td width="400">Name</td>
            <td width="100">ยอดคงเหลือ</td>
        </tr>
        ';

        $j = 0;
        $qry = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY product_id ASC");
        while($res = pg_fetch_array($qry)){
            $material_id = $res['product_id'];
            $name = $res['name'];
            $balance = GetAmountRawMaterial($material_id);

            //if($balance == 0) continue;

            $j++;

            $save_data .= '
            <tr>
                <td align="center">'.$j.'</td>
                <td align="center">'.$material_id.'</td>
                <td>'.$name.'</td>
                <td align="right">'.number_format($balance,0).'</td>
            </tr>';
        }

        $save_data .= '</table>';
    }elseif($type == "2"){
        $save_data = "<div style=\"font-size:large\"><b>รายงานสรุป สินค้าคงเหลือ (แยกรายละเอียด)</b></div>";

        $save_data .= '
        <table cellpadding="3" cellspacing="0" border="1" width="100%">
        <tr style="font-weight:bold" align="center" bgcolor="#CFCFCF">
            <td width="80">ลำดับ</td>
            <td width="90">ID</td>
            <td width="400">Name</td>
            <td width="100">ยอดคงเหลือ</td>
        </tr>
        ';

        $j = 0;
        $qry = pg_query("SELECT * FROM \"RawMaterialProduct\" ORDER BY product_id ASC");
        while($res = pg_fetch_array($qry)){
            $material_id = $res['product_id'];
            $name = $res['name'];
            $balance = GetAmountRawMaterial($material_id);

            //if($balance == 0) continue;

            $j++;
            
            $save_data .= '
            <tr>
                <td align="center">'.$j.'</td>
                <td align="center">'.$material_id.'</td>
                <td>'.$name.'</td>
                <td align="right">'.number_format($balance,0).'</td>
            </tr>';
            $save_data .= '<tr><td colspan="4"><table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-size:small">
        <tr style="font-weight:bold">
            <td width="100">วันที่</td>
            <td width="100">รหัสอ้างอิง</td>
            <td width="100">ประเภท</td>
            <td width="100">จำนวน</td>
            <td width="100">คงเหลือ</td>
        </tr>';
            
            $sum=0;
            $qry_dt = pg_query("SELECT * FROM \"StockMovement\" WHERE product_id='$material_id' ORDER BY auto_id ASC");
            while( $res_dt = pg_fetch_array($qry_dt) ){
                $amount = $res_dt['amount'];
                $type_inout = $res_dt['type_inout'];
                $date_inout = $res_dt['date_inout'];
                $ref_1 = $res_dt['ref_1'];

                if($type_inout == "I"){
                    $str_inout = "เข้า";
                    $sum += $amount;
                }else{
                    $str_inout = "ออก";
                    $sum += ($amount);
                }
                
                $save_data .= '
                <tr>
                    <td>'.$date_inout.'</td>
                    <td>'.$ref_1.'</td>
                    <td>'.$str_inout.'</td>
                    <td>'.number_format($amount,0).'</td>
                    <td>'.number_format($sum,0).'</td>
                </tr>';
            }
            $save_data .= '</table></td></tr>';
        }

        $save_data .= '</table>';
    }
}

elseif($mode == "detail"){
    $product_id = $_GET['id'];
    if( empty($product_id) OR $product_id == "" ){
        echo "invalid param.";
        exit;
    }
    
    if(substr($product_id, 0,1) == "P" )
        $product_name = GetProductName($product_id);
    else
        $product_name = GetRawMaterialName($product_id);
    
    $save_data .= '<div style="font-weight:bold">รหัส '.$product_id.' : '.$product_name.'</div>';
    $save_data .= '<table cellpadding="3" cellspacing="0" border="1" width="100%">
        <tr style="font-weight:bold" align="center" bgcolor="#CFCFCF">
        <td width="150">วันที่</td>
        <td width="150">รหัสอ้างอิง</td>
        <td width="150">ประเภท</td>
        <td width="110">จำนวน</td>
        <td width="110">คงเหลือ</td>
    </tr>';
    $qry_dt = pg_query("SELECT * FROM \"StockMovement\" WHERE product_id='$product_id' ORDER BY auto_id ASC");
    while( $res_dt = pg_fetch_array($qry_dt) ){
        $amount = $res_dt['amount'];
        $type_inout = $res_dt['type_inout'];
        $date_inout = $res_dt['date_inout'];
        $ref_1 = $res_dt['ref_1'];

        if($type_inout == "I"){
            $str_inout = "เข้า";
            $sum += $amount;
        }else{
            $str_inout = "ออก";
            $sum += ($amount);
        }

        $save_data .= '
        <tr>
            <td align="center">'.$date_inout.'</td>
            <td>'.$ref_1.'</td>
            <td align="center">'.$str_inout.'</td>
            <td align="right">'.number_format($amount,0).'</td>
            <td align="right">'.number_format($sum,0).'</td>
        </tr>';
    }
    $save_data .= '</table>';
}


//START PDF
include_once('../tcpdf/config/lang/eng.php');
include_once('../tcpdf/tcpdf.php');

//CUSTOM HEADER and FOOTER
class MYPDF extends TCPDF {
    public function Header(){
        //$this->Image('../images/logo.jpg', 10, 5, '', '', '', '', '');
        $this->SetFont('AngsanaUPC', '', 14);
        $this->MultiCell(190, 0, "วันที่พิมพ์ ".date('d-m-Y'), 0, 'R', 0, 0, 10, '', true);
    }

    public function Footer(){
        
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);

//set margins
$pdf->SetMargins(10, 15, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setJPEGQuality(100);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15);

// set font
$pdf->SetFont('AngsanaUPC', '', 14); //AngsanaUPC  CordiaUPC

$pdf->AddPage();

$pdf->writeHTML($save_data, true, false, true, false, '');

$pdf->Output('stock_list.pdf', 'I');
?>