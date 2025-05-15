<?php

/*
$querytimbrature="SELECT * from pcs_timbrature JOIN pcs_users ON pcs_timbrature.id_operatore=pcs_users.id_user";
$stmt=$dbh->query($querytimbrature);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $markers[]=$row;
}
*/

$querycantieri="SELECT sede,lat,lng from pcs_sedi_clienti where lat is not null";
$stmt=$dbh->query($querycantieri);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $markerscantieri[]=$row;
}
$querycantieripunteggi="SELECT count(*) as num,punteggio from pcs_sedi_clienti group by punteggio order by punteggio DESC";
$stmt=$dbh->query($querycantieripunteggi);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $punteggidata[]=$row['num'];
    $punteggilabel[]="'".$row['punteggio']."%'";
    $punteggi[]=$row;
}

$attivita=array();
$queryattivita="SELECT pcs_attivita_clean.*,pcs_sedi_clienti.sede from pcs_attivita_clean JOIN pcs_sedi_clienti ON pcs_attivita_clean.id_sede=pcs_sedi_clienti.id WHERE data_consigliata=CURRENT_DATE() OR (data_consigliata<CURRENT_DATE AND stato<>'conclusa' AND stato<>'annullata') order by data_consigliata";
$stmt=$dbh->query($queryattivita);
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['data_consigliata']==date("Y-m-d")) {
      $attivita['OGGI'][]=$row;
    } else {
      $attivita['SCADUTE'][]=$row;
    }
}

?>
<div class="container" id="firstcontainer">
    <div class="row">
        <div class="col-xs-12" style="margin-top:10px;">
            <!-- (i) open street map -->
            <div id="osm-map"></div>
            <!-- (f) open street map -->
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6" style="margin-top:20px;">

          <?php if (count($attivita['OGGI'])>0) : ?>
          <div class="panel panel-primary">
    				<div class="panel-heading">
    					<h3 class="panel-title">Programma di oggi <?php echo date("d/m/Y");?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge"><?php echo count($attivita['OGGI']);?></span></h3>
    					<span class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
    				</div>
    				<div class="panel-body">
              <table class="table table-bordered">
                  <thead>
                  <tr>
                      <th> Sede attività </th>
                      <th>  Data </th>
                      <th>  Stato </th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php for ($i=0;$i<count($attivita['OGGI']);$i++) { ?>
                      <tr>
                          <td>
                              <a href="module.php?modname=AttivitaClean&p[id]=<?php echo $attivita['OGGI'][$i]['id'];?>"><?php echo $attivita['OGGI'][$i]['sede'];?></a>
                          </td>
                          <td><?php echo $attivita['OGGI'][$i]['data_consigliata'];?></td>
                          <td><?php echo $attivita['OGGI'][$i]['stato'];?></td>
                      </tr>
                  <?php } ?>
                  </tbody>
              </table>
            </div>
    			</div>
        <?php endif; ?>

        <?php if (count($attivita['SCADUTE'])>0) : ?>
          <div class="panel panel-primary">
    				<div class="panel-heading">
    					<h3 class="panel-title">Attività SCADUTE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="badge"><?php echo count($attivita['SCADUTE']);?></span></h3>
    					<span class="pull-right clickable"><i class="glyphicon glyphicon-chevron-up"></i></span>
    				</div>
    				<div class="panel-body">
              <table class="table table-bordered">
                  <thead>
                  <tr>
                      <th> Sede attività </th>
                      <th>  Data </th>
                      <th>  Stato </th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php for ($i=0;$i<count($attivita['SCADUTE']);$i++) { ?>
                      <tr>
                          <td>
                              <a href="module.php?modname=AttivitaClean&p[id]=<?php echo $attivita['SCADUTE'][$i]['id'];?>"><?php echo $attivita['SCADUTE'][$i]['sede'];?></a>
                          </td>
                          <td><?php echo $attivita['SCADUTE'][$i]['data_consigliata'];?></td>
                          <td><?php echo $attivita['SCADUTE'][$i]['stato'];?></td>
                      </tr>
                  <?php } ?>
                  </tbody>
              </table>
            </div>
    			</div>
        <?php endif; ?>

        </div>
        <div class="col-xs-12 col-sm-6" style="margin-top:10px;">
            <div id="chart"></div>
        </div>
    </div>

    <script>
        jQuery(function($) {

          $(document).on('click', '.panel-heading span.clickable', function(e){
              var $this = $(this);
          	if(!$this.hasClass('panel-collapsed')) {
          		$this.parents('.panel').find('.panel-body').slideUp();
          		$this.addClass('panel-collapsed');
          		$this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
          	} else {
          		$this.parents('.panel').find('.panel-body').slideDown();
          		$this.removeClass('panel-collapsed');
          		$this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
          	}
          })



            var options = {
                series: [{
                    name: 'cantieri',
                    data: [<?php echo join(",",$punteggidata);?>]
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        distributed: true,
                    }
                },
                colors: ['#33b2df', '#546E7A', '#d4526e', '#13d8aa', '#A5978B', '#2b908f', '#f9a3a4', '#90ee7e',
                    '#f48024', '#69d2e7'
                ],
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: [<?php echo join(",",$punteggilabel);?>
                    ],
                },
                yaxis: {
                    title: {
                        text: '% di completamento',
                        offsetX: 0,
                        offsetY: 0,
                    },
                    labels: {
                        show: false
                    }
                },
                title: {
                    text: 'Cantieri',
                    align: 'left'
                },
            };

            var chart = new ApexCharts(document.querySelector("#chart"), options);

            chart.render();


            //https://github.com/pointhi/leaflet-color-markers
            var greenIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var goldIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var orangeIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Where you want to render the map.
            var element = document.getElementById('osm-map');

            // Height has to be set. You can do this in CSS too.
            element.style = 'height:300px;';

            // Create Leaflet map on map element.
            var map = L.map(element);

            getLocation(map);

            function getLocation(map) {
                navigator.geolocation.getCurrentPosition(showPosition,
                    function(error) {
                        showPosition(false);
                        if (error.code == error.PERMISSION_DENIED)
                            console.log("you denied me :-(");
                    });
            }
            function showPosition(position) {
                if (position==false) {
                    target=false;
                } else {
                    target=L.latLng(position.coords.latitude, position.coords.longitude);
                }

                // Add OSM tile leayer to the Leaflet map.
                L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Place a marker on the same location.
                //L.marker(target,{draggable:true}).addTo(map);

                var marker=Array();

                if (target) {
                    var newmarker = new L.marker(target, {draggable: true}).addTo(map).bindPopup('<b>Posizione</b> attuale')
                        .openPopup();
                    marker.push(newmarker);
                }

                <?php for ($i=0;$i<count($markers);$i++) : ?>
                    var tipo='<?php echo $markers[$i]['tipo'];?>';
                    var target=L.latLng(<?php echo $markers[$i]['lat']?>, <?php echo $markers[$i]['lng']?>);
                    var messaggio='<b><?php echo $markers[$i]['Cognome'];?> <?php echo $markers[$i]['Nome'];?></b>';
                messaggio+='<br/><?php echo $markers[$i]['timbratura'];?>';
                messaggio+='<br/>'+tipo;
                    if (tipo=='inizio') {
                        var newmarker=new L.marker(target, {draggable: true,icon:goldIcon}).addTo(map).bindPopup(messaggio).openPopup();
                    } else {
                        var newmarker=new L.marker(target, {draggable: true,icon:orangeIcon}).addTo(map).bindPopup(messaggio).openPopup();
                    }
                    marker.push(newmarker);
                <?php endfor; ?>

                <?php for ($i=0;$i<count($markerscantieri);$i++) : ?>
                var target=L.latLng(<?php echo $markerscantieri[$i]['lat']?>, <?php echo $markerscantieri[$i]['lng']?>);
                var messaggio="<?php echo $markerscantieri[$i]['sede'];?>";
                var newmarker=new L.marker(target, {icon:greenIcon}).addTo(map).bindPopup(messaggio).openPopup();
                marker.push(newmarker);
                <?php endfor; ?>

                //console.log(marker);

                var group = new L.featureGroup(marker);

                map.fitBounds(group.getBounds());

            }


        });
    </script>

    <hr>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->
