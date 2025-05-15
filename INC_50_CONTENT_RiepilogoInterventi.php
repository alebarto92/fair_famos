<div class="container" id="firstcontainer">
    <form id="newform">


        <?php
        $operatorescelto=$_GET['operatore'];
        $giorno=$_GET['giorno'];
        if ($giorno=='') {
            $giorno=date("Y-m-d");
        }
        $giornovalidate=validateDate($giorno);
        $operatori=array();
        $queryoperatori="SELECT distinct(operatore1),pcs_users.* FROM pcs_attivita_clean JOIN pcs_users on pcs_users.id_user=pcs_attivita_clean.operatore1 order by Cognome,Nome";
        $stmtoperatori=$dbh->query($queryoperatori);
        $operatori[0]['Nome']="SCEGLI OPERATORE";
        while ($rowoperatori=$stmtoperatori->fetch(PDO::FETCH_ASSOC)) :
            $operatori[]=$rowoperatori;
        endwhile;
        ?>

        <br/>
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4" style="margin-top:10px;margin-bottom:20px;">
                <select class="form-control chosen-select" name="filtrooperatore" id="filtrooperatore">
                    <?php foreach ($operatori as $o) : ?>
                        <option value="<?php echo $o['id_user'];?>" <?php if ($o['id_user']==$operatorescelto) { echo "SELECTED"; } ?> ><?php echo $o['Cognome'];?> <?php echo $o['Nome'];?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-2" style="margin-top:10px;margin-bottom:20px;">
                <input class="datepicker" type="text" name="giorno" id="giorno" value="<?php echo $giorno;?>"/>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-2" style="margin-top:10px;margin-bottom:20px;">
                <input class="btn btn-block btn-warning" type="button" name="applicafiltri" id="applicafiltri" value="VAI"/>
            </div>

        </div>
        <br/>


    <div class="row" style="background-color: #efefef;">
        <div class="col-xs-12 " >
            <?php


            if ($operatorescelto>0) {
                $timbraturetotali=array();

                $query="SELECT *,DATE_FORMAT(timbratura, '%Y-%m-%d') as giorno,DATE_FORMAT(timbratura, '%H:%i:%s') as orario FROM pcs_timbrature WHERE id_operatore=$operatorescelto AND '$giornovalidate'<=timbratura AND '$giornovalidate 23:59:59' >=timbratura order by timbratura";
                $stmt=$dbh->query($query);
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $timbraturetotali[$row['id_operatore']][$row['giorno']][]=$row;
                }
            }
            if (count($timbraturetotali)>0) {
                foreach ($timbraturetotali as $iddipendente=>$timbraturedipendente) :
                    $dipendente=getUtente($iddipendente);
                    echo "<h3>".$dipendente['Nome']." ".$dipendente['Cognome']."</h3>";

                    foreach ($timbraturedipendente as $giorno=>$timbrature) :


                        //cerco le attività svolte in quella data
                        $attivita=array();
                        $query2="SELECT * FROM pcs_attivita_clean JOIN pcs_sedi_clienti ON pcs_attivita_clean.id_sede=pcs_sedi_clienti.id JOIN pcs_clienti ON pcs_sedi_clienti.id_cliente=pcs_clienti.id WHERE stato='conclusa' AND '$giornovalidate'<=data_fine_attivita AND '$giornovalidate 23:59:59' >=data_fine_attivita AND (operatore1=$iddipendente or operatore2=$iddipendente or operatore3=$iddipendente or operatore4=$iddipendente)";
                        //echo $query2;
                        $stmt2=$dbh->query($query2);
                        while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                            $attivita[]=$row2;
                        }

                        echo "<h4>GIORNO: ".convertDate($giorno)."</h4>";

                        ?>

                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <h4>TIMBRATURE</h4>
                                <?php
                                $oldorario='';
                                $lavorato=0;
                                echo  "<table class='table table-bordered'>";
                                echo "<tr><th>Orario</th>";
                                echo "<th>tipo</th></tr>";
                                foreach ($timbrature as $key=>$value) :
                                    echo "<tr><td>".$value['orario']."</td>";
                                    echo "<td>".$value['tipo']."</td></tr>";
                                    if ($oldorario!='') {
                                        $diff = strtotime($value['orario']) - strtotime($oldorario);
                                        if ($oldtipo=='inizio') {
                                            $lavorato.=$diff;
                                        }
                                    }
                                    $oldtipo=$value['tipo'];
                                    $oldorario=$value['orario'];
                                endforeach;
                                echo "</table>";



                                echo "<h4>Totale lavorato: ".gmdate("H:i:s", $lavorato)."</h4>";
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <h4>ATTIVITA' SVOLTE</h4>
                                <?php if (count($attivita)>0) : ?>
                                    <?php foreach ($attivita as $key=>$value) :?>
                                        <?php //print_r($value);?>
                                        <table class='table table-bordered'>
                                            <tr>
                                                <td style="text-align: right;">Nome Azienda:</td>
                                                <td><?php echo $value['NomeAzienda'];?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">Sede:</td>
                                                <td><?php echo $value['sede'];?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">Indirizzo:</td>
                                                <td><?php echo $value['indirizzo'];?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">Citta:</td>
                                                <td><?php echo $value['citta'];?></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">Provincia:</td>
                                                <td><?php echo $value['provincia'];?></td>
                                            </tr>
                                        </table>
                                    <?php endforeach;?>
                                <?php endif; ?>

                            </div>
                        </div>
                        <?php

                        echo "<br/>";
                        echo "<br/>";
                    endforeach;





                endforeach;
            }


            ?>
        </div>
    </div>

</div>

    <script type="text/javascript">
        $(document).ready(function(){

            $("#applicafiltri").click(function(){

                var url="RiepilogoInterventi.php";
                var oper=$("#filtrooperatore").val();
                var giorno=$("#giorno").val();

                pargiorno='?giorno='+giorno;

                parope='';

                if (oper!=0) {
                    parope='&operatore='+oper;
                } else {
                    parope='';
                }

                url=url+pargiorno+parope;
                $(location).attr('href',url);

            })

            $('[data-toggle="popover"]').popover();

            $('[data-rel="chosen"],[rel="chosen"]').chosen({disable_search_threshold: 10});
            $('.chosen-select').chosen({disable_search_threshold: 10});

            $(".datepicker").datetimepicker({
                "allowInputToggle": true,
                "showClose": true,
                "showClear": true,
                "showTodayButton": true,
                "format": "DD/MM/YYYY",
                locale: 'it'});

        });
    </script>


    <hr/>
    <?php include("INC_90_FOOTER.php");?>

</div> <!-- /container -->