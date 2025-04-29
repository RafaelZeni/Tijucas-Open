<?php
require '../app/database/connection.php'; // função conecta_db()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['emailREC'];
    $novaSenha = $_POST['passREC'];
    $confirmaSenha = $_POST['passConfirm'];  // A senha confirmada

    // Verifica se as senhas coincidem
    if ($novaSenha !== $confirmaSenha) {
        echo "<script>alert('As senhas não coincidem. Por favor, tente novamente.'); window.location.href = 'index.php?page=recsenha';</script>";
        exit;
    }

    // Conecta ao banco de dados
    $conn = conecta_db();

    // Verifica o tipo de usuário (se é 'proprietario')
    $stmt = $conn->prepare("SELECT tipo_usu FROM tb_logins WHERE email_usu = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado && $resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();

        // Verifica se o tipo de usuário é 'proprietario'
        if ($row['tipo_usu'] === 'proprietario') {
            echo "<script>alert('Usuários do tipo Proprietário não podem alterar a senha.'); window.location.href = 'index.php?page=entrar';</script>";
            exit;
        }

        // Se o tipo de usuário não for 'proprietario', faz o hash da nova senha
        $novaSenhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Prepara a consulta para atualizar a senha
        $stmt = $conn->prepare("UPDATE tb_logins SET senha_usu = ? WHERE email_usu = ?");
        $stmt->bind_param("ss", $novaSenhaHash, $email);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Senha atualizada com sucesso!'); window.location.href = 'index.php?page=entrar';</script>";
            } else {
                echo "<script>alert('E-mail não encontrado.'); window.location.href = 'index.php?page=recsenha';</script>";
            }
        } else {
            echo "<script>alert('Erro ao atualizar senha: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('E-mail não encontrado.'); window.location.href = 'index.php?page=recsenha';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Recuperar Senha - Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>
<body class="login">

<section class="banner">
    <img src="conteudo_livre/assets/imgs/maisde40espaços.jpeg" alt="Estacionamento Tijucas Open">
    <div class="quadrado">
        <section class="form-container">
            <h3>Recuperar Senha</h3>
            <form method="post" class="forms">
                <label for="emailREC">Email</label>
                <input type="email" id="emailREC" name="emailREC" required placeholder="Digite seu e-mail">

                <label for="passREC">Nova Senha</label>
                <input type="password" id="passREC" name="passREC" required placeholder="Digite a nova senha">

                <label for="passConfirm">Confirmar Nova Senha</label>
                <input type="password" id="passConfirm" name="passConfirm" required placeholder="Confirme sua nova senha">

                <button type="submit" class="enviar">Atualizar Senha</button>

                <a class="recsenha" href="index.php?page=entrar">Voltar para Login</a>
            </form>
        </section>
    </div> 
</section>
</body>
</html>
