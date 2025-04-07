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
        <form action="../página agradecimento/index.html" method="POST">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome" required placeholder="DIgite seu nome aqui...">

            <label for="sobrenome">Sobrenome</label>
            <input type="text" id="sobrenome" name="sobrenome" required placeholder="Digite seu sobrenome aqui...">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="Digite seu email aqui...">

            <label for="mensagem">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="4" required placeholder="Digite sua mensagem aqui..."></textarea>

            <a class="enviar" href="../página agradecimento/index.html">Enviar</a>
        </form>
    </section>
    <footer>
        <div class="info">
            <div class="horarios">
                <h4>Horários:</h4>
                <p>Lojas:</p>                   
                <p>Seg a Sáb: 10:00 às 22:00</p>
                <p>Dom: 11:00 às 21:00</p>
                <p>Alimentação:</p>
                <p>Seg a Sáb: 12:00 ás 23:00</p>
                <p>Dom: das 12:00 ás 22:00</p>
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
