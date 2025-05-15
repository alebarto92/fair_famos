<?php if ($_REQUEST['modname']!='') { ?>
    <?php $_REQUEST['idmod']=getModuloFrom_nome_modulo($_REQUEST['modname']); ?>
<?php } ?>
<?php
$idmodGE=$_REQUEST['idmod'];
$idele=$_REQUEST['idele'];

$k=$_REQUEST['k'];
$k1=$_REQUEST['k1'];
$k2=$_REQUEST['k2'];
$view=$_REQUEST['view'];
?>
<?php if ($idmodGE>0) { ?>
    <?php $modulo=getModulo($idmodGE);?>
<?php } ?>
<?php if ($idmodGE==74) :
    if ($idele!='-1') {
        $ideleevaluated=str_replace("'","",$idele);
        $elemento=getElemento($idmodGE,$ideleevaluated);
        if ($elemento['lat']!='') {
            $defaultLat=$elemento['lat'];
        }
        if ($elemento['lng']!='') {
            $defaultLng = $elemento['lng'];
        }
    } else { $elemento['punteggio_servizi']=0; $elemento['punteggio_attivita']=0;} endif; ?>

<div class="container" id="firstcontainer">
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-md-12">
            <div class="box-inner">
                <div class="box-header" data-original-title="">
                    <h2><?php echo _($modulo['nome_modulo']);?> <?php if ($modulo['nome_modulo']=="Clienti") { echo " - ID: ".$idele; } ?></h2>

                </div>

                <?php if ($modulo['nome_modulo']=='Cantieri') { ?>
                    <div class="row">
                        <div class="col-xs-12 col-md-12">
                            <div class="progress" style="margin-right:30px;">
                                <div id="progressbarcantiere"></div>
                            </div>
                        </div>
                        <?php if ($elemento['suggerimento']!='') { ?>

                            <div class="col-xs-12 col-md-12">
                                <div class="alert alert-info alert-dismissable" style="margin-right:30px;">
                                    <button type="button" class="close" aria-hidden="true">&times;</button>
                                    <p><?php echo $elemento['suggerimento'];?></p>
                                </div>
                            </div>

                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="box-content">
                    <?php /* (i) ------------------------------ elenco del singolo modulo --------------------------------------------------------------------------------------   */ ?>
                    <div id="singoloelemento" >
                        LOADING...
                    </div>

                    <?php /* (i) ---------------------- Cantieri ---------------- */ ?>
                    <?php if ($modulo['nome_modulo']=='Cantieri') { ?>
                        <!-- (i) open street map -->
                        <h3>MAPPA</h3>
                        <div class="row">
                            <div class="col-xs-12 col-sm-4">
                                <a class="btn btn-warning" id="calcolacoordinatedaindirizzo">Calcola coordinate tramite indirizzo</a><br/>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                Lat: <input type="text" class=form-control" id="lat_suggerita" />
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                Lng: <input type="text" class=form-control" id="lng_suggerita" />
                            </div>
                        </div>
                        <br/>
                        <div id="osm-map"></div>

                        <!-- (f) open street map -->
                    <?php } ?>
                    <?php /* (f) ---------------------- Cantieri ---------------- */ ?>

                    <div id="bottoni" class="visualizzadopo" style="display:none;margin-top:10px;">
                        <button class="btn btn-sm btn-warning btn-save" id="nuovo_elemento_reload" name="nuovo_elemento_reload" >
                            <i class="glyphicon glyphicon-save"></i>
                            <?php echo _("Salva");?>
                        </button>
                            <button class="btn btn-sm btn-info btn-save" id="nuovo_elemento_close" name="nuovo_elemento_close" >
                                <i class="glyphicon glyphicon-repeat"></i>
                                <?php echo _("Chiudi");?>
                            </button>

                            <!--<button idele="<?php echo $idele;?>" idmod="<?php echo $idmodGE;?>" class="btn btn-sm btn-danger btn-save delete_elemento"  >
                                <i class="glyphicon glyphicon-trash"></i>
                                <?php echo _("Elimina");?>
                            </button>-->
                    </div>

                    <?php /* (f) ------------------------------ elenco del singolo modulo --------------------------------------------------------------------------------------   */ ?>

                </div>
            </div>
        </div>
    </div><!--/row-->
    <!-- (i) inline scripts related to this page -->


    <script src="ckeditor/ckeditor.js"></script>
    <script src="plupload/js/plupload.full.min.js"></script>
    <script type="text/javascript">
        jQuery(function($) {

        <?php if ($idmodGE==74) : ?>

            $(".alert button.close").click(function (e) {
                $(this).fadeOut('slow');
            });

            function aggiornaprogressbar() {
                var punteggio_servizi=<?php echo $elemento['punteggio_servizi'];?>;
                var punteggio_attivita=<?php echo $elemento['punteggio_attivita'];?>;
                var punteggio=punteggio_servizi+punteggio_attivita;
                //if ($("#qrcodeassociato").val() != '') {
                //    punteggio=punteggio+20;
                //}
                if ($("#indirizzo").val() != '') {
                    punteggio=punteggio+15;
                }
                if ($("#sede").val() != '') {
                    punteggio=punteggio+15;
                }
                if ($("#CAP").val() != '') {
                    punteggio=punteggio+5;
                }
                if ($("#citta").val() != '') {
                    punteggio=punteggio+5;
                }
                if ($("#provincia").val() != '') {
                    punteggio=punteggio+5;
                }

                if (($("#lat").val() =='') && ($("#lng").val() == '')) {

                } else {
                    punteggio=punteggio+25;
                }

                if (punteggio<20) {
                    progressbarcolor="danger";
                } else if (punteggio<=50) {
                    progressbarcolor="warning";
                } else {
                    progressbarcolor="success";
                }


                $("#progressbarcantiere").html('                                <div class="progress-bar progress-bar-'+progressbarcolor+' progress-bar-striped active" role="progressbar" aria-valuenow="'+punteggio+'"\n' +
                    '                                     aria-valuemin="0" aria-valuemax="100" style="min-width:2em;width:'+punteggio+'%">\n' +
                    punteggio+'%                                    \n' +
                    '                                </div>\n')

            }

            $(document).on('change', '#indirizzo', function() {
                aggiornaprogressbar();
            });
            $(document).on('change', '#qrcodeassociato', function() {
                aggiornaprogressbar();
            });
            $(document).on('change', '#sede', function() {
                aggiornaprogressbar();
            });
            $(document).on('change', '#CAP', function() {
                aggiornaprogressbar();
            });
            $(document).on('change', '#citta', function() {
                aggiornaprogressbar();
            });
            $(document).on('change', '#provincia', function() {
                aggiornaprogressbar();
            });



            // Where you want to render the map.
        var element = document.getElementById('osm-map');

        // Height has to be set. You can do this in CSS too.
        element.style = 'height:300px;';

        // Create Leaflet map on map element.
        var map = L.map(element);

        // Add OSM tile leayer to the Leaflet map.
        L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Target's GPS coordinates.
        var target = L.latLng('<?php echo $defaultLat;?>', '<?php echo $defaultLng;?>');

        // Set map's center to target with zoom 14.
        map.setView(target, 14);

        // Place a marker on the same location.
        //L.marker(target,{draggable:true}).addTo(map);

        var marker = new L.marker(target,{
            draggable: true
        }).addTo(map);

        marker.on("moveend", function(e) {
            var marker = e.target;
            var position = marker.getLatLng();
            map.panTo(new L.LatLng(position.lat, position.lng));

            $("#lat").val(position.lat);
            $("#lng").val(position.lng);
            $("#lat_suggerita").val(position.lat);
            $("#lng_suggerita").val(position.lng);
            aggiornaprogressbar();
            $("#nuovo_elemento_reload").delay(1000).fadeOut().fadeIn('slow');

        });



        $("#calcolacoordinatedaindirizzo").click(function(){
            var indirizzo=$("#indirizzo").val();
            var citta=$("#citta").val();
            var CAP=$("#CAP").val();
            var provincia=$("#provincia").val();
            //var paramstring="q="+indirizzo+","+citta+","+provincia;
            var paramstring="street="+indirizzo;
            paramstring+="&city="+citta;
            paramstring+="&postalcode="+CAP;

            var url="https://nominatim.openstreetmap.org/search?"+paramstring+"&format=json&polygon_geojson=1&addressdetails=1";
            console.log(url);
            $.get( url, function( data ) {
                if (data.length>0) {
                    var target2 = L.latLng(data[0].lat,data[0].lon);
                    marker.setLatLng(target2);
                    map.panTo(target2);
                    $("#lat").val(data[0].lat);
                    $("#lng").val(data[0].lon);
                    $("#lat_suggerita").val(data[0].lat);
                    $("#lng_suggerita").val(data[0].lon);
                    aggiornaprogressbar();
                    $("#nuovo_elemento_reload").delay(1000).fadeOut().fadeIn('slow');

                }

            });

        })


    <?php endif; ?>

            var idele=<?php echo $idele;?>;
            var idmod=<?php echo $idmodGE;?>;

            var decorrenzaattuale=''; //global, per prevenire il primo avviso sul cambio datadecorrenza
            $("#nuovo_elemento").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $(".btn-save").attr('disabled','disabled');

                //devo riattivare i campi enum messi in readonly
                $('input, select').attr('disabled', false);
                $.post("ajax_modifica_elemento.php", $("#nuovo_elemento").serialize(), function(msg){$("#messaggiovalidazione").html(msg);});
                setTimeout(function(){window.close();}, 2000);
            });

            $("#nuovo_elemento_close").click(function(e){
              console.log("ci sono");
                e.preventDefault();
                e.stopPropagation();
                $(".btn-save").attr('disabled','disabled');
                var url='<?php echo $_SERVER[HTTP_REFERER];?>';
                setTimeout(function(){$(location).attr('href',url);}, 100);
            });


            $(document).on('click', '.delete_elemento', function() {
              var idele=$(this).attr("idele");
              var idmod=$(this).attr("idmod");
              bootbox.confirm("<?php echo _('Sicuro di voler eliminare questo elemento?');?>", function(result) {
                  if (result) {
                    console.log("ajax_delete_elemento_new.php?idele="+idele+"&idmod="+idmod);
                    $.ajax({
                        type: "GET",
                        url: "ajax_delete_elemento_new.php?idele="+idele+"&idmod="+idmod,
                        dataType: 'json',
                        success: function(data){
                            console.log(data);
                            if (data.result==true) {
                                $.notify(data.msg,'success');
                                setTimeout(function(){location.reload();}, 2000);
                            } else {
                                $.notify(data.error);
                                console.log(data);
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                  }
              });
            });

            $("#ritorna_a_cliente").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                var url='module.php?modname=Clienti&p[id]='+$("#id_cliente").val();
                setTimeout(function(){$(location).attr('href',url);}, 100);
            });

            $("#nuovo_elemento_reload").click(function(e){

                //console.log("SONO QUI!!!!!!!");
                console.log($("#nuovo_elemento").serialize());
                e.preventDefault();
                e.stopPropagation();
                $(".btn-save").attr('disabled','disabled');
                //devo riattivare i campi enum messi in readonly
                $('input, select').attr('disabled', false);

                //console.log( $("#nuovo_elemento").serialize());
                $.post("ajax_modifica_elemento.php", $("#nuovo_elemento").serialize(), function(msg){$("#messaggiovalidazione").html(msg); console.log(msg)});
                $(".btn-save").removeAttr('disabled');
            });

            $("#nuovo_elemento_rimani").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                $(".btn-save").attr('disabled','disabled');
                //devo riattivare i campi enum messi in readonly
                $('input, select').attr('disabled', false);
                $.post("ajax_modifica_elemento.php?rimani=1", $("#nuovo_elemento").serialize(), function(msg){$("#messaggiovalidazione").html(msg);} );
            });

            var editor;

            $('[data-rel=tooltip]').tooltip();

            $('#singoloelemento').load('ajax_getmodulo.php?'+ $.param({
                    backurl: 'http://<?php echo $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];?>',
                    <?php if ($_REQUEST['debug']) { ?>
                    debug: '<?php echo $_REQUEST['debug'];?>',
                    <?php } ?>
                    <?php if ($k) { ?>
                    k: '<?php echo $k;?>',
                    <?php } ?>
                    <?php if ($k1) { ?>
                    k1: '<?php echo $k1;?>',
                    <?php } ?>
                    <?php if ($k2) { ?>
                    k2: '<?php echo $k2;?>',
                    <?php } ?>
                    <?php if ($view) { ?>
                    view: '<?php echo $view;?>',
                    <?php } ?>
                    idmod: idmod,
                    idele: idele}),function(result){
                //a questo punto il form dinamico è caricato!
                var ck=0;
                var editor=new Array;
                //attivo CKEDITOR su tutte le textarea di classe ckeditortextarea
                //e metto in ascolto l'evento onchange così da tenere sempre aggiornato il campo del form di riferimento da mandare in POST
                $(".ckeditortextarea").each(function(){
                    var nometextarea=$(this).attr("id");
                    editor[ck]=CKEDITOR.replace( nometextarea );
                    editor[ck].on('change', function( evt ) {
                        var data=evt.editor.getData();
                        var elemento=evt.editor.element;
                        var idelemento=elemento.getId();
                        $("#"+idelemento).text(data);
                        //alert(elemento.getId());
                        //alert(data);
                    });
                    ck++;
                });
                $(".visualizzadopo").show();
                decorrenzaattuale=$("#decorrenza").val();
                //$('.timepicker_interno').timepicker({showMeridian: false});

                $(".timepicker_interno").datetimepicker({locale: 'it',            "allowInputToggle": true,
                    "showClose": true,
                    "showClear": true,
                    "showTodayButton": true,
                    "format": "HH:mm:ss",
                });
                $('.datetimepicker').datetimepicker({locale: 'it',
                    "allowInputToggle": true,
                    "showClose": true,
                    "showClear": true,
                    "showTodayButton": true,
                    "format": "DD/MM/YYYY HH:mm:ss"
                });
                $(".datepicker").datetimepicker({
                    "allowInputToggle": true,
                    "showClose": true,
                    "showClear": true,
                    "showTodayButton": true,
                    "format": "DD/MM/YYYY",
                    locale: 'it'});
                $(".colorpicker").colorpicker();


<?php if ($idmodGE==74) : ?>
                aggiornaprogressbar();
<?php endif; ?>
            }); //fine $('#modal-body-myModal').load('ajax_getmodulo.php?'+ $.param({


        })
    </script>


    <!-- (f) inline scripts related to this page -->
    <hr>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->
