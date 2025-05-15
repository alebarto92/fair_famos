<?php $page="loadtimeseriesfile.php";?>
<?php include("INC_10_HEADER.php");?>
<?php include("INC_15_SCRIPTS.php");?>
<body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <div class="container" id="firstcontainer">
            <!-- Example row of columns -->
            <div class="row">
              <div class="col col-xs-12">
                <h3>LOAD DATA (csv file or tsv file or ;sv file)</h3>
                <h4>First row of file must contain the names of the columns</h4>

        <form id="uploadform" action="uploadtimeseriesdata.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="idexperiment" name="idexperiment" value="<?php echo $_GET['idexperiment'];?>"/>
<div class="row">
            <div class="col-xs-12 col-sm-6">
              <select class="form-control" name="separator" id="separator">
                <option value="">... Please choose separator value...</option>
                <option value="virgola">Comma separated values (,)</option>
                <option value="puntoevirgola">Semicolon separated values (;)</option>
                <option value="tab">Tab separated values (\t)</option>
              </select>
            </div>
            <div class="col-xs-12 col-sm-8">
            <input class="form-control" type="file" name="file">
            </div>
            <div class="col-xs-12 col-sm-8">
            <br/>
            <a class="btn btn-primary" name="upload" id="upload">Upload file</a>
          </div>
</div>
        </form>

        <br/>
        <div class="row">
          <div class="col-xs-12">
            <div id="loader" style="display:none;">
              <h4>Please wait... we are loading data into database.</h4>
              <div class="loader"></div>
            </div>
          </div>
        </div>

              </div>
            </div>
            <hr>
            <?php include("INC_90_FOOTER.php");?>
        </div> <!-- /container -->

<script>
$(document).ready(function() {

  $("#upload").click(function(){
    $( "#upload").attr('disabled','disabled');
    $( "#loader").show();
    document.getElementById("uploadform").submit();
    var url="https://labima.sw19.it/LABIMADB/";
    window.open(url, '_blank').focus();
  })

});

</script>
</body>
</html>
