<div class="container" id="firstcontainer">
    <div id='loading'>loading...</div>
<?php
    $mesetmp=$_GET['mese'];
    if ($mesetmp=='') {
        $mesetmp=date("Y-m");
    }
    list ($year,$month)=explode("-",$mesetmp);
    $totgiorni=cal_days_in_month(CAL_GREGORIAN, $month, $year);

$primogiorno=$mesetmp."-01";
$ultimogiorno=$mesetmp."-".$totgiorni;

    $operatori=array();
    $queryoperatori="SELECT pcs_users.* FROM pcs_users WHERE id_ruolo=3 order by Cognome,Nome";
    $stmtoperatori=$dbh->query($queryoperatori);
    while ($rowoperatori=$stmtoperatori->fetch(PDO::FETCH_ASSOC)) :
        $idop=$rowoperatori['id_user'];

        $query="SELECT *,DATE_FORMAT(data_fine_attivita,'%d') as giorno,TIMESTAMPDIFF(MINUTE,ora_consigliata_inizio,ora_consigliata_fine) as minutilavorati FROM pcs_attivita_clean LEFT JOIN pcs_sedi_clienti ON pcs_attivita_clean.id_sede=pcs_sedi_clienti.id WHERE ((operatore1=$idop AND fine1 is not NULL) OR (operatore2=$idop AND fine2 is not NULL) OR (operatore3=$idop AND fine3 is not NULL) OR (operatore4=$idop AND fine4 is not NULL) ) AND stato='conclusa' AND (data_consigliata BETWEEN '$primogiorno' AND '$ultimogiorno')";
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

            $tmp=$rowattivita['minutilavorati']*$moltiplicatore;
            $minutitotali+=$rowattivita['minutilavorati']*$moltiplicatore;
            $rowoperatori['attivita'][$rowattivita['giorno']]+=$rowattivita['minutilavorati']*$moltiplicatore;
            $rowattivita['minutilavorati']=$tmp;
            $rowoperatori['elenco_attivita'][$rowattivita['id']]=$rowattivita;

        endwhile;
        $rowoperatori['minutitotali']=$minutitotali;
        $operatori[]=$rowoperatori;
    endwhile;
    //echo "<pre>";
    //print_r($operatori);
    //echo "</pre>";
?>
    <h3>Riepilogo Ore</h3>
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-2" style="margin-top:10px;margin-bottom:20px;">
            Mese:
            <input class="datepicker" type="text" name="mese" id="mese" value="<?php echo $mesetmp;?>"/>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-2" style="margin-top:10px;margin-bottom:20px;">
            <input class="btn btn-block btn-warning" type="button" name="applicafiltri" id="applicafiltri" value="APPLICA FILTRO"/>
        </div>

    </div>
    <br/>

    <div class="row">
        <div class="col-xs-12">
            <table id="dynamic-table" class="RiepilogoOre table table-striped table-bordered bootstrap-datatable datatable">
                <thead>
                <tr>
                    <th>Op:</th>
                    <?php for ($i=0;$i<$totgiorni;$i++):
                            $giorno=sprintf("%02d",$i+1);
                        ?>
                        <th><?php echo $giorno;?></th>
                    <?php endfor;?>
                    <th>TOT</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $totoperatoridiversidazero=0;
                foreach ($operatori as $op) :
                    if ($op['minutitotali']==0) continue;
                    $totoperatoridiversidazero++;
                    ?>
                    <tr>
                        <td><?php echo $op['Cognome'];?> <?php echo $op['Nome'];?> </td>
                        <?php for ($i=0;$i<$totgiorni;$i++):
                        $giorno=sprintf("%02d",$i+1);
                        $giornoformatodata=$mesetmp."-".$giorno;
                        ?>
                            <td><a target="_blank" href="Calendario.php?1=1&idoperatore=<?php echo $op['id_user'];?>&viewday=<?php echo $giornoformatodata;?>"><?php echo convertToHoursMins($op['attivita'][$giorno],'%02d:%02d');?></a></td>
                        <?php endfor;?>
                            <td><b><?php echo convertToHoursMins($op['minutitotali'],'%02d:%02d');?></b></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php foreach ($operatori as $op) :
if ($op['minutitotali']==0) continue;
  ?>
    <div class="row dettaglioOperatore" id="dettaglioOre<?php echo $op['id_user'];?>">
        <div class="col-xs-12">
          <table class="RiepilogoOre table table-striped table-bordered">
            <tr>
              <th colspan="4"><?php echo $op['Cognome'];?> <?php echo $op['Nome'];?></th>
            </tr>
            <?php usort($op['elenco_attivita'], function($a, $b) {
              return $a['data_consigliata'] <=> $b['data_consigliata'];
            }); ?>

            <?php foreach ($op['elenco_attivita'] as $a) : ?>
              <tr>
                <td><?php echo $a['sede'];?></td>
                <td><?php echo $a['data_consigliata'];?> dalle <?php echo $a['ora_consigliata_inizio'];?> alle <?php echo $a['ora_consigliata_fine'];?></td>
                <td>FINE: <?php echo $a['data_fine_attivita'];?></td>
                <td>Lavorati: <?php echo $a['minutilavorati'];?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </div>
    </div>
<?php endforeach; ?>
    <script type="text/javascript">
        $(document).ready(function(){

          <?php if ($totoperatoridiversidazero>0) : ?>
            var table=$('.datatable')
            //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
                .DataTable( {
                    "bAutoWidth": false,
                    "bStateSave": true,
                    "ordering": false,
                    "aoColumns": [
                        <?php $nullcol[0]='null'; ?>
                        <?php for ($i=0;$i<=$totgiorni;$i++):
                        $nullcol[]='null';
                        ?>
                        <?php endfor; ?>
                        <?php echo join(",",$nullcol);?>
                    ],
                    "aaSorting": [],
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tutti"]],
                    "iDisplayLength": 50,
                    renderer: "bootstrap",
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Italian.json"
                    },
                    dom: 'Blfrtip',

                    buttons: [
                        {
                            extend: 'copyHtml5',
                            text: '<i class="fa fa-clipboard"></i> ',
                            title: 'RiepilogoOre',
                            exportOptions: {
                                columns: ':not(.no-print)'
                            },
                            footer: true,
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fa fa-file-excel-o"></i> ',
                            title: 'RiepilogoOre<?php echo $_GET['mese'];?>',
                            exportOptions: {
                                columns: ':not(.no-print)'
                            },
                            footer: true,
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="fa fa-table"></i> ',
                            title: 'RiepilogoOre',
                            exportOptions: {
                                columns: ':not(.no-print)'
                            },
                            footer: true,
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fa fa-file-pdf-o"></i> ',
                            title: 'RiepilogoOre',
                            exportOptions: {
                                columns: ':not(.no-print)'
                            },
                            footer: true,
                        },
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> ',
                            title: 'RiepilogoOre',
                            exportOptions: {
                                columns: ':not(.no-print)'
                            },
                            footer: true,
                        },
                    ],
                } );
          <?php endif; ?>

            $("#applicafiltri").hide();

/*            $("#applicafiltri").click(function(){

                var url="RiepilogoOre.php";
                var mese=$("#mese").val();

                pargiorno='?mese='+mese;

                url=url+pargiorno;

                $(location).attr('href',url);

            })
*/
            $('#mese').on('dp.change', function(e){
              //console.log(e.date);
              //console.log($(this).val());

              var oldmese='<?php echo $_GET['mese'];?>';

              var url="RiepilogoOre.php";
              var mese=$(this).val();

              pargiorno='?mese='+mese;

              url=url+pargiorno;

              if (mese!=oldmese) {
                $(location).attr('href',url);
              }
            })


            $('[data-toggle="popover"]').popover();

            $('[data-rel="chosen"],[rel="chosen"]').chosen({disable_search_threshold: 10});
            $('.chosen-select').chosen({disable_search_threshold: 10});

            $('.datepicker').datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "YYYY-MM",
                "locale": "it"
            });

        });
    </script>


    <hr/>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->
