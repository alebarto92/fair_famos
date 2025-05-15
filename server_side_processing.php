<?php
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	include("config.php");


//to read parameters passed by php page
$file = 'ajaxlog.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Rewrite data
$current = date("Y-m-d H:i:s")."\r\n";
$current .= json_encode($_REQUEST)."\r\n";
// Write the contents back to the file
file_put_contents($file, $current);


//"order":[{"column":"4","dir":"asc"}],
//"start":"100","length":"50",
//"search":{"value":"0256","regex":"false"}

if ($_GET['modname']) {
    $idmod = getModuloFrom_nome_modulo($_GET['modname']);
}

if ($_GET['id']>0) {
    $idmod=$_GET['id'];
} else {
    $_GET['id']=$idmod;
}

$pars=$_GET['p'];
    $filtridate=$_GET['fd'];

	$modulo=getModulo($idmod);


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


//https://legacy.datatables.net/examples/server_side/server_side.html

/*
 * Filtering
 * NOTE this does not match the built-in DataTables filtering which does it
 * word by word on any field. It's possible to do here, but concerned about efficiency
 * on very large tables, and MySQL's regex functionality is very limited
 */
$sHaving = "";
if ( $_GET['search']['value'] != "" )
{
    $sHaving = "HAVING (";
    for ( $i=0 ; $i<count($_GET['columns']) ; $i++ )
    {
        if ($_GET['columns'][$i]['name']!='') {
            $sHaving .= "`".$_GET['columns'][$i]['name']."` LIKE '%".$_GET['search']['value']."%' OR ";
        }
    }
    $sHaving = substr_replace( $sHaving, "", -3 );
    $sHaving .= ') ';

    //$nuovaquery=str_replace("WHERE ","$sWhere ",$nuovaquery);

}

	/*
	 * Paging
	 */
	$sLimit = "";

	if ( isset($_GET['start']) && $_GET['length'] != -1 ) {
		$sLimit = "LIMIT ".intval($_GET['start']).", ".intval($_GET['length']);
	}



/*
* Ordering
*/

$sOrder =" ";
if ( isset($_GET['order']) && count($_GET['order']) ) {
    $orderBy = array();

    for ( $i=0, $ien=count($_GET['order']) ; $i<$ien ; $i++ ) {
        // Convert the column index into the column data property
        $columnIdx = intval($_GET['order'][$i]['column']);
        $requestColumn = $_GET['columns'][$columnIdx];

        $column = $columns[ $columnIdx ];

        if ( $requestColumn['orderable'] == 'true' ) {
            $dir = $_GET['order'][$i]['dir'] === 'asc' ?
                'ASC' :
                'DESC';

            $orderBy[] = '`'.$requestColumn['name'].'` '.$dir;
        }
    }

    $sOrder = 'ORDER BY '.implode(', ', $orderBy).' ';
}


//to read parameters passed by php page
$file = 'ajaxlog.txt';
// Open the file to get existing content
$current = file_get_contents($file);
$current .= $nuovaquery.$sHaving.$sOrder.$sLimit."\r\n";
// Write the contents back to the file
file_put_contents($file, $current);

//faccio la query per vedere il totale generale e il totale filtrato

	$rResult=$dbh->query($nuovaquery.$sHaving.$sOrder.$sLimit);

    $rResultWithoutLimit=$dbh->query($nuovaquery.$sHaving.$sOrder);

/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $dbh->query( $sQuery);

	while($row = $rResultFilterTotal->fetch(PDO::FETCH_NUM)) {
        $aResultFilterTotal[]=$row;
	}


	$iFilteredTotal = $aResultFilterTotal[0];

	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$modulo['nome_tabella'].".".$modulo['chiaveprimaria'].")
		FROM ". $modulo['nome_tabella'];

	$rResultTotal = $dbh->query( $sQuery ) ;
	while($row = $rResultTotal->fetch(PDO::FETCH_NUM)) {
        $aResultTotal[]=$row;
	}
	$iTotal = $aResultTotal[0];


	/*
	 * Output
	 */
	$output = array(
		"draw" => intval($_GET['sEcho']),
		"recordsTotal" => $iTotal,
		"recordsFiltered" => $iFilteredTotal,
		"data" => array()
	);


	while($aRow = $rResult->fetch(PDO::FETCH_ASSOC)) {
	    $ev=$aRow;
		$row=array();
        $row[]="#";


        //----------------------------------------------------------------------------------------------------------------
        //--                                                                                                            --
        //--                                                                                                            --
        //----- (i) -----------------------------P R I M A   C O L O N N A------------------------------------------------
        //--                                                                                                            --
        //--                                                                                                            --
        //----------------------------------------------------------------------------------------------------------------


        //https://stackoverflow.com/questions/11274354/use-a-variable-within-heredoc-in-php-sql-practice

        $str ='';


        //----------------------------------------------------------------------------------------------------------------
        //--                                                                                                            --
        //----- (i) ----------------------------- D R O P D O W N --------------------------------------------------------
        //--                                                                                                            --
        //----------------------------------------------------------------------------------------------------------------

        $str.=<<<EX
                                <div class="dropdown">
                                    <div class="btn-group">
                                        <div class="btn-group" style="text-align:center;">
                                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"> <i class=" glyphicon glyphicon-wrench "></i> <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
EX;

        //----------------------------------------------------------------------------------------------------------------
        //----- (i) -----------------------------E D I T   E   D E L E T E -----------------------------------------------
        //----------------------------------------------------------------------------------------------------------------

if ($modulo['aprimodal']=='si') {
    if ($permessi_modulo['Can_update']=='si') {
        $str.=<<<EX
                                                <li><a data-toggle="tooltip" title="Edit" class="green aprimodal-ele" idmodalmod="{$modulo['id_modulo']}" idmodalele="'{$ev['chiaveprimaria']}'"><i class="glyphicon glyphicon-edit"></i> EDIT</a></li>
EX;
}
 } else {
    if ($permessi_modulo['Can_update']=='si') {
        $str.=<<<EX
                                                <li><a href="get_element.php?debug={$_GET['debug']}&idmod={$modulo['id_modulo']}&idele='{$ev['chiaveprimaria']}'" data-toggle="tooltip" title="Edit" class="green"><i class="glyphicon glyphicon-edit"></i> EDIT</a></li>
EX;
    }
 }
if ($permessi_modulo['Can_delete']=='si') {
$str.=<<<EX
                                                <li class="red"><a data-toggle="tooltip" title="Delete" class="red delete-elemento" idmodalmod="{$modulo['id_modulo']}" idmodalele="{$ev['chiaveprimaria']}" href="#" ><i class="glyphicon glyphicon-trash"></i> DELETE</a></li>
EX;
}

        //----------------------------------------------------------------------------------------------------------------
        //----- (f) -----------------------------E D I T   E   D E L E T E -----------------------------------------------
        //----------------------------------------------------------------------------------------------------------------




        //----------------------------------------------------------------------------------------------------------------
        //----- (i) -----------------------------SPECIFICI PER MODULO      -----------------------------------------------
        //----------------------------------------------------------------------------------------------------------------



//(i) CAntieri
if (($modulo['nome_modulo']=='Cantieri') and ($permessi_modulo['Can_create']=='si')) { $tmpmod=getModulo(getModuloFrom_nome_modulo('Servizi'));
    if ($tmpmod['aprimodal'] == 'si') {
        $str .= <<<EX
        <li class="divider"></li>
				<li class="dropdown-header">Servizi</li>
        <li><a data-toggle="tooltip" title="Altro servizio" class="aprimodal-ele"  k="id_sede-{$ev['chiaveprimaria']}" nomemodalmod="{$tmpmod['nome_modulo']}" idmodalmod="{$tmpmod['id_modulo']}" idmodalele="-1" href="#" >NUOVO </a></li>
EX;
    } else {
        $str .= <<<EX
        <li class="divider"></li>
				<li class="dropdown-header">Servizi</li>
        <li><a href="get_element.php?idele=-1&debug={$_GET['debug']}&idmod={$tmpmod['id_modulo']}&k=id_sede-{$ev['chiaveprimaria']}" data-toggle="tooltip" title="Altro servizio">NUOVO </a></li>
EX;
    }
}
if (($modulo['nome_modulo']=='Cantieri')) {
    $tmpmod = getModulo(getModuloFrom_nome_modulo('Servizi'));
    $totsedi = 0;
    $querysedi = "SELECT count(*) as tot FROM pcs_servizi where id_sede=" . $ev['chiaveprimaria'];
    $stmtreg = $dbh->query($querysedi);
    $rowsedi = $stmtreg->fetch(PDO::FETCH_ASSOC);
    $totsedi = $rowsedi['tot'];
    if ($totsedi == 0) {
        $str .= <<<EX
        <li class="disabled"><a data-toggle="tooltip" title="Servizi" >ELENCO <span class="badge">{$totsedi}</span></a></li>
EX;
    } else {
        $str .= <<<EX
        <li><a data-toggle="tooltip" title="Servizi" href="module.php?modname=Servizi&p[id_sede]={$ev['chiaveprimaria']}">ELENCO <span class="badge">{$totsedi}</span></a></li>
EX;
    }
    if ($permessi_modulo['Can_update'] == 'si'):
        $str .= <<<EX
        <li><a class="aprimodal-dettagli"  idcliente="{$ev['id_cliente']}" idmodalele="{$ev['chiaveprimaria']}" data-toggle="tooltip" title="Copia Servizi" >COPIA SERVIZI </a></li>
EX;
    endif;

        $str .= <<<EX
    <li class="divider"></li>
    <li class="dropdown-header">Attivita</li>
EX;
    $totsedi = 0;
    $querysedi = "SELECT count(*) as tot FROM pcs_attivita_clean where id_sede=" . $ev['chiaveprimaria'];
    $stmtreg = $dbh->query($querysedi);
    $rowsedi = $stmtreg->fetch(PDO::FETCH_ASSOC);
    $totsedi = $rowsedi['tot'];

    if ($totsedi == 0) {
        $str .= <<<EX
        <li class="disabled"><a data-toggle="tooltip" title="Attività" >ELENCO <span class="badge">{$totsedi}</span></a></li>
EX;
    } else {
        $str .= <<<EX
        <li><a data-toggle="tooltip" title="Attività" href="module.php?modname=AttivitaClean&p[id_sede]={$ev['chiaveprimaria']}">ELENCO <span class="badge">{$totsedi}</span></a></li>
EX;
    }
}
//(f) CAntieri


//(i) Experiments
if (($modulo['nome_modulo']=='Experiments' || $modulo['nome_modulo']=='UPLOAD EXPERIMENTAL DATA') ) {
    $str.=<<<EX
    <li><a data-toggle="tooltip" title="Load Time Series Data File " target="_blank" href="loadtimeseriesfile.php?idexperiment={$ev['chiaveprimaria']}"><i class="glyphicon glyphicon-list-alt"></i> LOAD FILE</a></li>
EX;
$str .= <<<EX
<li><a data-toggle="tooltip" title="Time Series Definition" href="module.php?modname=Time_series_definition&p[id_experiment]={$ev['chiaveprimaria']}"><i class="glyphicon glyphicon-list-alt"></i> TIME SERIES DEFINITION<span class="badge">{$totsedi}</span></a></li>
EX;

}

//(f) Experiments


        //----------------------------------------------------------------------------------------------------------------
        //----- (f) -----------------------------SPECIFICI PER MODULO      -----------------------------------------------
        //----------------------------------------------------------------------------------------------------------------

$str.=<<<EX
                                            </ul>
                                        </div>
EX;

        //----------------------------------------------------------------------------------------------------------------
        //--                                                                                                            --
        //----- (f) ----------------------------- D R O P D O W N --------------------------------------------------------
        //--                                                                                                            --
        //----------------------------------------------------------------------------------------------------------------


        //----------------------------------------------------------------------------------------------------------------
        //--                                                                                                            --
        //----- (i) -----------------------------A L L E G A T I ---------------------------------------------------------
        //--                                                                                                            --
        //----------------------------------------------------------------------------------------------------------------


                                        if ($modulo['allegati_possibili']=='si') :
                                            $queryAll="SELECT count(*) as tot FROM ".$GLOBAL_tb['files']." WHERE id_elem=".$ev['chiaveprimaria']." AND tb='".$modulo['nome_tabella']."'";
                                            $stmt2=$dbh->query($queryAll);
                                            $rowAll=$stmt2->fetch(PDO::FETCH_ASSOC);
                                            $totAllegati=$rowAll['tot'];
                                            if ($totAllegati>0) {
                                                $str.=<<<EX
                                                <button idmodalele="{$ev['chiaveprimaria']}" idmodalmod="{$modulo['id_modulo']}" class="aprimodal-allegati btn btn-info" type="button"> <i class=" glyphicon glyphicon-paperclip "></i> {$totAllegati} </button>
EX;
                                            } else {
                                            $str.=<<<EX
                                                <button idmodalele="{$ev['chiaveprimaria']}" idmodalmod="{$modulo['id_modulo']}" class="btn btn-danger" type="button"> <i class=" glyphicon glyphicon-paperclip "></i> {$totAllegati} </button>
EX;
                                             }
                                        endif;

        //----------------------------------------------------------------------------------------------------------------
        //--                                                                                                            --
        //----- (f) -----------------------------A L L E G A T I ---------------------------------------------------------
        //--                                                                                                            --
        //----------------------------------------------------------------------------------------------------------------


        $str.=<<<EX
</div>
</div>
EX;





        $row[]=$str;

        //----------------------------------------------------------------------------------------------------------------
        //--                                                                                                            --
        //--                                                                                                            --
        //----- (f) -----------------------------P R I M A   C O L O N N A------------------------------------------------
        //--                                                                                                            --
        //--                                                                                                            --
        //----------------------------------------------------------------------------------------------------------------


        foreach ($aRow as $key => $value) :
            if (!(in_array($key,$campi_non_mostrati_in_tabella))) $row[]=$value;
        endforeach;
		$output['aaData'][] = $row;
	}

	echo json_encode( $output );

// replace any non-ascii character with its hex code.
function escape($value) {
    $return = '';
    for($i = 0; $i < strlen($value); ++$i) {
        $char = $value[$i];
        $ord = ord($char);
        if($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
            $return .= $char;
        else
            $return .= '\\x' . dechex($ord);
    }
    return $return;
}

?>
