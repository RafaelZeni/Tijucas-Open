<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <title>Tijucas Open</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./conteudo_livre/assets/css/footer.css">
  </head>
  <body>
    <footer class="footer">
        <div class="footer-col">
            <h4>Horários 🕒</h4>
            <p><strong>Lojas:</strong><br>
            Seg a Sex: das 10:00 às 22:00<br>
            Sáb: das 09:00 às 22:00<br>
            Dom: das 11:00 às 20:00</p>

            <p><strong>Alimentação:</strong><br>
            Seg a Sex: das 12:00 às 23:00<br>
            Sáb: das 12:00 à 00:00<br>
            Dom: das 12:00 às 22:00</p>
        </div>

        <div class="footer-col">
            <h4>Endereço 📍</h4>
            <?php 
            $endereco = "R. Quinze de Novembro, 1306, Tijucas do Sul - PR, 83190-000";
            $endereco_url = urlencode($endereco);
            $link = "https://www.google.com/maps/dir/?api=1&destination=$endereco_url";
            ?>
            <a href="<?= $link ?>" target="_blank">Como chegar?</a>
            
        </div>

        <div class="footer-col">
            <h4>Contato 💬</h4>
            <p>Para mais informações, ligar para:<br>(XX) XXXXX-XXXX</p>
        </div>

        <div class="footer-col">
            <h4>Estacionamento 🚗</h4>
            <p>Gratuito todos os dias da semana</p>
        </div>
    </footer>

</body>
</html>