<?php 

include("config.php");
$filename=$_GET['filename'];
$idexperiment=$_GET['idexperiment'];
$dir    = "./uploads";

$files1 = scandir($dir);

foreach ($files1 as $file1) :
    if ($file1==".") continue;
    if ($file1=="..") continue;

    if (substr($file1,0,4)=="SQL-") {
        $filename=$dir."/".$file1;
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $i=0;
            while (($data = fgets($handle)) !== FALSE) {
                $i++;

                if ($stmt=$dbh->query($data)) {
                    //echo $i."-> ok<br/>";
                } else {
                    //echo $i."-> ko<br/>";
                }

            }
            fclose($handle);
            $filesource='./uploads/'.$file1;
            unlink($filesource);

        } else {
            $ret['result']=false;
            $ret['error']="Errore sul file $file1";
            echo json_encode($ret);
            exit();
        }
    }
endforeach;

          setNotificheCRUD("admWeb","SUCCESS","creafileSQL:"," 1 sono a leggere il file ".$filename);

$queryfinale="REPLACE INTO tb_time_series_data (id_time_series, position, data) SELECT id_time_series, position, data FROM tb_time_series_data_temp WHERE id_time_series IN (SELECT id FROM tb_time_series WHERE id_experiment=$idexperiment)";
setNotificheCRUD("admWeb","INFO","leggifilesql:",$queryfinale);
if ($stmt=$dbh->query($queryfinale)) {
    setNotificheCRUD("admWeb","SUCCESS","leggifilesql:",$queryfinale);
    $queryfinale="DELETE FROM tb_time_series_data_temp WHERE id_time_series IN (SELECT id FROM tb_time_series WHERE id_experiment=$idexperiment)";
    setNotificheCRUD("admWeb","INFO","leggifilesql:",$queryfinale);
    if ($stmt=$dbh->query($queryfinale)) {
        setNotificheCRUD("admWeb","SUCCESS","leggifilesql:",$queryfinale);
    } else {
        setNotificheCRUD("admWeb","ERROR","leggifilesql:",$queryfinale);
    }
} else {
    setNotificheCRUD("admWeb","ERROR","leggifilesql:",$queryfinale);
}

$ret['result']=true;
echo json_encode($ret);
exit();

