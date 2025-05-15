<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$query=$_REQUEST['query'];

if ($query!='') {
    if ($stmt=$dbh->query($query)) {
        $experiments=Array();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $experiments[]=$row;
        }

        //ora costruisco il div
        $html="";
        for ($i=0;$i<count($experiments);$i++) {

          //ora cerco quante timeseries per ciascun experiment
          $query2="SELECT count(*) as tot FROM tb_time_series WHERE id_experiment=?";
          $stmt2=$dbh->prepare($query2);
          $stmt2->execute(array($experiments[$i]['id']));
          $row2=$stmt2->fetch(PDO::FETCH_ASSOC);
          $tottimeseries=$row2['tot'];

          if ($experiments[$i]['immagine']!='') {
            $pezzi=explode("/",$experiments[$i]['immagine']);
            $tmp=$pezzi[count($pezzi)-1];
            $pezzi[count($pezzi)-1]=$resizes[0]['prefisso']."_".$resizes[0]['width']."_".$resizes[0]['height']."_".$tmp;
            $nomefile=join("/",$pezzi);
          } else {
            $nomefile="img/experiment300.jpg";
          }
          $disabled="disabled";
          if ($experiments[$i]['allegato']!='') {
            $pezzi=explode("/",$experiments[$i]['allegato']);
            $tmp=$pezzi[count($pezzi)-1];
            $pezzi[count($pezzi)-1]=$tmp;
            $nomefileallegato=join("/",$pezzi);
            $disabled='';
          }
          $html.='<div class="col-xs-12 col-sm-6 col-md-4" style="margin-top:10px;" id="experiment1">
          <img style="width:200px;" src="'.$nomefile.'"><br/>'.
          $tottimeseries.' time series available<br/>'.
          $experiments[$i]["title"].'<br/><a class="btn btn-warning" href="graph.php?idexperiments='.$experiments[$i]["id"].'" >DATA</a>&nbsp;<a '.$disabled.' class="btn btn-info" target="_blank" href='.$nomefileallegato.'>INFO</a><br/></div>';
        }

        $ret['result']=true;
        $ret['msg']='Found '.count($experiments).' results';
        $ret['experiments']=$experiments;
        $ret['html']=$html;
        echo json_encode($ret);
    } else {
        $ret['result']=false;
        $ret['error']="Error accessing db ";
        echo json_encode($ret);
    }
} else {
    $ret['result']=false;
    $ret['error']="Parameters not correct";
    echo json_encode($ret);
}


?>
