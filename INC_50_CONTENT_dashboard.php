<div class="container" id="firstcontainer">
    <!-- Example row of columns -->
    <div class="row">
        <div class="broker-studio_dashboard">
            <ul class="a-btn-group ">
                <?php foreach ($moduli as $mod) :
                    $idmod=$mod['id_modulo'];
                    $permessi=permessi($idmod,$utente['id_ruolo'],$superuserOverride);

                    //se can_read=no allora non può vedere il modulo
                    if ($permessi['Can_read']=='no') continue;
                    $urlmodulo='module.php?id='.$idmod;
                    if ($mod['script_modulo']!='' and $mod['modulo_standard']=='no') $urlmodulo=$mod['script_modulo'];
                    ?>
                    <li><a href="<?php echo $sitedir;?><?php echo $urlmodulo;?>"><i class="<?php echo $mod['font_icon'];?>"></i><br /><span><?php echo _($mod['nome_modulo']);?></span></a></li>
                <?php endforeach; ?>
                    <!--<li><a href="<?php echo $sitedir;?>contattaci.php"> <i class="fa fa-envelope"></i> <br /><span>Contattaci</span> </a></li>-->
                    <!--<li><a href="<?php echo $sitedir;?>schede-prodotto.php?tipofile=allegato"> <i class="fa fa-paperclip"></i> <br /><span>Schede Prodotto</span> </a></li>-->

            </ul>
        </div>
    </div>
    <hr>
    <?php include("INC_90_FOOTER.php");?>
</div> <!-- /container -->