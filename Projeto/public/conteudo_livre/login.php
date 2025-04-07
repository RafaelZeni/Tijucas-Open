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
                    header("Location: ./proprietário/proprietario.php"); 
                    break;
                case 'locatario':
                    header("Location: ./locatário/locatario.php");
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

<!-- HTML aqui embaixo SEM ELSE -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/login.css">
</head>
<body>

<section class="banner">
    <img src="conteudo_livre/assets/imgs/maisde40espaços.jpeg" alt="Estacionamento Tijucas Open">
    <div class="quadrado">
        <section class="form-container">
            <h3>Entrar</h3>
            <form method="post">
                <label for="emailLog">Email</label>
                <input type="email" id="emailLog" name="emailLog" required placeholder="Digite seu e-mail">

                <label for="passLog">Senha</label>
                <input type="password" id="passLog" name="passLog" required placeholder="Digite sua senha">

                <button type="submit" class="enviar">Enviar</button>
            </form>
        </section>
    </div> 
</section>

<footer>
    <div class="info">
        <div class="horarios">
            <h4>Horários:</h4>
            <p>Lojas: Seg a Sáb: 10:00 às 22:00 | Dom: 11:00 às 21:00</p>
            <p>Alimentação: Seg a Sáb: 12:00 às 23:00 | Dom: 12:00 às 22:00</p>
        </div>
        <div class="endereco">
            <h4>Endereço:</h4>
            <p>Rua XV de Novembro, 1306, Tijucas-SC</p>
        </div>
        <div class="contato">
            <h4>Contato:</h4>
            <p>Para mais informações, ligue para: XXX-XXXX-XXXX</p>
        </div>
        <div class="estacionamento">
            <h4>Estacionamento:</h4>
            <p>Gratuito todos os dias da semana</p>
        </div>
    </div>
</footer>

<script src="./script.js"></script>
</body>
</html>
