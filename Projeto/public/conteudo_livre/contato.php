<!--Página de Contato dedicada ao usuário, como
um meio de comunicação com o proprietário-->
<?php
$mensagemStatus = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = htmlspecialchars(trim($_POST["nomeCAD"]));
    $sobrenome = htmlspecialchars(trim($_POST["sobrenome"]));
    $email = filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL);
    $mensagem = htmlspecialchars(trim($_POST["mensagem"]));

    if ($email) {
        $destinatario = "SEU_EMAIL@dominio.com"; // <-- Substitua pelo seu e-mail
        $assunto = "Nova mensagem do formulário de contato";
        $corpo = "Nome: $nome $sobrenome\n";
        $corpo .= "Email: $email\n\n";
        $corpo .= "Mensagem:\n$mensagem";

        $headers = "From: $email\r\nReply-To: $email\r\n";

        if (mail($destinatario, $assunto, $corpo, $headers)) {
            $mensagemStatus = "<p style='color: green;'>Mensagem enviada com sucesso!</p>";
        } else {
            $mensagemStatus = "<p style='color: red;'>Erro ao enviar a mensagem.</p>";
        }
    } else {
        $mensagemStatus = "<p style='color: red;'>E-mail inválido.</p>";
    }   
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/style_conteudo_livre.css">
</head>
<body class="contato">
    <section class="banner">
        <img src="conteudo_livre/assets/imgs/bannerFaixada.jpg" alt="Estacionamento Tijucas Open">
    </section>
    <section class="form-container">
        <h2>Fale Conosco:</h2>
        <form action="" method="POST">
        <label for="nomeCAD">Nome</label>
        <!--Função para garantir que apenas letras sejam inseridas-->
        <input type="text" id="nomeCAD" name="nomeCAD" required placeholder="Digite o Nome da empresa" oninput="apenasLetras(this)">

            <label for="sobrenome">Sobrenome</label>
            <input type="text" id="sobrenome" name="sobrenome" required placeholder="Digite seu sobrenome aqui...">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Digite seu email aqui...">

            <label for="mensagem">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="4" required placeholder="Digite sua mensagem aqui..."></textarea>

            <a class="enviar" href="">Enviar</a>
        </form>
    </section>
    
    <script src="conteudo_livre/assets/js/contato.js"></script>
</body>
</html>
