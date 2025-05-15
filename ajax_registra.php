<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idmod=$_REQUEST['idmod'];
$idele=$_REQUEST['idele'];
$testo=$_REQUEST['testo'];

$modulo=getModulo($idmod);

if (!($idmod>0 and $idele!='')) {
    setNotificheCRUD("admWeb","ERROR","ajax_delete_elemento.php",$modulo['nome_modulo']." e idmod=".$idmod." e idele=".$idele);
    ?>
    <div class="registrazionerror alert alert-danger" role="alert">
        <div class="center">
            <strong>Attenzione!</strong>Problema registrazione visita!!
        </div>
    </div>
    <script>
        setTimeout(function(){$(".registrazionerror").hide();}, 2000);
    </script>
    <?php
    exit();
}

$permessi=permessi($idmod,$utente['id_ruolo']);

if (!($permessi['Can_update']=='si')) {
    setNotificheCRUD("admWeb","ERROR","ajax_registra.php",$modulo['nome_modulo']." niente permessi");
    ?>
    <div class="registrazionerror alert alert-danger" role="alert">
        <div class="center">
            <strong>Attenzione!</strong>Non hai i permessi per registrare questo elemento!
        </div>
    </div>
    <script>
        setTimeout(function(){$(".registrazionerror").hide();}, 2000);
    </script>
    <?php
    exit();
}

$comandoset="reg='si', testoregistrazione='".$testo."'";
$queryupdate = "UPDATE " . $modulo['nome_tabella'] . " SET " . $comandoset . " WHERE " . $modulo['chiaveprimaria'] . "='" . $idele . "'";
$stmt = $dbh->query($queryupdate);

if ($stmt) { ?>
    <div class="space6"></div>
    <div class="registrazionesuccess alert alert-success" role="alert">
        <div class="center">
            <strong>Congratulazioni!</strong> Elemento registrato!
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
            <strong>Attenzione!</strong>Problema registrazione elemento!
        </div>
    </div>
    <script>
        setTimeout(function(){$(".registrazionerror").hide();}, 2000);
    </script>
    <?php
}
?>