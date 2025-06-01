<?php

require_once '../../vendor/autoload.php';
require '../../app/database/connection.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$conn = conecta_db();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logins_id'])) {

    header("Location: ../../public/login.php"); 
    exit();
}

$logins_id = $_SESSION['logins_id'];
$accountName = 'Proprietário'; 

$query = "SELECT auth_secret, tipo_usu FROM tb_logins WHERE logins_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $logins_id);
$stmt->execute();
$result = $stmt->get_result();
$linha = $result->fetch_assoc();

if (!empty($linha['auth_secret'])) {
    $secret = $linha['auth_secret'];
} else {
    $secret = $ga->createSecret();

    $stmt_update = $conn->prepare("UPDATE tb_logins SET auth_secret = ? WHERE logins_id = ?");
    $stmt_update->bind_param("si", $secret, $logins_id);
    $stmt_update->execute();
    $stmt_update->close();
}

if ($linha['tipo_usu'] === 'locatario') {
    $query_locatario = "SELECT empresa_nome FROM tb_locatarios WHERE logins_id = ?";
    $stmt_locatario = $conn->prepare($query_locatario);
    $stmt_locatario->bind_param("i", $logins_id);
    $stmt_locatario->execute();
    $result_locatario = $stmt_locatario->get_result();
    $locatario_data = $result_locatario->fetch_assoc();

    if ($locatario_data && !empty($locatario_data['empresa_nome'])) {
        $accountName = $locatario_data['empresa_nome'];
    }
    $stmt_locatario->close();
}

$stmt->close();
$conn->close();

// Geração do QR code
$qrCodeUrl = $ga->getQrCodeGoogleUrl($accountName, $secret);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Configuração 2FA - Tijucas Open</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
            position: relative;
            background-color: white;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .banner {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .conteudo-banner {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            max-width: 450px;
            width: 90%;
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            font-size: 24px;
            color: #333;
        }

        img {
            max-width: 200px;
            margin-bottom: 25px;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #333;
        }

        p strong {
            font-weight: bold;
            color: #3b5c2f;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-size: 20px;
            color: #333;
            text-align: left;
        }

        input[type="text"] {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background-color: #3b5c2f;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2e471f;
        }
    </style>
</head>
<body>

    <div class="banner">
        <img src="bannerCachoeira.jpg" alt="Imagem de fundo" />
    </div>

    <div class="conteudo-banner">
        <h2>Escaneie o QR Code no app Google Authenticator</h2>
        <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code" />
        <p>Ou insira esse código: <strong><?php echo htmlspecialchars($secret); ?></strong></p>

        <form method="post" action="/Tijucas-Open/Projeto/public/locatário/index.php?page=verificar2fa">
            <label for="codigo">Digite o código exibido no seu app Google Authenticator:</label>
            <input type="text" name="codigo" id="codigo" required />
            <button type="submit">Verificar Código</button>
        </form>
        <button onclick="window.location.href='index.php'" style="margin-top: 20px; padding: 10px 15px; border-radius: 6px; border: none; background-color:rgb(0, 0, 0); color: white; cursor: pointer;">
            Voltar
        </button>
    </div>

</body>
</html>