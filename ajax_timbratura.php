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

if ($_REQUEST['delete']==1 && $_REQUEST['idtimbratura']!='') {
  $query="SELECT * FROM pcs_timbrature WHERE pcs_timbrature.id=?";
  $stmt=$dbh->prepare($query);
  $stmt->execute(array($_REQUEST['idtimbratura']));
  if ($TIMBRATURA=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $queryattivita="SELECT * FROM pcs_attivita_clean WHERE id=?";
    $stmtattivita=$dbh->prepare($queryattivita);
    $stmtattivita->execute(array($TIMBRATURA['id_attivita']));
    $ATTIVITA=$stmtattivita->fetch(PDO::FETCH_ASSOC);

    $idattivita=$TIMBRATURA['id_attivita'];
    $idoperatore=$TIMBRATURA['id_operatore'];

    if ($ATTIVITA['operatore1']==$idoperatore) {
      $queryupdate="UPDATE pcs_attivita_clean set fine1=NULL WHERE id=? ";
      $stmtupdate1=$dbh->prepare($queryupdate);
      $stmtupdate1->execute(array($idattivita));
    }
    if ($ATTIVITA['operatore2']==$idoperatore) {
      $queryupdate="UPDATE pcs_attivita_clean set fine2=NULL WHERE id=? ";
      $stmtupdate2=$dbh->prepare($queryupdate);
      $stmtupdate2->execute(array($idattivita));
    }
    if ($ATTIVITA['operatore3']==$idoperatore) {
      $queryupdate="UPDATE pcs_attivita_clean set fine3=NULL WHERE id=? ";
      $stmtupdate3=$dbh->prepare($queryupdate);
      $stmtupdate3->execute(array($idattivita));
    }
    if ($ATTIVITA['operatore4']==$idoperatore) {
      $queryupdate="UPDATE pcs_attivita_clean set fine4=NULL WHERE id=? ";
      $stmtupdate4=$dbh->prepare($queryupdate);
      $stmtupdate4->execute(array($idattivita));
    }
    $querydel="DELETE FROM pcs_timbrature WHERE id=?";
    $stmtdel=$dbh->prepare($querydel);
    if ($stmtdel->execute(array($_REQUEST['idtimbratura']))) {
      $ret['result']=true;
      echo json_encode($ret);
      exit();
    } else {
      $ret['result']=false;
      echo json_encode($ret);
      exit();
    }
  }

} else {
  $idattivita=$_REQUEST['idattivita'];
  $idoperatore=$_REQUEST['idoperatore'];
  $timbratura=$_REQUEST['timbratura'];
  $tipo="fine";

  if ($idattivita>0 && $idoperatore>0 && $timbratura!='') {

  } else {
      $ret['result']=false;
      echo json_encode($ret);
      exit();
  }

  $queryattivita="SELECT * FROM pcs_attivita_clean WHERE id=?";
  $stmtattivita=$dbh->prepare($queryattivita);
  $stmtattivita->execute(array($idattivita));
  $ATTIVITA=$stmtattivita->fetch(PDO::FETCH_ASSOC);

  if ($ATTIVITA['operatore1']==$idoperatore) {
    $queryupdate="UPDATE pcs_attivita_clean set fine1=? WHERE id=? ";
    $stmtupdate1=$dbh->prepare($queryupdate);
    $stmtupdate1->execute(array($timbratura,$idattivita));
  }
  if ($ATTIVITA['operatore2']==$idoperatore) {
    $queryupdate="UPDATE pcs_attivita_clean set fine2=? WHERE id=? ";
    $stmtupdate2=$dbh->prepare($queryupdate);
    $stmtupdate2->execute(array($timbratura,$idattivita));
  }
  if ($ATTIVITA['operatore3']==$idoperatore) {
    $queryupdate="UPDATE pcs_attivita_clean set fine3=? WHERE id=? ";
    $stmtupdate3=$dbh->prepare($queryupdate);
    $stmtupdate3->execute(array($timbratura,$idattivita));
  }
  if ($ATTIVITA['operatore4']==$idoperatore) {
    $queryupdate="UPDATE pcs_attivita_clean set fine4=? WHERE id=? ";
    $stmtupdate4=$dbh->prepare($queryupdate);
    $stmtupdate4->execute(array($timbratura,$idattivita));
  }

  $id=$idattivita."|".$idoperatore."|".$tipo;
  $query2="REPLACE INTO pcs_timbrature (id, id_attivita, id_operatore, tipo, timbratura) VALUES (?,?,?,?,?)";
  $stmt2=$dbh->prepare($query2);
  if ($stmt2->execute(array($id,$idattivita,$idoperatore,$tipo,$timbratura))) {
          $ret['result']=true;
          echo json_encode($ret);
          exit();
      } else {
          $ret['result']=false;
          echo json_encode($ret);
          exit();
      }
}

?>
