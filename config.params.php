<?php

//ricordarsi di installare image magick come estensione php sul server altrimenti non può fare
//le anteprime dei pdf!!!
//vedere test.php per provare
//https://help.dreamhost.com/hc/en-us/articles/215075007-GraphicsMagick-and-gmagick-PHP-module-on-Shared-hosting


date_default_timezone_set('Europe/Rome');
session_start();
//$superuserOverride=1;

/*----------------------- Prendo i parametri dalla sessione di login generale ---------*/
/*

		$_SESSION['BSMAIN']['id_utente']=$row['id_cliente'];
		$_SESSION['BSMAIN']['nome']=$row['nome'];
		$_SESSION['BSMAIN']['versione']=$row['versione'];
		$_SESSION['BSMAIN']['DBhost']=$row['dbhost'];
		$_SESSION['BSMAIN']['DBname']=$row['dbname'];
		$_SESSION['BSMAIN']['DBuser']=$row['dbuser'];
		$_SESSION['BSMAIN']['DBpass']=$row['dbpassword'];
		$_SESSION['BSMAIN']['stato']=$row['stato'];
		$_SESSION['BSMAIN']['data_scadenza']=$row['data_scadenza'];
		$_SESSION['BSMAIN']['data_inizio']=$row['data_inizio'];
		$_SESSION['BSMAIN']['username']=$row['username'];
		$_SESSION['BSMAIN']['email']=$row['email'];

*/
//----------- (i) nomi delle tabelle ---------------------------------------------------------------------------------
$GLOBAL_tb['moduli']                    ="pcs_moduli";
$GLOBAL_tb['permessi']                  ="pcs_permessi";
$GLOBAL_tb['users']                     ="pcs_users";
$GLOBAL_tb['ruoli']                     ="pcs_ruoli";
$GLOBAL_tb['lingue']                    ="pcs_lingue";
$GLOBAL_tb['lingue_interfaccia']        ="pcs_lingue_interfaccia";
$GLOBAL_tb['lingue_users']              ="pcs_lingue_users";
$GLOBAL_tb['notificheCRUD']             ="pcs_notificheCRUD";
$GLOBAL_tb['files']                     ="pcs_file";
$GLOBAL_tb['testi']                     ="pcs_testi";
$GLOBAL_tb['campi_traducibili']         ="pcs_campi_traducibili";
$GLOBAL_tb['clienti']                   ="pcs_clienti";
$GLOBAL_tb['sedi_clienti']              ="pcs_sedi_clienti";
$GLOBAL_tb['postazioni']                ="pcs_postazioni";
$GLOBAL_tb['interventi']                ="pcs_visite";
$GLOBAL_tb['visite']                    ="pcs_visite";
$GLOBAL_tb['ispezioni']                 ="pcs_ispezioni";
$GLOBAL_tb['tipi_servizio']             ="pcs_tipi_servizio";
$GLOBAL_tb['aree']                      ="pcs_aree";
$GLOBAL_tb['infestanti']                ="pcs_infestanti";
$GLOBAL_tb['reports']                   ="pcs_reports";
$GLOBAL_tb['servizi']                   ="pcs_servizi";
$GLOBAL_tb['attivita']                  ="pcs_attivita_clean";
//----------- (f) nomi delle tabelle ---------------------------------------------------------------------------------

$mese['01']="Gennaio";
$mese['02']="Febbraio";
$mese['03']="Marzo";
$mese['04']="Aprile";
$mese['05']="Maggio";
$mese['06']="Giugno";
$mese['07']="Luglio";
$mese['08']="Agosto";
$mese['09']="Settembre";
$mese['10']="Ottobre";
$mese['11']="Novembre";
$mese['12']="Dicembre";



$DEFAULT[4]['txtcolor']='#ffffff';
$DEFAULT[4]['bgcolor']='#00aabb';


//----------- (i) parametri di connessione ---------------------------------------------------------------------------
$user="c1pmdemoclean";
$pass="B3nv3nut0";
$database="c1pmdemoclean";
$host="localhost";

//----------- (f) parametri di connessione ---------------------------------------------------------------------------

//----------- (i) connessione ---------------------------------------------------------------------------
try {
    $dbh = new PDO('mysql:host='.$host.';dbname='.$database, $user, $pass);
    $dbh->exec("set names utf8");

    $query="SELECT * FROM ".$GLOBAL_tb['users']."  WHERE (username=? OR email=?) LIMIT 0,1";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array($_SESSION['BSMAIN']['username'], $_SESSION['BSMAIN']['email']));
    if ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $trovato=true;
        $_SESSION['BSid_user']=$row['id_user'];
    }

    //echo "Dentro il db";
//    foreach($dbh->query('SELECT * from FOO') as $row) {
//        print_r($row);
//    }
//    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
//----------- (f) connessione ---------------------------------------------------------------------------

/*--------------------------- Directory di questa versione --------- */
$sitedir="/democlean/";
$basedir=$sitedir;
$root_server = $_SERVER['DOCUMENT_ROOT'];

$projecttitle="DEMO CLEAN";

//----------- (f) variabili predefinite ------------------------------------------------------------------------------

/* (i) --------------------------------------------------- parametri specifici  --------------------------------------------------- */

$EMAILADMINBCC="fabio.franci@gmail.com";

$ftp_host = "clean-management.it";
$ftp_id = "studioweb19cmuser";
$ftp_pw = "B3nv3nut0";

$FTPUPLOADDIR="/web";

$sitoweb="https://clean-management.it";

$directoryfiles="/democlean/file";

//per le immagini

//widthmax e heightmax servono a plupload, sono le dimensioni massime delle immagini che vengono uploadate sul server

$widthmax=1200;
$heightmax=800;

//con l'array resizes invece vengono eseguiti i thumbnails, nella modalità crop, oppure landscape oppure portrait
//il resizes 0 è quello di default, non andrebbe cambiato mai!

//$resizes è definito a livello di file _parametri.php
//il primo è quello di default

$resizes[0]['prefisso']="crop";
$resizes[0]['crop']="crop";
$resizes[0]['width']=150;
$resizes[0]['height']=150;

$resizes[1]['prefisso']="crop";
$resizes[1]['crop']="crop";
$resizes[1]['width']=80;
$resizes[1]['height']=80;

$resizes[2]['prefisso']="thumb";
$resizes[2]['crop']="landscape"; //forzo il resize su width 400
$resizes[2]['width']=400;
$resizes[2]['height']=400;

$resizes[3]['prefisso']="small";
$resizes[3]['crop']="landscape"; //forzo il resize su width 300
$resizes[3]['width']=300;
$resizes[3]['height']=300;

//inoltre è obbligatorio mettere come campo traducibile della tabella tb_file il campo "nome", perché serve per l'interfaccia di upload, se gli allegati sono abilitati come modulo

/* (f) --------------------------------------------------- parametri specifici del nuovo cms --------------------------------------------------- */


// include le funzioni generali
include($root_server.$sitedir."functions.php");

//default english
$locale = "it_IT";

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages6", "./locale");
textdomain("messages6");

$utente=array();
$lingue=array();

$defaultLang='it';
$lang='it';

//$_SESSION['BSid_user']=1;

if ($_SESSION['pcs_id_user']>0) {
    $utente = getUtente($_SESSION['pcs_id_user']);

    if ($_SESSION['pcs_id_cliente']>0) {
        $utente['id_cliente']=$_SESSION['pcs_id_cliente'];
    } else {
        $utente['id_cliente']=0;
    }
//    $lingue = getLingue($_SESSION['id_user']);

    $lingue=explode(",",$utente['languages']);

    if (count($lingue)>0) {
    } else {
        $lingue[0]="it";
    }

    $defaultLang =substr($utente['main_language'],0,2);
    $lang =$defaultLang;

    $_SESSION['pcs_lang'] = $lang;

    $locale =$utente['main_language'];

    $_SESSION['pcs_locale']=$locale;

    putenv("LC_ALL=$locale");
    setlocale(LC_ALL, $locale);
    bindtextdomain("messages6", "./locale");
    textdomain("messages6");

}

//TIMEZONE
$TIMEZONE="Europe/Rome"; //timezone del server dove c'è mysql
?>
