<?php
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

$query="INSERT INTO `pcs_aree` (`id_sede`, `Servizio`, `Area`, `IE`, `SogliaEsca`, `Infestante1`, `Soglia1`, `Infestante2`, `Soglia2`, `Infestante3`, `Soglia3`, `Infestante4`, `Soglia4`, `SogliaTotalePerPostazione`, `SogliaTotalePerArea`) SELECT ?,  `Servizio`, `Area`, `IE`, `SogliaEsca`, `Infestante1`, `Soglia1`, `Infestante2`, `Soglia2`, `Infestante3`, `Soglia3`, `Infestante4`, `Soglia4`, `SogliaTotalePerPostazione`, `SogliaTotalePerArea` FROM pcs_aree WHERE id_sede=?";
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