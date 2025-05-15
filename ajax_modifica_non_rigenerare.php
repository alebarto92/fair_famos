<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");
 
$idattivita=intval($_REQUEST['idattivita']);
$valore=$_REQUEST['val'];

if ($idattivita>0 && $valore!='') {
    $query="UPDATE pcs_attivita_clean SET non_rigenerare=? WHERE id=?";
    $stmt = $dbh->prepare($query);
    if ($stmt->execute(array($valore,$idattivita))) {
        $ret['result']=true;

        echo json_encode($ret);
        exit;
    } else {
        $ret['result']=false;
        $ret['query']=$query;
        $ret['error']="Errore db!";
        echo json_encode($ret);
        exit;
    }
} else {
    $ret['result']=false;
    $ret['error']="Parametri errati o mancanti";
    echo json_encode($ret);
    exit;
}

