<?php
require_once '../../vendor/autoload.php'; // caminho para o autoload do Composer
require '../../app/database/connection.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();

$qrCodeUrl = $ga->getQrCodeGoogleUrl('TijucasOpen', $secret);

$query = "UPDATE tb_logins SET auth_secret = ? WHERE logins_id = ?";
$conn = conecta_db();
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $secret, $_SESSION['logins_id']);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<h2>Escaneie o QR Code no app Google Authenticator</h2>
<img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
<p>Ou insira esse código: <strong><?php echo $secret; ?></strong></p>

<form method="post" action="index.php?page=verificar2fa">
    <label for="codigo">Digite o código exibido no seu app Google Authenticator:</label>
    <input type="text" name="codigo" id="codigo" required>
    <button type="submit">Verificar Código</button>
</form>