<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");
 
$idCliente=$_POST['idCliente'];

if ($idCliente!='') {
    $query="SELECT * FROM pcs_clienti WHERE id='".$idCliente."' LIMIT 0,1";
    if ($stmt=$dbh->query($query)) {
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
} else {
    $ret['result']=false;
    $ret['error']="Parametri non validi";
    echo json_encode($ret);
}
    

?>