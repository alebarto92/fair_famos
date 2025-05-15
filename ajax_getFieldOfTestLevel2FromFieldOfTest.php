<?php
session_start();
if($_SESSION['sitosospeso'] == "1"){
    @header("Location:utente-sospeso.php");
}
include("config.php");

$idfieldoftest=$_POST['idfieldoftest'];

if ($idfieldoftest>0) {
    $query="SELECT * FROM tb_field_of_test_level_2 WHERE id_level1='".$idfieldoftest."'";
    if ($stmt=$dbh->query($query)) {
        $modelli=Array();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $modelli[]=$row;
        }
        $ret['result']=true;
        $ret['query']=$query;
        $ret['modelli']=$modelli;
        echo json_encode($ret);
    } else {
        $ret['result']=false;
        $ret['error']="Error on db access";
        echo json_encode($ret);
    }
} else {
    $ret['result']=false;
    $ret['error']="Parameters are not valid";
    echo json_encode($ret);
}


?>
