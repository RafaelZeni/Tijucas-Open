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
<form method="post">
    <label>Digite o código do Google Authenticator: </label>
    <input type="text" name="codigo" required>
    <button type="submit">Verificar</button>
    <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
</form>