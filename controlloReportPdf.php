<?php
include("config.php");
$ymassimo=190;

$idreport=$_GET['idreport'];

$invia=$_GET['invia'];

if ($invia>0) {
    $async=false;
} else {
    $invia=0;
    $async=true;
}

$debug=0;
if ($_GET['debug']=='VIACOLDEBUG') {
    $debug=1;
}

if ($idreport=='') {
    exit;
}

$tbreports      =$GLOBAL_tb['reports'];
$tbvisite       =$GLOBAL_tb['visite'];
$tbispezioni    =$GLOBAL_tb['ispezioni'];

$query1="SELECT * FROM $tbreports WHERE id=?";
if ($debug==1) echo $query1;
$stmt1=$dbh->prepare($query1);
$stmt1->execute(array($idreport));
if ($REPORT=$stmt1->fetch(PDO::FETCH_ASSOC)) {
    if ($invia>0) {
        //ripulisco la colonna "immagini" perché le devo rigenerare
        $queryUpdate="UPDATE $tbreports SET immagini=NULL WHERE id=?";
        $stmtU=$dbh->prepare($queryUpdate);
        $stmtU->execute(array($idreport));
    }
} else {
    echo "Spiacente, nessun report disponibile!";
    exit;
}

//ORA INIZIO A COSTRUIRE
//ESTRAGGO TUTTE LE VISITE E QUINDI LE ISPEZIONI CHE MI INTERESSANO, CIOE' COMPRESE TRA
//$report['data_inizio'] e $report['data_fine']

$queryc="SELECT * FROM pcs_sedi_clienti JOIN pcs_clienti ON pcs_sedi_clienti.id_cliente=pcs_clienti.id WHERE pcs_sedi_clienti.id=?";
$stmtc=$dbh->prepare($queryc);
$stmtc->execute(array($REPORT['id_sede']));
$CLIENTE=$stmtc->fetch(PDO::FETCH_ASSOC);

$query2="SELECT * FROM $tbvisite WHERE id_sede =? AND (data_fine_visita BETWEEN ? AND ?) order by data_fine_visita ASC";
$stmt2=$dbh->prepare($query2);
$stmt2->execute(array($REPORT['id_sede'],$REPORT['data_inizio'],$REPORT['data_fine']));
$VISITE=array();
while ($row=$stmt2->fetch(PDO::FETCH_ASSOC)) {
    $row['data_intervento']=$row['data_fine_visita'];
    $VISITE[]=$row;
}

if (count($VISITE)>0) {

} else {
    echo "Spiacente! Nessuna visita per questo cliente nel periodo ".$REPORT['data_inizio']." - ".$REPORT['data_fine'];
    exit;
}

//echo "<pre>";
//print_r($VISITE);
//echo "</pre>";

//ORA CHE HO LE VISITE, DEVO PREPARARE I GRAFICI IN BASE ALLE AREE (OVVERO AI SERVIZI) CHE ESTRAGGO DALLE ISPEZIONI
//INIZIO A PREPARARE I DATI

$totaleGrammi=array();
$grammiSingolaTrappola=60;
$consumoSingolaTrappola['Integra (0%)']=0;
$consumoSingolaTrappola['Pochissimo consumata (10%)']=$grammiSingolaTrappola/10;
$consumoSingolaTrappola['Lievemente consumata (25%)']=$grammiSingolaTrappola/4;
$consumoSingolaTrappola['Mediamente consumata (50%)']=$grammiSingolaTrappola/2;
$consumoSingolaTrappola['Molto consumata (75%)']=3*$grammiSingolaTrappola/4;
$consumoSingolaTrappola['Totalmente consumata (100%)']=$grammiSingolaTrappola;

$campoconteggioinsetti['ID']='conteggio_insetti_derrate';
$campoconteggioinsetti['IS']='conteggio_striscianti';
$campoconteggioinsetti['IV']='conteggio_volanti';
$campoconteggioinsetti['DA']='conteggio_roditori';

$nomeinsettogenerico['ID']='Insetti Derrate';
$nomeinsettogenerico['IS']='Blatte';
$nomeinsettogenerico['IV']='Insetti Volanti';
$nomeinsettogenerico['DA'] ='Roditori';


$titoloscheda['ID']="MONITORAGGIO/RILEVAMENTO INSETTI DERRATE ALIMENTARI ".$anno;
$titoloscheda['D'] ="MONITORAGGIO/CONTROLLO DERATTIZZAZIONE ".$anno;
$titoloscheda['DA']="MONITORAGGIO/CONTROLLO DERATTIZZAZIONE ATOSSICA ".$anno;
$titoloscheda['IV']="MONITORAGGIO/RILEVAMENTO INSETTI VOLANTI ".$anno;
$titoloscheda['IS']="MONITORAGGIO/RILEVAMENTO INSETTI STRISCIANTI ".$anno;

$totaleCatture=array();
$nomearea=array();
$nomeinsetto=array();

$servizireport=array();
$areecatture=array();


foreach ($VISITE as $V) :

    $insetti=array();

    //ora vediamo quali schede vanno preparate in base alle aree e ai servizi interessati
    $query="SELECT *,pcs1.nome_infestante as nome_infestante1,pcs2.nome_infestante as nome_infestante2,pcs3.nome_infestante as nome_infestante3,pcs4.nome_infestante as nome_infestante4 
FROM `pcs_ispezioni` 
join pcs_visite on pcs_visite.codice_visita=pcs_ispezioni.codice_visita 
join pcs_sedi_clienti on pcs_sedi_clienti.id=pcs_visite.id_sede 
join pcs_postazioni on pcs_postazioni.codice_postazione=pcs_ispezioni.codice_postazione 
join pcs_aree on pcs_aree.id_sede=pcs_sedi_clienti.id and pcs_aree.id=pcs_postazioni.id_area 
join pcs_tipi_servizio ON pcs_tipi_servizio.id=pcs_aree.Servizio 
LEFT JOIN pcs_infestanti pcs1 ON pcs1.id_infestante=pcs_aree.Infestante1
LEFT JOIN pcs_infestanti pcs2 ON pcs2.id_infestante=pcs_aree.Infestante2
LEFT JOIN pcs_infestanti pcs3 ON pcs3.id_infestante=pcs_aree.Infestante3
LEFT JOIN pcs_infestanti pcs4 ON pcs4.id_infestante=pcs_aree.Infestante4
WHERE pcs_ispezioni.codice_visita='".$V['codice_visita']."' order by pcs_aree.id,pcs_aree.Servizio,nome ";
    $stmt=$dbh->query($query);
    $SERVIZI=array();

    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['data_intervento']=$row['data_fine_visita'];
        $SERVIZI[$row['descrizione_servizio']][$row['id_area']][]=$row;
    }

    foreach ($SERVIZI as $scheda=>$arrayserv) :
        //i servizi

        foreach ($arrayserv as $idarea=>$servmaster) :
            //le aree (spesso coincidono con i servizi, ma potrebbero esserci più aree per lo stesso servizio)

            foreach ($servmaster as $serv) :

                $totaleinsetti=0;
                $insetti=array();
                //le ispezioni una per una

                //echo "<pre>";
                //print_r($serv);
                //echo "</pre>";

                //(i) servizio=='D'
                if ($serv['servizio']=='D') :
                    $nomearea['Area'.$idarea]=$serv['Area'];
                    $totaleGrammi[$serv['data_intervento']]['Area'.$idarea][$serv['prodotto']]['totale']   +=$grammiSingolaTrappola;
                    $totaleGrammi[$serv['data_intervento']]['Area'.$idarea][$serv['prodotto']]['consumo']  +=$consumoSingolaTrappola[$serv['stato_esca_roditori']];
                endif;

                if ($serv['servizio']!='D') :

                    $nomearea['Area'.$idarea]=$serv['Area'];
                    $nomeinsetto['Area'.$idarea]=$nomeinsettogenerico[$serv['servizio']];

                    if ($serv['Infestante1']>0) {
                        if ($serv['conteggio_infestante_1']>0) {
                            $totaleinsetti+=$serv['conteggio_infestante_1'];
                        } else {
                            $serv['conteggio_infestante_1']=0;
                        }
                        $insetti[]=$serv['nome_infestante1'].": ".$serv['conteggio_infestante_1'];
                    }
                    if ($serv['Infestante2']>0) {
                        if ($serv['conteggio_infestante_2']>0) {
                            $totaleinsetti+=$serv['conteggio_infestante_2'];
                        } else {
                            $serv['conteggio_infestante_2']=0;
                        }
                        $insetti[]=$serv['nome_infestante2'].": ".$serv['conteggio_infestante_2'];
                    }
                    if ($serv['Infestante3']>0) {
                        if ($serv['conteggio_infestante_3']>0) {
                            $totaleinsetti+=$serv['conteggio_infestante_3'];
                        } else {
                            $serv['conteggio_infestante_3']=0;
                        }
                        $insetti[]=$serv['nome_infestante3'].": ".$serv['conteggio_infestante_3'];
                    }
                    if ($serv['Infestante4']>0) {
                        if ($serv['conteggio_infestante_4']>0) {
                            $totaleinsetti+=$serv['conteggio_infestante_4'];
                        } else {
                            $serv['conteggio_infestante_4']=0;
                        }
                        $insetti[]=$serv['nome_infestante4'].": ".$serv['conteggio_infestante_4'];
                    }
                    if (count($insetti)>0) {
                        //ci sono indicati i dettagli degli insetti
                        //allore metto nella colonna Fauna, tutti gli insetti con accanto il loro conteggio
                        //e nella colonna destra il totale postazione
                    } else {

                        //altrimenti indico soltanto la fauna "generica" e a destra il totale postazione
                        $totaleinsetti=sprintf("%d",$serv[$campoconteggioinsetti[$serv['servizio']]]);
                        $insetti[]=$nomeinsettogenerico[$serv['servizio']].": ".$totaleinsetti;
                    }
                    $stringainsetti=join("\n",$insetti);
                    //$catture[$serv['data_intervento']][$idarea]+=;
                    $totaleCatture[$serv['data_intervento']][$serv['servizio']]['Area'.$idarea]+=$totaleinsetti;
                endif;

            endforeach; //servmaster as $serv
        endforeach; //$arrayserv as $servmaster

    endforeach; //SERVIZI

endforeach; //$VISITE as $V


//OK, ora sulla derattizzazione ho il totale grammi
echo "<!--<pre>TOTALE GRAMMI<br/>";
print_r($totaleGrammi);
echo "</pre>";
echo "<pre>CONSUMO GRAMMI<br/>";
print_r($consumoGrammi);
echo "</pre>";

echo "<pre>CATTURE<br/>";
print_r($totaleCatture);
echo "</pre>-->";



$seriephp=array();
$serielegendphp=array();

$seriephpcatture=array();
$serielegendephpcatture=array();

if (count($totaleGrammi)>0) {
    foreach ($totaleGrammi as $data=>$tmp) {
        foreach ($tmp as $area=>$tmp2) {
            $aree[$area]=1; //segno le aree
            foreach ($tmp2 as $nomeprod=>$val) {
                $nomiprod["$nomeprod"]=1;
            }
        }
    }
    $elencoaree=array_keys($aree);
    $elencoprod=array_keys($nomiprod);
}

if (count($totaleCatture)>0) {
    foreach ($totaleCatture as $data=>$tmp0) {
        foreach ($tmp0 as $serv=>$tmp) {
            $servizireport[$serv]=1;
            foreach ($tmp as $area=>$tmp2) {
                $areecatture[$area]=1; //segno le aree
                $seriephpcatture[$serv][$area]=1;
            }
        }
    }
}
$elencoservizi=array_keys($servizireport);
$elencoareecatture=array_keys($areecatture);

?>

<?php include("INC_10_HEADER.php");?>
<?php include("INC_15_SCRIPTS.php");?>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<?php include("INC_20_NAVBAR.php");?>
<div class="container" id="firstcontainer">
    <!-- Example row of columns -->

    <?php if (count($totaleGrammi)>0) : ?>

    <div class="row">
        <div class="col-xs-12">
            <div id="chartD" style="height:96%; width:96%;"></div>
        </div>
    </div>
        <div class="row" style="display:none;">
            <div class="col-xs-12">
                <img id="imgchartD" class="jqplot-target-img img-responsive">
            </div>
        </div>

    <?php endif; ?>

    <?php if (count($elencoservizi)>0) : ?>
    <?php foreach ($elencoservizi as $serv) : ?>
            <div class="row">
                <div class="col-xs-12">
                    <div id="chart<?php echo $serv;?>" style="height:96%; width:96%;margin-top:30px;"></div>
                </div>
            </div>
            <div class="row" style="display:none;">
                <div class="col-xs-12">
                    <img id="imgchart<?php echo $serv;?>" class="jqplot-target-img img-responsive">
                </div>
            </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <!--
        <div class="row">
            <div class="col-xs-12">
                <img id="imgChart1" class="img-responsive">
            </div>
        </div>
    -->
    <hr>
    <script>
        $(document).ready(function(){

            $.jqplot.config.enablePlugins = true;

            //--------------------------------------------------------------------------------------------------------
            //(i) Grafico consumo esca / totale esca
            //--------------------------------------------------------------------------------------------------------
            <?php if (count($totaleGrammi)>0) :  ?>

            var ticks=[];
            var serie=[];
            var legenda=[];

            //ora definisco subito le serie, in base a elencoaree e elencoprod
            <?php foreach ($elencoaree as $area) : ?>
                serie['<?php echo $area;?>']=[];
                <?php foreach ($elencoprod as $nomeprod) : ?>
                serie['<?php echo $area;?>']['<?php echo $nomeprod;?>']=[];
            <?php $seriephp[]="serie['".$area."']['".$nomeprod."']"; ?>
            <?php $serielegendphp[]=$nomearea[$area]." - ".$nomeprod; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

            //devo avere subito tutte le chiavi per generare le serie, altrimenti si rischia di saltare

            <?php foreach ($totaleGrammi as $data=>$valore) : ?>
                ticks.push('<?php echo $data;?>');
                //ora riempio le serie di dati, tanto so già quali legende ho, se il dato non esiste, metto 0
                <?php foreach ($elencoaree as $area) : ?>
                    <?php foreach ($elencoprod as $nomeprod) : ?>
                        var valore=0;
                        <?php if ($valore[$area][$nomeprod]['totale']>0) { ?>
                            //valore=<?php echo 100*$valore[$area][$nomeprod]['consumo']/$valore[$area][$nomeprod]['totale']; ?>;
                            valore=<?php echo $valore[$area][$nomeprod]['consumo']; ?>;
                        <?php } ?>
                        serie['<?php echo $area;?>']['<?php echo $nomeprod;?>'].push(valore);
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>

            //console.log(serie);

            var plot1 = $.jqplot('chartD', [<?php echo join(",",$seriephp);?>], {
                // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
                title: '<?php echo $titoloscheda['D'];?>  Consumo totale grammi esca',
                animate: !$.jqplot.use_excanvas,
                seriesDefaults:{
                    renderer:$.jqplot.BarRenderer,
                    pointLabels: { show: true }
                },
                series:[
                    <?php foreach ($serielegendphp as $s) : ?>
                    {label:'<?php echo $s;?>'},
                    <?php endforeach; ?>
                ],
                legend: {
                    show: true,
                    placement: 'outsideGrid',
                    location: 'e'
                },

                axesDefaults: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
                },
                axes: {
                    yaxis: {
                        padMax: 1.5
                    },
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        ticks: ticks,
                        tickOptions: {
                            angle: -90
                        }
                    }
                },
                highlighter: { show: false }
            });


            var imgData=$("#chartD").jqplotToImageStr({});
            $("#imgchartD").attr('src',imgData);

        <?php endif; ?>
            //--------------------------------------------------------------------------------------------------------
            //(f) Grafico consumo esca / totale esca
            //--------------------------------------------------------------------------------------------------------


            //--------------------------------------------------------------------------------------------------------
            //(i) Grafici per le catture, uno per ogni servizio
            //--------------------------------------------------------------------------------------------------------


            <?php if (count($elencoservizi)>0) : ?>
            var ticks=[];
            var serie=[];

                <?php foreach ($seriephpcatture as $serv=>$tmp0) : ?>
                    serie['<?php echo $serv;?>']=[];
                    <?php
                    foreach ($tmp0 as $area=>$tmp) :
                    ?>
                            serie['<?php echo $serv;?>']['<?php echo $area;?>']=[];
                        <?php
            $serielegendphpcatture[$serv][]=$nomearea[$area]." - ".$nomeinsetto[$area];
            $seriephpcatture2[$serv][]="serie['".$serv."']['".$area."']";

                    endforeach;
                    ?>
                <?php endforeach; ?>

                <?php foreach ($totaleCatture as $data=>$valore) : ?>
                ticks.push('<?php echo $data;?>');
                //ora riempio le serie di dati, tanto so già quali legende ho, se il dato non esiste, metto 0

                    <?php foreach ($valore as $serv=>$tmp) : ?>
                        <?php foreach ($tmp as $area=>$val) : ?>

                        var valore=0;
                        <?php if ($valore[$serv][$area]>0) { ?> valore=<?php echo $valore[$serv][$area]; ?>;<?php } ?>

                        serie['<?php echo $serv;?>']['<?php echo $area;?>'].push(valore);

                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>


            <?php endif; ?>


            //qui ho ormai costruito tutte le serie, esempio
//            serie['DA']['Magazzino']
//            serie['IS']['Magazzino']

            //ora rifaccio il ciclo sui servizi e faccio i grafici, uno per servizio

            <?php foreach ($elencoservizi as $serv) : ?>
            var plot<?php echo $serv;?> = $.jqplot('chart<?php echo $serv;?>', [<?php echo join(",",$seriephpcatture2[$serv]);?>], {
                // Only animate if we're not using excanvas (not in IE 7 or IE 8)..

                <?php if ($serv=='IS') { ?>
                seriesColors: ["#FFFF00"],
                <?php } ?>

                title: '<?php echo $titoloscheda[$serv];?> Totale Catture',
                animate: !$.jqplot.use_excanvas,
                seriesDefaults:{
                    renderer:$.jqplot.BarRenderer,
                    pointLabels: { show: true }
                },
                series:[
                    <?php foreach ($serielegendphpcatture[$serv] as $s) : ?>
                    {label:'<?php echo $s;?>'},
                    <?php endforeach; ?>
                ],
                legend: {
                    show: true,
                    placement: 'outsideGrid',
                    location: 'e'
                },

                axesDefaults: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
                },
                axes: {
                    yaxis: {
                        padMax: 1.5
                    },
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer,
                        ticks: ticks,
                        tickOptions: {
                            angle: -90
                        }
                    }
                },
                highlighter: { show: false }
            });

            var imgData=$("#chart<?php echo $serv;?>").jqplotToImageStr({});
            $("#imgchart<?php echo $serv;?>").attr('src',imgData);


        <?php endforeach; ?>

            //--------------------------------------------------------------------------------------------------------
            //(f) Grafici per le catture, uno per ogni servizio
            //--------------------------------------------------------------------------------------------------------

        });
    </script>
    <script>


        $(window).load(function() {

            function serializeData( data ) {
                // If this is not an object, defer to native stringification.
                if ( ! $.isPlainObject( data ) ) {
                    return( ( data == null ) ? "" : data.toString() );
                }
                var buffer = [];
                // Serialize each key in the object.
                for ( var name in data ) {
                    if ( ! data.hasOwnProperty( name ) ) {
                        continue;
                    }
                    var value = data[ name ];
                    buffer.push(
                        encodeURIComponent( name ) +
                        "=" +
                        encodeURIComponent( ( value == null ) ? "" : value )
                    );
                }
                // Serialize the buffer and clean it up for transportation.
                var source = buffer
                    .join( "&" )
                    .replace( /%20/g, "+" )
                ;
                return( source );
            }


            var totaleimmagini = $('.jqplot-target-img').length;

            function salvaimmagini(totaleimmagini) {
                var immaginisalvate=0;
                $('.jqplot-target-img').each(function(i, obj) {
                    var imageSrc = obj.src; // saves the base64 image uri in imageSrc
                    //console.log(imageSrc);


                    console.log(i);

                    var params={};
                    params.Value     =imageSrc;
                    params.idreport = '<?php echo $idreport;?>';
                    params.i =i;

                    var serialparams=serializeData(params);
                    //console.log(serialparams);

                    $.ajax({
                        type: "POST",
                        async: false,
                        url: 'saveImagesReports.php',
                        dataType: 'json',
                        data: serialparams,
                        success: function(data){
                            console.log(data);
                            if (data.result==true) {

                                immaginisalvate++;
/*
                                    $.notify({
                                        title: '<strong>Successo!</strong>',
                                        message: 'Immagine '+immaginisalvate+' di '+totaleimmagini+' salvata!'
                                    },{
                                        type: 'success'
                                    });
*/

                            } else {
                                console.log(data);
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                });


            }

            var invia=<?php echo $invia;?>;

            if (invia>0) {
                salvaimmagini(totaleimmagini);
                location.href="inviaPdfReport.php?idreport=<?php echo $idreport;?>";
            }
        });



    </script>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->
</body>
</html>
