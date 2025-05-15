<?php $page="graph.php";?>
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
            <div class="col col-xs-12 col-sm-3 col-md-4">
                <h3>FILTERS</h3>
                <div class="row">
                  <div class="col-xs-12 col">
                    <br/>Type of forcing
                    <?php
                      $query="SELECT id as id,Type_of_forcing as value FROM tb_type_of_forcing order by value";
                      $sth = $dbh->prepare($query);
                      $sth->execute();

                      /* Fetch all of the remaining rows in the result set */
                      $result01 = $sth->fetchAll();
                    ?>
                  <select multiple class="form-control experiment_filter chosen-select" name="type_of_forcing" id="type_of_forcing">
                    <?php foreach($result01 as $r) : ?>
                      <option value="<?php echo $r['id'];?>"><?php echo $r['value'];?></option>
                    <?php endforeach; ?>
                  </select>
                  </div>
                  <div class="col-xs-12 col">
                    <br/>Field of test
                    <?php
                      $query="SELECT id as id,field_of_test as value FROM tb_field_of_test order by value";
                      $sth = $dbh->prepare($query);
                      $sth->execute();

                      /* Fetch all of the remaining rows in the result set */
                      $result01 = $sth->fetchAll();
                    ?>
                  <select multiple class="form-control experiment_filter chosen-select" name="field_of_test" id="field_of_test">
                    <?php foreach($result01 as $r) : ?>
                      <option value="<?php echo $r['id'];?>"><?php echo $r['value'];?></option>
                    <?php endforeach; ?>
                  </select>
                  </div>
                  <div class="col-xs-12 col">
                    <br/>Infrastructure
                    <?php
                      $query="SELECT id as id,infrastructure as value FROM tb_infrastructure order by value";
                      $sth = $dbh->prepare($query);
                      $sth->execute();

                      /* Fetch all of the remaining rows in the result set */
                      $result01 = $sth->fetchAll();
                    ?>
                  <select multiple class="form-control experiment_filter chosen-select" name="infrastructure" id="infrastructure">
                    <?php foreach($result01 as $r) : ?>
                      <option value="<?php echo $r['id'];?>"><?php echo $r['value'];?></option>
                    <?php endforeach; ?>
                  </select>
                  </div>
                  <div class="col-xs-12 col">
                    <br/>Laboratory
                    <?php
                      $query="SELECT id as id,Lab_name as value FROM tb_Laboratory order by value";
                      $sth = $dbh->prepare($query);
                      $sth->execute();

                      /* Fetch all of the remaining rows in the result set */
                      $result01 = $sth->fetchAll();
                    ?>
                  <select multiple class="form-control experiment_filter chosen-select" name="laboratory" id="laboratory">
                    <?php foreach($result01 as $r) : ?>
                      <option value="<?php echo $r['id'];?>"><?php echo $r['value'];?></option>
                  <?php endforeach; ?>
                  </select>
                  </div>
                  <div class="col-xs-12 col">
                    <br/>Installation
                    <?php
                      $query="SELECT id as id,installation as value FROM tb_installations order by value";
                      $sth = $dbh->prepare($query);
                      $sth->execute();

                      /* Fetch all of the remaining rows in the result set */
                      $result01 = $sth->fetchAll();
                    ?>
                  <select multiple class="form-control experiment_filter chosen-select" name="installation" id="installation">
                    <?php foreach($result01 as $r) : ?>
                      <option value="<?php echo $r['id'];?>"><?php echo $r['value'];?></option>
                    <?php endforeach; ?>
                  </select>
                  </div>
                </div>
              </div>
              <div class="col col-xs-12 col-sm-9 col-md-8">
                <h3>RESULTS</h3>
                <div id="mydiv"></div>

        
            </div>


            </div>
            <hr>
            <?php include("INC_90_FOOTER.php");?>
        </div> <!-- /container -->
</body>
</html>
