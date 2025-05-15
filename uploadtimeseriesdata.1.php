<?php $pagina="upload";?>
<?php include("db_connect.php");?>
<?php include("readlargeCSVClass.php");?>
<?php include_once ("INC_10_HEADER.php"); ?>
<html>
<?php include_once ("INC_15_SCRIPTS.php"); ?>
<body>
<?php include_once ("INC_20_NAVBAR.php"); ?>
<div class="container-fluid" id="firstcontainer">

<div style="height:200px;">&nbsp;</div>

<?php

$uploadDir = __DIR__.'/uploads';

$idexperiment=$_POST['idexperiment'];

foreach ($_FILES as $file) {
    if (UPLOAD_ERR_OK === $file['error']) {
        $fileName = basename($file['name']);

        $tmparray=explode(".",$fileName);

        $ext=$tmparray[count($tmparray)-1];
        $filename=date('YmdHis').".".$ext;

        $separator=",";
        if ($_POST['separator']=="virgola") {
          $separator=",";
        }
        if ($_POST['separator']=="puntoevirgola") {
          $separator=";";
        }
        if ($_POST['separator']=="tab") {
          $separator="\t";
        }


        if (move_uploaded_file($file['tmp_name'], $uploadDir.DIRECTORY_SEPARATOR.$filename)) {
          setNotificheCRUD("admWeb","SUCCESS","uploadtimeseriesdata:","copiato con successo ".$filename." separator=$separator");

          $csv_reader = new readLargeCSV($uploadDir.DIRECTORY_SEPARATOR.$filename, $separator);

          $passaggio=0;
          $i=0; //conteggio totale delle righe del file

          $header=array();

          foreach($csv_reader->csvToArray() as $data){
            //setNotificheCRUD("admWeb","SUCCESS","uploadtimeseriesdata:","sono a leggere il file ".$filename);

            if ($passaggio==0) { //alla lettura del primo blocco di dati intercetto la prima riga che contiene gli header
              //setNotificheCRUD("admWeb","INFO","primo blocco dati:",json_encode($data));
              //inserimento header nel db
              foreach (array_keys($data[0]) as $h) :
                $header[]=$h;
                $query="SELECT * FROM tb_time_series WHERE id_experiment=$idexperiment AND column_name='$h'";
                //setNotificheCRUD("admWeb","INFO","uploadtimeseriesdata:",$query);
                $stmt=$dbh->query($query);
                if ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                  $idtimeseries[$h]=$row['id'];
                  setNotificheCRUD("admWeb","SUCCESS","uploadtimeseriesdata:","identificata colonna $h");
                } else {
                  $query1="INSERT INTO tb_time_series (id_experiment,column_name) VALUES ($idexperiment,'$h')";
                  //setNotificheCRUD("admWeb","INFO","uploadtimeseriesdata:",$query1);
                  $stmt1=$dbh->query($query1);
                  $idtimeseries[$h]=$dbh->lastInsertID();
                  setNotificheCRUD("admWeb","SUCCESS","uploadtimeseriesdata:","inserita nuova colonna $h con id ".$idtimeseries[$h]);
                }
              endforeach;
            }
            $passaggio++;

            foreach ($data as $riga) :
              $i++;
              foreach ($header as $h) :
                $idtimes=$idtimeseries[$h];
                $query2="REPLACE INTO tb_time_series_data (id_time_series,position,data) VALUES ($idtimes,$i,$riga[$h])";
                //setNotificheCRUD("admWeb","INFO","uploadtimeseriesdata:",$query2);
                if ($stmt2=$dbh->query($query2)) {
                  //echo "Data read and inserted in database!";
                  //setNotificheCRUD("admWeb","SUCCESS","uploadtimeseriesdata:",$query2);

                } else {
                  $errori.="Error in inserting data for $h at position $i<br/>";
                  //setNotificheCRUD("admWeb","ERROR","uploadtimeseriesdata:",$query2);
                }
              endforeach;
            endforeach;
           // you can do whatever you want with the $data.
          }


        } else {
          $ret['result']=false;
          //setNotificheCRUD("admWeb","ERROR","uploadtimeseriesdata:","errore copia ".$filename);
            echo json_encode($ret);
            exit;
        }

    }
}
?>

<div>Data successfully loaded. Go back to the <a href="module.php?id=83">experiment page</a> or to the <a href="graph.php">graph page</a> to see them.</div>

</div>
<script>
    $(document).ready(function() {

            var offsetscroll = $("#firstcontainer").offset().top;
            var navheight = $("nav").height();
            $('html,body').animate({scrollTop: offsetscroll - navheight}, 'slow');
        });
    </script>
</body>
</html>
