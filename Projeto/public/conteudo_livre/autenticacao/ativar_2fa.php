<?php
 // Certifique-se de iniciar a sessão
require_once '../../vendor/autoload.php';
require '../../app/database/connection.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$conn = conecta_db();

// 1. Buscar auth_secret existente
$query = "SELECT auth_secret FROM tb_logins WHERE logins_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['logins_id']);
$stmt->execute();
$result = $stmt->get_result();
$linha = $result->fetch_assoc();

// 2. Verifica se já existe um secret
if (!empty($linha['auth_secret'])) {
    $secret = $linha['auth_secret'];
} else {
    // 3. Se não existir, gera um novo e salva
    $secret = $ga->createSecret();
    $qrCodeUrl = $ga->getQrCodeGoogleUrl('TijucasOpen', $secret);

    $stmt = $conn->prepare("UPDATE tb_logins SET auth_secret = ? WHERE logins_id = ?");
    $stmt->bind_param("si", $secret, $_SESSION['logins_id']);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Geração do QR code
$qrCodeUrl = $ga->getQrCodeGoogleUrl('TijucasOpen', $secret);
?>

<h2>Escaneie o QR Code no app Google Authenticator</h2>
<img src="<?php echo $qrCodeUrl; ?>" alt="QR Code">
<p>Ou insira esse código: <strong><?php echo $secret; ?></strong></p>

<form method="post" action="/Tijucas-Open/Projeto/public/locatário/index.php?page=verificar2fa">
    <label for="codigo">Digite o código exibido no seu app Google Authenticator:</label>
    <input type="text" name="codigo" id="codigo" required>
    <button type="submit">Verificar Código</button>
</form>
