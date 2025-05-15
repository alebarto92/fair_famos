<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$saveandreload=$_REQUEST['saveandreload'];
$saveandreload=1;

$testifile=array();

sanitate($_POST);

foreach ($_POST as $key=>$value) {
    //cerco i campi dei testi dei files
    if (substr($key,0,9)=="file-nome") {
        list($trash1,$trash2,$fileid,$lang)=explode("-",$key);
        $testifile[$fileid][$lang]=$value;
    }
}

if ($_POST['elencocampiset']) {
    $campiset=explode(",",$_POST['elencocampiset']);
    if (count($campiset)>0) {
        foreach ($campiset as $cs) {
			if ($_POST['cs']) {
				$valori=join(",",$_POST[$cs]);
				$_POST[$cs]=$valori;
			}
        }
    }
}

$campiobbligatori=explode(",",$_POST['elencocampiobbligatori']);

$idmod=$_REQUEST['idmod'];
$idele=$_REQUEST['idele'];

if ($idmod>0) {
	$modulo=getModulo($idmod);
	$campi_readonly=explode(",",$modulo['campi_readonly']);
	$campi_nascosti=explode(",",$modulo['campi_nascosti']);
} else {
		echo "Modulo vuoto!";
		return false;
		exit;
}

$errore=array();


if ($idmod==80 and $idele!='') {

    //cancello la programmazione di questa attivita primaria

    $querydel="DELETE FROM pcs_programmazione where id_attivita_primaria=?";
    $stmtdel=$dbh->prepare($querydel);
    $stmtdel->execute(array($idele));

    for ($ii=1;$ii<$MAXRIGHEPROGRAMMAZIONE;$ii++) :

        $dalgiorno =validateDate($_POST["dal_giorno_$ii"]);
        $algiorno  =validateDate($_POST["al_giorno_$ii"]);
        $cadenza   =$_POST["cadenza_$ii"];
        for ($i=1;$i<8;$i++) {
            $ppp="checkgiorno_$ii";
            if ($_POST[$ppp."_".$i]==1){
                $checkgiorno[$i]=1;
            } else {
                $checkgiorno[$i]=0;
            }
        }

        if ($cadenza>0) {
            $query="INSERT INTO pcs_programmazione (lun,mar,mer,gio,ven,sab,dom,cadenza,dal_giorno,al_giorno,id_attivita_primaria) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $stmt=$dbh->prepare($query);
            //echo $query;
            //echo json_encode(array($checkgiorno[1],$checkgiorno[2],$checkgiorno[3],$checkgiorno[4],$checkgiorno[5],$checkgiorno[6],$checkgiorno[7],$cadenzavalue[$cadenza],$dalgiorno,$algiorno,intval($idele)));
            if ($stmt->execute(array($checkgiorno[1],$checkgiorno[2],$checkgiorno[3],$checkgiorno[4],$checkgiorno[5],$checkgiorno[6],$checkgiorno[7],$cadenzavalue[$cadenza],$dalgiorno,$algiorno,intval($idele)))) {

            } else {
                $errore[]="Errore inserimento dati programmazione attività";
            }
        }
    endfor;
}


//ORA BISOGNA CREARE l'ELENCO DEI CAMPI!
$elencocampikeys=explode(",",$_POST['elencocampi']);
foreach ($elencocampikeys as $field) {

    //(i) MODIFICA ESSENZIALE!!!!
    //se il campo è una chiave multipla allora qui arriva un array, dobbiamo ridurlo invece a elenco di valori con la ,
    if (is_array($_POST[$field])) {
        //remove empty values...
        // Filtering the array
        $_POST[$field] = array_filter($_POST[$field]);
        //join with ","
        $_POST[$field]=join(",",$_POST[$field]);
    }
    //(f) MODIFICA ESSENZIALE!!!!

    $elencocampivalues[$field]=$_POST[$field];
    if ($elencocampivalues[$field]=='' AND in_array($field,$campiobbligatori)) :
        $err['nome']=$field;
        $errore[]=$err;
    endif;
}
//ora verifico i campi traducibili obbligatori
		if ($_POST['elencocampitraducibili']!='') :
			$campitraducibili=explode(",",$_POST['elencocampitraducibili']);
			foreach ($campitraducibili as $nome_campo) :
				if (in_array($nome_campo,$campiobbligatori)):
				foreach ($lingue as $lang) :
					$tmp=$nome_campo."-".$lang;
					$valore=$_POST[$tmp];
					$err=array();
					$err['nome']=$nome_campo;
					$err['lang']=$lang;
					if ($valore=='') $errore[]=$err;
				endforeach;
				endif;
			endforeach;
		endif;
 /*
if (count($errore)>0) :
	?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
  		<strong>Attenzione!</strong>Campi obbligatori non compilati!<br/>
  		I seguenti campi devo essere compilati correttamente:
  		<ul>
  		<?php foreach ($errore as $err) { ?>
  		<li>
  		<?php echo $err['nome'];?>
  		<?php if ($err['lang']) echo "(".$err['lang'].")"; ?>
  		</li>
  		<?php } ?>
  		</ul>
  		</div>
</div>
<?php
exit;
endif;
 */

$permessi=permessi($idmod,$utente['id_ruolo'],$superuserOverride);

//mysql_insert_id()

if ($idele==-1) { //Crea nuovo elemento
	if ($permessi['Can_create']=='si') {
	} else { ?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
        <?php echo _("<strong>Attenzione!</strong>Non hai i permessi per creare questo elemento!");?>
  		</div>
</div>
    <script>
	setTimeout(function(){$(".registrazionerror").hide();}, 2000);
	</script>
	<?php
		exit;
	}

} else { //Update Elemento
	if ($permessi['Can_update']=='si') {

	} else { ?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
  		<?php echo _("<strong>Attenzione!</strong>Problema modifica elemento (1)!");?>
  		</div>
</div>
    <script>
	setTimeout(function(){$(".registrazionerror").hide();}, 2000);
	</script>
	<?php
		exit;
	}
}

if (1) {
//iniziamo una transazione
    $dbh->beginTransaction();

    //vediamo se ci sono i campi "unici"
    $campi_unici=explode(",",$modulo['campi_unici']);
    if (count($campi_unici)>0) {

    }

    $INSERIMENTO=false;
    if ($idele==-1) {
        $INSERIMENTO=true;
        //il campo ordine deve essere incrementato di 1
        $newordine=getLastPosition($modulo['nome_tabella']);
        $newordine++;
        $chiavicampi[0]='ordine';
        $valoricampi[0]=$newordine;
        $valoricampivuoti[0]='?';

        //inserimento
        foreach ($elencocampivalues as $key=>$value) {
            if ($key=='') continue;
            $value=validateDate($value); //se è una data in formato d/m/Y la converto in Y-m-d altrimenti rimane la stringa che ho passato
            $chiavicampi[]=$key;
            if (in_array($key,$campiobbligatori) or $value!='') {
                $valoricampi[]=$value;
            } else {
                $valoricampi[]=NULL;
            }
            $valoricampivuoti[]='?';
        }
        $listachiavicampi=join(",",$chiavicampi);
        $listavaloricampivuoti=join(",",$valoricampivuoti);

        $queryinsert="INSERT INTO ".$modulo['nome_tabella']." (".$listachiavicampi.") VALUES(".$listavaloricampivuoti.")";

        $stmt=$dbh->prepare($queryinsert);
        $stmt->execute($valoricampi);

        if (!($stmt)) {
            setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - INSERT",$queryinsert . "---". json_encode($valoricampi));
            $dbh->rollBack();
            ?>
            <div class="registrazionerror alert alert-danger" role="alert">
                <div class="center">
                    <?php echo _("<strong>Attenzione!</strong>Problema salvataggio elemento!");?>
                </div>
            </div>
            <script>
                setTimeout(function(){$(".registrazionerror").hide();}, 2000);
            </script>
            <?php
            exit;
        } else {

            $idele=$dbh->lastInsertId();

        }

    } else {
        //modifica
        $set = array();
        foreach ($elencocampivalues as $key => $value) {
            if ($key == '') continue;
            $value=validateDateTime($value); //se è una data in formato d/m/Y la converto in Y-m-d altrimenti rimane la stringa che ho passato
            $value=validateDate($value); //se è una data in formato d/m/Y la converto in Y-m-d altrimenti rimane la stringa che ho passato
            $setnuovo[]="$key=?";

            if (in_array($key,$campiobbligatori) or $value!='') {
                $valorisetnuovo[]=$value;
            } else {
                $valorisetnuovo[]=NULL;
            }
        }
        $comandoset = join(",", $setnuovo);
        if ($comandoset != '') {

            $queryupdate = "UPDATE " . $modulo['nome_tabella'] . " SET " . $comandoset . " WHERE " . $modulo['chiaveprimaria'] . "='" . $idele . "'";

            $stmt = $dbh->prepare($queryupdate);
            $stmt->execute($valorisetnuovo);

            if (!($stmt)) {
                setNotificheCRUD("admWeb", "ERROR", "ajax_modifica_elemento.php - UPDATE", $queryupdate . "---". json_encode($valorisetnuovo));
                $dbh->rollBack();
                ?>
                <div class="registrazionerror alert alert-danger" role="alert">
                    <div class="center">
                        <?php echo _("<strong>Attenzione!</strong>Problema modifica elemento!"); ?>
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
        } else {
            $stmt=true;
        }
    }

	if ($stmt) {
	    include("creadirectoryallegati.php");


        //se siamo in Cantieri allora aggiorno il punteggio
        if ($idmod==10) {
            setNotificheCRUD("admWeb","INFO","ajax_modifica_elemento.php - punteggio",'aggiornapunteggio');

            $queryupdate=calcolaPunteggioCantiere($idele);
            //setNotificheCRUD("admWeb","INFO","ajax_modifica_elemento.php - punteggio",$queryupdate);

            if ($stmt=$dbh->query($queryupdate)) {
                setNotificheCRUD("admWeb","SUCCESS","ajax_modifica_elemento.php - punteggio",$queryupdate);

            } else {
                setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - punteggio",$queryupdate);
            }
        }

        //se sono in servizi oppure in attivita devo ricalcolare il punteggio del cantiere
        if ($idmod==69 or $idmod==14) { //SERVIZI o ATTIVITA
            $elemento=getElemento($idmod,$idele);
            $queryupdate=calcolaPunteggioCantiere($elemento['id_sede']);
            //setNotificheCRUD("admWeb","INFO","ajax_modifica_elemento.php - punteggio",$queryupdate);

            if ($stmt=$dbh->query($queryupdate)) {
                setNotificheCRUD("admWeb","SUCCESS","ajax_modifica_elemento.php - punteggio dopo modifica $idmod",$queryupdate);

            } else {
                setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - punteggio dopo modifica $idmod",$queryupdate);
            }
        }

        setNotificheCRUD("admWeb","SUCCESS","ajax_modifica_elemento.php - INSERT",$queryinsert . "---". json_encode($valoricampi));
        setNotificheCRUD("admWeb", "SUCCESS", "ajax_modifica_elemento.php - UPDATE", $queryupdate . "---". json_encode($valorisetnuovo));
		$erroreTransazione=false;
		//vediamo se ci sono i campi traducibili

		if ($_POST['elencocampitraducibili']) :
			$campitraducibili=explode(",",$_POST['elencocampitraducibili']);
			foreach ($campitraducibili as $nome_campo) :
				foreach ($lingue as $lang) :
					$tmp=$nome_campo."-".$lang;
					$valore=$_POST[$tmp];
					$queryTesti="REPLACE into ".$GLOBAL_tb['testi']." SET id_ext=$idele, table_ext='".$modulo['nome_tabella']."', lang='".$lang."',chiave='".$nome_campo."', valore=?";
                    $stmt = $dbh->prepare($queryTesti);
                    $stmt->execute(array($valore));
                    if (!($stmt)) {
						setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - REPLACE",$queryTesti);
						$erroreTransazione=true;
					}
				endforeach;
			endforeach;
		endif;

        //salviamo la nota se c'è
        if ($_POST['modulo_nota']) :
            $queryNote="INSERT INTO ".$GLOBAL_tb['note']." (id_user,table_ext,id_ext,nota,data_nota) VALUES (".$utente['id_user'].",'".$modulo['nome_tabella']."',".$idele.",'".$_POST['modulo_nota']."',NOW())";
            if (!($stmt=$dbh->query($queryNote))) {
                setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - INSERT NOTE:",$queryNote);
                $erroreTransazione=true;
            }
        endif;

		//salviamo anche i testi delle immagini e degli allegati
		if (count($testifile)>0) :
			foreach ($testifile as $fileid=>$tf) :
				foreach ($tf as $lang=>$valore):
					$queryTesti="REPLACE into ".$GLOBAL_tb['testi']." SET id_ext=$fileid, table_ext='".$GLOBAL_tb['files']."', lang='".$lang."',chiave='nome', valore=?";
                    $stmt = $dbh->prepare($queryTesti);
                    $stmt->execute(array($valore));
					if (!($stmt)) {
						setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - REPLACE",$queryTesti);
						$erroreTransazione=true;
					}
				endforeach;
			endforeach;
		endif;
        //veririchiamo anche il post process update
        $post_process_update=json_decode($modulo['post_process_update'],true);

		if (count($post_process_update)>0) {
            setNotificheCRUD("admWeb","INFO","ajax_modifica_elemento.php - post process update:",$modulo['post_process_update']);
			foreach ($post_process_update as $ppuquery) {
				$ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
				$ppuquery=str_replace("%TIMEZONE%",$TIMEZONE,$ppuquery);
				$ppuquery=str_replace("%id_user%",$utente['id_user'],$ppuquery);
                $ppuquery=str_replace("%id_cliente%",$utente['id_cliente'],$ppuquery);
                if (!($stmt=$dbh->query($ppuquery))) {
					setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - post process update:",$ppuquery);
					$erroreTransazione=true;
				} else {
					setNotificheCRUD("admWeb","SUCCESS","ajax_modifica_elemento.php - post process update:",$ppuquery);
				}
			}
		}

        if ($INSERIMENTO) :
            $post_process_insert=json_decode($modulo['post_process_insert'],true);
            if (count($post_process_insert)>0) {
                setNotificheCRUD("admWeb","INFO","ajax_modifica_elemento.php - post process insert:",$modulo['post_process_insert']);
                foreach ($post_process_insert as $ppuquery) {
                    $ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
					$ppuquery=str_replace("%TIMEZONE%",$TIMEZONE,$ppuquery);
					$ppuquery=str_replace("%id_user%",$utente['id_user'],$ppuquery);
                    $ppuquery=str_replace("%id_cliente%",$utente['id_cliente'],$ppuquery);
                    if (!($stmt=$dbh->query($ppuquery))) {
                        setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - post process insert:",$ppuquery);
                        $erroreTransazione=true;
                    } else {
                        setNotificheCRUD("admWeb","SUCCESS","ajax_modifica_elemento.php - post process insert:",$ppuquery);
                    }
                }
            }
        endif;

		if ($erroreTransazione==true) {
            setNotificheCRUD("admWeb","ERROR","ajax_modifica_elemento.php - errore transazione:",'Errore transazione');

            $dbh->rollBack();
		?>
		<div class="registrazionerror alert alert-danger" role="alert">
				<div class="center">
				<?php echo _("<strong>Attenzione!</strong> Problema modifica testi oppure post process update!");?>
				</div>
		</div>
			<script>
			setTimeout(function(){$(".registrazionerror").hide();}, 2000);
			</script>
		<?php
			exit;
		} else {
            $dbh->commit();
        }
?>

<div class="space6"></div>
<div class="registrazionesuccess alert alert-success" role="alert">
		<div class="center">
            <?php echo _("<strong>Complimenti!</strong> Inserimento effettuato con successo!");?><br/>
			<script>
				setTimeout(function(){
					$(".registrazionesuccess").hide();
					<?php if ($saveandreload) {
					    $modulotmp=getModulo($idmod);
					    if ($modulotmp['modulo_standard']=='no') { ?>
                    var aggiunta='';
                    var url='http://<?php echo $_SERVER[HTTP_HOST].$sitedir;?><?php echo $modulotmp['script_modulo'];?>';
                        <?php } else {

					        if ($idmod==80) { ?>
                                var idsede=$("#id_sede").val();
                                var aggiunta='&p[id_sede]='+idsede;

                            <?php } ?>
        					var url='http://<?php echo $_SERVER[HTTP_HOST].$sitedir;?>module.php?id=<?php echo $idmod;?>';
        				<?php
                        }
					    ?>
					window.location.href = url;
					<?php  } else { ?>
					window.location.href = $("#backurl").val();
					<?php } ?>
				}, 2000);
			</script>
  		</div>
</div>
<?php
	}
/*-------------------------------------------------------------------------------------------------*/
	else { //if ($rv)
?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
  		<?php echo _("<strong>Warning!</strong> You have to fill all the fields!");?>
  		</div>
</div>
    <script>
	//setTimeout(function(){$(".registrazionerror").hide();}, 2000);
	</script>
<?php
	}

}
exit;
?>
