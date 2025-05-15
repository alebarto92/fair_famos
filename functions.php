<?php //tutte le funzioni possono essere definite dentro config.php

function convertToHoursMins($time, $format = '%02d ore %02d min') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

if (!function_exists('calcolaPunteggioCantiere')) :
    function calcolaPunteggioCantiere($idele) {
        global $dbh,$GLOBAL_tb;
        $tbcantieri=$GLOBAL_tb['sedi_clienti'];
        $tbservizi=$GLOBAL_tb['servizi'];
        $tbattivita=$GLOBAL_tb['attivita'];
        $tbprogrammazione=$GLOBAL_tb['programmazione'];
        $elemento=getElemento(10,$idele);
        $punteggio=0;
        //if ($elemento['qrcodeassociato']!='') {
        //    $punteggio+=20;
        //}
        if ($elemento['sede']!='') {
            $punteggio+=15;
        }
        if ($elemento['indirizzo']!='') {
            $punteggio+=15;
        }
        if ($elemento['CAP']!='') {
            $punteggio+=5;
        }
        if ($elemento['citta']!='') {
            $punteggio+=5;
        }
        if ($elemento['provincia']!='') {
            $punteggio+=5;
        }
        if (($elemento['lat']!='') && ($elemento['lng']!='')) {
            $punteggio+=25;
        }
        $queryservizi="SELECT count(*) as totservizi FROM $tbservizi where id_sede=$idele";
        $stmtservizi=$dbh->query($queryservizi);
        $rowservizi=$stmtservizi->fetch(PDO::FETCH_ASSOC);
        $suggerimento="";
        $punteggio_servizi=0;
        $punteggio_attivita=0;

        if ($rowservizi['totservizi']>0) {
            $punteggio+=25;
            $punteggio_servizi=25;
        } else {
            $suggerimento.="<a class=\"btn btn-sm btn-danger\" target=\"_blank\" href=\"get_element.php?idele=-1&debug=0&idmod=73&k=id_sede-$idele\">Devi aggiungere almeno un servizio!</a> &nbsp; ";
        }

        $queryprogrammazione="SELECT count(*) as totprogrammazione FROM $tbprogrammazione where id_attivita_primaria IN (SELECT $tbattivita.id FROM $tbattivita WHERE $tbattivita.id_sede=$idele)";
        $stmtprog=$dbh->query($queryprogrammazione);
        $rowprog=$stmtprog->fetch(PDO::FETCH_ASSOC);
        if ($rowprog['totprogrammazione']>0) {
            $punteggio+=5;
            $punteggio_attivita=5;
        } else {
            $suggerimento.="<a class=\"btn btn-sm btn-warning\" target=\"_blank\" href=\"module.php?modname=AttivitaClean&p[id_sede]=$idele\">Pianifica le attività di questo cantiere!</a>";
        }
        $queryupdate="UPDATE $tbcantieri SET punteggio=$punteggio,punteggio_servizi=$punteggio_servizi,punteggio_attivita=$punteggio_attivita,suggerimento='$suggerimento' WHERE id=$idele";
        return $queryupdate;
    }
endif;

if (!function_exists('validateDate')) :
function validateDate($date)
{
    $d = DateTime::createFromFormat('d/m/Y', $date);
    if ($d && $d->format('d/m/Y') === $date) {
        //è una data tipo d/m/Y allora la converto in mysql format
        return $d->format('Y-m-d');
    } else {
        //non è una data, restituisco la stringa come è arrivata
        return $date;
    }
}
endif;

if (!function_exists('validateDateTime')) :
    function validateDateTime($date)
    {
        $d = DateTime::createFromFormat('d/m/Y H:i:s', $date);
        if ($d && $d->format('d/m/Y H:i:s') === $date) {
            //è una data tipo d/m/Y allora la converto in mysql format
            return $d->format('Y-m-d H:i:s');
        } else {
            //non è una data, restituisco la stringa come è arrivata
            return $date;
        }
    }
endif;

if (!function_exists('validateDate')) :
    function validateDate($date)
    {
        $d = DateTime::createFromFormat('d/m/Y', $date);
        if ($d && $d->format('d/m/Y') === $date) {
            //è una data tipo d/m/Y allora la converto in mysql format
            return $d->format('Y-m-d');
        } else {
            //non è una data, restituisco la stringa come è arrivata
            return $date;
        }
    }
endif;

if (!function_exists('convertDate')) :
    function convertDate($date,$format='d/m/Y')
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if ($d && $d->format('Y-m-d') === $date) {
            //è una data tipo Y-m-d allora la converto in $format (d/m/Y)
            return $d->format($format);
        } else {
            //non è una data, restituisco la stringa come è arrivata
            return $date;
        }
    }
endif;

if (!function_exists('genPdfThumbnail')) :
    function genPdfThumbnail($source, $target,$width=160,$height=120)
    {
        if( extension_loaded('imagick') || class_exists("Imagick") ){ /*do Imagick*/
            //$source = realpath($source);
            $target = dirname($source).DIRECTORY_SEPARATOR.$target;
            $im     = new Imagick($source."[0]"); // 0-first page, 1-second page
            //$im->setImageColorspace(255); // prevent image colors from inverting
            $im->setimageformat("jpeg");
            $im->thumbnailimage($width, $height); // width and height
            $im->writeimage($target);
            $im->clear();
            $im->destroy();
        }
    }
endif;

if (!function_exists('TODDMMYYYY')) :
    function TODDMMYYYY($data) {
        if ($data=='') {
            return '';
        } else {
            list($aa,$mm,$gg)=explode("-",$data);
            return $gg."/".$mm."/".$aa;
        }
    }
endif;

if (!function_exists('TODDMMYYYYHHiiss')) :
    function TODDMMYYYYHHiiss($data) {
        if ($data=='') {
            return '';
        } else {
            list($date,$time)=explode(" ",$data);
            list($aa,$mm,$gg)=explode("-",$date);
            list($h,$i,$s)=explode(":",$time);
            return $gg."/".$mm."/".$aa." ".$h.":".$i.":".$s;
        }
    }
endif;

if (!function_exists('getUtente')) :
    function getUtente($id_user) {
        global $dbh,$GLOBAL_tb;
        $tbusers=$GLOBAL_tb['users'];
        $tbruoli=$GLOBAL_tb['ruoli'];
        $query="SELECT * FROM $tbusers,$tbruoli WHERE id_user=? AND $tbruoli.id_ruolo=$tbusers.id_ruolo";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($id_user));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
endif;

if (!function_exists('getLingue')) :
    function getLingue($id_user) {
        global $dbh,$GLOBAL_tb;
        $tblingue=$GLOBAL_tb['lingue'];
        $tblingueusers=$GLOBAL_tb['lingue_users'];

        $query="SELECT * FROM $tblingueusers LEFT JOIN $tblingue ON $tblingue.id_lang=$tblingueusers.id_lang WHERE id_user=$id_user";
        $stmt = $dbh->query($query);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lingue[]=$row['lang'];
        }
        return $lingue;
    }
endif;

if (!function_exists('getLingueInterfaccia')) :
    function getLingueInterfaccia() {
        global $dbh,$GLOBAL_tb;
        $tblingueinterfaccia=$GLOBAL_tb['lingue_interfaccia'];

        $query="SELECT * FROM $tblingueinterfaccia ";
        $stmt = $dbh->query($query);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lingue[]=$row;
        }
        return $lingue;
    }
endif;

if (!function_exists('getAllLangs')) :
    function getAllLangs() {
        global $dbh,$GLOBAL_tb;
        $tblingue=$GLOBAL_tb['lingue'];

        $query="SELECT * FROM $tblingue ";
        $stmt = $dbh->query($query);

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $lingue[]=$row['lang'];
        }
        return $lingue;
    }
endif;

if (!function_exists('getFiles')) :
    function getFiles($id_elem,$tb,$tipofile,$maxfiles=-1) {
        global $dbh,$GLOBAL_tb;
        $tbfiles=$GLOBAL_tb['files'];
        if ($maxfiles>0) {
            $query="SELECT * FROM $tbfiles WHERE tipo_file='".$tipofile."' AND id_elem=$id_elem AND tb='".$tb."' order by ordine LIMIT 0,$maxfiles ";
        } else {
            $query="SELECT * FROM $tbfiles WHERE tipo_file='".$tipofile."' AND id_elem=$id_elem AND tb='".$tb."' order by ordine";
        }
        $stmt=$dbh->query($query);
        $files=array();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $files[]=$row;
        }
        return $files;
    }
endif;

if (!function_exists('getModuli')) :
    function getModuli($pars='') {
        global $dbh,$GLOBAL_tb;
        $tbmoduli=$GLOBAL_tb['moduli'];

        $WHERE='';
        if ($pars['stato']) {
            $WHERE.=" AND stato='".$pars['stato']."'";
        }
        if ($pars['menu']) {
            $WHERE.=" AND menu='".$pars['menu']."'";
        }
        if ($pars['nome_modulo']) {
            $WHERE.=" AND nome_modulo='".$pars['nome_modulo']."'";
        }
        $query="SELECT * FROM $tbmoduli WHERE 1=1 $WHERE order by ordine";

        foreach($dbh->query($query) as $row) {
            foreach ($row as $key=>$value) {
                $row[$key]=stripslashes($value);
            }
            $moduli[]=$row;
        }
        return $moduli;
    }
endif;

if (!function_exists('getModulo')) :
    function getModulo($id) {
        global $dbh,$GLOBAL_tb;
        $tbmoduli=$GLOBAL_tb['moduli'];
        $query="SELECT * FROM $tbmoduli WHERE id_modulo=$id";
        foreach($dbh->query($query) as $row) {
            foreach ($row as $key=>$value) {
                $row[$key]=stripslashes($value);
            }
        }
        return $row;
    }
endif;

if (!function_exists('permessi')) :
    function permessi($id_modulo,$id_ruolo,$superuser=false) {
        global $dbh,$GLOBAL_tb;
        $tbpermessi=$GLOBAL_tb['permessi'];

        if ($superuser) {
            $perm['Can_create']='si';
            $perm['Can_read']='si';
            $perm['Can_update']='si';
            $perm['Can_delete']='si';
            return $perm;
        }
        $query="SELECT * FROM $tbpermessi WHERE id_modulo=? AND id_ruolo=? LIMIT 0,1";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($id_modulo, $id_ruolo));

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row;
        } else {
            $perm['Can_create']='no';
            $perm['Can_read']='no';
            $perm['Can_update']='no';
            $perm['Can_delete']='no';
            return $perm;
        }
    }
endif;

if (!function_exists('getElemento')) :
    function getElemento($idmod,$idele) {
        global $dbh;
        $modulo=getModulo($idmod);
        $query="SELECT * FROM ".$modulo['nome_tabella']." WHERE ".$modulo['chiaveprimaria']."='".$idele."' LIMIT 0,1";
        $stmt=$dbh->query($query);
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
endif;

if (!function_exists('getPostazione')) :
    function getPostazione($idele) {
        global $dbh,$GLOBAL_tb;
        $tbpostazioni=$GLOBAL_tb['postazioni'];
        $query="SELECT * FROM ".$tbpostazioni." WHERE id=? LIMIT 0,1";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($idele));
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
endif;

if (!function_exists('getTestiTraducibili')) :
    function getTestiTraducibili($nome_tabella,$idele,$lang) {
        global $dbh,$GLOBAL_tb;
        $tbcampitraducibili=$GLOBAL_tb['campi_traducibili'];
        $tbtesti=$GLOBAL_tb['testi'];
        $query="SELECT * FROM $tbcampitraducibili WHERE nome_tabella=? ";
        $stmt=$dbh->prepare($query);
        $stmt->execute(array($nome_tabella));
        $campi=array();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $campi[]=$row['nome_campo'];
        }
        if (count($campi)==0) {
            return;
        }
        $testi=array();
        foreach ($campi as $chiave) {
            if ($chiave=='') continue;
            $query="SELECT valore FROM $tbtesti WHERE id_ext=? AND table_ext=? AND lang=? AND chiave=? LIMIT 0,1";
            $stmt=$dbh->prepare($query);
            $stmt->execute(array($idele,$nome_tabella,$lang,$chiave));

            if ($row=$stmt->fetch(PDO::FETCH_ASSOC)) { $testi[$chiave]=$row['valore']; }
        }
        return $testi;
    }
endif;

if (!function_exists('getTipoColonna')) :
    function getTipoColonna($col) {
        $colType=$col['Type'];
        $colcomment=$col['Comment'];


        if ($colcomment=='colorpicker') {
            return "COLORPICKER";
        }

        //NUMERIC (input type=text)
        if ( strstr($colType,"bigint") || strstr($colType,"int") || strstr($colType,"mediumint") || strstr($colType,"smallint")   ) {
            return "INTEGER";
        }

        //NUMERIC (input type=text)
        if ( strstr($colType,"float")  || strstr($colType,"double")  ) {
            return "NUMERIC";
        }

        //DECIMAL (input type=text)
        if ( strstr($colType,"decimal")   ) {
            return "DECIMAL";
        }

        //VARCHAR e TINYTEXT (input type=text)
        if ( strstr($colType,"varchar")  || strstr($colType,"tinytext"))
        { //guarda se è enum
            return "TEXT";
        }

        //TEXT MEDIUMTEXT e LONGTEXT (textarea)
        if ( strstr($colType,"mediumtext") || strstr($colType,"text") || strstr($colType,"longtext"))
        { //guarda se è enum
            return "TEXTAREA";
        }

        //ENUM (radio button or select)
        if ( strstr($colType,"enum") )
        { //guarda se è enum
            return "ENUM";
        }

        //SET (checkbox or multiselect)
        if ( strstr($colType,"set"))
        { //guarda se è enum
            return "SET";
        }

        //DATETIME (datetimepicker)
        if ( strstr($colType,"datetime"))
        { //guarda se è enum
            return "DATETIME";
        }

        //DATE (datepicker)
        if ( strstr($colType,"date"))
        { //guarda se è enum
            return "DATE";
        }

        //TIME o TIMESTAMP (timepicker)
        if ( strstr($colType,"time") || strstr($colType,"timestamp"))
        { //guarda se è enum
            return "TIME";
        }

    }
endif;

if (!function_exists('getLastPosition')) :
    function getLastPosition($tab) {
        global $dbh;
        $query="SELECT max(ordine) as last FROM $tab ";
        if ($stmt=$dbh->query($query) ) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['last'];
        } else {
            return 0;
        }
    }
endif;

if (!function_exists('sanitate')) :
    function sanitate($array) {
        foreach($array as $key=>$value) {
            if(is_array($value)) { sanitate($value); }
            else {
                if ($array[$key]) { $array[$key] = addslashes($value); }
            }
        }
        return $array;
    }
endif;

if (!function_exists('getSetValues')) :
    function getSetValues($table,$column)
    {
        global $dbh;
        $sql = "SHOW COLUMNS FROM $table LIKE '$column'";

        $stmt=$dbh->query($sql);

        $line = $stmt->fetch(PDO::FETCH_ASSOC);
        $set  = $line['Type'];
        // Remove "set(" at start and ");" at end.
        $set  = substr($set,5,strlen($set)-7);
        // Split into an array.
        return preg_split("/','/",$set);
    }
endif;

if (!function_exists('getEnumValues')) :
    function getEnumValues($table_name,$column_name) {
        global $dbh;
        $stmt = $dbh->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table_name' AND COLUMN_NAME = '$column_name'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $enumList = explode(",", str_replace("'", "", substr($row['COLUMN_TYPE'], 5, (strlen($row['COLUMN_TYPE'])-6))));
        return $enumList;
    }
endif;

if (!function_exists('getModuloFrom_script_modulo')) :
    function getModuloFrom_script_modulo($string) {
        global $dbh,$GLOBAL_tb;
        $tbmoduli=$GLOBAL_tb['moduli'];
        $query="SELECT id_modulo FROM $tbmoduli WHERE modulo_standard='no' AND script_modulo=?";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($string));
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id_modulo'];
    }
endif;

if (!function_exists('getModuloFrom_nome_modulo')) :
    function getModuloFrom_nome_modulo($string) {
        global $dbh,$GLOBAL_tb;
        $tbmoduli=$GLOBAL_tb['moduli'];
        $query="SELECT id_modulo FROM $tbmoduli WHERE nome_modulo=?";
        $stmt = $dbh->prepare($query);
        $stmt->execute(array($string));
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id_modulo'];
    }
endif;

if (!function_exists('getModuloFrom_nome_tabella')) :
    function getModuloFrom_nome_tabella($string) {
        global $dbh,$GLOBAL_tb;
        $tbmoduli=$GLOBAL_tb['moduli'];
        $query="SELECT id_modulo FROM $tbmoduli WHERE nome_tabella='".$string."'";
        $stmt=$dbh->query($query);
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id_modulo'];
    }
endif;

if (!function_exists('setNotificheCRUD')) :
    function setNotificheCRUD($pTipoUtente="admWeb",$pCategoria=NULL,$pTipologia=NULL,$pEvento=NULL) {
        global $dbh,$GLOBAL_tb;
        $tbnotificheCRUD=$GLOBAL_tb['notificheCRUD'];
        $iException=0;
        // (i) Prerequisiti
        if (empty($pTipoUtente)) {
            return 0;
        }
        // () Formatting
        $pTipoUtente=addslashes($pTipoUtente);
        $pCategoria=addslashes($pCategoria);
        $pTipologia=addslashes($pTipologia);
        $pEvento=addslashes($pEvento);

        // () Formatting
        // (f) Prerequisiti
        try {
            $sqlInsNotifiche="";
            $sqlInsNotifiche="INSERT INTO $tbnotificheCRUD (data_notifica, id_utente, categoria, tipologia, evento)".
                " VALUES ( now(), '".trim($pTipoUtente)."','".
                trim($pCategoria)."','".
                trim($pTipologia)."','".
                trim($pEvento)."')";
            $insNotifiche = $dbh->query($sqlInsNotifiche);
            if (!$insNotifiche) {
                $iException++;
            }
        } catch  (Exception $setNotificheAggException){
            $iException++;

        }
        if ($iException>0) {
            return 0;
        } else {
            return 1;
        }
    }
endif;

?>
