<?php $pagina="clienti";?>
<?php include("INC_10_HEADER.php");?>
<?php include("INC_15_SCRIPTS.php");?>
<body>
<?php include("INC_20_NAVBAR.php");?>
<div class="container" id="firstcontainer">
    <?php $_GET[id]=getModuloFrom_nome_modulo('Clienti');?>
    <?php $modulo=getModulo($_GET[id]);?>
    <?php $pars=$_GET['p']; ?>
<?php    $permessi_modulo=permessi($_GET['id'],$utente['id_ruolo'],$superuserOverride); ?>
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a>
            </li>
            <li>
                <?php echo _($modulo['nome_modulo']);?>
            </li>
        </ul>
    </div>

    <div class="page-header">
        <h2>
            <i class="<?php echo $modulo['font_icon'];?>"></i> <?php echo _($modulo['nome_modulo']);?>
            <div class="pull-right">
                <?php if ($permessi_modulo['Can_delete']=='si') { ?>
                    <a id="multiplerowdelete" class="tooltip-danger btn btn-app btn-danger btn-xs" style="display:none;"
                       data-rel="tooltip" data-placement="left" title="Delete all selected items" >
                        <i class="glyphicon glyphicon-trash bigger-160"></i>
                    </a>
                <?php } ?>
                <?php
                if ($modulo['aprimodal'] == 'si') {
                    if (($permessi_modulo['Can_create'] == 'si') && ($modulo['abilita_bottone_new'] == 'si')) { ?>
                        <a class="tooltip-success btn btn-app btn-success btn-xs aprimodal-ele"
                           idmodalmod="<?php echo $modulo['id_modulo']; ?>" idmodalele="-1"
                           data-rel="tooltip" data-placement="left"
                           title="Add element of<?php echo _($modulo[nome_modulo]); ?>">
                            <span style="font-size:2.5em;" class="glyphicon glyphicon-plus"></span>
                        </a>
                    <?php } ?>
                <?php } else {
                    if (($permessi_modulo['Can_create'] == 'si') && ($modulo['abilita_bottone_new'] == 'si')) { ?>
                        <a
                                href="get_element.php?debug=<?php echo $_GET['debug']; ?>&idmod=<?php echo $modulo['id_modulo']; ?>&idele=-1"
                                class="tooltip-success btn btn-app btn-success btn-xs"
                                data-rel="tooltip" data-placement="left"
                                title="Add element of<?php echo $modulo['nome_modulo']; ?>">
                            <i class="ace-icon glyphicon glyphicon-plus bigger-160"></i>
                        </a>
                    <?php }
                }
                ?>

                <?php if ($permessi_modulo['Can_update']=='si') {
                    foreach ($_GET as $key=>$value) {
                        if ($key=="reordering") continue;
                        $get[]=$key."=".$value;
                    }
                    $querystring=join("&",$get);
                    $url="http://".$_SERVER[HTTP_HOST].$_SERVER[SCRIPT_NAME]."?".$querystring;
                    ?>
                <?php } ?>
            </div>
        </h2>

    </div><!-- /.page-header -->

    <?php //* * * * * * * * * * * * * * * * (i) modal allegati elemento  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>
    <div id="ModalAllegati" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger">Allegati</h4>
                </div>

                <div id="modal-body-ModalAllegati" class="modal-body">
                    LOADING...
                </div>

            </div>
        </div>
    </div><!-- PAGE CONTENT ENDS -->

    <?php //* * * * * * * * * * * * * * * * (f) modal allegati elemento  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>


    <!-- (i) TABELLA CLIENTI -->

            <table id="clienti" class="table table-bordered table-hover display nowrap margin-top-10 bootstrap-datatable datatable responsive" cellspacing="0" width="100%">
                <thead>
                <tr>
                    <th>Actions_Allegati</th>
                    <th>ID</th>
                    <th>Cognome/Rag.Sociale</th>
                    <th>Nome</th>
                    <th>Indirizzo</th>
                    <th>CAP</th>
                    <th>NomeAzienda</th>
                    <th>TelLavoro</th>
                    <th>Cell</th>
                    <th>Referenti</th>
                </tr>
                </thead>
            </table>

    <!-- (f) TABELLA CLIENTI -->
    <!-- custom script -->
<script>
    $(document).ready(function() {


        //allegati modal
        $(document).on("click",".aprimodal-allegati",function(){
            var idele=$(this).attr("idmodalele");
            var idmod=$(this).attr("idmodalmod");
            var view=$(this).attr("view");
            $("#ModalAllegati").modal({show:true});



            $('#modal-body-ModalAllegati').load('ajax_getallegati.php?'+ $.param({
                backurl: 'http://<?php echo $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];?>',
                idmod: idmod,
                view: view,
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

            }); //fine $('#modal-body-ModalAllegati').load('ajax_getallegati.php?'+ $.param({
        });




        //(i) ---------------DATATABLE---------------DATATABLE---------------DATATABLE---------------DATATABLE---------------

        $('#clienti').DataTable( {
            serverSide: true,
            "iDisplayLength": 50,
            ajax: "server_side_clienti.php?<?php echo $_SERVER['QUERY_STRING'];?>"
        } );

        //(f) ---------------DATATABLE---------------DATATABLE---------------DATATABLE---------------DATATABLE---------------

        //dettagli modal
        $(document).on("click",".aprimodal-dettagli",function(){
            var idcliente=$(this).attr("idcliente");
            $("#ModalDetails").modal({show:true});

            $('#modal-body-ModalDetails').load('ajax_getmodulocreasede.php?'+ $.param({
                backurl: 'http://<?php echo $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];?>',
                idcliente: idcliente}),function(result){

                $('.chosen-select').chosen({disable_search_threshold: 10});

            }); //fine $('#modal-body-ModalDettagli').load('ajax_getmodulocopiaaree.php?'+ $.param({
        });


        //copia aree da una sede ad un'altra
        $(document).on("click","#creasedesubmit",function(){
            var params={};
            params.nomesede=$("#nomesede").val();
            params.idcliente=$("#idclientetmp").val();
            console.log(params);
            $("#ModalDetails").hide();

            $.ajax({
                dataType: "json",
                type: 'POST',
                url: "ajax_crea_sede.php",
                data: jQuery.param(params) ,
                success: function (data) {
                    console.log(data);
                    if (data.result==true) {
                        $.notify({
                            title: '<strong>Successo!</strong>',
                            message: 'Sede creata.'
                        },{
                            type: 'success'
                        });
                        //var url='get_element.php?debug=0&modname=Polizze&idele='+data.idpolizza;
                        //setTimeout(function(){$(location).attr('href',url);}, 100);
                    } else {
                        $.notify({
                            title: '<strong>ERRORE!</strong>',
                            message: 'Sede non creata.'
                        },{
                            type: 'danger'
                        });
                    }
                },
                error: function (e) {
                    console.log(e);
                    $.notify({
                        title: '<strong>ERRORE!</strong>',
                        message: 'Problema di connessione.'
                    },{
                        type: 'danger'
                    });
                }
            });

            //poi devo fare un refresh della pagina
            setTimeout(function(){
                location.reload();
            }, 1000);

        });

        $(document).on("click",".delete-elemento",function(){
            var idele=$(this).attr("idmodalele");
            var idmod=$(this).attr("idmodalmod");
            bootbox.confirm("<?php echo _('Sicuro di voler eliminare questo elemento?');?>", function(result) {
                if (result) {
                    $.post("ajax_delete_elemento.php", { idmod: idmod, idele: idele } , function(msg){$("#responso").html(msg);} );
                    setTimeout(function(){location.reload();}, 2000);
                }
            });
        });



    } );
</script>
<hr>
<?php include("INC_90_FOOTER.php");?>

</body>
</html>
