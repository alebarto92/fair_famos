<?php
require_once("config.php");

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'pcs_clienti';
// Table's primary key
$primaryKey = 'id';
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'c.id', 'dt' => 0,'field' => 'id', 'formatter' => function ($d,$row) {
        global $dbh, $GLOBAL_tb;
        $queryAll="SELECT count(*) as tot FROM pcs_file WHERE id_elem=$d AND tb='pcs_clienti'";
        $stmt2=$dbh->query($queryAll);
        $rowAll=$stmt2->fetch(PDO::FETCH_ASSOC);
        $totAllegati=$rowAll['tot'];

        $querySedi="SELECT COUNT(pcs_sedi_clienti.id) AS tot FROM pcs_sedi_clienti WHERE id_cliente = $d";
        $stmtSedi=$dbh->query($querySedi);
        $rowSedi=$stmtSedi->fetch(PDO::FETCH_ASSOC);
        $totSedi=$rowSedi['tot'];

        if ($totAllegati>0) {
            $button='<button idmodalele="'.$d.'" idmodalmod="9" class="aprimodal-allegati btn btn-info" type="button"> <i class=" glyphicon glyphicon-paperclip "></i> '.$totAllegati.'</button>';
        } else {
            $button='<button idmodalele="'.$d.'" idmodalmod="9" class="btn btn-danger" type="button"> <i class=" glyphicon glyphicon-paperclip "></i> '.$totAllegati.' </button>';
        }

        if ($_SESSION['pcs_id_cliente']>0) {
            return('
<div class="dropdown">
    <div class="btn-group">
        <div class="btn-group" style="text-align:center;">
            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> <i class=" glyphicon glyphicon-wrench "></i> <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li class="dropdown-header">Cantieri</li>
                <li><a data-toggle="tooltip" title="Elenco" href="module.php?modname=Cantieri&amp;p[id_cliente]='.$d.'" data-original-title="Cantieri">ELENCO <span class="badge">'.$totSedi.'</span></a></li>   
            </ul>
        </div>'.$button.'
    </div>
</div>
');
        } else {
            return('
<div class="dropdown">
    <div class="btn-group">
        <div class="btn-group" style="text-align:center;">
            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> <i class=" glyphicon glyphicon-wrench "></i> <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li class="dropdown-header">Clienti</li>
                <li><a href="get_element.php?debug=0&amp;idmod=9&amp;idele='.$d.'" data-toggle="tooltip" title="" class="green" data-original-title="Edit"><i class="glyphicon glyphicon-edit"></i> EDIT</a></li>
                <li class="red"><a data-toggle="tooltip" title="Delete" class="red delete-elemento" idmodalmod="9" idmodalele="'.$d.'" href="#" ><i class="glyphicon glyphicon-trash"></i> DELETE</a></li>

                <li class="divider"></li>
                <li class="dropdown-header">Cantieri</li>
                <li><a href="get_element.php?idele=-1&amp;debug=0&amp;idmod=10&amp;k=id_cliente-'.$d.'" data-toggle="tooltip" title="Altra sede" data-original-title="Altra Sede">NUOVO </a></li>
                <li><a data-toggle="tooltip" title="Elenco" href="module.php?modname=Cantieri&amp;p[id_cliente]='.$d.'" data-original-title="Cantieri">ELENCO <span class="badge">'.$totSedi.'</span></a></li>   
            </ul>
        </div>'.$button.'
    </div>
</div>
');
        }
    }),    array( 'db' => 'c.id', 'dt' => 1,'field' => 'id'),
    array( 'db' => 'Cognome', 'dt' => 2,'field' => 'Cognome'),
    array( 'db' => 'Nome', 'dt' =>3,'field' => 'Nome'),
    array( 'db' => 'c.Indirizzo','AS'=>'Indirizzo', 'dt' => 4,'field' => 'Indirizzo'),
    array( 'db' => 'c.CAP', 'dt' => 5,'field' => 'CAP'),
    array( 'db' => 'c.NomeAzienda', 'dt' => 6,'field' => 'NomeAzienda'),
    array( 'db' => 'c.TelLavoro', 'dt' => 7,'field' => 'TelLavoro'),
    array( 'db' => 'c.Cell', 'dt' => 8,'field' => 'Cell'),
    array( 'db' => 'c.Referenti', 'dt' => 9,'field' => 'Referenti'),
    array( 'db' => '(SELECT COUNT(pcs_sedi_clienti.id) FROM pcs_sedi_clienti WHERE id_cliente = c.id)', 'AS'=>'totSedi', 'dt' => 10,'field' => 'totSedi'),

);

$sql_details = array(
    'user' => $user,
    'pass' => $pass,
    'db'   => $database,
    'host' => $host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
// require( 'ssp.class.php' );
require('ssp.customized.class.php' );

$joinQuery = "FROM `pcs_clienti` AS `c` ";
$extraWhere = "";
$groupBy = "";
$having = "";

$extraWhere = "";
$groupBy = "";
$having = "";

if ($_GET['f']) {
    foreach ($_GET['f'] as $key=>$value) {
        list($alias,$campo)=explode("-",$key);
        $valori=str_replace("|","','",$value);
        $where[]="`".$alias."`.`".$campo."` IN ('".$valori."')";
    }
}

if ($_GET['fd']) {
    foreach ($_GET['fd'] as $key=>$value) {
        list($alias,$campo)=explode("-",$key);

        list($da,$a)=explode("|",$value);
        list($d1,$m1,$y1)=explode("/",$da);
        $datada=$y1."-".$m1."-".$d1." 00:00";
        list($d2,$m2,$y2)=explode("/",$a);
        $dataa=$y2."-".$m2."-".$d2." 23:59";

        $where[]="(`".$alias."`.`".$campo."` BETWEEN '".$datada."' AND '".$dataa."')";
    }
}


if ($utente['id_cliente']>0) {
    $where[]="c.id=".$utente['id_cliente'];
}


if (count($where)>0) {
    $extraWhere=join(" AND ",$where);
}


if (count($where)>0) {
    $extraWhere=join(" AND ",$where);
}



echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
);






?>
