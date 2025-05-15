<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idmod=$_REQUEST['idmod'];
$idele=$_REQUEST['idele'];

$modulo=getModulo($idmod);

if (!($idmod>0 and $idele!='')) {
    setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento.php",$modulo['nome_modulo']." e idmod=".$idmod." e idele=".$idele);
    ?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
  		<strong>Error!</strong>There was a problem during deleting operation!
  		</div>
</div>
    <script>
	setTimeout(function(){$(".registrazionerror").hide();}, 2000);
	</script>
<?php
	exit();
}

$permessi=permessi($idmod,$utente['id_ruolo']);

if (!($permessi['Can_delete']=='si')) {
    setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento.php",$modulo['nome_modulo']." niente permessi");
    ?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
  		<strong>Error!</strong>You can't delete this element!
  		</div>
</div>
    <script>
	setTimeout(function(){$(".registrazionerror").hide();}, 2000);
	</script>
<?php
	exit();
}

//veririchiamo anche il pre process delete
$pre_process_delete=json_decode($modulo['pre_process_delete'],true);

setNotificheCRUD("admWeb","INFO","ajax_delete_elemento.php - pre process delete:",$modulo['pre_process_delete']);

if (count($pre_process_delete)>0) {
    foreach ($pre_process_delete as $ppuquery) {
        $ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
        //echo "<pre>";
        //echo $ppuquery;
        //echo "</pre>";
        if (!($dbh->query($ppuquery))) {
            setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento.php - pre process delete:",$ppuquery);
            $erroreTransazione=true;
        } else {
            setNotificheCRUD("admWeb","SUCCESS","ajax_delete_elemento.php - pre process delete:",$ppuquery);
        }
    }
}

$elemento=getElemento($idmod,$idele);

$query="DELETE FROM ".$modulo['nome_tabella']." WHERE ".$modulo['chiaveprimaria']."=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($idele));

$query2="DELETE FROM ".$GLOBAL_tb['testi']." WHERE id_ext='$idele' AND table_ext='".$modulo['nome_tabella']."'";
$stmt2=$dbh->query($query2);

$query3="DELETE FROM ".$GLOBAL_tb['files']." WHERE id_elem='$idele' AND tb='".$modulo['nome_tabella']."'";
$stmt3=$dbh->query($query3);

$query4="DELETE FROM ".$GLOBAL_tb['note']." WHERE id_ext='$idele' AND table_ext='".$modulo['nome_tabella']."'";
$stmt4=$dbh->query($query4);

//veririchiamo anche il pre process delete
$post_process_delete=json_decode($modulo['post_process_delete'],true);
setNotificheCRUD("admWeb","INFO","ajax_delete_elemento.php - pre process delete:",$modulo['post_process_delete']);

if (count($post_process_delete)>0) {
    foreach ($post_process_delete as $ppuquery) {
        $ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
        if (!($dbh->query($ppuquery))) {
            setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento.php - post process delete:",$ppuquery);
            $erroreTransazione=true;
        } else {
            setNotificheCRUD("admWeb","SUCCESS","ajax_delete_elemento.php - post process delete:",$ppuquery);
        }
    }
}

//veririchiamo anche il post process update
$post_process_update=json_decode($modulo['post_process_update'],true);
setNotificheCRUD("admWeb","INFO","ajax_delete_elemento.php - post process update:",$modulo['post_process_update']);

if (count($post_process_update)>0) {
    foreach ($post_process_update as $ppuquery) {
        $ppuquery=str_replace("%chiaveprimaria%",$idele,$ppuquery);
        if (!($dbh->query($ppuquery))) {
            setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento.php - post process update:",$ppuquery);
            $erroreTransazione=true;
        } else {
            setNotificheCRUD("admWeb","SUCCESS","ajax_delete_elemento.php - post process update:",$ppuquery);
        }
    }
}

	if ($stmt) {

        ?>
<div class="space6"></div>
<div class="registrazionesuccess alert alert-success" role="alert">
		<div class="center">
  		<strong>Congratulations!</strong> Element has been deleted!
    <script>
	setTimeout(function(){
		$(".registrazionesuccess").hide();
	}, 2000);
	</script>
  		</div>
</div>
<?php
	} else {
?>
<div class="registrazionerror alert alert-danger" role="alert">
		<div class="center">
  		<strong>Attenzione!</strong>Problema cancellazione elemento!
  		</div>
</div>
    <script>
	setTimeout(function(){$(".registrazionerror").hide();}, 2000);
	</script>
<?php
	}
?>
