<div class="container" id="firstcontainer">
    <br/><br/>
    <?php
    $idsede=$_GET['idsede'];
    $idmodulo=getModuloFrom_nome_modulo('Sedi Clienti');
    $idmodulo2=getModuloFrom_nome_modulo('MappeImpianto');
    if ($idsede>0) {

    } else {
        echo "Nessuna sede selezionata!";
        exit();
    }
    $sede=getElemento($idmodulo,$idsede);

    //controllo se sono loggato come cliente e se è possibile vedermi
    if ($_SESSION['pcs_id_cliente']>0) {
        if ($sede['id_cliente']==$_SESSION['pcs_id_cliente']) {

        } else {
            echo "Non hai il permesso di vedere questa mappa!";
            exit();
        }
    }

    $query2="SELECT * FROM pcs_mappeimpianto WHERE id_sede=$idsede";
    $stmt2=$dbh->prepare($query2);
    $stmt2->execute(array($idsede));
    $mappaimpianto=$stmt2->fetch(PDO::FETCH_ASSOC);


    $immaginemappa=getFiles($mappaimpianto['id'],'pcs_mappeimpianto','immagine',5);

    foreach ($immaginemappa as $chiave=>$im) :

        foreach ($lingue as $lang) :
        $testi=getTestiTraducibili("pcs_file",$im['id_file'],$lang);
        //if ($debug) { echo "<pre>"; print_r($testi); echo "</pre>"; }
        if (count($testi)>0) {
            foreach ($testi as $key=>$value) :
                $immaginemappa[$chiave][$key][$lang]=$value;
            endforeach;
        }
        endforeach;

    endforeach;

/*
    echo "<pre>";
    print_r($immaginemappa);
    echo "</pre>";
*/


    if ($immaginemappa[0]['file']=='') {
        $immaginemappa[0]['file']='mappaprova.png';
    }


    $query="SELECT pcs_postazioni.*,pcs_tipo_postazione.*,pcs_modello_postazione.*,pcs_prodotto_postazione.* FROM pcs_postazioni JOIN pcs_tipo_postazione ON pcs_tipo_postazione.id_tipo_postazione=pcs_postazioni.Tipo JOIN pcs_modello_postazione ON pcs_modello_postazione.id_modello_postazione=pcs_postazioni.Modello LEFT JOIN pcs_prodotto_postazione ON pcs_prodotto_postazione.id_prodotto_postazione=pcs_postazioni.Prodotto WHERE id_area IN (SELECT pcs_aree.id FROM pcs_aree WHERE pcs_aree.id_sede=?)";
    $stmt=$dbh->prepare($query);
    $stmt->execute(array($idsede));
    $trappole=array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $trappole[]=$row;
    }
    //print_r($trappole);

    ?>
    <div class="row" >
        <?php if ($_GET['indicemappa']=='') { $_GET['indicemappa']=0; } ?>
        <input type="hidden" name="indicemappa" id="indicemappa" value="<?php echo $_GET['indicemappa'];?>"/>
        <input type="hidden" name="idsede" id="idsede" value="<?php echo $_GET['idsede'];?>"/>
        <input type="hidden" name="scriptname" id="scriptname" value="https://<?php echo $_SERVER['SERVER_NAME'];?>/<?php echo $_SERVER['SCRIPT_NAME'];?>"/>

        <div class="col-xs-12 col-sm-8">
                <?php $i=0; $j=0; foreach ($trappole as $t) { $i++;
                    $tmp=json_decode($t['posizione_mappa_custom'],true);
                    if (count($tmp)>0) {

                    } else {
                        $j++; //almeno una trappola non è stata posizionata nelle mappe!
                    }
                        $datatop=80+($i-1)*35;
                        $dataleft=800;
                    ?>
                    <aside data-html="true" data-toggle="popover" title="<?php echo $t['nome'];?>" data-content="<b>TIPO:</b> <?php echo $t['tipo'];?><br/><b>MODELLO:</b> <?php echo $t['modello'];?><br/><b>PRODOTTO:</b> <?php echo $t['prodotto'];?>" dataleft="<?php echo $dataleft;?>" datatop="<?php echo $datatop;?>" style="left:<?php echo $dataleft;?>px;top:<?php echo $datatop;?>px;" id="d_<?php echo $t['codice_postazione'];?>" draggable="true" class="pop dragme trappole <?php echo $t['colore'];?>"><span><?php echo $t['nome'];?></span></aside>
                <?php } ?>
                <img id="div1" class="img-responsive" src="<?php echo $immaginemappa[$_GET['indicemappa']]['file'];?>" style="border:3px dotted black;" />
        </div>
        <div class="col-xs-12 col-sm-4">
            <div class="row">
                <div class="col-xs-12">
                    <div class="btn-group">

                        <?php $i=0;?>
                        <?php foreach ($immaginemappa as $im) : $i++;?>
                            <?php if ($indicemappa==$i-1) { $active='active'; } else { $active=''; } ?>
                            <button data-src="<?php echo $immaginemappa[$i-1]['file'];?>" data-attr="<?php echo $i-1;?>" type="button" class="<?php echo $active;?> btn btn-info cambiaimmaginemappa"><?php echo $i;?> (<?php echo $immaginemappa[$i-1]['nome']['it'];?>)</button>
                        <?php endforeach; ?>

                    </div>
                </div>
                <div class="col-xs-12">
                    <?php // if ($j>0) { //se almeno una trappola non è stata posizionata nelle mappe! ?>
                    <h3>Trappole</h3>
                        <div id="trappole" style="width:100%;"></div>
                    <?php // } ?>
                </div>
                <div class="col-xs-12">
                    <h3>Legenda</h3>
                    <?php $tipi=array();?>
                    <?php $i=0; foreach ($trappole as $t) { $i++;
                        if ($tipi[$t['tipo']]==1) continue;
                        $tipi[$t['tipo']]=1;
                        ?>
                        <p><a href="#" class="btn <?php echo $t['colore'];?>"> &nbsp;&nbsp;&nbsp; </a> (<?php echo $t['legenda'];?>)</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- /* http://jsfiddle.net/robertc/kKuqH/30/ */ -->

<script>
    //https://www.paulirish.com/2009/throttled-smartresize-jquery-event-handler/

    (function($,sr){

        // debouncing function from John Hann
        // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
        var debounce = function (func, threshold, execAsap) {
            var timeout;

            return function debounced () {
                var obj = this, args = arguments;
                function delayed () {
                    if (!execAsap)
                        func.apply(obj, args);
                    timeout = null;
                };

                if (timeout)
                    clearTimeout(timeout);
                else if (execAsap)
                    func.apply(obj, args);

                timeout = setTimeout(delayed, threshold || 100);
            };
        }
        // smartresize
        jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

    })(jQuery,'smartresize');

    jQuery(function($) {
        $('[data-toggle="popover"]').popover({html:true});

        //$('.pop').popover().click(function () {
        //    setTimeout(function () {
        //        $('.pop').popover('hide');
        //    }, 5000);
        //});

        $(".cambiaimmaginemappa").click(function(){
            var i=$(this).attr("data-attr");
            var src=$(this).attr("data-src");
            $("#div1").attr("src",src);
            $("#indicemappa").val(i);
            inizializzazione(i);
            $(".cambiaimmaginemappa").removeClass("active");
            $(this).addClass("active");
        })

        $(window).smartresize(function(){
            console.log("Sto facendo resize");
            var url=$("#scriptname").val();
            var url=url+"?idsede="+$("#idsede").val()+"&indicemappa="+$("#indicemappa").val();
            window.location.href = url;
        });

        //inizializzazione delle trappole
        var indicemappa=0;
        <?php if ($_GET['indicemappa']!=0) { ?>
            indicemappa=<?php echo $_GET['indicemappa'];?>;
        <?php } ?>

        function inizializzazione(indicemappa) {

            var dimtrappola=25;
            var spaziotrappola=30;

            var xmax=parseInt($("#div1").width());
            var ymax=parseInt($("#div1").height());

            var xtrappole=parseInt($("#trappole").width());

            var offsetDiv1=$("#div1").offset();

            var offsetTrappole=$("#trappole").offset();
            //console.log("offset div1");
            //console.log(offsetDiv1);
            console.log("offset trappole");
            console.log(offsetTrappole);

            //ora devo sistemare le trappole, diciamo che le metto in righe
            var trappoleperriga=parseInt(xtrappole/spaziotrappola); //25 + 5 di spazio
            var tottrappole=<?php echo $j;?>;
            var righenecessarie=tottrappole/trappoleperriga;
            var altezzadivtrappole=righenecessarie*spaziotrappola+spaziotrappola;

            $("#trappole").css("height",altezzadivtrappole);

            <?php

            $i=0;
            $j=0;
            foreach ($trappole as $t) :
            $i++;
            $tmp=json_decode($t['posizione_mappa_custom'],true);
            if (count($tmp)>0) {
                $datatop=$tmp['y'];
                $dataleft=$tmp['x'];
                $indicemappa=$tmp['indicemappa'];
            } else {
                $j++;
                $indicemappa=-1;
                $datatop=0;
                $dataleft=0;
            }
            ?>
            var datalefttmp=parseInt(<?php echo $dataleft;?>);
            var datatoptmp=parseInt(<?php echo $datatop;?>);
            var indicemappatrappola=parseInt(<?php echo $indicemappa;?>);

            <?php if ($tmp['xmax']>0 && $tmp['ymax']>0) { ?>
            var dataleft=datalefttmp*xmax/<?php echo $tmp['xmax'];?>;
            var datatop=datatoptmp*ymax/<?php echo $tmp['ymax'];?>;
            <?php } else { //vanno messe le trappole in righe... in base a $j e ai vari posizionamenti ?>
            j=parseInt(<?php echo $j-1;?>);
            var riga=parseInt(j/trappoleperriga);
            var colonna=j-riga*trappoleperriga;



            // in base a $j (che parte da 1) devo calcolare dataleft e datatop

            var dataleft=parseInt(offsetTrappole.left)-parseInt(offsetDiv1.left)+spaziotrappola*colonna+15
            var datatop=parseInt(offsetTrappole.top)-parseInt(offsetDiv1.top)+riga*spaziotrappola+5;
            <?php }?>

            //console.log("Trappola <?php echo $i;?>");
            //console.log(dataleft);
            //console.log(datatop);

            //visualizzo solo le trappole sull'immagine giusta

            if (indicemappatrappola>=0) {
                if (indicemappatrappola==indicemappa) {
                    $("#d_<?php echo $t['codice_postazione'];?>").show();
                    $("#d_<?php echo $t['codice_postazione'];?>").css("left",dataleft);
                    $("#d_<?php echo $t['codice_postazione'];?>").css("top",datatop);
                } else {
                    $("#d_<?php echo $t['codice_postazione'];?>").hide();
                }
            } else {
                $("#d_<?php echo $t['codice_postazione'];?>").show();
                $("#d_<?php echo $t['codice_postazione'];?>").css("left",dataleft);
                $("#d_<?php echo $t['codice_postazione'];?>").css("top",datatop);
            }

            <?php endforeach; //end foreach ?>
        }


        inizializzazione(<?php echo $_GET['indicemappa'];?>);

        //no resize
        var size = [window.width,window.height];  //public variable

        $(window).resize(function(){
            window.resizeTo(size[0],size[1]);
        });

        var offsetdiv1=$("#div1").offset();
        var widthdiv1=$("#div1").width();
        var heightdiv1=$("#div1").height();

        //inizializzazione posizione
    var offset_data;
    var idtrappola;
    //Global variable as Chrome doesn't allow access to event.dataTransfer in dragover

    function drag_start(event) {
        var elementid=event.toElement.id;
        var style = window.getComputedStyle(event.target, null);
        offset_data = (parseInt(style.getPropertyValue("left"),10) - event.clientX) + ',' + (parseInt(style.getPropertyValue("top"),10) - event.clientY);
        event.dataTransfer.setData("text/plain",offset_data);
        idtrappola=elementid;
    }
    function drag_over(event) {
        var offset;
        try {
            offset = event.dataTransfer.getData("text/plain").split(',');
        }
        catch(e) {
            offset = offset_data.split(',');
        }
        console.log(idtrappola);
        var dm = document.getElementById(idtrappola);
        dm.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
        dm.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';
        event.preventDefault();
        return false;
    }
    function drop(event) {
        var offset;
        try {
            offset = event.dataTransfer.getData("text/plain").split(',');
        }
        catch(e) {
            offset = offset_data.split(',');
        }

        //se sto facendo il drop "fuori", allora rimetto la posizione iniziale

        var x=event.clientX + parseInt(offset[0],10);
        var y=event.clientY + parseInt(offset[1],10);
        var offsetdiv1=$("#div1").offset();
        var xmax=parseInt($("#div1").width());
        var ymax=parseInt($("#div1").height());

        console.log("x="+x);
        console.log("y="+y);

        //controllo se è dentro il div
        //le coordinate x e y sono già relative al div
        //basta verificare che siano >0 e che siano non troppo a destra o troppo in basso


        console.log("xmax="+xmax);
        console.log("ymax="+ymax);

        console.log(offsetdiv1);

        if (x>0 && x<xmax) {
            if (y>0 && y<ymax) {
                //alert("x e y a posto!");
                //allora li posso salvare nella nuova posizione!!!
                var dm = document.getElementById(idtrappola);

                var codicepostazione=idtrappola.substring(2, idtrappola.length);

                var indicemappa=$("#indicemappa").val();

                dm.style.left = (event.clientX + parseInt(offset[0],10)) + 'px';
                dm.style.top = (event.clientY + parseInt(offset[1],10)) + 'px';

                $.post("ajax_scriviPosizioneTrappolaMappaCustom.php", { indicemappa:indicemappa, codice_postazione: codicepostazione, x:x, y:y, xmax:xmax, ymax:ymax }, function(data){
                    console.log(data);
                    var url=$("#scriptname").val();
                    var url=url+"?idsede="+$("#idsede").val()+"&indicemappa="+$("#indicemappa").val();
                    window.location.href = url;
                });

            } else {
                //alert("x dentro ma y fuori!");
                var codicepostazione=idtrappola.substring(2, idtrappola.length);

                $.post("ajax_scriviPosizioneTrappolaMappaCustom.php", { codice_postazione: codicepostazione, x:-1, y:-1, xmax:xmax, ymax:ymax }, function(data){
                    console.log(data);
                    var url=$("#scriptname").val();
                    var url=url+"?idsede="+$("#idsede").val()+"&indicemappa="+$("#indicemappa").val();
                    window.location.href = url;

                });
            }
        } else {

            var codicepostazione=idtrappola.substring(2, idtrappola.length);

            $.post("ajax_scriviPosizioneTrappolaMappaCustom.php", { codice_postazione: codicepostazione, x:-1, y:-1, xmax:xmax, ymax:ymax }, function(data){
                console.log(data);
                var url=$("#scriptname").val();
                var url=url+"?idsede="+$("#idsede").val()+"&indicemappa="+$("#indicemappa").val();
                window.location.href = url;
            });

            //alert("x fuori");
        }

        event.preventDefault();
        return false;
    }
    $(".dragme").each(function(){
        var dm=document.getElementById($(this).attr('id'));
        dm.addEventListener('dragstart',drag_start,false);
    });
    document.body.addEventListener('dragover',drag_over,false);
    document.body.addEventListener('drop',drop,false);

    });

</script>
<?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->