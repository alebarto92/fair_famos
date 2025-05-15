<?php
          include("config.php");
          include("readlargeCSVClass.php");

          $uploadDir = __DIR__.'/uploads';

          $idexperiment=$_GET['idexperiment'];
          $separator=$_GET['separator'];
          $filename=$_GET['filename'];

          // Open the file to get existing content
          $csv_reader = new readLargeCSV($uploadDir.DIRECTORY_SEPARATOR.$filename, $separator);
          $passaggio=$_GET['passaggio'];
          if ($passaggio=='') {
            $passaggio=0;
          }

          $blocco=50;

          $iniziorighe=$passaggio*$blocco+1;
          $pp=sprintf("%03d",$passaggio);

          $filenew = $uploadDir.DIRECTORY_SEPARATOR.'SQL-'.$pp.'-'.$filename;

          $i=0; //conteggio totale delle righe del file

          $header=array();
          $current = "";

          setNotificheCRUD("admWeb","SUCCESS","creafileSQL:"," 1 sono a leggere il file ".$filename);

          foreach($csv_reader->csvToArray() as $data){
            setNotificheCRUD("admWeb","SUCCESS","creafileSQL:"," 2 sono a leggere il file ".$filename);

            //if ($passaggio==0) { //alla lettura del primo blocco di dati intercetto la prima riga che contiene gli header
              setNotificheCRUD("admWeb","INFO","primo blocco dati:",json_encode($data));
              //inserimento header nel db
              foreach (array_keys($data[0]) as $h) :
                $header[]=$h;
                $query="SELECT * FROM tb_time_series WHERE id_experiment=$idexperiment AND column_name='$h'";
                //setNotificheCRUD("admWeb","INFO","uploadtimeseriesdata:",$query);
                $stmt=$dbh->query($query);
                if ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
                  $idtimeseries[$h]=$row['id'];
                  setNotificheCRUD("admWeb","SUCCESS","creafileSQL:","identificata colonna $h");
                } else {
                  $query1="INSERT INTO tb_time_series (id_experiment,column_name,freq) VALUES ($idexperiment,'$h',1)";
                  setNotificheCRUD("admWeb","INFO","creafileSQL:",$query1);
                  $stmt1=$dbh->query($query1);
                  $idtimeseries[$h]=$dbh->lastInsertID();
                  setNotificheCRUD("admWeb","SUCCESS","creafileSQL:","inserita nuova colonna $h con id ".$idtimeseries[$h]);
                }
              endforeach;
            //}

            foreach ($data as $riga) :
              $i++;
              if ($i<$iniziorighe) continue;
              if ($i%$blocco==0) {
                //finisce qui e richiama se stesso per fare un altro file
                file_put_contents($filenew, $current);
                $passaggio++;
                $url="https://labima.sw19.it/LABIMADB/creafileSQL.php?separator=$separator&idexperiment=$idexperiment&filename=$filename&passaggio=$passaggio";
                header("Location: $url");
                exit;
              }
              foreach ($header as $h) :
                $idtimes=$idtimeseries[$h];
                $query2="REPLACE INTO tb_time_series_data_temp (id_time_series,position,data) VALUES ($idtimes,$i,$riga[$h])";
                $current .= $query2.";\r\n";
                //setNotificheCRUD("admWeb","INFO","creafileSQL:",$query2);
              endforeach;
            endforeach;
           // you can do whatever you want with the $data.
           setNotificheCRUD("admWeb","SUCCESS","creafileSQL:","ora sono alla fine del ciclo");
           setNotificheCRUD("admWeb","SUCCESS","creafileSQL:","inserito nuovo file sql 1 ".$filenew);

          }
          setNotificheCRUD("admWeb","SUCCESS","creafileSQL:","inserito nuovo file sql 2 ".$filenew);
          file_put_contents($filenew, $current);

          $url="https://labima.sw19.it/LABIMADB/leggiFileSql.php?filename=$filename&idexperiment=$idexperiment";
          setNotificheCRUD("admWeb","SUCCESS","creafileSQL:","lancio ora $url");
          //header("Location: $url");
          //exit;

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_exec($ch);
          curl_close($ch);
          exit;
?>
