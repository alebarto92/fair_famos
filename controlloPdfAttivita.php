<?php
include("config.php");

$heightimage=300;

$hexcolor = "#043fa7";
list($r, $g, $b) = sscanf($hexcolor, "#%02x%02x%02x");


$r=4;
$g=63;
$b=167;

$ymassimo=240;

$codiceattivita=$_GET['codice_attivita'];

$debug=0;
if ($_GET['debug']=='VIACOLDEBUG') {
    $debug=1;
}

if ($codiceattivita=='') {
    exit;
}

$checkbox[1]='n';
$checkbox[0]='q';

$tbattivita=$GLOBAL_tb['attivita'];
$tbsedi=$GLOBAL_tb['sedi_clienti'];
$tbclienti=$GLOBAL_tb['clienti'];
$tbtrattamenti=$GLOBAL_tb['servizi'];
$tbfiles=$GLOBAL_tb['files'];
$tbutenti=$GLOBAL_tb['users'];
$PARAMETRI=array();
$queryparams="SELECT * FROM pcs_parametri_stampa";
$stmt=$dbh->query($queryparams);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $immagine=getFiles($row['id'],"pcs_parametri_stampa",'immagine',1);
    $row['immagine']=$immagine[0]['file'];
    $PARAMETRI[$row['nome']]=$row;
}
if ($PARAMETRI['logoGrande']['immagine']=='') {
    $PARAMETRI['logoGrande']['immagine']='intestazioneCertificatoTrattamentoDEMOCLEAN.jpg';
}
if ($PARAMETRI['logoPiccolo']['immagine']=='') {
    $PARAMETRI['logoPiccolo']['immagine']='intestazioneCertificatologopiccoloDEMOCLEAN.jpg';
}

$query="SELECT *,DATE_FORMAT(v.data_fine_attivita,'%d/%m/%Y %H:%i') as data_fine_attivita_formatted FROM $tbattivita v LEFT JOIN $tbutenti u1 ON u1.id_user=v.operatore1 LEFT JOIN $tbutenti u2 ON u2.id_user=v.operatore2 LEFT JOIN $tbutenti u3 ON u3.id_user=v.operatore3 LEFT JOIN $tbutenti u4 ON u4.id_user=v.operatore4 JOIN $tbsedi s ON v.id_sede=s.id JOIN $tbclienti c ON c.id=s.id_cliente WHERE v.id=?";
$stmt=$dbh->prepare($query);

$stmt->execute(array($codiceattivita));
$ATTIVITA=$stmt->fetch(PDO::FETCH_ASSOC);
$anno=substr($ATTIVITA['inizio1'],0,4);



$queryse="SELECT * FROM pcs_attivita_servizi_effettuati JOIN pcs_servizi ON pcs_servizi.id=pcs_attivita_servizi_effettuati.id_servizio WHERE id_attivita=? and effettuato='si'";
$stmtse=$dbh->prepare($queryse);
$stmtse->execute(array($codiceattivita));
while ($rowse=$stmtse->fetch(PDO::FETCH_ASSOC)) {
    $SERVIZIEFFETTUATI[$rowse['id_servizio']]=$rowse;
    $SERVIZIEFFETTUATI2[$codiceattivita."|".$rowse['id_servizio']]=$rowse;
}

$listaase=join("','",array_keys($SERVIZIEFFETTUATI2));

if ($listaase!='') {
    $queryfotose="SELECT * FROM pcs_foto WHERE privata='no' AND idase IN ('$listaase') order by idase,timestamp";
    $stmtfotose=$dbh->query($queryfotose);
    while ($rowfotose=$stmtfotose->fetch(PDO::FETCH_ASSOC)) {
        $fotose[$rowfotose['idase']][]=$rowfotose;
    }
}

if ($_GET['d']==1) {
    echo "<pre>";
    echo "query: $query<br/>";
    echo "ATTIVITA:<br/>";
    print_r($ATTIVITA);
    echo "PARAMETRI:<br/>";
    print_r($PARAMETRI);
    echo "SERVIZIEFFETTUATI:<br/>";
    print_r($SERVIZIEFFETTUATI);
    echo "FOTO:<br/>";
    print_r($fotose);
    echo "<pre>";
}

if ($ATTIVITA['nr_certificato']==0) {

    $query="SELECT max(nr_certificato) as nr_certificato FROM $tbattivita WHERE 1";
    $stmt=$dbh->query($query);
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    $ATTIVITA['nr_certificato']=$row['nr_certificato']+1;

    $query="UPDATE $tbattivita SET nr_certificato=? WHERE id=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($ATTIVITA['nr_certificato'],$codiceattivita));
}

//print_r($row);

$nomecliente=$ATTIVITA['nome']." ".$ATTIVITA['cognome']." - SEDE: ".$ATTIVITA['sede'];
$indirizzo=$ATTIVITA['indirizzo']." ".$ATTIVITA['CAP']." ".$ATTIVITA['citta']." (".$ATTIVITA['provincia'].")";
$contatti="Tel: ".$ATTIVITA['telefono_sede']." Email: ".$ATTIVITA['email_sede'];
$dataCertificato=$ATTIVITA['data_intervento_formatted'];
$NumCertificato=$ATTIVITA['nr_certificato'];

$hash=substr(md5($ATTIVITA['nome_o_ragione_sociale']),0,10);

$schedamonitoraggio=$codiceattivita;
$dataCertificato=$ATTIVITA['data_intervento_formatted'];

$tecnico1=getUtente($ATTIVITA['operatore1']);
$nometecnico1=$tecnico1['Nome']." ".$tecnico1['Cognome'];
$tecnico2=getUtente($ATTIVITA['operatore2']);
$nometecnico2=$tecnico2['Nome']." ".$tecnico2['Cognome'];
$tecnico3=getUtente($ATTIVITA['operatore3']);
$nometecnico3=$tecnico3['Nome']." ".$tecnico3['Cognome'];
$tecnico4=getUtente($ATTIVITA['operatore4']);
$nometecnico4=$tecnico4['Nome']." ".$tecnico4['Cognome'];

// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        //$image_file = K_PATH_IMAGES.'logo_example.jpg';
        //$this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        //$this->SetFont('helvetica', 'B', 20);
        // Title
        //$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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


if ($_GET['d']==1) {
    echo "sono qui";
}

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Studio Web 19');
$pdf->SetTitle('Studio Web 19  Certificati Clienti');
$pdf->SetSubject('Studio Web 19  Certificati Clienti');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(1, 5, 1, true);

//margin bottom a 0
$pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------


$pdf->setFontSubsetting(false);




//genero la prima pagina

$y=nuovapagina(1,0, 0);

if ($_GET['d']==1) {
    echo "<br/>sono qui 2";
}


$varx=10;

$border=0;

//ELENCO SERVIZI
$queryTrattamenti="SELECT * FROM $tbtrattamenti where id_sede=".$ATTIVITA['id_sede']." order by ordine";

if ($_GET['d']==1) {
    echo "<br/>".$queryTrattamenti;
}

$stmtTrattamenti = $dbh->query($queryTrattamenti);

while($rowTrattamenti = $stmtTrattamenti->fetch(PDO::FETCH_ASSOC)) {
    $tuttiiservizi[$rowTrattamenti['id']]=$rowTrattamenti;
}

//$y+=10;
$x=0;

$pdf->SetTextColor($r, $g, $b);
$pdf->SetFont('helvetica', 'BI', 14);
$pdf->MultiCell(190, 8, $PARAMETRI['servizi_effettuati']['valore'], $border, 'L', 1, 1, $x+$varx, $y, true);
$pdf->SetTextColor(0, 0, 0);
$y+=8;

$trattamentiscelti=array_keys($SERVIZIEFFETTUATI);

if ($_GET['d']==1) {
    echo "<br/>sono qui 2 e mezzo";
}


foreach ($tuttiiservizi as $key=>$value) {
            $border=0;
            $pdf->SetFont('zapfdingbats', '', 12);

            if (in_array($key,$trattamentiscelti)) {
                $pdf->MultiCell(6, 5, $checkbox[1], $border, 'L', 1, 1, $x+$varx, $y, true);
            } else {
                $pdf->MultiCell(6, 5, $checkbox[0], $border, 'L', 1, 1, $x+$varx, $y, true);
            }
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(190, 5, $value['nome_servizio'].' (periodicità '.$value['periodicita'].')', $border, 'L', 1, 1, $x+6+$varx, $y, true);
            $y+=10;
}

if ($_GET['d']==1) {
    echo "<br/>sono qui 3";
}

$notetotali=$ATTIVITA['note_operatore_1'].$ATTIVITA['note_operatore_2'].$ATTIVITA['note_operatore_3'].$ATTIVITA['note_operatore_4'];

//NOTE degli operatori
if ($notetotali!='') {
    $border=1;
    $y=200+$var;
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(190, 35, $notetotali, $border, 'L', 1, 1, $x+$varx, $y, true);
    $border=0;
}


$y=240+$var;
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(200, 5, $PARAMETRI['rigafooter1']['valore'], $border, 'L', 1, 1, $x+$varx, $y, true);

$y=255+$var;
$x=0;
$pdf->SetFont('helvetica', 'BI', 14);
$pdf->SetTextColor($r, $g, $b);
$pdf->MultiCell(95, 5, "OPERATORI: ", $border, 'L', 1, 1, $x+$varx, $y, true);
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0,0,0);
$pdf->MultiCell(100, 5, $nometecnico1, $border, 'L', 1, 1, $x+$varx, $y+7, true);
$pdf->MultiCell(100, 5, $nometecnico2, $border, 'L', 1, 1, $x+$varx, $y+14, true);
$pdf->MultiCell(100, 5, $nometecnico3, $border, 'L', 1, 1, $x+$varx, $y+21, true);
$pdf->MultiCell(100, 5, $nometecnico4, $border, 'L', 1, 1, $x+$varx, $y+28, true);






//FINE PRIMA PAGINA

//una pagina per ogni servizio svolto, se ci sono note oppure delle foto

foreach ($SERVIZIEFFETTUATI as $idservizio => $value) :
    $idase=$codiceattivita."|".$idservizio;
    if ($value['note']=='' and count($fotose[$idase])==0) {
        continue;
    }
    //allora faccio una nuova pagina per ogni servizio
    $y=nuovapagina();
    $x=0;
    $varx=10;
    $pdf->SetFont('helvetica', 'BI', 14);
    $pdf->SetTextColor($r, $g, $b);
    $pdf->MultiCell(205, 5, $SERVIZIEFFETTUATI[$idservizio]['nome_servizio'], $border, 'L', 1, 1, $x+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(0,0,0);
    if ($SERVIZIEFFETTUATI[$idservizio]['descrizione_servizio']!='') {
        $y+=8;
        $pdf->writeHTMLCell(190,12,$x+$varx,$y,$SERVIZIEFFETTUATI[$idservizio]['descrizione_servizio'], 1, 0, 0, true, '',true);
    }

    $y=$pdf->GetY()+10;

    //ora devo mettere le note
    if ($SERVIZIEFFETTUATI[$idservizio]['note']!='') {
        $y+=6;
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->MultiCell(95, 5, "NOTE" , $border, 'L', 1, 1, $x+$varx, $y, true);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(0,0,0);
        $y+=6;
        $pdf->writeHTMLCell(190,12,$x+$varx,$y,$SERVIZIEFFETTUATI[$idservizio]['note'], 0, 0, 0, true, '',true);
    }

    $y=$pdf->GetY()+20;
    //segnaposto.... altrimenti l'immagine non scorre in basso
    $pdf->writeHTMLCell(190,12,$x+$varx,$y,' ', 0, 0, 0, true, '',true);
    $y=$pdf->GetY()+20;

    //ora metto le foto se ci sono
    if (count($fotose[$idase])>0) {
        $val=$fotose[$idase];
        foreach ($val as $index=>$foto):

            $html='
            <div>
  <img style="vertical-align:middle; height:'.$heightimage.';" src="'.$foto['foto_absoluteurl'].'">
  <br/>&nbsp;&nbsp;&nbsp;'.$foto["note"].'<br/><br/>
</div>
';
            $pdf->SetMargins(10, 10, 10, false);
            $pdf->writeHTML($html, true, 0, true, 0, '');
            $y=$pdf->GetY()+10;
            if ($y>$ymassimo) {
                $y=nuovapagina();
            }
        endforeach;

    }

endforeach;


if ($debug) {
//Close and output PDF document

$pdf->Output('CertificatoA-'.$hash.'-'.$NumCertificato.'.pdf', 'I');

exit;
} else {
    if ($_GET['invia']==1) {
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$directoryfiles.'/Certificati'.DIRECTORY_SEPARATOR.'CertificatoA-'.$hash.'-'.$NumCertificato.'.pdf', 'F');
        $urlfile="https://".$_SERVER['HTTP_HOST'].$directoryfiles."/Certificati/CertificatoA-".$hash.'-'.$NumCertificato.".pdf";
        $query="UPDATE $tbattivita SET filepdf=? WHERE id=?";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($urlfile,$codiceattivita));
        setNotificheCRUD("APP","SUCCESS","controlloPdf.php","File inserito nel db: $urlfile");
        $attachment=$pdf->Output('CertificatoA-'.$hash.'-'.$NumCertificato.'.pdf', 'S');
        $nomeattachment='CertificatoA-'.$hash.'-'.$NumCertificato.'.pdf';

    } else {
        $pdf->Output('CertificatoA-'.$hash.'-'.$NumCertificato.'.pdf', 'I');
        //lo salva pure
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$directoryfiles.'/Certificati'.DIRECTORY_SEPARATOR.'CertificatoA-'.$hash.'-'.$NumCertificato.'.pdf', 'F');
        exit;
    }
}

/* --------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------- (i) EMAIL ------------------------------------------------------------------ */
/* --------------------------------------------------------------------------------------------------------------------------------------------------- */

//spediamo sempre e comunque la email! anche se già spedita in precedenza!

//if ($VISITA['pdf_inviato']=='si') {
if (0) { ?>
    <div class="registrazionesuccess alert alert-success" role="alert">
        <div class="center">
            <?php echo _("<strong>Complimenti!</strong> Pdf generato con successo!");?><br/>
            <script>
                setTimeout(function(){
                    var url='https://<?php echo $_SERVER[HTTP_HOST].$sitedir;?>module.php?modname=Attivita';
                    window.location.href = url;
                }, 2000);
            </script>
        </div>
    </div>

<?php } else {
    require 'class.phpmailer.php';


    $EMAILADMIN['email']="info@studioweb19.it";
    $EMAILADMIN['name']=$projecttitle;

    $testo="Buongiorno, inviamo in allegato il certificato relativo all'intervento effettuato presso la vostra sede in data ".$ATTIVITA['data_fine_attivita_formatted'];
    $testo.="<br/><br/>Cordiali saluti<br/><br/>$projecttitle";

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

    $mail->addAddress($VISITA['email_cliente']);            // Name is optional
    $mail->addAddress($VISITA['email_sede']);               // Name is optional
    $mail->AddBCC($EMAILADMINBCC);              // Name is optional
    $mail->SetFrom('info@studioweb19.it', $projecttitle);
    //$mail->SetFrom('amministrazione@cdpbranca.it', 'CDP Branca - Pest Control System');

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $projecttitle .' - Intervento del '.$VISITA['data_intervento_formatted'];
    $mail->Body    = $testo;

    $mail->AddStringAttachment($attachment, $nomeattachment);

    if ($mail->send()) {
        $query="UPDATE pcs_visite SET email_inviata=1 WHERE codice_visita=?";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($codicevisita));
        setNotificheCRUD("APP","SUCCESS","controlloPdf.php","Mail inviata: $codicevisita");
    ?>
        <div class="registrazionesuccess alert alert-success" role="alert">
            <div class="center">
                <?php echo _("<strong>Complimenti!</strong> Pdf generato e inviato con successo!");?><br/>
                <script>
                    setTimeout(function(){
                        var url='https://<?php echo $_SERVER[HTTP_HOST].$sitedir;?>module.php?modname=Attivita';
                        window.location.href = url;
                    }, 2000);
                </script>
            </div>
        </div>

    <?php } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        setNotificheCRUD("APP","ERROR","controlloPdfAttivita.php","Mail non inviata: $codicevisita");
    }
}


/* --------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------- (f) EMAIL ------------------------------------------------------------------ */
/* --------------------------------------------------------------------------------------------------------------------------------------------------- */


function nuovapagina($conintestazione=1,$conservizio=1,$logopiccolo=1) {
    global $ATTIVITA;
    global $PARAMETRI;
    global $tmpserv;
    global $titoloscheda;
    global $pdf;
    global $border,$y,$var,$varx,$vary,$r,$g,$b;
    $pdf->SetFont('times', '', 14);
    $pdf->AddPage('P', 'A4');
    $pdf->setJPEGQuality(75);
    $pdf->setCellPaddings(1, 1, 1, 1);
    $pdf->setCellMargins(1, 1, 1, 1);
    $pdf->SetFillColor(255, 255, 255);
    $var=-13;
    $varx=-5;




    if ($logopiccolo==0) {
        $pdf->MultiCell(191, 35, "", 1, 'L', 1, 1, 11 + $varx, $y, true);
        $pdf->Image($PARAMETRI['logoGrande']['immagine'], 10, 20 + $var, 87, 31, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->MultiCell(70, 7, $PARAMETRI['riga1']['valore'], 0, 'R', 1, 1, 12 + $varx + 110, $y + 7, true);
        $pdf->MultiCell(70, 7, $PARAMETRI['riga2']['valore'], 0, 'R', 1, 1, 12 + $varx + 110, $y + 13, true);
        $pdf->MultiCell(70, 7, $PARAMETRI['riga3']['valore'], 0, 'R', 1, 1, 12 + $varx + 110, $y + 19, true);
        $pdf->MultiCell(70, 7, $PARAMETRI['riga4']['valore'], 0, 'R', 1, 1, 12 + $varx + 110, $y + 25, true);
        $pdf->MultiCell(70, 7, $PARAMETRI['riga5']['valore'], 0, 'R', 1, 1, 12 + $varx + 110, $y + 32, true);
        $y=35+$vary;
        $pdf->SetTextColor($r, $g, $b);
        $pdf->SetFont('helvetica', 'B', 12);
        $border=0;
        $pdf->MultiCell(130, 7, "ORDINE DI SERVIZIO NUM ".$ATTIVITA['nr_certificato'].' del '.$ATTIVITA['data_fine_attivita_formatted'], $border, 'R', 1, 1, 12+$varx+60, $y+7, true);

        $border=0;
        $varx=0;
        $vary=10;

        $y+=15;
        $pdf->SetTextColor(0,0,0);

        //$pdf->SetTextColor($r, $g, $b);

        //Riga Cliente
        $pdf->MultiCell(280, 8, "Cliente: ".$ATTIVITA['Nome']." ".$ATTIVITA['Cognome'], $border, 'L', 1, 1, 5+$varx, $y, true);
        $y+=6;

        if ($ATTIVITA['provincia']!='') {
            $provincia="(".$ATTIVITA['provincia'].")";
        } else {
            $provincia='';
        }
        if ($ATTIVITA['CAP']!='') {
            $CAP="CAP ".$ATTIVITA['CAP'];
        } else {
            $CAP='';
        }

        //Riga Sede
        $pdf->MultiCell(280, 8, "Sede: ".$ATTIVITA['sede'], $border, 'L', 1, 1, 5+$varx, $y, true);
        $y+=6;

        //Riga Indirizzo
        $pdf->MultiCell(280, 8, "Indirizzo: ".$ATTIVITA['indirizzo'].', '.$CAP.' '.$ATTIVITA['citta'].' '.$provincia, $border, 'L', 1, 1, 5+$varx, $y, true);
        $y+=6;

        $border=0;

        $varx=0;
        $pdf->SetTextColor(0, 0, 0);
        $y+=6;
    } else {

        $pdf->Image($PARAMETRI['logoPiccolo']['immagine'], 10, 20 + $var, 20, 20, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);
        $y=10+$vary;
        $pdf->SetTextColor($r, $g, $b);
        $pdf->SetFont('helvetica', 'B', 12);
        $border=0;
        $pdf->MultiCell(130, 7, "ORDINE DI SERVIZIO NUM ".$ATTIVITA['nr_certificato'].' del '.$ATTIVITA['data_fine_attivita_formatted'], $border, 'R', 1, 1, 12+$varx+60, $y+7, true);

        $border=0;
        $varx=30;
        $vary=10;

        $y-=15;
        $pdf->SetTextColor(0,0,0);

        //Riga Cliente
        $pdf->MultiCell(280, 8, "Cliente: ".$ATTIVITA['Nome']." ".$ATTIVITA['Cognome'], $border, 'L', 1, 1, 5+$varx, $y, true);
        $y+=6;

        if ($ATTIVITA['provincia']!='') {
            $provincia="(".$ATTIVITA['provincia'].")";
        } else {
            $provincia='';
        }
        if ($ATTIVITA['CAP']!='') {
            $CAP="CAP ".$ATTIVITA['CAP'];
        } else {
            $CAP='';
        }

        //Riga Sede
        $pdf->MultiCell(280, 8, "Sede: ".$ATTIVITA['sede'], $border, 'L', 1, 1, 5+$varx, $y, true);
        $y+=6;

        //Riga Indirizzo
        $pdf->MultiCell(280, 8, "Indirizzo: ".$ATTIVITA['indirizzo'].', '.$CAP.' '.$ATTIVITA['citta'].' '.$provincia, $border, 'L', 1, 1, 5+$varx, $y, true);
        $y+=6;

        $border=0;

        $varx=0;
        $pdf->SetTextColor(0, 0, 0);
        $y+=12;
    }
    return $y;
}


//============================================================+
// END OF FILE
//============================================================+
