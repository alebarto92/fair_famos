<?php
include("config.php");

$ymassimo=190;

$codicevisita=$_GET['codice_visita'];

$debug=0;
if ($_GET['debug']=='VIACOLDEBUG') {
    $debug=1;
}

if ($codicevisita=='') {
    exit;
}

$checkbox[1]='n';
$checkbox[0]='q';

$tbvisite=$GLOBAL_tb['visite'];
$tbpostazioni=$GLOBAL_tb['postazioni'];
$tbaree=$GLOBAL_tb['aree'];
$tbsedi=$GLOBAL_tb['sedi_clienti'];
$tbclienti=$GLOBAL_tb['clienti'];
$tbtipiservizio=$GLOBAL_tb['tipiservizio'];
$tbfiles=$GLOBAL_tb['files'];
$tbutenti=$GLOBAL_tb['users'];

$query="SELECT *,DATE_FORMAT(v.data_fine_visita,'%d/%m/%Y') as data_intervento_formatted,DATE_FORMAT(v.data_inizio_visita,'%d/%m/%Y') as data_inizio_visita_formatted,s.email as email_sede, 
s.persona_di_riferimento as persona_di_riferimento_sede,s.telefono as telefono_sede ,c.Mail as email_cliente, 
c.Referenti as persona_di_riferimento_cliente FROM $tbvisite v JOIN $tbutenti u ON u.id_user=v.id_dipendente JOIN $tbsedi s ON v.id_sede=s.id JOIN $tbclienti c ON c.id=s.id_cliente WHERE codice_visita=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($codicevisita));
$VISITA=$stmt->fetch(PDO::FETCH_ASSOC);
$anno=substr($VISITA['data_intervento'],0,4);

if ($_GET['d']==1) {
    //echo "<pre>";
    //print_r($VISITA);
    //echo "<pre>";
}

$query2="SELECT * FROM $tbfiles f WHERE f.id_elem=? AND f.tipo_file='immagine' AND f.tb='".$tbutenti."' order by ordine";
$stmt2=$dbh->prepare($query2);
$stmt2->execute(array($VISITA['id_dipendente']));
$IMMAGINIOPERATORE=array();
while ($row=$stmt2->fetch(PDO::FETCH_ASSOC)) {
    $IMMAGINIOPERATORE[]=$row;
}

if ($VISITA['nr_certificato']==0) {

    $query="SELECT max(nr_certificato) as nr_certificato FROM $tbvisite WHERE 1";
    $stmt=$dbh->query($query);
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    $VISITA['nr_certificato']=$row['nr_certificato']+1;

    $query="UPDATE $tbvisite SET nr_certificato=? WHERE codice_visita=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($VISITA['nr_certificato'],$codicevisita));
}

//print_r($row);

$nomecliente=$VISITA['nome']." ".$VISITA['cognome']." - SEDE: ".$VISITA['sede'];
$indirizzo=$VISITA['indirizzo']." ".$VISITA['CAP']." ".$VISITA['citta']." (".$VISITA['provincia'].")";
$contatti="Tel: ".$VISITA['telefono_sede']." Email: ".$VISITA['email_sede'];
$dataCertificato=$VISITA['data_intervento_formatted'];
$NumCertificato=$VISITA['nr_certificato'];

$hash=substr(md5($VISITA['nome_o_ragione_sociale']),0,10);

$schedamonitoraggio=$codicevisita;
$dataCertificato=$VISITA['data_intervento_formatted'];

$tecnico=getUtente($VISITA['operatore']);
$nometecnico=$tecnico['Nome']." ".$tecnico['Cognome'];

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


//ora vediamo quali schede vanno preparate in base alle aree e ai servizi interessati
$query="SELECT *,pcs1.nome_infestante as nome_infestante1,pcs2.nome_infestante as nome_infestante2,pcs3.nome_infestante as nome_infestante3,pcs4.nome_infestante as nome_infestante4 
FROM `pcs_ispezioni` 
join pcs_visite on pcs_visite.codice_visita=pcs_ispezioni.codice_visita 
join pcs_sedi_clienti on pcs_sedi_clienti.id=pcs_visite.id_sede 
join pcs_postazioni on pcs_postazioni.codice_postazione=pcs_ispezioni.codice_postazione 
LEFT join pcs_tipo_postazione on pcs_tipo_postazione.id_tipo_postazione=pcs_ispezioni.tipo 
LEFT join pcs_modello_postazione on pcs_modello_postazione.id_modello_postazione=pcs_ispezioni.modello 
LEFT JOIN pcs_prodotto_postazione on pcs_prodotto_postazione.id_prodotto_postazione=pcs_ispezioni.prodotto 
join pcs_aree on pcs_aree.id_sede=pcs_sedi_clienti.id and pcs_aree.id=pcs_postazioni.id_area 
join pcs_tipi_servizio ON pcs_tipi_servizio.id=pcs_aree.Servizio 
LEFT JOIN pcs_infestanti pcs1 ON pcs1.id_infestante=pcs_aree.Infestante1
LEFT JOIN pcs_infestanti pcs2 ON pcs2.id_infestante=pcs_aree.Infestante2
LEFT JOIN pcs_infestanti pcs3 ON pcs3.id_infestante=pcs_aree.Infestante3
LEFT JOIN pcs_infestanti pcs4 ON pcs4.id_infestante=pcs_aree.Infestante4
WHERE pcs_ispezioni.codice_visita=? order by pcs_aree.id,pcs_aree.Servizio,CAST(nome as SIGNED INTEGER),nome ";

$stmt=$dbh->prepare($query);
$stmt->execute(array($codicevisita));
$SERVIZI=array();

$titoloscheda['ID']="SCHEDA MONITORAGGIO/RILEVAMENTO INSETTI DERRATE ALIMENTARI ".$anno;
$titoloscheda['D'] ="SCHEDA MONITORAGGIO/CONTROLLO DERATTIZZAZIONE ".$anno;
$titoloscheda['DA']="SCHEDA MONITORAGGIO/CONTROLLO DERATTIZZAZIONE ATOSSICA ".$anno;
$titoloscheda['IV']="SCHEDA MONITORAGGIO/RILEVAMENTO INSETTI VOLANTI INTERNO".$anno;
$titoloscheda['IVE']="SCHEDA MONITORAGGIO/RILEVAMENTO INSETTI VOLANTI ESTERNO ".$anno;
$titoloscheda['IS']="SCHEDA MONITORAGGIO/RILEVAMENTO INSETTI STRISCIANTI ".$anno;
$titoloscheda['RS']="SCHEDA MONITORAGGIO RODITORI E INSETTI STRISCIANTI ".$anno;

$campoconteggioinsetti['ID']='conteggio_insetti_derrate';
$campoconteggioinsetti['IS']='conteggio_striscianti';
$campoconteggioinsetti['IV']='conteggio_volanti';
$campoconteggioinsetti['DA'] ='conteggio_roditori';

$nomeinsettogenerico['ID']='Insetti Derrate';
$nomeinsettogenerico['IS']='Blatte';
$nomeinsettogenerico['IV']='Insetti Volanti';
$nomeinsettogenerico['IVE']='Insetti Volanti';
$nomeinsettogenerico['DA'] ='Roditori';
$nomeinsettogenerico['RS'] ='Roditori e Striscianti';

$sogliaOrdine['Integra (0%)']=1;
$sogliaOrdine['Pochissimo consumata (10%)']=2;
$sogliaOrdine['Lievemente consumata (25%)']=3;
$sogliaOrdine['Mediamente consumata (50%)']=4;
$sogliaOrdine['Molto consumata (75%)']=5;
$sogliaOrdine['Totalmente consumata (100%)']=6;

$sogliaOrdineVolanti['nessuna']=1;
$sogliaOrdineVolanti['bassa']=2;
$sogliaOrdineVolanti['media']=3;
$sogliaOrdineVolanti['alta']=4;

$testo['Integra (0%)']='Integra 0%';
$testo['Pochissimo consumata (10%)']='Poco 10%';
$testo['Lievemente consumata (25%)']='Lieve 25%';
$testo['Mediamente consumata (50%)']='Medio 50%';
$testo['Molto consumata (75%)']='Molto 75%';
$testo['Totalmente consumata (100%)']='Totale 100%';

$sogliaPercentuale['1']="0%";
$sogliaPercentuale['2']="10%";
$sogliaPercentuale['3']="25%";
$sogliaPercentuale['4']="50%";
$sogliaPercentuale['5']="75%";
$sogliaPercentuale['6']="100%";

if ($VISITA['aree_selezionate_tmp']!='') {
    $AREESELEZIONATE=explode(",",$VISITA['aree_selezionate_tmp']);
}

while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {

    if ($VISITA['aree_selezionate_tmp']!='') {

        if (!(in_array($row['id_area'],$AREESELEZIONATE))) {
            continue;
        }
    }
    $SERVIZI[$row['descrizione_servizio']][$row['id_area']][]=$row;
}







if (count(array_keys($SERVIZI))>0) {

} else {
    echo "Nessun servizio attivo per questa visita!";
    exit;
}
$progressivonote=0;
$note=array();

$serviziodisder['dis_esterna_adulticida_alati']="Disinfestazione esterna adulticida contro insetti alati";
$serviziodisder['dis_esterna_striscianti']="Disinfestazione esterna contro insetti striscianti";
$serviziodisder['dis_esterna_ovo_larvicida']="Disinfestazione esterna ovo-larvicida";
//$serviziodisder['dis_rete_fogniaria']="Disinfestazione rete fogniaria";
$serviziodisder['dis_gel_blatte']="Disinfestazione sistema soluzione gel per blatte";
//$serviziodisder['dis_fumigazione']="Disinfestazione sistema di fumigazione";
//$serviziodisder['der_eko7day']="Derattizzazione Eko7day";
//$serviziodisder['der_ekologica']="Derattizzazione Ekologica";
//$serviziodisder['dis_ekologica']="Disinfestazione Ekologica";
//$serviziodisder['inst_ekomille']="Installazione Ekomille";
//$serviziodisder['disinfezione']="Disinfezione";
$serviziodisder['dis_alati']="Disinfestazione Insetti Alati";
$serviziodisder['dis_striscianti']="Disinfestazione Insetti Striscianti";
$serviziodisder['dis_derrate']="Disinfestazione Insetti Delle Derrate";

$serviziodisder['dis_vespecalabroni']="Intervento Vespe/Calabroni";
$serviziodisder['dis_formiche']="Intervento Formiche";
$serviziodisder['dis_altrointervento']="Altro";
$serviziodisder['dis_altrointerventotesto']=$VISITA['dis_altrointerventotesto'];

$principio['difenacoum']="Difenacoum";
$principio['bromadiolone']="Bromadiolone";
$principio['brodifacoum']="Brodifacoum";
//$principio['coldif']="Coldif";
$principio['difetialone']="Difetialone";
//$principio['piastra_collante']="Piastra Collante";
//$principio['ekomille']="Ekomille";
//$principio['esca_virtuale']="Esca Virtuale";
$principio['altroprincipio']="Altro";
$principio['altroprincipiotesto']=$VISITA['altroprincipiotesto'];

$principioinsetti['altroprincipio_insetti']="Altro";
$principioinsetti['altroprincipio_insettitesto']=$VISITA['altroprincipio_insettitesto'];


$monitoraggio['mon_roditori']="Monitoraggio Roditori";
$monitoraggio['mon_striscianti']="Monitoraggio Insetti Striscianti";
$monitoraggio['mon_roditori_e_striscianti']="Monitoraggio Roditori e Insetti Striscianti";
$monitoraggio['mon_volanti']="Monitoraggio Insetti Volanti";
$monitoraggio['mon_derrate']="Monitoraggio Insetti delle Derrate";
$monitoraggio['mon_der_ecologica']="Derattizzazione Ecologica";
$monitoraggio['altromonitoraggio']="Altro monitoraggio";
$monitoraggio['altromonitoraggiotesto']=$VISITA['altromonitoraggiotesto'];

//genero la prima pagina


$y=nuovapagina(1,0, 0);

$varx=10;

$border=0;

$oldy=$y;

$pdf->SetTextColor(4, 63, 167);
$pdf->SetFont('helvetica', 'BI', 14);
$pdf->MultiCell(100, 8, "SERVIZI", $border, 'L', 1, 1, $x+$varx, $y-8, true);
$pdf->SetTextColor(0, 0, 0);

foreach ($serviziodisder as $key=>$value) {
    if ($VISITA[$key]=='') {
        $VISITA[$key]=0;
    }

    if ($key=="dis_altrointerventotesto" ) {
        $border=0;
        $pdf->MultiCell(56, 10, $value, $border, 'L', 1, 1, $x+$varx, $y, true);
    } else {
        $border=0;
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(6, 5, $checkbox[$VISITA[$key]], $border, 'L', 1, 1, $x+$varx, $y, true);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(100, 5, $value, $border, 'L', 1, 1, $x+6+$varx, $y, true);
    }
    $y+=5;

}

$y=$oldy;
$varx=115;

$pdf->SetTextColor(4, 63, 167);
$pdf->SetFont('helvetica', 'BI', 14);
$pdf->MultiCell(96, 8, "PRINCIPIO ATTIVO DERATTIZZAZIONE", $border, 'L', 1, 1, $x+$varx, $y-8, true);
$pdf->SetTextColor(0, 0, 0);

foreach ($principio as $key=>$value) {
    if ($VISITA[$key]=='') {
        $VISITA[$key]=0;
    }
    if ($key=="altroprincipiotesto" ) {
        $border=0;
        $pdf->MultiCell(76, 10, $value, $border, 'L', 1, 1, $x+$varx, $y, true);
    } else {
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(6, 5, $checkbox[$VISITA[$key]], $border, 'L', 1, 1, $x+$varx, $y, true);
        $pdf->SetFont('helvetica', '', 10);
        $border=0;
        $pdf->MultiCell(70, 5, $value, $border, 'L', 1, 1, $x+6+$varx, $y, true);
    }
    $y+=5;
}
$y+=10;

$pdf->SetTextColor(4, 63, 167);
$pdf->SetFont('helvetica', 'BI', 14);
$pdf->MultiCell(96, 8, "PRINCIPIO ATTIVO INSETTICIDA", $border, 'L', 1, 1, $x+$varx, $y-8, true);
$pdf->SetTextColor(0, 0, 0);

foreach ($principioinsetti as $key=>$value) {
    if ($VISITA[$key]=='') {
        $VISITA[$key]=0;
    }
    if ($key=="altroprincipio_insettitesto" ) {
        $border=0;
        $pdf->MultiCell(76, 10, $value, $border, 'L', 1, 1, $x+$varx, $y, true);
    } else {
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(6, 5, $checkbox[$VISITA[$key]], $border, 'L', 1, 1, $x+$varx, $y, true);
        $pdf->SetFont('helvetica', '', 10);
        $border=0;
        $pdf->MultiCell(70, 5, $value, $border, 'L', 1, 1, $x+6+$varx, $y, true);
    }
    $y+=5;
}
$y+=5;



$y=$oldy;
$varx=215;

$pdf->SetTextColor(4, 63, 167);
$pdf->SetFont('helvetica', 'BI', 14);
$pdf->MultiCell(56, 8, "MONITORAGGIO", $border, 'L', 1, 1, $x+$varx, $y-8, true);
$pdf->SetTextColor(0, 0, 0);

foreach ($monitoraggio as $key=>$value) {
    if ($VISITA[$key]=='') {
        $VISITA[$key]=0;
    }
    if ($key=="altromonitoraggiotesto") {
        $border=0;
        $pdf->MultiCell(86, 10, $value, $border, 'L', 1, 1, $x+$varx, $y, true);
    } else {
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->MultiCell(6, 5, $checkbox[$VISITA[$key]], $border, 'L', 1, 1, $x+$varx, $y, true);
        $pdf->SetFont('helvetica', '', 10);
        $border=0;
        $pdf->MultiCell(80, 5, $value, $border, 'L', 1, 1, $x+6+$varx, $y, true);
    }
    $y+=5;
}

$y=145;
$varx=5;

$border=0;
$pdf->SetTextColor(4, 63, 167);
$pdf->SetFont('helvetica', 'BI', 14);
$pdf->MultiCell(256, 8, "NOTE E AZIONI CORRETTIVE", $border, 'L', 1, 1, $x+$varx+110, $y, true);
$y+=8;

$pdf->SetTextColor(0, 0, 0);

if ($VISITA['Note_finali']!='') {
    $border=1;
}

$pdf->SetFont('helvetica', 'I', 10);
$pdf->MultiCell(285, 15, $VISITA['Note_finali'], $border, 'L', 1, 1, $x+$varx, $y, true);
$border=0;

//------------------------------------------------------------------------------------------------------
// FIRME
//------------------------------------------------------------------------------------------------------

// The '@' character is used to indicate that follows an image data stream and not an image file name


$immaginefirmatecnico=str_replace("data:image/png;base64,","",$VISITA['firmatecnico']);
$imgdatatecnico=base64_decode($immaginefirmatecnico);

$immaginefirma=str_replace("data:image/png;base64,","",$VISITA['firmacliente']);
$imgdata=base64_decode($immaginefirma);

$imgfirma=$IMMAGINIOPERATORE[0]['file'];

$varx=0;

$y=185+$var;
$x=5;
$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(95, 5, "Firma Tecnico: ", $border, 'L', 1, 1, $x+$varx, $y, true);
$pdf->Image($imgfirma, $x+$varx, $y+7, 80, 28, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);


$pdf->MultiCell(95, 5, "Firma Cliente: ".$VISITA['responsabile_presente'], $border, 'L', 1, 1, $x+$varx+180, $y, true);
$pdf->Image('@'.$imgdata, $x+$varx+180, $y, 50, 30);


//FINE PRIMA PAGINA

$varx=20;

foreach ($SERVIZI as $scheda=>$arrayserv) :

/*    echo "<pre>";
    print_r($arrayserv);
    echo "</pre>"; */

    foreach ($arrayserv as $servmaster) :

        $tmpserv=$servmaster[0];

        if ($tmpserv['servizio']=='SE') continue;

        if ($_GET['d']==1) {
            echo "TMPSERV:<br/><pre>";
            print_r($tmpserv);
            echo "</pre>";
        }

        nuovapagina();
        
        $totaleinsettiperarea=0;

        foreach ($servmaster as $serv) :

            if ($serv['stato_postazione']=="Controllo eseguito") {
                $serv['stato_postazione']="Regolare";
            }
            if ($serv['stato_postazione']=="Nuova Installazione") {
                $serv['stato_postazione']="Nuova";
            }

        if ($_GET['d']==1) {
            echo "<pre>";
            print_r($serv);
            echo "<pre>";
            exit;
        }

        if ($serv['note_per_cliente']!='') {
            $note[$progressivonote]=$serv['note_per_cliente'];
            $progressivonote++;
            $serv['note_per_cliente']=" ($progressivonote) ";
        }

        if ($y>=$ymassimo) {
            //bisogna creare una nuova pagina
            nuovapagina();
        }

        $ampiezzay=6;

        //(i) servizio=='D'
        if ($serv['servizio']=='D') :
            $ampiezzay=12;

            if ($sogliaOrdine[$serv['stato_esca_roditori']]>$sogliaOrdine[$serv['SogliaEsca']]) {
                // set color for background
                $pdf->SetFillColor(255, 255, 127);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }

            $pdf->MultiCell(30, 12, $serv['nome'], $border, 'C', 1, 1, 5+$varx, $y, true);
            $pdf->MultiCell(35, 12, $serv['tipo'], $border, 'C', 1, 1, 35+$varx, $y, true);
            $pdf->MultiCell(50, 12, $serv['modello'], $border, 'C', 1, 1, 70+$varx, $y, true);
            $pdf->MultiCell(50, 12, $serv['prodotto'], $border, 'C', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(25, 12, $testo[$serv['stato_esca_roditori']], $border, 'C', 1, 1, 170+$varx, $y, true);
            $pdf->MultiCell(25, 12, $serv['stato_postazione'], $border, 'C', 1, 1, 195+$varx, $y, true);
            if ($serv['stato_postazione']=='Regolare') {
                $pdf->SetFillColor(255, 255, 255);
            } elseif ($serv['stato_postazione']=='Nuova') {
                $pdf->SetFillColor(0, 255, 0);
            } else {
                $pdf->SetFillColor(255, 255, 0);
            }
            if ($sogliaOrdine[$serv['stato_esca_roditori']]>$sogliaOrdine[$serv['SogliaEsca']]) {
                // set color for background
                $pdf->SetFillColor(255, 255, 127);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->MultiCell(25, 12, $serv['tracce'], $border, 'C', 1, 1, 220+$varx, $y, true);
            $pdf->MultiCell(25, 12, $serv['specie'], $border, 'C', 1, 1, 245+$varx, $y, true);
            $pdf->MultiCell(15, 12, $serv['note_per_cliente'], $border, 'C', 1, 1, 270+$varx, $y, true);

        endif;
        //(f) servizio=='D'

            //(i) servizio=='IVE'
            if ($serv['servizio']=='IVE') :
                $ampiezzay=12;

                if ($sogliaOrdineVolanti[$serv['stato_infestazione_boccione']]>$sogliaOrdineVolanti[$serv['SogliaBoccione']]) {
                    // set color for background
                    $pdf->SetFillColor(255, 255, 127);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }

                $pdf->MultiCell(30, 12, $serv['nome'], $border, 'C', 1, 1, 5+$varx, $y, true);
                $pdf->MultiCell(35, 12, $serv['tipo'], $border, 'C', 1, 1, 35+$varx, $y, true);
                $pdf->MultiCell(50, 12, $serv['modello'], $border, 'C', 1, 1, 70+$varx, $y, true);
                $pdf->MultiCell(50, 12, $serv['prodotto'], $border, 'C', 1, 1, 120+$varx, $y, true);
                $pdf->MultiCell(25, 12, $serv['stato_infestazione_boccione'], $border, 'C', 1, 1, 170+$varx, $y, true);
                if ($serv['stato_postazione']=='Regolare') {
                    $pdf->SetFillColor(255, 255, 255);
                } elseif ($serv['stato_postazione']=='Nuova') {
                    $pdf->SetFillColor(0, 255, 0);
                } else {
                    $pdf->SetFillColor(255, 255, 0);
                }
                $pdf->MultiCell(25, 12, $serv['stato_postazione'], $border, 'C', 1, 1, 195+$varx, $y, true);
                if ($sogliaOrdineVolanti[$serv['stato_infestazione_boccione']]>$sogliaOrdineVolanti[$serv['SogliaBoccione']]) {
                    // set color for background
                    $pdf->SetFillColor(255, 255, 127);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                $pdf->MultiCell(15, 12, $serv['note_per_cliente'], $border, 'C', 1, 1, 220+$varx, $y, true);

            endif;
            //(f) servizio=='IVE'


            //(i) servizio!='D'
        if ($serv['servizio']=='ID' or $serv['servizio']=='DA' or $serv['servizio']=='IS' or $serv['servizio']=='IV' or $serv['servizio']=='RS') :
            $pdf->SetFillColor(255, 255, 255);

            $insetti=Array();
            $totaleinsetti=0;
            $evidenzapostazione=false;

            if ($_GET['d']==1) {
                echo "<pre>";
                print_r($serv);
                echo "</pre>";
            }

            if ($serv['Infestante1']>0) {
                if ($serv['conteggio_infestante_1']>0) {
                    $totaleinsetti+=$serv['conteggio_infestante_1'];
                } else {
                    $serv['conteggio_infestante_1']=0;
                }
                $insetti[]=$serv['nome_infestante1'].": ".$serv['conteggio_infestante_1']." / ".$serv['Soglia1'];
                if ($serv['conteggio_infestante_1']>=$serv['Soglia1'] and $serv['Soglia1']>0) {
                    $evidenzapostazione=true;
                }
            }
            if ($serv['Infestante2']>0) {
                if ($serv['conteggio_infestante_2']>0) {
                    $totaleinsetti+=$serv['conteggio_infestante_2'];
                } else {
                    $serv['conteggio_infestante_2']=0;
                }
                $insetti[]=$serv['nome_infestante2'].": ".$serv['conteggio_infestante_2']." / ".$serv['Soglia2'];
                if ($serv['conteggio_infestante_2']>=$serv['Soglia2'] and $serv['Soglia2']>0) {
                    $evidenzapostazione=true;
                }
            }
            if ($serv['Infestante3']>0) {
                if ($serv['conteggio_infestante_3']>0) {
                    $totaleinsetti+=$serv['conteggio_infestante_3'];
                } else {
                    $serv['conteggio_infestante_3']=0;
                }
                $insetti[]=$serv['nome_infestante3'].": ".$serv['conteggio_infestante_3']." / ".$serv['Soglia3'];
                if ($serv['conteggio_infestante_3']>=$serv['Soglia3'] and $serv['Soglia3']>0) {
                    $evidenzapostazione=true;
                }
            }
            if ($serv['Infestante4']>0) {
                if ($serv['conteggio_infestante_4']>0) {
                    $totaleinsetti+=$serv['conteggio_infestante_4'];
                } else {
                    $serv['conteggio_infestante_4']=0;
                }
                $insetti[]=$serv['nome_infestante4'].": ".$serv['conteggio_infestante_4']." / ".$serv['Soglia4'];
                if ($serv['conteggio_infestante_4']>=$serv['Soglia4'] and $serv['Soglia4']>0) {
                    $evidenzapostazione=true;
                }
            }

            if ($_GET['d']>1) {
                echo "insetti: ".count($insetti);
            }

            $ampiezzay=count($insetti)*6;
            if ($ampiezzay==0) {
                $ampiezzay=6;
            }

            if (($y+$ampiezzay)>$ymassimo) {
                nuovapagina(0);
            }

            if (count($insetti)>0) {
                //ci sono indicati i dettagli degli insetti
                //allore metto nella colonna Fauna, tutti gli insetti con accanto il loro conteggio
                //e nella colonna destra il totale postazione
            } else {
                //altrimenti indico soltanto la fauna "generica" e a destra il totale postazione
                $totaleinsetti=sprintf("%d",$serv[$campoconteggioinsetti[$serv['servizio']]]);
                $insetti[]=$nomeinsettogenerico[$serv['servizio']].": ".$totaleinsetti;
                if ($serv['SogliaTotalePerPostazione']>0 and $totaleinsetti>=$serv['SogliaTotalePerPostazione']) {
                    $evidenzapostazione=true;
                }
            }

            $stringainsetti=join("\n",$insetti);

            if ($evidenzapostazione==true) {
                $pdf->SetFillColor(255, 255, 127);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }

            $totaleinsettiperarea+=$totaleinsetti;

            // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
            //$pdf->MultiCell(55, 40, '[VERTICAL ALIGNMENT - MIDDLE] '.$txt, 1, 'J', 1, 0, '', '', true, 0, false, true, 40, 'M');

            $pdf->MultiCell(25, $ampiezzay, $serv['nome'], $border, 'C', 1, 1, 5+$varx, $y, true, 0, false, true, $ampiezzay, 'M');
                $pdf->MultiCell(30, $ampiezzay, $serv['tipo'], $border, 'C', 1, 1, 30+$varx, $y, true, 0, false, true, $ampiezzay, 'M');
                $pdf->MultiCell(30, $ampiezzay, $serv['modello'], $border, 'C', 1, 1, 60+$varx, $y, true, 0, false, true, $ampiezzay, 'M');
                $pdf->MultiCell(30, $ampiezzay, $serv['prodotto'].' ', $border, 'C', 1, 1, 90+$varx, $y, true, 0, false, true, $ampiezzay, 'M');
                $pdf->MultiCell(85, $ampiezzay, $stringainsetti, $border, 'C', 1, 1, 120+$varx, $y, true, 0, false, true, $ampiezzay, 'M');
                $pdf->MultiCell(25, $ampiezzay, $totaleinsetti, $border, 'C', 1, 1, 205+$varx, $y, true, 0, false, true, $ampiezzay, 'M');
            if ($serv['stato_postazione']=='Regolare') {
                $pdf->SetFillColor(255, 255, 255);
            } elseif ($serv['stato_postazione']=='Nuova') {
                $pdf->SetFillColor(0, 255, 0);
            } else {
                $pdf->SetFillColor(255, 255, 0);
            }
                $pdf->MultiCell(25, $ampiezzay, $serv['stato_postazione'], $border, 'C', 1, 1, 230+$varx, $y, true, 0, false, true, $ampiezzay, 'M');
            if ($evidenzapostazione==true) {
                $pdf->SetFillColor(255, 255, 127);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->MultiCell(15, $ampiezzay, $serv['note_per_cliente'], $border, 'C', 1, 1, 255+$varx, $y, true, 0, false, true, $ampiezzay, 'M');


        endif;
        //(f) servizio!='D'

        $y+=$ampiezzay;



    endforeach; //foreach ($servmaster as $serv) :

/*
 *  //LEGENDA
    //La legenda occupa in verticale circa 30

    if (($y+30+6)>$ymassimo) {
        nuovapagina(0);
    }
    $y+=6;

    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetFont('helvetica', '', 12);

    if ($tmpserv['servizio']=='D') :
        $pdf->MultiCell(285, 10, "LEGENDA", $border, 'C', 1, 1, 5+$varx, $y, true);
        $y+=10;
        //in base alla soglia, mettiamo la legenda

        $pdf->MultiCell(30, 20, "0% Ottimo", $border, 'L', 1, 1, 5+$varx, $y, true);

        if ($sogliaOrdine[$tmpserv['SogliaEsca']]==1) {
            //25% non è sufficiente
            $pdf->SetFillColor(255, 255, 127);
            $pdf->MultiCell(30, 20, "10% Insuff", $border, 'L', 1, 1, 30+$varx, $y, true);
            $pdf->MultiCell(30, 20, "25% Insuff", $border, 'L', 1, 1, 60+$varx, $y, true);
            $pdf->MultiCell(30, 20, "50% Insuff", $border, 'L', 1, 1, 90+$varx, $y, true);
            $pdf->MultiCell(40, 20, "75% Insuff", $border, 'L', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(40, 20, "100% Insuff", $border, 'L', 1, 1, 160+$varx, $y, true);
        }

        if ($sogliaOrdine[$tmpserv['SogliaEsca']]==2) {
            //50% non è sufficiente
            $pdf->MultiCell(30, 20, "10% Suff", $border, 'L', 1, 1, 30+$varx, $y, true);
            $pdf->SetFillColor(255, 255, 127);
            $pdf->MultiCell(30, 20, "25% Insuff", $border, 'L', 1, 1, 60+$varx, $y, true);
            $pdf->MultiCell(30, 20, "50% Insuff", $border, 'L', 1, 1, 90+$varx, $y, true);
            $pdf->MultiCell(40, 20, "75% Insuff", $border, 'L', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(45, 20, "100% Insuff", $border, 'L', 1, 1, 160+$varx, $y, true);
        }

        if ($sogliaOrdine[$tmpserv['SogliaEsca']]==3) {
            //50% non è sufficiente
            $pdf->MultiCell(30, 20, "10% Suff", $border, 'L', 1, 1, 30+$varx, $y, true);
            $pdf->MultiCell(30, 20, "25% Suff", $border, 'L', 1, 1, 60+$varx, $y, true);
            $pdf->SetFillColor(255, 255, 127);
            $pdf->MultiCell(30, 20, "50% Insuff", $border, 'L', 1, 1, 90+$varx, $y, true);
            $pdf->MultiCell(40, 20, "75% Insuff", $border, 'L', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(45, 20, "100% Insuff", $border, 'L', 1, 1, 160+$varx, $y, true);
        }

        if ($sogliaOrdine[$tmpserv['SogliaEsca']]==4) {
            //75% non è sufficiente
            $pdf->MultiCell(30, 20, "10% Buono", $border, 'L', 1, 1, 30+$varx, $y, true);
            $pdf->MultiCell(30, 20, "25% Buono", $border, 'L', 1, 1, 60+$varx, $y, true);
            $pdf->MultiCell(30, 20, "50% Suff", $border, 'L', 1, 1, 90+$varx, $y, true);
            $pdf->SetFillColor(255, 255, 127);
            $pdf->MultiCell(40, 20, "75% Insuff", $border, 'L', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(45, 20, "100% Insuff", $border, 'L', 1, 1, 160+$varx, $y, true);
        }

        if ($sogliaOrdine[$tmpserv['SogliaEsca']]>=4) {
            //100% non è sufficiente
            $pdf->MultiCell(30, 20, "10% Buono", $border, 'L', 1, 1, 30+$varx, $y, true);
            $pdf->MultiCell(30, 20, "25% Buono", $border, 'L', 1, 1, 60+$varx, $y, true);
            $pdf->MultiCell(30, 20, "50% Suff", $border, 'L', 1, 1, 90+$varx, $y, true);
            $pdf->MultiCell(40, 20, "75% Suff", $border, 'L', 1, 1, 120+$varx, $y, true);
            $pdf->SetFillColor(255, 255, 127);
            $pdf->MultiCell(45, 20, "100% Insufficiente", $border, 'C', 1, 1, 160+$varx, $y, true);
        }

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->MultiCell(85, 20, "Soglia di rischio: \n >".$sogliaPercentuale[$sogliaOrdine[$tmpserv['SogliaEsca']]]. " consumo esca postazione", $border, 'C', 1, 1, 205+$varx, $y, true);
    endif;

        if ($serv['servizio']=='ID' or $serv['servizio']=='DA' or $serv['servizio']=='IS' or $serv['servizio']=='IV'):
        if ($tmpserv['SogliaTotalePerArea']!='') {
            $pdf->SetFont('helvetica', '', 14);
            $pdf->MultiCell(140, 10, "Totale Catture", $border, 'C', 1, 1, 5+$varx, $y, true);
            $pdf->MultiCell(145, 10, "Soglia di rischio", $border, 'C', 1, 1, 145+$varx, $y, true);
            $y+=10;

            if ($totaleinsettiperarea>$tmpserv['SogliaTotalePerArea']) {
                $pdf->SetFillColor(255, 255, 127);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->MultiCell(140, 10, $totaleinsettiperarea, $border, 'C', 1, 1, 5+$varx, $y, true);
            $pdf->MultiCell(145, 10, ">".$tmpserv['SogliaTotalePerArea']." infestanti per area ", $border, 'C', 1, 1, 145+$varx, $y, true);

        } else if ($tmpserv['SogliaTotalePerPostazione']!='') {
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->MultiCell(285, 10, "LEGENDA", $border, 'C', 1, 1, 5+$varx, $y, true);
            $y+=10;

            if ($tmpserv['SogliaTotalePerPostazione']==1) {
                $tmpsoglia=0;
                $pdf->MultiCell(90, 15, "0 = Ottimo", $border, 'C', 1, 1, 5+$varx, $y, true);
                $pdf->SetFillColor(255, 255, 127);
                $pdf->MultiCell(100, 15, "=/> 1 Insufficiente", $border, 'C', 1, 1, 95+$varx, $y, true);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->MultiCell(95, 15, "Soglia di rischio: \n >".$tmpserv['SogliaTotalePerPostazione']." infestanti per postazione", $border, 'C', 1, 1, 195+$varx, $y, true);
            } else {
                $tmpsoglia=$tmpserv['SogliaTotalePerPostazione']-1;
                $pdf->MultiCell(90, 15, "< ".$tmpserv['SogliaTotalePerPostazione']." Ottimo", $border, 'C', 1, 1, 5+$varx, $y, true);
                $pdf->SetFillColor(255, 255, 127);
                $pdf->MultiCell(100, 15, "=/>".$tmpserv['SogliaTotalePerPostazione']." Insufficiente", $border, 'C', 1, 1, 95+$varx, $y, true);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->MultiCell(95, 15, "Soglia di rischio: \n >".$tmpserv['SogliaTotalePerPostazione']." infestanti per postazione", $border, 'C', 1, 1, 195+$varx, $y, true);
            }




        }
    endif;
*/
endforeach; //foreach ($arrayserv as $servmaster) :

endforeach; // foreach ($SERVIZI as $scheda=>$servizio)

// ---------------------------------------------------------



//--- se ci sono note, genero un'ultima pagina con le note


if ($progressivonote>0) {


    $y=nuovapagina(0,0,1);

    $y=40;
    $border=0;
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->MultiCell(275, 6, 'NOTE', $border, 'C', 1, 1, 15+$varx, $y, true);
    $y+=6;
    $pdf->SetFont('helvetica', '', 12);

    for ($i=1;$i<=$progressivonote;$i++) {
        $pdf->MultiCell(10, 6, "($i)", $border, 'C', 1, 1, 5+$varx, $y, true);
        $pdf->MultiCell(265, 6, $note[$i-1], $border, 'L', 1, 1, 15+$varx, $y, true);
        $y+=6;
    }




}




if ($debug) {
//Close and output PDF document

$pdf->Output('Certificato-'.$hash.'-'.$NumCertificato.'.pdf', 'I');

exit;
} else {
    if ($_GET['invia']==1) {
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$directoryfiles.'/Certificati'.DIRECTORY_SEPARATOR.'Certificato-'.$hash.'-'.$NumCertificato.'.pdf', 'F');
        $urlfile="https://".$_SERVER['HTTP_HOST'].$directoryfiles."/Certificati/Certificato-".$hash.'-'.$NumCertificato.".pdf";
        $query="UPDATE $tbvisite SET filepdf=? WHERE codice_visita=?";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($urlfile,$codicevisita));
        setNotificheCRUD("APP","SUCCESS","controlloPdf.php","File inserito nel db: $urlfile");
        $attachment=$pdf->Output('Certificato-'.$hash.'-'.$NumCertificato.'.pdf', 'S');
        $nomeattachment='Certificato-'.$hash.'-'.$NumCertificato.'.pdf';

    } else {
        $pdf->Output('Certificato-'.$hash.'-'.$NumCertificato.'.pdf', 'I');
        //lo salva pure
        $pdf->Output($_SERVER['DOCUMENT_ROOT'].$directoryfiles.'/Certificati'.DIRECTORY_SEPARATOR.'Certificato-'.$hash.'-'.$NumCertificato.'.pdf', 'F');
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
                    var url='https://<?php echo $_SERVER[HTTP_HOST].$sitedir;?>module.php?modname=Visite';
                    window.location.href = url;
                }, 2000);
            </script>
        </div>
    </div>

<?php } else {
    require 'class.phpmailer.php';


    $EMAILADMIN['email']="info@studioweb19.it";
    $EMAILADMIN['name']=$projecttitle;

    $testo="Buongiorno, inviamo in allegato il certificato relativo all'intervento effettuato presso la vostra sede in data ".$VISITA['data_intervento_formatted'];
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
                        var url='https://<?php echo $_SERVER[HTTP_HOST].$sitedir;?>module.php?modname=Visite';
                        window.location.href = url;
                    }, 2000);
                </script>
            </div>
        </div>

    <?php } else {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
        setNotificheCRUD("APP","ERROR","controlloPdf.php","Mail non inviata: $codicevisita");
    }
}


/* --------------------------------------------------------------------------------------------------------------------------------------------------- */
/* ---------------------------------------------------------------------- (f) EMAIL ------------------------------------------------------------------ */
/* --------------------------------------------------------------------------------------------------------------------------------------------------- */


function nuovapagina($conintestazione=1,$conservizio=1,$logopiccolo=1) {
    global $VISITA;
    global $tmpserv;
    global $titoloscheda;
    global $pdf;
    global $border,$y,$var,$varx,$vary;
    $pdf->SetFont('times', '', 14);
    $pdf->AddPage('L', 'A4');
    $pdf->setJPEGQuality(75);
    $pdf->setCellPaddings(1, 1, 1, 1);
    $pdf->setCellMargins(1, 1, 1, 1);
    $pdf->SetFillColor(255, 255, 255);
    $var=-13;
    $varx=-5;

    if ($logopiccolo==0) {
        $pdf->Image('intestazioneCertificato.jpg', 10, 20+$var, 285, 64, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);

        $pdf->SetFont('helvetica', 'B', 12);
        $border=0;
        $varx=0;
        $vary=10;

        $y=56+$vary;
    } else {

        $pdf->Image('intestazioneCertificatologopiccolo.jpg', 10, 25+$var, 20, 20, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);

        $pdf->SetFont('helvetica', 'B', 12);

        $border=0;
        $varx=30;
        $vary=10;

        $y=1+$vary;
    }



    //Riga Cliente
    $pdf->MultiCell(280, 8, "Cliente: ".$VISITA['Nome']." ".$VISITA['Cognome'], $border, 'L', 1, 1, 5+$varx, $y, true);
    $y+=6;

    if ($VISITA['provincia']!='') {
        $provincia="(".$VISITA['provincia'].")";
    } else {
        $provincia='';
    }
    if ($VISITA['CAP']!='') {
        $CAP="CAP ".$VISITA['CAP'];
    } else {
        $CAP='';
    }

    //Riga Sede
    $pdf->MultiCell(280, 8, "Sede: ".$VISITA['sede'].' '.$VISITA['indirizzo'].', '.$CAP.' '.$VISITA['citta'].' '.$provincia, $border, 'L', 1, 1, 5+$varx, $y, true);
    $y+=6;

    //Riga Area
    if ($conservizio==1) {
        $pdf->MultiCell(280, 8, "Area: " . $tmpserv['Area'] . " (" . $tmpserv['IE'] . ')', $border, 'L', 1, 1, 5 + $varx, $y, true);
        $y += 6;
    }

    //Riga Intervento
    $border=0;

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(15, 6, "Inizio: ", $border, 'L', 1, 1, 5+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(35, 6, $VISITA['data_inizio_visita_formatted'].' '.$VISITA['ora_inizio_visita'], $border, 'L', 1, 1, 20+$varx, $y, true);

    $varx+=50;

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(12, 6, "Fine: ", $border, 'L', 1, 1, 5+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(35, 6, $VISITA['data_intervento_formatted'].' '.$VISITA['ora_fine_visita'], $border, 'L', 1, 1, 17+$varx, $y, true);

    $varx+=50;

/*    $border=0;
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(21, 6, "Operatore: ", $border, 'L', 1, 1, 63+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(35, 6, $VISITA['Nome']." ".$VISITA['Cognome'], $border, 'L', 1, 1, 84+$varx, $y, true);
*/

    //$varx=-15;


    $border=0;
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(15, 6, "R.C.Q.: ", $border, 'L', 1, 1, 5+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(85, 6, $VISITA['persona_di_riferimento'], $border, 'L', 1, 1, 20+$varx, $y, true);


    $varx=-15;


    $border=0;
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(25, 6, "Email cliente:", $border, 'L', 1, 1, 204+$varx, $y, true);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(80, 6, $VISITA['email'], $border, 'L', 1, 1, 229+$varx, $y, true);

    $y+=8;

    $varx=0;

    //Riga Servizio
    if ($conservizio==1) {
        $pdf->SetFont('helvetica', 'B', 14);
        $border=0;

        $pdf->MultiCell(285, 8, $titoloscheda[$tmpserv['servizio']], $border, 'L', 1, 1, 5, $y, true);
        $y+=8;
    }

    $pdf->SetFont('helvetica', '', 10);
    $border=1;

    if ($conintestazione==1) {
        //(i) intestazione tabelle
        if ($tmpserv['servizio']=='ID' or $tmpserv['servizio']=='DA' or $tmpserv['servizio']=='IS' or $tmpserv['servizio']=='IV' or $tmpserv['servizio']=='RS'):

            $pdf->MultiCell(25, 8, "Nome", $border, 'C', 1, 1, 5+$varx, $y, true);
            $pdf->MultiCell(30, 8, "Tipo ", $border, 'C', 1, 1, 30+$varx, $y, true);
            $pdf->MultiCell(30, 8, "Modello", $border, 'C', 1, 1, 60+$varx, $y, true);
            $pdf->MultiCell(30, 8, "Prodotto", $border, 'C', 1, 1, 90+$varx, $y, true);
            $pdf->MultiCell(85, 8, "Fauna Rilevato/Soglia", $border, 'C', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Tot Infest", $border, 'C', 1, 1, 205+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Stato", $border, 'C', 1, 1, 230+$varx, $y, true);
            $pdf->MultiCell(15, 8, "Note", $border, 'C', 1, 1, 255+$varx, $y, true);

        endif; //ID IS DA IV

        if ($tmpserv['servizio']=='D') :

            $pdf->MultiCell(30, 8, "Nome", $border, 'C', 1, 1, 5+$varx, $y, true);
            $pdf->MultiCell(35, 8, "Tipo ", $border, 'C', 1, 1, 35+$varx, $y, true);
            $pdf->MultiCell(50, 8, "Modello", $border, 'C', 1, 1, 70+$varx, $y, true);
            $pdf->MultiCell(50, 8, "Prodotto", $border, 'C', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Consumo", $border, 'C', 1, 1, 170+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Stato", $border, 'C', 1, 1, 195+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Tracce", $border, 'C', 1, 1, 220+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Specie", $border, 'C', 1, 1, 245+$varx, $y, true);
            $pdf->MultiCell(15, 8, "Note", $border, 'C', 1, 1, 270+$varx, $y, true);

        endif; //D

        if ($tmpserv['servizio']=='IVE') :

            $pdf->MultiCell(30, 8, "Nome", $border, 'C', 1, 1, 5+$varx, $y, true);
            $pdf->MultiCell(35, 8, "Tipo ", $border, 'C', 1, 1, 35+$varx, $y, true);
            $pdf->MultiCell(50, 8, "Modello", $border, 'C', 1, 1, 70+$varx, $y, true);
            $pdf->MultiCell(50, 8, "Prodotto", $border, 'C', 1, 1, 120+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Infestazione", $border, 'C', 1, 1, 170+$varx, $y, true);
            $pdf->MultiCell(25, 8, "Stato", $border, 'C', 1, 1, 195+$varx, $y, true);
            $pdf->MultiCell(15, 8, "Note", $border, 'C', 1, 1, 220+$varx, $y, true);

        endif; //D

        $y+=8;
        //(f) intestazione tabelle

        return $y;
    }


}


//============================================================+
// END OF FILE
//============================================================+
