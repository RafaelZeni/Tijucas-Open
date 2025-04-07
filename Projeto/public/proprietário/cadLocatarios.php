<?php
include '../../app/views/include/header.php';
require '../../app/database/connection.php'; // função conecta_db()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cnpj     = $_POST['cnpjCAD'];
    $nome     = $_POST['nomeCAD'];
    $telefone = $_POST['telefoneCAD'];
    $email    = $_POST['emailCAD'];
    $senha    = $_POST['passCAD'];

    // Criptografa a senha antes de enviar pra procedure
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $conn = conecta_db();

    // Chamada da procedure
    $stmt = $conn->prepare("CALL pr_AdicionarLocatario(?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $cnpj, $nome, $telefone, $email, $senhaHash);

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
<html lang="pt-BR">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="">
</head>
<body>

<section class="banner">
    <img src="./assets/imgs/maisde40espaços.jpeg" alt="Estacionamento Tijucas Open">
    <div class="quadrado">
        <section class="form-container">
            <h3>Entrar</h3>
            <form method="post">
                <label for="cnpjCAD">CNPJ</label>
                <input type="text" id="cnpjCAD" name="cnpjCAD" required placeholder="Digite o CNPJ">

                <label for="nomeCAD">Nome</label>
                <input type="text" id="nomeCAD" name="nomeCAD" required placeholder="Digite o Nome da empresa">

                <label for="telefoneCAD">Telefone</label>
                <input type="text" id="telefoneCAD" name="telefoneCAD" required placeholder="Digite o Telefone">

                <label for="emailCAD">Email</label>
                <input type="email" id="emailCAD" name="emailCAD" required placeholder="Digite o E-mail">

                <label for="passCAD">Senha</label>
                <input type="password" id="passCAD" name="passCAD" required placeholder="Digite a senha">

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
