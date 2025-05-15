<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idattivita=$_POST['id_attivita'];
$dataconsigliata=$_POST['newstartdata'];
$oraconsigliata=$_POST['newstartora'];
$oraconsigliatafine=$_POST['newendora'];

if ($_POST['newend']) {
    list ($tmp1,$tmp)=explode("T",$_POST['newend']);
    list ($oraconsigliatafine,$trash)=explode(".",$tmp);
}

if ($idattivita>0) {
    $query="UPDATE pcs_attivita_clean SET data_consigliata=?,ora_consigliata_inizio=?,ora_consigliata_fine=?,non_rigenerare='si' WHERE id=?";
    $stmt=$dbh->prepare($query);
    if ($stmt->execute(array(
        $dataconsigliata,
        $oraconsigliata,
        '2' => $oraconsigliatafine!='' ? $oraconsigliatafine : null,
        $idattivita))) {
        $ret['result']=true;
        echo json_encode($ret);
        exit;
    } else {
        $ret['result']=false;
        $ret['error']="Errore modifica attivita";
        $ret['query']=$query;
        $ret['array']=array(
            $dataconsigliata,
            $oraconsigliata,
            '2' => $oraconsigliatafine!='' ? $oraconsigliatafine : null,
            $idattivita);
        echo json_encode($ret);
        exit;
    }

} else {
    $ret['result']=false;
    $ret['error']="Parametri non validi";
    $ret['post']=$_POST;
    echo json_encode($ret);
    exit;
}


?>