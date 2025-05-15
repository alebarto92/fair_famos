<?php $page="graph.php";?>
<?php include("INC_10_HEADER.php");?>
<?php include("INC_15_SCRIPTS.php");?>
<?php include("INC_20_NAVBAR.php");?>
<body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="container" id="firstcontainer">
          <?php //print_r($_POST);?>
            <?php for ($i=1;$i<$_POST['colnumber'];$i++) :
              if ($_POST['col'.$i]>0) {
                  $timeseries[]=$_POST['col'.$i];
                  if ($_POST['deltaT'.$i]>0) {
                    $offset[]=$_POST['deltaT'.$i];
                  } else {
                    $offset[]=0;
                  }
              }
            endfor;
            for ($i=0;$i<count($timeseries);$i++) :
              $a=$timeseries[$i];
              $query1="SELECT tbts.freq,tbts.id,CONCAT(tbe.title,' ',tbts.column_name) as name from tb_time_series tbts JOIN tb_experiments tbe ON tbe.id=tbts.id_experiment where tbts.id=$a";
              $stmt1=$dbh->query($query1);
              $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
              $trace[$i]['name']=$row1['name'];
              if ($row1['freq']>0) {
                $pos=1/$row1['freq'];
              } else {
                $pos=1;
              }

              $query="SELECT * FROM tb_time_series_data where id_time_series=$a";
              //echo $query;
              $stmt=$dbh->query($query);
              while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $trace[$i]['x'][]=$offset[$i]/1000+$row['position']*$pos;
                $trace[$i]['y'][]=$row['data'];
              }

            endfor;
            //echo "<pre>";
            //print_r($trace);
            //echo "</pre>";

            ?>
              </div>
            </div>

            <div id="myDiv"></div>
            <hr>
            <?php include("INC_90_FOOTER.php");?>
        </div> <!-- /container -->
<script src="https://cdn.plot.ly/plotly-2.8.3.min.js"></script>
<script>
$(document).ready(function() {
  <?php for ($i=0;$i<count($trace);$i++) :
    $arraytracce[]="trace".$i;
    $elencox=join(",",$trace[$i]['x']);
    $elencoy=join(",",$trace[$i]['y']);
    ?>
  var trace<?php echo $i;?> = {
    x: [<?php echo $elencox;?>],
    y: [<?php echo $elencoy;?>],
    name : '<?php echo $trace[$i]['name'];?>',
    type: 'scatter'
  };


  <?php endfor;
  $elencotracce=join(",",$arraytracce);
  ?>

    var data = [<?php echo $elencotracce;?>];

    Plotly.newPlot('myDiv', data);


});

</script>
</body>
</html>
