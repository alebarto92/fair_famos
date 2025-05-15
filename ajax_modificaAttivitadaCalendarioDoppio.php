<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idattivita=$_POST['id_attivita'];
$operatoresource=$_POST['operatore_source'];
$operatoredest=$_POST['operatore_dest'];
list($data_consigliata,$tmp)=explode("T",$_POST['nuovadata']);

if ($idattivita>0) {

    $query1="SELECT * FROM pcs_attivita_clean WHERE id=? ";
    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($idattivita));
    if ($ATTIVITA=$stmt1->fetch(PDO::FETCH_ASSOC)) {
      $query="UPDATE pcs_attivita_clean SET operatore1=?,operatore2=?,operatore3=?,operatore4=?,data_consigliata=?,non_rigenerare='si' WHERE id=?";
      $stmt=$dbh->prepare($query);
      if ($stmt->execute(array(
          '0'=>$ATTIVITA['operatore1']==$operatoresource ? $operatoredest : $ATTIVITA['operatore1'],
          '1'=>$ATTIVITA['operatore2']==$operatoresource ? $operatoredest : $ATTIVITA['operatore2'],
          '2'=>$ATTIVITA['operatore3']==$operatoresource ? $operatoredest : $ATTIVITA['operatore3'],
          '3'=>$ATTIVITA['operatore4']==$operatoresource ? $operatoredest : $ATTIVITA['operatore4'],
          $data_consigliata,
          $idattivita))) {
          $ret['result']=true;
          echo json_encode($ret);
          exit;
      } else {
          $ret['result']=false;
          $ret['error']="Errore modifica attivita";
          echo json_encode($ret);
          exit;
      }

    } else {
      $ret['result']=false;
      $ret['error']="Attività non esiste!";
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
