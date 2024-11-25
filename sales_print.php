<?php
require_once('../tcpdf/tcpdf.php');

class SalesReportPDF extends TCPDF {
    // Page header
    public function Header() {
        // Set font
        $this->SetFont('helvetica', 'B', 12);
        // Title
        $this->Cell(0, 15, 'TechSoft IT Solutions', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(10); // Line break
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 15, 'Sales Report', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Function to generate table rows
function generateRow($from, $to, $conn) {
    $contents = '';

    $stmt_sales = $conn->prepare("SELECT *, sales.id AS salesid FROM sales LEFT JOIN users ON users.id=sales.user_id WHERE sales_date BETWEEN :from AND :to ORDER BY sales_date DESC");
    $stmt_sales->execute(['from'=>$from, 'to'=>$to]);
    $total = 0;
    foreach($stmt_sales as $row) {
        $stmt_details = $conn->prepare("SELECT * FROM details LEFT JOIN products ON products.id=details.product_id WHERE sales_id=:id");
        $stmt_details->execute(['id'=>$row['salesid']]);
        $amount = 0;
        foreach($stmt_details as $details) {
            $subtotal = $details['price'] * $details['quantity'];
            $amount += $subtotal;
        }
        $total += $amount;
        $contents .= '
        <tr>
            <td>'.date('M d, Y', strtotime($row['sales_date'])).'</td>
            <td>'.$row['firstname'].' '.$row['lastname'].'</td>
            <td>'.$row['pay_id'].'</td>
            <td align="right">&#36; '.number_format($amount, 2).'</td>
        </tr>
        ';
    }

    $contents .= '
        <tr>
            <td colspan="3" align="right"><b>Total</b></td>
            <td align="right"><b>&#36; '.number_format($total, 2).'</b></td>
        </tr>
    ';
    return $contents;
}

// Check if the form is submitted
if(isset($_POST['print'])) {
    if(empty($_POST['date_range'])) {
        $_SESSION['error'] = 'Need date range to provide sales print';
        header('location: sales.php');
        exit();
    }

    $ex = explode(' - ', $_POST['date_range']);
    $from = date('Y-m-d', strtotime($ex[0]));
    $to = date('Y-m-d', strtotime($ex[1]));
    $from_title = date('M d, Y', strtotime($ex[0]));
    $to_title = date('M d, Y', strtotime($ex[1]));

    $conn = $pdo->open();

    try {
        $pdf = new SalesReportPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
        $pdf->SetCreator(PDF_CREATOR);  
        $pdf->SetTitle('Sales Report: '.$from_title.' - '.$to_title);  
        $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
        $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);  
        $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);  
        $pdf->SetDefaultMonospacedFont('helvetica');  
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
        $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
        $pdf->setPrintHeader(true);  
        $pdf->setPrintFooter(true);  
        $pdf->SetAutoPageBreak(TRUE, 10);  
        $pdf->SetFont('helvetica', '', 11);  
        $pdf->AddPage();  
        $content = '';  
        $content .= '
            <h2 align="center">TechSoft IT Solutions</h2>
            <h4 align="center">SALES REPORT</h4>
            <h4 align="center">'.$from_title." - ".$to_title.'</h4>
            <table border="1" cellspacing="0" cellpadding="3">  
                <tr>  
                    <th width="15%" align="center"><b>Date</b></th>
                    <th width="30%" align="center"><b>Buyer Name</b></th>
                    <th width="40%" align="center"><b>Transaction#</b></th>
                    <th width="15%" align="center"><b>Amount</b></th>  
                </tr>  
        ';  
        $content .= generateRow($from, $to, $conn);  
        $content .= '</table>';  
        $pdf->writeHTML($content);  
        $pdf->Output('sales_report_'.$from_title.'_'.$to_title.'.pdf', 'I');
    } catch (Exception $e) {
        $_SESSION['error'] = 'PDF generation error: ' . $e->getMessage();
        header('location: sales.php');
        exit();
    }

    $pdo->close();

} else {
    $_SESSION['error'] = 'Need date range to provide sales print';
    header('location: sales.php');
    exit();
}
?>
