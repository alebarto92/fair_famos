<?php
//============================================================+
// File name   : example_002.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 002 for TCPDF class
//               Removing Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Removing Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

$idreport=$_GET['idreport'];
include("config.php");

$tbreports =$GLOBAL_tb['reports'];
$tbsedi=$GLOBAL_tb['sedi_clienti'];
$tbclienti=$GLOBAL_tb['clienti'];

$query1="SELECT s.CAP as capsede,s.*,c.*,r.*,DATE_FORMAT(data_report,'%d/%m/%Y') as data_report_formatted,DATE_FORMAT(data_inizio,'%d/%m/%Y') as data_inizio_formatted,DATE_FORMAT(data_fine,'%d/%m/%Y') as data_fine_formatted,r.id as idreport,s.email as email_sede, s.persona_di_riferimento as persona_di_riferimento_sede,s.telefono as telefono_sede ,c.Mail as email_cliente, c.Referenti as persona_di_riferimento_cliente 
FROM $tbreports r JOIN $tbsedi s ON r.id_sede=s.id JOIN $tbclienti c ON c.id=s.id_cliente WHERE r.id=?";

$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($idreport));
if ($REPORT=$stmt1->fetch(PDO::FETCH_ASSOC)) {
    $REPORT['CAP']=$REPORT['capsede'];
} else {
    echo "Spiacente, nessun report disponibile!";
    exit;
}

require_once('tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo_example.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('times', 'BI', 20);

$immagini=json_decode($REPORT['immagini'],true);

foreach ($immagini as $immagine) :
    nuovapagina();
    $y+=10;
    $pdf->Image($immagine, 10, $y, 285, '', 'JPG', '', '', false, 300);

endforeach;

// ---------------------------------------------------------

//Close and output PDF document
$dir2=md5($idreport);

$pdf->Output($_SERVER['DOCUMENT_ROOT'].$directoryfiles.'/Reports/'.$dir2.'-'.$idreport.'/Report-'.$idreport.'.pdf', 'F');
$urlfile='https://'.$_SERVER['HTTP_HOST'].$directoryfiles.'/Reports/'.$dir2.'-'.$idreport.'/Report-'.$idreport.'.pdf';
$query="UPDATE $tbreports SET filepdf=? WHERE id=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($urlfile,$idreport));
setNotificheCRUD("APP","SUCCESS","inviaPdfReport.php","File inserito nel db: $urlfile");
$attachment=$pdf->Output('Report-'.$idreport.'.pdf', 'S');
$nomeattachment='Report-'.$idreport.'.pdf';


require 'class.phpmailer.php';


$testo="Buongiorno, inviamo in allegato il report relativo al periodo ".$REPORT['data_inizio_formatted']." ".$REPORT['data_fine_formatted'];
$testo.="<br/><br/>Cordiali saluti<br/><br/>$project_title";

$mail = new PHPMailer;

$mail->SMTPDebug = 0;
$mail->IsSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp-relay.sendinblue.com';                 // Specify main and backup server
$mail->Port = 587;                                    // Set the SMTP port
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'info@studioweb19.it';                  // SMTP username
$mail->Password = 'tBMDcv8Enj4YsGp7';           // SMTP password studioweb19
$mail->SMTPSecure = 'SSL';                            // Enable encryption, 'ssl' also accepted

$mail->AddReplyTo($EMAILADMIN['email'], $EMAILADMIN['name']);

$mail->addAddress($REPORT['email_cliente']);            // Name is optional
$mail->addAddress($REPORT['email_sede']);               // Name is optional
$mail->AddBCC($EMAILADMINBCC);              // Name is optional
$mail->SetFrom('info@studioweb19.it', $project_title);

$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $project_title.' - Report del '.$REPORT['data_inizio_formatted'].' - '.$REPORT['data_fine_formatted'];
$mail->Body    = $testo;

$mail->AddStringAttachment($attachment, $nomeattachment);


if ($mail->send()) {
    $query="UPDATE $tbreports SET pdf_inviato='si' WHERE id=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($idreport));
    setNotificheCRUD("APP","SUCCESS","inviaPdfReport.php","Mail inviata: $idreport");
    ?>
    <div class="registrazionesuccess alert alert-success" role="alert">
        <div class="center">
            <?php echo _("<strong>Complimenti!</strong> Pdf generato e inviato con successo!");?><br/>
            <script>
                setTimeout(function(){
                    var url='https://<?php echo $_SERVER[HTTP_HOST].$sitedir;?>module.php?modname=Report';
                    window.location.href = url;
                }, 2000);
            </script>
        </div>
    </div>

<?php } else {
    setNotificheCRUD("APP","ERROR","inviaPdfReport.php","Mail non inviata: $idreport");
}

//============================================================+
// END OF FILE
//============================================================+


function nuovapagina($conintestazione=1) {
    global $pdf;
    global $REPORT;
    global $border,$y,$var,$varx,$vary;
    global $idreport;
    $pdf->SetFont('times', '', 14);
    $pdf->AddPage('L', 'A4');
    $pdf->setJPEGQuality(75);
    $pdf->setCellPaddings(1, 1, 1, 1);
    $pdf->setCellMargins(1, 1, 1, 1);
    $pdf->SetFillColor(255, 255, 255);
    $var=-13;
    $varx=-5;
    $pdf->Image('intestazioneCertificato2.jpg', 10, 20+$var, 285, 40, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);

    $pdf->SetFont('helvetica', 'B', 12);
    $border=0;
    $varx=0;
    $vary=10;

    $y=48+$vary;

    //Riga Cliente
    $pdf->MultiCell(280, 8, "Cliente: ".$REPORT['Cliente'], $border, 'L', 1, 1, 15+$varx, $y, true);
    $y+=6;

    if ($REPORT['provincia']!='') {
        $provincia="(".$REPORT['provincia'].")";
    } else {
        $provincia='';
    }
    if ($REPORT['CAP']!='') {
        $CAP="CAP ".$REPORT['CAP'];
    } else {
        $CAP='';
    }

    //Riga Sede
    $pdf->MultiCell(280, 8, "Sede: ".$REPORT['sede'].' '.$REPORT['indirizzo'].', '.$CAP.' '.$REPORT['citta'].' '.$provincia, $border, 'L', 1, 1, 15+$varx, $y, true);
    $y+=6;

    $pdf->SetFont('helvetica', 'B', 14);
    $border=0;

    $pdf->SetFont('helvetica', 'B', 12);
    $border=0;
    $vary=10;

    $y=60+$vary;

    //Riga Report
    $border=0;

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(22, 6, "Periodo dal ", $border, 'L', 1, 1, 15+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(20, 6, $REPORT['data_inizio_formatted'], $border, 'L', 1, 1, 37+$varx, $y, true);

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(6, 6, "al ", $border, 'L', 1, 1, 57+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(20, 6, $REPORT['data_fine_formatted'], $border, 'L', 1, 1, 63+$varx, $y, true);

    $y+=8;

    $varx=190;

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(20, 6, "Report Nr", $border, 'L', 1, 1, -8+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(10, 6, $idreport, $border, 'L', 1, 1, 11+$varx, $y, true);

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(12, 6, "Data: ", $border, 'L', 1, 1, 21+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(20, 6, $REPORT['data_report_formatted'], $border, 'L', 1, 1, 33+$varx, $y, true);

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(10, 6, "Ora: ", $border, 'L', 1, 1, 53+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(16, 6, $REPORT['ora_report'], $border, 'L', 1, 1, 63+$varx, $y, true);

    $border=1;

}
