<div class="container" id="firstcontainer">
    <div id='loading'>loading...</div>
<?php


  //prendo tutte le settimane da quella attuale fino alla fine delle attività

  $primogiorno=$_GET['primogiorno'];
  $ultimogiorno=$_GET['ultimogiorno'];

  if ($primogiorno=='') {
    $primogiorno=date("Y-m-d");
  }
  if ($ultimogiorno=='') {
    $start = new DateTime($primogiorno, new DateTimeZone("UTC"));
    $month_later = clone $start;
    $month_later->add(new DateInterval("P2M"));
    $ultimogiorno=$month_later->format("Y-m-d");
  }


    $operatori=array();
    $elencosettimane=array();
    $queryoperatori="SELECT pcs_users.* FROM pcs_users WHERE id_ruolo=3 order by Cognome,Nome";
    $stmtoperatori=$dbh->query($queryoperatori);
    while ($rowoperatori=$stmtoperatori->fetch(PDO::FETCH_ASSOC)) :
        $idop=$rowoperatori['id_user'];

        $query="SELECT *,TIMESTAMPDIFF(MINUTE,ora_consigliata_inizio,ora_consigliata_fine) as minutilavorati FROM pcs_attivita_clean LEFT JOIN pcs_sedi_clienti ON pcs_attivita_clean.id_sede=pcs_sedi_clienti.id WHERE ((operatore1=$idop) OR (operatore2=$idop) OR (operatore3=$idop) OR (operatore4=$idop) ) AND stato='da_fare' AND (data_consigliata BETWEEN '$primogiorno' AND '$ultimogiorno')";
        $stmt=$dbh->query($query);

        $minutitotali=0;
        while ($rowattivita=$stmt->fetch(PDO::FETCH_ASSOC)) :

            $moltiplicatore=0;
            if ($rowattivita['operatore1']==$idop) {
                $moltiplicatore++;
            }
            if ($rowattivita['operatore2']==$idop) {
                $moltiplicatore++;
            }
            if ($rowattivita['operatore3']==$idop) {
                $moltiplicatore++;
            }
            if ($rowattivita['operatore4']==$idop) {
                $moltiplicatore++;
            }

            $date = new DateTime($rowattivita['data_consigliata']);
            $week = $date->format("W");
            $year = $date->format("Y");

            $w=$year.$week;

            $elencosettimane[$w]=1;

            $tmp=$rowattivita['minutilavorati']*$moltiplicatore;
            $minutitotali+=$rowattivita['minutilavorati']*$moltiplicatore;
            $rowoperatori['attivita'][$w]+=$rowattivita['minutilavorati']*$moltiplicatore;
            $rowattivita['minutilavorati']=$tmp;
            $rowoperatori['elenco_attivita'][$rowattivita['id']]=$rowattivita;

        endwhile;
        $rowoperatori['minutitotali']=$minutitotali;
        $operatori[]=$rowoperatori;
    endwhile;
    //echo "<pre>";
    //print_r($operatori);
    //echo "</pre>";

    ksort($elencosettimane);

?>
    <h3>Ore Potenziali</h3>

    <div class="row">
        <div class="col-xs-12">
            <table id="dynamic-table" class="RiepilogoOre table table-striped table-bordered bootstrap-datatable datatable responsive">
                <thead>
                <tr>
                    <th>Op:</th>
                    <?php foreach (array_keys($elencosettimane) as $w): ?>
                        <th><?php echo firstDayOfWeek(substr($w,0,4),substr($w,4,2));?><br/><?php echo lastDayOfWeek(substr($w,0,4),substr($w,4,2));?></th>
                    <?php endforeach;?>
                    <!--<th>TOT</th>-->
                </tr>
                </thead>
                <tbody>
                <?php foreach ($operatori as $op) :
                    if ($op['minutitotali']==0) continue;
                    ?>
                    <tr>
                        <td><?php echo $op['Cognome'];?> <?php echo $op['Nome'];?> </td>
                        <?php foreach (array_keys($elencosettimane) as $w): ?>
                            <td><a target="_blank" href="Calendario.php?1=1&idoperatore=<?php echo $op['id_user'];?>&viewweek=<?php echo validateDate(firstDayOfWeek(substr($w,0,4),substr($w,4,2)));?>">
                              <?php echo convertToHoursMins($op['attivita'][$w],'%02d:%02d');?>
                            </a></td>
                        <?php endforeach;?>
                            <!--<td><b><?php echo convertToHoursMins($op['minutitotali'],'%02d:%02d');?></b></td>-->
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <hr/>
<?php
function firstDayOfWeek($year,$week) {
  $date = new DateTime();
  $date->setISODate($year,$week);
  return $date->format('d/m/Y');
}
function lastDayOfWeek($year,$week) {
  $date = new DateTime();
  $date->setISODate($year,$week);
  return date('d/m/Y', strtotime($date->format('Y-m-d') .' +6 day'));
}
?>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->
