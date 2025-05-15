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

if ($debug) {
    echo "viewmode:$viewmode";
    echo "<pre><h4>ajax_getdettaglioletturapolizza.php</h4>";
    print_r($_REQUEST);
    echo "</pre>";
}

if ($idmod>0 and $idele!='') {
	$modulo=getModulo($idmod);
    $campi_visibility_hidden=explode(",",$modulo['campi_visibility_hidden']);
    $campi_readonly=explode(",",$modulo['campi_readonly']);
    $campi_readonly_on_update=explode(",",$modulo['campi_readonly_on_update']);
	$campi_nascosti=explode(",",$modulo['campi_nascosti']);
    if ($modulo['note']=='si' and $idele>0) { //solo in caso di update vedo le note!
        $note=getNote($idmod,$idele,$utente['id_user']);
        if ($debug) { echo "<b>note:</b>"; print_r($note); echo "<br/><hr/>";}
    }

} else {
		setNotificheCRUD("admWeb","ERROR","ajax_getdettagliolettura.php","mod: $idmod, ele: $idele");
		echo "Modulo vuoto!";
		return false;
		exit;
}

$permessi=permessi($idmod,$utente['id_ruolo'],$superuserOverride);

//query per la lettura dei campi della polizza
$query="
SELECT CONCAT(BS_sw19_compagnie.nome_compagnia,' (',BS_sw19_compagnie.canale,')') as Compagnia,
CONCAT('<a target=\"_parent\" href=\"?modname=Clienti&p[id]=',CAST(BS_sw19_clienti.id AS CHAR),'\">',CAST(BS_sw19_clienti.Ragione_sociale_o_Nome AS CHAR),'</a>') as Cliente,
BS_sw19_polizze.*
FROM BS_sw19_polizze JOIN BS_sw19_clienti ON BS_sw19_polizze.id_cliente=BS_sw19_clienti.id JOIN BS_sw19_compagnie ON BS_sw19_polizze.id_compagnia=BS_sw19_compagnie.id WHERE BS_sw19_polizze.id=$idele";

if ($stmt=$dbh->query($query)) {

} else {
    echo "ERRORE! POLIZZA INESISTENTE!";
    exit;
}

$row=$stmt->fetch(PDO::FETCH_ASSOC);

$campidanascondere=array();
$campidanascondere[]="id";
$campidanascondere[]="ordine";
$campidanascondere[]="id_cliente";
$campidanascondere[]="id_compagnia";
$campidanascondere[]="ultimo_aggiornamento";
if ($row['frazionamento']=='annuale') {
    $campidanascondere[]="scadenza_rata_01";
    $campidanascondere[]="scadenza_rata_02";
    $campidanascondere[]="scadenza_rata_03";
    $campidanascondere[]="scadenza_rata_04";
    $campidanascondere[]="scadenza_rata_05";
    $campidanascondere[]="scadenza_rata_06";
    $campidanascondere[]="scadenza_rata_07";
    $campidanascondere[]="scadenza_rata_08";
    $campidanascondere[]="scadenza_rata_09";
    $campidanascondere[]="scadenza_rata_10";
    $campidanascondere[]="scadenza_rata_11";
}
if ($row['frazionamento']=='semestrale') {
    $campidanascondere[]="scadenza_rata_02";
    $campidanascondere[]="scadenza_rata_03";
    $campidanascondere[]="scadenza_rata_04";
    $campidanascondere[]="scadenza_rata_05";
    $campidanascondere[]="scadenza_rata_06";
    $campidanascondere[]="scadenza_rata_07";
    $campidanascondere[]="scadenza_rata_08";
    $campidanascondere[]="scadenza_rata_09";
    $campidanascondere[]="scadenza_rata_10";
    $campidanascondere[]="scadenza_rata_11";
}
if ($row['frazionamento']=='quadrimestrale') {
    $campidanascondere[]="scadenza_rata_03";
    $campidanascondere[]="scadenza_rata_04";
    $campidanascondere[]="scadenza_rata_05";
    $campidanascondere[]="scadenza_rata_06";
    $campidanascondere[]="scadenza_rata_07";
    $campidanascondere[]="scadenza_rata_08";
    $campidanascondere[]="scadenza_rata_09";
    $campidanascondere[]="scadenza_rata_10";
    $campidanascondere[]="scadenza_rata_11";
}
if ($row['frazionamento']=='trimestrale') {
    $campidanascondere[]="scadenza_rata_04";
    $campidanascondere[]="scadenza_rata_05";
    $campidanascondere[]="scadenza_rata_06";
    $campidanascondere[]="scadenza_rata_07";
    $campidanascondere[]="scadenza_rata_08";
    $campidanascondere[]="scadenza_rata_09";
    $campidanascondere[]="scadenza_rata_10";
    $campidanascondere[]="scadenza_rata_11";
}


/*
echo "<table class='table'>";
$i=0;
foreach ($row as $key=>$value) {
    if ($key=='id') continue;
    if ($key=='ordine') continue;
    if ($key=='id_cliente') continue;
    if ($key=='id_compagnia') continue;
    $i++;
    if ($i%2==1) {
        echo "<tr>";
    }
    echo "<td align='right'>$key:</td>";
    echo "<td align='left'>$value</td>";
    if ($i%2==0) {
        echo "</tr>";
    }
}
if ($i%2==1) {
    //chiudo l'ultimo tr
    echo "</tr>";
}
echo "</table>";
*/

?>
    <form class='form-horizontal'>
    <div class="form-group">
<?php foreach ($row as $key=>$value) {
    if (in_array($key,$campidanascondere)) continue;
    ?>
    <label class="col-xs-12 col-sm-3 control-label"><?php echo $key;?></label>
    <div class="col-sm-3">
      <p class="form-control-static"><?php echo convertDate($value);?>&nbsp;</p>
    </div>
<?php
}
?>
    </div>
</form>
<?php

foreach ($lingue as $lang):
    $testi=getTestiTraducibili($modulo['nome_tabella'],$idele,$lang);
    //if ($debug) { echo "<pre>"; print_r($testi); echo "</pre>"; }
	if (count($testi)>0) {
		foreach ($testi as $key=>$value) :
			$elemento[$key][$lang]=$value;
		endforeach;
	}
endforeach;

?>

<?php

exit;