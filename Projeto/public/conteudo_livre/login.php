<?php
session_start();
require '../app/database/connection.php'; // função conecta_db()

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['emailLog'];
    $senha = $_POST['passLog'];

    $conn = conecta_db(); // usa sua função personalizada

    $stmt = $conn->prepare("SELECT logins_id, senha_usu, tipo_usu FROM tb_logins WHERE email_usu = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();

        if (password_verify($senha, $row['senha_usu'])) {
            $_SESSION['logins_id'] = $row['logins_id'];
            $_SESSION['tipo_usu'] = $row['tipo_usu'];

            switch ($row['tipo_usu']) {
                case 'proprietario':
                    header("Location: ./proprietário/index.php"); 
                    break;
                case 'locatario':
                    header("Location: ./locatário/index.php");
                    break;
                default:
                    header("Location: login.php");
                    break;
            }
            exit;
        } else {
            echo "<script>alert('Senha incorreta.');</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>
<body class="login">

<section class="banner">
    <img src="conteudo_livre/assets/imgs/maisde40espaços.jpeg" alt="Estacionamento Tijucas Open">
    <div class="quadrado">
        <section class="form-container">
            <h3>Entrar</h3>
            <form method="post" class="forms">
                <label for="emailLog">Email</label>
                <input type="email" id="emailLog" name="emailLog" required placeholder="Digite seu e-mail">

                <label for="passLog">Senha</label>
                <input type="password" id="passLog" name="passLog" required placeholder="Digite sua senha">

                <button type="submit" class="enviar">Enviar</button>

                <a class="recsenha" href="index.php?page=recsenha">Esqueceu sua senha?</a>
            </form>
        </section>
    </div> 
</section>
</body>
</html>
