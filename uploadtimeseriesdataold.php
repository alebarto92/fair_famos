<?php $pagina="upload";?>
<?php include("db_connect.php");?>
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


        if (move_uploaded_file($file['tmp_name'], $uploadDir.DIRECTORY_SEPARATOR.$filename)) {

          $rows = array_map('str_getcsv', file($uploadDir.DIRECTORY_SEPARATOR.$filename));
          $header = array_shift($rows);
          $csv = array();
          foreach ($rows as $row) {
            $csv[] = array_combine($header, $row);
          }

          //ora la parte di mysql
          foreach ($header as $h) :
            $query="SELECT * FROM tb_time_series WHERE id_experiment=$idexperiment AND column_name='$h'";
            $stmt=$dbh->query($query);
            if ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
              $idtimeseries[$h]=$row['id'];
            } else {
              $query1="INSERT INTO tb_time_series (id_experiment,column_name) VALUES ($idexperiment,'$h')";
              $stmt1=$dbh->query($query1);
              $idtimeseries[$h]=$dbh->lastInsertID();
            }
          endforeach;

          $i=0;
          foreach ($csv as $rowcsv) :
            $i++;
            foreach ($header as $h) :
              $idtimes=$idtimeseries[$h];
              $query2="REPLACE INTO tb_time_series_data (id_time_series,position,data) VALUES ($idtimes,$i,$rowcsv[$h])";
              if ($stmt2=$dbh->query($query2)) {

              } else {
                $errori.="Error in inserting data for $h at position $i<br/>";
              }
            endforeach;
          endforeach;

          if ($errori!='') {
            echo $errori;
          } else {
            echo "Successful!! You have inserted $i rows for ".count($header)." columns";
          }

        } else {
            $ret['result']=false;
            echo json_encode($ret);
            exit;
        }

    }
}
?>
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
