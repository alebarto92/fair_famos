<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");
 
$codice_postazione=$_POST['codice_postazione'];
$lat=$_POST['lat'];
$lng=$_POST['lng'];

if ($codice_postazione!='' and $lat!='' and $lng!='') {
    $query="UPDATE pcs_postazioni SET latitudine_p=?, longitudine_p=? WHERE codice_postazione=?";
    $stmt=$dbh->prepare($query);
    if ($stmt->execute(array($lat,$lng,$codice_postazione))) {
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        $ret['result']=true;
        echo json_encode($ret);
    } else {
        $ret['result']=false;
        $ret['error']="Errore modifica posizione postazione";
        echo json_encode($ret);
    }
} else {
    $ret['result']=false;
    $ret['error']="Parametri non validi";
    echo json_encode($ret);
}
    

?>