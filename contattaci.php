<?php $pagina="clienti";?>
<?php include("INC_10_HEADER.php");?>
<?php include("INC_15_SCRIPTS.php");?>
<body>
<?php include("INC_20_NAVBAR.php");?>
<div class="container" id="firstcontainer">
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="index.php">Home</a>
            </li>
            <li>
                Contattaci
            </li>
        </ul>
    </div>

    <div class="page-header">
        <h2>
            <i class="fa fa-envelope"></i> Contattaci
        </h2>
    </div><!-- /.page-header -->
        <form id="emailform">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label for="messaggio" class="input-desc">Messaggio</label>
                        <textarea placeholder="Testo del messaggio" class="form-control form-text-area" id="messaggio" name="messaggio" rows="4" ></textarea>
                        <input type="hidden" name="idcliente" id="idcliente" value="<?php echo $utente['id_cliente'];?>"/>
                        <div class="form-group" >
                            <br/><br/>
                            <button id="inviacontattaci" class="btn btn-warning">Invia Messaggio</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div id="responso"></div>


    <!-- custom script -->
<script>
    $(document).ready(function() {
        $("#inviacontattaci").click(function(e){
            e.preventDefault();
            var testomessaggio=$("#messaggio").val();
            if (testomessaggio=='') {
                alert("ERRORE! Il messaggio è vuoto!");
            } else {
                $.post("ajax_inviacontattaci.php", $("#emailform").serialize(), function(msg){$("#responso").html(msg);} );
            }
        });

    } );
</script>
</div>
<hr>
<?php include("INC_90_FOOTER.php");?>

</body>
</html>
