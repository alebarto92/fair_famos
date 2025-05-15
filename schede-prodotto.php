<?php $tipofile=$_GET['tipofile'];?>
<?php if ($tipofile!='allegato') {
    header("Location: index.php");
    exit;
} ?>
<?php $nomescheda['allegato']="Schede Prodotto";?>
<?php include("INC_10_HEADER.php");?>
<?php include("INC_15_SCRIPTS.php");?>
<body>
<?php include("INC_20_NAVBAR.php");?>
<div class="container" id="firstcontainer">
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a>
            </li>
            <li>
                <?php echo $nomescheda[$tipofile];?>
            </li>
        </ul>
    </div>

    <div class="page-header">
        <h2>
            <i class="fa fa-paperclip"></i> <?php echo $nomescheda[$tipofile];?>
        </h2>
    </div><!-- /.page-header -->

    <?php
        $schedeprodotto=array();
        $query="SELECT *, pcs_testi.valore as nome FROM pcs_file LEFT JOIN pcs_testi ON pcs_file.id_file=pcs_testi.id_ext and pcs_testi.table_ext='pcs_file' AND pcs_testi.lang='it' AND pcs_testi.chiave='nome' WHERE tb='pcs_clienti' AND tipo_file=? AND (id_elem=?  OR id_elem=1)";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($tipofile,$_SESSION['pcs_id_cliente']));
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $schedeprodotto[]=$row;
        }
    ?>

    <?php if (count($schedeprodotto)>0) : ?>

        <table class="table table-bordered" style="background-color:white;">
            <thead>
            <tr>
                <th>preview</th>
                <th>Link</th>
                <th>Descrizione File</th>
                <th>Data Aggiornamento</th>
            </tr>

            </thead>
            <?php foreach ($schedeprodotto as $p) : ?>
            <tr>
                <td><img src="<?php echo $p['file'];?>.jpg" style="width:80px;"/></td>
                <td><a target="_blank" href="<?php echo $p['file'];?>"><?php echo $p['file'];?></a></td>
                <td><?php echo $p['nome'];?></td>
                <td><?php echo $p['data_inserimento'];?></td>
            </tr>

            <?php endforeach; ?>
        </table>

    <?php else : ?>

        <?php echo _("Nessuna scheda prodotto allegata");?>

    <?php endif; ?>

</div>
<hr>
<?php include("INC_90_FOOTER.php");?>

</body>
</html>
