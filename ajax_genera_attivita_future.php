<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$oggi=date("Y-m-d");
$last = date('Y-m-d',strtotime($oggi . "+60 days"));

if ($_GET['idap']>0) {
    $idap=$_GET['idap'];
    $query="SELECT * FROM pcs_programmazione where (id_attivita_primaria=$idap) AND (('$oggi' BETWEEN dal_giorno AND al_giorno) OR  ('$last' BETWEEN dal_giorno AND al_giorno)) ";

} else {
    $query="SELECT * FROM pcs_programmazione where ('$oggi' BETWEEN dal_giorno AND al_giorno) OR  ('$last' BETWEEN dal_giorno AND al_giorno) ";

}

//rigenero i servizi di tutte le attività primarie, solo se ancora da fare

$query11="SELECT * FROM pcs_attivita_clean where id_attivita_primaria IS NULL and stato='da_fare'";
$stmt11=$dbh->query($query11);
while ($row11=$stmt11->fetch(PDO::FETCH_ASSOC)) {
  //genero anche i servizi
  $queryinsert="REPLACE INTO pcs_attivita_servizi_effettuati (id,id_attivita,id_servizio,ordine) SELECT CONCAT(?,'|',pcs_servizi.id),?,pcs_servizi.id,1 FROM pcs_servizi where pcs_servizi.id_sede= (SELECT pcs_attivita_clean.id_sede FROM pcs_attivita_clean WHERE pcs_attivita_clean.id=?)";
  $stmtinsert=$dbh->prepare($queryinsert);
  $stmtinsert->execute(array($row11['id'],$row11['id'],$row11['id']));

}

$periodisospensione=array();
$queryperiodisospensione="SELECT * FROM pcs_periodi_sospensione ";
$stmtps=$dbh->query($queryperiodisospensione);
while ($rowps=$stmtps->fetch(PDO::FETCH_ASSOC)) {
    $periodisospensione[]=$rowps;
}

$idmod=80; //attivita clean

$cadenzareverse=array_flip($cadenzavalue);

$stmt=$dbh->query($query);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $elencoattivitadareplicare[]=$row;
}
if (count($elencoattivitadareplicare)>0) {
    //echo "<pre>";
    //print_r($elencoattivitadareplicare);
    //echo "</pre>";

    foreach ($elencoattivitadareplicare as $att) :
        $idele=$att['id_attivita_primaria'];

        //prima di tutto eliminiamo le attivita future ancora non fatte o non "sganciate"
        $errore=array();
        $dbh->beginTransaction();
        $querydel="DELETE FROM pcs_attivita_clean WHERE id_attivita_primaria=$idele AND data_consigliata>='$oggi' AND non_rigenerare='no' AND stato<>'conclusa'";
//        echo "<hr/>";
//        echo $querydel;
//        echo "<br/>";

//        echo "<br/>oggi=$oggi";
//        echo "<br/>dal_giorno=".$att['dal_giorno'];
//        echo "<br/>last=$last";
//        echo "<br/>al_giorno=".$att['al_giorno'];
//        echo "<br/>";


        $stmtdel=$dbh->query($querydel);

        //recupero l'attività da duplicare dalla tabella pcs_attivita_clean
        if ($el=getElemento($idmod,$idele)) {

            $startdate  =$att['dal_giorno'];
            $enddate    =min($last,$att['al_giorno']);

            $dateattivita=array();

            foreach ($giornominuscolo as $key=>$value) :
                if ($att[$value]==1) {
                    $day_number=$key;
                    $tmp=getDateForSpecificDayBetweenDates($startdate,$enddate,$day_number);
                    $dateattivita=array_merge($dateattivita,$tmp);
                }
            endforeach;

            sort($dateattivita);






        //echo "<pre>";
        //print_r($dateattivita);
        //echo "</pre>";

            if (count($dateattivita)>0) {
                $cadenzareplica=$cadenzareverse[$att['cadenza']];

                $contatoresettimane=-1;
                foreach ($dateattivita as $datasuccessiva) :

                    $contatoresettimane++;

                    //controllo che la data non sia nei periodi di sospensione delle attività come da tabella
                    $continue=0;
                    for ($i=0;$i<count($periodisospensione);$i++) {
                        $ps=$periodisospensione[$i];

                        if ($datasuccessiva>=$ps['data_inizio_sospensione'] && $datasuccessiva<=$ps['data_fine_sospensione']) {
                          //echo "datasuccessiva=$datasuccessiva";
                          //echo "ps[data_inizio]=".$ps['data_inizio_sospensione'];
                          //echo "ps[data_fine]=".$ps['data_fine_sospensione'];
                          //echo "<br/>";
                          $continue=1;
                        }
                    }
                    if ($continue==1) { continue; }

                    //controllo che la data non sia già passata
                    if ($datasuccessiva<$oggi) {
                        continue;
                    }

                    if ($cadenzareplica==0) { //MAI
                        continue;
                    }

                    //qui bisogna vedere se siamo
                    // ogni settimana
                    // ogni 2 settimane
                    // ogni 3 settimane
                    // ogni 4 settimane

                    //in base alla cadenza devo fare o meno la prossima settimana
                    //echo $datasuccessiva;
                    if ($contatoresettimane % $cadenzareplica != 0) {
                        //echo "ko<br/>";
                        continue;
                    }
                    //echo "ok<br/>";

                    //prima verifico che non ci sia già una attività con quella data e quell'id_attivita_primaria
                    //ovvero una attività generata da non rigenerare

                    $query1="SELECT * FROM pcs_attivita_clean where id_attivita_primaria=? AND data_consigliata_da_programmazione=?";
                    $stmt1=$dbh->prepare($query1);
                    $stmt1->execute(array($idele,$datasuccessiva));
                    if($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
                        continue;
                    }

                    //poi dovrei verificare che non ci siano "doppioni" tra attivita primaria e attivita generate
                    //ovvero stessa data e stessa ora

                    $query2="SELECT * FROM pcs_attivita_clean where id=? AND data_consigliata=?";
                    $stmt2=$dbh->prepare($query2);
                    $stmt2->execute(array($idele,$datasuccessiva));
                    if($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        continue;
                    }

                    $query = "INSERT INTO pcs_attivita_clean (operatore1, operatore2, operatore3, operatore4, id_sede, ordine, data_consigliata, data_consigliata_da_programmazione,ora_consigliata_inizio, ora_consigliata_fine,  stato,descrizione_attivita,id_attivita_primaria) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt = $dbh->prepare($query);
                    if ($stmt->execute(array(
                        $el['operatore1'],
                        $el['operatore2'],
                        $el['operatore3'],
                        $el['operatore4'],
                        $el['id_sede'],
                        1,
                        $datasuccessiva,
                        $datasuccessiva,
                        $el['ora_consigliata_inizio'],
                        $el['ora_consigliata_fine'],
                        'da_fare',
                        $el['descrizione_attivita'],
                        $idele
                    ))) {

                          $idattivitagenerata=$dbh->lastInsertId();

                          //genero anche i servizi
                          $queryinsert="REPLACE INTO pcs_attivita_servizi_effettuati (id,id_attivita,id_servizio,ordine) SELECT CONCAT(?,'|',pcs_servizi.id),?,pcs_servizi.id,1 FROM pcs_servizi where pcs_servizi.id_sede= (SELECT pcs_attivita_clean.id_sede FROM pcs_attivita_clean WHERE pcs_attivita_clean.id=?)";
                          $stmtinsert=$dbh->prepare($queryinsert);
                          $stmtinsert->execute(array($idattivitagenerata,$idattivitagenerata,$idattivitagenerata));
                          //messo anche nel post process insert delle attivita_clean!!!



                    } else {
                        $errore[]=$idele." --- ".$datasuccessiva;
                    }


                    //echo "<br/>--------<br/>";
                    //echo $datasuccessiva;
                    //echo "<br/>--------<br/>";


                endforeach;

            }

            if (count($errore)>0) {
                $errori[]="Errore attività $idele";
                //echo "problemi";
                //echo "<pre>";
                //print_r($errore);
                //echo "</pre>";
                $dbh->rollBack();
            } else {
                $dbh->commit();
            }

        } else {
            continue; //vado al prossimo, questa attività non esiste!!!
        }

    endforeach;


        if (count($errori)>0) {
            $ret['result']=false;
            $ret['error']=$errori;
            echo json_encode($ret);
            exit;
        } else {

            //qui va fatto garbage collection, ovvero dobbiamo ripulire la tabella dei servizi che non hanno più la attività perché rimossa dalla rigenerazione
            // DA FARE DA FARE
            $querygarbage="DELETE FROM pcs_attivita_servizi_effettuati WHERE id_attivita NOT IN (SELECT id FROM pcs_attivita_clean)";
            $stmtgarbage=$dbh->query($querygarbage);

            $ret['result']=true;
            $ret['msg']="Tutte le attivita' sono state generate con successo";
            echo json_encode($ret);
            exit;
        }
}

function getDateForSpecificDayBetweenDates($startDate,$endDate,$day_number){
    $endDate = strtotime($endDate);
    $days=array('1'=>'Monday','2' => 'Tuesday','3' => 'Wednesday','4'=>'Thursday','5' =>'Friday','6' => 'Saturday','7'=>'Sunday');
    for($i = strtotime($days[$day_number], strtotime($startDate)); $i <= $endDate; $i = strtotime('+1 week', $i))
        $date_array[]=date('Y-m-d',$i);
    return $date_array;
}
?>
