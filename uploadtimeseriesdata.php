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

          ?>
          <div>Data successfully loaded. Go back to the <a href="module.php?id=94">experiment page</a> or to the <a href="graph_selection.php">graph page</a> to see them.</div>
          <?php

        } else {
            $ret['result']=false;
            //setNotificheCRUD("admWeb","ERROR","uploadtimeseriesdata:","errore copia ".$filename);
            echo json_encode($ret);
            exit;
        }

    }
}
?>

</div>
<script>
  $(window).load(function() {
    var url="creafileSQL.php?separator=<?php echo $separator;?>&idexperiment=<?php echo $idexperiment;?>&filename=<?php echo $filename;?>";
    console.log(url)
  $.ajax({
                    type: "GET",
                    url: url,
                    dataType: 'json',
                    success: function(data){
                        if (data.result==true) {
                          console.log(data);
                        } else {
                            console.log(data);
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

              });

</script>
</body>
</html>
