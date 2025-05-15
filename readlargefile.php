<?php $page="readlargefile.php";?>
<?php include("INC_10_HEADER.php");?>
<?php include("INC_15_SCRIPTS.php");?>
<?php include("INC_20_NAVBAR.php");?>
<body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="container" id="firstcontainer">
            <!-- Example row of columns -->
            <div class="row">
              <div class="col col-xs-12">
                <h3>Choose columns to plot</h3>


                <?php
                $columnsnumber=11;
                $query="SELECT tbts.id,CONCAT(tbe.title,' ',tbts.column_name) as name from tb_time_series tbts JOIN tb_experiments tbe ON tbe.id=tbts.id_experiment ";
                $stmt=$dbh->query($query);
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $timeseries[$row['id']]=$row['name'];
                }
                ?>
                <form action="graph2.php" method="post">
                  <input type="hidden" id="colnumber" name="colnumber" value="<?php echo $columnsnumber;?>">
                <?php for ($i=1;$i<$columnsnumber;$i++) : ?>
                <div class="col-xs-12 col-sm-4">
                <select class="form-control" name="col<?php echo $i;?>" id="col<?php echo $i;?>">
                  <option value=""> --- </option>
                  <?php foreach ($timeseries as $key=>$value) : ?>
                    <option value="<?php echo $key;?>"><?php echo $value;?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <?php endfor; ?>

            </div>
              <div class="row">
                <div class="col-xs-12">
                  <input type="submit" id="showgraph" class="btn btn-success" value="Show Graph"/>
                </div>
              </div>

              </div>
            </div>

            <div id="myDiv"></div>
            <hr>
            <?php include("INC_90_FOOTER.php");?>
        </div> <!-- /container -->
</body>
</html>
