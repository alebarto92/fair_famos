<?php

session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idmod=$_REQUEST['idmod'];
$idele=$_REQUEST['idele'];
$debug=$_REQUEST['debug'];
//progettiamoci da sguardi indiscreti!
if ($debug!="VIACOLDEBUG") $debug=0;

$viewmode=$_REQUEST['view'];

$idmoduloFile=getModuloFrom_nome_tabella($GLOBAL_tb[files]);

if ($debug) {
    echo "viewmode:$viewmode";
    echo "<pre><h4>ajax_getmodulo.php</h4>";
    print_r($_REQUEST);
    echo "</pre>";
}

if ($idmod>0 and $idele!='') {
	$modulo=getModulo($idmod);
    $campi_visibility_hidden=explode(",",$modulo['campi_visibility_hidden']);
    $campi_readonly=explode(",",$modulo['campi_readonly']);
    $campi_readonly_on_update=explode(",",$modulo['campi_readonly_on_update']);
    $campi_nascosti=explode(",",$modulo['campi_nascosti']);
    $campi_nascosti_insert=explode(",",$modulo['campi_nascosti_insert']);
    if ($modulo['note']=='si' and $idele>0) { //solo in caso di update vedo le note!
        $note=getNote($idmod,$idele,$utente['id_user']);
        if ($debug) { echo "<b>note:</b>"; print_r($note); echo "<br/><hr/>";}
    }

	if ($modulo['nome_modulo']=="Plugins") {
		//aggiungo chaviesterne ai campi nascosti altrimenti si mangia l'espressione {}
        $campi_nascosti[]="chiaviesterne";
        $campi_nascosti[]="chiaviesternemultiple";
		$campi_nascosti[]="add_column";
	}


	$chiaviesternearray=array();
    if ($debug) { echo "<b>modulo chiaviesterne:</b>";echo $modulo['chiaviesterne'];echo "<br/>"; }
	$chiaviesternearray=json_decode($modulo['chiaviesterne'],true);

    if ($debug) { echo "<b>chiavi esterne array:</b>";print_r($chiaviesternearray); echo "<br/>"; }

	$chiaviesterne=array();
	if (count($chiaviesternearray)>0)
		$chiaviesterne=array_keys($chiaviesternearray);

    if ($debug) { echo "<b>chiavi esterne:</b>";print_r($chiaviesterne); echo "<br/><hr/>"; }

    $chiaviesternemultiplearray=array();
    if ($debug) { echo "<b>modulo chiaviesterne multiple:</b>";echo $modulo['chiaviesternemultiple'];echo "<br/>"; }
    $chiaviesternemultiplearray=json_decode($modulo['chiaviesternemultiple'],true);

    if ($debug) { echo "<b>chiavi esterne multiple array:</b>";print_r($chiaviesternemultiplearray); echo "<br/>"; }

    $chiaviesternemultiple=array();
    if (count($chiaviesternemultiplearray)>0)
        $chiaviesternemultiple=array_keys($chiaviesternemultiplearray);

    if ($debug) { echo "<b>chiavi esterne multiple:</b>";print_r($chiaviesternemultiple); echo "<br/><hr/>";}


} else {
		setNotificheCRUD("admWeb","ERROR","ajax_getmodulo.php","mod: $idmod, ele: $idele");
		echo "Modulo vuoto!";
		return false;
		exit;
}

$permessi=permessi($idmod,$utente['id_ruolo'],$superuserOverride);

if ($idele==-1) { //Crea nuovo elemento
	if ($permessi['Can_create']=='si') {
		$elemento=array();
        //forza gli elementi dell'array se glieli passo da query string
        if ($_REQUEST[k]) {
            list($key,$value)=explode("-",$_REQUEST[k]);
            $elemento[$key]=$value;
        }
        if ($_REQUEST[k1]) {
            list($key,$value)=explode("-",$_REQUEST[k1]);
            $elemento[$key]=$value;
        }
        if ($_REQUEST[k2]) {
            list($key,$value)=explode("-",$_REQUEST[k2]);
            $elemento[$key]=$value;
        }
	} else { ?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
  		<?php echo _("<strong>Attenzione!</strong>You can't delete this element!");?>
  		</div>
</div>
    <script>
	setTimeout(function(){$(".registrazionerror").hide();}, 2000);
	</script>
	<?php
		exit;
	}

} else { //Update Elemento oppure view Elemento

    if ($viewmode) {

    } else {
        if ($permessi['Can_update'] == 'si') {

        } else { ?>
            <div class="registrazionerror alert alert-danger" role="alert">
                <div class="center">
                    <?php echo _("<strong>Attenzione!</strong>Non puoi modificare questo elemento!"); ?>
                </div>
            </div>
            <script>
                setTimeout(function () {
                    $(".registrazionerror").hide();
                }, 2000);
            </script>
            <?php
            exit;
        }
    }



    $elemento=getElemento($idmod,$idele);

foreach ($lingue as $lang):
    $testi=getTestiTraducibili($modulo['nome_tabella'],$idele,$lang);
    //if ($debug) { echo "<pre>"; print_r($testi); echo "</pre>"; }
	if (count($testi)>0) {
		foreach ($testi as $key=>$value) :
			$elemento[$key][$lang]=$value;
		endforeach;
	}
endforeach;

	//echo "<br/>idmod: ".$idmod;
	//echo "<br/>idele: ".$idele;
}

?>
<?php //inside modal-body ?>
<?php /* (i) ------------------------------------------ Generazione form in base ai campi della tabella -------------------------------------------- */?>
												<div id="messaggiovalidazione"></div>
												<form id="nuovo_elemento" name="nuovo_elemento" method="post">
												<input type="hidden" name="id_user" id="id_user" value="<?php echo $_SESSION['id_user'];?>"/>
												<input type="hidden" name="idmod" id="idmod" value="<?php echo $idmod;?>"/>
												<input type="hidden" name="idele" id="idele" value="<?php echo $idele;?>"/>
												<input type="hidden" name="backurl" id="backurl" value="<?php echo $_REQUEST['backurl'];?>"/>

												<?php 	$campiobbligatori=array();
														$query="SHOW FULL COLUMNS FROM ".$modulo['nome_tabella'];
														$stmt=$dbh->query($query);
														$columns=array();
														$fields=array();
														while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) :
															//escludo la chiave primaria
															if ($row['Key']=='PRI') continue;

                                                            if (in_array($row['Field'],$campi_readonly) || $viewmode) $row['readonly']='si';
                                                            //se sono in update, devo escludere i campi readonly on update
                                                            if ($idele>0) {
                                                                if (in_array($row['Field'],$campi_readonly_on_update) || $viewmode) $row['readonly']='si';
                                                            }

                                                            if (in_array($row['Field'],$campi_nascosti)) continue;

                                                            if ($idele==-1) {
                                                                if (in_array($row['Field'],$campi_nascosti_insert)) continue;
                                                            }


															//chiavi esterne, creo la select
															if (in_array($row['Field'],$chiaviesterne)) {
																$row['chiaveesterna']=$chiaviesternearray[$row['Field']];
															}

                                                            //chiavi esterne multiple, creo la select
                                                            if (in_array($row['Field'],$chiaviesternemultiple)) {
                                                                $row['chiaveesternamultipla']=$chiaviesternemultiplearray[$row['Field']];
                                                            }

                                                            //costruisco l'array dei campi con il loro tipo e la loro "obbligatorietà"
															$columns[]=$row;
															$fields[$row['Field']]=1;
															if ($row['Null']=='NO') {
																$campiobbligatori[$row['Field']]=1;
															}
														endwhile;

														//aggiungo i campi traducibili in fondo

														$fieldstraducibili=array();
                                                        $tbcampitraducibili=$GLOBAL_tb['campi_traducibili'];
														$query2="SELECT * FROM $tbcampitraducibili WHERE nome_tabella='".$modulo['nome_tabella']."' order by ordine";
														if ($stmt2=$dbh->query($query2)) {
                                                            while ($row=$stmt2->fetch(PDO::FETCH_ASSOC)) :
                                                                if ($row['html']=='si') {
                                                                    $col['Type']="text";
                                                                } else {
                                                                    $col['Type']="varchar(100)";
                                                                }
                                                                if ($row['obbligatorio']=='si') {
                                                                    $col['Null']="NO";
																	$campiobbligatori[$col['Field']]=1;
                                                                } else {
                                                                    $col['Null']="";
                                                                }
                                                                if (in_array($row['nome_campo'],$campi_readonly) || $viewmode) $col['readonly']='si';
                                                                $col['Field']=$row['nome_campo'];
                                                                $col['Traducibile']='si';
                                                                $fieldstraducibili[$col['Field']]=1;
                                                                $columns[]=$col;
                                                            endwhile;
                                                        }

														$elencocampi=join(",",array_keys($fields));
														$elencocampitraducibili=join(",",array_keys($fieldstraducibili));
                                                        $elencocampiobbligatori=join(",",array_keys($campiobbligatori));
                                                        $campiset=array();?>
												<input type="hidden" name="elencocampi"                 id="elencocampi"                    value="<?php echo $elencocampi;?>"/>
												<input type="hidden" name="elencocampitraducibili"      id="elencocampitraducibili"         value="<?php echo $elencocampitraducibili;?>"/>
                                                <input type="hidden" name="elencocampiobbligatori"      id="elencocampiobbligatori"         value="<?php echo $elencocampiobbligatori;?>"/>
<?php

//$colsmprimacolonna=6;
//$colsmsecondacolonna=5;

//if ($modulo['aprimodal']=='si') {
    $colsmprimacolonna=12;
    $colsmsecondacolonna=12;
//}

/*
------------------------------------------------------------------------------------------------------------------------------------
//
// Le colonne sono costruite leggendo prima i campi della query dentro la tabella moduli
// poi vengono aggiunti i campi presenti nella tabella "Traduzioni"
// si forma pertanto l'array columns, dove gli ultimi n elementi saranno i campi traducibili, contrassegnato con il campo 'Traducibile'='si'
// se il campo è traducibile, creo le linguette delle lingue, una per ogni campo
// e poi chiudo
// in fase di inserimento dati, mando in post nel campo hidden l'elenco dei campi (nomeprincipale) traducibili e poi li splitto per assegnarli alle lingue
// esempio, se i campi sono titolo-it, titolo-en, titolo-fr io manderò nel campo hidden campitraducibili il nome "titolo"
-------------------------------------------------------------------------------------------------------------------------------------
*/?>
                                                    <div class="row border-dashed col-xs-12 col-sm-<?php echo $colsmprimacolonna;?>" ><!-- prima colonna -->
                                                        <?php
                                                        foreach ($columns as $col) :

                                                        ?>
                                                        <div class="row-new" id="DIV_<?php echo $col['Field'];?>" <?php if (in_array($col['Field'],$campi_visibility_hidden)) echo "style='display:none;'";?>>
                                                            <?php if ($modulo['aprimodal']=='si') { ?>
                                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                                <?php } else { ?>


                                                            <?php if (getTipoColonna($col)=="TEXTAREA") { ?>
                                                                <div class="col-xs-12 ">
                                                                    <?php } else { ?>
                                                                    <?php if ($col['Field']=='id_sede' or $col['Field']=='id_attivita') { ?>
                                                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                                                        <?php } elseif ($col['Field']=='timbratura') { ?>
                                                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                                                            <?php } elseif ($col['Field']=='nome_servizio') { ?>
                                                                            <div class="col-xs-12 col-sm-6 col-md-6">
                                                                        <?php } else { ?>
                                                                        <div class="col-xs-12 col-sm-6 col-md-3">
                                                                            <?php } ?>
                                                                            <?php }?>
                                                                            <?php } ?>
                                                                            <div class="form-group"  >
                                                                                <label><?php echo $col['Field'];?>
                                                                                    <?php if ($col['Null']=='NO')
                                                                                    {
                                                                                        echo " (*) ";
                                                                                    } ?>
                                                                                </label>

                                                                                <?php /* (i) creo il panel con le linguette */ ?>
                                                                                <?php if ($col['Traducibile']=='si') : ?>

													<div class="tabbable">
														<ul class="nav nav-tabs" >
														<?php $ll=0;foreach ($lingue as $lang) : ?>
															<li <?php if ($ll==0) { ?> class="active" <?php } ?> >
																<!--<a data-toggle="tab" class="tablang" data-lang="<?php echo $lang;?>" href="#panel-<?php echo $col['Field'];?>-<?php echo $lang;?>">-->
																<a data-toggle="tab" class="tablang tablang-<?php echo $lang;?>" data-lang="<?php echo $lang;?>" href=".panel-<?php echo $lang;?>">
																	<?php echo strtoupper($lang);?>
																</a>
															</li>
														<?php $ll++;endforeach; ?>
														</ul>

																	<?php endif; ?><?php // ?>
																<?php /* (i) creo il panel con le linguette */ ?>

																	<div>
																		<?php
																		/* (i) cerco le chiavi esterne */
																		if ($col['chiaveesterna']) :
																			$query_est=$col['chiaveesterna'];


                                                                            //if ($debug) print_r($permessi);

                                                                            if ($debug) echo "<b>chiaveesterna -> queryest nativa:</b>".$query_est."<br/><hr/>";
                                                                            if ($debug) print_r($elemento);
                                                                            $nuovaqueryest=str_replace("%chiaveprimaria%",$idele,$query_est);
                                                                            $nuovaqueryest=str_replace("%id_user%",$utente['id_user'],$nuovaqueryest);
                                                                            $nuovaqueryest=str_replace("%id_cliente%",$utente['id_cliente'],$nuovaqueryest);
                                                                            $nuovaqueryest=str_replace("%id_negozio%",$utente['id_negozio'],$nuovaqueryest);
                                                                            $canreadall=$permessi['Can_read_all']=='si' ? 1 : 0;
                                                                            $nuovaqueryest=str_replace("%can_read_all%",$canreadall,$nuovaqueryest);
                                                                            $nuovaqueryest=str_replace("%defaultLang%",$defaultLang,$nuovaqueryest);
                                                                            $nuovaqueryest=str_replace("%lang%",$_SESSION['lang'],$nuovaqueryest);
                                                                            $nuovaqueryest=str_replace("%chiaveprimaria%",$elemento['chiaveprimaria'],$nuovaqueryest);


                                                                            if ($debug) echo "<b>chiaveesterna -> nuovaqueryest:</b>".$nuovaqueryest."<br/><hr/>";

																			$stmt=$dbh->query($nuovaqueryest);
																			$enum_est=array();
																			if ($col['Null']=='YES') {
																				$enum_est['']='';
																			}
																			while ($row_est=$stmt->fetch(PDO::FETCH_ASSOC)) {
																				$enum_est[$row_est['id']]=$row_est['value'];
																			}
																		?>
																			<select class="form-control chosen-select" id="<?php echo $col['Field'];?>" name="<?php echo $col['Field'];?>" <?php if ($col['readonly']=='si') echo "disabled";?> data-placeholder="<?php echo $col['Field'];?>">
																			<?php foreach ($enum_est as $key=>$en) : ?>
																			<option <?php if ($elemento[$col['Field']]==$key) {  echo "selected"; } ?> value="<?php echo $key;?>"><?php echo $en;?></option>
																			<?php endforeach; ?>
																			</select>

																		<?php
                                                                            //continue; // ? non capisco perché il continue
																		endif;

																		/* (f) cerco le chiavi esterne */

                                                            /* (i) cerco le chiavi esterne multiple*/
                                                            if ($col['chiaveesternamultipla']) :
                                                                $query_est=$col['chiaveesternamultipla'];

                                                                $nuovaqueryest=str_replace("%chiaveprimaria%",$idele,$query_est);
                                                                $nuovaqueryest=str_replace("%id_user%",$utente['id_user'],$nuovaqueryest);
                                                                $nuovaqueryest=str_replace("%id_cliente%",$utente['id_cliente'],$nuovaqueryest);
                                                                $nuovaqueryest=str_replace("%id_negozio%",$utente['id_negozio'],$nuovaqueryest);
                                                                $canreadall=$permessi['Can_read_all']=='si' ? 1 : 0;
                                                                $nuovaqueryest=str_replace("%can_read_all%",$canreadall,$nuovaqueryest);
                                                                $nuovaqueryest=str_replace("%defaultLang%",$defaultLang,$nuovaqueryest);
                                                                $nuovaqueryest=str_replace("%lang%",$lang,$nuovaqueryest);

                                                                if ($debug) echo "<b>chiave esterna multipla -> nuovaqueryest:</b>".$nuovaqueryest."<br/><hr/>";

                                                                $valoriattuali=explode(",",$elemento[$col['Field']]);

                                                                $stmt=$dbh->query($nuovaqueryest);
                                                                $enum_est=array();
                                                                if ($col['Null']=='YES') {
                                                                    $enum_est['']='';
                                                                }
                                                                $campiset[]=$col['Field'];
                                                                while ($row_est=$stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                    $enum_est[$row_est['id']]=$row_est['value'];
                                                                }
                                                                                ?>



                <div class="control-group">
                    <div class="controls">
                        <select name="<?php echo $col['Field'];?>[]" id="<?php echo $col['Field'];?>" multiple class="form-control" data-rel="chosen" <?php if ($col['readonly']=='si') echo "disabled";?>>
                                                           <?php foreach ($enum_est as $en=>$val) : ?>
                                                                    <option <?php if (in_array($en,$valoriattuali)) {  echo "SELECTED"; } ?> value="<?php echo $en;?>"><?php echo $val;?></option>
                                                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                                                                <?php
                                                                  //continue; // ? non capisco perché il continue
                                                            endif;

                                                            /* (f) cerco le chiavi esterne multiple*/

                                                                        if (!(($col['chiaveesternamultipla']) or ($col['chiaveesterna']))) :

                                                                        switch (getTipoColonna($col)) {


                                                                        /* (i) ----------------------- COLORPICKER ----------------------- */

                                                                        case 'COLORPICKER' : //è scritto nel commento del campo


                                                                            if ($elemento[$col['Field']]=='') {
                                                                                $elemento[$col['Field']]=$DEFAULT[$modulo['id_modulo']][$col['Field']];
                                                                            }

                                                                            ?>


                                                                            <div class="input-group colorpicker colorpicker-component">
                                                                                <input type="text" value="<?php echo $elemento[$col['Field']];?>" class="form-control"
                                                                                       name="<?php echo $col['Field'];?>" id="<?php echo $col['Field'];?>"
                                                                                       value="<?php echo $elemento[$col['Field']];?>" />
                                                                                <span class="input-group-addon"><i></i></span>
                                                                            </div>
                                                                            <?php
                                                                            break;

                                                                        /* (f) ----------------------- COLORPICKER ----------------------- */

																		/* (i) ----------------------- INTEGER ----------------------- */

																		case 'INTEGER' : //mettere se è integer un counter
																		?>

																		<div class="input-group">
                                                                        <input  class="col-xs-12 form-control" type="text" <?php if ($col['readonly']=='si') echo "readonly";?>
																		name="<?php echo $col['Field'];?>" id="<?php echo $col['Field'];?>"
																		value="<?php echo $elemento[$col['Field']];?>" />
                                                                                                    <span class="input-group-addon">
                                                                                                        <i class="fa fa-sort-numeric-asc"></i>
                                                                                                    </span>
																		</div>

																		<?php
																		break;

																		/* (f) ----------------------- INTEGER ----------------------- */

																		    /* (i) ----------------------- NUMERIC ----------------------- */

																		case 'NUMERIC' : //mettere se è integer un counter
																		?>

																		<div class="input-group">
																		<input  class="col-xs-12 form-control" type="text" <?php if ($col['readonly']=='si') echo "readonly";?>
																		name="<?php echo $col['Field'];?>" id="<?php echo $col['Field'];?>"
																		value="<?php echo $elemento[$col['Field']];?>" />
                                                                                                    <span class="input-group-addon">
                                                                                                        <i class="fa fa-sort-numeric-asc"></i>
                                                                                                    </span>
																		</div>

																		<?php
																		break;

																		    /* (f) ----------------------- NUMERIC ----------------------- */

																		    /* (i) ----------------------- DECIMAL ----------------------- */

																		case 'DECIMAL' : //mettere se è integer un counter
																		?>

																		<div class="input-group">
																		<input  class="col-xs-12 form-control" type="text" <?php if ($col['readonly']=='si') echo "readonly";?>
																		name="<?php echo $col['Field'];?>" id="<?php echo $col['Field'];?>"
																		value="<?php echo $elemento[$col['Field']];?>" />
																			<span class="input-group-addon">
                                                                                <i class="glyphicon glyphicon-euro"></i>
																			</span>
																		</div>

																		<?php
																		break;

																		    /* (f) ----------------------- DECIMAL ----------------------- */

																		    /* (i) ----------------------- DATETIME ----------------------- */

																		case 'DATETIME':
																		?>

																		<div class="input-group">
                                                                            <?php $datetimepickervalue=$elemento[$col['Field']];?>
																			<input  <?php if ($col['readonly']=='si') echo "readonly";?> id="<?php echo $col['Field'];?>" name="<?php echo $col['Field'];?>" type="text" class="datetimepicker form-control" value="<?php echo TODDMMYYYYHHiiss($datetimepickervalue);?>" />
																			<span class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
																			</span>
																		</div>

																		<?php
																		break;

																		    /* (f) ----------------------- DATETIME ----------------------- */


                                                                            /* (i) ----------------------- DATE ----------------------- */

                                                                        case 'DATE':
                                                                            ?>

                                                                        <div class="input-group">
                                                                            <?php $datetimepickervalue=$elemento[$col['Field']];?>
                                                                            <input  <?php if ($col['readonly']=='si') echo "readonly";?> id="<?php echo $col['Field'];?>" name="<?php echo $col['Field'];?>" type="text" class="datepicker form-control" value="<?php echo TODDMMYYYY($datetimepickervalue);?>" />
                                                                            <span class="input-group-addon">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </span>
                                                                        </div>

                                                                        <?php
                                                                        break;

                                                                            /* (f) ----------------------- DATE ----------------------- */


                                                                            /* (i) ----------------------- TIME ----------------------- */

                                                                            case 'TIME':
                                                                                ?>
                                                                                <div class="input-group bootstrap-timepicker timepicker">
                                                                                    <?php $datetimepickervalue=$elemento[$col['Field']];?>
                                                                                    <input  <?php if ($col['readonly']=='si') echo "readonly";?> id="<?php echo $col['Field'];?>" name="<?php echo $col['Field'];?>" type="text" class="timepicker_interno form-control" value="<?php echo $datetimepickervalue;?>" />
                                                                            <span class="input-group-addon">
                                                                                <i class="glyphicon glyphicon-time"></i>
                                                                            </span>
                                                                                </div>

                                                                                <?php
                                                                                break;

                                                                            /* (f) ----------------------- TIME ----------------------- */

                                                                            /* (i) ----------------------- ENUM ----------------------- */

																		case 'ENUM':

																		$enumvalues=getEnumValues($modulo['nome_tabella'],$col['Field']);
																		?>
                                                                        <?php
                                                                            if (count($enumvalues)<1) {
																		?>

                                                                        <div class="radio">
																			<?php foreach ($enumvalues as $en) : ?>
																				<label>
																					<input  name="<?php echo $col['Field'];?>" type="radio" value="<?php echo $en;?>" class="ace" <?php if ($col['readonly']=='si') echo "disabled";?> <?php if ($elemento[$col['Field']]==$en) {  echo "checked"; } ?>/>
																					<span class="lbl"> <?php echo $en;?></span>
																				</label>&nbsp;&nbsp;&nbsp;&nbsp;
																			<?php endforeach; ?>
																		</div>

																		<?php
																		} else { ?>

																			<select  class="chosen-select form-control" id="<?php echo $col['Field'];?>" name="<?php echo $col['Field'];?>" <?php if ($col['readonly']=='si') echo "disabled";?>  data-placeholder="<?php echo $col['Field'];?>">
																			<?php foreach ($enumvalues as $en) : ?>
																			<option <?php if ($elemento[$col['Field']]==$en) {  echo "selected"; } ?> value="<?php echo $en;?>"><?php echo $en;?></option>
																			<?php endforeach; ?>
																			</select>


																		<?php
																		} ?>


																		<?php
																		break;

																		/* (f) ----------------------- ENUM ----------------------- */

                                                                        /* (i) ----------------------- SET ----------------------- */

                                                                            case 'SET':

                                                                                $campiset[]=$col['Field'];
                                                                                $setvalues=getSetValues($modulo['nome_tabella'],$col['Field']);
                                                                                $arraycheckbox=explode(",",$elemento[$col['Field']]);
                                                                                ?>

                                                                                <div class="checkbox">
                                                                                    <?php foreach ($setvalues as $en) : ?>
                                                                                        <label>
                                                                                            <input  name="<?php echo $col['Field'];?>[]" type="checkbox" value="<?php echo $en;?>" class="ace" <?php if ($col['readonly']=='si') echo "disabled";?> <?php if (in_array($en,$arraycheckbox)) {  echo "checked"; } ?>/>
                                                                                            <span class="lbl"> <?php echo $en;?></span>
                                                                                        </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                    <?php endforeach; ?>
                                                                                </div>

                                                                                <?php
                                                                                break;

                                                                        /* (f) ----------------------- SET ----------------------- */

                                                                        /* (i) ----------------------- TEXT ----------------------- */

																		case 'TEXT':
																		?>

																	<?php if ($col['Traducibile']=='si') { // (1 inizio) if then else campo traducibile ?>
																		<div class="tab-content" style="height:70px;padding:10px;">
																		<?php $ll=0;foreach ($lingue as $lang) : ?>
																			<div id="panel-<?php echo $col['Field'];?>-<?php echo $lang;?>" class="tab-pane fade panel-<?php echo $lang;?> <?php if ($ll==0) { ?>in active <?php } ?>">


																		<div class="input-group">
																		<input  class="col-xs-12 form-control" type="text" <?php if ($col['readonly']=='si') echo "readonly";?>
																			   name="<?php echo $col['Field'];?>-<?php echo $lang;?>" id="<?php echo $col['Field'];?>-<?php echo $lang;?>"
																		value="<?php echo $elemento[$col['Field']][$lang];?>" />
                                                                                                    <span class="input-group-addon">
                                                                                                        <i class="fa fa-sort-alpha-asc"></i>
                                                                                                    </span>
                                                                        </div>

																			</div>

																		<?php $ll++;endforeach; ?>
																		</div>

																	</div><!-- chiudo tabbable -->

																	<?php } else {  // (2) if then else campo traducibile ?>

																		<div class="input-group">
																		<input  class="col-xs-12 form-control" type="text" <?php if ($col['readonly']=='si') echo "readonly";?>
																		name="<?php echo $col['Field'];?>" id="<?php echo $col['Field'];?>"
																		value="<?php echo $elemento[$col['Field']];?>" />
                                                                                                    <span class="input-group-addon">
                                                                                                        <i class="fa fa-sort-alpha-asc"></i>
                                                                                                    </span>
                                                                        </div>

																	<?php } // (3 fine) if then else campo traducibile ?>

																		<?php
																		break;

																		/* (f) ----------------------- TEXT ----------------------- */

																		/* (i) ----------------------- TEXTAREA ----------------------- */

																		case 'TEXTAREA': $textarea++;
																		?>


																	<?php if ($col['Traducibile']=='si') { // (1 inizio) if then else campo traducibile ?>
																		<div class="tab-content" style="height:360px;padding:10px;">
																		<?php $ll=0;foreach ($lingue as $lang) : ?>
																			<div id="panel-<?php echo $col['Field'];?>-<?php echo $lang;?>" class="tab-pane fade panel-<?php echo $lang;?> <?php if ($ll==0) { ?>in active <?php } ?>">
                                                                                <?php if ($col['readonly']=='si') { ?>
                                                                                     <div class="col-xs-12" ><?php echo $elemento[$col['Field']][$lang];?></div>
                                                                                <?php } else { ?>
                                                                                    <textarea  name="<?php echo $col['Field'];?>-<?php echo $lang;?>" id="<?php echo $col['Field'];?>-<?php echo $lang;?>" class="textarea col-xs-12 ckeditortextarea" ><?php echo $elemento[$col['Field']][$lang];?></textarea>
                                                                                <?php }?>

																			</div>

																		<?php $ll++;endforeach; ?>
																		</div>
																	</div><!-- chiudo tabbable -->

																	<?php } else {  // (2) if then else campo traducibile ?>

                                                                                <?php if ($col['readonly']=='si') { ?>
                                                                                    <div class="col-xs-12" ><?php echo $elemento[$col['Field']];?></div>
                                                                                <?php } else { ?>
                                                                                    <textarea  name="<?php echo $col['Field'];?>" id="<?php echo $col['Field'];?>" class="textarea col-xs-12 ckeditortextarea"><?php echo $elemento[$col['Field']];?></textarea>
                                                                                <?php }?>

																	<?php } // (3 fine) if then else campo traducibile ?>

																		<?php
																		break;

																		/* (f) ----------------------- TEXTAREA ----------------------- */


																		?>


																		<?php
																		} //end switch


																		endif; ?>
																	</div>
																</div>
																<div style="clear:both;"></div>
																<div class="space-4"></div>
															</div>
														</div>

														<?php
														endforeach;
														?>

                                                    </div><!-- prima colonna -->
                                                    <div class="row col-xs-12 col-sm-1" ></div><!--spazio intermedio-->
                                                    <div class="row col-xs-12 col-sm-<?php echo $colsmsecondacolonna;?>" ><!-- seconda colonna -->

                                                    <?php if (($modulo['note']=='si') or (count($note)>0)) : ?>
                                                    <div class="row border-dashed">
        												<div class="col-xs-12 col-sm-12">
                                                            <div class="box-inner">
                                                            <div class="box-header well">
                                                                <h2><?php echo _("Note");?></h2>
                                                            </div>

                                                            <div class="box-content">
                                                                <div class="row col-xs-12 col-sm-12">
                                                                    <?php if (count($note)>0) : ?>
                                                                        <div id="accordion1" class="accordion-style1 panel-group">
                                                                            <?php foreach ($note as $n) : ?>
                                                                                <div class="panel panel-default">
                                                                                    <div class="panel-heading">
                                                                                        <h4 class="panel-title">
                                                                                            <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse1<?php echo $n['id'];?>">
                                                                                                <i class="ace-icon fa fa-angle-right bigger-110" data-icon-hide="ace-icon fa fa-angle-down" data-icon-show="ace-icon fa fa-angle-right"></i><?php echo _("Nota del ");?> <?php echo $n['data_nota'];?>
                                                                                            </a>
                                                                                        </h4>
                                                                                    </div>

                                                                                    <div class="panel-collapse collapse" id="collapse1<?php echo $n['id'];?>">
                                                                                        <div class="panel-body">
                                                                                            <?php echo $n['nota'];?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php if ($modulo['note']=='si') : ?>
                                                                <?php /* (i) Nuova nota */ ?>
                                                                <div class="form-group">
                                                                    <label><span class="label label-info"><?php echo _("Inserisci una nota");?></span></label>
                                                                    <div>
                                                                        <textarea class="col-sm-12" name="modulo_nota" id="modulo_nota"></textarea>
                                                                    </div>
                                                                </div>
                                                                    <div style="clear:both;"></div>
                                                                    <div class="space-4"><br/></div>
                                                                <?php /* (f) Nuova nota */ ?>
                                                            <?php endif;?>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>




                                                        <?php /* (i) ---------------------- Attivita ---------------- */ ?>
                                                    <?php if ($idele>0 and $idmod==80) {
                                                            $righeprogrammazione=array();
                                                            $query2="SELECT * FROM pcs_programmazione where id_attivita_primaria=$idele";
                                                            $stmt2=$dbh->query($query2);
                                                            while ($row2=$stmt2->fetch(PDO::FETCH_ASSOC)) {
                                                                $righeprogrammazione[]=$row2;
                                                            }
                                                            //echo "<pre>";
                                                            //print_r($righeprogrammazione);
                                                            //echo "</pre>";
                                                        ?>


                                                        <?php if ($elemento['id_attivita_primaria']>0) { ?>

                                                        <?php } else { ?>

                                                        <h3>PROGRAMMA QUESTA ATTIVITA</h3>

                                                        <div class="row dashed">
                                                            <div class="col-xs-12">
                                                                <table class="table-striped table table-bordered responsive">
                                                                    <thead>
                                                                    <tr>
                                                                        <th> # </th>
                                                                        <?php for ($i=1;$i<8;$i++) : ?>
                                                                            <th><?php echo $giorno[$i];?></th>
                                                                        <?php endfor;?>
                                                                        <th>CADENZA</th>
                                                                        <th>DAL GIORNO</th>
                                                                        <th>AL GIORNO</th>
                                                                        <th> <button i="1" type="button" class="aggiungiriga btn btn-warning btn-secondary"> <i class="fa fa-plus"></i> </button>
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php for ($ii=1;$ii<$MAXRIGHEPROGRAMMAZIONE;$ii++) :
                                                                        if ($ii<=count($righeprogrammazione)) {
                                                                            $valori=$righeprogrammazione[$ii-1];
                                                                        } else {
                                                                            $valori=array();
                                                                        }
                                                                        ?>

                                                                        <tr <?php if (((count($righeprogrammazione)>0) && ($ii>count($righeprogrammazione))) || ((count($righeprogrammazione)==0) && ($ii>1))) { ?> style="display:none;" <?php  } ?>id="rigaprodotto_<?php echo $ii;?>" data-index="<?php echo $ii;?>" class="rigaprodotto_<?php echo $ii;?>" <?php if ($ii==1 or $ii<=count($righeprogrammazione)) { } else { ?>  <?php } ?> >
                                                                            <td> <?php echo $ii;?> </td>
                                                                            <?php for ($i=1;$i<8;$i++) : ?>
                                                                                <td>
                                                                                    <?php
                                                                                    $checked='';
                                                                                    if ($valori[strtolower($giorno[$i])]==1) {
                                                                                        $checked="CHECKED";
                                                                                    }?>
                                                                                    <input <?php echo $checked;?> class="checkgiorno_<?php echo $ii;?>" type="checkbox" <?php echo $checked;?> name="checkgiorno_<?php echo $ii;?>_<?php echo $i;?>" id="checkgiorno_<?php echo $ii;?>_<?php echo $i;?>" value="1" />
                                                                                </td>
                                                                            <?php endfor;?>
                                                                            <td>
                                                                                <select class="form-control" name="cadenza_<?php echo $ii;?>" id="cadenza_<?php echo $ii;?>">
                                                                                    <option <?php if ($valori['cadenza']=='Mai') { echo "SELECTED"; } ?> value="0"> MAI </option>
                                                                                    <option <?php if ($valori['cadenza']=='ogni settimana') { echo "SELECTED"; } ?> value="1"> ogni settimana</option>
                                                                                    <option <?php if ($valori['cadenza']=='ogni 2 settimane') { echo "SELECTED"; } ?> value="2"> ogni 2 settimane</option>
                                                                                    <option <?php if ($valori['cadenza']=='ogni 3 settimane') { echo "SELECTED"; } ?> value="3"> ogni 3 settimane</option>
                                                                                    <option <?php if ($valori['cadenza']=='ogni 4 settimane') { echo "SELECTED"; } ?> value="4"> ogni 4 settimane</option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control datepicker" id="dal_giorno_<?php echo $ii;?>" name="dal_giorno_<?php echo $ii;?>" value="<?php echo convertDate($valori['dal_giorno']);?>"/>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control datepicker" id="al_giorno_<?php echo $ii;?>" name="al_giorno_<?php echo $ii;?>" value="<?php echo convertDate($valori['al_giorno']);?>"/>
                                                                            </td>
                                                                            <td><div class="btn-group" role="group" aria-label="Aggiungi/Togli ">
                                                                                    <button i="<?php echo $ii+1;?>" type="button" class="aggiungiriga btn btn-warning btn-secondary"> <i class="fa fa-plus"></i> </button>
                                                                                    <button i="<?php echo $ii;?>" type="button" class="togliriga    btn btn-danger btn-secondary"> <i class="fa fa-minus"></i> </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endfor; ?>

                                                                    <!--<tr>
                                                                        <td colspan="11" style="text-align:center;">
                                                                            <a class="btn btn-small btn-primary" id="genera_attivita_manualmente">GENERA ATTIVITA <br/>per i prossimi <?php echo $GIORNIPROGRAMMAZIONE;?> giorni</a>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td colspan="11" style="text-align:center;">
                                                                            <a class="btn btn-small btn-danger" id="rimuovi_attivita">RIMUOVI Dalla <br/> Programmazione</a>
                                                                        </td>
                                                                    </tr>-->
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <?php } ?>

                                                    <?php } ?>
                                                    <?php /* (f) ---------------------- Attivita ---------------- */ ?>




												<?php /* (i) ---------------------- files ---------------- */ ?>
												<?php if ($idele>0 and $modulo['allegati_possibili']=='si') : ?>

                                                    <?php if ($modulo['max_files_immagini']) : ?>
														<div class="row border-dashed">
															<div class="col-xs-12 col-sm-12">
																<div class="box-inner">
																	<div class="box-header well">
																		<h2><?php echo _("Images");?></h2>
																	</div>

																	<div class="box-content">
																		<div class="widget-main">

																				<?php
																				$files=array();
																				$files=getFiles($idele,$modulo['nome_tabella'],'immagine',$modulo['max_files_immagini']);
																				if (count($files)>0) : ?>
																				<ul id="immagini-sortable" class="sortable">
																					<?php
																					foreach ($files as $f) :
																						$pezzi=explode("/",$f['file']);
																						$tmp=$pezzi[count($pezzi)-1];
																						$pezzi[count($pezzi)-1]=$resizes[0]['prefisso']."_".$resizes[0]['width']."_".$resizes[0]['height']."_".$tmp;
																						$nomefile=join("/",$pezzi);

																						foreach ($lingue as $lang):
																							$testif=getTestiTraducibili($GLOBAL_tb[files],$f['id_file'],$lang);
																							if (count($testif)>0) {
																								foreach ($testif as $key=>$value) :
																									$f[$key][$lang]=$value;
																								endforeach;
																							}
																						endforeach;
																					?>

																						<li class="file-sortable" id="file-<?php echo $f['id_file'];?>">
																						<div class="row">
																							<div class="col-xs-3">
																							<img class="img-responsive" style="padding-bottom:5px;" width="150" height="150" alt="150x150" src="<?php echo $sitoweb.$nomefile;?>"/>
																							</div>
																							<div class="col-xs-5">
																								<div class="tabbable">
																									<ul class="nav nav-tabs" >
																									<?php $ll=0;foreach ($lingue as $lang) : ?>
																										<li <?php if ($ll==0) { ?> class="active" <?php } ?> >
																											<a data-toggle="tab" class="tablang tablang-<?php echo $lang;?>" data-lang="<?php echo $lang;?>" href=".panel-<?php echo $lang;?>">
																												<?php echo strtoupper($lang);?>
																											</a>
																										</li>
																									<?php $ll++;endforeach; ?>
																									</ul>
																									<div class="tab-content" style="height:70px;padding:10px;">
																									<?php $ll=0;foreach ($lingue as $lang) : ?>
																										<div id="panelfile-<?php echo $f['id_file'];?>-<?php echo $lang;?>" class="tab-pane fade panel-<?php echo $lang;?> <?php if ($ll==0) { ?>in active <?php } ?>">

																									<input class="col-xs-12" type="text"
																									name="file-nome-<?php echo $f['id_file'];?>-<?php echo $lang;?>" id="file-nome-<?php echo $f['id_file'];?>-<?php echo $lang;?>"
																									value="<?php echo $f['nome'][$lang];?>" />

																										</div>

																									<?php $ll++;endforeach; ?>
																									</div>

																								</div><!-- chiudo tabbable -->

																							</div>
																							<div class="col-xs-2">
																							<?php if (!($viewmode)) { ?>
                                                                                                <a class="btn btn-danger cancellafile" idmodalmod="<?php echo $idmoduloFile;?>" idmodalele="<?php echo $f['id_file'];?>"><?php echo _("Delete");?></a>
                                                                                            <?php } ?>
																							</div>

																						</div>
																						<hr/>
																						</li>

																					<?php
																					endforeach;
																					?>
																				</ul>
																				<?php
																				endif;
																				?>
																			<div id="ajaxloaderplupload" style="display:none;">
																			<img src="assets/img/loading.gif"/>
																			</div>
																			<div id="uploader">
																			<div id="filelist"></div>
																			<br/>

                                                                            <?php if (!($viewmode)) : ?>
																			<?php if ($modulo['max_files_immagini']>0 and count($files)>=$modulo['max_files_immagini']) { } else { ?>
                                                                                <div id="responso_upload_immagini" class="alert-success" style="display:none;">OK!</div>
                                                                                    <a class="btn btn-large btn-info" id="pickfiles" href="#"><i class="glyphicon glyphicon-picture"></i>&nbsp;&nbsp;<?php echo _("Add images");?></a>

																			<?php } //if ($modulo['max_files']>0 and count($files)>=$modulo['max_files']) ?>
                                                                            <?php endif; //viewmode ?>

																			</div>


																	</div>
																	</div>
																</div>
															</div>
														</div>
                                                        <?php endif; ?>


                                                    <?php if ($modulo['max_files_allegati']) : ?>
														<div class="row border-dashed">
															<div class="col-xs-12 col-sm-12">
																<div class="box-inner">
																	<div class="box-header well">
																		<h2><?php echo _("Attachments");?></h2>
																	</div>

																	<div class="box-content">
																		<div class="widget-main">

																				<?php
																				$files_allegati=array();
																				$files_allegati=getFiles($idele,$modulo['nome_tabella'],'allegato',$modulo['max_files_allegati']);
																				if (count($files_allegati)>0) : ?>
																				<ul id="allegati-sortable" class="sortable">
																					<?php
																					foreach ($files_allegati as $f) :
																						foreach ($lingue as $lang):
																							$testif=getTestiTraducibili($GLOBAL_tb[files],$f['id_file'],$lang);
																							if (count($testif)>0) {
																								foreach ($testif as $key=>$value) :
																									$f[$key][$lang]=$value;
																								endforeach;
																							}
																						endforeach;
																					?>
																						<li class="file-sortable" id="file-<?php echo $f['id_file'];?>">

																						<div class="row">
																							<div class="col-xs-5">
																									<a target="_blank" href="<?php echo $sitoweb.$f['file'];?>"><i style="font-size:80px;" class="ace-icon glyphicon glyphicon-file bigger-230"></i></a>
                                                                                                <br/>
                                                                                                <?php if ($modulo['nome_modulo']=="CaricamentoDati") { ?>
                                                                                                    <?php $tokenarray['id_azienda']=$elemento['id_azienda'];?>
                                                                                                    <?php $tokenarray['anno']=$elemento['anno'];?>
                                                                                                    <?php $tokenarray['mese']=$elemento['mese'];?>
                                                                                                    <?php $token=base64_encode(json_encode($tokenarray));?>
                                                                                                    <?php $url=$sitedir."bilanciocaricato.php?token=".$token; ?>
                                                                                                    <br/>
                                                                                                    <a target="_blank" href="<?php echo $url;?>"><?php echo _("Vedi Conto Economico");?></a>
                                                                                                <?php } ?>
																							</div>
																							<div class="col-xs-5">
																								<div class="tabbable">
																									<ul class="nav nav-tabs" >
																									<?php $ll=0;foreach ($lingue as $lang) : ?>
																										<li <?php if ($ll==0) { ?> class="active" <?php } ?> >
																											<a data-toggle="tab" class="tablang tablang-<?php echo $lang;?>" data-lang="<?php echo $lang;?>" href=".panel-<?php echo $lang;?>">
																												<?php echo strtoupper($lang);?>
																											</a>
																										</li>
																									<?php $ll++;endforeach; ?>
																									</ul>
																									<div class="tab-content" style="height:70px;padding:10px;">
																									<?php $ll=0;foreach ($lingue as $lang) : ?>
																										<div id="panelfile-<?php echo $f['id_file'];?>-<?php echo $lang;?>" class="tab-pane fade panel-<?php echo $lang;?> <?php if ($ll==0) { ?>in active <?php } ?>">

																									<input class="col-xs-12" type="text"
																									name="file-nome-<?php echo $f['id_file'];?>-<?php echo $lang;?>" id="file-nome-<?php echo $f['id_file'];?>-<?php echo $lang;?>"
																									value="<?php echo $f['nome'][$lang];?>" />
																										</div>

																									<?php $ll++;endforeach; ?>
																									</div>

																								</div><!-- chiudo tabbable -->

																							</div>
																							<div class="col-xs-2">
                                                                                            <?php if (!($viewmode)) { ?>
    																							<a class="btn btn-danger cancellafile" idmodalmod="<?php echo $idmoduloFile;?>" idmodalele="<?php echo $f['id_file'];?>"><?php echo _("Delete");?></a>
                                                                                            <?php } ?>
																							</div>
																						</div>
																						<hr/>
																						</li>

																					<?php
																					endforeach;
																					?>
																				</ul>
																				<?php
																				endif;
																				?>



                                                                            <div id="ajaxloaderplupload_allegati" style="display:none;">
																			<img src="assets/img/loading.gif"/>
																			</div>
																			<div id="uploader_allegati">
																			<div id="filelist_allegati"></div>
																			<br/>


                                                                        <?php if (!($viewmode)) : ?>

																			<?php if ($modulo['max_files_allegati']>0 and count($files_allegati)>=$modulo['max_files_allegati']) { } else { ?>
                                                                                <div id="responso_upload_allegati" class="alert-success" style="display:none;">OK!</div>
																				<?php
																					if (count($files_allegati)>0) { ?>
																				<a class="btn btn-large btn-info" id="pickfiles_allegati" href="#"><i class="glyphicon glyphicon-paperclip"></i>&nbsp;&nbsp;<?php echo _("Add more attachments");?></a>
																				<?php } else { ?>
																				<a class="btn btn-large btn-info" id="pickfiles_allegati" href="#"><i class="glyphicon glyphicon-paperclip"></i>&nbsp;&nbsp;<?php echo _("Add attachments");?></a>
																				<?php }
																				 ?>
																				<?php } //if ($modulo['max_files']>0 and count($files)>=$modulo['max_files']) ?>
                                                                        <?php endif; //viewmode ?>

																			</div>


																		</div>
																	</div>
																</div>
															</div>
														</div>
                                                        <?php endif; ?>

												<?php endif; ?>
												<?php /* (f) ---------------------- files ---------------- */ ?>

                                                    <?php // aggiungo ora i campi set ?>
                                                    <input type="hidden" name="elencocampiset" id="elencocampiset" value="<?php echo join(",",$campiset);?>" />
                                                </form>
                                                    </div><!-- seconda colonna -->
                                                                    <div style="clear:both;"></div>
                                                                    <div class="space-4"></div>

<?php /* (f) ------------------------------------------ Generazione form in base ai campi della tabella -------------------------------------------- */?>

		<script type="text/javascript">
			jQuery(function($) {

        $(document).on('click', ".delete-elemento",function(){
            var idele=$(this).attr("idmodalele");
            var idmod=$(this).attr("idmodalmod");
            bootbox.confirm("<?php echo _('Sicuro di voler eliminare questo elemento?');?>", function(result) {
                if (result) {
                    $.post("ajax_delete_elemento.php", { idmod: idmod, idele: idele } , function(msg){$("#responso").html(msg);} );
                    setTimeout(function(){location.reload();}, 2000);
                }
            });
        });

                /* Add events */
                window.responsiveTables.init();

                $(".aggiungiriga").click(function(){

                    var i=$(this).attr("i");
                    $("#rigaprodotto_"+i).show();
                    $(".sceglipersona").chosen({allow_single_deselect: true,disable_search_threshold: 10});


                });
                $(".togliriga").click(function(){
                    var i=$(this).attr("i");
                    console.log("i="+i);
                    $("#rigaprodotto_"+i).hide();
                    $("#cadenza_"+i).val(0); //in questo modo è come se lo "cancellassi" perché nel form se c'è 0 non viene scritto

                });

                $(".tutticheckati").click(function(){
                    var areaid=$(this).attr("data-index");

                    var checkBoxes = $("input[class=checkgiorno_"+areaid);
//                            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
                    checkBoxes.prop("checked", true);
                })
                $(".tuttivuoti").click(function(){
                    var areaid=$(this).attr("data-index");
                    var checkBoxes = $("input[class=checkgiorno_"+areaid);
//                            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
                    checkBoxes.prop("checked", false);
                })

            <?php if ($viewmode==1) { ?>
                $("#nuovo_elemento_close").show();
                $("#nuovo_elemento_save").hide();
            <?php } else { ?>
                $("#nuovo_elemento_close").show();
                $("#nuovo_elemento_save").show();
            <?php }  ?>

                $('[data-rel="chosen"],[rel="chosen"]').chosen({allow_single_deselect: true ,disable_search_threshold: 10});
                $('.chosen-select').chosen({disable_search_threshold: 10, allow_single_deselect: true });



                <?php //-------------------------------------------------------------------------------------------------------------- ?>
                <?php if ($modulo['nome_modulo']=='Experiments' || $modulo['nome_modulo']=='UPLOAD EXPERIMENTAL DATA' ) : ?>

                function loadFieldOfTestLevel2FromFieldOfTest(idfieldoftest) {
                    if (idfieldoftest>0) {
                        var params={};
                        params.idfieldoftest=idfieldoftest;
                        $.ajax({
                            dataType: "json",
                            type: 'POST',
                            url: "ajax_getFieldOfTestLevel2FromFieldOfTest.php",
                            data: jQuery.param(params),
                            success: function (data) {
                                console.log(data);
                                if (data.result==true) {
                                    $('#Field_of_test_level2').children('option').remove();
                                    if (data.modelli.length>1) {
                                        $('#Field_of_test_level2').append("<option value='0'>Please choose one</option>");
                                    }
                                    for (i=0;i<data.modelli.length;i++) {
                                        var modello=data.modelli[i];
                                        var key=modello.id;
                                        var value=modello.field_of_test_level2;
                                        $('#Field_of_test_level2').append("<option value='"+key+"'>"+value+"</option>");
                                    }
                                    <?php if ($idele!='') { ?>
                                    $("#Field_of_test_level2").val(<?php echo $elemento['Field_of_test_level2'];?>);
                                    <?php } ?>
                                    $('#Field_of_test_level2').trigger("chosen:updated");
                                }
                                if (data.result==false) {
                                    alert("Errore caricamento dati!");
                                }
                            },
                            error: function (e) {
                                alert("Errore db!");
                            }
                        });

                    }
                }

                var idfieldoftest=$("#Field_of_test").val();
                //alert(idfieldoftest);
                loadFieldOfTestLevel2FromFieldOfTest(idfieldoftest);

                $("body").on("change", "#Field_of_test", function () {
                    var idfieldoftest=$("#Field_of_test").val();
                    $('#Field_of_test_level2').children('option').remove();
                    loadFieldOfTestLevel2FromFieldOfTest(idfieldoftest);
                });


                <?php endif; //nomemodulo==Experiments ?>
                <?php //-------------------------------------------------------------------------------------------------------------- ?>




                <?php //-------------------------------------------------------------------------------------------------------------- ?>
                <?php if ($modulo['nome_modulo']=='Servizi') : ?>

                var select, chosen;

                // cache the select element as we'll be using it a few times
                select = $("#nome_servizio");

                // init the chosen plugin
                select.chosen({ no_results_text: 'Press Enter to add new entry:' });

                // get the chosen object
                chosen = select.data('chosen');

                // Bind the keyup event to the search box input
                chosen.dropdown.find('input').on('keyup', function(e)
                {
                    // if we hit Enter and the results list is empty (no matches) add the option
                    if (e.which == 13 && chosen.dropdown.find('li.no-results').length > 0)
                    {
                        var option = $("<option>").val(this.value).text(this.value);

                        // add the new option
                        select.prepend(option);
                        // automatically select it
                        select.find(option).prop('selected', true);
                        // trigger the update
                        select.trigger("chosen:updated");
                    }
                });


                function caricaCampiDaServizio(nomeservizio) {
                    if (nomeservizio!='') {

                        var params={};
                        params.nomeservizio=nomeservizio;

                        $.ajax({
                            dataType: "json",
                            type: 'POST',
                            url: "ajax_leggiServizio.php",
                            data: jQuery.param(params),
                            success: function (data) {
                                console.log(data);
                                if (data.result==true) {
                                    var str=data.servizi.descrizione_servizio;

                                    $("#periodicita").val(data.servizi.periodicita);
                                    $('#periodicita').trigger("chosen:updated");
                                    $("#dalla_data").val(data.servizi.dalla_data);
                                    $("#alla_data").val(data.servizi.alla_data);

                                    if (!str || 0 === str.length) {
                                        CKEDITOR.instances['descrizione_servizio'].setData('');
                                    } else {
                                        CKEDITOR.instances['descrizione_servizio'].setData(str);
                                    }
                                }
                                if (data.result==false) {
                                    alert("Errore caricamento dati!");
                                }
                            },
                            error: function (e) {
                                alert("Errore db!");
                            }
                        });

                    }
                }

                var nomeservizio=$("#nome_servizio").val();
                //alert(nomeservizio);
                //inizializzazione
                caricaCampiDaServizio($("#nome_servizio").val());

                $("body").on("change", "#nome_servizio", function () {
                    var nomeservizio=$("#nome_servizio").val();
                    caricaCampiDaServizio($("#nome_servizio").val());
                });

                <?php endif; //nomemodulo==Servizi ?>
                <?php //-------------------------------------------------------------------------------------------------------------- ?>


                <?php //-------------------------------------------------------------------------------------------------------------- ?>
                <?php if ($modulo['nome_modulo']=='Cantieri') : ?>


                function caricaCampiDaCliente(idCliente) {
                    if (idCliente!='') {

                        var params={};
                        params.idCliente=idCliente;
                        console.log(params);

                        $.ajax({
                            dataType: "json",
                            type: 'POST',
                            url: "ajax_leggiCliente.php",
                            data: jQuery.param(params),
                            success: function (data) {
                                console.log(data);
                                if (data.result==true) {
                                    $("#codiceCliente").val(data.cliente.codiceCliente);
                                }
                                if (data.result==false) {
                                    alert("Errore caricamento dati!");
                                }
                            },
                            error: function (e) {
                                alert("Errore db!");
                            }
                        });

                    }
                }

                var idCliente=$("#id_cliente").val();
                //alert(nomeservizio);
                //inizializzazione
                if (idCliente>0) {
                    caricaCampiDaCliente(idCliente);
                }

                $("body").on("change", "#id_cliente", function () {
                    var idCliente=$("#id_cliente").val();
                    caricaCampiDaCliente(idCliente);
                });

                <?php endif; //nomemodulo==Cantieri ?>
                <?php //-------------------------------------------------------------------------------------------------------------- ?>


                $("#nuovi_video").click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    $("#video_add").show();
                });
                $("#addvideo").click(function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    $(".btn-save").attr('disabled','disabled');
                    var idmod=<?php echo $idmod;?>;
                    var idele="<?php echo $idele;?>";
                    var videorisorsa=$("#videorisorsa-new").val();
                    $.post("ajax_addvideo.php", { idele: idele, idmod: idmod, risorsa:videorisorsa } , function(msg){$("#responso_upload_video").html(msg);} );
                    setTimeout(function(){location.reload();}, 2000);
                });

                $('.tablang').on('shown.bs.tab', function (e) {
                    var lingua=$(this).attr('data-lang');
                    //rimuove active da tutte le linguette
                    $('.nav-tabs > li.active').removeClass('active');
                    //attiva tutte le linguette in lingua giusta
                    $(".tablang-"+lingua).parent().addClass('active');
                });
//                $(".tablang").click(function(){
//                    var lingua=$(this).attr('data-lang');
//                    //rimuove active da tutte le linguette
//                    $('.nav-tabs > li.active').removeClass('active');
//                    //attiva tutte le linguette in lingua giusta
//                    $(".tablang-"+lingua).parent().addClass('active');
//                });


// (i) checkbox all e none per campi set e chiaviesterne
$(".checkbox_all").click(function(){
    var fieldname=$(this).attr('fieldname');
    $(".checkbox_"+fieldname).prop('checked', true);
});
$(".checkbox_none").click(function(){
    var fieldname=$(this).attr('fieldname');
    $(".checkbox_"+fieldname).prop('checked', false);
});
// (f) checkbox all e none per campi set e chiaviesterne

// (i) delete file

				$(".cancellafile").click(function(){
					var idele=$(this).attr("idmodalele");
					var idmod=$(this).attr("idmodalmod");
					bootbox.confirm("<?php echo _('Are you sure?');?>", function(result) {
					  if (result) {
						$.post("ajax_delete_elemento.php", { idmod: idmod, idele: idele } , function(msg){$("#responso").html(msg);} );
					  	setTimeout(function(){location.reload();}, 2000);
					  }
					});
				});

// (f) delete file


// (i) ---------------- ----------------- plupload immagini ------------------- ------------------ -----------------
	var multiselection=true;
	var uagent = navigator.userAgent.toLowerCase();
		if (
		(uagent.match(/ipad/i)) 		||
		(uagent.match(/iphone/i)) 		||
		(uagent.match(/android/i))		||
		(uagent.match(/blackberry/i))	||
		(uagent.match(/webos/i))
		) {
			multiselection=false;
		}
	var uploader = new plupload.Uploader({
		unique_names : true,
		multi_selection: multiselection,
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		browse_button : 'pickfiles',
		container : 'uploader',
		max_file_size : '20mb',
		url : 'upload.php?tipofile=immagine&idele=<?php echo $idele;?>&nome_tabella=<?php echo $modulo['nome_tabella'];?>',
		flash_swf_url : 'plupload/js/plupload.flash.swf',
		silverlight_xap_url : 'plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpeg,jpg,gif,png"}
		],
		resize : {width : <?php echo $widthmax;?>, height : <?php echo $heightmax;?>, quality : 90}
	});

	//uploader.bind('Init', function(up, params) {
	//	$('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
	//});

//	$('#uploadfiles').click(function(e) {
//		uploader.start();
//		e.preventDefault();
//	});

	uploader.init();

	uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$("#ajaxloaderplupload").show();
			$('#filelist').append(
				'<div id="' + file.id + '">' +
				file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
			'</div>');
		});
		up.refresh(); // Reposition Flash/Silverlight
		//ora le carico subito
		uploader.start();
	});

	uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
	});

	uploader.bind('Error', function(up, err) {
		$('#filelist').append("<div>Error: " + err.code +
			", Message: " + err.message +
			(err.file ? ", File: " + err.file.name : "") +
			"</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	uploader.bind('FileUploaded', function(up, file,info) {
		$('#' + file.id + " b").html("100%");
		var obj = JSON.parse(info.response);
        var filename=obj.cleanFileName;
		//$.get("inserisciFoto.php", { 'filename': filename, 'id': id, 'tipo': tipo });
	});

	uploader.bind('UploadComplete',function(){
		//alert("Caricamento Completato!");
		$("#ajaxloaderplupload").hide();
        $("#responso_upload_immagini").show();
        setTimeout(function() {
            location.reload();
        }, 2000);
   	});
// (f) ---------------- ----------------- plupload immagini ------------------- ------------------ -----------------


// (i) ---------------- ----------------- plupload allegati ------------------- ------------------ -----------------
	var multiselection=true;
	var uagent = navigator.userAgent.toLowerCase();
		if (
		(uagent.match(/ipad/i)) 		||
		(uagent.match(/iphone/i)) 		||
		(uagent.match(/android/i))		||
		(uagent.match(/blackberry/i))	||
		(uagent.match(/webos/i))
		) {
			multiselection=false;
		}
	var uploader_allegati = new plupload.Uploader({
		unique_names : true,
		multi_selection: multiselection,
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		browse_button : 'pickfiles_allegati',
		container : 'uploader_allegati',
		max_file_size : '20mb',
		url : 'upload.php?tipofile=allegato&idele=<?php echo $idele;?>&nome_tabella=<?php echo $modulo['nome_tabella'];?>',
		flash_swf_url : 'plupload/js/plupload.flash.swf',
		silverlight_xap_url : 'plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpeg,jpg,gif,png"},
			{title : "Zip files", extensions : "zip"},
			{title : "Office files", extensions : "doc,docx,ods,odt,xls,xlsx,csv"},
			{title : "Pdf files", extensions : "pdf"}
		],
		resize : {width : <?php echo $widthmax;?>, height : <?php echo $heightmax;?>, quality : 90} //parametri definiti a livello di file _parametri
	});

	//uploader_allegati.bind('Init', function(up, params) {
	//	$('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
	//});

//	$('#uploadfiles').click(function(e) {
//		uploader_allegati.start();
//		e.preventDefault();
//	});

	uploader_allegati.init();

	uploader_allegati.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$("#ajaxloaderplupload_allegati").show();
			$('#filelist_allegati').append(
				'<div id="' + file.id + '">' +
				file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
			'</div>');
		});
		up.refresh(); // Reposition Flash/Silverlight
		//ora le carico subito
		uploader_allegati.start();
	});

	uploader_allegati.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
	});

	uploader_allegati.bind('Error', function(up, err) {
		$('#filelist').append("<div>Error: " + err.code +
			", Message: " + err.message +
			(err.file ? ", File: " + err.file.name : "") +
			"</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	uploader_allegati.bind('FileUploaded', function(up, file,info) {
		$('#' + file.id + " b").html("100%");
		var obj = JSON.parse(info.response);
        var filename=obj.cleanFileName;
		//$.get("inserisciFoto.php", { 'filename': filename, 'id': id, 'tipo': tipo });
	});

	uploader_allegati.bind('UploadComplete',function(){
		//alert("Caricamento Completato!");
		$("#ajaxloaderplupload_allegati").hide();
        $("#responso_upload_allegati").show();
        setTimeout(function() {
            location.reload();
        }, 2000);
   	});
// (f) ---------------- ----------------- plupload allegati ------------------- ------------------ -----------------


    });


</script>

<?php

exit;
