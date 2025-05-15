<?php

include("config.php");

if ($_POST['idreport']>0) {

    $dir2=md5($_POST['idreport']);

    $data = $_POST['Value'];

    $i=$_POST['i'];


    if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
        $data = substr($data, strpos($data, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            throw new \Exception('invalid image type');
        }

        $data = base64_decode($data);

        if ($data === false) {
            throw new \Exception('base64_decode failed');
        }
    } else {
        throw new \Exception('did not match data URI with image data');
    }


    $nome=$_POST['idreport']."-".$i.".{$type}";

    //questo va sistemato!!!
    if (!file_exists($_SERVER['DOCUMENT_ROOT'].$directoryfiles."/Reports/$dir2-".$_POST['idreport'])) {

        $dirtobecreated=$_SERVER['DOCUMENT_ROOT'].$directoryfiles."/Reports/$dir2-".$_POST['idreport'];

        mkdir ($dirtobecreated);
        chmod ($dirtobecreated,0777);

    }


    $immagine=$_SERVER['DOCUMENT_ROOT'].$directoryfiles."/Reports/$dir2-".$_POST['idreport']."/".$nome;
    $immagineurl="https://".$_SERVER['HTTP_HOST'].$directoryfiles."/Reports/$dir2-".$_POST['idreport']."/".$nome;

    //salvo l'url dentro la tabella report, così la posso richiamare poi quando genero il pdf

    $tbreports      =$GLOBAL_tb['reports'];

    $query1="SELECT * FROM $tbreports WHERE id=?";
    $stmt1=$dbh->prepare($query1);
    $stmt1->execute(array($_POST['idreport']));
    $REPORT=$stmt1->fetch(PDO::FETCH_ASSOC);
    $immagini=json_decode($REPORT['immagini'],true);
    if (count($immagini)>0) {
        foreach ($immagini as $im) {
            $imageskeys[$im]=1;
        }
    }

    if (file_put_contents($immagine, $data)) {

        $ret['nome']=$nome;
        $ret['immagine']=$immagine;
        $ret['immagineurl']=$immagineurl;

        //ora salvo in jpg
        $image = imagecreatefrompng($immagine);
        $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        imagealphablending($bg, TRUE);
        imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        imagedestroy($image);
        $quality = 100; // 0 = worst / smaller file, 100 = better / bigger file
        imagejpeg($bg, $immagine . ".jpg", $quality);
        imagedestroy($bg);

        $immaginejpg=$immagine.".jpg";

        $ret['immaginejpg']=$immaginejpg;
        $ret['immagineurljpg']=$immagineurl.".jpg";


        setNotificheCRUD("admWeb","SUCCESS","saveImageReports:",$immagine);
        $ret['result']=true;
        $imageskeys[$immaginejpg]=1;

        $immagininuove=array_keys($imageskeys);

        $queryUpdate="UPDATE $tbreports SET immagini=? WHERE id=?";
        $stmtU=$dbh->prepare($queryUpdate);
        $stmtU->execute(array(json_encode($immagininuove),$_POST['idreport']));
    } else {
        setNotificheCRUD("admWeb","ERROR","saveImageReports:",$immagine);
        $ret['result']=false;
    }
    echo json_encode($ret);
    exit;


} else {
    echo "eccoci qui";
}


