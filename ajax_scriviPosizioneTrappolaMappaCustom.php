<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$codice_postazione=$_POST['codice_postazione'];
$x=$_POST['x'];
$y=$_POST['y'];
$xmax=$_POST['xmax'];
$ymax=$_POST['ymax'];
$indicemappa=$_POST['indicemappa'];

$posizione['x']=$x;
$posizione['y']=$y;
$posizione['xmax']=$xmax;
$posizione['ymax']=$ymax;
$posizione['indicemappa']=$indicemappa;

if ($codice_postazione!='') {

    if (($posizione['x']==-1) && ($posizione['y']==-1)) {
        $query="UPDATE pcs_postazioni SET posizione_mappa_custom=NULL WHERE codice_postazione=?";
        $stmt=$dbh->prepare($query);
        if ($stmt->execute(array($codice_postazione))) {
            $ret['result']=true;
            echo json_encode($ret);
        } else {
            $ret['result']=false;
            $ret['error']="Query non corretta!";
            echo json_encode($ret);
        }
    } else {
        $query="UPDATE pcs_postazioni SET posizione_mappa_custom=? WHERE codice_postazione=?";
        $stmt=$dbh->prepare($query);
        if ($stmt->execute(array(json_encode($posizione),$codice_postazione))) {
            $ret['result']=true;
            echo json_encode($ret);
        } else {
            $ret['result']=false;
            $ret['error']="Query non corretta!";
            echo json_encode($ret);
        }
    }




} else {
    $ret['result']=false;
    $ret['error']="Parametri non validi";
    echo json_encode($ret);
}
    

?>