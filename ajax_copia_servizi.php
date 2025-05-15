<?php
/**
 * Created by PhpStorm.
 * User: fabio
 * Date: 02/11/2020
 * Time: 09:56
 */
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idsedepartenza=$_REQUEST['sedepartenza'];
$idsedearrivo=$_REQUEST['sedearrivo'];

$modulo=getModulo($idmod);

if (!($idsedepartenza>0 and $idsedearrivo>0)) {
    $ret['result']=false;
    echo json_encode($ret);
    exit();
}

$query="INSERT INTO `pcs_servizi` (`id_sede`, `nome_servizio`, `periodicita`, `dalla_data`, `alla_data`, `descrizione_servizio`) SELECT ?,  `nome_servizio`, `periodicita`, `dalla_data`, `alla_data`, `descrizione_servizio` FROM pcs_servizi WHERE id_sede=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($idsedearrivo,$idsedepartenza));

	if ($stmt) {
        $ret['result']=true;
        echo json_encode($ret);
        exit();
    } else {
        $ret['result']=true;
        echo json_encode($ret);
        exit();
    }
?>