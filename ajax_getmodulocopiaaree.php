<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idele=$_REQUEST['idele'];
$idcliente=$_REQUEST['idcliente'];

//query per la lettura dei campi della polizza
$query="SELECT pcs_clienti.id as idcliente,CAST(CONCAT('ID ',pcs_clienti.id,' ',pcs_clienti.nome,' ', pcs_clienti.cognome)  AS CHAR) as nomecliente,pcs_sedi_clienti.* FROM pcs_sedi_clienti JOIN pcs_clienti ON pcs_clienti.id=pcs_sedi_clienti.id_cliente order by idcliente,sede";

$sedi=array();
if ($stmt=$dbh->query($query)) {
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['id']==$idele) {
            $sedearrivo=$row;
        } else {
            $sedi[]=$row;
        }
    }
    if (count($sedi)==0) {
        echo "NON CI SONO ALTRE SEDI PER QUESTO CLIENTE!";
        exit;
    }
?>
    <form id="copiaareeform">
        <div class="row text-center">
            <div class="col-xs-12">
                <div class="form-group">
                    <label>Sede da cui copiare le aree</label>
                    <select class="form-control chosen-select" id="sedepartenza">
                        <?php foreach ($sedi as $value) : ?>
                            <option value="<?php echo $value['id'];?>"><?php echo $value['nomecliente'];?> - <?php echo $value['sede'];?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label>Sede a cui copiare le aree</label>
                    <select class="form-control chosen-select" id="sedearrivo">
                        <option value="<?php echo $sedearrivo['id'];?>"><?php echo $sedearrivo['sede'];?></option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group">
                    <label>&nbsp;</label><br/>
                    <a id="copiaareesubmit" class="btn btn-success btn-large">COPIA</a>
                </div>
            </div>
        </div>
    </form>
<?php } else {
    echo "ERRORE! SEDI NON PRESENTI!";
    exit;
}


exit;