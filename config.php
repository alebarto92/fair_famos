<?php

//ricordarsi di installare image magick come estensione php sul server altrimenti non può fare
//le anteprime dei pdf!!!
//vedere test.php per provare
//https://help.dreamhost.com/hc/en-us/articles/215075007-GraphicsMagick-and-gmagick-PHP-module-on-Shared-hosting


//PALERMO
$defaultLat="38.1156879";
$defaultLng="13.3612671";

//FIRENZE
$defaultLat="43.7563782";
$defaultLng="11.2897175";



date_default_timezone_set('Europe/Rome');
session_start();
//$superuserOverride=1;

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
$GLOBAL_tb['tipi_servizio']             ="pcs_tipi_servizio";
$GLOBAL_tb['servizi']                   ="pcs_servizi";
$GLOBAL_tb['attivita']                  ="pcs_attivita_clean";
$GLOBAL_tb['programmazione']            ="pcs_programmazione";
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

$giorno[1]="Lun";
$giorno[2]="Mar";
$giorno[3]="Mer";
$giorno[4]="Gio";
$giorno[5]="Ven";
$giorno[6]="Sab";
$giorno[7]="Dom";

$giornominuscolo[1]="lun";
$giornominuscolo[2]="mar";
$giornominuscolo[3]="mer";
$giornominuscolo[4]="gio";
$giornominuscolo[5]="ven";
$giornominuscolo[6]="sab";
$giornominuscolo[7]="dom";


$cadenzavalue[0]="MAI";
$cadenzavalue[1]="ogni settimana";
$cadenzavalue[2]="ogni 2 settimane";
$cadenzavalue[3]="ogni 3 settimane";
$cadenzavalue[4]="ogni 4 settimane";

$GIORNIPROGRAMMAZIONE=60;
$MAXRIGHEPROGRAMMAZIONE=100;

$DEFAULT[4]['txtcolor']='#ffffff';
$DEFAULT[4]['bgcolor']='#00aabb';


//----------- (i) parametri di connessione ---------------------------------------------------------------------------

$user="Sql1831396";
$pass="Daviddi_2024";
$database="Sql1831396_3";
$host="31.11.39.190";

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
$sitedir="/fair_famos/";
$basedir=$sitedir;
$root_server = $_SERVER['DOCUMENT_ROOT'];

$projecttitle="FAIR";

//----------- (f) variabili predefinite ------------------------------------------------------------------------------

/* (i) --------------------------------------------------- parametri specifici  --------------------------------------------------- */

$EMAILADMINBCC="fabio.franci@gmail.com";

$ftp_host = "labima.sw19.it";
$ftp_id = "studioweb19labimauser";
$ftp_pw = "B3nv3nut0";

$FTPUPLOADDIR="/web";

$sitoweb="https://www.famos-project.eu";

$directoryfiles="/fair_famos/file";

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
$resizes[1]['width']=300;
$resizes[1]['height']=300;

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
include("functions.php");

//default english
$locale = "it_IT";

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages", "./locale");
textdomain("messages");

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