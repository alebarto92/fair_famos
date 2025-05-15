<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idcontratto=$_POST['idcontratto'];

$totgiorni['ogni 7 giorni']="7 days";
$totgiorni['ogni 15 giorni']="15 days";
$totgiorni['mensile']="1 month";
$totgiorni['ogni 45 giorni']="45 days";
$totgiorni['bimestrale']="2 months";
$totgiorni['trimestrale']="3 months";
$totgiorni['quadrimestrale']="4 months";
$totgiorni['semestrale']="6 months";
$totgiorni['annuale']="1 year";


if ($idcontratto>0) {

    $query="SELECT pcs_contratti.*,pcs_sedi_clienti.operatoreassegnato1,pcs_sedi_clienti.operatoreassegnato2,pcs_sedi_clienti.operatoreassegnato3 FROM pcs_contratti JOIN pcs_sedi_clienti ON pcs_contratti.id_sede=pcs_sedi_clienti.id JOIN pcs_clienti ON pcs_clienti.id=pcs_sedi_clienti.id_cliente WHERE pcs_contratti.id=?";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($idcontratto));
    $ret['query']=$query;


    if ($CONTRATTO=$stmt->fetch(PDO::FETCH_ASSOC)) {

        //a questo punto devo vedere la differenza di tempo tra data scadenza e data inizio contratto, dividerlo per il numero di initerventi
        //e generare gli interventi in base a questo incremento di giorni

        if (intval($CONTRATTO['interventi_da_fare'])<1) {
            $ret['result']=false;
            $ret['error']="Controllare numero di interventi da fare!";
            $ret['post']=$_POST;
            echo json_encode($ret);
            exit;
        }


        //inizio la transazione mysql
        $dbh->beginTransaction();

        $errors=array();

        $iddipendente=$CONTRATTO['operatoreassegnato1'];
        $secondo=$CONTRATTO['operatoreassegnato2'];
        $terzo=$CONTRATTO['operatoreassegnato3'];
        $oraconsigliata="09:00:00";

        //installazione
        $queryatt="INSERT into pcs_attivita_clean (id_dipendente,secondo_operatore,terzo_operatore,id_sede,tipo,data_consigliata,ora_consigliata,stato) VALUES (?,?,?,?,?,?,?,?)";
        $stmtatt=$dbh->prepare($queryatt);
        $datainstallazione=$CONTRATTO['data_installazione'];
        $tipo="Installazione";
        $arrayquery=array($iddipendente,$secondo,$terzo,$CONTRATTO['id_sede'],$tipo,$datainstallazione,$oraconsigliata,"da_fare");
        if ($stmtatt->execute($arrayquery)) {

        } else {
            $errors[]=$dbh->errorInfo();
            $arrayqueries[]=$arrayquery;
            $queries[]=$queryatt;
        }

        for ($i=1;$i<=$CONTRATTO['interventi_da_fare'];$i++) {
            //genero le attività a partire dalla data di prima visita
            $queryatt="INSERT into pcs_attivita_clean (id_dipendente,secondo_operatore,terzo_operatore,id_sede,tipo,data_consigliata,ora_consigliata,stato) VALUES (?,?,?,?,?,?,?,?)";
            $stmtatt=$dbh->prepare($queryatt);
            $tipo="Visita";

            if ($i==1) {
                $dataconsigliata=$CONTRATTO['data_prima_visita'];
            } else {
                $dataconsigliata=date('Y-m-d', strtotime($dataconsigliata. " + ".$totgiorni[$CONTRATTO['cadenza']]));
            }



            $arrayquery=array($iddipendente,$secondo,$terzo,$CONTRATTO['id_sede'],$tipo,$dataconsigliata,$oraconsigliata,"da_fare");

            if ($stmtatt->execute($arrayquery)) {

            } else {
                $errors[]=$dbh->errorInfo();
                $arrayqueries[]=$arrayquery;
                $queries[]=$queryatt;

            }
        }
        if (count($errors)==0) {
            //tutto bene
            $queryfinale="UPDATE pcs_contratti set attivita_generate='si' WHERE id=?";
            $stmt=$dbh->prepare($queryfinale);
            $stmt->execute(array($idcontratto));
            $dbh->commit();
            $ret['result']=true;
            echo json_encode($ret);
            exit;
        } else {
            $dbh->rollBack();
            $ret['result']=false;
            $ret['error']="Errori durante la creazione delle attivita: attivita non generate!";
            $ret['errors']=$errors;
            $ret['arrays']=$arrayqueries;
            $ret['queries']=$queries;
            echo json_encode($ret);
            exit;
        }

    } else {
        $ret['result']=false;
        $ret['error']="Parametri non validi";
        $ret['post']=$_POST;
        echo json_encode($ret);
        exit;
    }

} else {
    $ret['result']=false;
    $ret['error']="Parametri non validi";
    $ret['post']=$_POST;
    echo json_encode($ret);
    exit;
}


?>