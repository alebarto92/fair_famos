<div class="container" id="firstcontainer">
    <!-- Example row of columns -->
    <div class="row">
        <?php if ($_GET['id']>0) { ?>
    <?php $modulo=getModulo($_GET['id']);?>
<?php } ?>
<?php if ($_GET['modname']) { ?>
    <?php $_GET['id']=getModuloFrom_nome_modulo($_GET['modname']);?>
    <?php $modulo=getModulo($_GET[id]);?>
<?php } ?>
<?php $bStateSave=true;?>
<?php if ($_GET['azzera']==1) $bStateSave=false;?>
<?php $pars=$_GET['p']; ?>
<?php $filtridate=$_GET['fd'];?>
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
<!--<div class="pull-right"><a class="btn btn-danger">PULISCI</a></div>-->

<?php
//proteggiamoci da sguardi indiscreti!
if ($_GET[debug]!='VIACOLDEBUG') $_GET[debug]=0;
?>

<?php /* (i) ------------------------------ elenco del singolo modulo --------------------------------------------------------------------------------------   */ ?>
<?php if ($_GET['id']>0) :
//$query="SHOW FULL COLUMNS FROM ".$modulo['nome_tabella'];
//echo $query;
//$res=mysql_query($query);

$permessi_modulo=permessi($_GET['id'],$utente['id_ruolo'],$superuserOverride);
$campi_non_mostrati_in_tabella=explode(",",$modulo['campi_non_mostrati_in_tabella']);
$campi_testo_in_lingua=explode(",",$modulo['campi_testo_in_lingua']);
$campi_hidden_xs=explode(",",$modulo['campi_hidden_xs']);
$campi_hidden_sm=explode(",",$modulo['campi_hidden_sm']);
    $campi_solo_xls=explode(",",$modulo['campi_solo_xls']);
    $campi_non_xls=explode(",",$modulo['campi_non_xls']);
$campi_readonly=explode(",",$modulo['campi_readonly']);

$espressione_per_chiave_primaria=$modulo['espressione_per_chiave_primaria'];

if ($modulo['add_column']) :
    $addcolumn=json_decode($modulo['add_column'],true);
endif;

    if ($_GET[debug]) print_r($addcolumn);

if ($modulo['query']) {
    $query=$modulo['query'];
} else {
    $query="SELECT $espressione_per_chiave_primaria,".$modulo['nome_tabella'].".* FROM ".$modulo['nome_tabella']." order by ordine";
}

$nuovaquery=str_replace("%id_user%",$utente['id_user'],$query);
$nuovaquery=str_replace("%id_cliente%","'".$utente['id_cliente']."'",$nuovaquery);
$canreadall=$permessi_modulo['Can_read_all']=='si' ? 1 : 0;
$nuovaquery=str_replace("%can_read_all%",$canreadall,$nuovaquery);
$nuovaquery=str_replace("%defaultLang%",$lang,$nuovaquery);
$nuovaquery=str_replace("%lang%",$lang,$nuovaquery);

    //filtri passati da $_GET['p']
    if (count($pars)>0) {
        foreach ($pars as $key=>$val) {
            $cond[]=$modulo['nome_tabella'].".$key='".stripslashes($val)."'";
        }
        $where="(".join(" AND ",$cond).")";
        //ora nella query devo inserire queste condizioni aggiuntive
        $nuovaquery=str_replace("%wherefiltropar%","$where ",$nuovaquery);
    } else {
        $nuovaquery=str_replace("%wherefiltropar%","10=10 ",$nuovaquery);
    }

    //filtri passati tramite filtro campo data
    if (count($filtridate)>0) {
        foreach ($filtridate as $key=>$val) {
            list($da,$a)=explode("|",$val);
            list($d1,$m1,$y1)=explode("/",$da);
            $datada=$y1."-".$m1."-".$d1." 00:00";
            list($d2,$m2,$y2)=explode("/",$a);
            $dataa=$y2."-".$m2."-".$d2." 23:59";
            $datacond[]=$modulo['nome_tabella'].".$key>='$datada' AND ".$modulo['nome_tabella'].".$key<='$dataa'";
        }
        $where="(".join(" AND ",$datacond).")";
        //ora nella query devo inserire queste condizioni aggiuntive
        $nuovaquery=str_replace("%wherefiltrodate%","$where ",$nuovaquery);
    } else {
        $nuovaquery=str_replace("%wherefiltrodate%","10=10 ",$nuovaquery);
    }

if ($_GET[debug]) echo $nuovaquery;

    $nuovaquery.=" LIMIT 0,1";
    $stmt=$dbh->query($nuovaquery);
    $righe = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $numero_records=1;
//$righe=array();
//if ($stmt=$dbh->query($nuovaquery)) {
//    $numero_records=$stmt->rowCount();
//    $righe = $stmt->fetchAll(PDO::FETCH_ASSOC);
//} else {
//    setNotificheCRUD("admWeb","ERROR","module.php",$nuovaquery);
//}
?>

    <div class="page-header">
        <h2>
            <i class="<?php echo $modulo['font_icon'];?>"></i> <?php echo _($modulo['nome_modulo']);?> <!--<span class="badge"><?php echo $numero_records;?></span>-->
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

                if ($modulo['nome_modulo']=='AttivitaClean') { ?>
                  <a id="azzeraattivita"
                     class="tooltip-danger btn btn-app btn-danger btn-xs"
                     data-rel="tooltip" data-placement="left"
                     title="Azzera Attività Passate">
                      <i class="fa fa-trash-alt"></i>
                  </a>
                <?php } ?>

                <?php if ($permessi_modulo['Can_update']=='si') {
                    foreach ($_GET as $key=>$value) {
                        if ($key=="reordering") continue;
                        $get[]=$key."=".$value;
                    }
                    if ($_GET['reordering']==1) { } else { $get[]="reordering=1"; }
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

    <?php //* * * * * * * * * * * * * * * * (i) modal dettagli TIMBRATURE  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>
    <div id="ModalDetailsTimbrature" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger">TIMBRATURE</h4>
                </div>

                <div id="modal-body-ModalDetailsTimbrature" class="modal-body">
                    LOADING...
                </div>

            </div>
        </div>
    </div><!-- PAGE CONTENT ENDS -->

    <?php //* * * * * * * * * * * * * * * * (f) modal dettagli TIMBRATURE  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>


    <?php //* * * * * * * * * * * * * * * * (i) modal dettagli elemento  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>
    <div id="ModalDetails" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger">COPIA SERVIZI</h4>
                </div>

                <div id="modal-body-ModalDetails" class="modal-body">
                    LOADING...
                </div>

            </div>
        </div>
    </div><!-- PAGE CONTENT ENDS -->

    <?php //* * * * * * * * * * * * * * * * (f) modal dettagli elemento  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>

    <?php // * * * * * * * * * * * * * * * * (i) modal nuovo elemento * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>
    <div id="myModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="blue bigger"><span id="nomemodal"><?php echo _($modulo['nome_modulo']);?></span></h4>
                </div>

                <div id="modal-body-myModal" class="modal-body">
                    LOADING...
                </div>

                <div class="modal-footer">
                    <a class="btn btn-success" id="nuovo_elemento_save" name="nuovo_elemento_save">
                        <?php echo _("Salva");?>
                    </a>
                </div>
                    <button class="btn btn-sm btn-info btn-save" id="nuovo_elemento_close" name="nuovo_elemento_close" >
                        <i class="glyphicon glyphicon-repeat"></i>
                        <?php echo _("Chiudi");?>
                    </button>
            </div>
        </div>
    </div><!-- PAGE CONTENT ENDS -->
    <?php // * * * * * * * * * * * * * * * * (f) modal * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *  ?>

    <?php
    if ($numero_records>0) :

        //preparo le intestazioni

        $labels[0]="label-warning";
        $labels[1]="label-info";
        $labels[2]="label-danger";
        $labels[3]="label-success";
        $labels[4]="label-warning";
        $labels[5]="label-info";
        $labels[6]="label-danger";
        $labels[7]="label-success";
        $labels[8]="label-warning";
        $labels[9]="label-info";
        $labels[10]="label-danger";
        $labels[11]="label-success";
        $labels[12]="label-warning";
        $labels[13]="label-info";
        $labels[14]="label-danger";
        $labels[15]="label-success";

        $query1="SHOW FULL COLUMNS FROM ".$modulo['nome_tabella'];
        $stmt1=$dbh->query($query1);
        $columns1=array();
        while ($row=$stmt1->fetch(PDO::FETCH_ASSOC)) :
            //escludo la chiave primaria
            if ($row['Key']=='PRI') continue;

            $row['tipo']=getTipoColonna($row['Type']);
            if ($row['tipo']=='ENUM') {
                $values=getEnumValues($modulo['nome_tabella'],$row['Field']);
                $row['values']=$values;
                $label=array();
                $v=0;
                foreach ($values as $val) {
                    $label[$val]=$labels[$v];
                    if ($val=='no') { $label['no']=$labels[4]; continue; }
                    if ($val=='si') { $label['si']=$labels[5]; continue; }
                    $v++;
                }
                $row['labels']=$label;
            }

            //costruisco l'array dei campi con il loro tipo
            $columns1[$row['Field']]=$row;

        endwhile;

        $header=array();
        $riga1=$righe[0];
        foreach ($riga1 as $key=>$value) {

            if (!(in_array($key,$campi_non_mostrati_in_tabella))) $header[]=$key;
        }

        ?>

        <div>
            <form>
                <table id="dynamic-table" class="table table-striped table-bordered bootstrap-datatable datatable responsive">
                    <thead>
                    <?php if ($modulo['ricercasingola']=='1') { ?>
                        <tr class="searchrow">
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <?php foreach ($header as $h) :
                                $classhidden='';
                                if (in_array($h,$campi_hidden_xs)) { $classhidden.=" hidden-xs "; }
                                if (in_array($h,$campi_hidden_sm)) { $classhidden.=" hidden-sm "; }
                                if (in_array($h,$campi_solo_xls)) { $classhidden.=" hidden "; }
                                if (in_array($h,$campi_non_xls)) { $classhidden.=" no-print "; }
                                ?>
                                <th class="<?php echo $classhidden;?>">
                                    <input class="inputsearch" type="search" placeholder="Cerca">
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th class="no-print"> # </th>

                            <th class="no-print"><?php echo _("Actions");
                            if ($modulo['allegati_possibili']=='si') {  echo "___"._("Allegati");?></th><?php } else { ?></th><?php } ?>

                        <?php foreach ($header as $h) :
                            $classhidden='';
                            if (in_array($h,$campi_hidden_xs)) { $classhidden.=" hidden-xs "; }
                            if (in_array($h,$campi_hidden_sm)) { $classhidden.=" hidden-sm "; }
                            if (in_array($h,$campi_solo_xls)) { $classhidden.=" hidden "; }
                            if (in_array($h,$campi_non_xls)) { $classhidden.=" no-print "; }
                            ?>
                            <th class="<?php echo $classhidden;?>"><?php echo _("$h");?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($righe as $ev) : ?>
                        <tr id="<?php echo $ev['ordine'];?>" idmodalele="<?php echo $ev['chiaveprimaria'];?>" idmodalmod="<?php echo $modulo['id_modulo'];?>" >
                          <td class="center"> <?php echo $ev['ordine'];?> </td>
                            <td>
                                <div class="dropdown">
                                    <div class="btn-group">
                                        <div class="btn-group" style="text-align:center;">
                                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> <i class=" glyphicon glyphicon-wrench "></i> <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <li class="dropdown-header"><?php echo $modulo['nome_modulo'];?></li>
                                                <?php if ($modulo['aprimodal']=='si') { ?>
                                                    <?php if ($permessi_modulo['Can_update']=='si') { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Edit');?>" class="green aprimodal-ele" idmodalmod="<?php echo $modulo['id_modulo'];?>" idmodalele="'<?php echo $ev['chiaveprimaria'];?>'"><i class="glyphicon glyphicon-edit"></i> EDIT</a></li>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if ($permessi_modulo['Can_update']=='si') { ?>
                                                        <li><a href="get_element.php?debug=<?php echo $_GET['debug'];?>&idmod=<?php echo $modulo['id_modulo'];?>&idele='<?php echo $ev['chiaveprimaria'];?>'" data-toggle="tooltip" title="<?php echo _('Edit');?>" class="green"><i class="glyphicon glyphicon-edit"></i> EDIT</a></li>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php if ($permessi_modulo['Can_delete']=='si') { ?>
                                                    <li class="red"><a data-toggle="tooltip" title="<?php echo _('Delete');?>" class="red delete-elemento" idmodalmod="<?php echo $modulo['id_modulo'];?>" idmodalele="<?php echo $ev['chiaveprimaria'];?>" href="#" ><i class="glyphicon glyphicon-trash"></i> DELETE</a></li>
                                                <?php } ?>

                                                <?php //(i) Attivita ?>

                                                <?php if (($modulo['nome_modulo']=='Attivita' && $ev['tipo']=='Trattamento')) { ?>
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Controllo Pdf Trattamento');?>" target="_blank" href="controlloPdfTrattamento.php?codice_attivita=<?php echo $ev['chiaveprimaria'];?>"><i class="fa fa-file-pdf-o"></i> CONTROLLO PDF</a></li>
                                                <?php } ?>
                                                <?php if (($modulo['nome_modulo']=='Attivita' && $ev['tipo']=='Trattamento') and ($permessi_modulo['Can_update']=='si')) { ?>
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Invia Pdf Trattamento al Cliente');?>" href="controlloPdfTrattamento.php?codice_attivita=<?php echo $ev['chiaveprimaria'];?>"><i class="fa fa-envelope"></i> INVIA PDF AL CLIENTE</a></li>
                                                <?php } ?>

                                                <?php if (($modulo['nome_modulo']=='Attivita' ) and ($permessi_modulo['Can_update']=='si')) { ?>
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Registra');?>" class="registravisita" idmodalmod="<?php echo $modulo['id_modulo'];?>" idmodalele="<?php echo $ev['chiaveprimaria'];?>" href="#"><i class="fa fa-check"></i> REGISTRA</a></li>
                                                <?php } ?>

                                                <?php if (($modulo['nome_modulo']=='Attivita' && $ev['tipo']=='Trattamento') and ($permessi_modulo['Can_update']=='si')) { ?>
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Replica');?>" class="replicatrattamento" idmodalmod="<?php echo $modulo['id_modulo'];?>" idmodalele="<?php echo $ev['chiaveprimaria'];?>" href="#"><i class="fa fa-calendar"></i> REPLICA</a></li>
                                                <?php } ?>

                                                <?php //(f) Attivita ?>

                                                <?php //(i) Clienti ?>
                                                <?php if ($modulo['nome_modulo']=='Clienti') { ?>
                                                    <li class="divider"></li>
                                                    <li class="dropdown-header"><?php echo _('Sedi Clienti');?></li>
                                                <?php } ?>
                                                <?php if (($modulo['nome_modulo']=='Clienti') and ($permessi_modulo['Can_create']=='si')) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Sedi Clienti')); ?>
                                                    <?php if ($tmpmod['aprimodal']=='si') { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Altra sede');?>" class="aprimodal-ele" k1="id_user-<?php echo $_SESSION['id_user'];?>" k="id_cliente-<?php echo $ev['chiaveprimaria'];?>" nomemodalmod="<?php echo $tmpmod['nome_modulo'];?>" idmodalmod="<?php echo $tmpmod['id_modulo'];?>" idmodalele="-1" href="#" >NUOVO </a></li>
                                                    <?php } else { ?>
                                                        <li><a href="get_element.php?idele=-1&debug=<?php echo $_GET['debug'];?>&idmod=<?php echo $tmpmod['id_modulo'];?>&k=id_cliente-<?php echo $ev['chiaveprimaria'];?>&k1=id_user-<?php echo $_SESSION['id_user'];?>" data-toggle="tooltip" title="<?php echo _('Altra Sede');?>">NUOVO </a></li>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php if (($modulo['nome_modulo']=='Clienti')) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Sedi Clienti')); ?>
                                                    <?php
                                                    $totsedi=0;
                                                    $querysedi="SELECT count(*) as tot FROM ".$GLOBAL_tb['sedi_clienti']." where id_cliente=".$ev['chiaveprimaria'];
                                                    $stmtreg=$dbh->query($querysedi);
                                                    $rowsedi=$stmtreg->fetch(PDO::FETCH_ASSOC);
                                                    $totsedi=$rowsedi['tot'];
                                                    ?>
                                                    <?php if ($totsedi==0) { ?>
                                                        <li class="disabled"><a data-toggle="tooltip" title="<?php echo _('Sedi clienti');?>" >ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } else { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Sedi clienti');?>" href="module.php?modname=Sedi clienti&p[id_cliente]=<?php echo $ev['chiaveprimaria'];?>">ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php //(f) Clienti ?>


                                                <?php //(i) Sedi Clienti ?>

                                                <?php if ($modulo['nome_modulo']=='Sedi Clienti') { ?>
                                                    <li class="divider"></li>
                                                    <li class="dropdown-header"><?php echo _('Aree');?></li>
                                                <?php } ?>
                                                <?php if (($modulo['nome_modulo']=='Sedi Clienti') and ($permessi_modulo['Can_create']=='si')) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Aree')); ?>
                                                    <?php if ($tmpmod['aprimodal']=='si') { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Altra Area');?>" class="aprimodal-ele" k1="id_user-<?php echo $_SESSION['id_user'];?>" k="id_sede-<?php echo $ev['chiaveprimaria'];?>" nomemodalmod="<?php echo $tmpmod['nome_modulo'];?>" idmodalmod="<?php echo $tmpmod['id_modulo'];?>" idmodalele="-1" href="#" >NUOVO </a></li>
                                                    <?php } else { ?>
                                                        <li><a href="get_element.php?idele=-1&debug=<?php echo $_GET['debug'];?>&idmod=<?php echo $tmpmod['id_modulo'];?>&k=id_sede-<?php echo $ev['chiaveprimaria'];?>&k1=id_user-<?php echo $_SESSION['id_user'];?>" data-toggle="tooltip" title="<?php echo _('Altra Area');?>">NUOVO </a></li>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php if ($modulo['nome_modulo']=='Sedi Clienti' ) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Aree')); ?>
                                                    <?php
                                                    $totpostazioni=0;
                                                    $queryp="SELECT count(*) as tot FROM ".$GLOBAL_tb['postazioni']." where id_area IN (SELECT id FROM ".$GLOBAL_tb['aree']." WHERE id_sede=".$ev['chiaveprimaria'].")";

                                                    $stmtp=$dbh->query($queryp);
                                                    $rowp=$stmtp->fetch(PDO::FETCH_ASSOC);
                                                    $totpostazioni=$rowp['tot'];
                                                    ?>
                                                    <?php
                                                    $totsedi=0;
                                                    $querysedi="SELECT count(*) as tot FROM ".$GLOBAL_tb['aree']." where id_sede=".$ev['chiaveprimaria'];
                                                    $stmtreg=$dbh->query($querysedi);
                                                    $rowsedi=$stmtreg->fetch(PDO::FETCH_ASSOC);
                                                    $totsedi=$rowsedi['tot'];
                                                    ?>

                                                    <?php
                                                    //mappa personalizzata
                                                    if ($modulo['nome_modulo']=='Sedi Clienti') {  ?>
                                                        <?php
                                                        $querysedi="SELECT count(*) as tot FROM pcs_mappeimpianto where id_sede=".$ev['chiaveprimaria'];
                                                        $stmtreg=$dbh->query($querysedi);
                                                        $rowsedi=$stmtreg->fetch(PDO::FETCH_ASSOC);
                                                        $totpostazioni=$rowsedi['tot'];

                                                        ?>
                                                        <?php if ($totpostazioni==0) { ?>
                                                            <li><a href="get_element.php?idele=-1&debug=<?php echo $_GET['debug'];?>&idmod=68&k=id_sede-<?php echo $ev['chiaveprimaria'];?>" data-toggle="tooltip" title="<?php echo _('Attiva Mappa Personalizzata');?>">ATTIVA MAPPA PERSONALIZZATA</a></li>
                                                        <?php }  else { ?>
                                                            <li><a href="mappacustom.php?idsede=<?php echo $ev['chiaveprimaria'];?>" >MAPPA PERSONALIZZATA</a></li>
                                                        <?php } ?>

                                                    <?php } ?>


                                                    <?php if ($totpostazioni==0) { ?>
                                                        <li class="disabled"><a data-idsede="<?php echo $ev['chiaveprimaria'];?>" class="aprimappa">MAPPA <span class="badge"><?php echo $totpostazioni;?></span> postazioni</a></li>
                                                    <?php }  else { ?>
                                                        <li><a data-idsede="<?php echo $ev['chiaveprimaria'];?>" class="aprimappa">MAPPA <span class="badge"><?php echo $totpostazioni;?></span> postazioni</a></li>
                                                    <?php } ?>

                                                    <?php if ($totsedi==0) { ?>
                                                        <li class="disabled"><a data-toggle="tooltip" title="<?php echo _('Aree');?>" >ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } else { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Aree');?>" href="module.php?modname=Aree&p[id_sede]=<?php echo $ev['chiaveprimaria'];?>">ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } ?>
                                                    <?php if ($permessi_modulo['Can_update']=='si'): ?>
                                                    <li><a class="aprimodal-dettagli"  idcliente="<?php echo $ev['id_cliente'];?>" idmodalele="<?php echo $ev['chiaveprimaria'];?>" data-toggle="tooltip" title="<?php echo _('Copia Aree');?>" >COPIA AREE </a></li>
                                                        <?php endif; ?>
                                                <?php } ?>

                                                <?php if ($modulo['nome_modulo']=='Sedi Clienti') { ?>
                                                    <li class="divider"></li>
                                                    <li class="dropdown-header"><?php echo _('Visite');?></li>
                                                <?php } ?>
                                                <?php if ($modulo['nome_modulo']=='Sedi Clienti' ) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Visite')); ?>
                                                    <?php
                                                    $totpostazioni=0;
                                                    $queryp="SELECT count(*) as tot FROM ".$GLOBAL_tb['visite']." WHERE id_sede=".$ev['chiaveprimaria'];

                                                    $stmtp=$dbh->query($queryp);
                                                    $rowp=$stmtp->fetch(PDO::FETCH_ASSOC);
                                                    $totpostazioni=$rowp['tot'];
                                                    ?>
                                                    <?php if ($totpostazioni==0) { ?>
                                                        <li class="disabled"><a data-toggle="tooltip" title="<?php echo _('Visite');?>" >ELENCO <span class="badge"><?php echo $totpostazioni;?></span></a></li>
                                                    <?php } else { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Visite');?>" href="module.php?modname=Visite&p[id_sede]=<?php echo $ev['chiaveprimaria'];?>">ELENCO <span class="badge"><?php echo $totpostazioni;?></span></a></li>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php //(f) Sedi Clienti ?>

                                                <?php //(i) Aree ?>
                                                <?php if ($modulo['nome_modulo']=='Aree') { ?>
                                                    <li class="divider"></li>
                                                    <li class="dropdown-header"><?php echo _('Postazioni');?></li>
                                                <?php } ?>
                                                <?php if (($modulo['nome_modulo']=='Aree')) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Postazioni')); ?>
                                                    <?php
                                                    $totsedi=0;
                                                    $querysedi="SELECT count(*) as tot FROM ".$GLOBAL_tb['postazioni']." where id_area=".$ev['chiaveprimaria'];
                                                    $stmtreg=$dbh->query($querysedi);
                                                    $rowsedi=$stmtreg->fetch(PDO::FETCH_ASSOC);
                                                    $totsedi=$rowsedi['tot'];
                                                    ?>
                                                    <?php if ($totsedi==0) { ?>
                                                        <li class="disabled"><a data-toggle="tooltip" title="<?php echo _('Postazioni');?>" >ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } else { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Postazioni');?>" href="module.php?modname=Postazioni&p[id_area]=<?php echo $ev['chiaveprimaria'];?>">ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php //(f) Aree ?>

                                                <?php //(i) Visite ?>
                                                <?php if (($modulo['nome_modulo']=='Visite') and ($permessi_modulo['Can_update']=='si')) { ?>
                                                    <!--<li><a data-toggle="tooltip" title="<?php echo _('Registra');?>" class="registravisita" idmodalmod="<?php echo $modulo['id_modulo'];?>" idmodalele="<?php echo $ev['chiaveprimaria'];?>" href="#"><i class="fa fa-check"></i> REGISTRA</a></li>-->
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Controllo Pdf');?>" target="_blank" href="controlloPdf.php?codice_visita=<?php echo $ev['chiaveprimaria'];?>"><i class="fa fa-file-pdf-o"></i> CONTROLLO PDF</a></li>
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Invia Pdf al Cliente');?>" href="controlloPdf.php?invia=1&codice_visita=<?php echo $ev['chiaveprimaria'];?>"><i class="fa fa-envelope"></i> INVIA PDF AL CLIENTE</a></li>
                                                <?php } ?>
                                                <?php if (($modulo['nome_modulo']=='Visite') and ($permessi_modulo['Can_read']=='si')) { ?>
                                                    <li class="divider"></li>
                                                    <li class="dropdown-header"><?php echo _('Ispezioni');?></li>
                                                <?php } ?>
                                                <?php if (($modulo['nome_modulo']=='Visite')) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Ispezioni')); ?>
                                                    <?php
                                                    $totsedi=0;
                                                    $querysedi="SELECT count(*) as tot FROM ".$GLOBAL_tb['ispezioni']." where codice_visita='".$ev['chiaveprimaria']."'";
                                                    $stmtreg=$dbh->query($querysedi);
                                                    $rowsedi=$stmtreg->fetch(PDO::FETCH_ASSOC);
                                                    $totsedi=$rowsedi['tot'];
                                                    ?>
                                                    <?php if ($totsedi==0) { ?>
                                                        <li class="disabled"><a data-toggle="tooltip" title="<?php echo _('Ispezioni');?>" >ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } else { ?>
                                                        <li><a data-toggle="tooltip" title="<?php echo _('Ispezioni');?>" href="module.php?modname=Ispezioni&p[codice_visita]=<?php echo $ev['chiaveprimaria'];?>">ELENCO <span class="badge"><?php echo $totsedi;?></span></a></li>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php //(f) Visite ?>

                                                <?php //(i) Report ?>
                                                <?php if (($modulo['nome_modulo']=='Report') and ($permessi_modulo['Can_update']=='si')) { ?>
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Controllo Pdf');?>" target="_blank" href="controlloReportPdf.php?idreport=<?php echo $ev['chiaveprimaria'];?>"><i class="fa fa-file-pdf-o"></i> CONTROLLO PDF</a></li>
                                                    <li><a data-toggle="tooltip" title="<?php echo _('Invia Pdf al Cliente');?>" target="_blank" href="controlloReportPdf.php?idreport=<?php echo $ev['chiaveprimaria'];?>&invia=1"><i class="fa fa-envelope"></i> INVIA PDF AL CLIENTE</a></li>
                                                <?php } ?>
                                                <?php //(f) Report ?>


                                                <?php
                                                if (count($addcolumn)>0) : ?>
                                                    <li class="divider"></li>
                                                    <li class="dropdown-header"><?php echo _('Addons');?></li>
                                                    <?php foreach ($addcolumn as $ac) :
                                                        $ac['url']=str_replace("|%chiaveprimaria%|",$ev['chiaveprimaria'],$ac['url']);
                                                        ?>
                                                        <li><a href="<?php echo $ac['url'];?>"><i class=" <?php echo $ac['font-icon'];?> bigger-130"></i></a></li>
                                                        <?php
                                                    endforeach;
                                                endif;
                                                ?>

                                            </ul>
                                        </div>
                                        <?php
                                        if ($modulo['allegati_possibili']=='si') : ?>
                                            <?php
                                            $queryAll="SELECT count(*) as tot FROM ".$GLOBAL_tb['files']." WHERE id_elem=".$ev['chiaveprimaria']." AND tb='".$modulo['nome_tabella']."'";
                                            $stmt2=$dbh->query($queryAll);
                                            $rowAll=$stmt2->fetch(PDO::FETCH_ASSOC);
                                            $totAllegati=$rowAll['tot'];
                                            ?>
                                            <?php if ($totAllegati>0) { ?>
                                                <button idmodalele="<?php echo $ev['chiaveprimaria'];?>" idmodalmod="<?php echo $modulo['id_modulo'];?>" class="aprimodal-allegati btn btn-info" type="button"> <i class=" glyphicon glyphicon-paperclip "></i> <?php echo $totAllegati;?> </button>
                                            <?php } else { ?>
                                                <button idmodalele="<?php echo $ev['chiaveprimaria'];?>" idmodalmod="<?php echo $modulo['id_modulo'];?>" class="btn btn-danger" type="button"> <i class=" glyphicon glyphicon-paperclip "></i> <?php echo $totAllegati;?> </button>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <?php foreach ($header as $h) :
                                $classhidden='';
                                if (in_array($h,$campi_hidden_xs)) { $classhidden.=" hidden-xs "; }
                                if (in_array($h,$campi_hidden_sm)) { $classhidden.=" hidden-sm "; }
                                if (in_array($h,$campi_solo_xls)) { $classhidden.=" hidden "; }
                                if (in_array($h,$campi_non_xls)) { $classhidden.=" no-print "; }
                                ?>
                                <td class="<?php echo $classhidden;?>">
                                    <?php //se è un campo enum mettiamo delle labels
                                    if (($columns1[$h]['tipo'])=="ENUM") {

                                        ?>
                                            <span class="label <?php echo $columns1[$h]['labels'][$ev[$h]];?> "><?php echo $ev[$h];?></span>
                                    <?php
                                    } else {
                                        echo $ev[$h];
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </form>
        </div>



        <div id="div_mappa" class="panel panel-primary" style="display:none;" >
            <div id="printable">
                <div class="panel-heading">
                    <div class="panel-title">
                    </div>
                    <div class="panel-title pull-left mostra">
                        <h4>Mappa Impianto</h4>
                    </div>
                    <div class="panel-title pull-right mostra">
                        <a class="btn btn-warning btn-scrolltop"><i class="fa fa-angle-double-up" aria-hidden="true"></i></a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body" id="mapcontainer" >
                    <div id="map"></div>
                </div>
            </div>
            <input class="btn btn-primary" type="button" onclick="printDiv('printable')" value="STAMPA MAPPA" />

        </div>

        <?php
    else:

        echo _("Nessun elemento presente!");

    endif;
endif; ?>
<?php /* (f) ------------------------------ elenco del singolo modulo --------------------------------------------------------------------------------------   */ ?>


<script src="ckeditor/ckeditor.js"></script>
<script src="plupload/js/plupload.full.min.js"></script>

        <script>
            function printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;

                document.body.innerHTML = printContents;

                window.print();

                document.body.innerHTML = originalContents;
            }
        </script>

<script type="text/javascript">
    jQuery(function($) {

      $(document).on("click","#azzeraattivita",function(){

        bootbox.confirm("<?php echo _('Sicuro di voler azzerare tutte le attività passate non ancora concluse?');?>", function(result) {
            if (result) {
              var params={};
              $.ajax({
                  dataType: "json",
                  type: 'POST',
                  url: "ajax_azzeraattivitaClean.php",
                  data: jQuery.param(params) ,
                  success: function (data) {
                      console.log(data);
                      if (data.result==true) {
                          $.notify({
                              title: '<strong>Successo!</strong>',
                              message: 'Attività azzerate.'
                          },{
                              type: 'success'
                          });
                          setTimeout(function(){location.reload();}, 100);
                      } else {
                          $.notify({
                              title: '<strong>ERRORE!</strong>',
                              message: 'Attività non azzerate.'
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
            }
        });
      });

        $(document).on("click",".generaattivitadacontratti",function(){
            var idcontratto=$(this).attr('attr');
            var params={};
            params.idcontratto=idcontratto;

            $.ajax({
                dataType: "json",
                type: 'POST',
                url: "ajax_genera_attivita_da_contratti.php",
                data: jQuery.param(params) ,
                success: function (data) {
                    console.log(data);
                    if (data.result==true) {
                        $.notify({
                            title: '<strong>Successo!</strong>',
                            message: "Attivita inserite con successo!"
                        },{
                            type: 'success'
                        });
                        setTimeout(function(){location.reload();}, 2000);
                    } else {
                        $.notify({
                            title: '<strong>ERRORE!</strong>',
                            message: data.error
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


        });

    <?php if ($modulo['nome_modulo']=='Sedi Clienti') : ?>

        //(i) MAPPA DI GOOGLE
        function setMarkers(map,elencopost,geocoder) {

            var bounds = new google.maps.LatLngBounds();
            // Adds markers to the map.
            console.log(elencopost);
            for (var i = 0; i < elencopost.length; i++) {
                var beach = elencopost[i];
                var data = elencopost[i];
                //console.log("Elemento "+i+":");
                //console.log(beach);

                var icona=new Array();

                icona[1]='https://maps.google.com/mapfiles/ms/icons/red-dot.png';
                icona[2]='https://maps.google.com/mapfiles/ms/icons/green-dot.png';
                icona[3]='https://maps.google.com/mapfiles/ms/icons/yellow-dot.png';
                icona[4]='https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
                icona[5]='https://maps.google.com/mapfiles/ms/icons/purple-dot.png';
                icona[6]='https://maps.google.com/mapfiles/ms/icons/orange-dot.png';
                icona[7]='https://maps.google.com/mapfiles/ms/icons/pink-dot.png';

                iconp = icona[beach.id_servizio];

                var content = "<h4 style='text-align:center;'>"+beach.nome+"</h4>";
                content = content + "<p><i>CODICE:</i> "+beach.codice_postazione+"</p>"
                content = content + "<p><i>AREA:</i> "+beach.Area+"</p>"
                content = content + "<p><i>Servizio:</i> "+beach.descrizione_servizio+"</p>"
                content = content + "<p><i>Tipo:</i> "+beach.tipo+"</p>"
                content = content + "<p><i>Modello:</i> "+beach.modello+"</p>"
                content = content + "<p><i>Prodotto:</i> "+beach.prodotto+"</p>"
                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                    position: {lat: parseFloat(beach.latitudine_p), lng: parseFloat(beach.longitudine_p)},
                    map: map,
                    icon:iconp,
                    <?php if ($utente['id_ruolo']<4) { //se non è cliente ?>
                    draggable: true,
                    <?php } ?>
                    codice_postazione: beach.codice_postazione
                });
                (function (marker, data, content, infowindow) {
                    google.maps.event.addListener(marker, "click", function (e) {
                        infowindow.setContent(content);
                        infowindow.open(map, marker);
                    });
                    google.maps.event.addListener(marker, "dragend", function (e) {
                        var lat, lng, address;

                        var params1={};
                        params1.codice_postazione=marker.codice_postazione;
                        params1.lat=marker.getPosition().lat();
                        params1.lng=marker.getPosition().lng();
                        console.log(params1);
                        $.ajax({
                            type: "POST",
                            url: "ajax_modificaPosizionePostazione.php",
                            data: params1,
                            dataType: 'json',
                            success: function(data){
                                console.log(data);
                                if (data.result==true) {
                                } else {
                                    $.notify(data.error);
                                    console.log(data);
                                }
                            },
                            error: function(data) {
                                console.log(data);
                            }
                        });
                        /*
                        geocoder.geocode({ 'latLng': marker.getPosition() }, function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                lat = marker.getPosition().lat();
                                lng = marker.getPosition().lng();
                                address = results[0].formatted_address;
                                alert("Latitude: " + lat + "\nLongitude: " + lng + "\nAddress: " + address);
                            }
                        });
                        */
                    });
                })(marker, data, content, infowindow);


                /*
                                google.maps.event.addListener(marker, 'dragend', function() {
                                    console.log(marker);
                                    var params1={};
                                    params1.codice_postazione=marker.codice_postazione;
                                    params1.lat=marker.getPosition().lat();
                                    params1.lng=marker.getPosition().lng();
                                    console.log(params1);
                                    $.ajax({
                                        type: "POST",
                                        url: "ajax_modificaPosizionePostazione.php",
                                        data: params1,
                                        dataType: 'json',
                                        success: function(data){
                                            console.log(data);
                                            if (data.result==true) {
                                            } else {
                                                $.notify(data.error);
                                                console.log(data);
                                            }
                                        },
                                        error: function(data) {
                                            console.log(data);
                                        }
                                    });
                                  //alert(marker.getPosition().lat());
                                  //alert(marker.getPosition().lng());

                                });
                */
                bounds.extend(marker.getPosition());
            }

            bounds.extend(marker.getPosition());
            map.fitBounds(bounds);
        }

        $(".aprimappa").on("click",function(){

            $("#div_mappa").show();
            var navheight=$("nav").height();
            var offsetscroll=$("#div_mappa").offset().top;
            $('html,body').animate({scrollTop: offsetscroll-navheight},'slow');

            var map = new google.maps.Map(document.getElementById('map'), {
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var geocoder = geocoder = new google.maps.Geocoder();
            var mapwidth=$("#mapcontainer").width();
            $("#map").width(mapwidth);
            $("#map").height(500);

            var params={};
            var idsede=$(this).attr("data-idsede");
            //console.log(idsede);
            params.idsede=idsede;
            //console.log(params);
            $.ajax({
                type: "POST",
                url: "ajax_get_postazioniPerMappa.php",
                data: params,
                dataType: 'json',
                success: function(data){
                    console.log(data);
                    if (data.result==true) {
                        //console.log(data.caditoie);
                        taskmap=data.postazioni;
                        setMarkers(map,taskmap,geocoder);
                    } else {
                        $.notify(data.error);
                        console.log(data);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });



            //setMarkers(map,taskmap);
        })

        //(f) MAPPA DI GOOGLE

        <?php endif; ?>

//additional functions for data table
        var editor;

        $('[data-rel=tooltip]').tooltip();

        //dettagli modal timbrature
        $(document).on("click",".aprimodal-dettagli-timbrature",function(){
            var idattivita=$(this).attr("data-id-attivita");
            $("#ModalDetailsTimbrature").modal({show:true});

            $('#modal-body-ModalDetailsTimbrature').load('ajax_getmodulotimbrature.php?'+ $.param({
                backurl: 'http://<?php echo $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];?>',
                idattivita: idattivita}),function(result){

                $('.chosen-select').chosen({disable_search_threshold: 10});

            }); //fine $('#modal-body-ModalDettagli').load('ajax_getmodulocopiaaree.php?'+ $.param({
        });


        //dettagli modal
        $(document).on("click",".aprimodal-dettagli",function(){
            var idele=$(this).attr("idmodalele");
            var idcliente=$(this).attr("idcliente");
            $("#ModalDetails").modal({show:true});

            $('#modal-body-ModalDetails').load('ajax_getmodulocopiaservizi.php?'+ $.param({
                backurl: 'http://<?php echo $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];?>',
                idcliente: idcliente,
                idele: idele}),function(result){

                $('.chosen-select').chosen({disable_search_threshold: 10});

            }); //fine $('#modal-body-ModalDettagli').load('ajax_getmodulocopiaaree.php?'+ $.param({
        });

        //timbrature
        $(document).on("click","#aggiornatimbrature",function(){

            $("#ModalDetailsTimbrature").hide();

            var timbrature=Array();

            $(".timbratura").each(function(){
              var operatore=$(this).attr("data-idoperatore");
              var idattivita=$(this).attr("data-idattivita");

              if (typeof timbrature[operatore] != "undefined") {
              } else {
                timbrature[operatore]={};
              }

              var tipo=$(this).attr("data-tipo");
              if (tipo=="timbraturadata") {
                timbrature[operatore].data=$(this).val();
              }
              if (tipo=="timbraturaora") {
                timbrature[operatore].ora=$(this).val();
              }
              timbrature[operatore].idattivita=idattivita;
              timbrature[operatore].operatore=operatore;
            })
            if (timbrature.length>0) {
              for (var operatore in timbrature) {
                var params={};
                params.idoperatore=operatore;
                params.idattivita=timbrature[operatore].idattivita;
                params.timbraturadata=timbrature[operatore].data;
                params.timbraturaora=timbrature[operatore].ora;
                params.timbratura=params.timbraturadata+' '+params.timbraturaora;
                console.log(params);

                if (params.timbraturadata=='' || params.timbraturaora=='') { continue; }

                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: "ajax_timbratura.php",
                    data: jQuery.param(params) ,
                    success: function (data) {
                        console.log(data);
                        if (data.result==true) {
                            $.notify({
                                title: '<strong>Successo!</strong>',
                                message: 'Timbratura aggiornata.'
                            },{
                                type: 'success'
                            });
                            //var url='get_element.php?debug=0&modname=Polizze&idele='+data.idpolizza;
                            //setTimeout(function(){$(location).attr('href',url);}, 100);
                        } else {
                            $.notify({
                                title: '<strong>ERRORE!</strong>',
                                message: 'Timbratura non aggiornata.'
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
              }
            }

            //poi devo fare un refresh della pagina
            setTimeout(function(){
                location.reload();
            }, 1000);
        });


        $(document).on("click",".eliminatimbratura",function(){
          var idtimbratura=$(this).attr("idtimbratura");
          var params={};
          params.idtimbratura=idtimbratura;
          params.delete=1;



          bootbox.confirm("<?php echo _('Sicuro di voler azzerare questa timbratura?');?>", function(result) {
              if (result) {
                $("#ModalDetailsTimbrature").hide();
                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: "ajax_timbratura.php",
                    data: jQuery.param(params) ,
                    success: function (data) {
                        console.log(data);
                        if (data.result==true) {
                            $.notify({
                                title: '<strong>Successo!</strong>',
                                message: 'Timbratura cancellata.'
                            },{
                                type: 'success'
                            });
                        } else {
                            $.notify({
                                title: '<strong>ERRORE!</strong>',
                                message: 'Timbratura non cancellata.'
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

              }
            });

        })

        //copia servizi da una sede ad un'altra
        $(document).on("click","#copiaareesubmit",function(){
            var params={};
            params.sedepartenza=$("#sedepartenza").val();
            params.sedearrivo=$("#sedearrivo").val();
            $("#ModalDetails").hide();

            $.ajax({
                dataType: "json",
                type: 'POST',
                url: "ajax_copia_servizi.php",
                data: jQuery.param(params) ,
                success: function (data) {
                    console.log(data);
                    if (data.result==true) {
                        $.notify({
                            title: '<strong>Successo!</strong>',
                            message: 'Servizi copiati.'
                        },{
                            type: 'success'
                        });
                        //var url='get_element.php?debug=0&modname=Polizze&idele='+data.idpolizza;
                        //setTimeout(function(){$(location).attr('href',url);}, 100);
                    } else {
                        $.notify({
                            title: '<strong>ERRORE!</strong>',
                            message: 'Aree non copiate.'
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


        $( document ).on( "click", ".aprimodal-ele", function() {
            var idele=$(this).attr("idmodalele");
            var idmod=$(this).attr("idmodalmod");
            var view=$(this).attr("view");
            var k='';
            var k1='';
            var k2='';
            var k=$(this).attr("k");
            var k1=$(this).attr("k1");
            var k2=$(this).attr("k2");
            var nomemodalmod=$(this).attr("nomemodalmod");
            if (nomemodalmod != '') {
                $("#nomemodal").text(nomemodalmod);
            }
            $('#myModal').modal({show:true});

            $('#modal-body-myModal').load('ajax_getmodulo.php?'+ $.param({
                backurl: 'http://<?php echo $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];?>',
                idmod: idmod,
                view: view,
                k: k,
                k1: k1,
                k2: k2,
                <?php if ($_REQUEST['debug']) { ?>
                debug: <?php echo $_REQUEST['debug'];?>,
                <?php } ?>
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
                <?php /* da vedere ?>
                $('#nuovo_elemento').formValidation('resetForm', true);

                $('#nuovo_elemento').formValidation({
                    framework: 'bootstrap',
                    excluded: [':disabled'],
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        social: {
                            validators: {
                                notEmpty: {
                                    message: 'Social is required'
                                }
                            }
                        }
                    }
                });
                <?php */ ?>

            }); //fine $('#modal-body-myModal').load('ajax_getmodulo.php?'+ $.param({

        }); // fine $( document ).on( "click", ".aprimodal-ele", function() {

        $('#myModal').on('shown.bs.modal', function(e){

            $("#nuovo_elemento_save").click(function(){
                //devo riattivare i campi enum messi in readonly
                $('input, select').attr('disabled', false);
                $.post("ajax_modifica_elemento.php", $("#nuovo_elemento").serialize(), function(msg){$("#messaggiovalidazione").html(msg);});
            });

            $("#nuovo_elemento_close").click(function(e){
              console.log("Dentro close");
                e.preventDefault();
                //window.location.href = $("#backurl").val();
                $("#myModal").modal('hide');
            });

            $("#nuovo_elemento_rimani").click(function(){
                //devo riattivare i campi enum messi in readonly
                $('input, select').attr('disabled', false);
                $.post("ajax_modifica_elemento.php?rimani=1", $("#nuovo_elemento").serialize(), function(msg){$("#messaggiovalidazione").html(msg);} );
            });

            $("#ritorna_a_cliente").click(function(e){
                e.preventDefault();
                e.stopPropagation();
                var url='module.php?modname=Clienti&p[id]='+$("#id_cliente").val();
                setTimeout(function(){$(location).attr('href',url);}, 100);
            });
        });

        $(document).on('click', ".delete-elemento",function(){
            var idele=$(this).attr("idmodalele");
            var idmod=$(this).attr("idmodalmod");
            bootbox.confirm("<?php echo _('Sicuro di voler eliminare questo elemento?');?>", function(result) {
                if (result) {
                    $.post("ajax_delete_elemento_new.php", { idmod: idmod, idele: idele } , function(msg){$("#responso").html(msg);} );
                    setTimeout(function(){location.reload();}, 2000);
                }
            });
        });

    $(document).on('click', ".registravisita",function(){
         var idele=$(this).attr("idmodalele");
            var idmod=$(this).attr("idmodalmod");

            bootbox.prompt({
                title: "Registro questo elemento con il testo seguente",
                locale: 'custom',
                callback: function (result) {
                    $.post("ajax_registra.php", { idmod: idmod, idele: idele, testo:result } , function(msg){$("#responso").html(msg);                     console.log(msg);
                    } );
                    //setTimeout(function(){location.reload();}, 2000);
                }
            });

        });

    $(document).on('click', ".replicatrattamento",function(){
         var idele=$(this).attr("idmodalele");
            var idmod=$(this).attr("idmodalmod");


            var form = $('<form>Da data: <input type="date" name="daData"/><br/>A data: <input type="date" name="aData"/><br/>Cadenza (in giorni): <input type="number" name="cadenza"/></form>');
            bootbox.alert(form,function(){
                var daData = form.find('input[name=daData]').val();
                var aData = form.find('input[name=aData]').val();
                var cadenza = form.find('input[name=cadenza]').val();
                console.log(daData);
                console.log(aData);
                console.log(cadenza);
                $.post("ajax_replica.php", { idmod: idmod, idele: idele, daData:daData, aData:aData, cadenza:cadenza } , function(msg){$("#responso").html(msg);
                console.log(msg);
                } );

            });

        });


    <?php if ($numero_records>0) : ?>

        <?php //print_r($columns1);?>
        <?php //print_r($header);?>

        //initiate dataTables plugin

        /* Add events */
        var table=$('.datatable')
            //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
            .dataTable( {
                serverSide: true,
                ajax: "server_side_processing.php?<?php echo $_SERVER['QUERY_STRING'];?>",
                "sPaginationType": "bootstrap",
                "bAutoWidth": false,
                "bStateSave": true,
                "aoColumns": [
                    { "bVisible": false },
                    { "bSortable": false },
                    <?php foreach ($header as $h) {
                        $nullcol[]='{ "name": "'.$h.'" }';
                     ?>
                    <?php } ?>
                    <?php echo join(",",$nullcol);?>,
                ],
                "aaSorting": [],
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "Tutti"]],
                "iDisplayLength": 50,
                "renderer": "bootstrap",
                "dom": 'Blfrtip',
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel ',
                        title: '',
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        footer: true,
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-table"></i> CSV ',
                        title: '',
                        exportOptions: {
                            columns: ':not(.no-print)'
                        },
                        footer: true,
                    },
                ],
                /*initComplete: function() {
                    var api = this.api();
                    api.columns([0, 1, 2, 3]).every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                }*/
            } );
/*
        table.columns().eq(0).each(function (colIdx) {
            $('input', table.column(colIdx).footer()).on('keyup change', function () {
                table
                    .column(colIdx)
                    .search(this.value)
                    .draw();
            });
        });
*/
        $.fn.dataTableExt.oApi.fnPagingInfo = function (oSettings) {
            return {
                "iStart": oSettings._iDisplayStart,
                "iEnd": oSettings.fnDisplayEnd(),
                "iLength": oSettings._iDisplayLength,
                "iTotal": oSettings.fnRecordsTotal(),
                "iFilteredTotal": oSettings.fnRecordsDisplay(),
                "iPage": Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength),
                "iTotalPages": Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength)
            };
        }
        $.extend($.fn.dataTableExt.oPagination, {
            "bootstrap": {
                "fnInit": function (oSettings, nPaging, fnDraw) {
                    var oLang = oSettings.oLanguage.oPaginate;
                    var fnClickHandler = function (e) {
                        e.preventDefault();
                        if (oSettings.oApi._fnPageChange(oSettings, e.data.action)) {
                            fnDraw(oSettings);
                        }
                    };

                    $(nPaging).addClass('pagination').append(
                        '<ul class="pagination">' +
                        '<li class="prev disabled"><a href="#">&larr; ' + oLang.sPrevious + '</a></li>' +
                        '<li class="next disabled"><a href="#">' + oLang.sNext + ' &rarr; </a></li>' +
                        '</ul>'
                    );
                    var els = $('a', nPaging);
                    $(els[0]).bind('click.DT', { action: "previous" }, fnClickHandler);
                    $(els[1]).bind('click.DT', { action: "next" }, fnClickHandler);
                },

                "fnUpdate": function (oSettings, fnDraw) {
                    var iListLength = 5;
                    var oPaging = oSettings.oInstance.fnPagingInfo();
                    var an = oSettings.aanFeatures.p;
                    var i, j, sClass, iStart, iEnd, iHalf = Math.floor(iListLength / 2);

                    if (oPaging.iTotalPages < iListLength) {
                        iStart = 1;
                        iEnd = oPaging.iTotalPages;
                    }
                    else if (oPaging.iPage <= iHalf) {
                        iStart = 1;
                        iEnd = iListLength;
                    } else if (oPaging.iPage >= (oPaging.iTotalPages - iHalf)) {
                        iStart = oPaging.iTotalPages - iListLength + 1;
                        iEnd = oPaging.iTotalPages;
                    } else {
                        iStart = oPaging.iPage - iHalf + 1;
                        iEnd = iStart + iListLength - 1;
                    }

                    for (i = 0, iLen = an.length; i < iLen; i++) {
                        // remove the middle elements
                        $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                        // add the new list items and their event handlers
                        for (j = iStart; j <= iEnd; j++) {
                            sClass = (j == oPaging.iPage + 1) ? 'class="active"' : '';
                            $('<li ' + sClass + '><a href="#">' + j + '</a></li>')
                                .insertBefore($('li:last', an[i])[0])
                                .bind('click', function (e) {
                                    e.preventDefault();
                                    oSettings._iDisplayStart = (parseInt($('a', this).text(), 10) - 1) * oPaging.iLength;
                                    fnDraw(oSettings);
                                });
                        }

                        // add / remove disabled classes from the static elements
                        if (oPaging.iPage === 0) {
                            $('li:first', an[i]).addClass('disabled');
                        } else {
                            $('li:first', an[i]).removeClass('disabled');
                        }

                        if (oPaging.iPage === oPaging.iTotalPages - 1 || oPaging.iTotalPages === 0) {
                            $('li:last', an[i]).addClass('disabled');
                        } else {
                            $('li:last', an[i]).removeClass('disabled');
                        }
                    }
                }
            }
        });

        <?php endif; ?>


        /********************************/
        //add tooltip for small view action buttons in dropdown menu
        $('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

        //tooltip placement on right or left
        function tooltip_placement(context, source) {
            var $source = $(source);
            var $parent = $source.closest('table')
            var off1 = $parent.offset();
            var w1 = $parent.width();

            var off2 = $source.offset();
            //var w2 = $source.width();

            if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
            return 'left';
        }







    })
</script>
<!-- (f) inline scripts related to this page -->
        <hr>
        <?php include("INC_90_FOOTER.php");?>
    </div> <!-- /container -->
