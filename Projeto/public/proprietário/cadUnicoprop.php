<?php
include '../../app/views/include/header.php';
require '../../app/database/connection.php'; // função conecta_db()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['emailCAD'];
    $senha    = $_POST['passCAD'];

    // Criptografa a senha antes de enviar pra procedure
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $conn = conecta_db();

    // Chamada da procedure
    $stmt = $conn->prepare("CALL pr_AdicionarProprietario(?, ?)");
    $stmt->bind_param("ss", $email, $senhaHash);

    if ($stmt->execute()) {
        echo "<script>alert('Locatário cadastrado com sucesso!'); window.location.href = 'login_data.html';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $conn->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="">
</head>
<body>

<section class="banner">
    <img src="./assets/imgs/maisde40espaços.jpeg" alt="Estacionamento Tijucas Open">
    <div class="quadrado">
        <section class="form-container">
            <h3>Entrar</h3>
            <form method="post">
                <label for="emailCAD">Email</label>
                <input type="email" id="emailCAD" name="emailCAD" required placeholder="Digite o E-mail">

                <label for="passCAD">Senha</label>
                <input type="password" id="passCAD" name="passCAD" required placeholder="Digite a senha">

                <button type="submit" class="enviar">Enviar</button>
            </form>
        </section>
    </div> 
</section>
</body>
</html>
