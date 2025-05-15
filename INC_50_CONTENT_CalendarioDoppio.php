<div class="container" id="firstcontainer" >
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


    $operatoriscelti1=explode(",",$_GET['idoperatore1']);
    $sediscelte1=explode(",",$_GET['idsede1']);
    $statiscelti1=explode(",",$_GET['stato1']);
    $operatoriscelti2=explode(",",$_GET['idoperatore2']);
    $sediscelte2=explode(",",$_GET['idsede2']);
    $statiscelti2=explode(",",$_GET['stato2']);

    ?>
<div class="row">
    <div class="col col-xs-12 col-sm-6">
    <div id="calendar1" style="width:100%;"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
          <select class="form-control chosen-select" name="filtrooperatore1" id="filtrooperatore1">
              <?php foreach ($operatori as $o) : ?>
                  <option value="<?php echo $o['id_user'];?>" <?php if (in_array($o['id_user'],$operatoriscelti1)) { echo "SELECTED"; } ?> ><?php echo $o['Cognome'];?> <?php echo $o['Nome'];?></option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
          <select class="form-control chosen-select" multiple name="filtrosedi1" id="filtrosedi1">
              <?php foreach ($sediclienti as $o) : ?>
                  <option value="<?php echo $o['id'];?>" <?php if (in_array($o['id'],$sediscelte1)) { echo "SELECTED"; } ?> ><?php echo $o['value'];?> </option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
          <select class="form-control chosen-select" multiple name="filtrostato1" id="filtrostato1">
              <?php foreach ($stati as $s) : ?>
                  <option value="<?php echo $s;?>" <?php if (in_array($s,$statiscelti1)) { echo "SELECTED"; } ?> ><?php echo $s;?></option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-2" style="margin-top:10px;margin-bottom:20px;">
          <input class="applicafiltri btn btn-block btn-warning" type="button" name="applicafiltri1" id="applicafiltri1" value="VAI"/>
      </div>
    </div>    </div>
    <div class="col col-xs-12 col-sm-6">

    <div id="calendar2" style="width:100%;"></div>
    <div class="row">
      <div class="col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
          <select class="form-control chosen-select" name="filtrooperatore2" id="filtrooperatore2">
              <?php foreach ($operatori as $o) : ?>
                  <option value="<?php echo $o['id_user'];?>" <?php if (in_array($o['id_user'],$operatoriscelti2)) { echo "SELECTED"; } ?> ><?php echo $o['Cognome'];?> <?php echo $o['Nome'];?></option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
          <select class="form-control chosen-select" multiple name="filtrosedi2" id="filtrosedi2">
              <?php foreach ($sediclienti as $o) : ?>
                  <option value="<?php echo $o['id'];?>" <?php if (in_array($o['id'],$sediscelte2)) { echo "SELECTED"; } ?> ><?php echo $o['value'];?> </option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-3" style="margin-top:10px;margin-bottom:20px;">
          <select class="form-control chosen-select" multiple name="filtrostato2" id="filtrostato2">
              <?php foreach ($stati as $s) : ?>
                  <option value="<?php echo $s;?>" <?php if (in_array($s,$statiscelti2)) { echo "SELECTED"; } ?> ><?php echo $s;?></option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-2" style="margin-top:10px;margin-bottom:20px;">
          <input class="applicafiltri btn btn-block btn-warning" type="button" name="applicafiltri2" id="applicafiltri2" value="VAI"/>
      </div>
    </div>

    </div>
</div>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-rel="chosen"],[rel="chosen"]').chosen({allow_single_deselect: true ,disable_search_threshold: 10});
            $('.chosen-select').chosen({disable_search_threshold: 10, allow_single_deselect: true });
            $(".applicafiltri").click(function(){

                var url="CalendarioDoppio.php?1=1";
                var oper1=$("#filtrooperatore1").val();
                var stato1=$("#filtrostato1").val();
                var sede1=$("#filtrosedi1").val();
                var oper2=$("#filtrooperatore2").val();
                var stato2=$("#filtrostato2").val();
                var sede2=$("#filtrosedi2").val();

                parope1='';
                parstato1='';
                parsede1='';
                parope2='';
                parstato2='';
                parsede2='';

                if (oper1!=null) {
                    parope1='&idoperatore1='+oper1;
                } else {
                    parope1='';
                }

                if (stato1!=null) {
                    parstato1='&stato1='+stato1;
                } else {
                    parstato1='';
                }
                if (sede1!=null) {
                    parsede1='&idsede1='+sede1;
                } else {
                    parsede1='';
                }

                if (oper2!=null) {
                    parope2='&idoperatore2='+oper2;
                } else {
                    parope2='';
                }

                if (stato2!=null) {
                    parstato2='&stato2='+stato2;
                } else {
                    parstato2='';
                }
                if (sede2!=null) {
                    parsede2='&idsede2='+sede2;
                } else {
                    parsede2='';
                }

                url=url+parope1+parstato1+parsede1+parope2+parstato2+parsede2;
                $(location).attr('href',url);


            })

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl1 = document.getElementById('calendar1');
            var calendar1 = new FullCalendar.Calendar(calendarEl1, {
                plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listDay,listWeek'
                },
                defaultDate: '<?php echo date("Y-m-d");?>',
                editable: true,
                droppable: false,
                views: {
                    month: {
                        droppable: true
                    }
                },
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

		            eventLeave: function( info ) { console.log('calendar 1 eventLeave');
                console.log("L'evento spostato è il numero "+info.event.id+" e l'operatore che sposta è <?php echo $_GET[idoperatore1];?>");
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

              eventReceive: function( info ) {

                console.log('calendar 1 eventReceive');
              console.log("L'evento ricevuto è il numero "+info.event.id+" e l'operatore che riceve è <?php echo $_GET[idoperatore1];?>");
              console.log(info.event.start.toISOString());
              var params={};
              params.id_attivita=info.event.id;
              params.operatore_source=<?php echo $_GET[idoperatore2];?>;
              params.operatore_dest  =<?php echo $_GET[idoperatore1];?>;
              params.nuovadata=info.event.start.toISOString();

              console.log(params);

              $.ajax({
                  dataType: "json",
                  type: 'POST',
                  url: "ajax_modificaAttivitadaCalendarioDoppio.php",
                  data: jQuery.param(params),
                  success: function (data) {
                      console.log(data);
                      if (data.result==true) {
                        location.reload();
                      } else {
                          alert("Errore modifica attività!");
                      }
                  },
                  error: function (e) {
                      alert("Errore db!");
                      console.log(e);
                  }
              });
            },

                events: {
                    url: 'ajax_getEvents.php?stato=<?php echo $_GET[stato1];?>&idoperatore=<?php echo $_GET[idoperatore1];?>&idsede=<?php echo $_GET[idsede1];?>',
                    failure: function() {
                        alert("Non funziona");
                    }
                },
                loading: function(bool) {
                    document.getElementById('loading').style.display =
                        bool ? 'block' : 'none';
                }
            });
            calendar1.render();
            calendar1.setOption('locale', 'it');


            var calendarEl2 = document.getElementById('calendar2');
            var calendar2 = new FullCalendar.Calendar(calendarEl2, {
                plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listDay,listWeek'
                },
                defaultDate: '<?php echo date("Y-m-d");?>',
                editable: true,
                droppable: false,
                views: {
                    month: {
                        droppable: true
                    }
                },
                navLinks: true, // can click day/week names to navigate views
                eventLimit: true, // allow "more" link when too many events
                lazyFetching: false,

                eventLeave: function( info ) { console.log('calendar 2 eventLeave');
                console.log("L'evento spostato è il numero "+info.event.id+" e l'operatore che sposta è <?php echo $_GET[idoperatore2];?>");
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

              eventReceive: function( info ) { console.log('calendar 2 eventReceive');
              console.log("L'evento ricevuto è il numero "+info.event.id+" e l'operatore che riceve è <?php echo $_GET[idoperatore2];?>");
              console.log(info.event.start.toISOString());
              var params={};
              params.id_attivita=info.event.id;
              params.operatore_source=<?php echo $_GET[idoperatore1];?>;
              params.operatore_dest  =<?php echo $_GET[idoperatore2];?>;
              params.nuovadata=info.event.start.toISOString();

              console.log(params);

              $.ajax({
                  dataType: "json",
                  type: 'POST',
                  url: "ajax_modificaAttivitadaCalendarioDoppio.php",
                  data: jQuery.param(params),
                  success: function (data) {
                      console.log(data);
                      if (data.result==true) {
                          location.reload();
                      } else {
                          alert("Errore modifica attività!");
                      }
                  },
                  error: function (e) {
                      alert("Errore db!");
                      console.log(e);
                  }
              });
              },
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

                events: {
                    url: 'ajax_getEvents.php?stato=<?php echo $_GET[stato2];?>&idoperatore=<?php echo $_GET[idoperatore2];?>&idsede=<?php echo $_GET[idsede2];?>',
                    failure: function() {
                        alert("Non funziona");
                    }
                },
                loading: function(bool) {
                    document.getElementById('loading').style.display =
                        bool ? 'block' : 'none';
                }
            });
            calendar2.render();
            calendar2.setOption('locale', 'it');

        });
    </script>


    <hr/>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->
