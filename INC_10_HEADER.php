<?php include 'config.php';
if ($_SESSION['pcs_id_user']>0) {

    //$utente=getUtente($_SESSION['pcs_id_user']);

} else {

    if ($_SERVER['SCRIPT_NAME'] != $sitedir . "login.php") {
        header('Location:' . $sitedir."login.php");
        exit;
    }
}?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo $projecttitle;?> LABIMA DB <?php //echo ini_get('post_max_size'); ?>
</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/responsivetables.css">

    <style>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">

    <!-- (i) 3rd party components -->
    <link rel="stylesheet" href="3rd-party/chosen/chosen.min.css">
    <!-- (f) 3rd party components -->

    <link rel="stylesheet" media="screen" href="css/main.css">
    <?php if ($page=="Calendario") { ?>
      <link rel="stylesheet" media="print" href="css/print.css" />
    <?php } ?>

    <link rel="stylesheet" href="css/hover-min.css" >
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css">


    <link rel="stylesheet" href="3rd-party/bootstrap-datetimepicker-monim67.github.io/bootstrap-datetimepicker.min.css" />
    <link rel="stylesheet" href="3rd-party/bootstrap-colorpicker-2.x/dist/css/bootstrap-colorpicker.min.css" />



    <link rel="stylesheet" href="jqplot/jquery.jqplot.min.css">
    <link rel="stylesheet/less" href="css/timepicker.less" />

    <link rel="icon" href="favicon.ico" type="image/png" />
    <link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet"/>

    <?php if ($page=="Calendario" || $page=="CalendarioDoppio") { ?>
        <link href='fullcalendar-4.3.1/packages/core/main.css' rel='stylesheet' />
        <link href='fullcalendar-4.3.1/packages/daygrid/main.css' rel='stylesheet' />
        <link href='fullcalendar-4.3.1/packages/timegrid/main.css' rel='stylesheet' />
        <link href='fullcalendar-4.3.1/packages/list/main.css' rel='stylesheet' />
    <?php } ?>


    <script src="https://kit.fontawesome.com/7077614094.js" crossorigin="anonymous"></script>
    <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
</head>
