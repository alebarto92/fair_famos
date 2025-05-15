<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$oggi=date("Y-m-d");

$querydel="DELETE FROM pcs_attivita_clean WHERE id_attivita_primaria is not null AND data_consigliata<='$oggi' AND non_rigenerare='no' AND stato<>'conclusa'";

if ($stmt=$dbh->query($querydel)) {
    $servizi=Array();
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $servizi=$row;
    }
    $ret['result']=true;
    $ret['cliente']=$servizi;
    echo json_encode($ret);
} else {
    $ret['result']=false;
    $ret['error']="Errore accesso al db";
    echo json_encode($ret);
}


?>
