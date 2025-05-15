<div class="print container" id="firstcontainer">
    <div id='loading'>loading...</div>
    <?php
    $stati=getEnumValues("pcs_attivita_clean","stato");

    $queryoperatori="SELECT pcs_users.* FROM pcs_users WHERE id_ruolo=3 AND clean=1 order by Cognome,Nome";
    $stmtoperatori=$dbh->query($queryoperatori);
    while ($rowoperatori=$stmtoperatori->fetch(PDO::FETCH_ASSOC)) :
    $operatori[]=$rowoperatori;
    endwhile;

    $querysedi="SELECT pcs_sedi_clienti.id as id, CONCAT('ID ',pcs_clienti.id,' ',pcs_clienti.nome,' ',pcs_clienti.cognome,' - ',pcs_sedi_clienti.sede) as value FROM pcs_sedi_clienti JOIN pcs_clienti ON pcs_clienti.id=pcs_sedi_clienti.id_cliente ORDER BY value";
    $stmtsedi=$dbh->query($querysedi);
    while ($rowsedi=$stmtsedi->fetch(PDO::FETCH_ASSOC)) :
        $sediclienti[]=$rowsedi;
    endwhile;


    $operatoriscelti=explode(",",$_GET['idoperatore']);
    $sediscelte=explode(",",$_GET['idsede']);
    $statiscelti=explode(",",$_GET['stato']);

    ?>

    <div class="row">
        <div class="col-sm-offset-1 col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
            <select class="form-control chosen-select" multiple name="filtrooperatore" id="filtrooperatore">
                <?php foreach ($operatori as $o) : ?>
                    <option value="<?php echo $o['id_user'];?>" <?php if (in_array($o['id_user'],$operatoriscelti)) { echo "SELECTED"; } ?> ><?php echo $o['Cognome'];?> <?php echo $o['Nome'];?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="dontprint col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
            <select class="form-control chosen-select" multiple name="filtrosedi" id="filtrosedi">
                <?php foreach ($sediclienti as $o) : ?>
                    <option value="<?php echo $o['id'];?>" <?php if (in_array($o['id'],$sediscelte)) { echo "SELECTED"; } ?> ><?php echo $o['value'];?> </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="dontprint col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
            <select class="form-control chosen-select" multiple name="filtrostato" id="filtrostato">
                <?php foreach ($stati as $s) : ?>
                    <option value="<?php echo $s;?>" <?php if (in_array($s,$statiscelti)) { echo "SELECTED"; } ?> ><?php echo $s;?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="dontprint col-xs-12 col-sm-4 col-md-2" style="margin-top:10px;margin-bottom:20px;">
            <input class="btn btn-block btn-warning" type="button" name="applicafiltri" id="applicafiltri" value="APPLICA FILTRO"/>
        </div>

    </div>
    <br/>

    <div class="print" id="calendar" style="width:100%;"></div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-rel="chosen"],[rel="chosen"]').chosen({allow_single_deselect: true ,disable_search_threshold: 10});
            $('.chosen-select').chosen({disable_search_threshold: 10, allow_single_deselect: true });
            $("#applicafiltri").click(function(){

                var url="Calendario.php?1=1";
                var oper=$("#filtrooperatore").val();
                var stato=$("#filtrostato").val();
                var sede=$("#filtrosedi").val();

                parope='';
                parstato='';
                parsede='';

                if (oper!=null) {
                    parope='&idoperatore='+oper;
                } else {
                    parope='';
                }

                if (stato!=null) {
                    parstato='&stato='+stato;
                } else {
                    parstato='';
                }
                if (sede!=null) {
                    parsede='&idsede='+sede;
                } else {
                    parsede='';
                }

                url=url+parope+parstato+parsede;
                $(location).attr('href',url);


            })
        });
    </script>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listDay,listWeek'
                },
                defaultDate: '<?php echo date("Y-m-d");?>',
                editable: true,
                navLinks: true, // can click day/week names to navigate views
                eventLimit: true, // allow "more" link when too many events
                lazyFetching: false,

                eventRender: function(info) {
                    if (info.event.extendedProps.non_rigenerare=='si')
                    {
                        $(info.el).css("border", "3px dashed #fff");
                        $(info.el).css("-webkit-box-shadow", "0px 0px 0px 2px #222");
                        $(info.el).css("-moz-box-shadow","0px 0px 0px 2px #222");
                        $(info.el).css("box-shadow","0px 0px 0px 2px #222");
                    }
                    if (!(info.event.extendedProps.id_attivita_primaria>0))
                    {
                        $(info.el).css("border", "4px solid #ffff00");
                        $(info.el).css("-webkit-box-shadow", "0px 0px 0px 2px #000");
                        $(info.el).css("-moz-box-shadow","0px 0px 0px 2px #000");
                        $(info.el).css("box-shadow","0px 0px 0px 2px #000");
                        //$(info.el).css("background-image", "linear-gradient(to bottom right, red , blue)");
                    }
                },

                eventClick: function(info) {
                    bootbox.alert({
                        title: info.event.title,
                        message: info.event.extendedProps.description,
                        callback: function(){ /* your callback code */ }
                    });
                    console.log(info.event);
                },

                eventResize: function(info) {
                    //devo chiamare updateAttivita
                    var params={};
                    params.id_attivita=info.event.id;

                    //costruisco la nuova data di inizio e ora di inizio
                    var anno=info.event.start.getFullYear();
                    var mese=info.event.start.getMonth()+1;
                    if (mese<10) {
                        mese='0'+mese;
                    }
                    var giorno=info.event.start.getDate();
                    if (giorno<10) {
                        giorno='0'+giorno;
                    }

                    var ore=info.event.start.getHours();
                    if (ore<10) { ore='0'+ore; }

                    var minuti=info.event.start.getMinutes();
                    if (minuti<10) { minuti='0'+minuti; }

                    params.newstartdata=anno+'-'+mese+'-'+giorno;
                    params.newstartora=ore+':'+minuti;

                    //costruisco la nuova ora di fine

                    var ore=info.event.end.getHours();
                    if (ore<10) { ore='0'+ore; }

                    var minuti=info.event.end.getMinutes();
                    if (minuti<10) { minuti='0'+minuti; }

                    params.newendora=ore+':'+minuti;

                    console.log(params);

                    $.ajax({
                        dataType: "json",
                        type: 'POST',
                        url: "ajax_modificaAttivitadaCalendario.php",
                        data: jQuery.param(params),
                        success: function (data) {
                            console.log(data);
                            if (data.result==true) {

                            } else {
                                alert("Errore modifica attività!");
                            }
                        },
                        error: function (e) {
                            alert("Errore db!");
                        }
                    });
                },

                eventDrop: function(info) {
                    var params={};
                    params.id_attivita=info.event.id;

                    //costruisco la nuova data di inizio e ora di inizio
                    var anno=info.event.start.getFullYear();
                    var mese=info.event.start.getMonth()+1;
                    if (mese<10) {
                        mese='0'+mese;
                    }
                    var giorno=info.event.start.getDate();
                    if (giorno<10) {
                        giorno='0'+giorno;
                    }

                    var ore=info.event.start.getHours();
                    if (ore<10) { ore='0'+ore; }

                    var minuti=info.event.start.getMinutes();
                    if (minuti<10) { minuti='0'+minuti; }

                    params.newstartdata=anno+'-'+mese+'-'+giorno;
                    params.newstartora=ore+':'+minuti;

                    //costruisco la nuova ora di fine

                    var ore=info.event.end.getHours();
                    if (ore<10) { ore='0'+ore; }

                    var minuti=info.event.end.getMinutes();
                    if (minuti<10) { minuti='0'+minuti; }

                    params.newendora=ore+':'+minuti;

                    console.log(params);

                    $.ajax({
                        dataType: "json",
                        type: 'POST',
                        url: "ajax_modificaAttivitadaCalendario.php",
                        data: jQuery.param(params),
                        success: function (data) {
                            console.log(data);
                            if (data.result==true) {

                            } else {
                                alert("Errore modifica attività!");
                            }
                        },
                        error: function (e) {
                            alert("Errore db!");
                        }
                    });

                },

                events: {
                    url: 'ajax_getEvents.php?stato=<?php echo $_GET[stato];?>&idoperatore=<?php echo $_GET[idoperatore];?>&idsede=<?php echo $_GET[idsede];?>',
                    failure: function() {
                        alert("Non funziona");
                    }
                },
                loading: function(bool) {
                    document.getElementById('loading').style.display =
                        bool ? 'block' : 'none';
                }
            });

            calendar.render();
            calendar.setOption('locale', 'it');

            <?php if ($_GET['viewday']!='') { ?>
              calendar.changeView('timeGridDay', '<?php echo $_GET['viewday'];?>');
            <?php } ?>
            <?php if ($_GET['viewweek']!='') { ?>
              calendar.changeView('timeGridWeek', '<?php echo $_GET['viewweek'];?>');
            <?php } ?>


        });


    </script>


    <hr/>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->
