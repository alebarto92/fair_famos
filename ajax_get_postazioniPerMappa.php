<?php
include("config.php");

$idsede=$_REQUEST['idsede'];
if ($_SESSION['pcs_id_user']>0 and $idsede!='') {

} else {
    $ret['result']=false;
    $ret['error']="Parametri mancanti!";
    $ret['params']['user']=$_SESSION['pcs_id_user'];
    $ret['params']['idsede']=$idsede;
    echo json_encode($ret);
    exit;
}

$query="SELECT pcs_postazioni.*,pcs_aree.*,pcs_tipi_servizio.*,pcs_tipo_postazione.tipo,pcs_modello_postazione.modello,pcs_prodotto_postazione.prodotto FROM pcs_postazioni JOIN pcs_aree ON pcs_postazioni.id_area=pcs_aree.id JOIN pcs_tipi_servizio ON pcs_tipi_servizio.id=pcs_aree.Servizio LEFT JOIN pcs_tipo_postazione ON pcs_tipo_postazione.id_tipo_postazione=pcs_postazioni.Tipo LEFT JOIN pcs_modello_postazione ON pcs_modello_postazione.id_modello_postazione=pcs_postazioni.Modello LEFT JOIN pcs_prodotto_postazione ON pcs_prodotto_postazione.id_prodotto_postazione=pcs_postazioni.Prodotto WHERE pcs_aree.id_sede=$idsede";
$stmt=$dbh->query($query);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $postazioni[]=$row;
}
	if ($stmt) {
        $ret['result']=true;
        $ret['postazioni']=$postazioni;
        echo json_encode($ret);
        exit;
    } else {
        $ret['result']=false;
        $ret['error']="Problema ricerca postazioni!";
        echo json_encode($ret);
        exit;
	}
?>