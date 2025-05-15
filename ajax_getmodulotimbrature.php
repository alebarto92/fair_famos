<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idattivita=$_REQUEST['idattivita'];
$idoperatore=$_REQUEST['idoperatore'];

//query per la lettura dei campi della polizza
$query="SELECT CAST(CONCAT('codiceCliente ',pcs_clienti.codiceCliente,' ',pcs_clienti.nome,' ', pcs_clienti.cognome) AS CHAR) as nomecliente,
pcs_sedi_clienti.sede,pcs_attivita_clean.*,DATE_FORMAT(data_consigliata,'%d-%m-%Y') as data_consigliata_formatted, TIME_FORMAT(ora_consigliata_inizio,'%H:%i') as ora_consigliata_formatted
 FROM pcs_attivita_clean
 JOIN pcs_sedi_clienti ON pcs_sedi_clienti.id=pcs_attivita_clean.id_sede JOIN pcs_clienti ON pcs_clienti.id=pcs_sedi_clienti.id_cliente WHERE pcs_attivita_clean.id=$idattivita ";
//echo $query;
$stmt=$dbh->query($query);
if ($ATTIVITA=$stmt->fetch(PDO::FETCH_ASSOC)) {

  $operatori=array();
  $queryoperatori="SELECT pcs_users.* FROM pcs_users WHERE id_ruolo=3 order by Cognome,Nome";
  $stmtoperatori=$dbh->query($queryoperatori);
  while ($rowoperatori=$stmtoperatori->fetch(PDO::FETCH_ASSOC)) :
      $operatori[$rowoperatori['id_user']]=$rowoperatori;
  endwhile;

  $operatorisquadra=[];
  $operatorisquadra[]=$ATTIVITA['operatore1'];
  if ($ATTIVITA['operatore2']!='') {
    $operatorisquadra[]=$ATTIVITA['operatore2'];
  }
  if ($ATTIVITA['operatore3']!='') {
    $operatorisquadra[]=$ATTIVITA['operatore3'];
  }
  if ($ATTIVITA['operatore4']!='') {
    $operatorisquadra[]=$ATTIVITA['operatore4'];
  }
  $ATTIVITA['operatori']=$operatorisquadra;

  $querytimbrature="SELECT *,DATE_FORMAT(timbratura,'%Y-%m-%d') as timbraturadata,TIME_FORMAT(timbratura,'%H:%i') as timbraturaora FROM pcs_timbrature WHERE id_attivita=$idattivita";
  $stmttimbrature=$dbh->query($querytimbrature);
  while ($rowtimbrature=$stmttimbrature->fetch(PDO::FETCH_ASSOC)) {
    $timbrature[$rowtimbrature['id_operatore']]=$rowtimbrature;
  }


?>
    <form id="timbratureform">
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
                    <h4>Riepilogo Attività</h4>
                    <b>CLIENTE: </b><?php echo convertDate($ATTIVITA['nomecliente']);?><br/>
                    <b>CANTIERE: </b><?php echo convertDate($ATTIVITA['sede']);?><br/>
                    <b>DATA e ORA: </b><?php echo convertDate($ATTIVITA['data_consigliata']);?> dalle <?php echo ($ATTIVITA['ora_consigliata_inizio']);?> alle <?php echo ($ATTIVITA['ora_consigliata_fine']);?><br/>
                    <b>OPERATORI: </b>
                    <?php foreach ($ATTIVITA['operatori'] as $op) {
                      echo "<i>";
                      echo $operatori[$op]['Cognome']; echo " ";
                      echo $operatori[$op]['Nome']; echo " ";
                      echo "</i>";
                    } ?>
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-12 col-sm-7">
                <div class="form-group">
                  <h4>Timbrature</h4>
                  <?php for ($i=0;$i<count($ATTIVITA['operatori']);$i++) {
                    $idop=$ATTIVITA['operatori'][$i]?>

                    <div class="row">
                      <div class="col col-xs-12">
                        <?php echo $operatori[$idop]['Cognome'];?>
                        <?php echo $operatori[$idop]['Nome'];?><br/>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col col-xs-12 col-sm-7">
                        <input class="timbratura" data-idattivita="<?php echo $idattivita;?>" data-idoperatore="<?php echo $idop;?>" data-tipo="timbraturadata" type="date" value="<?php echo $timbrature[$idop]['timbraturadata'];?>"/>
                      </div>
                      <div class="col col-xs-12 col-sm-3">
                        <input class="timbratura" data-idattivita="<?php echo $idattivita;?>" data-idoperatore="<?php echo $idop;?>" data-tipo="timbraturaora" type="time" value="<?php echo $timbrature[$idop]['timbraturaora'];?>"/>
                      </div>
                      <div class="col col-xs-12 col-sm-2">
                        <a class="eliminatimbratura btn btn-danger" idtimbratura="<?php echo $timbrature[$idop]['id'];?>"><i class="fa fa-trash"></i></a>
                      </div>
                    </div>
                    <br/><br/>
                  <?php } ?>
                  <a class="btn btn-primary" id="aggiornatimbrature">AGGIORNA TIMBRATURE</a><br/><br/>
                  <input type="reset" class="btn btn-warning" value="ANNULLA"/>
                </div>
            </div>
        </div>
    </form>
<?php } else {
    echo "ERRORE! ATTIVITA NON PRESENTE!";
    exit;
}


exit;
