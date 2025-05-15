<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idele=$_REQUEST['idele'];
$idmod=$_REQUEST['idmod'];

$modulo=getModulo($idmod);

if (!($idmod>0 and $idele!='')) {
    setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento_new.php",$modulo['nome_modulo']." e idmod=".$idmod." e idele=".$idele);
    $ret['result']=false;
    echo json_encode($ret);
    exit();
}

$permessi=permessi($idmod,$utente['id_ruolo']);

if (!($permessi['Can_delete']=='si')) {
    setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento_new.php",$modulo['nome_modulo']." niente permessi");
    $ret['result']=false;
    echo json_encode($ret);
    exit();
}

//veririchiamo anche il pre process delete
$pre_process_delete=json_decode($modulo['pre_process_delete'],true);

setNotificheCRUD("admWeb","INFO","ajax_delete_elemento_new.php - pre process delete:",$modulo['pre_process_delete']);

if (count($pre_process_delete)>0) {
    foreach ($pre_process_delete as $ppuquery) {
        $ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
        //echo "<pre>";
        //echo $ppuquery;
        //echo "</pre>";
        if (!($dbh->query($ppuquery))) {
            setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento_new.php - pre process delete:",$ppuquery);
            $erroreTransazione=true;
        } else {
            setNotificheCRUD("admWeb","SUCCESS","ajax_delete_elemento_new.php - pre process delete:",$ppuquery);
        }
    }
}

$elemento=getElemento($idmod,$idele);

$query="DELETE FROM ".$modulo['nome_tabella']." WHERE ".$modulo['chiaveprimaria']."=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($idele));

//se sono in servizi oppure in attivita devo ricalcolare il punteggio del cantiere
if ($idmod==69 or $idmod==14) { //SERVIZI o ATTIVITA
    setNotificheCRUD("admWeb","INFO","ajax_delete_elemento_new.php - prima di lanciare aggiornamento punteggio $idmod $idele",json_encode($elemento));
    $queryupdate=calcolaPunteggioCantiere($elemento['id_sede']);
    //setNotificheCRUD("admWeb","INFO","ajax_modifica_elemento.php - punteggio",$queryupdate);

    if ($stmt=$dbh->query($queryupdate)) {
        setNotificheCRUD("admWeb","SUCCESS","ajax_delete_elemento_new.php - punteggio dopo delete $idmod",$queryupdate);

    } else {
        setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento_new.php - punteggio dopo delete $idmod",$queryupdate);
    }
}

$query2="DELETE FROM ".$GLOBAL_tb['testi']." WHERE id_ext='$idele' AND table_ext='".$modulo['nome_tabella']."'";
$stmt2=$dbh->query($query2);

$query3="DELETE FROM ".$GLOBAL_tb['files']." WHERE id_elem='$idele' AND tb='".$modulo['nome_tabella']."'";
$stmt3=$dbh->query($query3);

$query4="DELETE FROM ".$GLOBAL_tb['note']." WHERE id_ext='$idele' AND table_ext='".$modulo['nome_tabella']."'";
$stmt4=$dbh->query($query4);

//veririchiamo anche il pre process delete
$post_process_delete=json_decode($modulo['post_process_delete'],true);
setNotificheCRUD("admWeb","INFO","ajax_delete_elemento_new.php - pre process delete:",$modulo['post_process_delete']);

if (count($post_process_delete)>0) {
    foreach ($post_process_delete as $ppuquery) {
        $ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
        if (!($dbh->query($ppuquery))) {
            setNotificheCRUD("admWeb","ERROR"," - post process delete:",$ppuquery);
            $erroreTransazione=true;
        } else {
            setNotificheCRUD("admWeb","SUCCESS","ajax_delete_elemento_new.php - post process delete:",$ppuquery);
        }
    }
}

//veririchiamo anche il post process update
$post_process_update=json_decode($modulo['post_process_update'],true);
setNotificheCRUD("admWeb","INFO","ajax_delete_elemento_new.php - post process update:",$modulo['post_process_update']);

if (count($post_process_update)>0) {
    foreach ($post_process_update as $ppuquery) {
        $ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
        if (!($dbh->query($ppuquery))) {
            setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento_new.php - post process update:",$ppuquery);
            $erroreTransazione=true;
        } else {
            setNotificheCRUD("admWeb","SUCCESS","ajax_delete_elemento_new.php - post process update:",$ppuquery);
        }
    }
}
	if ($stmt) {
        $ret['result']=true;
        echo json_encode($ret);
        exit();
	} else {
        $ret['result']=false;
        $ret['query']=$query;
        echo json_encode($ret);
        exit();
	}
?>
