<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idmod=$_REQUEST['idmod'];
$idele=$_REQUEST['idele'];
$daData=$_REQUEST['daData'];
$aData=$_REQUEST['aData'];
$cadenza=$_REQUEST['cadenza'];

$el=getElemento($idmod,$idele);

$modulo=getModulo($idmod);

if (!($idmod>0 and $idele!='')) {
    setNotificheCRUD("admWeb","ERROR","ajax_replica.php",$modulo['nome_modulo']." e idmod=".$idmod." e idele=".$idele);
    ?>
    <div class="registrazionerror alert alert-danger" role="alert">
        <div class="center">
            <strong>Attenzione!</strong>Problema replica attività!!
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
    setNotificheCRUD("admWeb","ERROR","ajax_replica.php",$modulo['nome_modulo']." niente permessi");
    ?>
    <div class="registrazionerror alert alert-danger" role="alert">
        <div class="center">
            <strong>Attenzione!</strong>Non hai i permessi per replicare questo elemento!
        </div>
    </div>
    <script>
        setTimeout(function(){$(".registrazionerror").hide();}, 2000);
    </script>
    <?php
    exit();
}

//in base alle date si fanno le repliche da data a data
$datainiziale=date('Y-m-d', strtotime($daData));
$datafinale=date('Y-m-d', strtotime($aData));

if ($datafinale<$datainiziale) {
    ?>
    <div class="registrazionerror alert alert-danger" role="alert">
        <div class="center">
            <strong>Attenzione!</strong>La data finale è precedente alla data iniziale!
        </div>
    </div>
    <script>
        setTimeout(function(){$(".registrazionerror").hide();}, 2000);
    </script>
<?php
    } else {

    $datasuccessiva=$datainiziale;
    $datasuccessiva=date('Y-m-d', strtotime($datasuccessiva. " + ".$cadenza." DAYS"));
    while ($datasuccessiva<$datafinale) :
        //iniziamo inserendo il primo
        $query = "INSERT INTO pcs_attivita_clean (operatore1, operatore2, operatore3, operatore4, id_sede, ordine, data_consigliata, ora_consigliata_inizio, ora_consigliata_fine,  stato) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array(
                $el['operatore1'],
            $el['operatore2'],
            $el['operatore3'],
            $el['operatore4'],
            $el['id_sede'],
            1,
            $datasuccessiva,
            $el['ora_consigliata_inizio'],
            $el['ora_consigliata_fine'],
            'da_fare'
        ));
        $datasuccessiva=date('Y-m-d', strtotime($datasuccessiva. " + ".$cadenza." DAYS"));


    endwhile;


}


if ($stmt) { ?>
    <div class="space6"></div>
    <div class="registrazionesuccess alert alert-success" role="alert">
        <div class="center">
            <strong>Congratulazioni!</strong> Replicazione avvenuta!
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
            <strong>Attenzione!</strong>Problema replicazione elemento!
        </div>
    </div>
    <script>
        setTimeout(function(){$(".registrazionerror").hide();}, 2000);
    </script>
    <?php
}
?>