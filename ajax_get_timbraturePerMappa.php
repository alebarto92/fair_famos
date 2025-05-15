<?php
include("config.php");

$idattivita=$_REQUEST['idattivita'];
if ($_SESSION['pcs_id_user']>0 and $idattivita!='') {

} else {
    $ret['result']=false;
    $ret['error']="Parametri mancanti!";
    $ret['params']['user']=$_SESSION['pcs_id_user'];
    $ret['params']['idattivita']=$idattivita;
    echo json_encode($ret);
    exit;
}

$query="SELECT pcs_timbrature.* FROM pcs_timbrature  WHERE pcs_timbrature.id_attivita=$idattivita";
$stmt=$dbh->query($query);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $timbrature[]=$row;
}
	if ($stmt) {
        $ret['result']=true;
        $ret['timbrature']=$timbrature;
        echo json_encode($ret);
        exit;
    } else {
        $ret['result']=false;
        $ret['error']="Problema ricerca timbrature!";
        echo json_encode($ret);
        exit;
	}
?>