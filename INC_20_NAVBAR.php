<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
    <div class="container">

        <div class="navbar-header" style="width:100%;">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><?php echo $projecttitle;?></a>

        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">

                <li><a href="<?php echo $sitedir;?>"> <i class="fa fa-tachometer"></i> Home </a></li>
                <?php if ($utente['id_ruolo']!=4)  { //cliente ?>

                <?php } ?>
                <?php /* (i) ------------------------------------------------------------ MODULI --------------------------------------- */ ?>
                <?php $parsSidebar['stato']='attivo';$parsSidebar['menu']='si';$moduli=getModuli($parsSidebar); ?>

                <?php foreach ($moduli as $mod) :
                    $idmod=$mod['id_modulo'];
                    $permessi=permessi($idmod,$utente['id_ruolo'],$superuserOverride);

                    //se can_read=no allora non può vedere il modulo
                    if ($permessi['Can_read']=='no') continue;
                    $urlmodulo='module.php?id='.$idmod;
                    if ($mod['script_modulo']!='' and $mod['modulo_standard']=='no') $urlmodulo=$mod['script_modulo'];
                    ?>

                    <li <?php if (($_SERVER['REQUEST_URI'] ==$sitedir.$urlmodulo) || ($_GET[modname]==$mod['nome_modulo'])){ ?>class="active" <?php } ?>>
                        <a href="<?php echo $sitedir;?><?php echo $urlmodulo;?>">
                            <i class="menu-icon <?php echo $mod['font_icon'];?>"></i>
                            <span class="menu-text"> <?php echo _($mod['nome_modulo']);?> </span>
                        </a>
                        <b class="arrow"></b>
                    </li>

                <?php endforeach; ?>
                <?php /* (f) ------------------------------------------------------------ MODULI --------------------------------------- */ ?>

                <?php /* ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-star"></i> Clienti <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#"> <i class="fa fa-plus"></i> Nuovo Cliente</a></li>
                        <li><a href="#"> <i class="fa fa-list-ul"></i> Tutti i Clienti </a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-umbrella"></i> Polizze <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#"> <i class="fa fa-plus"></i> Nuova Polizza</a></li>
                        <li><a href="#"> <i class="fa fa-list-ul"></i> Tutte le polizze </a></li>
                        <li><a href="#"> <i class="fa fa-book"></i> Tipi di polizza</a></li>
                    </ul>
                </li>
                <li><a href="#"> <i class="fa fa-euro"></i> Incassi &nbsp;<span class="badge badge-important pull-right">4</span></a></a></li>
                <li><a href="#"> <i class="fa fa-file-text"></i> Fatturazione </a></li>
                <li><a href="#"> <i class="fa fa-bolt"></i> Sinistri</a></li>
                <li><a href="#"> <i class="fa fa-calendar"></i> Promemoria &nbsp;<span class="badge badge-important pull-right">2</span></a></li>

                <?php */ ?>

                    <!--<li><a href="<?php echo $sitedir;?>contattaci.php"> <i class="fa fa-envelope"></i> Contattaci </a></li>-->
                    <!--<li><a href="<?php echo $sitedir;?>schede-prodotto.php?tipofile=allegato"> <i class="fa fa-paperclip"></i> Schede Prodotto </a></li>-->
                    <!--<li><a href="graph.php"> <i class="fa fa-chart-bar"></i> Graph </a></li>-->
                <li><a href="logout.php"> <i class="fa fa-sign-out"></i> Logout </a></li>

            </ul>


        </div><!--/.navbar-collapse -->
    </div>
</nav>
