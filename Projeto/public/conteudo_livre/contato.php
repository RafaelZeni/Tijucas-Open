<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Tijucas Open</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="conteudo_livre/assets/css/contato.css">
</head>
<body>
    <section class="banner">
        <img src="conteudo_livre/assets/imgs/bannerFaixada.jpg" alt="Estacionamento Tijucas Open">
    </section>
    <section class="form-container">
        <h2>Fale Conosco:</h2>
        <form action="" method="POST">
        <label for="nomeCAD">Nome</label>
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
