<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");
 
$idcliente=$_REQUEST['idcliente'];
$nomesede=$_REQUEST['nomesede'];

if (!($idcliente>0 and $nomesede!='')) {
    $ret['result']=false;
    echo json_encode($ret);
	exit();
}

$query="INSERT INTO pcs_sedi_clienti (ordine,id_cliente,sede,indirizzo,CAP,citta,provincia,persona_di_riferimento,telefono,email) SELECT '0','".$idcliente."','".$nomesede."',Indirizzo, CAP, Citta, Provincia, Referenti, Cell, Mail from `pcs_clienti` WHERE pcs_clienti.id=$idcliente";
$stmt=$dbh->query($query);

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