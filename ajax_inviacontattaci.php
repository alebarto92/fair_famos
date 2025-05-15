<?php 
session_start();

include("config.php");

$messaggio=$_REQUEST['messaggio'];
$idcliente=$_REQUEST['idcliente'];

$query="SELECT * FROM pcs_clienti WHERE id=?";
$stmt=$dbh->prepare($query);
$stmt->execute(array($idcliente));
$CLIENTE=$stmt->fetch(PDO::FETCH_ASSOC);

require 'class.phpmailer.php';

$mail = new PHPMailer;

$mail->SMTPDebug = 0;
$mail->IsSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp-relay.sendinblue.com';                 // Specify main and backup server
$mail->Port = 587;                                    // Set the SMTP port
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'info@studioweb19.it';                  // SMTP username
$mail->Password = 'tBMDcv8Enj4YsGp7';           // SMTP password studioweb19
$mail->SMTPSecure = 'SSL';                            // Enable encryption, 'ssl' also accepted

$mail->AddReplyTo($CLIENTE['Mail'], $CLIENTE['Cliente']);

$mail->AddBCC($EMAILADMINBCC);              // Name is optional
$mail->SetFrom('info@studioweb19.it', $project_title);

$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $project_title.' - Contattaci';
$mail->Body    = $messaggio;

if ($mail->send()) { ?>
<div class="alert alert-success" role="alert">
		<div class="center">
			Messaggio inviato con successo!
  		</div>
</div>
    <?php
}
else
{ ?>
<div id="loginerror" class="alert alert-danger alert-dismissible" role="alert">
		<div class="center">
  <strong><?php echo _("Errore!");?></strong> <?php echo _("Messaggio non inviato!");?>
  		</div>
</div>
    <script>
	setTimeout(function(){$("#loginerror").hide();}, 2000);
	</script>
<?php  } ?>