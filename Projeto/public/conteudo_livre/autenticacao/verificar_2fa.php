<?php
require_once '../../vendor/autoload.php';
require '../../app/database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = $_POST['codigo'];
    $conn = conecta_db();

    $query = "SELECT auth_secret FROM tb_logins WHERE logins_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['logins_id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $linha = $resultado->fetch_object();

    if ($linha && $linha->auth_secret) {
        $ga = new PHPGangsta_GoogleAuthenticator;
        $secret = $linha->auth_secret;
        $checkResultado = $ga->verifyCode($secret, $codigo, 4); // tolerância de 2 min

        if ($checkResultado) {
            $_SESSION['2fa_passed'] = true;

            switch ($_SESSION['tipo_usu']) {
                case 'proprietario':
                    header("Location: /Tijucas-Open/Projeto/public/proprietário/index.php");
                    break;
                case 'locatario':
                    header("Location: /Tijucas-Open/Projeto/public/locatário/index.php");
                    break;
                default:
                    header("Location: /Tijucas-Open/Projeto/public/index.php?page=entrar");
                    break;
            }
            exit;
        } else {
            $erro = "Código incorreto!";
        }
    } else {
        $erro = "2FA não configurado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Verificação 2FA - Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
        }


        .conteudo-banner {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 90%;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-size: 20px;
            color: #333;
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
        }

        button:hover {
            background-color: #2e471f;
        }

        .erro {
            color: red;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="conteudo-banner">
        <form method="post">
            <label for="codigo">Digite o código do Google Authenticator:</label>
            <input type="text" name="codigo" id="codigo" required>
            <button type="submit">Verificar</button>
            <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
        </form>
    </div>
</body>
</html>
