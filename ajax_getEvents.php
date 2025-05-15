<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$queryspeciale="
SELECT pcs_attivita_clean.id FROM pcs_attivita_clean WHERE
(fine1 is not null AND fine1 < data_consigliata OR fine1 >= (data_consigliata + INTERVAL 1 DAY)) OR
(fine2 is not null AND fine2 < data_consigliata OR fine2 >= (data_consigliata + INTERVAL 1 DAY)) OR
(fine3 is not null AND fine3 < data_consigliata OR fine3 >= (data_consigliata + INTERVAL 1 DAY)) OR
(fine4 is not null AND fine4 < data_consigliata OR fine4 >= (data_consigliata + INTERVAL 1 DAY))";

if ($stmt=$dbh->query($queryspeciale)) {
    $eventicontimbraturespeciali=Array();
    while ($rowspeciali=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $eventicontimbraturespeciali[]=$rowspeciali['id'];
    }
}
//print_r($eventicontimbraturespeciali);


//to read parameters passed by php page
$file = 'ajaxlogeventsclean.txt';
// Open the file to get existing content
$current = "CLEAN\r\n";
// Rewrite data
$current .= date("Y-m-d H:i:s")."\r\n";
$current .= json_encode($_REQUEST)."\r\n";

//"start":"2020-12-28T00:00:00+01:00","end":"2021-02-08T00:00:00+01:00";
$startdate=substr($_GET['start'],0,10);
$enddate  =substr($_GET['end'],0,10);

$current .= $startdate."\r\n";
$current .= $enddate."\r\n";

$wheredate="(data_consigliata BETWEEN '$startdate' AND '$enddate') ";

if ($_GET['stato']!='') {
    $tmp=explode(",",$_GET['stato']);
    $stati=join("','",$tmp);
    $stati="'".$stati."'";
    $filtrostato=" AND stato IN ($stati) ";
} else {
    $filtrostato="";
}
if ($_GET['idsede']!='') {
    $tmp=explode(",",$_GET['idsede']);
    $sedi=join("','",$tmp);
    $sedi="'".$sedi."'";
    $filtrosede=" AND pcs_attivita_clean.id_sede IN ($sedi) ";
} else {
    $filtrosede="";
}
if ($_GET['idoperatore']!='') {
    $tmp=explode(",",$_GET['idoperatore']);
    $operatori=join("','",$tmp);
    $operatori="'".$operatori."'";
    $filtrooperatore=" AND (pcs_attivita_clean.operatore1 IN ($operatori) OR pcs_attivita_clean.operatore2 IN ($operatori) OR pcs_attivita_clean.operatore3 IN ($operatori) OR pcs_attivita_clean.operatore4 IN ($operatori)) ";
} else {
    $filtrooperatore="";
}

    $query="SELECT pcs_attivita_clean.id as idattivita,u1.txtcolor,u1.bgcolor,CONCAT_WS(' ',u1.cognome,' ',u1.nome,'<br/>',u2.cognome,' ',u2.nome,'<br/>',u3.cognome,' ',u3.nome,'<br/>',u4.cognome,' ',u4.nome) as squadra,pcs_attivita_clean.id as idattivita,pcs_attivita_clean.stato as statoattivita,CONCAT(pcs_clienti.nome,' ',pcs_clienti.cognome) as nome_cliente,pcs_sedi_clienti.*,DATE_FORMAT(data_consigliata,'%d-%m-%Y') as data_consigliata_formatted, TIME_FORMAT(ora_consigliata_inizio,'%H:%i') as ora_consigliata_formatted,pcs_attivita_clean.* FROM pcs_attivita_clean JOIN pcs_users u1 ON u1.id_user=pcs_attivita_clean.operatore1 LEFT JOIN pcs_users u2 ON u2.id_user=pcs_attivita_clean.operatore2 LEFT JOIN pcs_users u3 ON u3.id_user=pcs_attivita_clean.operatore3 LEFT JOIN pcs_users u4 ON u4.id_user=pcs_attivita_clean.operatore4
 JOIN pcs_sedi_clienti ON pcs_attivita_clean.id_sede=pcs_sedi_clienti.id  JOIN pcs_clienti ON pcs_clienti.id=pcs_sedi_clienti.id_cliente WHERE $wheredate $filtrostato $filtrooperatore $filtrosede ORDER by data_consigliata ASC";

    $current .= $query."\r\n";


// Write the contents back to the file
file_put_contents($file, $current);


//  {
//"title": "Meeting",
//    "start": "2019-08-12T10:30:00-05:00",
//    "end": "2019-08-12T12:30:00-05:00"
//  }

    if ($stmt=$dbh->query($query)) {
        $eventi=Array();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventi[]=$row;
        }

    } else {
        $ret['result']=false;
        $ret['error']="Errore accesso al db";
        $ret['query']=$query;
        echo json_encode($ret);
        exit;
    }


// Require our Event class and datetime utilities
require 'utils.php';

// Short-circuit if the client did not give us a date range.
//if (!isset($_GET['start']) || !isset($_GET['end'])) {
//    die("Please provide a date range.");
//}

//$_GET['start']=date("Y")."-01-01";
//$_GET['end']=date("Y")."-12-31";

// Parse the start/end parameters.
// These are assumed to be ISO8601 strings with no time nor timeZone, like "2013-12-29".
// Since no timeZone will be present, they will parsed as UTC.
$range_start = parseDateTime($_GET['start']);
$range_end = parseDateTime($_GET['end']);

// Parse the timeZone parameter if it is present.
$time_zone = '';
if (isset($_GET['timeZone'])) {
    $time_zone = new DateTimeZone($_GET['timeZone']);
}

$output_arrays = array();
foreach ($eventi as $e) {


    if ($e['sede']=='') {
        $e['sede']="ATTENZIONE:SEDE MANCANTE!!!";
    }
    if ($_GET['idoperatore']>0) {
        $e['title']=$e['sede'];
    } else if ($_GET['idsede']!='') {
        $e['title']=$e['squadra'];
    } else {
        $e['title']=$e['sede'];
    }

    $e['title']=$e['sede'];

//$e['start']=$e['data_consigliata']."T".$e['ora_consigliata_formatted'];

    $e['start']=date("c", strtotime($e['data_consigliata'].' '.$e['ora_consigliata_inizio']));

    if ($e['ora_consigliata_fine']!='') {
        $e['end']=$e['data_consigliata']."T".$e['ora_consigliata_fine'];
        $e['end']=date("c", strtotime($e['data_consigliata'].' '.$e['ora_consigliata_fine']));
    }
    $e['description'].="<b>Cliente:</b><p>".$e['nome_cliente'].'</p>';
    $e['description'].="<b>Sede:</b><p>".$e['sede'].'</p>';
    $e['description'].="<b>Indirizzo:</b><p>".$e['indirizzo'].' '.$e['CAP'].' '.$e['citta'].' '.$e['provincia'].'</p>';
    $e['description'].="<b>Squadra:</b><p>".$e['squadra'].'</p>';

    //$e['description'].="<br/><b>txtcolor:</b>".$e['txtcolor'];
    //$e['description'].="<br/><b>bgcolor:</b>".$e['bgcolor'];
    //$e['description'].="<br/><b>idattivita:</b>".$e['idattivita'];

    if ($e['descrizione_attivita']!='') {
        $e['description'].="<h4>Descrizione attività</h4>".$e['descrizione_attivita']."<br/>";
    }
    $e['description'].="<p><b>Stato attività: </b>".$e['stato'].'</p>';

    $e['backgroundColor']=$e['bgcolor'];
    $e['textColor']=$e['txtcolor'];
    $e['borderColor']=$e['backgroundColor'];

    if ($e['stato']=='conclusa') {

        if (in_array($e['idattivita'],$eventicontimbraturespeciali)) {
          $e['backgroundColor']="#00ff00";
          $e['textColor']="#FF0000";
          $e['borderColor']=$e['backgroundColor'];
        } else {
          $e['backgroundColor']="#00ff00";
          $e['textColor']="#000000";
          $e['borderColor']=$e['backgroundColor'];
        }

        $e['description'].="<p><b>Data fine attività: </b>".$e['data_fine_attivita'].'</p>';
    }
    if ($e['stato']=='annullata') {
        $e['backgroundColor']="#ff0000";
        $e['textColor']="#000000";
        $e['borderColor']=$e['backgroundColor'];
    }


    $e['description'].="<p><b><a target='_blank' href='get_element.php?debug=0&idmod=80&idele=".$e['idattivita']."'>Vedi l'attività</a> </b></p>";
    $e['description'].="<p><b><a target='_blank' href='module.php?modname=AttivitaClean&debug=0&p[id]=".$e['idattivita']."'>Elimina l'attività o inserisci timbrature</a> </b></p>";
    $e['description'].="<p><b><a target='_blank' href='module.php?modname=Cantieri&p[id]=".$e['id_sede']."'>Vedi il cantiere</a> </b></p>";

    if ($e['stato']=='conclusa') {
        $e['description'].="<p><b><a target='_blank' href='controlloPdfAttivita.php?codice_attivita=".$e['idattivita']."'>Controllo PDF</a> </b></p>";
    }


    // Convert the input array into a useful Event object
    $event = new Event($e);

    // If the event is in-bounds, add it to the output
    if ($event->isWithinDayRange($range_start, $range_end)) {
        $output_arrays[] = $event->toArray();
    }

}
// Send JSON to the client.
echo json_encode($output_arrays);
exit;

?>
