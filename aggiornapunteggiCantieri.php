<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$query="SELECT * FROM pcs_sedi_clienti";
$stmt=$dbh->query($query);
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cantieri[] = $row;
}
foreach ($cantieri as $c) {
    $row['id']=$c['id'];
    //echo "<br/>sto facendo ".$c['id'];
    $queryupdate=calcolaPunteggioCantiere($row['id']);
    //setNotificheCRUD("admWeb","INFO","ajax_modifica_elemento.php - punteggio",$queryupdate);
    if ($stmt=$dbh->query($queryupdate)) {
        setNotificheCRUD("admWeb","SUCCESS","aggiornapunteggiCantieri.php ".$row['id'],$queryupdate);
        //echo "<br>Aggiornato punteggio per cantiere ".$row['id'];

    } else {
        setNotificheCRUD("admWeb","ERROR","aggiornapunteggiCantieri.php ".$row['id'],$queryupdate);
        //echo "<br>ERRORE punteggio per cantiere ".$row['id'];
    }
}
$ret['result']=true;
$ret['msg']="Punteggi dei cantieri aggiornati";
echo json_encode($ret);
exit;
?>